<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboard;
use Coroowicaksono\ChartJsIntegration\AreaChart;
use App\Models\Customer;
use App\Models\UserStoresPivot;
use Illuminate\Support\Facades\DB;

class DataBreakdown extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        $storeQuery = '';
        if (auth()->user()->role != 'admin') {
            $stores = UserStoresPivot::where('user_id', auth()->user()->id)->get('store_id');
            $storeQuery = ' where store_id in (';
            foreach ($stores as $store) $storeQuery .= $store->store_id . ',';

            $storeQuery = substr($storeQuery, 0, -1);
            $storeQuery .= ') ';
        }

        $total = Customer::count();

        $data = array(
            DB::select("select sum(clv) / " . $total * 0.05 . " as five from (select * from customers " . $storeQuery . " order by clv desc limit " . round($total * 0.05) . ") as f"),
            DB::select("select sum(clv) / " . $total * 0.10 . " as five from (select * from customers " . $storeQuery . " order by clv desc limit " . round($total * 0.10) . ") as f"),
            DB::select("select sum(clv) / " . $total * 0.15 . " as five from (select * from customers " . $storeQuery . " order by clv desc limit " . round($total * 0.15) . ") as f"),
            DB::select("select sum(clv) / " . $total * 0.20 . " as five from (select * from customers " . $storeQuery . " order by clv desc limit " . round($total * 0.20) . ") as f"),
            DB::select("select sum(clv) / " . $total * 0.25 . " as five from (select * from customers " . $storeQuery . " order by clv desc limit " . round($total * 0.25) . ") as f")
        );

        $data2 = array(
            DB::select("select sum(apfr) / " . $total * 0.05 . " as five from (select * from customers " . $storeQuery . " order by apfr desc limit " . round($total * 0.05) . ") as f"),
            DB::select("select sum(apfr) / " . $total * 0.10 . " as five from (select * from customers " . $storeQuery . " order by apfr desc limit " . round($total * 0.10) . ") as f"),
            DB::select("select sum(apfr) / " . $total * 0.15 . " as five from (select * from customers " . $storeQuery . " order by apfr desc limit " . round($total * 0.15) . ") as f"),
            DB::select("select sum(apfr) / " . $total * 0.20 . " as five from (select * from customers " . $storeQuery . " order by apfr desc limit " . round($total * 0.20) . ") as f"),
            DB::select("select sum(apfr) / " . $total * 0.25 . " as five from (select * from customers " . $storeQuery . "order by apfr desc limit " . round($total * 0.25) . ") as f")
        );

        return [
            (new AreaChart())
                ->title('Average CLV for top Customers')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])
                ->series(array([
                    'barPercentage' => 0.5,
                    'label' => 'CLV',
                    'backgroundColor' => '#f7a35c',
                    'data' => [round($data[0][0]->five, 2), round($data[1][0]->five, 2), round($data[2][0]->five, 2), round($data[3][0]->five, 2), round($data[4][0]->five, 2)],
                ]))
                ->options([
                    'xaxis' => [
                        'categories' => ['Top 5%', 'Top 10%', 'Top 15%', 'Top 20%', 'Top 25%']
                    ],
                ])
                ->width('full'),
            (new AreaChart())
                ->title('Average APFR for top Customers')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])
                ->series(array([
                    'barPercentage' => 0.5,
                    'label' => 'APFR',
                    'backgroundColor' => '#90ed7d',
                    'data' => [round($data2[0][0]->five, 4), round($data2[1][0]->five, 4), round($data2[2][0]->five, 4), round($data2[3][0]->five, 4), round($data2[4][0]->five, 4)],
                ]))
                ->options([
                    'xaxis' => [
                        'categories' => ['Top 5%', 'Top 10%', 'Top 15%', 'Top 20%', 'Top 25%']
                    ],
                ])
                ->width('full'),
        ];
    }

    /**
     * Get the URI key for the dashboard.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'data-breakdown';
    }

    public static function label()
    {
        return 'Data Breakdown';
    }
}
