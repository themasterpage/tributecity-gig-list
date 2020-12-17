<?php

/**
 * This is a placeholder for a future implementation of the TributeCity widget.
 */

namespace Inc\Base;

use Inc\Api\Widgets\TributeCityWidget;

class WidgetController extends BaseController
{
    public function register()
    {
        $tributecity_widget = new TributeCityWidget();
        $tributecity_widget->register();
    }
}
