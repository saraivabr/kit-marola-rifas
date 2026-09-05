<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Rifa;
use App\Services\RifaService;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Vendas & Financeiro';

    protected static ?string $navigationLabel = 'Pedidos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Pedido')
                    ->schema([
                        Forms\Components\Select::make('rifa_id')
                            ->label('Rifa')
                            ->required()
                            ->options(Rifa::all(['title', 'id'])->pluck('title', 'id'))
                            ->searchable(),

                        Forms\Components\Select::make('status')
                            ->label('Status do Pedido')
                            ->default(Order::STATUS_RESERVED)
                            ->required()
                            ->options([
                                Order::STATUS_PAID => 'Pago (Aprovado)',
                                Order::STATUS_RESERVED => 'Reservado (Aguardando Pix)',
                                Order::STATUS_CANCELED => 'Cancelado',
                            ]),

                        Forms\Components\TextInput::make('customer_fullname')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(64),

                        Forms\Components\TextInput::make('customer_email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('customer_telephone')
                            ->label('WhatsApp com DDD')
                            ->required()
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantidade de Cotas')
                            ->numeric()
                            ->default(1)
                            ->required(),

                        Forms\Components\TagsInput::make('numbers_reserved')
                            ->label('Números / Cotas da Sorte Alocadas')
                            ->placeholder('Digite a cota e aperte Enter')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_fullname')
                    ->label('Cliente')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Order $record) => $record->customer_telephone),

                Tables\Columns\TextColumn::make('rifa.title')
                    ->label('Campanha')
                    ->limit(20)
                    ->searchable()
                    ->visibleFrom('md'),

                Tables\Columns\TextColumn::make('numbers_reserved')
                    ->label('Cotas')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return count($state) . ' cotas: ' . implode(', ', array_slice($state, 0, 2)) . (count($state) > 2 ? '...' : '');
                        }
                        return $state ?: 'Pendente';
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        Order::STATUS_PAID, 'paid' => 'success',
                        Order::STATUS_RESERVED, 'reserved' => 'warning',
                        Order::STATUS_CANCELED, 'canceled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Order::STATUS_PAID, 'paid' => 'Pago',
                        Order::STATUS_RESERVED, 'reserved' => 'Pendente',
                        Order::STATUS_CANCELED, 'canceled' => 'Cancelado',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data / Hora')
                    ->dateTime('d/m/Y H:i')
                    ->visibleFrom('md')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options([
                        Order::STATUS_PAID => 'Apenas Pagos',
                        Order::STATUS_RESERVED => 'Apenas Aguardando',
                        Order::STATUS_CANCELED => 'Cancelados',
                    ]),

                Tables\Filters\SelectFilter::make('rifa_id')
                    ->label('Filtrar por Rifa')
                    ->options(fn () => Rifa::pluck('title', 'id')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Funcionalidade 2: Reenviar WhatsApp do Pedido
                    Tables\Actions\Action::make('resend_whatsapp')
                        ->label('Reenviar WhatsApp')
                        ->icon('heroicon-m-chat-bubble-left-ellipsis')
                        ->color('success')
                        ->visible(fn (Order $record) => $record->status === Order::STATUS_PAID && !empty($record->numbers_reserved))
                        ->requiresConfirmation()
                        ->modalHeading('Reenviar Cotas via WhatsApp')
                        ->modalDescription(fn (Order $record) => "Deseja reenviar os números da sorte para {$record->customer_fullname} ({$record->customer_telephone})?")
                        ->action(function (Order $record) {
                            try {
                                $whats = app(WhatsAppService::class);
                                $whats->sendPaymentConfirmation($record, $record->numbers_reserved);

                                Notification::make()
                                    ->title('WhatsApp Enviado!')
                                    ->body("Confirmação enviada para {$record->customer_telephone}")
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Erro ao enviar WhatsApp')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    // Funcionalidade 3: Baixa Manual com 1 Clique
                    Tables\Actions\Action::make('manual_approve')
                        ->label('Aprovar / Baixar')
                        ->icon('heroicon-m-check-badge')
                        ->color('warning')
                        ->visible(fn (Order $record) => $record->status !== Order::STATUS_PAID)
                        ->requiresConfirmation()
                        ->modalHeading('Aprovar Pedido Manualmente')
                        ->modalDescription('Confirmar pagamento manual e alocar números da sorte imediatamente?')
                        ->action(function (Order $record) {
                            try {
                                $rifaService = app(RifaService::class);
                                $numbers = $rifaService->allocateNumbers($record);

                                Notification::make()
                                    ->title('Pedido Aprovado com Sucesso!')
                                    ->body("Cotas alocadas: " . implode(', ', $numbers))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Erro na aprovação')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\EditAction::make()->label('Editar'),
                    Tables\Actions\ViewAction::make()->label('Visualizar'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Opções do Pedido'),
            ])
            ->bulkActions([
                // Funcionalidade 9: Exportação em CSV dos Compradores
                Tables\Actions\BulkAction::make('export_csv')
                    ->label('Exportar Compradores (CSV)')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Collection $records) {
                        $csv = "Pedido;Cliente;Telefone;Email;Campanha;Cotas;Status;Data\n";
                        foreach ($records as $o) {
                            $cotas = is_array($o->numbers_reserved) ? implode('|', $o->numbers_reserved) : '';
                            $csv .= "{$o->id};\"{$o->customer_fullname}\";{$o->customer_telephone};{$o->customer_email};\"{$o->rifa?->title}\";\"{$cotas}\";{$o->status};{$o->created_at}\n";
                        }

                        return response()->streamDownload(function () use ($csv) {
                            echo "\xEF\xBB\xBF"; // UTF-8 BOM
                            echo $csv;
                        }, 'compradores_kitmarola_' . date('Y-m-d_His') . '.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),

                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
