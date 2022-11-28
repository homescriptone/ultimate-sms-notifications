<?php
/**
 * This class provide fields for differents features of the plugin.
 */
class Woo_Usn_UI_Fields {

	/**
	 * This function returns names of rules used for Customer List.
	 *
	 * @return array
	 */
	public static function get_cl_rules_names() {
		$list = array(
			'_completed_date'     => __( 'If completed date is', 'ultimate-sms-notifications' ),
			'_paid_date'     	  => __( 'If paid date is', 'ultimate-sms-notifications' ),
			'_payment_method'     => __( 'If payment methods used', 'ultimate-sms-notifications' ),
			'_shipping_method'    => __( 'If shipping methods used', 'ultimate-sms-notifications' ),
			'_billing_country'    => __( 'If customer billing country', 'ultimate-sms-notifications' ),
			'_shipping_country'   => __( 'If customer delivery country', 'ultimate-sms-notifications' ),
			'_billing_email'      => __( 'If customer email domain name', 'ultimate-sms-notifications' ),
			'shop_order'          => __( 'If order status', 'ultimate-sms-notifications' ),
			'_order_total'        => __( 'If order amount', 'ultimate-sms-notifications' ),
			'_order_tax'          => __( 'If order taxes', 'ultimate-sms-notifications' ),
			'_order_shipping'     => __( 'If shipping amount', 'ultimate-sms-notifications' ),
			'_order_shipping_tax' => __( 'If shipping amount (inc taxes)', 'ultimate-sms-notifications' ),
			'_customer_mobile_marketing' => __( 'If customer mobile marketing consent', 'ultimate-sms-notifications' ),
		);

		return apply_filters( 'woo_usn_cl_rules_names', $list );
	}

	/**
	 * This function returns operators for rules used for Customer List.
	 *
	 * @return array
	 */
	public static function get_cl_operators_names() {
		$list = array(
			'woo-usn-math-operators' => array(
				'<'  => __( 'is less than', 'ultimate-sms-notifications' ),
				'<=' => __( 'is less or equal to', 'ultimate-sms-notifications' ),
				'==' => __( 'equals', 'ultimate-sms-notifications' ),
				'>'  => __( 'is more than', 'ultimate-sms-notifications' ),
				'>=' => __( 'is more or equal to', 'ultimate-sms-notifications' ),
				// '%'        => __( 'is a multiple of', 'ultimate-sms-notifications' ),
			),
			'woo-usn-operators'      => array(
				'in'     => __( 'IN', 'ultimate-sms-notifications' ),
				'not-in' => __( 'NOT IN', 'ultimate-sms-notifications' ),
			),
		);

		return apply_filters( 'woo_usn_cl_operators_names', $list );
	}

	/**
	 * Format html fields.
	 *
	 * @param mixed $html_field HTMLFields.
	 *
	 * @return void
	 */
	public static function format_html_fields( $html_field ) {
		$allowedposttags = array();
		$allowed_atts    = array(
			'align'            => array(),
			'class'            => array(),
			'type'             => array(),
			'id'               => array(),
			'dir'              => array(),
			'lang'             => array(),
			'style'            => array(),
			'xml:lang'         => array(),
			'src'              => array(),
			'alt'              => array(),
			'href'             => array(),
			'rev'              => array(),
			'target'           => array(),
			'novalidate'       => array(),
			'value'            => array(),
			'name'             => array(),
			'tabindex'         => array(),
			'action'           => array(),
			'method'           => array(),
			'for'              => array(),
			'width'            => array(),
			'height'           => array(),
			'data'             => array(),
			'title'            => array(),
			'checked'          => array(),
			'placeholder'      => array(),
			'rel'              => array(),
			'data-analytic-id' => array(),
			'data-id'          => array(),
			'rows'             => array(),
			'selected'         => array(),
			'multiple'         => array(),

		);
		$allowedposttags['&nsbp;']    = $allowed_atts;
		$allowedposttags['form']     = $allowed_atts;
		$allowedposttags['label']    = $allowed_atts;
		$allowedposttags['select']   = $allowed_atts;
		$allowedposttags['input']    = $allowed_atts;
		$allowedposttags['textarea'] = $allowed_atts;
		$allowedposttags['iframe']   = $allowed_atts;
		$allowedposttags['script']   = $allowed_atts;
		$allowedposttags['style']    = $allowed_atts;
		$allowedposttags['strong']   = $allowed_atts;
		$allowedposttags['small']    = $allowed_atts;
		$allowedposttags['table']    = $allowed_atts;
		$allowedposttags['span']     = $allowed_atts;
		$allowedposttags['abbr']     = $allowed_atts;
		$allowedposttags['code']     = $allowed_atts;
		$allowedposttags['pre']      = $allowed_atts;
		$allowedposttags['div']      = $allowed_atts;
		$allowedposttags['img']      = $allowed_atts;
		$allowedposttags['h1']       = $allowed_atts;
		$allowedposttags['h2']       = $allowed_atts;
		$allowedposttags['h3']       = $allowed_atts;
		$allowedposttags['h4']       = $allowed_atts;
		$allowedposttags['h5']       = $allowed_atts;
		$allowedposttags['h6']       = $allowed_atts;
		$allowedposttags['ol']       = $allowed_atts;
		$allowedposttags['br']       = $allowed_atts;
		$allowedposttags['ul']       = $allowed_atts;
		$allowedposttags['li']       = $allowed_atts;
		$allowedposttags['em']       = $allowed_atts;
		$allowedposttags['hr']       = $allowed_atts;
		$allowedposttags['br']       = $allowed_atts;
		$allowedposttags['tr']       = $allowed_atts;
		$allowedposttags['th']       = $allowed_atts;
		$allowedposttags['td']       = $allowed_atts;
		$allowedposttags['p']        = $allowed_atts;
		$allowedposttags['a']        = $allowed_atts;
		$allowedposttags['b']        = $allowed_atts;
		$allowedposttags['i']        = $allowed_atts;
		$allowedposttags['option']   = $allowed_atts;
		$allowedposttags['button']   = $allowed_atts;
		echo wp_kses( $html_field, $allowedposttags );
	}

}
