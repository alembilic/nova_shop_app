<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GatekeeperProductsController extends Controller
{
    public function getData()
    {
        $least_no_of_orders = 7;
        $popular_products_on_order = 6;
        $sku_in_order_no = 3;
        $sku = "5703779181424, 5703779181455, 5703779182452";

        $data = DB::select("
        SELECT name, count(items.id) as popularity FROM `items`
        inner join orders on items.order_id = orders.order_id
        where orders.order_id in (
            SELECT order_id FROM ( SELECT orders2.order_id, ROW_NUMBER() OVER (PARTITION BY orders2.customer_email ORDER BY orders2.created_at ASC) AS rn FROM orders as orders2 ) x 
            WHERE rn = " . $popular_products_on_order . ") and 
        status = 'complete' and 
        orders.customer_email in (
                SELECT customer_email FROM ( SELECT *, ROW_NUMBER() OVER (PARTITION BY orders3.customer_email ORDER BY orders3.created_at ASC) AS rn FROM orders as orders3 ) x 
                    WHERE rn = " . $least_no_of_orders . "
        ) and
        id in (
            select items2.id from items as items2 where items2.order_id in (SELECT order_id FROM ( SELECT orders4.order_id, ROW_NUMBER() OVER (PARTITION BY orders4.customer_email ORDER BY orders4.created_at ASC) AS rn FROM orders as orders4) x 
                WHERE rn = " . $sku_in_order_no . ") and 
                items2.sku in (" . $sku . ")
        )
        group by name
        order by popularity desc
        limit 20
        ");

        dd($data);
    }
}
