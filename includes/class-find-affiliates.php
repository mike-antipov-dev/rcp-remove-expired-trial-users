<?php

/**
 * Creates affilates list to be excluded from being removed
 */

class Find_Affiliates {

    private $wa_api_args;

    public function __construct() {
        $this->wa_api_args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode(WPAF_LOGIN . ':' . WPAF_PASS)
            ),
            'sslverify' => false,
            'method' => 'GET',
            'timeout' => 3
        );
        add_action( 'fwpa_cron_hook', array($this, 'find_affiliates'));
    }

    /**
     * Fetch affiliates via REST API and write them to the table
     */
    public function find_affiliates() {
        global $wpdb;
        // Retrieve object with customer IDs and previous query offset
        $cids_and_offset = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'rcp_remove_cids_and_offset'" );
        $cids_and_offset = json_decode($cids_and_offset);

        // Fetch customer ids from REST API
        $affiliates = wp_remote_request(get_site_url() . '/wp-json/affwp/v1/affiliates?number=100&offset=' . $cids_and_offset->offset, $this->wa_api_args);
        $affiliates = json_decode(wp_remote_retrieve_body($affiliates));

        // Populate DB table with user CIDs and offset
        foreach ($affiliates as $affiliate) {
            $user = get_user_by('id', $affiliate->user_id);
            $id = $user->id;
            array_push($cids_and_offset->cids, $id);
        }
        $cids_and_offset->offset = $cids_and_offset->offset + 10;

        $table_name = $wpdb->prefix . 'options';
        $res = $wpdb->update( 
            $table_name, 
            array( 
                'option_value' => json_encode($cids_and_offset)
            ), 
            array('option_name' => 'rcp_remove_cids_and_offset')
        );
    }
    
}