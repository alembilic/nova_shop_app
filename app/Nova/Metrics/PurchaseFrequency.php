<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

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
        $range = $request->range;
        $timezone = $request->timezone;
        $dates = $this->currentRange($range, $timezone);
        $prev_dates = $this->previousRange($range, $timezone);

        $result = Customer::where(
            'first_purchase',
            '>',
            $dates[0]->toDateString()
        )->where(
            'first_purchase',
            '<',
            $dates[1]->toDateString()
        )->sum('apfr');

        $prev_result = Customer::where(
            'first_purchase',
            '>',
            $prev_dates[0]->toDateString()
        )->where(
            'first_purchase',
            '<',
            $prev_dates[1]->toDateString()
        )->sum('apfr');

        return $this->result($result)->previous($prev_result);
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
        ];
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
