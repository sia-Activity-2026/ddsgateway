<?php

namespace App\Providers;

use App\Models\User;
use Dusterio\LumenPassport\LumenPassport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Lumen routes use a leading "\" on the controller name (e.g. "\Dusterio\..."); the
        // container key must match exactly or resolution falls through to the vendor class.
        $dusterioToken = \Dusterio\LumenPassport\Http\Controllers\AccessTokenController::class;
        $appToken = \App\Http\Controllers\AccessTokenController::class;
        $this->app->bind($dusterioToken, $appToken);
        $this->app->bind('\\'.$dusterioToken, $appToken);
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        LumenPassport::routes($this->app);

        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        /*
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });
        */
    }
}
