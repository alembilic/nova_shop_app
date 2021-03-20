<?php

namespace Acme\Analytics;

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Card;

class Analytics extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'analytics';
    }
}
