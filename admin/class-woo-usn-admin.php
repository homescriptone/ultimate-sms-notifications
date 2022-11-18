<?php

// phpcs:ignorefile
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Usn
 * @subpackage Woo_Usn/admin
 * @author     HomeScript <homescript1@gmail.com>
 * @link       https://homescriptone.com
 * @since      1.0.0
 */
class Woo_Usn_Admin
{
    private  $plugin_name ;
    private  $version ;
    private  $api_choosed = 'Twilio' ;
    private  $usn_api ;
    public function __construct( $plugin_name, $version )
    {
        global  $usn_sms_loader ;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->usn_api = $usn_sms_loader;
        $options = get_option( 'woo_usn_api_choosed' );
        if ( !$options ) {
            $this->api_choosed = get_option( 'woo_usn_api_choosed' );
        }
    }
    
    public function enqueue_styles()
    {
        global  $usn_utility ;
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/woo-usn-admin.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-datatables-css',
            plugin_dir_url( __FILE__ ) . 'css/jquery-datatables.css',
            array(),
            $this->version,
            'all'
        );
        
        if ( $usn_utility->is_usn_page() ) {
            wp_enqueue_style(
                'woo-usn-jquery-datepicker-css',
                plugin_dir_url( __FILE__ ) . 'css/jquery-datepicker.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-phone-validator',
                plugin_dir_url( __FILE__ ) . 'css/jquery-phone-validator.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-select2-css',
                plugin_dir_url( __FILE__ ) . 'css/jquery-select2.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_editor();
            wp_enqueue_style( 'jquery-ui-style' );
        }
    
    }
    
