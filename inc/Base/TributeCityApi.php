<?php

namespace Inc\Base;

/**
 * @package TributeCityGigList
 *
 */
class TributeCityApi
{
    public static function getApiData($options = null)
    {
        $remote_url = 'http://tributecity.test/api/gig';
        $bandId = get_option('tributecity_band_id');
        $idToken = get_option('tributecity_token');
        $args = array(
            'headers'     => array(
                'Authorization' => 'Bearer ' . $idToken,
                'Accept' => 'application/json'
            ),
            'body' => array(
                'band_id' => $bandId
            )
        );
        // If band_id, merge into array
        if (is_int($options)) {
            $args['body']['gig_id'] = $options;
        }
        if (is_string($options) && $options == 'set') {
            $args['body']['archive'] = $options;
        }

        $request = wp_remote_post($remote_url, $args);

        if (is_wp_error($request)) {
            return false;
        }

        $data = wp_remote_retrieve_body($request);
        return json_decode($data);
    }
}
