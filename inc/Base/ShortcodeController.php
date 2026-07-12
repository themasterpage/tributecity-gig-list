<?php
/**
 * Shortcode registration and rendering.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Handles [tributecity_gigs] / [tributecity-gigs] shortcodes.
 */
class ShortcodeController extends BaseController {

	/**
	 * Register shortcodes.
	 *
	 * @return void
	 */
	public function register(): void {
		add_shortcode( 'tributecity_gigs', array( $this, 'render_shortcode' ) );
		// Legacy tag retained for backward compatibility with existing content.
		add_shortcode( 'tributecity-gigs', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Shortcode callback.
	 *
	 * Attributes:
	 * - gig_id (string): Show a single gig by ID (also accepts ?gig_id= query arg).
	 * - archive (bool): Show archived gigs (also accepts ?archive= query arg).
	 * - limit (int): Limit number of gigs returned.
	 * - layout (string): table|cards|list — overrides the Styling tab default for this shortcode.
	 *
	 * @param array<string, mixed>|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts = array() ): string {
		Enqueue::enqueue_resolved_public_styles();

		$atts = shortcode_atts(
			array(
				'gig_id'  => '',
				'archive' => '',
				'limit'   => '',
				'layout'  => '',
			),
			(array) $atts,
			'tributecity_gigs'
		);

		// Query-string overrides (sanitized).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only view state.
		if ( isset( $_GET['gig_id'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$atts['gig_id'] = sanitize_key( wp_unslash( (string) $_GET['gig_id'] ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only view state.
		if ( isset( $_GET['archive'] ) ) {
			$atts['archive'] = '1';
		}

		$options = array(
			'gig_id'  => sanitize_key( (string) $atts['gig_id'] ),
			'archive' => self::to_bool( $atts['archive'] ),
			'limit'   => absint( $atts['limit'] ),
			'layout'  => sanitize_key( (string) $atts['layout'] ),
		);

		// Explicit shortcode limit (e.g. limit="5").
		$options['user_limit'] = $options['limit'] > 0;

		/*
		 * Archive uses a light interactive table (search + pagination), so we can
		 * load the full history. Optional hard cap via filter (0 = no API limit).
		 */
		if ( $options['archive'] && ! $options['user_limit'] ) {
			/**
			 * Max archived shows to fetch (0 = all). Default unlimited.
			 *
			 * @param int $limit API limit, or 0 for no limit.
			 */
			$archive_limit = (int) apply_filters( 'tributecity_gig_list_archive_limit', 0 );
			if ( $archive_limit > 0 ) {
				$options['limit'] = $archive_limit;
			}
		}

		$data = TributeCityApi::get_api_data( $options );

		if ( '' !== $options['gig_id'] ) {
			return $this->render_gig_detail( $data );
		}

		return $this->render_gig_list( $data, $options );
	}

	/**
	 * Render the gig list (table, cards, or stacked list).
	 *
	 * @param mixed                $data    API response.
	 * @param array<string, mixed> $options Request options.
	 * @return string
	 */
	public function render_gig_list( $data, array $options ): string {
		$is_archive      = ! empty( $options['archive'] );
		$layout_override = ! empty( $options['layout'] ) ? (string) $options['layout'] : null;
		$list_layout     = StyleManager::get_active_layout( $layout_override );
		$current_url     = $this->get_current_page_url();
		$date_format     = get_option( 'date_format' );

		/*
		 * Archive mode: interactive table (search + pagination).
		 * Show / date / location only — no posters or detail links.
		 */
		if ( $is_archive ) {
			$list_layout      = 'archive-table';
			$show_details     = false;
			$archive_per_page = (int) apply_filters( 'tributecity_gig_list_archive_per_page', 10 );
			// Allowed UI values are 10 / 25 / 50 (or “all” in the select). Normalize odd defaults.
			if ( ! in_array( $archive_per_page, array( 10, 25, 50 ), true ) ) {
				$archive_per_page = 10;
			}
			Enqueue::enqueue_archive_script();
		} else {
			// Current shows: posters + detail links always (including limit="N" teaser embeds).
			$show_details     = true;
			$archive_per_page = 15;
		}

		// Archive/current toggle only on full-page lists (not limit="N" widgets).
		$show_toggle = empty( $options['user_limit'] );
		$show_credit     = (bool) get_option( 'tributecity_show_credit', 0 );
		$wrapper_classes = StyleManager::get_wrapper_classes( $is_archive ? 'table' : $layout_override );
		if ( $is_archive ) {
			$wrapper_classes .= ' tributecity-gig-list--archive-interactive';
		}
		$wrapper_style = StyleManager::get_wrapper_style_attr();

		ob_start();
		// Variables intentionally available to the template.
		require $this->plugin_path . 'templates/gig-list.php';
		return (string) ob_get_clean();
	}

	/**
	 * Render a single gig detail view.
	 *
	 * @param mixed $data API response.
	 * @return string
	 */
	public function render_gig_detail( $data ): string {
		$wrapper_classes = StyleManager::get_wrapper_classes();
		$wrapper_style   = StyleManager::get_wrapper_style_attr();

		if ( empty( $data ) || ! is_array( $data ) || empty( $data[0] ) ) {
			return '<div class="' . esc_attr( $wrapper_classes ) . ' tributecity-gig-list--empty" style="' . esc_attr( $wrapper_style ) . '">' .
				esc_html__( 'This show could not be found.', 'tributecity-gig-list' ) .
				'</div>';
		}

		$gig          = $data[0];
		$date_format  = (string) get_option( 'date_format' );
		$time_format  = (string) get_option( 'time_format' );
		$hide_title   = (bool) get_option( 'tributecity_hide_title', 0 );
		$media_base   = TributeCityApi::MEDIA_BASE_URL;
		$list_url     = $this->get_current_page_url( array( 'gig_id' => null ) );
		$show_credit  = (bool) get_option( 'tributecity_show_credit', 0 );

		$extended = '';
		if ( ! empty( $gig->end_date ) ) {
			$extended = ' – ' . wp_date( $date_format, strtotime( (string) $gig->end_date ) );
		}

		$start_ts  = ! empty( $gig->start_date ) ? strtotime( (string) $gig->start_date ) : false;
		$time_ts   = ! empty( $gig->start_time ) ? strtotime( (string) $gig->start_time ) : false;
		$date_part = $start_ts ? wp_date( $date_format, $start_ts ) : '';
		$time_part = $time_ts ? wp_date( $time_format, $time_ts ) : '';
		$date_time = trim( $date_part . $extended . ( $time_part ? ', ' . $time_part : '' ) );

		// API field is ticket_price; keep price as a legacy fallback.
		$raw_price = '';
		if ( isset( $gig->ticket_price ) && '' !== (string) $gig->ticket_price ) {
			$raw_price = (string) $gig->ticket_price;
		} elseif ( isset( $gig->price ) ) {
			$raw_price = (string) $gig->price;
		}

		$display_price = $this->get_display_price(
			$raw_price,
			! empty( $gig->sold_out ),
			! empty( $gig->free )
		);

		ob_start();
		require $this->plugin_path . 'templates/gig-detail.php';
		return (string) ob_get_clean();
	}

	/**
	 * Format price display text.
	 *
	 * @param string $price    Raw price string.
	 * @param bool   $sold_out Whether the gig is sold out.
	 * @param bool   $free     Whether the gig is free.
	 * @return string
	 */
	private function get_display_price( string $price, bool $sold_out, bool $free ): string {
		if ( $sold_out ) {
			return __( 'Sold Out', 'tributecity-gig-list' );
		}
		if ( $free ) {
			return __( 'Free', 'tributecity-gig-list' );
		}
		return $price;
	}

	/**
	 * Build the current page URL with optional query arg overrides.
	 *
	 * Pass null as a value to remove a query arg.
	 *
	 * @param array<string, string|null> $overrides Query args to set or remove.
	 * @return string
	 */
	private function get_current_page_url( array $overrides = array() ): string {
		$permalink = get_permalink();
		if ( ! is_string( $permalink ) || '' === $permalink ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via esc_url_raw below.
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string) $_SERVER['REQUEST_URI'] ) : '/';
			$permalink   = home_url( $request_uri );
		}

		$url = remove_query_arg( array( 'gig_id', 'archive' ), $permalink );

		foreach ( $overrides as $key => $value ) {
			if ( null === $value || '' === $value ) {
				$url = remove_query_arg( $key, $url );
			} else {
				$url = add_query_arg( $key, $value, $url );
			}
		}

		return esc_url_raw( $url );
	}

	/**
	 * Coerce a shortcode attribute to boolean.
	 *
	 * @param mixed $value Attribute value.
	 * @return bool
	 */
	private static function to_bool( $value ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}
		$value = strtolower( trim( (string) $value ) );
		return in_array( $value, array( '1', 'true', 'yes', 'on' ), true );
	}
}
