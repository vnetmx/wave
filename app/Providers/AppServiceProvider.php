<?php

namespace App\Providers;

use App\Events\AddressUpdated;
use App\Listeners\OpenpayAddressUpdated;
use App\Listeners\OpenpayUserCreation;
use App\Payments\PaymentInterface;
use App\Services\OpenpayService;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() == 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }

        $service = config('payments.default');
        View::share('payment_head_js', $service::getHeadJS());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaymentInterface::class, function() {
            $service = config('payments.default');
            return $service::instance();
        });

        $this->app->bind(UserService::class, function () {
           return new UserService(
               setting('auth'),
               config('voyager.user.default_role'),
               setting('billing.trial_days', 14),
           );
        });

        $this->app->bind(OpenpayService::class, function(){
            return new OpenpayService(
                config('openpay.merchant_id'),
                config('openpay.public_api_key'),
                config('openpay.private_api_key')
            );
        });
    }
}
