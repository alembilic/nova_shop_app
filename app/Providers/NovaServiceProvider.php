<?php

namespace App\Providers;

use App\Nova\Metrics\AverageCLV;
use App\Nova\Metrics\PurchaseFrequency;
use App\Nova\Metrics\TotalSales;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Acme\Analytics\Analytics;
use App\Http\Controllers\AnalyzeController;
use App\Nova\Dashboards\DataBreakdown;
use App\Nova\Dashboards\DeepDive;
use App\Nova\Dashboards\GatekeeperProducts;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        $user_id = auth()->user()->id;
        return [
            new AverageCLV,
            new PurchaseFrequency,
            new TotalSales,
            (new Analytics)->withMeta((new AnalyzeController)->popularProducts('YTD', $user_id))
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [new DataBreakdown, new DeepDive, new GatekeeperProducts];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
