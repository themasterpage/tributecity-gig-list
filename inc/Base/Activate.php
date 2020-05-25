<?php

namespace Inc\Base;

/**
 * @package TributeCityGigList
 *
 */
class Activate
{
    public static function activate()
    {
        flush_rewrite_rules();
    }
}
