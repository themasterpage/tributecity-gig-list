<?php
/**
 * Plugin Name:       TributeCity Gig List
 * Plugin URI:        https://tributecity.com
 * Description:       Display live and archived show listings from a TributeCity Pro band account via shortcode.
 * Version:           2.5.3
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Lenny Mann
 * Author URI:        https://themasterpage.net
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tributecity-gig-list
 * Domain Path:       /languages
 *
 * @package TributeCityGigList
 *
 * Copyright (C) 2022-2026 Lenny Mann (lenny@themasterpage.net)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

defined( 'ABSPATH' ) || exit;

define( 'TRIBUTECITY_GIG_LIST_VERSION', '2.5.3' );
define( 'TRIBUTECITY_GIG_LIST_FILE', __FILE__ );
define( 'TRIBUTECITY_GIG_LIST_PATH', plugin_dir_path( __FILE__ ) );
define( 'TRIBUTECITY_GIG_LIST_URL', plugin_dir_url( __FILE__ ) );
define( 'TRIBUTECITY_GIG_LIST_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Load Composer autoloader when present.
 */
if ( file_exists( TRIBUTECITY_GIG_LIST_PATH . 'vendor/autoload.php' ) ) {
	require_once TRIBUTECITY_GIG_LIST_PATH . 'vendor/autoload.php';
}

/**
 * Plugin activation callback.
 *
 * @return void
 */
function tributecity_gig_list_activate() {
	TributeCity\GigList\Base\Activate::activate();
}

/**
 * Plugin deactivation callback.
 *
 * @return void
 */
function tributecity_gig_list_deactivate() {
	TributeCity\GigList\Base\Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'tributecity_gig_list_activate' );
register_deactivation_hook( __FILE__, 'tributecity_gig_list_deactivate' );

/**
 * Bootstrap plugin services after plugins are loaded.
 *
 * @return void
 */
function tributecity_gig_list_init() {
	load_plugin_textdomain(
		'tributecity-gig-list',
		false,
		dirname( TRIBUTECITY_GIG_LIST_BASENAME ) . '/languages'
	);

	if ( class_exists( 'TributeCity\\GigList\\Plugin' ) ) {
		TributeCity\GigList\Plugin::register_services();
	}
}
add_action( 'plugins_loaded', 'tributecity_gig_list_init' );
