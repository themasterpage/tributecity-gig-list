<?php
/**
 * Admin settings page template.
 *
 * @package TributeCityGigList
 *
 * @var TributeCity\GigList\Api\Callbacks\AdminCallbacks $this Callbacks instance (when required from class).
 */

defined( 'ABSPATH' ) || exit;

$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( (string) $_GET['tab'] ) ) : 'settings'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only tab navigation.
if ( ! in_array( $active_tab, array( 'settings', 'styling', 'about' ), true ) ) {
	$active_tab = 'settings';
}

$base_url = admin_url( 'admin.php?page=tributecity-gig-list' );
?>
<div class="wrap tributecity-gig-list-admin">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php settings_errors(); ?>

	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu', 'tributecity-gig-list' ); ?>">
		<a
			href="<?php echo esc_url( add_query_arg( 'tab', 'settings', $base_url ) ); ?>"
			class="nav-tab <?php echo 'settings' === $active_tab ? 'nav-tab-active' : ''; ?>"
		>
			<?php esc_html_e( 'Settings', 'tributecity-gig-list' ); ?>
		</a>
		<a
			href="<?php echo esc_url( add_query_arg( 'tab', 'styling', $base_url ) ); ?>"
			class="nav-tab <?php echo 'styling' === $active_tab ? 'nav-tab-active' : ''; ?>"
		>
			<?php esc_html_e( 'Styling', 'tributecity-gig-list' ); ?>
		</a>
		<a
			href="<?php echo esc_url( add_query_arg( 'tab', 'about', $base_url ) ); ?>"
			class="nav-tab <?php echo 'about' === $active_tab ? 'nav-tab-active' : ''; ?>"
		>
			<?php esc_html_e( 'About & shortcode', 'tributecity-gig-list' ); ?>
		</a>
	</nav>

	<div class="tributecity-gig-list-admin__panel<?php echo 'styling' === $active_tab ? ' tributecity-gig-list-admin__panel--wide' : ''; ?>">
		<?php if ( 'about' === $active_tab ) : ?>
			<?php require TRIBUTECITY_GIG_LIST_PATH . 'templates/about.php'; ?>
		<?php elseif ( 'styling' === $active_tab ) : ?>
			<form method="post" action="options.php" class="tributecity-styling-form">
				<?php
				settings_fields( 'tributecity_gig_list_styling' );
				do_settings_sections( 'tributecity-gig-list-styling' );
				submit_button( __( 'Save styling', 'tributecity-gig-list' ) );
				?>
			</form>
			<p class="description">
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %s: front-end gigs page URL */
						__( 'After saving, preview the listings on your site: %s', 'tributecity-gig-list' ),
						'<a href="' . esc_url( home_url( '/gigs/' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( home_url( '/gigs/' ) ) . '</a>'
					),
					array(
						'a' => array(
							'href'   => true,
							'target' => true,
							'rel'    => true,
						),
					)
				);
				?>
			</p>
		<?php else : ?>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'tributecity_gig_list_settings' );
				do_settings_sections( 'tributecity-gig-list' );
				submit_button( __( 'Save settings', 'tributecity-gig-list' ) );
				?>
			</form>
		<?php endif; ?>
	</div>
</div>
