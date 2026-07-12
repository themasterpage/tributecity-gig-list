<?php
/**
 * Admin settings field callbacks and sanitizers.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Api\Callbacks;

use TributeCity\GigList\Base\BaseController;
use TributeCity\GigList\Base\StyleManager;
use TributeCity\GigList\Base\TributeCityApi;

defined( 'ABSPATH' ) || exit;

/**
 * Renders admin UI pieces and sanitizes option values.
 */
class AdminCallbacks extends BaseController {

	/**
	 * Render the main settings page template.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'tributecity-gig-list' ) );
		}

		require $this->plugin_path . 'templates/admin-settings.php';
	}

	/**
	 * Sanitize API token (plain text, no tags).
	 *
	 * @param mixed $input Raw option value.
	 * @return string
	 */
	public function sanitize_text( $input ): string {
		$value = is_string( $input ) ? $input : '';
		$value = sanitize_text_field( wp_unslash( $value ) );
		TributeCityApi::clear_cache();
		return $value;
	}

	/**
	 * Sanitize band ID as a positive integer string.
	 *
	 * @param mixed $input Raw option value.
	 * @return string
	 */
	public function sanitize_band_id( $input ): string {
		$value = is_scalar( $input ) ? (string) $input : '';
		$value = preg_replace( '/[^0-9]/', '', $value );
		TributeCityApi::clear_cache();
		return is_string( $value ) ? $value : '';
	}

	/**
	 * Sanitize checkbox to 1 or 0.
	 *
	 * @param mixed $input Raw option value.
	 * @return int
	 */
	public function sanitize_checkbox( $input ): int {
		return ( ! empty( $input ) ) ? 1 : 0;
	}

	/**
	 * Sanitize style theme slug.
	 *
	 * @param mixed $input Raw option value.
	 * @return string
	 */
	public function sanitize_style_theme( $input ): string {
		return StyleManager::sanitize_theme( $input );
	}

	/**
	 * Sanitize list layout slug.
	 *
	 * @param mixed $input Raw option value.
	 * @return string
	 */
	public function sanitize_list_layout( $input ): string {
		return StyleManager::sanitize_layout( $input );
	}

	/**
	 * Sanitize font size slug.
	 *
	 * @param mixed $input Raw option value.
	 * @return string
	 */
	public function sanitize_font_size( $input ): string {
		return StyleManager::sanitize_font_size( $input );
	}

	/**
	 * Section description for API credentials.
	 *
	 * @return void
	 */
	public function section_api(): void {
		echo '<p>';
		echo esc_html__(
			'This plugin displays show listings for TributeCity Pro accounts. Enter the API token and Band ID from your TributeCity dashboard (API Functionality manager).',
			'tributecity-gig-list'
		);
		echo '</p>';
		echo '<p class="description">';
		echo esc_html__(
			'Requests are sent only when a page containing the shortcode is viewed, using the credentials you configure here.',
			'tributecity-gig-list'
		);
		echo '</p>';
	}

	/**
	 * Section description for display options.
	 *
	 * @return void
	 */
	public function section_display(): void {
		echo '<p>';
		echo esc_html__( 'Control how gig listings appear on your site.', 'tributecity-gig-list' );
		echo '</p>';
	}

	/**
	 * Section description for styling.
	 *
	 * @return void
	 */
	public function section_styling(): void {
		echo '<p>';
		echo esc_html__(
			'Choose whether listings inherit your WordPress theme styles, pick a visual theme, and select a list layout (table, cards, or stacked list). Layout works with any theme, including site styles.',
			'tributecity-gig-list'
		);
		echo '</p>';
	}

	/**
	 * Token field markup.
	 *
	 * @return void
	 */
	public function field_token(): void {
		$value = (string) get_option( 'tributecity_token', '' );
		printf(
			'<input type="password" class="regular-text" id="tributecity_token" name="tributecity_token" value="%s" autocomplete="off" spellcheck="false" />',
			esc_attr( $value )
		);
		echo '<p class="description">';
		echo esc_html__( 'API bearer token from your TributeCity Pro dashboard. Stored in your WordPress database.', 'tributecity-gig-list' );
		echo '</p>';
	}

