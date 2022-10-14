<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://homescriptone.com
 * @since      1.0.0
 *
 * @package    Woo_Usn
 * @subpackage Woo_Usn/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woo_Usn
 * @subpackage Woo_Usn/public
 * @author     HomeScript <homescript1@gmail.com>
 */
class Woo_Usn_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Usn_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Usn_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        global  $usn_utility ;
        if ( !is_admin() || $usn_utility->is_product_page() ) {
            wp_enqueue_style(
                $this->plugin_name . '-phone-validator',
                plugin_dir_url( __FILE__ ) . 'css/jquery-phone-validator.css',
                array(),
                $this->version,
                'all'
            );
        }
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/woo-usn-public.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Usn_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Usn_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        
        if ( class_exists( 'WooCommerce' ) && is_checkout() ) {
            $enqueue_list = array( 'jquery' );
            $localize_object = array();
            wp_enqueue_script(
                $this->plugin_name . '-phone-validator',
                plugin_dir_url( __FILE__ ) . 'js/jquery-phone-validator.js',
                array( 'jquery', 'jquery-ui-tooltip' ),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name . '-phone-validator-utils',
                plugin_dir_url( __FILE__ ) . 'js/jquery-phone-validator-utils.js',
                array( 'jquery', 'jquery-ui-tooltip' ),
                $this->version,
                false
            );
            
            if ( class_exists( 'WC_Geolocation' ) ) {
                $location = WC_Geolocation::geolocate_ip();
                $country = $location['country'];
                if ( '' === $country ) {
                    $country = 'IN';
                }
            }
            
            $enqueue_list[] = $this->plugin_name . '-phone-validator';
            $localize_object['woo_usn_phone_utils_path'] = plugin_dir_url( __FILE__ ) . 'js/jquery-phone-validator-utils.js';
            $localize_object['wrong_phone_number_messages'] = __( 'The phone number provided isn\'t valid, please correct it.', 'ultimate-sms-notifications' );
            $localize_object['user_country_code'] = $country;
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'js/woo-usn-public.js',
                $enqueue_list,
                $this->version,
                false
            );
            wp_localize_script( $this->plugin_name, 'woo_usn_ajax_object', $localize_object );
        }
    
    }
    
    /**
     * This method send SMS based on the order ID.
     *
     * @param object $order_id WooCommerce Order ID.
     *
     * @return void
     */
    public function sms_from_thank_you( $order_id )
    {
        global  $usn_sms_loader ;
        $usn_sms_loader->send_api_messages( $order_id );
        do_action( 'woo_usn_send_sms_after_an_order', $order_id, $usn_sms_loader );
    }

}