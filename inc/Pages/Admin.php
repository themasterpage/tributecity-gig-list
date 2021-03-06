<?php

namespace Inc\Pages;

use Inc\Base\BaseController;
use Inc\Api\SettingsApi;
use Inc\Api\Callbacks\AdminCallbacks;

/**
 * @package TributeCityGigList
 *
 */
class Admin extends BaseController
{
    public $callbacks;

    public $settings;
    public $pages;
    public $subpages;

    public function register()
    {
        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();
        $this->setPages();
        // $this->setSubpages();
        $this->setSettings();
        $this->setSections();
        $this->setFields();
        // $this->settings->addPages($this->pages)->withSubPage('Token')->addSubPages($this->subpages)->register();
        $this->settings->addPages($this->pages)->withSubPage('Token')->register();
    }

    public function setPages()
    {
        $this->pages = array(
            array(
                'page_title' => 'TributeCity Plugin Settings',
                'menu_title' => 'TributeCity API',
                'capability' => 'administrator',
                'menu_slug' => 'tributecity_plugin',
                'callback' => array($this->callbacks, 'adminToken'),
                'icon_url' => 'dashicons-playlist-audio',
                'position' => 110
            )
        );
    }

    public function setSubpages()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'tributecity_plugin',
                'page_title' => 'Custom Font Styles',
                'menu_title' => 'Styles',
                'capability' => 'administrator',
                'menu_slug' => 'tributecity_style',
                'callback' => array($this->callbacks, 'adminStyles')
            )
        );
    }

    public function setSettings()
    {
        $args = array(
            array(
                'option_group' => 'tributecity_plugin_settings',
                'option_name' => 'tributecity_token',
                'callback' => array($this->callbacks, 'textSanitize')
            ),
            array(
                'option_group' => 'tributecity_plugin_settings',
                'option_name' => 'tributecity_band_id',
                'callback' => array($this->callbacks, 'intSanitize')
            ),
            array(
                'option_group' => 'tributecity_plugin_settings',
                'option_name' => 'tributecity_hide_title',
                'callback' => array($this->callbacks, 'checkboxSanitize')
            )
        );
        $this->settings->setSettings($args);
    }

    public function setSections()
    {
        $args = array(
            array(
                'id' => 'tributecity_admin_index',
                'title' => 'API Settings',
                'callback' => array($this->callbacks, 'tributecityAdminSection'),
                'page' => 'tributecity_plugin'
            )
        );
        $this->settings->setSections($args);
    }

    public function setFields()
    {
        $args = array(
            array(
                'id' => 'tributecity_token',
                'title' => 'Token *',
                'callback' => array($this->callbacks, 'tributecityTokenDisplay'),
                'page' => 'tributecity_plugin',
                'section' => 'tributecity_admin_index',
                'args' => array(
                    'label_for' => 'tributecity_token',
                    'class' => 'example-class'
                )
            ),
            array(
                'id' => 'tributecity_band_id',
                'title' => 'Band ID *',
                'callback' => array($this->callbacks, 'tributecityBandDisplay'),
                'page' => 'tributecity_plugin',
                'section' => 'tributecity_admin_index',
                'args' => array(
                    'label_for' => 'tributecity_band_id',
                    'class' => 'example-class'
                )
            ),
            array(
                'id' => 'tributecity_hide_title',
                'title' => 'Hide Band Name',
                'callback' => array($this->callbacks, 'checkboxField'),
                'page' => 'tributecity_plugin',
                'section' => 'tributecity_admin_index',
                'args' => array(
                    'label_for' => 'tributecity_hide_title',
                    'class' => 'ui-toggle',
                    'hint' => 'Hide your band name title if your page already displays it.'
                )
            ),
        );
        $this->settings->setFields($args);
    }
}
