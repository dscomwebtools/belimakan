<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\CategoryLabel;
use App\Models\Feature;
use App\Models\Sosmed;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.client.menu', function ($view) {
            // $categories = Category::all();
            // $view->with('categories', $categories);
            $category_labels = CategoryLabel::with('categories')
                ->whereHas('categories')
                ->orderByRaw('CASE WHEN id = 1 THEN 1 ELSE 0 END') // id=1 jadi paling akhir
                ->orderBy('name') // opsional, biar sisanya tetap urut abjad
                ->get();

            $view->with('category_labels', $category_labels);
        });

        View::composer('layouts.client.footer', function ($view) {
            $sosmed = Sosmed::all();
            $view->with('sosmed', $sosmed);
        });

        View::composer('components.client.features', function ($view) {
            $features = Feature::all();
            $view->with('features', $features);
        });
    }
}
