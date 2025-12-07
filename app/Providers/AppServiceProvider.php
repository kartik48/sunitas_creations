<?php

namespace App\Providers;

use App\Models\CartItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Share cart count with all views
        View::composer('*', function ($view) {
            $cartCount = 0;

            if (auth()->check()) {
                $cartCount = CartItem::where('user_id', auth()->id())->sum('quantity');
            } elseif (Session::has('cart_session_id')) {
                $cartCount = CartItem::where('session_id', Session::get('cart_session_id'))->sum('quantity');
            }

            $view->with('cartCount', $cartCount);
        });
    }
}
