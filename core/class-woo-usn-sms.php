<?php

/**
 *
 * This class is responsible to send SMS using API.
 */
class Woo_Usn_SMS
{
    /**This variable contains all the data related to the SMS API object.
     *
     * @var object
     */
    private  $sms_api ;
    /**
     * Class constructor.
     */
    public function __construct()
    {
        global  $usn_utility ;
        $this->sms_api = json_decode( $usn_utility::get_api_credentials() );
    }
    
    public function get_sms_api()
    {
        return $this->sms_api->api_used;
    }
    
    /**
     * Send SMS using Twilio Rest API.
     *
     * @return mixed
     */
    private function send_sms_with_rest(
        $id,
        $token,
        $to,
        $body,
        $from = false
    )
    {
        $headers = array(
            'Authorization' => 'Basic ' . base64_encode( $id . ':' . $token ),
        );
        
        if ( 'Twilio' === $this->sms_api->api_used || 'twilio_whatsapp' === $this->sms_api->api_used ) {
            $url = "https://api.twilio.com/2010-04-01/Accounts/{$id}/Messages.json";
            $data = array(
                'From' => $from,
                'To'   => $to,
                'Body' => $body,
            );
        } elseif ( 'Telesign' === $this->sms_api->api_used ) {
            $url = 'https://rest-api.telesign.com/v1/messaging';
            $data = array(
                'phone_number' => $to,
                'message'      => $body,
                'message_type' => 'ARN',
            );
        } elseif ( 'Message Bird' === $this->sms_api->api_used ) {
            $url = 'https://rest.messagebird.com/messages';
            $headers = array(
                'Authorization' => 'AccessKey ' . $token,
            );
            $data = array(
                'originator' => $id,
                'recipients' => $to,
                'body'       => $body,
            );
        } elseif ( 'SendChamp' === $this->sms_api->api_used ) {
            $headers = array(
                'Authorization' => 'Bearer ' . $token,
            );
            $url = get_option( 'woo_usn_sendchamp_domain_url' );
            $data = array(
                'to'          => [ $to ],
                'message'     => $body,
                'sender_name' => $id,
                'route'       => 'dnd',
            );
        }
        
        $result = wp_remote_post( $url, array(
            'body'    => $data,
            'headers' => $headers,
            'timeout' => 65,
        ) );
        $body_result = wp_remote_retrieve_body( $result );
        
        if ( is_object( $body_result ) ) {
            $decoded = get_object_vars( $body_result );
        } else {
            $decoded = json_decode( $body_result );
            
            if ( "SendChamp" == $this->sms_api->api_used ) {
                $decode_sendchmap_data = get_object_vars( $decoded );
                
                if ( $decode_sendchmap_data['status'] == "success" ) {
                    return 200;
                } else {
                    
                    if ( function_exists( 'wc_get_logger' ) ) {
                        $wc_log = wc_get_logger();
                        $wc_log->error( 'SendChamp error : ' . print_r( $decode_sendchmap_data, true ), array(
                            'source' => 'ultimate-sms-notifications',
                        ) );
                    } else {
                        Woo_Usn_Utility::write_log( 'SendChamp error : ' . print_r( $decoded, true ) );
                    }
                    
                    return 400;
                }
            
            }
        
        }
        
        
        if ( 'Twilio' === $this->sms_api->api_used ) {
            return $decoded->status;
        } elseif ( 'Message Bird' === $this->sms_api->api_used ) {
            $recipients = current( $decoded->recipients->items );
            $status = $recipients->status;
            
            if ( null === $status ) {
                return 400;
            } else {
                return 200;
            }
        
        } else {
            
            if ( null !== $decoded->status->code ) {
                return $decoded->status->code;
            } else {
                return 400;
            }
        
        }
    
    }
    
