<?php

namespace Inc;

/**
 * @package TributeCityGigList
 *
 */
final class Init
{
    /**
     * get_services function
     *
     * Store all the classes inside an array
     * @return array Full list of available classes
     */
    public static function get_services()
    {
        return [
            Pages\Admin::class,
            Base\Enqueue::class,
            Base\SettingsLinks::class,
            Base\ApiController::class,
            // Base\WidgetController::class,
        ];
    }

    /**
     * register_services function
     *
     * Loop through the classes and initialize them.  Call the
     * register() method if they exists.
     * @return void
     */
    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * instantiate function
     *
     * Initialize the class
     * @param class $class     class from the services array
     * @return class instance  new instance of the class
     */
    private static function instantiate($class)
    {
        $service = new $class();
        return $service;
    }
}
