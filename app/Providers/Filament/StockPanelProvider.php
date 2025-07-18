<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ProductStock;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\TransferInvoiceResource;
use Exception;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class StockPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('stock')
            ->path('stock')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->profile()
            ->font('Cairo')
            ->sidebarWidth('300px')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Stock/Resources'), for: 'App\\Filament\\Stock\\Resources')
            ->discoverPages(in: app_path('Filament/Stock/Pages'), for: 'App\\Filament\\Stock\\Pages')
            ->pages([
                Pages\Dashboard::class,
                ProductStock::class,
            ])
            ->resources([
                ProductResource::class,
                OrderResource::class,
                InvoiceResource::class,
                TransferInvoiceResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Stock/Widgets'), for: 'App\\Filament\\Stock\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
