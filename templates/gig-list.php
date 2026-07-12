<?php
/**
 * Front-end gig list template.
 *
 * Expected variables from ShortcodeController::render_gig_list():
 *
 * @package TributeCityGigList
 *
 * @var mixed                $data            API response.
 * @var array<string, mixed> $options         Request options.
 * @var string               $current_url     Base page URL.
 * @var string               $date_format     WP date format.
 * @var bool                 $show_details    Whether to show detail links.
 * @var bool                 $show_toggle     Whether to show archive/current toggle.
 * @var bool                 $show_credit     Whether to show optional credit link.
 * @var string               $wrapper_classes CSS classes for root element.
 * @var string               $wrapper_style   Inline CSS variables for root element.
 * @var string               $list_layout      Active layout slug (table|cards|list|archive-table).
 * @var int                  $archive_per_page Rows per page for interactive archive table.
 */

defined( 'ABSPATH' ) || exit;

$is_archive   = ! empty( $options['archive'] );
$has_message  = is_object( $data ) && isset( $data->message );
$has_listings = is_array( $data ) && ! empty( $data ) && ! $has_message;
$show_toggle  = isset( $show_toggle ) ? (bool) $show_toggle : empty( $options['user_limit'] );
$show_details = isset( $show_details ) ? (bool) $show_details : true;
$archive_per_page = isset( $archive_per_page ) ? max( 5, (int) $archive_per_page ) : 15;

if ( empty( $wrapper_classes ) ) {
	$wrapper_classes = 'tributecity-gig-list';
}

if ( empty( $wrapper_style ) ) {
	$wrapper_style = '';
}

if ( empty( $list_layout ) ) {
	$list_layout = 'table';
}

$media_base = \TributeCity\GigList\Base\TributeCityApi::MEDIA_BASE_URL;

/**
 * Normalize gig objects for all layouts.
 *
 * @param object $gig Gig payload.
 * @return array<string, mixed>
 */
$normalize_gig = static function ( $gig ) use ( $date_format, $show_details, $current_url, $media_base ) {
	$start_ts  = ! empty( $gig->start_date ) ? strtotime( (string) $gig->start_date ) : false;
	$date_text = $start_ts ? wp_date( $date_format, $start_ts ) : '';
	$location  = trim(
		( isset( $gig->location ) ? (string) $gig->location : '' ) .
		( ! empty( $gig->country ) ? ', ' . (string) $gig->country : '' )
	);

	$detail_url = '';
	if ( $show_details && ! empty( $gig->gig_id ) ) {
		$detail_url = add_query_arg( 'gig_id', sanitize_key( (string) $gig->gig_id ), $current_url );
	}

	// Posters for current-list layouts when the API provides one (including limit teasers).
	$poster_url = '';
	if ( $show_details && ! empty( $gig->poster ) ) {
		$poster_url = $media_base . ltrim( (string) $gig->poster, '/' );
	}

	$venue = isset( $gig->venue_name ) ? (string) $gig->venue_name : '';

	$start_iso = '';
	if ( ! empty( $gig->start_date ) ) {
		$iso_raw = (string) $gig->start_date . ( ! empty( $gig->start_time ) ? ' ' . (string) $gig->start_time : '' );
		$iso_ts  = strtotime( $iso_raw );
		if ( $iso_ts ) {
			$start_iso = wp_date( 'c', $iso_ts );
		}
	}

	return array(
		'name'       => isset( $gig->gig_name ) ? (string) $gig->gig_name : '',
		'date'       => $date_text,
		'location'   => $location,
		'venue'      => $venue,
		'detail_url' => $detail_url,
		'poster_url' => $poster_url,
		'gig_id'     => isset( $gig->gig_id ) ? (string) $gig->gig_id : '',
		'start_iso'  => $start_iso,
	);
};
?>
<section
	class="<?php echo esc_attr( $wrapper_classes ); ?>"
	data-tcgl-v="2.5.1"
	aria-labelledby="tributecity-gig-list-heading"
	<?php echo $wrapper_style ? ' style="' . esc_attr( $wrapper_style ) . '"' : ''; ?>
