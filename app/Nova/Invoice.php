<?php

namespace App\Nova;

use App\Nova\Actions\PrintReceipt;
use App\Nova\Metrics\AverageSales;
use App\Nova\Metrics\TotalSales;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class Invoice extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Invoice';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'invoice_no';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'invoice_no',
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
            Text::make("Invoice no")
                ->rules('required', 'max:254')
                ->creationRules('unique:invoices,invoice_no')
                ->updateRules('unique:invoices,invoice_no,{{resourceId}}'),

            Text::make("Type")
                ->rules("required"),

            BelongsTo::make("Branch"),

            BelongsTo::make("Terminal"),
            
            BelongsTo::make("Creator", 'user', 'App\Nova\User'),

            DateTime::make("Canceled on"),


            Heading::make("Payment information")->exceptOnForms(),

            Textarea::make("Remarks"),

            new Panel("Payment information", $this->paymentFields()),

            HasMany::make("Sessions"),

            HasMany::make("Items"),
        ];
    }

    protected function paymentFields()
    {
        return [
            Text::make("Payment type")
                ->rules('required'),

            Number::make("Subtotal")
                ->rules("required"),

            Number::make("Discount")
                ->step(0.01),

            Number::make("Tax")
                ->step(0.01),

            Number::make("Total")
                ->step(0.01),
                
            Number::make("Paid")
                ->step(0.01),
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
        return [
            new AverageSales,
            new TotalSales,
        ];
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
        return [
            (new PrintReceipt)->showOnTableRow(),
        ];
    }
}
