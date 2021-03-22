<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderedItem;
use App\Models\Store;
use App\Models\User;
use App\Models\UserStoresPivot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;

class TestController extends Controller
{
    public function test($store_id = 0)
    {
        ini_set('max_execution_time', 3000);
        //clearing DB
        $clear = Customer::query()->delete();
        if ($clear) dump("Database cleared");

        $status = 'complete';

        //finding stores
        $store_id ? $stores = [$store_id] : $stores = Order::groupBy('store_id')->get('store_id');
        if ($stores) dump("Found: " . count($stores) . " stores");

        foreach ($stores as $store) {
            $store_query = ' and store_id = ' . $store->store_id;

            $totalOrders = DB::select("select count(t2.customer_email) as count from(
                 select customer_email
                FROM orders as order1
                where status= '" . $status . "'" . $store_query . "
                GROUP by customer_email) as t2");
            $totalOrders = $totalOrders[0]->count;
            if ($stores) dump("Found: " . $totalOrders . " orders");

            $n = 500;
            $inserted = 0;
            for ($i = 0; $i <= $totalOrders; $i += $n) {

                //selecting data
                $data = DB::select("
                select store_id, max(order1.customer_firstname) as customer_firstname, min(order1.customer_lastname) as customer_lastname, min(order1.created_at) as first_purchase, sum(order1.total_item_count) as total_items, count(order1.customer_email) as order_times, order1.customer_email, sum(order1.grand_total) as total, sum(order1.shipping_amount) as shipping, (
                    0
                ) as cost  FROM orders as order1
                where status= '" . $status . "'" . $store_query . "
                GROUP by customer_email
                LIMIT " . $n . " 
                OFFSET " . $i . " 
                ");

                //processing data
                $customers = [];
                foreach ($data as $one) {
                    $clv = $one->total - $one->shipping - $one->cost;
                    $datetime1 = new DateTime($one->first_purchase);
                    $datetime2 = new DateTime();
                    $interval = $datetime1->diff($datetime2);

                    array_push($customers, array(
                        'email' => $one->customer_email,
                        'name' => $one->customer_firstname . ' ' . $one->customer_lastname,
                        'clv' => $clv,
                        'aclv' => round($clv / $one->order_times, 2),
                        'apfr' => round($one->order_times / $interval->format('%a'), 4),
                        'first_purchase' => $one->first_purchase,
                        'total_order_count' => $one->order_times,
                        'apv' => round($clv / $one->order_times, 2),
                        'store_id' => $one->store_id,
                        'created_at' => $datetime2,
                        'updated_at' => $datetime2
                    ));
                }

                //inserting data
                $insert_customer_data = Customer::insert($customers);
                $inserted += count($customers);
                if ($insert_customer_data) dump("Added: " . $inserted . " customers of " . $totalOrders);
            }
        }

        ini_set('max_execution_time', 150);
        return 0;
    }

    public function popularProducts($filter = null, $user_id = null)
    {
        if ($user_id == null) $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $storeQuery = '';
        if ($user->role != 'admin') {
            $stores = UserStoresPivot::where('user_id', $user_id)->get('store_id');
            $storeQuery = ' and store_id in (';
            foreach ($stores as $store) $storeQuery .= $store->store_id . ',';

            $storeQuery = substr($storeQuery, 0, -1);
            $storeQuery .= ') ';
        }

        $whereDate = '';
        if (isset($filter)) {
            $ranges = $this->getRange(
                $filter
            );
            $whereDate = " and items.created_at between '" . $ranges[0]->format('Y-m-d h:m:s') . "' and '" . $ranges[1]->format('Y-m-d h:m:s') . "' ";
        }

        $db_data = DB::select("
        SELECT items.name, count(items.name) as amount, sum(orders.grand_total) total_sum 
        FROM `ordered_items` as items 
        inner join orders on items.order_id = orders.id
        where orders.status = 'complete' " . $whereDate . $storeQuery . " 
        group by items.name
        order by amount desc
        limit 20
        ");

        $timeFilters = (new DateTimeController())->getFilters();
        $filters = array();
        foreach ($timeFilters as $key => $value) {
            array_push($filters, ['key' => $key, 'value' => $value]);
        }

        return [
            'title' => 'Most popular products',
            'heads' => ['name', 'amount', 'total sum'],
            'rows' => $db_data,
            'timeFilters' => $filters,
            'user_id' => $user_id
        ];
    }

    public function getRange($range)
    {
        $dates = explode(',', $range);

        if (isset($dates[1])) {
            return [
                Carbon::createFromFormat('Y-m-d', $dates[0]),
                Carbon::createFromFormat('Y-m-d', $dates[1])
            ];
        }

        if ($range == 'TODAY') {
            return [
                today(),
                now(),
            ];
        }

        if ($range == 'MTD') {
            return [
                now()->firstOfMonth(),
                now(),
            ];
        }

        if ($range == 'QTD') {
            return [
                Carbon::firstDayOfQuarter(),
                now()
            ];
        }

        if ($range == 'YTD') {
            return [
                now()->firstOfYear(),
                now(),
            ];
        }

        return [
            now()->subDays($range),
            now(),
        ];
    }
}