>
	<h2 class="tributecity-gig-list__heading" id="tributecity-gig-list-heading">
		<?php
		echo $is_archive
			? esc_html__( 'Archived show listings', 'tributecity-gig-list' )
			: esc_html__( 'Current show listings', 'tributecity-gig-list' );
		?>
	</h2>

	<?php if ( ! $has_listings ) : ?>
		<div class="tributecity-gig-list__empty">
			<?php
			echo $is_archive
				? esc_html__( 'There are no archived shows to display.', 'tributecity-gig-list' )
				: esc_html__( 'There are no shows to display.', 'tributecity-gig-list' );
			?>
		</div>
	<?php elseif ( 'archive-table' === $list_layout ) : ?>
		<?php
		$total_rows = is_array( $data ) ? count( $data ) : 0;
		?>
		<div
			class="tributecity-gig-list__archive-panel"
			data-tcgl-archive
			data-per-page="<?php echo esc_attr( (string) $archive_per_page ); ?>"
		>
			<div class="tributecity-gig-list__archive-toolbar">
				<label class="tributecity-gig-list__archive-search-label" for="tributecity-archive-search">
					<span class="screen-reader-text"><?php esc_html_e( 'Search shows', 'tributecity-gig-list' ); ?></span>
					<input
						type="search"
						id="tributecity-archive-search"
						class="tributecity-gig-list__archive-search"
						placeholder="<?php esc_attr_e( 'Search by show, date, or location…', 'tributecity-gig-list' ); ?>"
						autocomplete="off"
					/>
				</label>
				<p class="tributecity-gig-list__archive-status" data-tcgl-archive-status aria-live="polite">
					<?php
					printf(
						/* translators: %d: total archived shows */
						esc_html__( 'Showing all %d shows', 'tributecity-gig-list' ),
						(int) $total_rows
					);
					?>
				</p>
			</div>

			<div class="tributecity-gig-list__table-wrap tributecity-gig-list__table-wrap--interactive">
				<table class="tributecity-gig-list__table tributecity-gig-list__table--archive">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Show', 'tributecity-gig-list' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Date', 'tributecity-gig-list' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Location', 'tributecity-gig-list' ); ?></th>
						</tr>
					</thead>
					<tbody data-tcgl-archive-body>
						<?php foreach ( $data as $index => $gig ) : ?>
							<?php
							if ( ! is_object( $gig ) ) {
								continue;
							}
							$row         = $normalize_gig( $gig );
							$search_blob = strtolower( $row['name'] . ' ' . $row['date'] . ' ' . $row['location'] . ' ' . $row['venue'] );
							?>
							<tr
								class="tributecity-gig-list__row"
								data-tcgl-row
								data-search="<?php echo esc_attr( $search_blob ); ?>"
								data-index="<?php echo esc_attr( (string) $index ); ?>"
							>
								<td data-label="<?php esc_attr_e( 'Show', 'tributecity-gig-list' ); ?>">
									<span class="tributecity-gig-list__show-name"><?php echo esc_html( $row['name'] ); ?></span>
								</td>
								<td data-label="<?php esc_attr_e( 'Date', 'tributecity-gig-list' ); ?>">
									<?php echo esc_html( $row['date'] ); ?>
								</td>
								<td data-label="<?php esc_attr_e( 'Location', 'tributecity-gig-list' ); ?>">
									<?php echo esc_html( $row['location'] ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<p class="tributecity-gig-list__archive-empty" data-tcgl-archive-empty hidden>
				<?php esc_html_e( 'No shows match your search.', 'tributecity-gig-list' ); ?>
			</p>

			<nav class="tributecity-gig-list__archive-pager" data-tcgl-archive-pager aria-label="<?php esc_attr_e( 'Archive pages', 'tributecity-gig-list' ); ?>">
				<button type="button" class="tributecity-gig-list__pager-btn" data-tcgl-page-prev disabled>
					<?php esc_html_e( 'Previous', 'tributecity-gig-list' ); ?>
				</button>
				<span class="tributecity-gig-list__pager-status" data-tcgl-page-status></span>
				<button type="button" class="tributecity-gig-list__pager-btn" data-tcgl-page-next>
					<?php esc_html_e( 'Next', 'tributecity-gig-list' ); ?>
				</button>

				<label class="tributecity-gig-list__per-page" for="tributecity-archive-per-page">
					<span class="tributecity-gig-list__per-page-label"><?php esc_html_e( 'Rows', 'tributecity-gig-list' ); ?></span>
					<select id="tributecity-archive-per-page" class="tributecity-gig-list__per-page-select" data-tcgl-per-page>
						<option value="10"<?php selected( (int) $archive_per_page, 10 ); ?>><?php echo esc_html( '10' ); ?></option>
						<option value="25"<?php selected( (int) $archive_per_page, 25 ); ?>><?php echo esc_html( '25' ); ?></option>
						<option value="50"<?php selected( (int) $archive_per_page, 50 ); ?>><?php echo esc_html( '50' ); ?></option>
						<option value="all"><?php esc_html_e( 'All', 'tributecity-gig-list' ); ?></option>
					</select>
				</label>
			</nav>
		</div>
	<?php elseif ( 'cards' === $list_layout ) : ?>
		<div class="tributecity-gig-list__cards" role="list">
			<?php foreach ( $data as $gig ) : ?>
				<?php
				if ( ! is_object( $gig ) ) {
					continue;
				}
				$row = $normalize_gig( $gig );
				echo '<div class="tributecity-gig-list__cards-item" role="listitem">';
				require TRIBUTECITY_GIG_LIST_PATH . 'templates/partials/card-item.php';
				echo '</div>';
				?>
			<?php endforeach; ?>
		</div>
	<?php elseif ( 'list' === $list_layout ) : ?>
		<ul class="tributecity-gig-list__stack">
			<?php foreach ( $data as $gig ) : ?>
				<?php
				if ( ! is_object( $gig ) ) {
					continue;
				}
				$row = $normalize_gig( $gig );
				?>
				<li class="tributecity-gig-list__stack-item">
					<div class="tributecity-gig-list__stack-date">
						<?php echo esc_html( $row['date'] ); ?>
					</div>
					<div class="tributecity-gig-list__stack-main">
						<div class="tributecity-gig-list__stack-title">
							<?php if ( $row['detail_url'] ) : ?>
								<a href="<?php echo esc_url( $row['detail_url'] ); ?>"><?php echo esc_html( $row['name'] ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $row['name'] ); ?>
							<?php endif; ?>
						</div>
						<div class="tributecity-gig-list__stack-meta">
							<?php
							$meta_bits = array_filter(
								array(
									$row['venue'],
									$row['location'],
								)
							);
							echo esc_html( implode( ' · ', $meta_bits ) );
							?>
						</div>
					</div>
					<?php if ( $row['detail_url'] ) : ?>
						<div class="tributecity-gig-list__stack-action">
							<a href="<?php echo esc_url( $row['detail_url'] ); ?>">
								<?php esc_html_e( 'View', 'tributecity-gig-list' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<div class="tributecity-gig-list__table-wrap">
			<table class="tributecity-gig-list__table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Show', 'tributecity-gig-list' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Date', 'tributecity-gig-list' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Location', 'tributecity-gig-list' ); ?></th>
						<?php if ( $show_details ) : ?>
							<th scope="col"><?php esc_html_e( 'Details', 'tributecity-gig-list' ); ?></th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $data as $gig ) : ?>
						<?php
						if ( ! is_object( $gig ) ) {
							continue;
						}
						$row = $normalize_gig( $gig );
						?>
						<tr class="tributecity-gig-list__row">
							<td data-label="<?php esc_attr_e( 'Show', 'tributecity-gig-list' ); ?>">
								<span class="tributecity-gig-list__show-name"><?php echo esc_html( $row['name'] ); ?></span>
							</td>
							<td data-label="<?php esc_attr_e( 'Date', 'tributecity-gig-list' ); ?>">
								<?php echo esc_html( $row['date'] ); ?>
							</td>
							<td data-label="<?php esc_attr_e( 'Location', 'tributecity-gig-list' ); ?>">
								<?php echo esc_html( $row['location'] ); ?>
							</td>
							<?php if ( $show_details ) : ?>
								<td data-label="<?php esc_attr_e( 'Details', 'tributecity-gig-list' ); ?>">
									<?php if ( $row['detail_url'] ) : ?>
										<a href="<?php echo esc_url( $row['detail_url'] ); ?>">
											<?php esc_html_e( 'View', 'tributecity-gig-list' ); ?>
										</a>
									<?php endif; ?>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<?php if ( $has_listings && $show_toggle ) : ?>
		<div class="tributecity-gig-list__archive-toggle">
			<?php if ( $is_archive ) : ?>
				<a class="tributecity-gig-list__toggle-link" href="<?php echo esc_url( $current_url ); ?>">
					<?php esc_html_e( 'Display current shows', 'tributecity-gig-list' ); ?>
				</a>
			<?php else : ?>
				<a class="tributecity-gig-list__toggle-link" href="<?php echo esc_url( add_query_arg( 'archive', '1', $current_url ) ); ?>">
					<?php esc_html_e( 'Display archived shows', 'tributecity-gig-list' ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $show_credit ) : ?>
		<p class="tributecity-gig-list__credit">
			<?php
			echo wp_kses(
				sprintf(
					/* translators: %s: TributeCity website link */
					__( 'Gig list powered by %s', 'tributecity-gig-list' ),
					'<a href="' . esc_url( 'https://tributecity.com' ) . '" rel="noopener noreferrer">' . esc_html__( 'TributeCity.com', 'tributecity-gig-list' ) . '</a>'
				),
				array(
					'a' => array(
						'href' => true,
						'rel'  => true,
					),
				)
			);
			?>
		</p>
	<?php endif; ?>

	<?php
	// JSON-LD for search engines (ItemList of MusicEvent). Server-rendered, no JS required.
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Seo::list_json_ld() returns encoded JSON-LD script.
	echo \TributeCity\GigList\Base\Seo::list_json_ld( $has_listings ? $data : array(), $current_url, $is_archive );
	?>
</section>
