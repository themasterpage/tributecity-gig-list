<?php

namespace Inc\Base;

use Inc\Base\BaseController;
use Inc\Base\TributeCityApi;

/**
 * @package TributeCityGigList
 *
 */
class ApiController extends BaseController
{
    public function register()
    {
        add_action('init', array($this, 'activate'));
    }

    public function activate()
    {
        add_shortcode('tributecity-gigs', array($this, 'processGigsApi'));
    }

    public function processGigsApi($atts = array())
    {
        $args = shortcode_atts(
            array(
                'gig_id' => isset($_GET['gig_id']) ? sanitize_key($_GET['gig_id']) : false,
                'archive' => isset($_GET['archive']) ? true : false,
                'limit' => isset($atts['limit']) && is_int($atts['limit']) ? $atts['limit'] : false
            ),
            $atts,
            'tributecity-gigs'
        );

        $options = array();
        $options['gigid'] = $args['gig_id'];
        $options['archive'] = $args['archive'];
        $options['limit'] = $args['limit'];

        $data = TributeCityApi::getApiData($options);

        if (!empty($gigId)) {
            // Call gig details output
            return $this->createGigDetail($data);
        } else {
            // Call gig list output
            return $this->createGigList($data, $options);
        }
    }

    public function createGigList($data, $options)
    {
        // Get the queried object and sanitize it
        $current_page = sanitize_post($GLOBALS['wp_the_query']->get_queried_object());
        // Get the page slug
        $slug = $current_page->post_name;
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $gig_list_link = get_option('tributecity_details_link');

        ob_start();
        require_once("$this->plugin_path/templates/gig_list.php");
        $output = ob_get_clean();
        return $output;
    }


    public function createGigDetail($data)
    {
        //TODO: This needs to be updated on production
        $imgServer = 'https://tributecity.test/media/';
        if (!$data) {
            return false;
        }
        $gig = $data[0];
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $extended = (empty($gig->end_date)) ? '' : ' - ' . date("{$date_format}", strtotime($gig->end_date));
        $dateTime = date("{$date_format}", strtotime($gig->start_date)) . $extended . ', ' . date("{$time_format}", strtotime($gig->start_time));
        $price = self::_getDisplayPrice($gig->price, $gig->sold_out, $gig->free);

        ob_start();
        require_once("$this->plugin_path/templates/gig_detail.php");
        $output = ob_get_clean();

        return $output;
    }

    private static function _getDisplayPrice($price, $sold_out, $free)
    {
        if ($sold_out) {
            return 'Sold Out';
        }
        if ($free) {
            return 'Free';
        }
        return $price;
    }
}
