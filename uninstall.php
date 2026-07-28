<?php
/**
 * Uninstall handler for TributeCity Gig List.
 *
 * Fired when the plugin is uninstalled via the WordPress admin.
 *
 * @package TributeCityGigList
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

$tributecity_gig_list_options = array(
	'tributecity_band_id',
	'tributecity_token',
	'tributecity_hide_title',
	'tributecity_show_credit',
	'tributecity_use_theme_styles',
	'tributecity_style_theme',
	'tributecity_list_layout',
	'tributecity_font_size',
);

foreach ( $tributecity_gig_list_options as $tributecity_gig_list_option_name ) {
	delete_option( $tributecity_gig_list_option_name );

	// Multisite: remove per-site options if network-wide uninstall.
	if ( is_multisite() ) {
		$tributecity_gig_list_sites = get_sites( array( 'fields' => 'ids' ) );
		foreach ( $tributecity_gig_list_sites as $tributecity_gig_list_site_id ) {
			delete_blog_option( (int) $tributecity_gig_list_site_id, $tributecity_gig_list_option_name );
		}
	}
}

// Clear API response transients.
global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$wpdb->esc_like( '_transient_tributecity_gigs_' ) . '%',
		$wpdb->esc_like( '_transient_timeout_tributecity_gigs_' ) . '%'
	)
);