    public function enqueue_scripts()
    {
        global  $usn_utility ;
        $woo_usn_ajax_variables = array(
            'woo_usn_ajax_url'      => admin_url( 'admin-ajax.php' ),
            'woo_usn_ajax_security' => wp_create_nonce( 'woo-usn-ajax-nonce' ),
            'woo_usn_sms_api_used'  => ( get_option( 'woo_usn_api_choosed' ) == '' ? 'Twilio' : get_option( 'woo_usn_api_choosed' ) ),
        );
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/woo-usn-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        wp_enqueue_script(
            $this->plugin_name . '-datatables-js',
            plugin_dir_url( __FILE__ ) . 'js/jquery-datatables.js',
            array( 'jquery' ),
            $this->version,
            false
        );
        
        if ( $usn_utility->is_usn_page( 'cl' ) ) {
            wp_enqueue_script(
                $this->plugin_name . '-blockUI',
                plugin_dir_url( __FILE__ ) . 'js/jquery-blockui.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name . '-cl',
                plugin_dir_url( __FILE__ ) . 'js/woo-usn-admin-cl.js',
                array(
                'jquery',
                $this->plugin_name . '-blockUI',
                'jquery-ui-core',
                'jquery-ui-datepicker'
            ),
                $this->version,
                false
            );
            $woo_usn_cl_variables = array(
                'woo_usn_cl_rules_names'            => Woo_Usn_UI_Fields::get_cl_rules_names(),
                'woo_usn_cl_operators_names'        => Woo_Usn_UI_Fields::get_cl_operators_names(),
                'woo_usn_get_payment_methods'       => $usn_utility::get_wc_payment_gateways(),
                'woo_usn_get_shipping_methods'      => $usn_utility::get_wc_shipping_methods(),
                'woo_usn_country'                   => $usn_utility::get_wc_country(),
                'woo_usn_customer_roles'            => $usn_utility::get_wp_roles(),
                'woo_usn_customer_order_status'     => wc_get_order_statuses(),
                'woo_usn_input_number_placeholders' => __( 'enter the amount', 'ultimate-sms-notifications' ),
                'woo_usn_text_field'                => __( 'separate domain name by commas', 'ultimate-sms-notifications' ),
                'loader_message'                    => __( 'Loading ...', 'ultimate-sms-notifications' ),
                'woo_usn_cl_table_list'             => __( 'Customer List Details ', 'ultimate-sms-notifications' ),
                'woo_usn_cl_customer_name'          => __( 'Customers Names ', 'ultimate-sms-notifications' ),
                'woo_usn_cl_customer_phonenumber'   => __( 'Customers Phone Numbers ', 'ultimate-sms-notifications' ),
            );
            $woo_usn_ajax_variables = array_merge( $woo_usn_ajax_variables, $woo_usn_cl_variables );
        }
        
        wp_localize_script( $this->plugin_name, 'woo_usn_ajax_object', $woo_usn_ajax_variables );
        // settings page.
        
        if ( $usn_utility->is_usn_page() ) {
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
            wp_enqueue_script(
                $this->plugin_name . '-select2',
                plugin_dir_url( __FILE__ ) . 'js/jquery-select2.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            $woo_usn_ajax_variables['woo_usn_phone_utils_path'] = plugin_dir_url( __FILE__ ) . 'js/jquery-phone-validator-utils.js';
            wp_enqueue_script(
                $this->plugin_name . '-settings',
                plugin_dir_url( __FILE__ ) . 'js/woo-usn-admin-settings.js',
                array(
                'jquery',
                'jquery-ui-tooltip',
                'serializejson',
                'wp-hooks',
                $this->plugin_name . '-select2',
                $this->plugin_name,
                $this->plugin_name . '-phone-validator'
            ),
                $this->version,
                false
            );
            wp_localize_script( $this->plugin_name . '-settings', 'woo_usn_ajax_object', $woo_usn_ajax_variables );
        }
    
    }
    
    /**
     * Give feedback or reviews on the website or WP.org.
     */
    public function review_answers()
    {
        $successfull = 0;
        
        if ( isset( $_POST['type'] ) ) {
            $successfull = 1;
            
            if ( $_POST['type'] === 'already_give' && wp_verify_nonce( $_POST['security'], 'woo-usn-ajax-nonce' ) ) {
                update_option( 'woousn_have_already_give_reviews', true );
            } elseif ( $_POST['type'] === 'dismiss' && wp_verify_nonce( $_POST['security'], 'woo-usn-ajax-nonce' ) ) {
                update_option( 'woousn_dismiss_banner', true );
            }
            
            if ( 1 === $successfull ) {
                update_option( 'woo_usn_display_banner', $successfull );
            }
        }
        
        echo  wp_json_encode( array(
            'status' => $successfull,
        ) ) ;
        wp_die();
    }
    
    public static function usn_settings_link( $links, $file )
    {
        
        if ( preg_match( '/woo-usn\\.php/', $file ) && current_user_can( 'manage_options' ) ) {
            $settings = array(
                'settings' => '<a href="admin.php?page=ultimate-sms-notifications&tab=sms-api">' . __( 'Settings', 'ultimate-sms-notifications' ) . '</a>',
            );
            $links = array_merge( $settings, $links );
        }
        
        return $links;
    }
    
    public function send_sms_on_status_change( $order_id, $old_status, $new_status )
    {
        global  $usn_utility ;
        global  $usn_sms_loader ;
        $_order = new WC_Order( $order_id );
        $country = $_order->get_billing_country();
        $country_indicator = $usn_utility::get_country_town_code( $country );
        $_phone_number = $_order->get_billing_phone();
        $phone_number = $usn_utility::get_right_phone_numbers( $country_indicator, $_phone_number );
        $phone_number = $country_indicator . $phone_number;
        $order_type = array(
            'on-hold',
            'completed',
            'processing',
            'cancelled',
            'pending',
            'failed'
        );
        $options = get_option( 'woo_usn_options' );
        $shop_manager_can_change_on_order_status_change = $options['woo_usn_message_after_order_changed'];
        
        if ( '1' == $shop_manager_can_change_on_order_status_change ) {
            
            if ( in_array( $new_status, $order_type ) ) {
                
                if ( $new_status == 'completed' ) {
                    $message = $options['woo_usn_completed_messages'];
                } elseif ( $new_status == 'on-hold' ) {
                    $message = $options['woo_usn_on_hold_messages'];
                } elseif ( $new_status == 'processing' ) {
                    $message = $options['woo_usn_processing_messages'];
                } elseif ( $new_status == 'cancelled' ) {
                    $message = $options['woo_usn_cancelled_messages'];
                } elseif ( $new_status == 'pending_payment' ) {
                    $message = $options['woo_usn_processing_messages'];
                } elseif ( $new_status == 'failed' ) {
                    $message = $options['woo_usn_failed_messages'];
                }
                
                $message = $usn_utility::decode_message_to_send( $order_id, $message );
            }
            
            try {
                $status_code = $usn_sms_loader->send_sms( $phone_number, $message );
                $status_code = Woo_Usn_Utility::get_sms_status( $status_code, $usn_sms_loader->get_sms_api() );
            } catch ( Exception $errors ) {
                $return = Woo_Usn_Utility::log_errors( $errors );
            }
            $orders_messages = '<br/><strong>' . __( 'Phone Numbers : ', 'ultimate-sms-notifications' ) . '</strong>' . $phone_number . '<br/><strong>' . __( 'Message sent : ', 'ultimate-sms-notifications' ) . '</strong>' . $message . '<br/><strong>' . __( 'Delivery Messages Status : ', 'ultimate-sms-notifications' ) . '</strong>' . $return . '<br/>' . 'Sent from <strong>Ultimate SMS & WhatsApp Notifications for WooCommerce</strong>';
            $_order->add_order_note( $orders_messages );
        }
    
    }
    
    public function send_sms_from_orders_by_ajax()
    {
        
        if ( is_admin() && true == current_user_can( 'install_plugins' ) ) {
            $posted_data = filter_input_array( INPUT_POST );
            $security = $posted_data['security'];
            
            if ( wp_verify_nonce( $security, 'woo-usn-ajax-nonce' ) ) {
                global  $usn_utility ;
                $ajax_data = $posted_data['data'];
                $order_id = sanitize_text_field( $ajax_data['order-id'] );
                $message = sanitize_text_field( $ajax_data['messages-to-send'] );
                $order = wc_get_order( $order_id );
                $country = $order->get_billing_country();
                $country_indicator = $usn_utility::get_country_town_code( $country );
                $_phone_number = $order->get_billing_phone();
                $phone_number = $country_indicator . $usn_utility::get_right_phone_numbers( $country_indicator, $_phone_number );
                
                if ( !empty($phone_number) && !empty($message) ) {
                    $message = $usn_utility::decode_message_to_send( $order_id, $message );
                    try {
                        $status_code = $this->usn_api->send_sms( $phone_number, $message );
                        $return = Woo_Usn_Utility::get_sms_status( $status_code, $this->usn_api->get_sms_api() );
                    } catch ( Exception $errors ) {
                        $return = Woo_Usn_Utility::log_errors( $errors );
                    }
                    $orders_messages = '<br/><strong>' . __( 'Phone Numbers : ', 'ultimate-sms-notifications' ) . '</strong>' . $phone_number . '<br/><strong>' . __( 'Message sent : ', 'ultimate-sms-notifications' ) . '</strong>' . $message . '<br/><strong>' . __( 'Delivery Messages Status : ', 'ultimate-sms-notifications' ) . '</strong>' . $return . '<br/>' . 'Sent from <strong>Ultimate SMS & WhatsApp Notifications for WooCommerce</strong>';
                    $order->add_order_note( $orders_messages );
                    echo  $return ;
                } else {
                    esc_html_e( 'Please fill messages and phone numbers fields before press Send.', 'ultimate-sms-notifications' );
                }
            
            }
            
            wp_die();
        }
    
    }
    
    public function footer_credits( $text )
    {
        global  $usn_utility ;
        if ( $usn_utility->is_usn_page() ) {
            $text = sprintf(
                __( 'If you like %1$s please leave us a %2$s rating.This will make happy %3$s.', 'ultimate-sms-notifications' ),
                printf( '<strong>%s</strong>', esc_html__( 'Ultimate SMS & WhatsApp Notifications for WooCommerce', 'ultimate-sms-notifications' ) ),
                '<a href="https://wordpress.org/support/plugin/ultimate-sms-notifications/reviews?rate=5#new-post" target="_blank" class="wc-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'ultimate-sms-notifications' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
                sprintf( '<strong>%s</strong>', esc_html__( 'HomeScript team', 'ultimate-sms-notifications' ) )
            );
        }
        return $text;
    }
    
    public function get_api_response_code()
    {
        $posted_data = filter_input_array( INPUT_POST );
        $security = $posted_data['security'];
        
        if ( wp_verify_nonce( $security, 'woo-usn-ajax-nonce' ) ) {
            $ajax_data = $posted_data['data'];
            $testing_numbers = sanitize_text_field( $ajax_data['testing-numbers'] );
            $testing_message = sanitize_text_field( $ajax_data['testing-messages'] );
            $country_code = sanitize_text_field( $ajax_data['country_code'] );
            $testing_message = Woo_Usn_Utility::decode_message_to_send( null, $testing_message );
            
            if ( !$testing_numbers ) {
                $status_code = __( 'Please provide an phone number before to press Send SMS.', 'ultimate-sms-notifications' );
            } else {
                try {
                    $testing_numbers = Woo_Usn_Utility::get_right_phone_numbers( $country_code, $testing_numbers );
                    $testing_numbers = $country_code . $testing_numbers;
                    $return = $this->usn_api->send_sms( $testing_numbers, $testing_message );
                    $status_code = Woo_Usn_Utility::get_sms_status( $return, $this->usn_api->get_sms_api() );
                } catch ( Exception $errors ) {
                    $status_code = Woo_Usn_Utility::log_errors( $errors );
                }
            }
        
        }
        
        Woo_Usn_UI_Fields::format_html_fields( $status_code );
        wp_die();
    }
    
    public function check_requirements()
    {
        //Check if WC is installed.
        global  $usn_utility, $usn_api_is_defined ;
        $usn_utility::is_wc_active( 'admin' );
        $usn_utility::get_api_credentials();
        $options = get_option( 'woo_usn_wha_api_details' );
        
        if ( !$usn_api_is_defined ) {
            if ( isset( $options["api_choosed"] ) ) {
                return;
            }
            $links = '<a href="admin.php?page=ultimate-sms-notifications&tab=sms-api" >' . __( 'you can setup the plugin here', 'ultimate-sms-notifications' ) . '</a>';
            ?>
            <div class=" notice notice-error">
                <p>
					<?php 
            Woo_Usn_UI_Fields::format_html_fields( wp_sprintf( '<strong>Ultimate SMS & WhatsApp Notifications for WooCommerce is almost ready.</strong> To get started, %s.', $links ) );
            ?>
                </p>
            </div>
			<?php 
        }
        
        $options = get_option( 'woo_usn_options' );
        if ( isset( $options['woo_usn_sms_to_vendors'] ) ) {
            
            if ( !function_exists( 'wcfm_get_vendor_id_by_post' ) && !function_exists( "dokan" ) ) {
                ?>
                <div class=" notice notice-error">
                    <p>
						<?php 
                echo  __( '<strong>SMS Notifications to vendors</strong> requires <strong>Dokan</strong>/<strong>WCFM Vendors</strong> in order to work. ', 'ultimate-sms-notifications' ) ;
                ?>
                    </p>
                </div>
				<?php 
            }
        
        }
        $woo_usn_display_banner = get_option( 'woo_usn_display_banner' );
        $display_banner = 'display : block ;';
        
        if ( $woo_usn_display_banner == 1 ) {
            $reviews_already_give = get_option( 'woousn_have_already_give_reviews' );
            $dismiss_banner = get_option( 'woousn_dismiss_banner' );
            if ( $dismiss_banner || $reviews_already_give ) {
                $display_banner = 'display : none ;';
            }
        }
        
        // display newsletters banner.
        ?>
        <div id="woo_usn_banner" class="notice notice-info" style="<?php 
        echo  $display_banner ;
        ?>">
            <p>
                <img id="woorci-logo" src="<?php 
        echo  WOO_USN_URL . '../admin/img/usn.svg' ;
        ?>"
                     style="width : 50px; float :left;">
            <div id="woousn-thank-you" style="display : inline;">
                <p id="woorci-banner-content"><strong
                            style="font-size : 15px;"><?php 
        _e( 'Enjoying Ultimate SMS & WhatsApp Notifications for WooCommerce?', 'ultimate-sms-notifications' );
        ?></strong>
                    <br/> <?php 
        _e( ' Hope that you had a neat and snappy experience with the plugin. Would you please show us a little love by rating us in the WordPress.org?', 'ultimate-sms-notifications' );
        ?>
                </p>
                <p style="position: relative; left: 1px; top: -8px;">
                    <a href="https://wordpress.org/support/plugin/ultimate-sms-notifications/reviews/#postform"
                       id="usn-review" target="_blank"><span class="dashicons dashicons-external"></span>Sure! I'd love
                        to!</a>
                    &nbsp
                    <a href="#" id="usn-already-give-review"><span class="dashicons dashicons-smiley"></span>I've
                        already
                        left a review</a> &nbsp
                    <a href="#" id="usn-never-show-again"><span class="dashicons dashicons-dismiss"></span>Never show
                        again</a>
                </p>
            </div>
        </div>
		<?php 
        do_action( 'woo_usn_admin_notices' );
    }
    
    public function save_api_credentials()
    {
        $success = 0;
        $posted_data = filter_input_array( INPUT_POST );
        $security = $posted_data['security'];
        
        if ( wp_verify_nonce( $security, 'woo-usn-ajax-nonce' ) ) {
            $data = $posted_data['data'];
            $api_choosed = sanitize_text_field( $data['api_choosed'] );
            $first_api_key = sanitize_text_field( $data['first_api_key'] );
            $second_api_key = sanitize_text_field( $data['second_api_key'] );
            update_option( 'woo_usn_api_choosed', $api_choosed );
            
            if ( 'Twilio' == $api_choosed ) {
                $twilio_phone_number = sanitize_text_field( $data['woo_usn_twilio_phone_number'] );
                if ( !$twilio_phone_number ) {
                    return $success;
                }
                update_option( 'woo_usn_twilio_account_sid', $first_api_key );
                update_option( 'woo_usn_twilio_auth_token', $second_api_key );
                update_option( 'woo_usn_twilio_phone_number', $twilio_phone_number );
                $success = 1;
            } elseif ( 'Telesign' == $api_choosed ) {
                update_option( 'woo_usn_telesign_custom_id', $first_api_key );
                update_option( 'woo_usn_telesign_api_key', $second_api_key );
                $success = 1;
            } elseif ( 'Kivalo' == $api_choosed ) {
                update_option( 'woo_usn_kivalo_from_phone_number', $first_api_key );
                update_option( 'woo_usn_kivalo_api_key', $second_api_key );
                $success = 1;
            } elseif ( "WA API" == $api_choosed ) {
                update_option( 'woo_usn_waapi_client_id', $first_api_key );
                update_option( 'woo_usn_waapi_instance_id', $second_api_key );
                update_option( 'woo_usn_waapi_domain_url', $data['api_domain'] );
                $success = 1;
            } elseif ( "Message Bird" == $api_choosed ) {
                update_option( 'woo_usn_message_bird_from_number', $first_api_key );
                update_option( 'woo_usn_message_bird_api_key', $second_api_key );
                $success = 1;
            } elseif ( "SendChamp" == $api_choosed ) {
                update_option( 'woo_usn_sendchamp_from_number', $first_api_key );
                update_option( 'woo_usn_sendchamp_api_key', $second_api_key );
                update_option( 'woo_usn_sendchamp_domain_url', $data['api_domain'] );
                $success = 1;
            } elseif ( "twilio_whatsapp" == $api_choosed ) {
                $twilio_phone_number = sanitize_text_field( $data['woo_usn_twilio_whatsapp_phone_number'] );
                if ( !$twilio_phone_number ) {
                    return $success;
                }
                update_option( 'woo_usn_twilio_account_sid', $first_api_key );
                update_option( 'woo_usn_twilio_auth_token', $second_api_key );
                update_option( 'woo_usn_twilio_whatsapp_phone_number', $twilio_phone_number );
                $success = 1;
            } elseif ( "eBulkSMS" == $api_choosed ) {
                $ebulksms_phone_number = sanitize_text_field( $data['woo_usn_ebulksms_phone_number'] );
                update_option( 'woo_usn_ebulksms_username', $first_api_key );
                update_option( 'woo_usn_ebulksms_api_key', $second_api_key );
                update_option( 'woo_usn_ebulksms_from_number', $ebulksms_phone_number );
                $success = 1;
            } else {
                $creds = array(
                    'first'  => $data['first_api_key'],
                    'second' => $data['second_api_key'],
                );
                update_option( 'woo_usn_creds', $creds );
                $success = 1;
            }
        
        }
        
        $success = apply_filters( 'woo_usn_save_credentials_status', $success, $posted_data['data'] );
        echo  wp_json_encode( array(
            'status' => $success,
        ) ) ;
        wp_die();
    }
    
    public function delete_api_credentials()
    {
        $return = 0;
        $posted_data = filter_input_array( INPUT_POST );
        $security = $posted_data['security'];
        
        if ( wp_verify_nonce( $security, 'woo-usn-ajax-nonce' ) ) {
            $data = $posted_data['data'];
            $this->api_choosed = sanitize_text_field( $data['api_choosed'] );
            delete_option( 'woo_usn_api_choosed' );
            
            if ( 'Twilio' == $this->api_choosed ) {
                delete_option( 'woo_usn_twilio_account_sid' );
                delete_option( 'woo_usn_twilio_auth_token' );
                delete_option( 'woo_usn_twilio_phone_number' );
                $return = 1;
            } elseif ( 'Telesign' == $this->api_choosed ) {
                delete_option( 'woo_usn_telesign_api_key' );
                delete_option( 'woo_usn_telesign_custom_id' );
                $return = 1;
            } elseif ( 'Kivalo' == $this->api_choosed ) {
                delete_option( 'woo_usn_kivalo_from_phone_number' );
                delete_option( 'woo_usn_kivalo_api_key' );
                $return = 1;
            } elseif ( 'WA API' == $this->api_choosed ) {
                delete_option( 'woo_usn_waapi_client_id' );
                delete_option( 'woo_usn_waapi_instance_id' );
                delete_option( 'woo_usn_waapi_domain_url' );
                $return = 1;
            } elseif ( "Message Bird" == $this->api_choosed ) {
                delete_option( 'woo_usn_message_bird_from_number' );
                delete_option( 'woo_usn_message_bird_api_key' );
                $return = 1;
            } else {
                delete_option( 'woo_usn_creds' );
                $return = 1;
            }
        
        }
        
        $return = apply_filters( 'woo_usn_delete_credentials_status', $return, $posted_data['data'] );
        echo  wp_json_encode( array(
            'status' => $return,
        ) ) ;
        wp_die();
    }
    
    public function send_sms_to_cl()
    {
        $posted_data = filter_input_array( INPUT_POST );
        $security = $posted_data['security'];
        
        if ( wp_verify_nonce( $security, 'woo-usn-ajax-nonce' ) ) {
            $cl = $posted_data['data']["contact-list"];
            $msg = $posted_data['data']["testing-messages"];
            $order_lists = Woo_Usn_Customer_List::get_customer_details_from_id( $cl );
            foreach ( $order_lists as $order_id ) {
                $_order = new WC_Order( $order_id );
                $country = $_order->get_billing_country();
                $_phone_number = Woo_Usn_Utility::get_right_phone_numbers( $country, $_order->get_billing_phone() );
                if ( !isset( $_phone_number ) && !isset( $country ) ) {
                    return;
                }
                $country_indicator = Woo_Usn_Utility::get_country_town_code( $country );
                $phone_number = $country_indicator . Woo_Usn_Utility::get_right_phone_numbers( $country_indicator, $_phone_number );
                $sms_obj = new Woo_Usn_SMS();
                $sms_obj->send_sms( $phone_number, $msg );
            }
        }
        
        echo  __( 'SMS are being sent, you can check SMS logs for more details.', 'ultimate-sms-notificaions' ) ;
        wp_die();
    }

}