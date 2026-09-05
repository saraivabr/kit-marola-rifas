<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Últimos Pedidos em Tempo Real';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->with('rifa')->latest()->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('# Pedido')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_fullname')
                    ->label('Cliente')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_telephone')
                    ->label('WhatsApp')
                    ->copyable()
                    ->copyMessage('Telefone copiado!'),

                Tables\Columns\TextColumn::make('rifa.title')
                    ->label('Rifa')
                    ->limit(30),

                Tables\Columns\TextColumn::make('numbers_reserved')
                    ->label('Cotas Alocadas')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return count($state) . ' cotas: ' . implode(', ', array_slice($state, 0, 4)) . (count($state) > 4 ? '...' : '');
                        }
                        return $state ?: 'Aguardando pagamento';
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => OrderStatus::tryFrom($state)?->getLabel() ?? ucfirst($state))
                    ->color(fn (?string $state) => match ($state) {
                        'paid', Order::STATUS_PAID => 'success',
                        'reserved', Order::STATUS_RESERVED => 'warning',
                        'canceled', Order::STATUS_CANCELED => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Horário')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_order')
                    ->label('Ver Detalhes')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', ['record' => $record])),
            ]);
    }
}
