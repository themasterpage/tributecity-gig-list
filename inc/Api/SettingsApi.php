<?php
/**
 * Thin wrapper around WordPress Settings API menu registration.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Api;

defined( 'ABSPATH' ) || exit;

/**
 * Registers admin menu pages, settings, sections, and fields.
 */
class SettingsApi {

	/**
	 * Top-level admin pages.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	public array $admin_pages = array();

	/**
	 * Submenu pages.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	public array $admin_subpages = array();

	/**
	 * Registered settings.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	public array $settings = array();

	/**
	 * Settings sections.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	public array $sections = array();

	/**
	 * Settings fields.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	public array $fields = array();

	/**
	 * Hook menu and settings registration.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( ! empty( $this->admin_pages ) ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}

		if ( ! empty( $this->settings ) ) {
			add_action( 'admin_init', array( $this, 'register_custom_fields' ) );
		}
	}

	/**
	 * Set top-level pages.
	 *
	 * @param array<int, array<string, mixed>> $pages Page definitions.
	 * @return self
	 */
	public function add_pages( array $pages ): self {
		$this->admin_pages = $pages;
		return $this;
	}

	/**
	 * Mirror the first page as its own submenu entry (WordPress convention).
	 *
	 * @param string|null $title Submenu title override.
	 * @return self
	 */
	public function with_sub_page( ?string $title = null ): self {
		if ( empty( $this->admin_pages ) ) {
			return $this;
		}

		$admin_page = $this->admin_pages[0];

		$this->admin_subpages = array(
			array(
				'parent_slug' => $admin_page['menu_slug'],
				'page_title'  => $admin_page['page_title'],
				'menu_title'  => $title ? $title : $admin_page['menu_title'],
				'capability'  => $admin_page['capability'],
				'menu_slug'   => $admin_page['menu_slug'],
				'callback'    => $admin_page['callback'],
			),
		);

		return $this;
	}

	/**
	 * Merge additional submenu pages.
	 *
	 * @param array<int, array<string, mixed>> $pages Subpage definitions.
	 * @return self
	 */
	public function add_sub_pages( array $pages ): self {
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );
		return $this;
	}

	/**
	 * Register menu pages with WordPress.
	 *
	 * @return void
	 */
	public function add_admin_menu(): void {
		foreach ( $this->admin_pages as $page ) {
			add_menu_page(
				$page['page_title'],
				$page['menu_title'],
				$page['capability'],
				$page['menu_slug'],
				$page['callback'],
				$page['icon_url'],
				$page['position']
			);
		}

		foreach ( $this->admin_subpages as $page ) {
			add_submenu_page(
				$page['parent_slug'],
				$page['page_title'],
				$page['menu_title'],
				$page['capability'],
				$page['menu_slug'],
				$page['callback']
			);
		}
	}

	/**
	 * Set settings definitions.
	 *
	 * @param array<int, array<string, mixed>> $settings Settings.
	 * @return self
	 */
	public function set_settings( array $settings ): self {
		$this->settings = $settings;
		return $this;
	}

	/**
	 * Set section definitions.
	 *
	 * @param array<int, array<string, mixed>> $sections Sections.
	 * @return self
	 */
	public function set_sections( array $sections ): self {
		$this->sections = $sections;
		return $this;
	}

	/**
	 * Set field definitions.
	 *
	 * @param array<int, array<string, mixed>> $fields Fields.
	 * @return self
	 */
	public function set_fields( array $fields ): self {
		$this->fields = $fields;
		return $this;
	}

	/**
	 * Register settings, sections, and fields with the Settings API.
	 *
	 * @return void
	 */
	public function register_custom_fields(): void {
		foreach ( $this->settings as $setting ) {
			$args = array();
			if ( isset( $setting['callback'] ) ) {
				$args['sanitize_callback'] = $setting['callback'];
			}
			if ( isset( $setting['default'] ) ) {
				$args['default'] = $setting['default'];
			}
			register_setting(
				$setting['option_group'],
				$setting['option_name'],
				$args
			);
		}

		foreach ( $this->sections as $section ) {
			add_settings_section(
				$section['id'],
				$section['title'],
				isset( $section['callback'] ) ? $section['callback'] : '',
				$section['page']
			);
		}

		foreach ( $this->fields as $field ) {
			add_settings_field(
				$field['id'],
				$field['title'],
				isset( $field['callback'] ) ? $field['callback'] : '',
				$field['page'],
				$field['section'],
				isset( $field['args'] ) ? $field['args'] : array()
			);
		}
	}
}
