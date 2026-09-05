<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentStatusResource;
use App\Models\Order;
use App\Models\Payment;
use App\Services\WooviService;
use App\Services\RifaService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    private const STATUS_APPROVED = 'approved';

    public function __construct(
        private WooviService $paymentGateway,
        private RifaService $rifaService
    ) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $order = Order::with([
            'rifa' => fn (BelongsTo $query) => $query->select('id', 'title', 'price', 'slug', 'partner_pix_key', 'partner_name', 'partner_split_percent'),
            'payment' => fn (HasOne $query) => $query->select('id', 'order_id'),
        ])
            ->where('id', $request->input('orderId', 0))
            ->first();

        if (! $order) {
            return back();
        }

        if ($order->payment) {
            return redirect()->route('payment.show', ['payment' => $order->payment->id]);
        }

        if ($order->expire_at && now() > Carbon::parse($order->expire_at)) {
            return redirect()->route('rifas.show', ['rifa' => $order->rifa]);
        }

        try {
            $response = $this->paymentGateway->generatePix($order, $order->rifa);

            $payment = new Payment;
            $payment->id = $response->id;
            $payment->ticket_url = $response->ticket_url;
            $payment->payment_code = $response->payment_method_id;
            $payment->date_of_expiration = Carbon::parse($response->date_of_expiration)->timezone(config('app.timezone'));
            $payment->transaction_amount = $response->transaction_amount;
            $payment->qr_code = $response->qr_code;
            $payment->order_id = $order->id;
            $payment->save();

            return Inertia::location(route('payment.show', ['payment' => $response->id]));
        } catch (\Throwable $e) {
            Log::emergency('Erro ao gerar pagamento Woovi: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['warning' => 'Erro ao gerar o Pix. Tente novamente em instantes.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        if (empty($payment->qr_code_img)) {
            $qrcode = QrCode::encoding('UTF-8')->format('svg')->size(300)->generate($payment->qr_code);
            $payment->qr_code_img = 'data:image/svg+xml;base64,'.base64_encode($qrcode);
        }
        $payment->date_of_expiration = Carbon::parse($payment->date_of_expiration)->timezone('America/Sao_Paulo');

        return inertia('Payment/PsrShow', [
            'payment' => $payment->only([
                'id',
                'qr_code',
                'qr_code_img',
                'ticket_url',
                'transaction_amount',
                'date_of_expiration',
                'date_approved',
            ]),
        ]);
    }

    /**
     * Recebe notificação via WebHook do Woovi ou legado, consulta o pagamento e atualiza
     */
    public function update(Request $request): Response
    {
        Log::info('Webhook de Pagamento recebido:', $request->all());

        // Identifica pagamento via Woovi / OpenPix
        $paymentId = $request->input('charge.correlationID') 
            ?? $request->input('charge.identifier') 
            ?? $request->input('correlationID')
            ?? $request->input('data.id')
            ?? $request->input('id');

        if (! $paymentId) {
            return response(null, 204);
        }

        $payment = Payment::where('id', $paymentId)->first();
        if (! $payment) {
            Log::warning("Webhook: Pagamento {$paymentId} não encontrado localmente.");
            return response(null, 204);
        }

        $paymentInfo = $this->paymentGateway->getPayment($paymentId);

        if ($paymentInfo->status === self::STATUS_APPROVED) {
            $payment->update([
                'date_approved' => Carbon::parse($paymentInfo->date_approved ?? now()),
            ]);

            $order = Order::with('rifa')->where('id', $payment->order_id)->first();
            if ($order) {
                $this->rifaService->allocateNumbers($order);
            }
        }

        return response('');
    }

    /**
     * Realiza polling de verificação de status do pagamento e números alocados
     */
    public function check(Payment $payment)
    {
        if (! $payment->date_approved) {
            $paymentInfo = $this->paymentGateway->getPayment($payment->id);
            if ($paymentInfo->status === self::STATUS_APPROVED) {
                $payment->update([
                    'date_approved' => Carbon::parse($paymentInfo->date_approved ?? now()),
                ]);

                $order = Order::with('rifa')->where('id', $payment->order_id)->first();
                if ($order) {
                    $this->rifaService->allocateNumbers($order);
                }
            }
        } elseif ($payment->order && $payment->order->status === Order::STATUS_PAID && empty($payment->order->numbers_reserved)) {
            $this->rifaService->allocateNumbers($payment->order);
        }

        return new PaymentStatusResource($payment->fresh(['order']));
    }
}
