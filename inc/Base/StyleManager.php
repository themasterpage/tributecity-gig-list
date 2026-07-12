<?php
/**
 * Front-end style / theme / layout settings helpers.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Resolves style mode, themes, layouts, and CSS class names.
 */
class StyleManager {

	/**
	 * Option: use site global styles (minimal structural CSS only).
	 */
	public const OPTION_USE_THEME = 'tributecity_use_theme_styles';

	/**
	 * Option: selected override theme slug.
	 */
	public const OPTION_STYLE_THEME = 'tributecity_style_theme';

	/**
	 * Option: list layout slug (table, cards, list).
	 */
	public const OPTION_LAYOUT = 'tributecity_list_layout';

	/**
	 * Option: front-end font size slug.
	 */
	public const OPTION_FONT_SIZE = 'tributecity_font_size';

	/**
	 * Default override theme when not inheriting the site theme.
	 */
	public const DEFAULT_THEME = 'classic';

	/**
	 * Default list layout.
	 */
	public const DEFAULT_LAYOUT = 'table';

	/**
	 * Default font size.
	 */
	public const DEFAULT_FONT_SIZE = 'medium';

	/**
	 * Available visual override themes.
	 *
	 * @return array<string, array{label: string, description: string, colors: array<int, string>, default_layout: string}>
	 */
	public static function get_themes(): array {
		return array(
			'classic'        => array(
				'label'          => __( 'Classic', 'tributecity-gig-list' ),
				'description'    => __( 'Clean light table with neutral admin-style grays. Good default for most sites.', 'tributecity-gig-list' ),
				'colors'         => array( '#f0f0f1', '#1d2327', '#2271b1' ),
				'default_layout' => 'table',
			),
			'dark-stage'     => array(
				'label'          => __( 'Dark Stage', 'tributecity-gig-list' ),
				'description'    => __( 'Concert-ready dark background with high-contrast text and gold accents.', 'tributecity-gig-list' ),
				'colors'         => array( '#121212', '#f5f0e6', '#d4a017' ),
				'default_layout' => 'table',
			),
			'concert-poster' => array(
				'label'          => __( 'Concert Poster', 'tributecity-gig-list' ),
				'description'    => __( 'Bold typography, strong borders, and a rock-poster energy.', 'tributecity-gig-list' ),
				'colors'         => array( '#1a0a0a', '#fff8e7', '#c41e3a' ),
				'default_layout' => 'cards',
			),
			'clean-minimal'  => array(
				'label'          => __( 'Clean Minimal', 'tributecity-gig-list' ),
				'description'    => __( 'Sparse layout, thin rules, and lots of whitespace.', 'tributecity-gig-list' ),
				'colors'         => array( '#ffffff', '#111111', '#666666' ),
				'default_layout' => 'list',
			),
			'soft-cards'     => array(
				'label'          => __( 'Soft Cards', 'tributecity-gig-list' ),
				'description'    => __( 'Rounded cards, soft shadows, and a modern marketing-site feel.', 'tributecity-gig-list' ),
				'colors'         => array( '#f8fafc', '#0f172a', '#4f46e5' ),
				'default_layout' => 'cards',
			),
		);
	}

	/**
	 * Available list layouts (structure, independent of color theme).
	 *
	 * @return array<string, array{label: string, description: string, icon: string}>
	 */
	public static function get_layouts(): array {
		return array(
			'table' => array(
				'label'       => __( 'Table', 'tributecity-gig-list' ),
				'description' => __( 'Columns for show, date, location, and details. Best for dense schedules.', 'tributecity-gig-list' ),
				'icon'        => 'table',
			),
			'cards' => array(
				'label'       => __( 'Cards', 'tributecity-gig-list' ),
				'description' => __( 'Grid of show cards with optional poster art. Great for featured listings.', 'tributecity-gig-list' ),
				'icon'        => 'cards',
			),
			'list'  => array(
				'label'       => __( 'Stacked list', 'tributecity-gig-list' ),
				'description' => __( 'Vertical rows with date emphasized. Clean for mobile-first pages.', 'tributecity-gig-list' ),
				'icon'        => 'list',
			),
		);
	}

	/**
	 * Available font size presets for front-end display.
	 *
	 * @return array<string, array{label: string, description: string, scale: string}>
	 */
	public static function get_font_sizes(): array {
		return array(
			'small'   => array(
				'label'       => __( 'Small', 'tributecity-gig-list' ),
				'description' => __( 'Compact text for dense pages.', 'tributecity-gig-list' ),
				'scale'       => '0.875',
			),
			'medium'  => array(
				'label'       => __( 'Medium', 'tributecity-gig-list' ),
				'description' => __( 'Default size — balanced for most themes.', 'tributecity-gig-list' ),
				'scale'       => '1',
			),
			'large'   => array(
				'label'       => __( 'Large', 'tributecity-gig-list' ),
				'description' => __( 'Easier reading on large screens.', 'tributecity-gig-list' ),
				'scale'       => '1.125',
			),
			'x-large' => array(
				'label'       => __( 'Extra large', 'tributecity-gig-list' ),
				'description' => __( 'Maximum emphasis for hero-style listings.', 'tributecity-gig-list' ),
				'scale'       => '1.25',
			),
		);
	}

	/**
	 * Whether the listing should inherit the active WordPress theme styles.
	 *
	 * @return bool
	 */
	public static function use_theme_styles(): bool {
		return (bool) get_option( self::OPTION_USE_THEME, 0 );
	}

