<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Member extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Member';

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
        'identifier', 'name', 'phone', 'email', 'city'
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

            Text::make("Identifier")
                ->exceptOnForms()
                ->sortable(),

            Text::make("Name")
                ->rules("required")
                ->sortable(),

            Text::make("Email")
                ->rules("required")
                ->sortable(),

            Text::make("Phone number")
                ->rules("required")
                ->sortable(),

            Select::make("Gender")
                ->options([
                    "Male" => "Male",
                    "Female" => "Female"
                ])
                ->sortable(),

            Date::make("Birthdate")
                ->sortable()
                ->rules("required"),

            Heading::make("Address information"),

            Text::make("Address line 1")
                ->hideFromIndex(),

            Text::make("Address line 2")
                ->hideFromIndex(),

            Text::make("Postcode")
                ->hideFromIndex(),

            Text::make("City")
                ->rules("required")
                ->sortable(),

            Text::make("State")
                ->rules("required")
                ->sortable(),

            Text::make("Country")
                ->hideFromIndex()
                ->sortable(),

            HasMany::make("Visits"),
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
