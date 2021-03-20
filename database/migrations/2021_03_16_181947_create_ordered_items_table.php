<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordered_items', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('item_id');
            $table->integer('parent_item_id')->nullable();
            $table->longText('product_options')->nullable();
            $table->float('weight')->nullable();
            $table->text('sku')->nullable();
            $table->text('name')->nullable();
            $table->float('base_cost')->nullable();
            $table->float('price')->nullable();
            $table->float('original_price')->nullable();
            $table->float('discount_amount')->nullable();
            $table->integer('qty_shipped')->nullable();
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
        Schema::dropIfExists('ordered_items');
    }
}
