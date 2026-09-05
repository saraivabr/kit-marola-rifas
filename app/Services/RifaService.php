<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Rifa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RifaService
{
    /**
     * Conta o número de pedidos de uma rifa de acordo com a situação do pedido
     *
     * @return int
     */
    public function countOrdersByStatus(Rifa $rifa, OrderStatus $orderStatus)
    {
        $jsonLenFunc = DB::getDriverName() === 'sqlite' ? 'json_array_length' : 'JSON_LENGTH';

        return (int) $rifa->orders()
            ->where('status', $orderStatus)
            ->sum(DB::raw("{$jsonLenFunc}(numbers_reserved)"));
    }

    /**
     * Captura o ranking de compradores
     */
    public function getRanking(Rifa $rifa): Collection
    {
        $jsonLenFunc = DB::getDriverName() === 'sqlite' ? 'json_array_length' : 'JSON_LENGTH';

        return Order::query()->select([
            'customer_fullname',
            'customer_telephone',
            DB::raw("SUM({$jsonLenFunc}(numbers_reserved)) AS numbers_reserved_total"),
        ])
            ->where('status', OrderStatus::PAID)
            ->where('rifa_id', $rifa->id)
            ->groupBy('customer_telephone')
            ->orderBy('numbers_reserved_total', 'desc')
            ->get();
    }

    /**
     * Captura os vencedores da rifa
     *
     * @return Rifa[]
     */
    public function winners(Rifa $rifa)
    {
        return $rifa->winners()->with('order')->get();
    }

    /**
     * Verifica se uma rifa há vencedores
     *
     * @return bool
     */
    public function hasWinner(Rifa $rifa)
    {
        return (bool) $rifa->winners()->count();
    }

    private WhatsAppService $whatsAppService;

    public function __construct(?WhatsAppService $whatsAppService = null)
    {
        $this->whatsAppService = $whatsAppService ?? app(WhatsAppService::class);
    }

    /**
     * Aloca os números da sorte somente após o pagamento confirmado
     */
    public function allocateNumbers(Order $order): array
    {
        if ($order->status === Order::STATUS_PAID && !empty($order->numbers_reserved)) {
            return $order->numbers_reserved;
        }

        $rifa = $order->rifa;
        $quantity = $order->quantity ?: (is_array($order->numbers_reserved) && count($order->numbers_reserved) ? count($order->numbers_reserved) : 1);

        $rifaNumbers = collect()->range(0, $rifa->total_numbers_available - 1);

        $paidOrders = Order::select('numbers_reserved')
            ->where('rifa_id', $rifa->id)
            ->where('status', Order::STATUS_PAID)
            ->where('id', '!=', $order->id)
            ->get();

        $takenNumbers = $paidOrders->pluck('numbers_reserved')
            ->filter()
            ->flatten()
            ->map(fn ($n) => (int) $n);

        $available = $rifaNumbers->diff($takenNumbers)->shuffle();

        if ($available->count() < $quantity) {
            $quantity = $available->count();
        }

        $digits = strlen((string) ($rifa->total_numbers_available - 1));
        if ($digits < 2) {
            $digits = 2;
        }

        $chosen = $available->splice(0, $quantity)
            ->map(fn ($num) => str_pad((string) $num, $digits, '0', STR_PAD_LEFT))
            ->sort()
            ->values()
            ->all();

        $order->numbers_reserved = $chosen;
        $order->status = Order::STATUS_PAID;
        $order->save();

        // Envia notificação com os números da sorte via WhatsApp
        try {
            $this->whatsAppService->sendPaymentConfirmation($order, $chosen);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Erro ao enviar WhatsApp pós-alocação: " . $e->getMessage());
        }

        return $chosen;
    }
}
