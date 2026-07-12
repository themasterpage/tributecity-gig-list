<?php
/**
 * Plugin bootstrap: registers service classes.
 *
 * @package TributeCityGigList
 */

namespace TributeCity\GigList;

defined( 'ABSPATH' ) || exit;

/**
 * Final bootstrap class that wires plugin services.
 */
final class Plugin {

	/**
	 * List of service class names to instantiate.
	 *
	 * @return array<int, class-string>
	 */
	public static function get_services(): array {
		return array(
			Pages\Admin::class,
			Base\Enqueue::class,
			Base\SettingsLinks::class,
			Base\ShortcodeController::class,
		);
	}

	/**
	 * Instantiate services and call register() when present.
	 *
	 * @return void
	 */
	public static function register_services(): void {
		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/**
	 * Create a service instance.
	 *
	 * @param class-string $class Fully-qualified class name.
	 * @return object
	 */
	private static function instantiate( string $class ): object {
		return new $class();
	}
}
