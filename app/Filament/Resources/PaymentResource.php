<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Order;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Vendas & Financeiro';

    protected static ?string $navigationLabel = 'Transações Pix';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes da Transação Pix Woovi')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('ID / CorrelationID')
                            ->disabled(),

                        Forms\Components\Select::make('order_id')
                            ->label('Pedido Vinculado')
                            ->options(fn () => Order::pluck('customer_fullname', 'id'))
                            ->searchable(),

                        Forms\Components\TextInput::make('transaction_amount')
                            ->label('Valor (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),

                        Forms\Components\TextInput::make('ticket_url')
                            ->label('Link do Checkout Woovi')
                            ->url(),

                        Forms\Components\DateTimePicker::make('date_of_expiration')
                            ->label('Data de Vencimento')
                            ->timezone('America/Sao_Paulo'),

                        Forms\Components\DateTimePicker::make('date_approved')
                            ->label('Data de Pagamento Confirmado')
                            ->timezone('America/Sao_Paulo'),

                        Forms\Components\Textarea::make('qr_code')
                            ->label('Código Pix Copia e Cola')
                            ->rows(3)
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
                    ->label('ID')
                    ->searchable()
                    ->limit(12)
                    ->copyable()
                    ->visibleFrom('sm'),

                Tables\Columns\TextColumn::make('order.customer_fullname')
                    ->label('Cliente')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Payment $record) => 'R$ ' . number_format($record->transaction_amount, 2, ',', '.')),

                Tables\Columns\TextColumn::make('date_approved')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => $state ? 'Pago' : 'Pendente'),

                Tables\Columns\TextColumn::make('date_approved')
                    ->label('Pago em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Pendente')
                    ->visibleFrom('md')
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_of_expiration')
                    ->label('Expira em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('approved_only')
                    ->label('Apenas Aprovados')
                    ->query(fn (Builder $query) => $query->whereNotNull('date_approved')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('open_ticket')
                        ->label('Abrir Checkout Woovi')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->color('info')
                        ->visible(fn (Payment $record) => !empty($record->ticket_url))
                        ->url(fn (Payment $record) => $record->ticket_url)
                        ->openUrlInNewTab(),

                    Tables\Actions\EditAction::make()->label('Editar'),
                    Tables\Actions\DeleteAction::make()->label('Excluir'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Ações do Pagamento'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayments::route('/'),
        ];
    }
}
