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
        $remote_url = 'https://tributecity.com/api/gig';
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

        if ($options['gigid']) {
            $args['body']['gig_id'] = $options['gigid'];
        }
        if ($options['archive']) {
            $args['body']['archive'] = $options['archive'];
        }
        if ($options['limit']) {
            $args['body']['limit'] = $options['limit'];
        }

        $request = wp_remote_post($remote_url, $args);

        if (is_wp_error($request)) {
            return false;
        }

        $data = wp_remote_retrieve_body($request);
        return json_decode($data);
    }
}
