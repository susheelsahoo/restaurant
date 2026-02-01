<?php

namespace App\Providers;

use Livewire\Livewire;
use App\Core\KTBootstrap;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Update defaultStringLength
        Builder::defaultStringLength(191);

        KTBootstrap::init();

        // Share blog categories to all views (menu categories are admin-only)
        // Guard with Schema::hasTable to avoid errors during migrations or when DB is not ready
        if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
            view()->share('blogCategories', \App\Models\Category::where('is_active', true)->get());
        } else {
            view()->share('blogCategories', collect());
        }

        if (app()->environment('production')) {
            Livewire::setUpdateRoute(function ($handle) {
                return Route::post('/starterkit/metronic/laravel/livewire/update', $handle);
            });
        }
    }
}
