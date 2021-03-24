<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserStoresPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;

class DeepDiveDashController extends Controller
{
    public function byOrder($n = 1, $filter, $user_id)
    {
        if ($user_id == null) $user_id = auth()->user()->id;
        $user = User::find($user_id);

        $storeQuery = '';
        if ($user->role != 'admin') {
            $stores = UserStoresPivot::where('user_id', $user->id)->get('store_id');
            $storeQuery = ' and store_id in (';
            foreach ($stores as $store) $storeQuery .= $store->store_id . ',';

            $storeQuery = substr($storeQuery, 0, -1);
            $storeQuery .= ') ';
        }

        $whereDate = '';
        if (isset($filter) and $filter != 'ALL') {
            $ranges = (new AnalyzeController())->getRange(
                $filter
            );
            $whereDate = " and items.created_at between '" . $ranges[0]->format('Y-m-d h:m:s') . "' and '" . $ranges[1]->format('Y-m-d h:m:s') . "' ";
        }

        $db_data = DB::select("
        SELECT items.name, count(items.name) as amount, sum(orders2.grand_total) total_sum 
        FROM items as items 
        inner join (
           SELECT * FROM (
  			  SELECT *, ROW_NUMBER() OVER (PARTITION BY customer_email ORDER BY created_at ASC) AS rn
   				 FROM orders
					) x
				WHERE rn = " . $n . " 
        	)  as orders2 on  items.order_id = orders2.order_id
        where orders2.status = 'complete' " . $whereDate . $storeQuery . " 
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
            'timeFilters' => $filters,
            'user_id' => $user_id
        ];
    }
}
