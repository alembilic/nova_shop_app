<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use App\Http\Controllers\DateTimeController;
use App\Models\UserStoresPivot;

class PurchaseFrequency extends Value
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
            return $this->sum($request, Customer::class, 'apfr', 'first_purchase');

        $stores = UserStoresPivot::where('user_id', $request->user()->id)->get('store_id');
        return $this->sum($request, Customer::whereIn('store_id', $stores), 'apfr', 'first_purchase');
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
        return 'purchase-frequency';
    }
}
