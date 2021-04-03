<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GatekeeperProductsController extends Controller
{
    public function getData($total_order_count = null, $popular_products_on_order = null, $sku_in_order_no = null, $selected_sku = null)
    {
        $parsed_selected_sku = [];
        if ($selected_sku)
            foreach ($selected_sku as $sku) array_push($parsed_selected_sku, $sku['sku']);

        if (isset($sku_in_order_no) or !empty($parsed_selected_sku)) $filterBy_sku = true;
        else $filterBy_sku = false;

        // customers that have ordered more than 2 orders and on order 1 bought item with sku '5703779181424'

        $customers = DB::table('customers')->select('customers.id')
            ->join('orders', 'orders.customer_email', '=', 'customers.email')
            ->join('items', 'items.order_id', '=', 'orders.order_id')
            ->where('status', 'complete')
            ->when(isset($sku_in_order_no), function ($query) use ($sku_in_order_no) {
                return $query->where('customer_order_number', '=', $sku_in_order_no);
            })
            ->when(isset($total_order_count), function ($query) use ($total_order_count) {
                return $query->where('total_order_count', '>=', $total_order_count);
            })
            ->when($filterBy_sku, function ($query) use ($parsed_selected_sku) {
                return $query->whereIn('sku', $parsed_selected_sku);
            })
            ->get();

        //popular items from selected group of users on their 2 order

        $data = DB::table('customers')->select(['items.name', 'items.sku', DB::raw('COUNT(items.name) as quantity')])
            ->join('orders', 'orders.customer_email', '=', 'customers.email')
            ->join('items', 'items.order_id', '=', 'orders.order_id')
            ->where('status', 'complete')
            ->whereIn('customers.id', $customers->pluck('id'))
            ->when(isset($popular_products_on_order), function ($query) use ($popular_products_on_order) {
                return $query->where('customer_order_number', '=', $popular_products_on_order);
            })
            ->groupBy('items.name')
            ->orderBy('quantity', 'DESC')
            ->paginate(15);

        return [
            'title' => 'Gatekeeper products',
            'heads' => ['name', 'sku', 'quantity'],
            'allData' => $data,
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
