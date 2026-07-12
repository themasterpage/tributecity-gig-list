<?php
/**
 * Admin settings page configuration.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Pages;

use TributeCity\GigList\Api\Callbacks\AdminCallbacks;
use TributeCity\GigList\Api\SettingsApi;
use TributeCity\GigList\Base\BaseController;
use TributeCity\GigList\Base\StyleManager;

defined( 'ABSPATH' ) || exit;

/**
 * Configures the TributeCity admin menu and Settings API fields.
 */
class Admin extends BaseController {

	/**
	 * Settings API helper.
	 *
	 * @var SettingsApi
	 */
	public SettingsApi $settings;

	/**
	 * Admin field callbacks.
	 *
	 * @var AdminCallbacks
	 */
	public AdminCallbacks $callbacks;

	/**
	 * Top-level pages.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	public array $pages = array();

	/**
	 * Wire settings registration.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->settings  = new SettingsApi();
		$this->callbacks = new AdminCallbacks();

		$this->set_pages();
		$this->set_settings();
		$this->set_sections();
		$this->set_fields();

		$this->settings
			->add_pages( $this->pages )
			->with_sub_page( __( 'Settings', 'tributecity-gig-list' ) )
			->register();
	}

	/**
	 * Define top-level menu page.
	 *
	 * @return void
	 */
	public function set_pages(): void {
		$this->pages = array(
			array(
				'page_title' => __( 'TributeCity Gig List', 'tributecity-gig-list' ),
				'menu_title' => __( 'TributeCity Gigs', 'tributecity-gig-list' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'tributecity-gig-list',
				'callback'   => array( $this->callbacks, 'render_settings_page' ),
				'icon_url'   => 'dashicons-playlist-audio',
				'position'   => 110,
			),
		);
	}

	/**
	 * Register option settings with sanitization callbacks.
	 *
	 * @return void
	 */
	public function set_settings(): void {
		$args = array(
			array(
				'option_group' => 'tributecity_gig_list_settings',
				'option_name'  => 'tributecity_token',
				'callback'     => array( $this->callbacks, 'sanitize_text' ),
				'default'      => '',
			),
			array(
				'option_group' => 'tributecity_gig_list_settings',
				'option_name'  => 'tributecity_band_id',
				'callback'     => array( $this->callbacks, 'sanitize_band_id' ),
				'default'      => '',
			),
			array(
				'option_group' => 'tributecity_gig_list_settings',
				'option_name'  => 'tributecity_hide_title',
				'callback'     => array( $this->callbacks, 'sanitize_checkbox' ),
				'default'      => 0,
			),
			array(
				'option_group' => 'tributecity_gig_list_settings',
				'option_name'  => 'tributecity_show_credit',
				'callback'     => array( $this->callbacks, 'sanitize_checkbox' ),
				'default'      => 0,
			),
			array(
				'option_group' => 'tributecity_gig_list_styling',
				'option_name'  => StyleManager::OPTION_USE_THEME,
				'callback'     => array( $this->callbacks, 'sanitize_checkbox' ),
				'default'      => 0,
			),
			array(
				'option_group' => 'tributecity_gig_list_styling',
				'option_name'  => StyleManager::OPTION_STYLE_THEME,
				'callback'     => array( $this->callbacks, 'sanitize_style_theme' ),
				'default'      => StyleManager::DEFAULT_THEME,
			),
			array(
				'option_group' => 'tributecity_gig_list_styling',
				'option_name'  => StyleManager::OPTION_LAYOUT,
				'callback'     => array( $this->callbacks, 'sanitize_list_layout' ),
				'default'      => StyleManager::DEFAULT_LAYOUT,
			),
			array(
				'option_group' => 'tributecity_gig_list_styling',
				'option_name'  => StyleManager::OPTION_FONT_SIZE,
				'callback'     => array( $this->callbacks, 'sanitize_font_size' ),
				'default'      => StyleManager::DEFAULT_FONT_SIZE,
			),
		);

		$this->settings->set_settings( $args );
	}

	/**
	 * Register settings sections.
	 *
	 * @return void
	 */
	public function set_sections(): void {
		$args = array(
			array(
				'id'       => 'tributecity_gig_list_api',
				'title'    => __( 'API credentials', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'section_api' ),
				'page'     => 'tributecity-gig-list',
			),
			array(
				'id'       => 'tributecity_gig_list_display',
				'title'    => __( 'Display options', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'section_display' ),
				'page'     => 'tributecity-gig-list',
			),
			array(
				'id'       => 'tributecity_gig_list_styling',
				'title'    => __( 'Appearance', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'section_styling' ),
				'page'     => 'tributecity-gig-list-styling',
			),
		);

		$this->settings->set_sections( $args );
	}

	/**
	 * Register settings fields.
	 *
	 * @return void
	 */
	public function set_fields(): void {
		$args = array(
			array(
				'id'       => 'tributecity_token',
				'title'    => __( 'API token', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'field_token' ),
				'page'     => 'tributecity-gig-list',
				'section'  => 'tributecity_gig_list_api',
				'args'     => array(
					'label_for' => 'tributecity_token',
				),
			),
			array(
				'id'       => 'tributecity_band_id',
				'title'    => __( 'Band ID', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'field_band_id' ),
				'page'     => 'tributecity-gig-list',
				'section'  => 'tributecity_gig_list_api',
				'args'     => array(
					'label_for' => 'tributecity_band_id',
				),
			),
			array(
				'id'       => 'tributecity_hide_title',
				'title'    => __( 'Hide band name', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'field_checkbox' ),
				'page'     => 'tributecity-gig-list',
				'section'  => 'tributecity_gig_list_display',
				'args'     => array(
					'label_for' => 'tributecity_hide_title',
					'hint'      => __( 'Hide the band name and tagline on the single-show detail view when your page already shows them.', 'tributecity-gig-list' ),
				),
			),
			array(
				'id'       => 'tributecity_show_credit',
				'title'    => __( 'Show credit link', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'field_checkbox' ),
				'page'     => 'tributecity-gig-list',
				'section'  => 'tributecity_gig_list_display',
				'args'     => array(
					'label_for' => 'tributecity_show_credit',
					'hint'      => __( 'Optional. When enabled, displays a small “Powered by TributeCity.com” link under listings. Off by default.', 'tributecity-gig-list' ),
				),
			),
			array(
				'id'       => StyleManager::OPTION_USE_THEME,
				'title'    => __( 'Use site styles', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'field_use_theme_styles' ),
				'page'     => 'tributecity-gig-list-styling',
				'section'  => 'tributecity_gig_list_styling',
				'args'     => array(
					'label_for' => StyleManager::OPTION_USE_THEME,
				),
			),
			array(
				'id'       => StyleManager::OPTION_STYLE_THEME,
				'title'    => __( 'Style theme', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'field_style_theme' ),
				'page'     => 'tributecity-gig-list-styling',
				'section'  => 'tributecity_gig_list_styling',
				'args'     => array(
					'label_for' => StyleManager::OPTION_STYLE_THEME,
				),
			),
			array(
				'id'       => StyleManager::OPTION_LAYOUT,
				'title'    => __( 'List layout', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'field_list_layout' ),
				'page'     => 'tributecity-gig-list-styling',
				'section'  => 'tributecity_gig_list_styling',
				'args'     => array(
					'label_for' => StyleManager::OPTION_LAYOUT,
				),
			),
			array(
				'id'       => StyleManager::OPTION_FONT_SIZE,
				'title'    => __( 'Font size', 'tributecity-gig-list' ),
				'callback' => array( $this->callbacks, 'field_font_size' ),
				'page'     => 'tributecity-gig-list-styling',
				'section'  => 'tributecity_gig_list_styling',
				'args'     => array(
					'label_for' => StyleManager::OPTION_FONT_SIZE,
				),
			),
		);

		$this->settings->set_fields( $args );
	}
}
