<?php

/**
 * Main class that removes all RCP expired users except ones that are affiliates
 */

class Remove_Expired_Users {

    private $api_args;
    public $args;

    public function __construct() {
        $this->api_args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode(RCP_LOGIN . ':' . RCP_PASS)
            ),
            'sslverify' => false,
            'method' => 'DELETE',
            'timeout' => 20
        );
    
        $this->args = array(
            'status' => 'expired',
            'object_id' => 2,
            'number' => 20
        );
        add_action( 'rtu_cron_hook', array($this, 'remove_expired_users'));
    }

    /**
     * Remove all expired trial users excluding the ones from the affiliates list
     */
    public function remove_expired_users() {
        $memberships = rcp_get_memberships($this->args);
        foreach ($memberships as $membership) {
            if (gettype($membership) == 'object' && method_exists($membership,'get_id'))
            {
                // Membership ID
                $mid = $membership->get_id();
                // Customer
                $customer = $membership->get_customer();
    
                // Get affiliate's IDs
                global $wpdb;
                $affiliates = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name = 'rcp_remove_cids_and_offset'" );
                $affiliates = json_decode($affiliates);
    
                $cid = '';
                // Fix 'Call to a member function get_id() on bool'
                if (method_exists($customer,'get_user_id')) {
                    $cid = $membership->get_user_id();
                }
    
                // Check if a user is affiliate
                $is_affiliate = false;
                foreach ($affiliates->cids as $a_cid) {
                    $is_affiliate = $cid == $a_cid ? true : false;
                }
    
                if ($membership->get_status() == 'expired' && $membership->get_object_id() == 2 && !$is_affiliate) {
                    // Remove membership
                    $response = wp_remote_request( 'https://staging.guptaprogram.com/wp-json/rcp/v1/memberships/delete/' . $mid, $this->api_args );
                    Remove user and his meta
                    $result = wp_delete_user($cid);
                }
            }
        }
    }

}