<?php
/**
 * Remote TributeCity API client.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Fetches gig data from tributecity.com with caching and error handling.
 */
class TributeCityApi {

	/**
	 * Remote API endpoint for gig data.
	 */
	private const API_ENDPOINT = 'https://tributecity.com/api/gig';

	/**
	 * Default cache lifetime in seconds (15 minutes).
	 */
	private const CACHE_TTL = 15 * MINUTE_IN_SECONDS;

	/**
	 * Media base URL for poster images hosted by TributeCity.
	 */
	public const MEDIA_BASE_URL = 'https://tributecity.com/media/';

	/**
	 * Request gig data from the TributeCity API.
	 *
	 * @param array<string, mixed> $options Request options: gig_id, archive, limit.
	 * @return array<int, object>|object|false Decoded response or false on failure.
	 */
	public static function get_api_data( array $options = array() ) {
		$band_id = (string) get_option( 'tributecity_band_id', '' );
		$token   = (string) get_option( 'tributecity_token', '' );

		if ( '' === $band_id || '' === $token ) {
			return false;
		}

		$gig_id  = isset( $options['gig_id'] ) ? sanitize_key( (string) $options['gig_id'] ) : '';
		$archive = ! empty( $options['archive'] );
		$limit   = isset( $options['limit'] ) ? absint( $options['limit'] ) : 0;

		$cache_key = 'tributecity_gigs_' . md5(
			wp_json_encode(
				array(
					'band'    => $band_id,
					'gig'     => $gig_id,
					'archive' => $archive,
					'limit'   => $limit,
				)
			)
		);

		$cached = get_transient( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		// TributeCity API accepts GET/HEAD only (POST returns 405).
		$query = array(
			'band_id' => $band_id,
		);

		if ( '' !== $gig_id ) {
			$query['gig_id'] = $gig_id;
		}
		if ( $archive ) {
			$query['archive'] = 1;
		}
		if ( $limit > 0 ) {
			$query['limit'] = $limit;
		}

		$url = add_query_arg( $query, self::API_ENDPOINT );

		/**
		 * Filter HTTP request arguments sent to TributeCity.
		 *
		 * @param array<string, mixed> $args    Request args for wp_remote_get().
		 * @param array<string, mixed> $options Original shortcode/request options.
		 * @param string               $url     Request URL including query args.
		 */
		$args = apply_filters(
			'tributecity_gig_list_api_request_args',
			array(
				'timeout' => 15,
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'Accept'        => 'application/json',
				),
			),
			$options,
			$url
		);

		/**
		 * Filter the TributeCity API request URL.
		 *
		 * @param string               $url     Full request URL.
		 * @param array<string, mixed> $options Original shortcode/request options.
		 */
		$url = (string) apply_filters( 'tributecity_gig_list_api_url', $url, $options );

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return false;
		}

		$raw  = wp_remote_retrieve_body( $response );
		$data = json_decode( $raw );

		if ( null === $data && JSON_ERROR_NONE !== json_last_error() ) {
			return false;
		}

		/**
		 * Filter how long API responses are cached (seconds). Use 0 to disable.
		 *
		 * @param int                  $ttl     Cache lifetime in seconds.
		 * @param array<string, mixed> $options Request options.
		 */
		$ttl = (int) apply_filters( 'tributecity_gig_list_cache_ttl', self::CACHE_TTL, $options );

		if ( $ttl > 0 ) {
			set_transient( $cache_key, $data, $ttl );
		}

		return $data;
	}

	/**
	 * Delete all cached TributeCity gig transients.
	 *
	 * @return void
	 */
	public static function clear_cache(): void {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				$wpdb->esc_like( '_transient_tributecity_gigs_' ) . '%',
				$wpdb->esc_like( '_transient_timeout_tributecity_gigs_' ) . '%'
			)
		);
	}
}
