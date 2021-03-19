<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class Order extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Order::class;

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
        'id', 'status', 'customer_email', 'customer_firstname', 'customer_lastname'
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

            Select::make('Status', 'status')->options([
                'canceled' => 'Canceled',
                'closed' => 'Closed',
                'complete' => 'Complete',
            ])->rules('required'),

            BelongsTo::make('Customer'),
            Number::make('Order ID', 'order_id')->onlyOnForms()->sortable()->rules('required')->min(1)->step(1),
            Number::make('Store ID', 'store_id')->onlyOnForms()->sortable()->rules('required')->min(1)->step(1),
            Text::make('Customer First Name', 'customer_firstname')->sortable()->rules('required', 'max:254'),
            Text::make('Customer Last Name', 'customer_lastname')->sortable()->rules('required', 'max:254'),
            Number::make('Grand Total', 'grand_total')->sortable()->rules('required')->min(1)->step(0.01),
            Number::make('Shipping Amount', 'shipping_amount')->sortable()->rules('required')->min(1)->step(0.01),

            // $table->string('customer_order_number');
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
