<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\TransferInvoice;
use App\Observers\StockMovementObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Order::observe(StockMovementObserver::class);
        Invoice::observe(StockMovementObserver::class);
        TransferInvoice::observe(StockMovementObserver::class);

    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
