<?php

namespace Inc\Base;

/**
 * @package TributeCityGigList
 *
 */
class Deactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
