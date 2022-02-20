<?php

/**
 * Fired during plugin activation. Creates a table for storing affiliates IDs
*/

class RCP_Remove_Expired_Users_Activator {

	public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $option_query = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = 'rcp_remove_cids_and_offset'");
        if($option_query == null) {
            // Create the cids and ofsset object option
            $cids_and_offset = json_encode(array(
                'cids' => array(
                ),
                'offset' => 0
            ));
            $table = $wpdb->prefix . 'options';
            $wpdb->insert( 
                $table, 
                array( 
                    'option_name' => 'rcp_remove_cids_and_offset',
                    'option_value' => $cids_and_offset
                )
            );
        }
    }
}