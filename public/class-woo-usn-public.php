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
    
    /**
     * Get customer consent.
     */
    public function get_customer_consent()
    {
        $options = get_option( 'woo_usn_options' );
        
        if ( isset( $options['woo_usn_sms_consent'] ) ) {
            $content = __( 'I would receive any kind of SMS on my phone number.', 'ultimmate-sms-notifications' );
            if ( !empty($options['woo_usn_sms_consent_text_to_display']) ) {
                $content = $options['woo_usn_sms_consent_text_to_display'];
            }
            ?>
				<p class="form-row validate-required">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="woo_usn_sms_consent" <?php 
            checked( apply_filters( 'woo_usn_must_send_sms', isset( $_POST['woo_usn_sms_consent'] ) ), true );
            ?> id="woo_usn_consent_sms" />
						<span class="woocommerce-terms-and-conditions-checkbox-text"><?php 
            Woo_Usn_UI_Fields::format_html_fields( $content );
            ?></span>&nbsp;
					</label>
				</p>
			<?php 
        }
    
    }
    
    /**
     * Store customer consent.
     */
    public function store_customer_consent( WC_Order $customer )
    {
        $sent_consent = filter_input( INPUT_POST, 'woo_usn_sms_consent' );
        $consent = 'off';
        $customer_id = $customer->get_customer_id();
        
        if ( 'on' === $sent_consent ) {
            update_user_meta( $customer_id, 'woo_usn_allow_sms_sending', $sent_consent );
            $consent = $sent_consent;
        }
        
        global  $wpdb ;
        $table_name = $wpdb->prefix . '_woousn_subscribers_list';
        $timezone_format = _x( 'Y-m-d  H:i:s', 'timezone date format' );
        //phpcs:disable
        $wpdb->insert( $table_name, array(
            'customer_id'              => $customer_id,
            'customer_consent'         => $consent,
            'customer_registered_page' => 'checkout',
            'date'                     => date_i18n( $timezone_format, false, true ),
        ) );
        //phpcs:enable
    }

}