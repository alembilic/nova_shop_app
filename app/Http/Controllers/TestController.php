<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class TestController extends Controller
{
    public function test($for_update_query = '')
    {
        //clearing DB
        $clear = Customer::query()->delete();

        if (false) {
            $for_update_query = 'and order1.customer_email in (select customer_email from customers)';
        }

        $status = 'complete';
        $store_query = ' and store_id = 1';

        $data = DB::select("
        select store_id, max(order1.customer_firstname) as customer_firstname, min(order1.customer_lastname) as customer_lastname, min(order1.created_at) as first_purchase, sum(order1.customer_order_number) as total_items, count(order1.customer_email) as order_times, order1.customer_email, sum(order1.grand_total) as total, sum(order1.shipping_amount) as shipping, (
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
            where status= '" . $status . "' " . $for_update_query . " " . $store_query . "
            GROUP by customer_email");
        dump("analyzed: " . count($data) . " orders");

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

        $insert_customer_data = Customer::insert($customers);

        dump("added: " . count($customers) . " customers");

        return 0;
    }
}
