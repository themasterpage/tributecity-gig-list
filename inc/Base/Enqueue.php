<?php
/**
 * Script and style registration.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Conditionally enqueues admin and public assets.
 */
class Enqueue extends BaseController {

	/**
	 * Hook asset loaders.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
		// Late priority so public CSS wins over theme styles (e.g. .lz-prose max-width).
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public' ), 100 );
	}

	/**
	 * Load admin assets only on this plugin's settings screen.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin( string $hook_suffix ): void {
		if ( 'toplevel_page_tributecity-gig-list' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'tributecity-gig-list-admin',
			$this->plugin_url . 'assets/css/admin.css',
			array(),
			TRIBUTECITY_GIG_LIST_VERSION
		);

		wp_enqueue_script(
			'tributecity-gig-list-admin',
			$this->plugin_url . 'assets/js/admin.js',
			array(),
			TRIBUTECITY_GIG_LIST_VERSION,
			true
		);
	}

	/**
	 * Register/enqueue public styles based on styling settings.
	 *
	 * @return void
	 */
	public function enqueue_public(): void {
		// Depend on known theme handles when present so we print after theme CSS.
		$deps = array();
		if ( wp_style_is( 'lz-main', 'registered' ) || wp_style_is( 'lz-main', 'enqueued' ) ) {
			$deps[] = 'lz-main';
		}

		// Structural CSS always available for shortcode renders.
		wp_register_style(
			'tributecity-gig-list-public',
			$this->plugin_url . 'assets/css/public-base.css',
			$deps,
			TRIBUTECITY_GIG_LIST_VERSION
		);

		$theme      = StyleManager::get_active_theme();
		$theme_path = 'assets/css/themes/' . $theme . '.css';
		$theme_file = $this->plugin_path . $theme_path;

		if ( file_exists( $theme_file ) ) {
			wp_register_style(
				'tributecity-gig-list-theme',
				$this->plugin_url . $theme_path,
				array( 'tributecity-gig-list-public' ),
				TRIBUTECITY_GIG_LIST_VERSION
			);
		}

		if ( $this->should_load_public_assets() ) {
			$this->enqueue_resolved_public_styles();
		}
	}

	/**
	 * Enqueue structural CSS and optional theme override.
	 *
	 * @return void
	 */
	public static function enqueue_resolved_public_styles(): void {
		wp_enqueue_style( 'tributecity-gig-list-public' );

		// Critical rules as inline CSS so they always win the cascade.
		$critical = '
			.tributecity-gig-list{
				font-size:calc(1rem * var(--tcgl-font-scale, 1))!important;
			}
			.tributecity-gig-list--font-small{--tcgl-font-scale:0.875}
			.tributecity-gig-list--font-medium{--tcgl-font-scale:1}
			.tributecity-gig-list--font-large{--tcgl-font-scale:1.125}
			.tributecity-gig-list--font-x-large{--tcgl-font-scale:1.25}
			.tributecity-gig-list--archive-interactive .tributecity-gig-list__archive-search,
			.tributecity-gig-list--archive-interactive .tributecity-gig-list__pager-btn,
			.tributecity-gig-list--archive-interactive .tributecity-gig-list__per-page-select{
				font-size:1em!important;
				font-family:inherit!important;
			}
			@media(max-width:639px){
				.tributecity-gig-list{
					padding-left:1rem!important;
					padding-right:1rem!important;
					box-sizing:border-box!important;
				}
				.tributecity-gig-list__heading,
				.tributecity-gig-list__subtitle,
				.tributecity-gig-list__subtitle-meta{
					padding-left:0.15rem!important;
					padding-right:0.15rem!important;
				}
				.tributecity-gig-list--layout-cards .tributecity-gig-list__card-body{
					padding:1rem 1.15rem 1.2rem!important;
				}
			}
		';
		wp_add_inline_style( 'tributecity-gig-list-public', $critical );

		if ( StyleManager::use_theme_styles() ) {
			return;
		}

		if ( wp_style_is( 'tributecity-gig-list-theme', 'registered' ) ) {
			wp_enqueue_style( 'tributecity-gig-list-theme' );
		}
	}

	/**
	 * Enqueue archive table interactivity (search + pagination).
	 *
	 * @return void
	 */
	public static function enqueue_archive_script(): void {
		$instance = new self();

		wp_enqueue_script(
			'tributecity-gig-list-archive',
			$instance->plugin_url . 'assets/js/archive.js',
			array(),
			TRIBUTECITY_GIG_LIST_VERSION,
			true
		);

		wp_localize_script(
			'tributecity-gig-list-archive',
			'tributecityGigListArchive',
			array(
				'i18n' => array(
					/* translators: %1$d: visible count, %2$d: total count */
					'results'     => __( 'Showing %1$d of %2$d shows', 'tributecity-gig-list' ),
					/* translators: %d: total matching shows */
					'resultsAll'  => __( 'Showing all %d shows', 'tributecity-gig-list' ),
					'noResults'   => __( 'No shows match your search.', 'tributecity-gig-list' ),
					/* translators: %1$d: current page, %2$d: total pages */
					'pageStatus'  => __( 'Page %1$d of %2$d', 'tributecity-gig-list' ),
					'prev'        => __( 'Previous', 'tributecity-gig-list' ),
					'next'        => __( 'Next', 'tributecity-gig-list' ),
					'searchLabel' => __( 'Search shows', 'tributecity-gig-list' ),
					'searchPh'    => __( 'Search by show, date, or location…', 'tributecity-gig-list' ),
					'rowsLabel'   => __( 'Rows', 'tributecity-gig-list' ),
				),
			)
		);
	}

	/**
	 * Detect whether public styles are needed on this request.
	 *
	 * @return bool
	 */
	private function should_load_public_assets(): bool {
		if ( is_singular() ) {
			$post = get_post();
			if ( $post instanceof \WP_Post && has_shortcode( $post->post_content, 'tributecity_gigs' ) ) {
				return true;
			}
			if ( $post instanceof \WP_Post && has_shortcode( $post->post_content, 'tributecity-gigs' ) ) {
				return true;
			}
		}

		/**
		 * Filter whether public CSS should load on the current request.
		 *
		 * @param bool $load Whether to load public assets.
		 */
		return (bool) apply_filters( 'tributecity_gig_list_load_public_assets', false );
	}
}
