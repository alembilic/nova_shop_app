<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GatekeeperProductsController extends Controller
{
    public function getData($least_no_of_orders = null, $popular_products_on_order = null, $sku_in_order_no = null, $selected_sku = null)
    {
        if (isset($least_no_of_orders)) {
            $least_no_of_orders_query = "and orders.customer_email in (
                SELECT customer_email FROM ( SELECT *, ROW_NUMBER() OVER (PARTITION BY orders3.customer_email ORDER BY orders3.created_at ASC) AS rn FROM orders as orders3 ) x 
                WHERE rn = " . $least_no_of_orders . ")";
        } else $least_no_of_orders_query = '';

        if (isset($popular_products_on_order)) {
            $popular_products_on_order_query = "and orders.order_id in (
                SELECT order_id FROM ( SELECT orders2.order_id, ROW_NUMBER() OVER (PARTITION BY orders2.customer_email ORDER BY orders2.created_at ASC) AS rn FROM orders as orders2 ) x 
                WHERE rn = " . $popular_products_on_order . ")";
        } else $popular_products_on_order_query = '';

        if (isset($sku_in_order_no)) $sku_in_order_no_query = 'WHERE rn = ' . $sku_in_order_no;
        else $sku_in_order_no_query = '';

        if (isset($selected_sku)) {
            $sku_query = 'and items2.sku in (';
            foreach ($selected_sku as $one) {
                $sku_query .= "'" . $one['sku'] . "',";
            }
            $sku_query = substr($sku_query, 0, -1);
            $sku_query .= ')';
        } else $sku_query = '';

        if (isset($selected_sku) or isset($sku_in_order_no)) {
            $whole_sku_query = "and items.id in (
            select items2.id from items as items2 where items2.order_id in (SELECT order_id FROM ( SELECT orders4.order_id, ROW_NUMBER() OVER (PARTITION BY orders4.customer_email ORDER BY orders4.created_at ASC) AS rn FROM orders as orders4) x 
            " . $sku_in_order_no_query . ") 
        " . $sku_query . ")";
        } else $whole_sku_query = '';

        $data = DB::select("
        SELECT name, count(items.id) as popularity FROM `items`
        inner join orders on items.order_id = orders.order_id
        where status = 'complete' 
        " . $popular_products_on_order_query . "
        " . $least_no_of_orders_query . "
        " . $whole_sku_query . "
        group by name
        order by popularity desc
        limit 20
        ");

        return [
            'title' => 'Popular products',
            'heads' => ['name', 'popularity'],
            'rows' => $data
        ];
    }

    public function getSelectData(Request $request)
    {
        $items = Item::where('sku', 'LIKE', '%' . $request->sku . '%')->groupBy('sku')->get(['sku'])->take(15);

        return response()->json([
            'items' => $items
        ]);
    }

    public function getFilteredData(Request $request)
    {
        return $this->getData($request->least_no_of_orders ?? null, $request->popular_products_on_order ?? null, $request->sku_in_order_no ?? null, $request->sku ?? null);
    }
}
