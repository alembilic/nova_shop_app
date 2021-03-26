<?php

namespace App\Nova\Dashboards;

use App\Http\Controllers\GatekeeperProductsController;
use Laravel\Nova\Dashboard;

class GatekeeperProducts extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        $data = (new GatekeeperProductsController())->getData();

        return [
            //
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'gatekeeper-products';
    }

    public static function label()
    {
        return 'Gatekeeper Products';
    }
}
