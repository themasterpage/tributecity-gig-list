<?php

namespace Inc\Base;

use Inc\Base\BaseController;

/**
 * @package TributeCityGigList
 *
 */
class Enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
    }

    function enqueue()
    {
        wp_enqueue_style('mypluginstyle', $this->plugin_url . 'assets/tributecity.css');
        wp_enqueue_script('myplugscript', $this->plugin_url . 'assets/tributecity.js');
    }
}
