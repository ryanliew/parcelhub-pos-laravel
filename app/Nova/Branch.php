<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Branch extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Branch';

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
        'name', 'code'
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

            Text::make('Name')
                ->rules('required'),

            Text::make('Code')
                ->rules('required'),

            Text::make('Address')
                ->rules('required')
                ->hideFromIndex(),

            Text::make('Registration no')
                ->rules('required')
                ->hideFromIndex(),

            Text::make('Fax')
                ->hideFromIndex(),

            Text::make('Toll free no', 'tollfree')
                ->hideFromIndex(),

            Text::make("Website")
                ->hideFromIndex(),

            Number::make('Terminal count')
                ->rules('required')
                ->hideFromIndex(),

            Heading::make("Owner information")->onlyOnForms(),

            new Panel("Owner information", $this->ownerFields()),

            Heading::make('Payment information')->onlyOnForms(),

            new Panel("Payment information", $this->paymentFields()),

            HasMany::make("Terminals"),

            HasMany::make("Tables"),
        ];
    }

    protected function ownerFields()
    {
        return [
            Text::make("Business display name", 'owner')
                ->rules('required')
                ->hideFromIndex(),

            Text::make("Registered company name")
                ->rules('required')
                ->hideFromIndex(),

            Text::make('Phone', 'contact')
                ->rules('required'),

            Text::make('Email')
                ->rules('required'),
        ];
    }

    public function paymentFields()
    {
        return [
            Text::make("Payment bank name", 'payment_bank')
                ->rules('required')
                ->hideFromIndex(),

            Text::make("Payment account number", 'payment_acc_no')
                ->rules('required')
                ->hideFromIndex(),

            Boolean::make("Has SST", "has_gst")
                ->hideFromIndex(),
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