    /**
     * This functions send SMS to phone numbers using the SMS API defined.
     *
     * @param string $phone_number Customer phone number.
     * @param string $message_to_send Message to send to customer.
     *
     * @return bool|string
     */
    public final function send_sms( $phone_number, $message_to_send )
    {
        $status = 400;
        $phone_number = str_ireplace( ' ', '', $phone_number );
        $skip_sms = false;
        $sms_api = $this->sms_api->api_used;
        if ( !$skip_sms ) {
            
            if ( 'Twilio' === $sms_api ) {
                $from_number = get_option( 'woo_usn_twilio_phone_number' );
                if ( !strpos( '+', $phone_number ) ) {
                    $phone_number = '+' . $phone_number;
                }
                $status = 400;
                $status_code = $this->send_sms_with_rest(
                    $this->sms_api->first_api_key,
                    $this->sms_api->second_api_key,
                    $phone_number,
                    $message_to_send,
                    $from_number
                );
                if ( 'queued' == $status_code ) {
                    $status = 200;
                }
            } elseif ( 'Telesign' === $sms_api ) {
                $status = $this->send_sms_with_rest(
                    $this->sms_api->first_api_key,
                    $this->sms_api->second_api_key,
                    $phone_number,
                    $message_to_send,
                    false
                );
            } elseif ( 'Kivalo' === $this->sms_api->api_used ) {
                $from_number = $this->sms_api->first_api_key;
                $api_key = $this->sms_api->second_api_key;
                $url = "http://sms.kivalosolutions.com/sms/api?action=send-sms&api_key={$api_key}&to={$phone_number}&from={$from_number}&sms={$message_to_send}";
                $response = wp_remote_get( $url, array(
                    'timeout' => 30,
                ) );
                $response_body = wp_remote_retrieve_body( $response );
                $decoded_body = json_decode( $response_body );
                $status = $decoded_body->code;
            } elseif ( 'Message Bird' === $sms_api ) {
                $status = $this->send_sms_with_rest(
                    $this->sms_api->first_api_key,
                    $this->sms_api->second_api_key,
                    $phone_number,
                    $message_to_send,
                    false
                );
            } elseif ( 'SendChamp' === $sms_api ) {
                $status = $this->send_sms_with_rest(
                    $this->sms_api->first_api_key,
                    $this->sms_api->second_api_key,
                    $phone_number,
                    $message_to_send,
                    false
                );
            }
        
        }
        $status = apply_filters(
            'woo_usn_send_sms_to_customer',
            $status,
            $sms_api,
            $phone_number,
            $message_to_send
        );
        /**
         * you have the ability to send sms using a external sms api if you don't want to use the previous ones.
         */
        return $status;
    }
    
    /**
     * This method allows to send SMS based on the data related at a WC order.
     *
     * @param int $order WooCommerce Order ID.
     *
     * @return void
     */
    public function send_api_messages( $order )
    {
        global  $usn_utility ;
        $log = new WC_Logger();
        $_order = new WC_Order( $order );
        $country = $_order->get_billing_country();
        $_phone_number = $usn_utility::get_right_phone_numbers( $country, $_order->get_billing_phone() );
        if ( !isset( $_phone_number ) && !isset( $country ) ) {
            return;
        }
        $country_indicator = $usn_utility::get_country_town_code( $country );
        $phone_number = $country_indicator . $usn_utility::get_right_phone_numbers( $country_indicator, $_phone_number );
        $options = get_option( 'woo_usn_options' );
        if ( isset( $options['woo_usn_message_after_customer_purchase'] ) ) {
            $customer_message = $usn_utility::decode_message_to_send( $order, $options['woo_usn_defaults_messages'] );
        }
        
        if ( isset( $options['woo_usn_sms_to_admin'] ) ) {
            $admin_can_receive_messages = esc_attr( $options['woo_usn_sms_to_admin'] );
            $admin_numbers = esc_attr( $options['woo_usn_admin_numbers'] );
            $admin_message = $usn_utility::decode_message_to_send( $order, $options['woo_usn_admin_messages_template'] );
        }
        
        // send SMS to customer.
        if ( isset( $customer_message ) && !is_admin() ) {
            $this->sms_to_numbers( $_order, $phone_number, $customer_message );
        }
        // send SMS to shop manager.
        if ( isset( $admin_can_receive_messages ) && !is_admin() ) {
            $this->sms_to_numbers( $_order, $admin_numbers, $admin_message );
        }
    }
    
    private function sms_to_numbers( $order_obj, $pn, $message )
    {
        $return = $this->send_sms( $pn, $message );
        $return = Woo_Usn_Utility::get_sms_status( $return, $this->get_sms_api() );
        $orders_messages = '<br/><strong>' . __( 'Phone Numbers : ', 'ultimate-sms-notifications' ) . '</strong>' . $pn . '<br/><strong>' . __( 'Messages : ', 'ultimate-sms-notifications' ) . '</strong>' . $message . '<br/><strong>' . __( 'Delivery Messages : ', 'ultimate-sms-notifications' ) . '</strong>' . $return . '<br/>' . 'Sent from <strong>Ultimate SMS & WhatsApp Notifications for WooCommerce</strong>';
        $order_obj->add_order_note( $orders_messages );
    }
    
    public static function send_sms_to_new_customers( $customer_obj, $data )
    {
        //prevent multiple sms send after user creation.
        if ( $customer_obj->get_date_created() != null ) {
            return;
        }
        $woo_usn_options = get_option( 'woo_usn_options' );
        
        if ( !empty($woo_usn_options['woo_usn_messages_after_customer_signup']) ) {
            $billing_phone = $data['billing_phone'];
            $billing_country = $data['billing_country'];
            $real_country_code = Woo_Usn_Utility::get_country_town_code( $billing_country );
            $real_pn = $real_country_code . $billing_phone;
            $template_message = $woo_usn_options['woo_usn_defaults_customer_signup'];
            $customer_message = preg_replace( array( '/%store_name%/', '/%customer_name%/', '/%customer_phone_number%/' ), array( get_bloginfo( 'name' ), $data['billing_first_name'] . ' ' . $data['billing_last_name'], $real_pn ), $template_message );
            $sms = new Woo_Usn_SMS();
            $sms->send_sms( $real_pn, $customer_message );
        }
    
    }

}