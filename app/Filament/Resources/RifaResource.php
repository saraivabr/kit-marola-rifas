<?php

namespace App\Filament\Resources;

use App\Enums\RifaStatus;
use App\Filament\Resources\RifaResource\Pages;
use App\Models\Rifa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RifaResource extends Resource
{
    protected static ?string $model = Rifa::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Campanhas & Rifas';

    protected static ?string $navigationLabel = 'Rifas';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('RifaTabs')
                    ->tabs([
                        // ABA 1: Informações Gerais & Mídia
                        Forms\Components\Tabs\Tab::make('📦 Detalhes & Mídia')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título da Rifa')
                                    ->placeholder('Ex: Boné RAW Original + Case Completo')
                                    ->maxLength(255)
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('thumbnail')
                                    ->label('Foto Oficial de Capa')
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                                    ->image()
                                    ->imageEditor()
                                    ->directory('rifas')
                                    ->disk(env('FILAMENT_FILESYSTEM_DISK', config('filesystems.default')))
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('description')
                                    ->label('Descrição do Prêmio')
                                    ->placeholder('Descreva os itens inclusos no kit e benefícios...')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug / URL Amigável')
                                    ->helperText('Gerado automaticamente pelo título')
                                    ->hidden(fn (string $context) => $context !== 'edit')
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),

                        // ABA 2: Preço & Cotas
                        Forms\Components\Tabs\Tab::make('💰 Preços & Cotas')
                            ->schema([
                                Forms\Components\Grid::make([
                                    'default' => 1,
                                    'sm' => 2,
                                    'md' => 4,
                                ])
                                    ->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->label('Valor por Cota (R$)')
                                            ->numeric()
                                            ->prefix('R$')
                                            ->required()
                                            ->live(onBlur: true),

                                        Forms\Components\TextInput::make('total_numbers_available')
                                            ->label('Total de Cotas Disponíveis')
                                            ->numeric()
                                            ->default(100)
                                            ->minValue(10)
                                            ->required()
                                            ->live(onBlur: true),

                                        Forms\Components\TextInput::make('buy_max')
                                            ->label('Máximo por Pedido')
                                            ->numeric()
                                            ->default(20)
                                            ->minValue(1)
                                            ->required(),

                                        Forms\Components\TextInput::make('buy_min')
                                            ->label('Mínimo por Pedido')
                                            ->numeric()
                                            ->default(1)
                                            ->minValue(1)
                                            ->required(),
                                    ]),
                            ]),

                        // ABA 3: Split Woovi & Parceiro
                        Forms\Components\Tabs\Tab::make('🤝 Split PIX Woovi (70/30)')
                            ->schema([
                                Forms\Components\Section::make('Regras de Divisão Automática')
                                    ->description('A Woovi credita automaticamente a porcentagem do parceiro via Pix no momento do pagamento.')
                                    ->schema([
                                        Forms\Components\TextInput::make('partner_pix_key')
                                            ->label('Chave PIX do Parceiro (Destinatário)')
                                            ->placeholder('Telefone com DDD, CPF, E-mail ou Aleatória')
                                            ->helperText('Chave cadastrada na subconta da OpenPix para repasse instantâneo')
                                            ->required(),

                                        Forms\Components\TextInput::make('partner_name')
                                            ->label('Nome do Parceiro / Loja')
                                            ->placeholder('Ex: Headshop Raw Oficial')
                                            ->default('Parceiro Kit Marola')
                                            ->required(),

                                        Forms\Components\TextInput::make('partner_split_percent')
                                            ->label('Porcentagem do Parceiro (%)')
                                            ->numeric()
                                            ->default(70)
                                            ->suffix('%')
                                            ->minValue(1)
                                            ->maxValue(99)
                                            ->required()
                                            ->live(onBlur: true),

                                        Forms\Components\Placeholder::make('split_calculator')
                                            ->label('📊 Simulador de Faturamento ao Vender 100%')
                                            ->content(function (Forms\Get $get): string {
                                                $price = (float) ($get('price') ?: 2.99);
                                                $total = (int) ($get('total_numbers_available') ?: 100);
                                                $partnerPercent = (int) ($get('partner_split_percent') ?: 70);
                                                $platformPercent = 100 - $partnerPercent;

                                                $totalGross = $price * $total;
                                                $partnerTotal = ($partnerPercent / 100) * $totalGross;
                                                $platformTotal = ($platformPercent / 100) * $totalGross;

                                                return "Total Bruto: R$ " . number_format($totalGross, 2, ',', '.') .
                                                    "  |  Repasse Parceiro ({$partnerPercent}%): R$ " . number_format($partnerTotal, 2, ',', '.') .
                                                    "  |  Sua Margem ({$platformPercent}%): R$ " . number_format($platformTotal, 2, ',', '.');
                                            })
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),
                            ]),

                        // ABA 4: Sorteio & Progresso
                        Forms\Components\Tabs\Tab::make('⚙️ Sorteio & Progresso')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('raffle')
                                            ->label('Modalidade do Sorteio')
                                            ->options([
                                                'Loteria Federal' => 'Loteria Federal',
                                                'Sorteio Imediato após 100%' => 'Sorteio Imediato após 100%',
                                                'Live no Instagram' => 'Live no Instagram',
                                            ])
                                            ->default('Sorteio Imediato após 100%')
                                            ->required(),

                                        Forms\Components\Select::make('status')
                                            ->label('Status da Publicação')
                                            ->options([
                                                Rifa::STATUS_PUBLISHED => 'Publicada (Ativa)',
                                                Rifa::STATUS_DRAFT => 'Rascunho (Oculta)',
                                                Rifa::STATUS_FINISHED => 'Finalizada',
                                            ])
                                            ->default(Rifa::STATUS_PUBLISHED)
                                            ->required(),

                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label('Data de Lançamento')
                                            ->timezone('America/Sao_Paulo')
                                            ->default(now()),

                                        Forms\Components\DateTimePicker::make('expired_at')
                                            ->label('Data Limite (Opcional)')
                                            ->timezone('America/Sao_Paulo'),

                                        Forms\Components\TextInput::make('progress_percentage')
                                            ->label('Progresso Visual Fixo na Home (%)')
                                            ->numeric()
                                            ->placeholder('Ex: 45 para mostrar 45% vendido')
                                            ->helperText('Se vazio, o sistema calcula pelas cotas reais pagas')
                                            ->suffix('%'),

                                        Forms\Components\Radio::make('ranking_buyer')
                                            ->label('Exibir Ranking de Maiores Compradores?')
                                            ->boolean('Sim, exibir', 'Ocultar')
                                            ->default(true)
                                            ->inline(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título da Rifa')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Rifa $record) => 'R$ ' . number_format($record->price, 2, ',', '.') . ' / cota')
                    ->limit(28),

                Tables\Columns\TextColumn::make('sold_percentage')
                    ->label('Progresso')
                    ->badge()
                    ->color(fn (float $state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'info'))
                    ->formatStateUsing(fn ($state, Rifa $record) => ($record->progress_percentage ?: $state) . '%'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        Rifa::STATUS_PUBLISHED => 'success',
                        Rifa::STATUS_DRAFT => 'gray',
                        Rifa::STATUS_FINISHED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        Rifa::STATUS_PUBLISHED => 'Ativa',
                        Rifa::STATUS_DRAFT => 'Rascunho',
                        Rifa::STATUS_FINISHED => 'Finalizada',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('partner_split_percent')
                    ->label('Split')
                    ->badge()
                    ->color('primary')
                    ->visibleFrom('md')
                    ->formatStateUsing(fn ($state) => ($state ?: 70) . '% Parceiro / ' . (100 - ($state ?: 70)) . '% Você'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_public')
                        ->label('Ver no Site')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->color('info')
                        ->url(fn (Rifa $record): string => route('rifas.show', ['rifa' => $record]))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicar Rifa')
                        ->icon('heroicon-m-document-duplicate')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Duplicar Rifa')
                        ->modalDescription('Deseja criar uma cópia desta rifa para uma nova edição?')
                        ->action(function (Rifa $record) {
                            $clone = $record->replicate();
                            $clone->title = $record->title . ' (Nova Edição)';
                            $clone->slug = \Illuminate\Support\Str::slug($clone->title . '-' . time());
                            $clone->status = Rifa::STATUS_DRAFT;
                            $clone->progress_percentage = 0;
                            $clone->save();

                            Notification::make()
                                ->title('Rifa Duplicada!')
                                ->body('A nova edição foi criada como rascunho com sucesso.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\EditAction::make()->label('Editar'),
                    Tables\Actions\DeleteAction::make()->label('Excluir'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Ações da Rifa'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRifas::route('/'),
            'create' => Pages\CreateRifa::route('/create'),
            'edit' => Pages\EditRifa::route('/{record}/edit'),
            'view' => Pages\ViewRifa::route('/{record}'),
        ];
    }
}
