<?php

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

/**
 * @package TributeCityGigList
 *
 */
class AdminCallbacks extends BaseController
{
    public function adminToken()
    {
        return require_once("$this->plugin_path/templates/admin_token.php");
    }

    public function adminStyles()
    {
        return require_once("$this->plugin_path/templates/admin_styles.php");
    }

    public function intSanitize($input)
    {
        // only accept integer value 
        return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }

    public function textSanitize($input)
    {
        // only accept string and remove any tags or special characters
        return filter_var($input, FILTER_SANITIZE_STRING);
    }

    public function linkSanitize($input)
    {
        // only accept string and remove any tags or special characters
        $str = filter_var($input, FILTER_SANITIZE_STRING);
        // remove slashes from string
        $str = str_replace('/', '', $str);
        return $str;
    }

    public function tributecityAdminSection()
    {
        echo 'This plugin is only valid for TributeCity Pro Account users and will not function unless you have a valid account.<p style="font-size: .65rem; font-style: italic;">* You must get the following input data (Token, Band ID) from your TributeCity.com dashboard by accessing the API Functionality manager.</p>';
    }

    public function tributecityTokenDisplay()
    {
        $tributecityToken = esc_attr(get_option('tributecity_token'));
        echo '<input type="text" class="regular-text" name="tributecity_token" value="' . $tributecityToken . '" placeholder="Enter your TributeCity generated token">';
    }

    public function tributecityBandDisplay()
    {
        $tributecityBandId = esc_attr(get_option('tributecity_band_id'));
        echo '<input type="text" class="regular-text" name="tributecity_band_id" value="' . $tributecityBandId . '" placeholder="Enter your TributeCity band id">';
    }

    public function tributecityDetailsLinkDisplay()
    {
        $tributecityDetailsLink = esc_attr(get_option('tributecity_details_link'));
        echo '<input type="text" class="regular-text" name="tributecity_details_link" value="' . $tributecityDetailsLink . '" placeholder="Enter your gig details permalink, aka gig-details">';
    }
}
