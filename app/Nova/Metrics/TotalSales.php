<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use App\Models\Order;
use App\Http\Controllers\DateTimeController;
use App\Models\UserStoresPivot;

class TotalSales extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        if (auth()->user()->role == 'admin')
            return $this->count($request, Order::where('status', 'complete'), 'id', 'created_at');

        $stores = UserStoresPivot::where('user_id', $request->user()->id)->get('store_id');
        return $this->count($request, Order::where('status', 'complete')->whereIn('store_id', $stores), 'id', 'created_at');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return (new DateTimeController())->getFilters();
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'total-sales';
    }
}
