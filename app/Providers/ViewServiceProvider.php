<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\CategoryLabel;
use App\Models\Feature;
use App\Models\Sosmed;
use App\Models\User;
use App\Models\Visitor;
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
            $category_labels = CategoryLabel::with(['categories' => function ($q) {
                $q->orderBy('name', 'asc'); // urutin categories juga
            }])
                ->whereHas('categories')
                ->orderByRaw('CASE WHEN id = 1 THEN 1 ELSE 0 END') // id=1 jadi paling akhir
                ->orderBy('name', 'asc') // sisanya urut abjad
                ->get();

            $view->with('category_labels', $category_labels);
        });

        View::composer('layouts.client.footer', function ($view) {
            $sosmed = Sosmed::where('is_footer', true)->get();
            $visitorCount = Visitor::sum('count');
            $wa = User::where('level_id', 1)
                ->where('is_sales', 1)
                ->get();
            $view->with('sosmed', $sosmed)->with('visitorCount', $visitorCount)->with('wa', $wa);
        });

        View::composer('components.client.features', function ($view) {
            $features = Feature::all();
            $view->with('features', $features);
        });
    }
}
