<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use App\Http\Controllers\DateTimeController;

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
        // $range = $request->range;
        // $timezone = $request->timezone;
        // $dates = $this->currentRange($range, $timezone);
        // $prev_dates = $this->previousRange($range, $timezone);

        // $result = Customer::where(
        //     'first_purchase',
        //     '>',
        //     $dates[0]->toDateString()
        // )->where(
        //     'first_purchase',
        //     '<',
        //     $dates[1]->toDateString()
        // )->sum('apfr');

        // $prev_result = Customer::where(
        //     'first_purchase',
        //     '>',
        //     $prev_dates[0]->toDateString()
        // )->where(
        //     'first_purchase',
        //     '<',
        //     $prev_dates[1]->toDateString()
        // )->sum('apfr');

        return $this->sum($request, Customer::class, 'apfr', 'first_purchase');
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
