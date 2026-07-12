<?php
/**
 * Front-end single gig detail template.
 *
 * Expected variables from ShortcodeController::render_gig_detail():
 *
 * @package TributeCityGigList
 *
 * @var object $gig           Gig data object.
 * @var string $date_time     Formatted date/time string.
 * @var string $display_price Formatted price string.
 * @var bool   $hide_title    Whether to hide band name/tagline.
 * @var string $media_base    Media base URL for posters.
 * @var string $list_url      URL back to the list view.
 * @var bool   $show_credit      Whether to show optional credit link.
 * @var string $wrapper_classes  CSS classes for root element.
 * @var string $wrapper_style    Inline CSS variables for root element.
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $wrapper_classes ) ) {
	$wrapper_classes = 'tributecity-gig-list tributecity-gig-list--detail';
} else {
	$wrapper_classes .= ' tributecity-gig-list--detail';
}

if ( empty( $wrapper_style ) ) {
	$wrapper_style = '';
}

$allowed_desc = array(
	'a'      => array(
		'href'   => true,
		'title'  => true,
		'target' => true,
		'rel'    => true,
	),
	'br'     => array(),
	'em'     => array(),
	'strong' => array(),
	'p'      => array(),
	'ul'     => array(),
	'ol'     => array(),
	'li'     => array(),
	'span'   => array( 'class' => true ),
);

$poster_url = ( ! empty( $gig->poster ) ) ? $media_base . ltrim( (string) $gig->poster, '/' ) : '';
$event_url  = ! empty( $gig->event_url ) ? (string) $gig->event_url : '';
$fb_url     = ! empty( $gig->fb_event_url ) ? (string) $gig->fb_event_url : '';
$ticket_url = ! empty( $gig->ticket_url ) ? (string) $gig->ticket_url : '';
$venue_url  = ! empty( $gig->url ) ? (string) $gig->url : '';
// API sometimes returns hostnames without a scheme.
if ( $venue_url && ! preg_match( '#^https?://#i', $venue_url ) ) {
	$venue_url = 'https://' . ltrim( $venue_url, '/' );
}
$description = isset( $gig->description ) ? (string) $gig->description : '';

$gig_name = isset( $gig->gig_name ) ? (string) $gig->gig_name : '';
$subtitle_parts = array_filter(
	array(
		$gig_name,
		$date_time,
		isset( $gig->venue_name ) ? (string) $gig->venue_name : '',
		isset( $gig->location ) ? (string) $gig->location : '',
	)
);
?>
<article
	class="<?php echo esc_attr( $wrapper_classes ); ?>"
	data-tcgl-v="2.5.0"
	itemscope
	itemtype="https://schema.org/MusicEvent"
	<?php echo $wrapper_style ? ' style="' . esc_attr( $wrapper_style ) . '"' : ''; ?>
>
	<?php if ( ! $hide_title ) : ?>
		<?php if ( ! empty( $gig->band_name ) ) : ?>
			<p class="tributecity-gig-list__band-name" itemprop="performer" itemscope itemtype="https://schema.org/MusicGroup">
				<span itemprop="name"><?php echo esc_html( (string) $gig->band_name ); ?></span>
			</p>
		<?php endif; ?>
		<?php if ( ! empty( $gig->tag_line ) ) : ?>
			<p class="tributecity-gig-list__tagline">
				<?php echo esc_html( (string) $gig->tag_line ); ?>
			</p>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $gig_name ) : ?>
		<h2 class="tributecity-gig-list__subtitle" itemprop="name">
			<?php echo esc_html( $gig_name ); ?>
		</h2>
		<?php if ( count( $subtitle_parts ) > 1 ) : ?>
			<p class="tributecity-gig-list__subtitle-meta">
				<?php
				// Full summary line without duplicating the H2 title when possible.
				$meta_line = array_values(
					array_filter(
						array(
							$date_time,
							isset( $gig->venue_name ) ? (string) $gig->venue_name : '',
							isset( $gig->location ) ? (string) $gig->location : '',
						)
					)
				);
				echo esc_html( implode( ', ', $meta_line ) );
				?>
			</p>
		<?php endif; ?>
	<?php elseif ( ! empty( $subtitle_parts ) ) : ?>
		<p class="tributecity-gig-list__subtitle">
			<?php echo esc_html( implode( ', ', $subtitle_parts ) ); ?>
		</p>
	<?php endif; ?>

	<div class="tributecity-gig-list__detail-grid">
		<div class="tributecity-gig-list__detail-media">
			<?php if ( $poster_url ) : ?>
				<img
					class="tributecity-gig-list__poster"
					src="<?php echo esc_url( $poster_url ); ?>"
					alt="<?php echo esc_attr( $gig_name ? sprintf( /* translators: %s: show name */ __( 'Poster for %s', 'tributecity-gig-list' ), $gig_name ) : __( 'Show poster', 'tributecity-gig-list' ) ); ?>"
					loading="lazy"
					decoding="async"
					width="600"
					height="338"
					itemprop="image"
				/>
			<?php endif; ?>

			<ul class="tributecity-gig-list__external-links">
				<?php if ( $event_url ) : ?>
					<li>
						<a href="<?php echo esc_url( $event_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Event Page', 'tributecity-gig-list' ); ?>
							<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'tributecity-gig-list' ); ?></span>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $fb_url ) : ?>
					<li>
						<a href="<?php echo esc_url( $fb_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Facebook Event Page', 'tributecity-gig-list' ); ?>
							<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'tributecity-gig-list' ); ?></span>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $venue_url ) : ?>
					<li>
						<a href="<?php echo esc_url( $venue_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Venue Website', 'tributecity-gig-list' ); ?>
							<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'tributecity-gig-list' ); ?></span>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( $ticket_url ) : ?>
					<li>
						<a href="<?php echo esc_url( $ticket_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Tickets', 'tributecity-gig-list' ); ?>
							<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'tributecity-gig-list' ); ?></span>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="tributecity-gig-list__detail-info">
			<h3 class="tributecity-gig-list__section-title"><?php esc_html_e( 'Details', 'tributecity-gig-list' ); ?></h3>
			<dl class="tributecity-gig-list__meta">
				<div>
					<dt><?php esc_html_e( 'Date/Time', 'tributecity-gig-list' ); ?></dt>
					<dd>
						<?php
						$start_iso = '';
						if ( ! empty( $gig->start_date ) ) {
							$iso_raw   = (string) $gig->start_date . ( ! empty( $gig->start_time ) ? ' ' . (string) $gig->start_time : '' );
							$iso_ts    = strtotime( $iso_raw );
							$start_iso = $iso_ts ? wp_date( 'c', $iso_ts ) : '';
						}
						?>
						<?php if ( $start_iso ) : ?>
							<time datetime="<?php echo esc_attr( $start_iso ); ?>" itemprop="startDate"><?php echo esc_html( $date_time ); ?></time>
						<?php else : ?>
							<?php echo esc_html( $date_time ); ?>
						<?php endif; ?>
					</dd>
				</div>
				<div>
					<dt><?php esc_html_e( 'Price', 'tributecity-gig-list' ); ?></dt>
					<dd><?php echo esc_html( $display_price ); ?></dd>
				</div>
				<?php if ( ! empty( $gig->venue_name ) || ! empty( $gig->location ) ) : ?>
					<div itemprop="location" itemscope itemtype="https://schema.org/Place">
						<?php if ( ! empty( $gig->venue_name ) ) : ?>
							<dt><?php esc_html_e( 'Venue', 'tributecity-gig-list' ); ?></dt>
							<dd itemprop="name"><?php echo esc_html( (string) $gig->venue_name ); ?></dd>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $gig->address_1 ) || ! empty( $gig->address_2 ) ) : ?>
					<div>
						<dt><?php esc_html_e( 'Address', 'tributecity-gig-list' ); ?></dt>
						<dd itemprop="address">
							<?php
							echo esc_html( isset( $gig->address_1 ) ? (string) $gig->address_1 : '' );
							if ( ! empty( $gig->address_2 ) ) {
								echo ' ' . esc_html( (string) $gig->address_2 );
							}
							?>
						</dd>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $gig->location ) ) : ?>
					<div>
						<dt><?php esc_html_e( 'Location', 'tributecity-gig-list' ); ?></dt>
						<dd><?php echo esc_html( (string) $gig->location ); ?></dd>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $gig->country ) ) : ?>
					<div>
						<dt><?php esc_html_e( 'Country', 'tributecity-gig-list' ); ?></dt>
						<dd><?php echo esc_html( (string) $gig->country ); ?></dd>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $gig->phone ) ) : ?>
					<div>
						<dt><?php esc_html_e( 'Phone', 'tributecity-gig-list' ); ?></dt>
						<dd><?php echo esc_html( (string) $gig->phone ); ?></dd>
					</div>
				<?php endif; ?>
			</dl>

			<?php if ( '' !== trim( wp_strip_all_tags( $description ) ) ) : ?>
				<h3 class="tributecity-gig-list__section-title"><?php esc_html_e( 'Description', 'tributecity-gig-list' ); ?></h3>
				<div class="tributecity-gig-list__description" itemprop="description">
					<?php echo wp_kses( $description, $allowed_desc ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<p class="tributecity-gig-list__back">
		<a href="<?php echo esc_url( $list_url ); ?>" rel="nofollow">
			<?php esc_html_e( 'Return to all show listings', 'tributecity-gig-list' ); ?>
		</a>
	</p>

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
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Seo::detail_json_ld() returns encoded JSON-LD.
	echo \TributeCity\GigList\Base\Seo::detail_json_ld( $gig, $list_url );
	?>
</article>
