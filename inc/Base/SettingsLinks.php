<?php
/**
 * Plugin action links on the Plugins screen.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Adds a Settings link next to Deactivate on the Plugins list table.
 */
class SettingsLinks extends BaseController {

	/**
	 * Hook the plugin action links filter.
	 *
	 * @return void
	 */
	public function register(): void {
		add_filter( 'plugin_action_links_' . $this->plugin, array( $this, 'settings_link' ) );
	}

	/**
	 * Append Settings link.
	 *
	 * @param array<int, string> $links Existing action links.
	 * @return array<int, string>
	 */
	public function settings_link( array $links ): array {
		$url = admin_url( 'admin.php?page=tributecity-gig-list' );

		$links[] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $url ),
			esc_html__( 'Settings', 'tributecity-gig-list' )
		);

		return $links;
	}
}