	/**
	 * Active override theme slug (ignored when inheriting site styles).
	 *
	 * @return string
	 */
	public static function get_active_theme(): string {
		$theme  = (string) get_option( self::OPTION_STYLE_THEME, self::DEFAULT_THEME );
		$themes = self::get_themes();

		if ( ! isset( $themes[ $theme ] ) ) {
			return self::DEFAULT_THEME;
		}

		return $theme;
	}

	/**
	 * Active list layout slug.
	 *
	 * @param string|null $override Optional shortcode/layout override.
	 * @return string
	 */
	public static function get_active_layout( ?string $override = null ): string {
		if ( null !== $override && '' !== $override ) {
			return self::sanitize_layout( $override );
		}

		$layout  = (string) get_option( self::OPTION_LAYOUT, self::DEFAULT_LAYOUT );
		$layouts = self::get_layouts();

		if ( ! isset( $layouts[ $layout ] ) ) {
			return self::DEFAULT_LAYOUT;
		}

		return $layout;
	}

	/**
	 * Active font size slug.
	 *
	 * @return string
	 */
	public static function get_active_font_size(): string {
		$size  = (string) get_option( self::OPTION_FONT_SIZE, self::DEFAULT_FONT_SIZE );
		$sizes = self::get_font_sizes();

		if ( ! isset( $sizes[ $size ] ) ) {
			return self::DEFAULT_FONT_SIZE;
		}

		return $size;
	}

	/**
	 * Numeric scale factor for the active font size.
	 *
	 * @return string
	 */
	public static function get_font_scale(): string {
		$sizes = self::get_font_sizes();
		$size  = self::get_active_font_size();

		return isset( $sizes[ $size ]['scale'] ) ? (string) $sizes[ $size ]['scale'] : '1';
	}

	/**
	 * Suggested layout for a visual theme (used as UX hint, not a hard lock).
	 *
	 * @param string|null $theme Theme slug.
	 * @return string
	 */
	public static function get_theme_default_layout( ?string $theme = null ): string {
		$theme  = $theme ? $theme : self::get_active_theme();
		$themes = self::get_themes();

		if ( isset( $themes[ $theme ]['default_layout'] ) ) {
			return self::sanitize_layout( $themes[ $theme ]['default_layout'] );
		}

		return self::DEFAULT_LAYOUT;
	}

	/**
	 * CSS classes for the shortcode root element.
	 *
	 * @param string|null $layout_override Optional layout override (e.g. shortcode).
	 * @return string
	 */
	public static function get_wrapper_classes( ?string $layout_override = null ): string {
		$layout  = self::get_active_layout( $layout_override );
		$font    = self::get_active_font_size();
		$classes = array(
			'tributecity-gig-list',
			'tributecity-gig-list--layout-' . sanitize_html_class( $layout ),
			'tributecity-gig-list--font-' . sanitize_html_class( $font ),
		);

		if ( self::use_theme_styles() ) {
			$classes[] = 'tributecity-gig-list--inherit';
		} else {
			$classes[] = 'tributecity-gig-list--themed';
			$classes[] = 'tributecity-gig-list--theme-' . sanitize_html_class( self::get_active_theme() );
		}

		/**
		 * Filter shortcode wrapper classes.
		 *
		 * @param array<int, string> $classes CSS classes.
		 * @param string             $layout  Active layout slug.
		 */
		$classes = apply_filters( 'tributecity_gig_list_wrapper_classes', $classes, $layout );

		return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
	}

	/**
	 * Inline style attributes for the shortcode root (CSS variables).
	 *
	 * @return string
	 */
	public static function get_wrapper_style_attr(): string {
		$scale = self::get_font_scale();
		$style = '--tcgl-font-scale: ' . esc_attr( $scale ) . ';';

		/**
		 * Filter inline styles on the shortcode wrapper.
		 *
		 * @param string $style CSS declarations (without style="" wrapper).
		 */
		$style = (string) apply_filters( 'tributecity_gig_list_wrapper_style', $style );

		return $style;
	}

	/**
	 * Sanitize theme slug option.
	 *
	 * @param mixed $input Raw value.
	 * @return string
	 */
	public static function sanitize_theme( $input ): string {
		$theme = is_string( $input ) ? sanitize_key( $input ) : self::DEFAULT_THEME;
		if ( ! isset( self::get_themes()[ $theme ] ) ) {
			return self::DEFAULT_THEME;
		}
		return $theme;
	}

	/**
	 * Sanitize layout slug option.
	 *
	 * @param mixed $input Raw value.
	 * @return string
	 */
	public static function sanitize_layout( $input ): string {
		$layout = is_string( $input ) ? sanitize_key( $input ) : self::DEFAULT_LAYOUT;
		if ( ! isset( self::get_layouts()[ $layout ] ) ) {
			return self::DEFAULT_LAYOUT;
		}
		return $layout;
	}

	/**
	 * Sanitize font size slug option.
	 *
	 * @param mixed $input Raw value.
	 * @return string
	 */
	public static function sanitize_font_size( $input ): string {
		$size = is_string( $input ) ? sanitize_key( $input ) : self::DEFAULT_FONT_SIZE;
		if ( ! isset( self::get_font_sizes()[ $size ] ) ) {
			return self::DEFAULT_FONT_SIZE;
		}
		return $size;
	}
}
