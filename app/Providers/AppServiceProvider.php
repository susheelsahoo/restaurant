<?php

namespace App\Providers;

use Livewire\Livewire;
use App\Core\KTBootstrap;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Schema\Builder;
use Illuminate\Pagination\Paginator;
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

        // ✅ ADD THIS
        Paginator::useBootstrapFive();

        KTBootstrap::init();

        if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
            $blogCategoriesQuery = \App\Models\Category::query();

            if (\Illuminate\Support\Facades\Schema::hasColumn('categories', 'is_active')) {
                $blogCategoriesQuery->where('is_active', true);
            }

            view()->share(
                'blogCategories',
                $blogCategoriesQuery->get()
            );
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
