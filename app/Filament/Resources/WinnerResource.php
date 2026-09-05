<?php

namespace App\Filament\Resources;

use App\Enums\WinnerPosition;
use App\Filament\Resources\WinnerResource\Pages;
use App\Models\Order;
use App\Models\Rifa;
use App\Models\Winner;
use App\Services\WhatsAppService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WinnerResource extends Resource
{
    protected static ?string $model = Winner::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Sorteios & Premiações';

    protected static ?string $navigationLabel = 'Ganhadores & Sorteios';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Premiação e Sorteio')
                    ->description('Realize o sorteio aleatório auditável ou registre o número apurado pela Loteria Federal.')
                    ->schema([
                        Forms\Components\Select::make('rifa_id')
                            ->label('Selecione a Rifa')
                            ->options(Rifa::pluck('title', 'id'))
                            ->reactive()
                            ->required(),

                        // Funcionalidade 4: Sorteador Automático Integrado com 1 Clique
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('draw_random_winner')
                                ->label('🎲 Sortear Cota Aleatória Paga')
                                ->icon('heroicon-m-sparkles')
                                ->color('success')
                                ->visible(fn (Forms\Get $get) => !empty($get('rifa_id')))
                                ->requiresConfirmation()
                                ->modalHeading('Realizar Sorteio Automático')
                                ->modalDescription('O sistema irá sortear aleatoriamente um dos bilhetes entre os compradores que pagaram.')
                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                    $rifaId = $get('rifa_id');
                                    $paidOrders = Order::where('rifa_id', $rifaId)
                                        ->where('status', Order::STATUS_PAID)
                                        ->get();

                                    $allNumbers = [];
                                    foreach ($paidOrders as $order) {
                                        if (is_array($order->numbers_reserved)) {
                                            foreach ($order->numbers_reserved as $num) {
                                                $allNumbers[] = [
                                                    'number' => (string) $num,
                                                    'order_id' => $order->id,
                                                    'customer' => $order->customer_fullname,
                                                ];
                                            }
                                        }
                                    }

                                    if (empty($allNumbers)) {
                                        Notification::make()
                                            ->title('Nenhuma cota paga')
                                            ->body('Esta rifa ainda não possui cotas pagas para serem sorteadas.')
                                            ->warning()
                                            ->send();
                                        return;
                                    }

                                    $winnerChoice = $allNumbers[array_rand($allNumbers)];
                                    $set('drawn_number', $winnerChoice['number']);
                                    $set('order_id', $winnerChoice['order_id']);

                                    Notification::make()
                                        ->title('Cota Sorteada com Sucesso!')
                                        ->body("Cota contemplada: {$winnerChoice['number']} - Ganhador: {$winnerChoice['customer']}")
                                        ->success()
                                        ->send();
                                }),
                        ])
                        ->columnSpanFull(),

                        Forms\Components\TextInput::make('drawn_number')
                            ->label('Número da Cota Sorteada')
                            ->placeholder('Ex: 042')
                            ->reactive()
                            ->required(),

                        Forms\Components\Select::make('order_id')
                            ->label('Pedido do Ganhador')
                            ->reactive()
                            ->options(function (Forms\Get $get) {
                                $rifaId = $get('rifa_id');
                                if (!$rifaId) return [];

                                $drawNumber = (string) $get('drawn_number');
                                $orders = Order::where('rifa_id', $rifaId)
                                    ->where('status', Order::STATUS_PAID)
                                    ->get();

                                $options = [];
                                foreach ($orders as $o) {
                                    if (empty($drawNumber) || (is_array($o->numbers_reserved) && in_array($drawNumber, $o->numbers_reserved))) {
                                        $options[$o->id] = "{$o->customer_fullname} ({$o->customer_telephone})";
                                    }
                                }
                                return $options;
                            })
                            ->required(),

                        Forms\Components\Select::make('position')
                            ->label('Colocação')
                            ->options(WinnerPosition::class)
                            ->default(WinnerPosition::FIRST_PLACE)
                            ->required(),

                        Forms\Components\Textarea::make('testimonial')
                            ->label('Depoimento do Ganhador (Opcional)')
                            ->placeholder('Comentário ou feedback deixado pelo vencedor...')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('video')
                            ->label('Vídeo de Depoimento / Entrega do Prêmio (Opcional)')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                            ->disk(env('FILAMENT_FILESYSTEM_DISK', config('filesystems.default')))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rifa.title')
                    ->label('Rifa')
                    ->weight('bold')
                    ->limit(30),

                Tables\Columns\TextColumn::make('order.customer_fullname')
                    ->label('Ganhador')
                    ->description(fn (Winner $record) => $record->order?->customer_telephone),

                Tables\Columns\TextColumn::make('drawn_number')
                    ->label('Cota da Sorte')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('position')
                    ->label('Prêmio')
                    ->badge()
                    ->formatStateUsing(fn ($state) => WinnerPosition::tryFrom($state)?->getLabel() ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data do Sorteio')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Funcionalidade 10: Notificar Ganhador via WhatsApp
                    Tables\Actions\Action::make('notify_winner_whatsapp')
                        ->label('Notificar no WhatsApp')
                        ->icon('heroicon-m-chat-bubble-left-ellipsis')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Notificar Vencedor no WhatsApp')
                        ->modalDescription(fn (Winner $record) => "Deseja enviar a mensagem oficial de parabéns para {$record->order?->customer_fullname}?")
                        ->action(function (Winner $record) {
                            try {
                                $order = $record->order;
                                if (!$order || empty($order->customer_telephone)) {
                                    throw new \Exception('Telefone do cliente não encontrado.');
                                }

                                $phone = preg_replace('/\D/', '', $order->customer_telephone);
                                if (strlen($phone) === 11) {
                                    $phone = '55' . $phone;
                                }

                                $message = "🏆 *PARABÉNS, " . strtoupper($order->customer_fullname) . "!* 🏆\n\n"
                                    . "Você foi o(a) grande vencedor(a) da rifa *{$record->rifa?->title}*!\n\n"
                                    . "🎟️ *Sua Cota Contemplada:* #{$record->drawn_number}\n\n"
                                    . "Nossa equipe já está separando o seu prêmio para envio com frete grátis! Responda esta mensagem para combinarmos os detalhes da entrega.\n\n"
                                    . "🌿 *Kit Marola Rifas*";

                                $payload = [
                                    'number' => $phone,
                                    'text' => $message,
                                ];

                                \Illuminate\Support\Facades\Http::acceptJson()
                                    ->withHeaders(['token' => env('UAZAPI_INSTANCE_TOKEN', '3e9532b9-11d7-4b85-a90d-8e2c0221a9e2')])
                                    ->post('https://escreveai.uazapi.com/message/sendText/5511980948484', $payload);

                                Notification::make()
                                    ->title('Ganhador Notificado!')
                                    ->body("Mensagem de parabéns enviada para {$order->customer_telephone}")
                                    ->success()
                                    ->send();
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Erro ao notificar')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\EditAction::make()->label('Editar'),
                    Tables\Actions\DeleteAction::make()->label('Excluir'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Ações do Ganhador'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWinners::route('/'),
            'create' => Pages\CreateWinner::route('/create'),
            'edit' => Pages\EditWinner::route('/{record}/edit'),
        ];
    }
}
