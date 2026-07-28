<?php
/**
 * About / usage tab content.
 *
 * @package TributeCityGigList
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render About tab in local scope (Plugin Check PrefixAllGlobals).
 *
 * @return void
 */
( static function (): void {
	/**
	 * Render a shortcode snippet with a copy-to-clipboard control.
	 *
	 * @param string $shortcode Shortcode text to display and copy.
	 * @return void
	 */
	$render_shortcode_copy = static function ( string $shortcode ): void {
		?>
	<span class="tributecity-shortcode-copy">
		<code class="tributecity-shortcode-copy__code"><?php echo esc_html( $shortcode ); ?></code>
		<button
			type="button"
			class="tributecity-shortcode-copy__btn"
			data-copy="<?php echo esc_attr( $shortcode ); ?>"
			aria-label="<?php echo esc_attr( sprintf( /* translators: %s: shortcode text */ __( 'Copy %s', 'tributecity-gig-list' ), $shortcode ) ); ?>"
			title="<?php esc_attr_e( 'Copy to clipboard', 'tributecity-gig-list' ); ?>"
		>
			<span class="dashicons dashicons-clipboard" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Copy to clipboard', 'tributecity-gig-list' ); ?></span>
		</button>
	</span>
		<?php
	};
	?>
<div class="tributecity-gig-list-about">
	<p>
		<?php
		echo esc_html__(
			'TributeCity Gig List displays show listings from a TributeCity Pro band account. Listings are managed in your TributeCity dashboard; this plugin only displays them on your WordPress site.',
			'tributecity-gig-list'
		);
		?>
	</p>

	<h2><?php esc_html_e( 'Shortcodes', 'tributecity-gig-list' ); ?></h2>
	<p>
		<?php
		echo esc_html__(
			'Add the following shortcode to any page or post:',
			'tributecity-gig-list'
		);
		?>
	</p>
	<ul>
		<li>
			<?php $render_shortcode_copy( '[tributecity_gigs]' ); ?>
			— <?php esc_html_e( 'Current show listings.', 'tributecity-gig-list' ); ?>
		</li>
	</ul>

	<h3><?php esc_html_e( 'Attributes', 'tributecity-gig-list' ); ?></h3>
	<table class="widefat striped" style="max-width: 640px;">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Attribute', 'tributecity-gig-list' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Description', 'tributecity-gig-list' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Example', 'tributecity-gig-list' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><code>limit</code></td>
				<td><?php esc_html_e( 'Maximum number of shows to display. Posters and detail links still appear; the archive toggle is hidden.', 'tributecity-gig-list' ); ?></td>
				<td><?php $render_shortcode_copy( '[tributecity_gigs limit="5"]' ); ?></td>
			</tr>
			<tr>
				<td><code>archive</code></td>
				<td><?php esc_html_e( 'Show archived listings instead of upcoming shows.', 'tributecity-gig-list' ); ?></td>
				<td><?php $render_shortcode_copy( '[tributecity_gigs archive="1"]' ); ?></td>
			</tr>
			<tr>
				<td><code>gig_id</code></td>
				<td><?php esc_html_e( 'Display a single show by ID.', 'tributecity-gig-list' ); ?></td>
				<td><?php $render_shortcode_copy( '[tributecity_gigs gig_id="123"]' ); ?></td>
			</tr>
			<tr>
				<td><code>layout</code></td>
				<td><?php esc_html_e( 'List layout override: table, cards, or list.', 'tributecity-gig-list' ); ?></td>
				<td><?php $render_shortcode_copy( '[tributecity_gigs layout="cards"]' ); ?></td>
			</tr>
		</tbody>
	</table>

	<h2><?php esc_html_e( 'External service', 'tributecity-gig-list' ); ?></h2>
	<p>
		<?php
		echo esc_html__(
			'When a visitor views a page that includes the shortcode, this plugin requests gig data from tributecity.com using the API token and Band ID you configure. No site visitor personal data is sent. Poster images may load from tributecity.com/media/.',
			'tributecity-gig-list'
		);
		?>
	</p>

	<p>
		<a class="button button-secondary" href="https://tributecity.com" target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Visit TributeCity.com', 'tributecity-gig-list' ); ?>
			<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'tributecity-gig-list' ); ?></span>
		</a>
	</p>
</div>
	<?php
} )();
