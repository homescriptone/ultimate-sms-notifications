<?php

class Woo_Usn_Activator {

	/**
	 * Activate function.
	 */
	public static function activate() {
		global $wpdb;
		global $woo_usn_db_version;

		$installed_ver = get_option( "woo_usn_db_version" );

		if ( $installed_ver != $woo_usn_db_version ) {
			$table_name      = $wpdb->prefix . '_woousn_sms_logs';
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				sms_gateways text NOT NULL,
				date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				phone_number text NOT NULL,	
				message_sent text NOT NULL,	
				sms_status text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			add_option( 'woo_usn_db_version', $woo_usn_db_version );
		}

	}
}
