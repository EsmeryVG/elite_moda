<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Pluralizer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
    \Illuminate\Support\Facades\View::composer('layouts.navbar', function ($view) {
        $alertas = [];

        if (auth()->check()) {
            $alertas = \App\Http\Controllers\HomeController::obtenerAlertas();
        }

        $view->with('alertasNavbar', $alertas);
    });

    \Illuminate\Support\Facades\Blade::if('permiso', function (string $clave) {
    return auth()->check() && auth()->user()->tienePermiso($clave);
});
}
}
