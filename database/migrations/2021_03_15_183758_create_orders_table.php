<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->integer('order_id')->nullable();
            $table->integer('store_id');
            $table->integer('customer_id')->nullable();
            $table->string('customer_email');
            $table->string('customer_firstname')->nullable();
            $table->string('customer_lastname')->nullable();
            $table->integer('total_item_count')->nullable();
            $table->integer('customer_order_number')->nullable();
            $table->float('grand_total')->nullable();
            $table->float('shipping_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
