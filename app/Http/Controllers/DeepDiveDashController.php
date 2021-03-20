<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeepDiveDashController extends Controller
{
    public function byOrder($n)
    {
        $db_data = DB::select("
        SELECT items.name, count(items.name) as amount, sum(orders.grand_total) total_sum 
        FROM `ordered_items` as items 
        inner join orders on items.order_id = orders.id
        where orders.status = 'complete' and orders.customer_order_number = " . $n . "
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

        return [
            'title' => 'Most popular ' . $which . ' order products',
            'heads' => ['name', 'amount', 'total sum'],
            'rows' => $db_data
        ];
    }
}
