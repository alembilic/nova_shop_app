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
            $least_no_of_orders_query = " and orders.customer_email in ( 
                select orders2.customer_email from orders as orders2 
                where orders2.customer_order_number = " . $least_no_of_orders . ")";
        } else $least_no_of_orders_query = '';

        if (isset($popular_products_on_order)) {
            $popular_products_on_order_query = " and orders.order_id in (
                select orders3.order_id from orders as orders3 
                WHERE orders3.customer_order_number = " . $popular_products_on_order . ")";
        } else $popular_products_on_order_query = '';

        if (isset($sku_in_order_no)) $sku_in_order_no_query = ' and orders4.customer_order_number = ' . $sku_in_order_no;
        else $sku_in_order_no_query = '';

        $sku_query = '';
        if (isset($selected_sku)) {
            if (count($selected_sku) > 0) {
                $sku_query = ' and items2.sku in (';
                foreach ($selected_sku as $one) {
                    $sku_query .= "'" . $one['sku'] . "',";
                }
                $sku_query = substr($sku_query, 0, -1);
                $sku_query .= ')';
            }
        }

        if (isset($selected_sku) or isset($sku_query)) {
            $whole_sku_query = "and items.id in (
                select items2.id from items as items2
                inner join orders as orders4 on items2.order_id = orders4.order_id 
                where items2.id is not null " . $sku_in_order_no_query . $sku_query . ")";
        } else $whole_sku_query = '';

        $data = DB::select("
        SELECT name, sku, count(items.id) as popularity FROM `items`
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
            'title' => 'Gatekeeper products',
            'heads' => ['name', 'sku', 'popularity'],
            'rows' => $data
        ];
    }

    public function getSelectData(Request $request)
    {
        $items = Item::where('sku', 'LIKE', '%' . $request->sku . '%')->orWhere('name', 'LIKE', '%' . $request->sku . '%')->groupBy('sku')->get(['sku', 'name'])->take(15);

        $options = [];
        foreach ($items as $item) array_push($options, ['sku' => $item->sku, 'options' => $item->sku . ' - ' . $item->name]);

        return response()->json([
            'items' => $options
        ]);
    }

    public function getFilteredData(Request $request)
    {
        return $this->getData($request->least_no_of_orders ?? null, $request->popular_products_on_order ?? null, $request->sku_in_order_no ?? null, $request->sku ?? null);
    }
}
