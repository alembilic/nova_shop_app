<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class TestController extends Controller
{
    public function test($store_id)
    {
        //clearing DB
        $clear = Customer::query()->delete();
        if ($clear) dump("Database cleared");

        $status = 'complete';

        //finding stores
        $store_id ? $stores = [$store_id] : $stores = Order::groupBy('store_id')->get('store_id');
        if ($stores) dump("Found: " . count($stores) . " stores");

        foreach ($stores as $store) {
            $store_query = ' and store_id = ' . $store->store_id;

            //selecting data
            $data = DB::select("
        select store_id, max(order1.customer_firstname) as customer_firstname, min(order1.customer_lastname) as customer_lastname, min(order1.created_at) as first_purchase, sum(order1.total_item_count) as total_items, count(order1.customer_email) as order_times, order1.customer_email, sum(order1.grand_total) as total, sum(order1.shipping_amount) as shipping, (
            select sum(base_cost) from ordered_items where order_id in (
                 select o.id
            from orders as o
            inner join
            (    SELECT customer_email
                FROM orders
                 where customer_email = order1.customer_email and status= 'complete'
                GROUP BY orders.customer_email
                HAVING count(id) > 1
            ) as o2 on o2.customer_email = o.customer_email
            )
            ) as cost  FROM orders as order1
            where status= '" . $status . "'" . $store_query . "
            GROUP by customer_email");
            if ($data) dump("Analyzed: " . count($data) . " customers");

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
            if ($insert_customer_data) dump("Added: " . count($customers) . " customers");
        }

        return 0;
    }

    public function popularProducts()
    {
        $db_data = DB::select("
        SELECT items.name, count(items.name) as amount, sum(orders.grand_total) total_sum 
        FROM `ordered_items` as items 
        inner join orders on items.order_id = orders.id
        where orders.status = 'complete'
        group by items.name
        order by amount desc
        limit 20
        ");

        return [
            'title' => 'Most popular products',
            'heads' => ['name', 'amount', 'total sum'],
            'rows' => $db_data
        ];
    }
}
