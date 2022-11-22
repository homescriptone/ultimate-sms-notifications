<?php
/**
 *
 * Plugin Name:       Ultimate SMS Notifications for WooCommerce
 * Plugin URI:        https://ultimatesmsnotifications.com?utm_source=customer_websites&utm_medium=plugin_page
 * Description:       Send orders notifications and more on mobile using WooCommerce.
 * Version:           1.9.8.2
 * Author:            HomeScript
 * Author URI:        https://ultimatesmsnotifications.com?utm_source=customer_websites&utm_medium=plugin_page
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ultimate-sms-notifications
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 5.0
 *
  */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) && ! defined( 'ABSPATH' ) ) {
	die;
}
require plugin_dir_path( __FILE__ ) . '/abstract/constants.php';
require plugin_dir_path( __FILE__ ) . '/require.php';


register_activation_hook( __FILE__, 'activate_woo_usn' );
/**
 * Code fired after activate the plugin.
 *
 * @return void
 */
function activate_woo_usn() {
	$options = get_option( 'woo_usn_options' );
	if ( ! isset( $options ) ) {
		update_option( 'woo_usn_options', array() );
	}
}

/**
 * This function the core of the plugin.
 */
function run_woo_usn() {
	$plugin = new Woo_Usn();
	$plugin->run();
}

run_woo_usn();
