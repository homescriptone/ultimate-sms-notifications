<?php

class Woo_Usn_Activator
{
    /**
     * Activate function.
     */
    public static function activate()
    {
        global  $wpdb ;
        global  $woo_usn_db_subscribers_version ;
        $subscribers_installed_ver = get_option( 'woo_usn_subscribers_db_version' );
        
        if ( $subscribers_installed_ver != $woo_usn_db_subscribers_version ) {
            $table_name = $wpdb->prefix . '_woousn_subscribers_list';
            $charset_collate = $wpdb->get_charset_collate();
            $rid = rand( 0, 500 );
            $sql = "CREATE TABLE {$table_name} (id mediumint(20) DEFAULT '{$rid}' NOT NULL AUTO_INCREMENT,customer_id mediumint(255) NOT NULL,customer_consent text NOT NULL,customer_registered_page text NOT NULL,customer_order_id VARCHAR(255),date datetime DEFAULT '2022-12-12 00:00:00' NOT NULL,PRIMARY KEY  (id)) {$charset_collate};";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );
            add_option( 'woo_usn_subscribers_db_version', $woo_usn_db_subscribers_version );
        }
    
    }

}