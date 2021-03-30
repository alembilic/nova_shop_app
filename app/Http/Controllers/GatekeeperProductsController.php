<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GatekeeperProductsController extends Controller
{
    public function getData($least_no_of_orders = null, $popular_products_on_order = null, $sku_in_order_no = null, $selected_sku = null)
    {
        $parsed_selected_sku = [];
        if ($selected_sku)
            foreach ($selected_sku as $sku) array_push($parsed_selected_sku, $sku['sku']);

        if (isset($least_no_of_orders))
            $customer_emails = Order::where('customer_order_number', $least_no_of_orders)->where('status', 'complete')->pluck('customer_email');
        else $customer_emails = null;

        if (isset($popular_products_on_order))
            $orders_on_order = Order::where('customer_order_number', $popular_products_on_order)->where('status', 'complete')->pluck('order_id');
        else $orders_on_order = null;

        if (isset($sku_in_order_no) or !empty($parsed_selected_sku)) $filter_sku = true;
        else $filter_sku = false;

        if ($filter_sku) {
            $sku_pool = DB::table('items')->join('orders', 'items.order_id', '=', 'orders.order_id')
                ->where('status', 'complete')
                ->when(isset($sku_in_order_no), function ($query) use ($sku_in_order_no) {
                    return $query->where('customer_order_number', '=', $sku_in_order_no);
                })
                ->when(!empty($parsed_selected_sku), function ($query) use ($parsed_selected_sku) {
                    return $query->whereIn('customer_order_number', $parsed_selected_sku);
                })->pluck('items.id');
        } else $sku_pool = null;

        $data2 = DB::table('items')->selectRaw('name, count(name) as popularity')
            ->join('orders', 'items.order_id', '=', 'orders.order_id')
            ->where('status', 'complete')
            ->when(isset($least_no_of_orders), function ($query) use ($customer_emails) {
                return $query->whereIn('customer_email', $customer_emails->toArray());
            })
            ->when(isset($popular_products_on_order), function ($query) use ($orders_on_order) {
                return $query->whereIn('items.order_id', $orders_on_order->toArray());
            })
            ->when($filter_sku, function ($query) use ($sku_pool) {
                return $query->whereIn('items.id', $sku_pool->toArray());
            })
            ->groupBy('name')
            ->orderBy('popularity', 'DESC')
            ->take(20)->get();


        // if (isset($least_no_of_orders)) {
        //     $least_no_of_orders_query = " and orders.customer_email in ( 
        //         select orders2.customer_email from orders as orders2 
        //         where orders2.customer_order_number = " . $least_no_of_orders . ")";
        // } else $least_no_of_orders_query = '';

        // if (isset($popular_products_on_order)) {
        //     $popular_products_on_order_query = " and orders.order_id in (
        //         select orders3.order_id from orders as orders3 
        //         WHERE orders3.customer_order_number = " . $popular_products_on_order . ")";
        // } else $popular_products_on_order_query = '';

        // if (isset($sku_in_order_no)) $sku_in_order_no_query = ' and orders4.customer_order_number = ' . $sku_in_order_no;
        // else $sku_in_order_no_query = '';

        // $sku_query = '';
        // if (isset($selected_sku)) {
        //     if (count($selected_sku) > 0) {
        //         $sku_query = ' and items2.sku in (';
        //         foreach ($selected_sku as $one) {
        //             $sku_query .= "'" . $one['sku'] . "',";
        //         }
        //         $sku_query = substr($sku_query, 0, -1);
        //         $sku_query .= ')';
        //     }
        // }

        // if (isset($selected_sku) or isset($sku_query)) {
        //     $whole_sku_query = "and items.id in (
        //         select items2.id from items as items2
        //         inner join orders as orders4 on items2.order_id = orders4.order_id 
        //         where items2.id is not null " . $sku_in_order_no_query . $sku_query . ")";
        // } else $whole_sku_query = '';

        // $data = DB::select("
        // SELECT name, sku, count(items.id) as popularity FROM `items`
        // inner join orders on items.order_id = orders.order_id
        // where status = 'complete' 
        // " . $popular_products_on_order_query . "
        // " . $least_no_of_orders_query . "
        // " . $whole_sku_query . "
        // group by name
        // order by popularity desc
        // limit 20
        // ");


        return [
            'title' => 'Gatekeeper products',
            'heads' => ['name', 'sku', 'popularity'],
            'rows' => $data2
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
