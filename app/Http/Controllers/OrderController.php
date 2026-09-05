<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Rifa;
use Carbon\Carbon;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $result = Order::with([
            'rifa' => fn ($query) => $query->select('id', 'title', 'price', 'slug'),
            'payment' => fn ($query) => $query->select('id', 'order_id'),
        ])
            ->where('id', $id)
            ->first();

        if ($result === null) {
            return redirect('/');
        }

        if (now() > Carbon::parse($result->expire_at)) {
            return redirect(route('rifas.show', ['rifa' => $result->rifa]));
        }

        if ($result->payment) {
            return redirect(route('payment.show', ['payment' => $result->payment]));
        }

        $rifa = $result->rifa;

        $order = $result->makeHidden('rifa');
        $qty = $order->quantity ?: (is_array($order->numbers_reserved) && count($order->numbers_reserved) ? count($order->numbers_reserved) : 1);
        $order->transaction_amount = $rifa->price * $qty;
        $order->expire_at = Carbon::parse($order->expire_at);

        return inertia('Order/PsrResume', [
            'order' => $order,
            'rifa' => $rifa,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $rifaId = $request->input('rifa');
        $rifa = Rifa::findOrFail($rifaId);

        $quantity = (int) $request->input('quantity');

        // Verifica quantidade de bilhetes já vendidos (apenas pedidos pagos contam como indisponíveis)
        $paidOrders = Order::select('numbers_reserved')
            ->where('rifa_id', $rifa->id)
            ->where('status', Order::STATUS_PAID)
            ->get();

        $takenCount = $paidOrders->pluck('numbers_reserved')
            ->filter()
            ->flatten()
            ->count();

        $availableCount = $rifa->total_numbers_available - $takenCount;

        if ($availableCount < $quantity) {
            return abort(409, 'Quantidade de cotas indisponível.');
        }

        // Não gera números antes do pagamento; os números serão alocados aleatoriamente após a confirmação do Pix
        $order = new Order;
        $order->customer_fullname = $request->input('fullname');
        $order->customer_email = $request->input('email');
        $order->customer_telephone = $request->input('telephone');
        $order->customer_instagram = $request->input('instagram');
        $order->rifa_id = $rifa->id;
        $order->quantity = $quantity;
        $order->numbers_reserved = [];
        $order->status = Order::STATUS_RESERVED;
        $order->expire_at = now()->addMinutes(config('payment.order_expired', 60));
        $order->saveOrFail();

        // Gera cobrança Pix imediatamente e leva o cliente direto para o pagamento sem passos intermediários
        try {
            $paymentGateway = app(\App\Services\WooviService::class);
            $response = $paymentGateway->generatePix($order, $rifa);

            $payment = new \App\Models\Payment;
            $payment->id = $response->id;
            $payment->ticket_url = $response->ticket_url;
            $payment->payment_code = $response->payment_method_id;
            $payment->date_of_expiration = Carbon::parse($response->date_of_expiration)->timezone(config('app.timezone', 'America/Sao_Paulo'));
            $payment->transaction_amount = $response->transaction_amount;
            $payment->qr_code = $response->qr_code;
            $payment->qr_code_img = $response->qr_code_img;
            $payment->order_id = $order->id;
            $payment->save();

            return Inertia::location(route('payment.show', ['payment' => $payment->id]));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Woovi: Fallback para resumo do pedido: ' . $e->getMessage());
            return Inertia::location(route('orders.show', [$order->id]));
        }
    }
}

