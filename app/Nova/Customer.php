<?php

namespace App\Nova;

use App\Models\UserStoresPivot;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;

class Customer extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Customer::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email'
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
            Text::make('Name', 'name')->sortable()->rules('required', 'max:254'),
            Text::make('Email', 'email')->sortable()->rules('required', 'email', 'max:254'),
            HasMany::make('Orders'),
            BelongsTo::make('Store'),
            Number::make('Total Order Count', 'total_order_count')->onlyOnForms()->sortable()->min(1)->step(1),
            Number::make('CLV', 'clv')->sortable()->min(1)->step(1),
            Number::make('ACLV', 'aclv')->sortable()->min(1)->step(1),
            Number::make('APV', 'apv')->sortable()->min(1)->step(1),
            Number::make('APFR', 'apfr')->sortable()->min(1)->step(1),
            DateTime::make('First Purchase', 'first_purchase')->sortable(),
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

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (auth()->user()->role == 'admin') return $query;

        return $query->whereIn('store_id', UserStoresPivot::where('user_id', $request->user()->id)->get('store_id'));
    }
}
