<?php

/**
 * @package TributeCityGigList
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('tributecity_band_id');
delete_option('tributecity_token');
