<?php
/**
 * Activation routines.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin activation.
 */
class Activate {

	/**
	 * Run on plugin activation.
	 *
	 * @return void
	 */
	public static function activate(): void {
		// Default options (do not overwrite existing values).
		add_option( 'tributecity_band_id', '' );
		add_option( 'tributecity_token', '' );
		add_option( 'tributecity_hide_title', 0 );
		add_option( 'tributecity_show_credit', 0 );
		add_option( StyleManager::OPTION_USE_THEME, 0 );
		add_option( StyleManager::OPTION_STYLE_THEME, StyleManager::DEFAULT_THEME );
		add_option( StyleManager::OPTION_LAYOUT, StyleManager::DEFAULT_LAYOUT );
		add_option( StyleManager::OPTION_FONT_SIZE, StyleManager::DEFAULT_FONT_SIZE );

		flush_rewrite_rules();
	}
}
