<?php

/*
*
* @package TributeCityGigList
Plugin Name: TributeCity Gig List
Plugin URI:  http://tributecity.com 
Description: This widget will call via API, tributecity.com for all shows by a band
Version:     1.0
Author:      Lenny Mann
Author URI:  https://themasterpage.net 
License:     GPL2 etc

Copyright YEAR PLUGIN_AUTHOR_NAME (lenny@themasterpage.net)
TributeCity Gig List is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
TributeCity Gig List is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with TributeCity Gig List. If not, see https://tributecity.com.
*/

use Inc\Base\Activate;
use Inc\Base\Deactivate;

// If file is called directly, kick them out!
defined('ABSPATH') or die('You cannot access this plugin');

// Utilize the Composer autoload file
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

function activate_tributecity_gig_list()
{
    Activate::activate();
}

function deactivate_tributecity_gig_list()
{
    Deactivate::deactivate();
}

// activation
register_activation_hook(__FILE__, 'activate_tributecity_gig_list');

// deactivation
register_deactivation_hook(__FILE__, 'deactivate_tributecity_gig_list');

if (class_exists('Inc\\Init')) {
    Inc\Init::register_services();
}
