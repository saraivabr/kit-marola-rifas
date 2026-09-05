<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Kit Marola Rifas VIP')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Campanhas & Rifas',
                'Vendas & Financeiro',
                'Sorteios & Premiações',
            ])
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
                'info' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverviewWidget::class,
                \App\Filament\Widgets\LatestOrdersWidget::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString('
                    <style>
                        /* Estilos Mobile-First: Cards Verticais para Tabelas no Celular */
                        @media (max-width: 767px) {
                            .fi-ta-table {
                                display: block !important;
                            }
                            .fi-ta-table thead {
                                display: none !important;
                            }
                            .fi-ta-table tbody {
                                display: flex !important;
                                flex-direction: column !important;
                                gap: 0.85rem !important;
                                width: 100% !important;
                            }
                            .fi-ta-table tbody tr {
                                display: flex !important;
                                flex-direction: column !important;
                                background-color: rgb(24 24 27 / 0.9) !important;
                                border: 1px solid rgb(63 63 70 / 0.6) !important;
                                border-radius: 1.25rem !important;
                                padding: 1rem !important;
                                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4) !important;
                            }
                            .fi-ta-table tbody tr td {
                                display: flex !important;
                                align-items: center !important;
                                justify-content: space-between !important;
                                padding: 0.35rem 0 !important;
                                border: none !important;
                            }
                            .fi-ta-table tbody tr td:last-child {
                                margin-top: 0.6rem !important;
                                padding-top: 0.6rem !important;
                                border-top: 1px solid rgb(63 63 70 / 0.4) !important;
                                justify-content: flex-end !important;
                            }
                            /* Abas com rolagem touch suave */
                            .fi-tabs {
                                overflow-x: auto !important;
                                -webkit-overflow-scrolling: touch !important;
                                flex-wrap: nowrap !important;
                                scrollbar-width: none !important;
                            }
                            .fi-tabs::-webkit-scrollbar {
                                display: none !important;
                            }
                            /* Botões touch confortáveis */
                            .fi-btn {
                                min-height: 44px !important;
                                border-radius: 0.75rem !important;
                            }
                            /* Padding inferior para Safe Area iOS e Android */
                            body {
                                padding-bottom: max(1.5rem, env(safe-area-inset-bottom)) !important;
                            }
                        }
                    </style>
                ')
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
