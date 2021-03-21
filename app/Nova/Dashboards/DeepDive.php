<?php

namespace App\Nova\Dashboards;

use Acme\Analytics\Analytics;
use App\Http\Controllers\DeepDiveDashController;
use Illuminate\Support\Facades\Request;
use Laravel\Nova\Dashboard;

class DeepDive extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        $user_id = auth()->user()->id;

        return [
            (new Analytics())->withMeta((new DeepDiveDashController)->byOrder(1, 'YTD', $user_id)),
            (new Analytics())->withMeta((new DeepDiveDashController)->byOrder(2, 'YTD', $user_id)),
            (new Analytics())->withMeta((new DeepDiveDashController)->byOrder(3, 'YTD', $user_id)),
            (new Analytics())->withMeta((new DeepDiveDashController)->byOrder(4, 'YTD', $user_id)),
            (new Analytics())->withMeta((new DeepDiveDashController)->byOrder(5, 'YTD', $user_id))
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'deep-dive';
    }

    public static function label()
    {
        return 'Deep Dive';
    }
}