	/**
	 * Band ID field markup.
	 *
	 * @return void
	 */
	public function field_band_id(): void {
		$value = (string) get_option( 'tributecity_band_id', '' );
		printf(
			'<input type="text" class="regular-text" id="tributecity_band_id" name="tributecity_band_id" value="%s" inputmode="numeric" pattern="[0-9]*" />',
			esc_attr( $value )
		);
		echo '<p class="description">';
		echo esc_html__( 'Numeric Band ID from your TributeCity Pro account.', 'tributecity-gig-list' );
		echo '</p>';
	}

	/**
	 * Generic checkbox field.
	 *
	 * @param array<string, mixed> $args Field args (label_for, hint).
	 * @return void
	 */
	public function field_checkbox( array $args ): void {
		$name    = isset( $args['label_for'] ) ? (string) $args['label_for'] : '';
		$hint    = isset( $args['hint'] ) ? (string) $args['hint'] : '';
		$checked = (int) get_option( $name, 0 );

		if ( '' === $name ) {
			return;
		}

		// Hidden field ensures unchecked boxes save as 0 (Settings API omits missing keys).
		printf(
			'<input type="hidden" name="%1$s" value="0" />
			<label for="%1$s"><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s /> %3$s</label>',
			esc_attr( $name ),
			checked( 1, $checked, false ),
			esc_html( $hint )
		);
	}

	/**
	 * “Use site styles” checkbox.
	 *
	 * @return void
	 */
	public function field_use_theme_styles(): void {
		$name    = StyleManager::OPTION_USE_THEME;
		$checked = (int) get_option( $name, 0 );

		printf(
			'<input type="hidden" name="%1$s" value="0" />
			<label for="%1$s"><input type="checkbox" id="%1$s" name="%1$s" value="1" class="tributecity-use-theme-styles" %2$s /> %3$s</label>',
			esc_attr( $name ),
			checked( 1, $checked, false ),
			esc_html__( 'Use the current site / theme styles (minimal plugin styling only).', 'tributecity-gig-list' )
		);
		echo '<p class="description">';
		echo esc_html__(
			'When enabled, the plugin only applies layout structure. Fonts, colors, and link styles come from your active theme.',
			'tributecity-gig-list'
		);
		echo '</p>';
	}

	/**
	 * Theme radio cards for visual overrides.
	 *
	 * @return void
	 */
	public function field_style_theme(): void {
		$current = StyleManager::get_active_theme();
		$themes  = StyleManager::get_themes();
		$use     = StyleManager::use_theme_styles();

		echo '<div class="tributecity-theme-picker' . ( $use ? ' is-disabled' : '' ) . '" id="tributecity-theme-picker" ' . ( $use ? 'aria-disabled="true"' : '' ) . '>';
		echo '<p class="description tributecity-theme-picker__hint">';
		echo esc_html__( 'Pick an override theme when “Use site styles” is turned off.', 'tributecity-gig-list' );
		echo '</p>';
		echo '<div class="tributecity-theme-picker__grid" role="radiogroup" aria-label="' . esc_attr__( 'Style theme', 'tributecity-gig-list' ) . '">';

		foreach ( $themes as $slug => $theme ) {
			$input_id = 'tributecity_style_theme_' . $slug;
			$selected = ( $current === $slug );
			$colors   = isset( $theme['colors'] ) && is_array( $theme['colors'] ) ? $theme['colors'] : array();

			echo '<label class="tributecity-theme-card' . ( $selected ? ' is-selected' : '' ) . '" for="' . esc_attr( $input_id ) . '">';
			printf(
				'<input type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s %5$s />',
				esc_attr( $input_id ),
				esc_attr( StyleManager::OPTION_STYLE_THEME ),
				esc_attr( $slug ),
				checked( $current, $slug, false ),
				disabled( $use, true, false )
			);

			echo '<span class="tributecity-theme-card__swatches" aria-hidden="true">';
			foreach ( $colors as $color ) {
				printf(
					'<span class="tributecity-theme-card__swatch" style="background:%s"></span>',
					esc_attr( $color )
				);
			}
			echo '</span>';

			echo '<span class="tributecity-theme-card__label">' . esc_html( $theme['label'] ) . '</span>';
			echo '<span class="tributecity-theme-card__desc">' . esc_html( $theme['description'] ) . '</span>';
			echo '</label>';
		}

		echo '</div></div>';
	}

