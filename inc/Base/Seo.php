<?php
/**
 * SEO helpers: semantic structure support + JSON-LD Event schema.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Builds Schema.org JSON-LD for gig listings and single events.
 */
class Seo {

	/**
	 * Emit JSON-LD for a list of gigs (ItemList of MusicEvent).
	 *
	 * @param array<int, object>|mixed $data    API response.
	 * @param string                   $list_url Canonical list URL (no gig_id).
	 * @param bool                     $archive  Whether this is archive mode.
	 * @return string Script tag HTML or empty string.
	 */
	public static function list_json_ld( $data, string $list_url, bool $archive = false ): string {
		if ( ! is_array( $data ) || empty( $data ) ) {
			return '';
		}

		$items = array();
		$pos   = 0;

		foreach ( $data as $gig ) {
			if ( ! is_object( $gig ) ) {
				continue;
			}
			$event = self::event_from_gig( $gig, $list_url );
			if ( empty( $event ) ) {
				continue;
			}
			++$pos;
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos,
				'item'     => $event,
			);
		}

		if ( empty( $items ) ) {
			return '';
		}

		$graph = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'ItemList',
			'name'            => $archive
				? __( 'Archived show listings', 'tributecity-gig-list' )
				: __( 'Current show listings', 'tributecity-gig-list' ),
			'numberOfItems'   => count( $items ),
			'itemListElement' => $items,
		);

		return self::script_tag( $graph );
	}

	/**
	 * Emit JSON-LD for a single gig detail.
	 *
	 * @param object $gig      Gig object.
	 * @param string $list_url List URL base.
	 * @return string Script tag HTML or empty string.
	 */
	public static function detail_json_ld( $gig, string $list_url ): string {
		if ( ! is_object( $gig ) ) {
			return '';
		}
		$event = self::event_from_gig( $gig, $list_url );
		if ( empty( $event ) ) {
			return '';
		}
		$event['@context'] = 'https://schema.org';
		return self::script_tag( $event );
	}

	/**
	 * Map a TributeCity gig object to Schema.org MusicEvent.
	 *
	 * @param object $gig      Gig payload.
	 * @param string $list_url List page URL for detail links.
	 * @return array<string, mixed>
	 */
	private static function event_from_gig( $gig, string $list_url ): array {
		$name = isset( $gig->gig_name ) ? (string) $gig->gig_name : '';
		if ( '' === $name ) {
			return array();
		}

		$start = self::iso_datetime(
			isset( $gig->start_date ) ? (string) $gig->start_date : '',
			isset( $gig->start_time ) ? (string) $gig->start_time : ''
		);

		$event = array(
			'@type' => 'MusicEvent',
			'name'  => $name,
		);

		if ( $start ) {
			$event['startDate'] = $start;
		}

		$end = self::iso_datetime(
			isset( $gig->end_date ) ? (string) $gig->end_date : '',
			isset( $gig->end_time ) ? (string) $gig->end_time : ''
		);
		if ( $end ) {
			$event['endDate'] = $end;
		}

		if ( ! empty( $gig->description ) ) {
			$event['description'] = wp_strip_all_tags( (string) $gig->description );
		}

		if ( ! empty( $gig->gig_id ) && $list_url ) {
			$event['url'] = add_query_arg( 'gig_id', sanitize_key( (string) $gig->gig_id ), $list_url );
		} elseif ( ! empty( $gig->event_url ) ) {
			$event['url'] = esc_url_raw( (string) $gig->event_url );
		}

		$band = isset( $gig->band_name ) ? (string) $gig->band_name : '';
		if ( $band ) {
			$event['performer'] = array(
				'@type' => 'MusicGroup',
				'name'  => $band,
			);
		}

		$venue_name = isset( $gig->venue_name ) ? (string) $gig->venue_name : '';
		$location   = isset( $gig->location ) ? (string) $gig->location : '';
		if ( $venue_name || $location ) {
			$place = array( '@type' => 'Place' );
			if ( $venue_name ) {
				$place['name'] = $venue_name;
			}
			$address = array( '@type' => 'PostalAddress' );
			if ( ! empty( $gig->address_1 ) ) {
				$address['streetAddress'] = (string) $gig->address_1;
				if ( ! empty( $gig->address_2 ) ) {
					$address['streetAddress'] .= ', ' . (string) $gig->address_2;
				}
			}
			if ( $location ) {
				// "City, State" style strings from the API.
				$parts = array_map( 'trim', explode( ',', $location ) );
				if ( ! empty( $parts[0] ) ) {
					$address['addressLocality'] = $parts[0];
				}
				if ( ! empty( $parts[1] ) ) {
					$address['addressRegion'] = $parts[1];
				}
			}
			if ( ! empty( $gig->country ) ) {
				$address['addressCountry'] = (string) $gig->country;
			}
			if ( ! empty( $gig->postal_code ) ) {
				$address['postalCode'] = (string) $gig->postal_code;
			}
			if ( count( $address ) > 1 ) {
				$place['address'] = $address;
			}
			$event['location'] = $place;
		}

		$offers = array();
		if ( ! empty( $gig->ticket_url ) ) {
			$offer = array(
				'@type' => 'Offer',
				'url'   => esc_url_raw( (string) $gig->ticket_url ),
			);
			if ( ! empty( $gig->free ) ) {
				$offer['price']         = '0';
				$offer['priceCurrency'] = 'USD';
			} elseif ( ! empty( $gig->ticket_price ) ) {
				$offer['price'] = (string) $gig->ticket_price;
			}
			if ( ! empty( $gig->sold_out ) ) {
				$offer['availability'] = 'https://schema.org/SoldOut';
			} else {
				$offer['availability'] = 'https://schema.org/InStock';
			}
			$offers[] = $offer;
		}
		if ( ! empty( $offers ) ) {
			$event['offers'] = $offers;
		}

		if ( ! empty( $gig->poster ) ) {
			$event['image'] = TributeCityApi::MEDIA_BASE_URL . ltrim( (string) $gig->poster, '/' );
		}

		if ( ! empty( $gig->event_url ) ) {
			$event['sameAs'] = array( esc_url_raw( (string) $gig->event_url ) );
		}

		/**
		 * Filter a single MusicEvent schema node before JSON-LD output.
		 *
		 * @param array<string, mixed> $event Schema node.
		 * @param object               $gig   Source gig.
		 */
		return (array) apply_filters( 'tributecity_gig_list_schema_event', $event, $gig );
	}

	/**
	 * Build an ISO-8601 datetime string when possible.
	 *
	 * @param string $date Y-m-d.
	 * @param string $time H:i:s or empty.
	 * @return string
	 */
	private static function iso_datetime( string $date, string $time ): string {
		$date = trim( $date );
		if ( '' === $date ) {
			return '';
		}
		$time = trim( $time );
		$raw  = $date . ( $time ? ' ' . $time : ' 00:00:00' );
		$ts   = strtotime( $raw );
		if ( ! $ts ) {
			return $date;
		}
		return wp_date( 'c', $ts );
	}

	/**
	 * Wrap a data array in a JSON-LD script element.
	 *
	 * @param array<string, mixed> $data Schema payload.
	 * @return string
	 */
	private static function script_tag( array $data ): string {
		$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		if ( ! is_string( $json ) || '' === $json ) {
			return '';
		}
		return '<script type="application/ld+json">' . $json . '</script>' . "\n";
	}
}
