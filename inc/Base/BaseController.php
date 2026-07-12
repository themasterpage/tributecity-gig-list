<?php
/**
 * Shared base controller for path/URL helpers.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList\Base;

defined( 'ABSPATH' ) || exit;

/**
 * Provides common plugin path and URL properties.
 */
class BaseController {

	/**
	 * Absolute filesystem path to the plugin root (with trailing slash).
	 *
	 * @var string
	 */
	public string $plugin_path;

	/**
	 * Public URL to the plugin root (with trailing slash).
	 *
	 * @var string
	 */
	public string $plugin_url;

	/**
	 * Plugin basename (folder/main-file.php).
	 *
	 * @var string
	 */
	public string $plugin;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->plugin_path = TRIBUTECITY_GIG_LIST_PATH;
		$this->plugin_url  = TRIBUTECITY_GIG_LIST_URL;
		$this->plugin      = TRIBUTECITY_GIG_LIST_BASENAME;
	}
}