	/**
	 * List layout radio cards (table / cards / stacked list).
	 *
	 * @return void
	 */
	public function field_list_layout(): void {
		$current = StyleManager::get_active_layout();
		$layouts = StyleManager::get_layouts();
		$themes  = StyleManager::get_themes();

		// Theme → suggested layout map for admin JS.
		$suggestions = array();
		foreach ( $themes as $slug => $theme ) {
			$suggestions[ $slug ] = isset( $theme['default_layout'] )
				? (string) $theme['default_layout']
				: StyleManager::DEFAULT_LAYOUT;
		}

		echo '<div class="tributecity-layout-picker" id="tributecity-layout-picker"';
		echo ' data-suggestions="' . esc_attr( wp_json_encode( $suggestions ) ) . '">';
		echo '<p class="description">';
		echo esc_html__(
			'Layout controls structure. Themes control colors and decoration. You can mix any layout with any theme.',
			'tributecity-gig-list'
		);
		echo '</p>';

		echo '<div class="tributecity-layout-picker__grid" role="radiogroup" aria-label="' . esc_attr__( 'List layout', 'tributecity-gig-list' ) . '">';

		foreach ( $layouts as $slug => $layout ) {
			$input_id = 'tributecity_list_layout_' . $slug;
			$selected = ( $current === $slug );

			echo '<label class="tributecity-layout-card' . ( $selected ? ' is-selected' : '' ) . '" for="' . esc_attr( $input_id ) . '">';
			printf(
				'<input type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s />',
				esc_attr( $input_id ),
				esc_attr( StyleManager::OPTION_LAYOUT ),
				esc_attr( $slug ),
				checked( $current, $slug, false )
			);

			echo '<span class="tributecity-layout-card__icon tributecity-layout-card__icon--' . esc_attr( $slug ) . '" aria-hidden="true"></span>';
			echo '<span class="tributecity-layout-card__label">' . esc_html( $layout['label'] ) . '</span>';
			echo '<span class="tributecity-layout-card__desc">' . esc_html( $layout['description'] ) . '</span>';
			echo '</label>';
		}

		echo '</div>';

		echo '<p class="tributecity-layout-picker__suggest">';
		echo '<button type="button" class="button button-secondary" id="tributecity-apply-theme-layout">';
		echo esc_html__( 'Use suggested layout for selected theme', 'tributecity-gig-list' );
		echo '</button> ';
		echo '<span class="description" id="tributecity-layout-suggestion-note"></span>';
		echo '</p>';

		echo '<p class="description">';
		echo esc_html__(
			'Optional shortcode override: [tributecity_gigs layout="cards"] (table, cards, or list).',
			'tributecity-gig-list'
		);
		echo '</p>';
		echo '</div>';
	}

	/**
	 * Font size selector for front-end display.
	 *
	 * @return void
	 */
	public function field_font_size(): void {
		$current = StyleManager::get_active_font_size();
		$sizes   = StyleManager::get_font_sizes();

		echo '<fieldset class="tributecity-font-size">';
		echo '<legend class="screen-reader-text">' . esc_html__( 'Font size', 'tributecity-gig-list' ) . '</legend>';
		echo '<p class="description" style="margin-top:0">';
		echo esc_html__(
			'Scales text on both displays: current show listings (cards / table / list) and the interactive archived shows table (including search, pager, and rows control).',
			'tributecity-gig-list'
		);
		echo '</p>';
		echo '<div class="tributecity-font-size__options" role="radiogroup" aria-label="' . esc_attr__( 'Font size', 'tributecity-gig-list' ) . '">';

		foreach ( $sizes as $slug => $size ) {
			$input_id = 'tributecity_font_size_' . $slug;
			printf(
				'<label class="tributecity-font-size__option%1$s" for="%2$s">
					<input type="radio" id="%2$s" name="%3$s" value="%4$s" %5$s />
					<span class="tributecity-font-size__label" style="font-size: calc(1rem * %6$s)">%7$s</span>
					<span class="tributecity-font-size__desc">%8$s</span>
				</label>',
				( $current === $slug ) ? ' is-selected' : '',
				esc_attr( $input_id ),
				esc_attr( StyleManager::OPTION_FONT_SIZE ),
				esc_attr( $slug ),
				checked( $current, $slug, false ),
				esc_attr( $size['scale'] ),
				esc_html( $size['label'] ),
				esc_html( $size['description'] )
			);
		}

		echo '</div></fieldset>';
	}
}
