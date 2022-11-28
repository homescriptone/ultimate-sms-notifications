<?php

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'woo_usn/v1',
			'/send_sms',
			array(
				'methods'  => 'POST',
				'callback' => 'woo_usn_send_mobile_notif_by_rest',
			),
			true
		);
	}
);


/**
 * Wrapper for sending SMS or WhatsApp message via API.
 */
function woo_usn_send_mobile_notif_by_rest( WP_REST_Request $request ) {
	$params = $request->get_params();
	if ( isset( $params['message'] ) ) {
        $message =  $params['message'];
    }
  
    if ( isset( $params['phone_number'] ) ) {
        $phone_number =  $params['phone_number'];
    }

    if (  ! $phone_number ) {
        return wp_send_json_error( __( 'Phone Number is not provided', 'ultimate-sms-notifications' ), 500 );
    }

    $sms_obj = new Woo_Usn_SMS();
    $delivery_status = $sms_obj->send_sms( $phone_number, $message );
    $status_code     = 'Failed to send message';
    if ( 400 == $delivery_status ) {
        $status_code = "Message sent successfully";
    }
    $delivery_status = 200;
	return wp_send_json(
		array(
			'status_code'  => $status_code,
            'message_sent' => $message,
            'phone_number' => $phone_number
		),
		$delivery_status
	);
}