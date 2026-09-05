<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Rifa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $paidOrders = Order::where('status', Order::STATUS_PAID)->get();
        $totalOrdersCount = $paidOrders->count();

        $rifas = Rifa::all()->keyBy('id');

        $grossRevenue = 0.0;
        $totalTicketsSold = 0;
        $partnerTotal = 0.0;
        $platformTotal = 0.0;

        foreach ($paidOrders as $order) {
            $rifa = $rifas->get($order->rifa_id);
            if (!$rifa) continue;

            $qty = $order->quantity ?: (is_array($order->numbers_reserved) ? count($order->numbers_reserved) : 1);
            $totalTicketsSold += $qty;

            $orderTotal = $qty * (float) $rifa->price;
            $grossRevenue += $orderTotal;

            $partnerPercent = (int) ($rifa->partner_split_percent ?: 70);
            $partnerVal = ($partnerPercent / 100) * $orderTotal;
            $partnerTotal += $partnerVal;
            $platformTotal += ($orderTotal - $partnerVal);
        }

        $activeRifasCount = Rifa::where('status', Rifa::STATUS_PUBLISHED)->count();

        return [
            Stat::make('Faturamento Total', 'R$ ' . number_format($grossRevenue, 2, ',', '.'))
                ->description($totalOrdersCount . ' pedidos pagos confirmados')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Lucro Plataforma (30%)', 'R$ ' . number_format($platformTotal, 2, ',', '.'))
                ->description('Comissão retida na Woovi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

            Stat::make('Repasse Parceiros (70%)', 'R$ ' . number_format($partnerTotal, 2, ',', '.'))
                ->description('Repassado via Split Pix')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Cotas Vendidas', number_format($totalTicketsSold, 0, ',', '.'))
                ->description($activeRifasCount . ' rifas ativas no catálogo')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),
        ];
    }
}
