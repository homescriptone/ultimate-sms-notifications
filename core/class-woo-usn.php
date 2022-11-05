<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Usn
 * @subpackage Woo_Usn/includes
 * @author     HomeScript <homescript1@gmail.com>
 * @link       https://homescriptone.com
 */
class Woo_Usn
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Woo_Usn_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected  $loader ;
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected  $plugin_name ;
    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected  $version ;
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        
        if ( defined( 'WOO_USN_VERSION' ) ) {
            $this->version = WOO_USN_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        
        $this->plugin_name = 'woo-usn';
        $this->load_dependencies();
        $this->set_locale();
        
        if ( is_admin() ) {
            $this->define_admin_hooks();
        } else {
            $this->define_public_hooks();
        }
    
    }
    
    /**
     * Load the required dependencies for this plugin.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        require_once WOO_USN_PATH . '../core/class-woo-usn-loader.php';
        require_once WOO_USN_PATH . '../i18n/class-woo-usn-i18n.php';
        require_once WOO_USN_PATH . '../admin/class-woo-usn-admin.php';
        require_once WOO_USN_PATH . '../public/class-woo-usn-public.php';
        $this->loader = new Woo_Usn_Loader();
    }
    
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Woo_Usn_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Woo_Usn_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Woo_Usn_Admin( $this->get_plugin_name(), $this->get_version() );
        /**
         * Admin part.
         */
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'check_requirements' );
        $this->loader->add_filter(
            'admin_footer_text',
            $plugin_admin,
            'footer_credits',
            10,
            1
        );
        $this->loader->add_action(
            'admin_menu',
            'Woo_Usn_Admin_Menu',
            'add_menus',
            10,
            1
        );
        $this->loader->add_action( 'admin_init', 'Woo_Usn_Admin_Settings', 'display_options_on_each_tab' );
        /**
         * AJAX functions.
         */
        $this->loader->add_action( 'wp_ajax_woo_usn_save-api-credentials', $plugin_admin, 'save_api_credentials' );
        $this->loader->add_action( 'wp_ajax_woo_usn_delete-api-credentials', $plugin_admin, 'delete_api_credentials' );
        $this->loader->add_action( 'wp_ajax_woo_usn-review-answers', $plugin_admin, 'review_answers' );
        $this->loader->add_action( 'wp_ajax_woo_usn_send-messages-manually-from-orders', $plugin_admin, 'send_sms_from_orders_by_ajax' );
        $this->loader->add_action( 'wp_ajax_woo_usn-get-api-response-code', $plugin_admin, 'get_api_response_code' );
        $this->loader->add_action( 'wp_ajax_woo_usn-send-sms-to-contacts', $plugin_admin, 'send_sms_to_cl' );
        /**
         * CPT part.
         */
        $this->loader->add_action( 'add_meta_boxes', 'Woo_Usn_Admin_Settings', 'message_from_orders_metabox' );
        $this->loader->add_action( 'init', 'Woo_Usn_CPT', 'init' );
        $this->loader->add_action( 'add_meta_boxes', 'Woo_Usn_CPT', 'add_metabox' );
        $this->loader->add_filter(
            'plugin_action_links',
            $plugin_admin,
            'usn_settings_link',
            11,
            2
        );
        $this->loader->add_action(
            'woocommerce_order_status_changed',
            $plugin_admin,
            'send_sms_on_status_change',
            15,
            3
        );
        $this->loader->add_filter( 'manage_edit-woo_usn-sms-panel_columns', $plugin_admin, 'change_sms_cpt_columns' );
        $this->loader->add_filter( 'manage_edit-woo_usn-stats_columns', $plugin_admin, 'change_sms_stat_cpt_columns__premium_only' );
        $this->loader->add_action( 'save_post_woo_usn-list', 'Woo_Usn_CPT', 'save_customer_list' );
        // Extending the plugin.
        $this->loader->add_filter( 'woo_usn_api_choosed', 'Woo_Usn_Admin_Settings', 'add_external_api' );
        $this->loader->add_action(
            'woo_usn_options_before_saving_sms_api_fields',
            'Woo_Usn_Admin_Settings',
            'add_external_api_fields',
            12
        );
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Woo_Usn_Public( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action(
            'woocommerce_thankyou',
            $plugin_public,
            'sms_from_thank_you',
            99
        );
        $this->loader->add_action( 'woocommerce_checkout_after_terms_and_conditions', $plugin_public, 'get_customer_consent' );
        $this->loader->add_action(
            'woocommerce_checkout_update_customer',
            'Woo_Usn_SMS',
            'send_sms_to_new_customers',
            12,
            2
        );
        $this->loader->add_action(
            'woocommerce_checkout_order_created',
            $plugin_public,
            'store_customer_consent',
            15
        );
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }
    
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
    
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Woo_Usn_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }
    
    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

}