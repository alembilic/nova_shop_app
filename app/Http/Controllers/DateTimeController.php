<?php

namespace App\Http\Controllers;

use App\Models\DateFilter;
use Laravel\Nova\Fields\DateTime;

class DateTimeController extends Controller
{
    public function getFilters()
    {
        $data = [
            365 => __('Last 365 Days'),
            60 => __('Last 60 Days'),
            30 => __('Last 30 Days'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
            'ALL' => __('ALL'),
        ];

        $dbFilters = DateFilter::get();

        foreach ($dbFilters as $filter) {
            $from = date("Y-m-d", strtotime($filter->from));
            $to = date("Y-m-d", strtotime($filter->to));
            $data[$from . ',' . $to] =  $from . ' to ' . $to;
        }

        return $data;
    }
}
