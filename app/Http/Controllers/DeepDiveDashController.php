<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;

class DeepDiveDashController extends Controller
{
    public function byOrder($n = 1, $filter)
    {
        $whereDate = '';
        if (isset($filter)) {
            $ranges = (new TestController())->getRange(
                $filter
            );
            $whereDate = " and items.created_at between '" . $ranges[0]->format('Y-m-d h:m:s') . "' and '" . $ranges[1]->format('Y-m-d h:m:s') . "'";
        }

        $db_data = DB::select("
        SELECT items.name, count(items.name) as amount, sum(orders.grand_total) total_sum 
        FROM `ordered_items` as items 
        inner join orders on items.order_id = orders.id
        where orders.status = 'complete' and orders.customer_order_number = " . $n . $whereDate . " 
        group by items.name
        order by amount desc, total_sum desc
        limit 10
        ");

        switch ($n) {
            case 1:
                $which = 'first';
                break;

            case 2:
                $which = 'secound';
                break;

            case 3:
                $which = 'third';
                break;

            case 4:
                $which = 'fourth';
                break;

            case 5:
                $which = 'fifth';
                break;

            default:
                $which = 'first';
                break;
        }

        $timeFilters = (new DateTimeController())->getFilters();
        $filters = array();
        foreach ($timeFilters as $key => $value) {
            array_push($filters, ['key' => $key, 'value' => $value]);
        }

        return [
            'title' => 'Most popular ' . $which . ' order products',
            'heads' => ['name', 'amount', 'total sum'],
            'rows' => $db_data,
            'timeFilters' => $filters
        ];
    }
}
