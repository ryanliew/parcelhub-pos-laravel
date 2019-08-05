<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use LifeOnScreen\SortRelations\SortRelations;

class Product extends Resource
{
    use SortRelations;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Product';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'sku';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'sku', 'description'
    ];

    public static $sortRelations = [
        'id' => "products.id",
        // overriding product_type relation sorting
        'product_type'         => [
            // sorting multiple columns
            'product_types.name',
        ],
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
            ID::make()->sortable(),

            Text::make("SKU")
                ->sortable()
                ->rules('required', 'max:254')
                ->creationRules('unique:products,sku')
                ->updateRules('unique:products,sku,{{resourceId}}'),

            Textarea::make('Description'),

            Boolean::make('Is tax inclusive'),

            BelongsTo::make("Vendor")->nullable(),

            BelongsTo::make("Product type")
                ->sortable(),

            BelongsTo::make("Tax"),

            Number::make("Hour start")
                ->step(0.01)
                ->sortable(),

            Number::make("Hour end")
                ->step(0.01)
                ->sortable(),

            Number::make("Price")
                ->step(0.01)
                ->sortable(),

            Number::make("Member price")
                ->step(0.01)
                ->sortable(),
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
        // You can modify your base query here.
        return $query
                ->select('products.*', 'product_types.id as product_types_id', 'product_types.name')
                ->leftJoin('product_types', 'product_types.id', '=', 'products.product_type_id');
    }
}
