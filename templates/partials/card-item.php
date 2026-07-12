<?php
/**
 * Single card item — responsive poster + details.
 *
 * Desktop: 50/50 columns. Mobile: stacked with padded body text.
 * Layout is CSS-driven (not rigid inline flex) for full responsiveness.
 *
 * @package TributeCityGigList
 *
 * @var array<string, mixed> $row Normalized gig row.
 */

defined( 'ABSPATH' ) || exit;

$has_poster = ! empty( $row['poster_url'] );
$item_id    = ! empty( $row['gig_id'] ) ? 'tcgl-gig-' . sanitize_html_class( (string) $row['gig_id'] ) : '';
$card_class = 'tributecity-gig-list__card' . ( $has_poster ? '' : ' tributecity-gig-list__card--no-poster' );
?>
<article
	class="<?php echo esc_attr( $card_class ); ?>"
	data-tcgl-card="50-50"
	<?php echo $item_id ? ' id="' . esc_attr( $item_id ) . '"' : ''; ?>
	itemscope
	itemtype="https://schema.org/MusicEvent"
>
	<?php if ( $has_poster ) : ?>
		<div class="tributecity-gig-list__card-media">
			<?php if ( ! empty( $row['detail_url'] ) ) : ?>
				<a href="<?php echo esc_url( $row['detail_url'] ); ?>" itemprop="url">
					<img
						src="<?php echo esc_url( $row['poster_url'] ); ?>"
						alt="<?php echo esc_attr( sprintf( /* translators: %s: show name */ __( 'Poster for %s', 'tributecity-gig-list' ), $row['name'] ) ); ?>"
						loading="lazy"
						decoding="async"
						class="tributecity-gig-list__card-poster"
						width="600"
						height="338"
						itemprop="image"
					/>
				</a>
			<?php else : ?>
				<img
					src="<?php echo esc_url( $row['poster_url'] ); ?>"
					alt="<?php echo esc_attr( sprintf( /* translators: %s: show name */ __( 'Poster for %s', 'tributecity-gig-list' ), $row['name'] ) ); ?>"
					loading="lazy"
					decoding="async"
					class="tributecity-gig-list__card-poster"
					width="600"
					height="338"
					itemprop="image"
				/>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="tributecity-gig-list__card-body">
		<?php if ( ! empty( $row['date'] ) ) : ?>
			<p class="tributecity-gig-list__card-date">
				<time datetime="<?php echo esc_attr( ! empty( $row['start_iso'] ) ? $row['start_iso'] : '' ); ?>" itemprop="startDate">
					<?php echo esc_html( $row['date'] ); ?>
				</time>
			</p>
		<?php endif; ?>

		<h3 class="tributecity-gig-list__card-title" itemprop="name">
			<?php if ( ! empty( $row['detail_url'] ) ) : ?>
				<a href="<?php echo esc_url( $row['detail_url'] ); ?>"><?php echo esc_html( $row['name'] ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $row['name'] ); ?>
			<?php endif; ?>
		</h3>

		<?php if ( ! empty( $row['venue'] ) || ! empty( $row['location'] ) ) : ?>
			<div class="tributecity-gig-list__card-place" itemprop="location" itemscope itemtype="https://schema.org/Place">
				<?php if ( ! empty( $row['venue'] ) ) : ?>
					<p class="tributecity-gig-list__card-venue" itemprop="name"><?php echo esc_html( $row['venue'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $row['location'] ) ) : ?>
					<p class="tributecity-gig-list__card-location" itemprop="address"><?php echo esc_html( $row['location'] ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $row['detail_url'] ) ) : ?>
			<p class="tributecity-gig-list__card-action">
				<a class="tributecity-gig-list__card-link" href="<?php echo esc_url( $row['detail_url'] ); ?>">
					<?php esc_html_e( 'View details', 'tributecity-gig-list' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</article>
