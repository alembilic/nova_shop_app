<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;

class OrderedItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\OrderedItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('Order'),
            BelongsTo::make('Item'),
            Number::make('Parent item ID', 'parent_item_id')->onlyOnForms()->sortable()->min(1)->step(1),
            Text::make('Product Options', 'product_options')->onlyOnForms()->rules('required', 'max:500'),
            Number::make('Weight', 'weight')->sortable()->min(0)->step(0.01),
            Text::make('SKU', 'sku')->rules('max:255'),
            Text::make('Item name', 'name')->rules('required', 'max:255'),
            Number::make('Base cost', 'base_cost')->sortable()->rules('required')->min(0)->step(0.01),
            Number::make('Price', 'price')->sortable()->rules('required')->min(0)->step(0.01),
            Number::make('Original price', 'original_price')->rules('required')->sortable()->min(0)->step(0.01),
            Number::make('Discount amount', 'discount_amount')->sortable()->min(0)->step(0.01),
            Number::make('Quantity shiped', 'qty_shipped')->sortable()->min(1)->step(1),


            // $table->integer('order_id');
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
