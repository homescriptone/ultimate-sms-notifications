<?php

/**
 * This class is responsible to display the settings page for the plugin.
 */
class Woo_Usn_Admin_Settings
{
    /**
     * Function for configure settings.
     */
    public static function configure_woo_usn_settings()
    {
        ?>
		<?php 
        
        if ( isset( $_GET ) && isset( $_GET['tab'] ) && !wp_verify_nonce( '_wpnonce' ) ) {
            $active_tab = filter_input( INPUT_GET, 'tab' );
        } else {
            $active_tab = 'options';
        }
        
        $settings_names = apply_filters( 'woo_usn_settings_names', array(
            'options'      => array(
            'url'   => '?page=ultimate-sms-notifications&tab=options',
            'title' => __( 'SMS Notifications', 'ultimate-sms-notifications' ),
        ),
            'sms-api'      => array(
            'url'   => '?page=ultimate-sms-notifications&tab=sms-api',
            'title' => __( 'SMS Gateways', 'ultimate-sms-notifications' ),
        ),
            'whatsapp-api' => array(
            'url'   => admin_url( 'admin.php?page=ultimate-sms-notifications-pricing' ),
            'title' => __( 'WhatsApp Gateways', 'ultimate-sms-notifications' ),
        ),
        ) );
        ?>
		<div class="wrap">
			<h1> <?php 
        __( 'Ultimate SMS Notifications for WooCommerce', 'ultimate-sms-notifications' );
        ?></h1>
			<?php 
        settings_errors();
        ?>

			<h2 class="woousn nav-tab-wrapper">
				<?php 
        foreach ( $settings_names as $keyname => $keyvalues ) {
            $class_name = ( $active_tab === $keyname ? 'woousn-tab-active nav-tab-active' : '' );
            ?>
					<a href="<?php 
            echo  wp_kses_post( $keyvalues['url'] ) ;
            ?>"
					   class="woousn-tab nav-tab <?php 
            echo  esc_attr( $class_name ) ;
            ?>"> <?php 
            echo  wp_kses_post( $keyvalues['title'] ) ;
            ?></a>
					<?php 
        }
        ?>
			</h2>


			<form method="post" action="options.php">
				<?php 
        
        if ( 'options' === $active_tab ) {
            ?>
					<div class="woousn-options-tab">
						<?php 
            settings_fields( 'woo_usn_options' );
            do_settings_sections( 'woo_usn_options' );
            do_action( 'woo_usn_options' );
            submit_button();
            ?>
					</div>
					<?php 
        } elseif ( 'sms-api' === $active_tab ) {
            self::display_settings_fields();
        } elseif ( 'whatsapp-api' === $active_tab ) {
        }
        
        do_action( 'woo_usn_add_settings_tabs', $active_tab );
        ?>

			</form>
		</div>
		<?php 
    }
    
    /**
     * Function to display the options on each tab.
     */
    public static function display_options_on_each_tab()
    {
        /*
         * -----------------------------------------------------------------------------
         * Options.
         * -----------------------------------------------------------------------------
         */
        // Select Options.
        add_settings_section(
            'woo_usn_options_page',
            // ID used to identify this section and with which to register options.
            __( 'Setup your SMS Notifications', 'ultimate-sms-notifications' ),
            // Title to be displayed on the administration page.
            __CLASS__ . '::description_for_the_tab_pages',
            // Callback used to render the description of the section.
            'woo_usn_options'
        );
        add_settings_field(
            'woo_usn_messages_from_orders',
            // ID used to identify the field throughout the theme.
            __( 'Send SMS from WooCommerce Orders Details :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::messages_from_orders',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-message-from-orders',
            )
        );
        add_settings_field(
            'woo_usn_messages_after_customer_signup',
            // ID used to identify the field throughout the theme.
            __( 'Send SMS to customer registering a new account from checkout page :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::after_customer_sign',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-message-after-signups',
            )
        );
        add_settings_field(
            'woo_usn_defaults_customer_signup',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send to customer signing up :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::default_signup_message',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-signup-defaults-messages',
            )
        );
        add_settings_field(
            'woo_usn_customer_signup_tags',
            // ID used to identify the field throughout the theme.
            '',
            // The label to the left of the option interface element.
            __CLASS__ . '::display_customer_signup_tags',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'is_vendor' => true,
                'class'     => 'woo-usn-signup-defaults-messages',
            )
        );
        add_settings_field(
            'woo_usn_message_after_customer_purchase',
            // ID used to identify the field throughout the theme.
            __( 'Send SMS after customer purchase :  ', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::after_customer_purchase',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-message-after-customer-purchase',
            )
        );
        add_settings_field(
            'woo_usn_defaults_messages',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send to customer after a successfully purchase on your store :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::default_sms_messages',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-defaults-messages',
            )
        );
        add_settings_field(
            'woo_usn_message_after_order_changed',
            // ID used to identify the field throughout the theme.
            __( 'Send SMS after changing WooCommerce Order Status :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::after_order_changed',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-message-after-order-changed',
            )
        );
        add_settings_field(
            'woo_usn_completed_messages',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send when the order status is completed :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::sms_when_completed',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_processing_messages',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send when the order status is processing :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::sms_when_processing',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_cancelled_messages',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send when the order status is cancelled :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::sms_when_cancelled',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_refunded_messages',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send when the order status is refunded :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::sms_when_refunded',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_on_hold_messages',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send when the order status is on hold :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::sms_when_on_hold',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_failed_messages',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send when the order status is failed :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::sms_when_failed',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_pending_payment_messages',
            // ID used to identify the field throughout the theme.
            __( 'SMS to send when the order status is pending payment :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::sms_when_pending_payment',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_sms_to_admin',
            // ID used to identify the field throughout the theme.
            __( 'Send SMS to shop owner/manager after an order completed :  ', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::messages_to_admin',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                // The array of arguments to pass to the callback. In this case, just a description.
                '',
            )
        );
        add_settings_field(
            'woo_usn_admin_messages_template',
            // ID used to identify the field throughout the theme.
            __( 'SMS that shop owner/manager will receive after a completed order : ', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::display_shop_admin_template_messages',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-admin-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_admin_numbers',
            // ID used to identify the field throughout the theme.
            __( 'Phone number of the store owner/manager : ', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::display_shop_admin_numbers',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-admin-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_sms_to_vendors',
            // ID used to identify the field throughout the theme.
            __( 'Send SMS to vendors after an order completed :', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::messages_to_vendor',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                // The array of arguments to pass to the callback. In this case, just a description.
                '',
            )
        );
        add_settings_field(
            'woo_usn_vendor_messages_template',
            // ID used to identify the field throughout the theme.
            __( 'SMS that vendor will receive after an completed order : ', 'ultimate-sms-notifications' ),
            // The label to the left of the option interface element.
            __CLASS__ . '::display_vendor_template_messages',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'class' => 'woo-usn-vendor-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_vendor_options_tags',
            // ID used to identify the field throughout the theme.
            '',
            // The label to the left of the option interface element.
            __CLASS__ . '::display_options_tags',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                'is_vendor' => true,
                'class'     => 'woo-usn-vendor-completed-messages',
            )
        );
        add_settings_field(
            'woo_usn_options_tags',
            // ID used to identify the field throughout the theme.
            '',
            // The label to the left of the option interface element.
            __CLASS__ . '::display_options_tags',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                // The array of arguments to pass to the callback. In this case, just a description.
                '',
            )
        );
        add_settings_field(
            'woo_usn_sms_consent',
            // ID used to identify the field throughout the theme.
            __( 'Get customer approbation before sending him SMS : ', 'ultimate-sms-notifications' ),
            __CLASS__ . '::display_consent_sms',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                // The array of arguments to pass to the callback. In this case, just a description.
                '',
            )
        );
        add_settings_field(
            'woo_usn_sms_default_country',
            // ID used to identify the field throughout the theme.
            __( 'Default Country to use on the Phone Number country selector : ', 'ultimate-sms-notifications' ),
            __CLASS__ . '::choose_default_country_selector',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                // The array of arguments to pass to the callback. In this case, just a description.
                '',
            )
        );
        add_settings_field(
            'woo_usn_failed_emails_notifications',
            // ID used to identify the field throughout the theme.
            __( 'Send Notifications to shop owner/manager if emails sending failed : ', 'ultimate-sms-notifications' ),
            __CLASS__ . '::send_failed_email_notification',
            // The name of the function responsible for rendering the option interface.
            'woo_usn_options',
            // The page on which this option will be displayed.
            'woo_usn_options_page',
            // The name of the section to which this field belongs.
            array(
                // The array of arguments to pass to the callback. In this case, just a description.
                '',
            )
        );
        do_action( 'woo_usn_options_settings_field' );
        register_setting( 'woo_usn_options', 'woo_usn_options' );
    }
    
    public static function send_failed_email_notification()
    {
        $options = get_option( 'woo_usn_options' );
        $checked = 0;
        if ( !empty($options['woo_usn_failed_emails_notifications']) ) {
            $checked = 1;
        }
        Woo_Usn_UI_Fields::format_html_fields( __( 'The shop owner/manager will receive an automated SMS once a email failed to send. ( Pro Feature )', 'ultimate-sms-notifications' ) );
        echo  '<br/>' ;
        Woo_Usn_UI_Fields::format_html_fields( wp_sprintf( "<a href='%s'>%s</a>", admin_url( 'admin.php?page=ultimate-sms-notifications-pricing' ), __( 'Click here to upgrade', 'ultimate-sms-notifications' ) ) );
    }
    
    public static function choose_default_country_selector()
    {
        $options = get_option( 'woo_usn_options' );
        $countries = new WC_Countries();
        homescript_input_fields( 'woo_usn_options[default_country_selector]', array(
            'type'    => 'select',
            'options' => $countries->get_countries(),
        ), $options['default_country_selector'] ?? 'IN' );
    }
    
    public static function display_consent_sms()
    {
        $options = get_option( 'woo_usn_options' );
        $checked = 1;
        if ( !isset( $options['woo_usn_sms_consent'] ) ) {
            $checked = 0;
        }
        $message = __( 'By enabling, customers can decide if they want to receive mobile notifications.', 'ultimate-sms-notifications' );
        $message .= '<br/>' . wp_sprintf( "<a href='%s'>%s</a>", admin_url( 'admin.php?page=ultimate-sms-notifications-pricing' ), __( 'Click here to upgrade', 'ultimate-sms-notifications' ) );
        Woo_Usn_UI_Fields::format_html_fields( $message );
    }
    
    /**
     * Message to vendor.
     */
    public static function messages_to_vendor()
    {
        $options = get_option( 'woo_usn_options' );
        $checked = 0;
        if ( !empty($options['woo_usn_sms_to_vendors']) ) {
            $checked = 1;
        }
        Woo_Usn_UI_Fields::format_html_fields( __( 'The vendor phone number will receive an automated SMS once a customer purchases products from his shop. ( Pro Feature )', 'ultimate-sms-notifications' ) );
        echo  '<br/>' ;
        Woo_Usn_UI_Fields::format_html_fields( wp_sprintf( "<a href='%s'>%s</a>", admin_url( 'admin.php?page=ultimate-sms-notifications-pricing' ), __( 'Click here to upgrade', 'ultimate-sms-notifications' ) ) );
    }
    
    /**
     * Display admin template messages fields.
     */
    public static function display_shop_admin_template_messages()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_admin_messages_template]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_admin_messages_template'] ?? '',
        ) );
    }
    
    public static function display_vendor_template_messages()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_vendor_messages_template]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_vendor_messages_template'] ?? '',
        ) );
    }
    
    /**
     * Display settings fields.
     */
    public static function display_settings_fields()
    {
        do_action( 'woo_usn_options_before_sms_api_fields' );
        $display_settings = apply_filters( 'woo_usn_display_sms_api', true );
        
        if ( $display_settings ) {
            ob_start();
            ?>
			<div class="woousn-settings-panel usn-center-panel-values" id="usn-contents">
				<h3><?php 
            esc_html_e( 'Configure SMS Gateways', 'ultimate-sms-notifications' );
            ?></h3>
				<?php 
            esc_html_e( 'Please provide API Credentials in order to be able to use Ultimate SMS Notifications into your store.', 'ultimate-sms-notifications' );
            Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
            Woo_Usn_UI_Fields::format_html_fields( '<br/>Choose the suitable SMS API you want to use : ' );
            $options = get_option( 'woo_usn_api_choosed' );
            $items = apply_filters( 'woo_usn_api_choosed', array( 'Telesign', 'Twilio' ) );
            $api_html = "<select id='woo_usn_api_to_choose' name='woo_usn_api_choosed'>";
            foreach ( $items as $item_id => $item_value ) {
                
                if ( is_string( $item_id ) ) {
                    $selected = ( $options === $item_id ? 'selected="selected"' : '' );
                    $api_html .= "<option value='" . $item_id . "' {$selected}>" . $item_value . '</option>';
                } else {
                    $selected = ( $options === $item_value ? 'selected="selected"' : '' );
                    $api_html .= "<option value='{$item_value}' {$selected}>{$item_value}</option>";
                }
            
            }
            $api_html .= '</select>';
            Woo_Usn_UI_Fields::format_html_fields( $api_html );
            ?>
				<div id="woo_usn_api_telesign" class="wrap" style="display : block;" data-name="telesign">
					<?php 
            Woo_Usn_UI_Fields::format_html_fields( 'Set Telesign by providing information necessary in the fields below.<br/>' );
            Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
            homescript_input_fields( 'woo_usn_telesign_custom_id', array(
                'type'        => 'text',
                'label'       => '<strong>' . __( ' Customer ID : ', 'ultimate-sms-notifications' ) . '</strong>',
                'input_class' => array( 'woousn-text-customs-api' ),
                'placeholder' => 'FFFFFFFF-EEEE-DDDD-1234-AB1234567890',
                'required'    => true,
                'default'     => esc_attr( get_option( 'woo_usn_telesign_custom_id' ) ),
                'description' => __( "You can retrieve it from your <a href='https://portal.telesign.com/login' target='__blank'>Telesign Portal</a>.", 'ultimate-sms-notifications' ),
            ) );
            Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
            homescript_input_fields( 'woo_usn_telesign_api_key', array(
                'type'        => 'text',
                'label'       => '<strong>' . __( ' API Key : ', 'ultimate-sms-notifications' ) . '</strong>',
                'input_class' => array( 'woousn-text-customs-api' ),
                'required'    => true,
                'placeholder' => 'EXAMPLE----TE8sTgg45yusumoN6BYsBVkh+yRJ5czgsnCehZaOYldPJdmFh6NeX8kunZ2zU1YWaUw/0wV6xfw==',
                'default'     => esc_attr( get_option( 'woo_usn_telesign_api_key' ) ),
                'description' => __( "You can retrieve it from your <a href='https://portal.telesign.com/login' target='__blank'>Telesign Portal</a>.", 'ultimate-sms-notifications' ),
            ) );
            Woo_Usn_UI_Fields::format_html_fields( "You will need a Customer ID and API Key in order to use TeleSign’s API. If you already have an account you can retrieve\n\t\tthem from your account dashboard within the  <a href='https://portal.telesign.com/login'>Portal</a>. If you have not signed up\n\t\tyet, sign up <a href='https://portal.telesign.com/signup'>here</a>." );
            ?>
				</div>

				<div id="woo_usn_api_twilio" class="wrap" style="display : none;" data-name="twilio">
					<?php 
            Woo_Usn_UI_Fields::format_html_fields( 'Set Twilio by providing information necessary in the fields below.<br/>' );
            echo  '<br/>' ;
            homescript_input_fields( 'woo_usn_twilio_account_sid', array(
                'type'        => 'text',
                'label'       => '<strong>' . __( ' Account SID : ', 'ultimate-sms-notifications' ) . '</strong>',
                'input_class' => array( 'woousn-text-customs-api' ),
                'placeholder' => 'ACXXXXXXXXXXXXXXXXXX',
                'required'    => true,
                'default'     => esc_attr( get_option( 'woo_usn_twilio_account_sid' ) ),
                'description' => __( "You can retrieve it from your <a href='https://www.twilio.com/referral/bc0mtq'>Twilio Console</a>.", 'ultimate-sms-notifications' ),
            ) );
            echo  '<br/>' ;
            homescript_input_fields( 'woo_usn_twilio_auth_token', array(
                'type'        => 'text',
                'label'       => '<strong>' . __( '  Auth Token :  ', 'ultimate-sms-notifications' ) . '</strong>',
                'input_class' => array( 'woousn-text-customs-api' ),
                'placeholder' => 'YYYYYYYYYYYYYYYY',
                'required'    => true,
                'default'     => esc_attr( get_option( 'woo_usn_twilio_auth_token' ) ),
                'description' => __( "You can retrieve it from your <a href='https://www.twilio.com/referral/bc0mtq'>Twilio Console</a>.", 'ultimate-sms-notifications' ),
            ) );
            echo  '<br/>' ;
            homescript_input_fields( 'woo_usn_twilio_phone_number', array(
                'type'        => 'text',
                'label'       => '<strong>' . __( '  Twilio Phone Number :  ', 'ultimate-sms-notifications' ) . '</strong>',
                'input_class' => array( 'woousn-text-customs-api' ),
                'placeholder' => '+1234567890',
                'required'    => true,
                'default'     => esc_attr( get_option( 'woo_usn_twilio_phone_number' ) ),
                'description' => __( "You can retrieve it from your <a href='https://www.twilio.com/referral/bc0mtq'>Twilio Console</a>.", 'ultimate-sms-notifications' ),
            ) );
            _e( "You will need a Account SID and Auth Token in order to use Twilio's API. If you already have an account you can retrieve\n\t\t\tthem from your Twilio Console within the  <a href='www.twilio.com/referral/bc0mtq' target='__blank'>Console</a>. If you have not signed up\n\t\t\tyet, sign up <a href='www.twilio.com/referral/bc0mtq' target='__blank'>here</a>.", 'ultimate-sms-notifications' );
            ?>
				</div>
				<?php 
            do_action( 'woo_usn_options_before_saving_sms_api_fields' );
            ?>
			</div>
			<?php 
        }
        
        $sms_output_html = ob_get_clean();
        // add filter for replace the view displaying api fields.
        $sms_output_html = apply_filters( 'woo_usn_edit_sms_gateways_fields_html', $sms_output_html );
        Woo_Usn_UI_Fields::format_html_fields( $sms_output_html );
        ?>
		<div id="woo_usn_testing_sections" class="wrap">
			<?php 
        submit_button(
            __( 'Save API Credentials', 'ultimate-sms-notifications' ),
            'primary',
            '',
            false,
            array(
            'id' => 'woo_usn_saving',
        )
        );
        echo  '&nbsp;' ;
        submit_button(
            __( 'Delete API Credentials ', 'ultimate-sms-notifications' ),
            'primary',
            '',
            false,
            array(
            'id' => 'woo_usn_deleting',
        )
        );
        ?>
		</div>
		<br/>
		<div class="woousn-cl-loader" style="display: none;"></div>
		<div class="woo_usn_modal_body"></div>
		<span class="woo_usn_panels" id="woo_usn_saving_status" style="display:none;"></span>
		<div id="woo_usn_testing_sections" class="wrap">
			<h3><?php 
        esc_html_e( 'Status', 'ultimate-sms-notifications' );
        ?></h3>
			<?php 
        esc_html_e( 'Send test messages to a number to find out if your API credentials are working properly.', 'ultimate-sms-notifications' );
        ?>
			<br/>

			<br/>
			<?php 
        homescript_input_fields( 'woo_usn_testing_numbers', array(
            'required'    => true,
            'label'       => '<strong>' . __( 'Testing numbers : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woo-usn-testing-numbers', 'woousn-text-customs' ),
        ) );
        homescript_input_fields( 'woo_usn_testing_messages', array(
            'type'        => 'textarea',
            'required'    => true,
            'label'       => '<strong>' . __( 'Testing messages : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-textarea', 'woo-usn-testing-messages' ),
            'placeholder' => __( 'Type your message here.', 'ultimate-sms-notifications' ),
        ) );
        homescript_input_fields( 'woo_usn_testing_status', array(
            'type'        => 'textarea',
            'required'    => true,
            'id'          => 'woo-usn-response-status',
            'input_class' => array( 'woousn-textarea', 'woo-usn-response-status' ),
            'placeholder' => __( 'Type your message here.', 'ultimate-sms-notifications' ),
        ) );
        submit_button(
            __( 'Send Test SMS', 'ultimate-sms-notifications' ),
            'primary',
            '',
            false,
            array(
            'id' => 'woo_usn_testing',
        )
        );
        ?>
		</div>
		<br/>
		<div class="woousn-cl-status" style="display: none;"></div>

		<div class="woousn-body-cl-status">
		</div>
		</div>
		<?php 
    }
    
    /**
     * Display checkbox for sending messages to admin.
     */
    public static function messages_to_admin()
    {
        $options = get_option( 'woo_usn_options' );
        $checked = 0;
        if ( !empty($options['woo_usn_sms_to_admin']) ) {
            $checked = 1;
        }
        homescript_input_fields( 'woo_usn_options[woo_usn_sms_to_admin]', array(
            'type'        => 'checkbox',
            'label'       => __( 'Enable/Disable', 'ultimate-sms-notifications' ),
            'description' => __( '<br/>By enabling it, the shop owner/manager phone number will receive an automated SMS once any purchase is made on his shop.', 'ultimate-sms-notifications' ),
        ), $checked );
    }
    
    /**
     * Setting pending payment orders status by sms.
     */
    public static function messages_from_orders()
    {
        $options = get_option( 'woo_usn_options' );
        $checked = 0;
        if ( !empty($options['woo_usn_messages_from_orders']) ) {
            $checked = 1;
        }
        homescript_input_fields( 'woo_usn_options[woo_usn_messages_from_orders]', array(
            'type'        => 'checkbox',
            'label'       => __( 'Enable/Disable', 'ultimate-sms-notifications' ),
            'description' => __( '<br/>By enabling it, you will be able to send a customized SMS from customer order details.', 'ultimate-sms-notifications' ),
        ), $checked );
    }
    
    /**
     * Display fields where admin put his phone numbers.
     */
    public static function display_shop_admin_numbers()
    {
        $options = get_option( 'woo_usn_options' );
        $display_options = array(
            'type'        => 'text',
            'required'    => true,
            'input_class' => array( 'woousn-text-customs' ),
            'placeholder' => '+1234567890',
        );
        homescript_input_fields( 'woo_usn_options[woo_usn_admin_numbers]', $display_options, ( isset( $options['woo_usn_admin_numbers'] ) ? esc_attr( $options['woo_usn_admin_numbers'] ) : '' ) );
    }
    
    /**
     * Function for setting who allows to send SMS after customer purchase.
     */
    public static function after_customer_purchase()
    {
        $options = get_option( 'woo_usn_options' );
        $checked = 0;
        if ( !empty($options['woo_usn_message_after_customer_purchase']) ) {
            $checked = 1;
        }
        homescript_input_fields( 'woo_usn_options[woo_usn_message_after_customer_purchase]', array(
            'type'        => 'checkbox',
            'label'       => __( 'Enable/Disable', 'ultimate-sms-notifications' ),
            'description' => __( '<br/>By enabling it, an automated SMS will be sent to the customer alongside the WooCommerce email.', 'ultimate-sms-notifications' ),
        ), $checked );
    }
    
    /**
     * Function for setting who allows to send SMS after order changed.
     */
    public static function after_order_changed()
    {
        $options = get_option( 'woo_usn_options' );
        $checked = 0;
        if ( !empty($options['woo_usn_message_after_order_changed']) ) {
            $checked = 1;
        }
        homescript_input_fields( 'woo_usn_options[woo_usn_message_after_order_changed]', array(
            'type'        => 'checkbox',
            'label'       => __( 'Enable/Disable', 'ultimate-sms-notifications' ),
            'description' => __( '<br/>By enabling it, an automatic SMS will be sent to the customer to inform him of the change of status of his order.', 'ultimate-sms-notifications' ),
        ), $checked );
    }
    
    /**
     * Display options tags.
     */
    public static function display_options_tags( $is_vendor = false )
    {
        global  $usn_utility ;
        Woo_Usn_UI_Fields::format_html_fields( '<p> Use these tags to customize your message : </p>' );
        
        if ( !isset( $is_vendor['is_vendor'] ) ) {
            foreach ( $usn_utility::get_list_of_tag_names() as $tag_names => $tag_desc ) {
                Woo_Usn_UI_Fields::format_html_fields( '<strong style="background-color : #5ce1e6;">' . str_replace( '/', '', $tag_names ) . '</strong>' . $tag_desc . '<br/>' );
            }
        } else {
            Woo_Usn_UI_Fields::format_html_fields( '
                <strong style="background-color : #5ce1e6;">%vendor_name%</strong> : Vendor Name <br/> 
                <strong style="background-color : #5ce1e6;">%vendor_products_names%</strong> : Vendor Product Name<br/> 
                <strong style="background-color : #5ce1e6;">%vendor_product_link%</strong> : Vendor Product Link<br/> 
                <strong style="background-color : #5ce1e6;">%vendor_order_id%</strong> : Vendor Order ID<br/> 
                <strong style="background-color : #5ce1e6;">%vendor_order_link%</strong> : Vendor Order Link<br/> 
                
                You can still use, the normal tags listed below for sending SMS to the vendors too.
                ' );
        }
    
    }
    
    /**
     * Display the description for settings tab pages.
     */
    public static function description_for_the_tab_pages()
    {
        esc_html_e( 'You can configure options you want to use into this plugin here.', 'ultimate-sms-notifications' );
        if ( !class_exists( 'WooCommerce' ) ) {
            Woo_Usn_UI_Fields::format_html_fields( '<br/><br/><strong>This options works best with WooCommerce, it is needed in order to use the plugin.</strong>', 'ultimate-sms-notifications' );
        }
    }
    
    /**
     * Display a field for set the messages for changement of order status.
     */
    public static function sms_when_completed()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_completed_messages]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_completed_messages'] ?? '',
        ) );
    }
    
    /**
     * Display a field for set the messages for changement of order status.
     */
    public static function sms_when_processing()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_processing_messages]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_processing_messages'] ?? '',
        ) );
    }
    
    /**
     * Display a field for set the messages for changement of order status.
     */
    public static function sms_when_cancelled()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_cancelled_messages]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_cancelled_messages'] ?? '',
        ) );
    }
    
    /**
     * Display a field for set the messages for changement of order status.
     */
    public static function sms_when_on_hold()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_on_hold_messages]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_on_hold_messages'] ?? '',
        ) );
    }
    
    /**
     * Display a field for set the messages for changement of order status.
     */
    public static function sms_when_pending_payment()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_pending_payment_messages]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_pending_payment_messages'] ?? '',
        ) );
    }
    
    /**
     * Display a field for set the messages for changement of order status.
     */
    public static function sms_when_failed()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_failed_messages]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_failed_messages'] ?? '',
        ) );
    }
    
    /**
     * Display a field for set the messages for changement of order status.
     */
    public static function sms_when_refunded()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_refunded_messages]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_refunded_messages'] ?? '',
        ) );
    }
    
    /**
     * Message who is sent to user when his orders is in pending payment status.
     */
    public static function default_sms_messages()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( ' woo_usn_options[woo_usn_defaults_messages]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woo_usn_messages_to_send', 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_defaults_messages'] ?? '',
        ) );
    }
    
    /**
     * Add SMS Gateway to the list.
     *
     * @param array $api_name SMS Gateway lists.
     *
     * @return array
     */
    public static function add_external_api( $api_name )
    {
        $list = array(
            'Kivalo',
            'Message Bird',
            'SendChamp',
            'AvlyText',
            'Octopush',
            'tyntecsms' => 'Tyntec SMS',
            'fast2sms' => 'Fast2SMS'
        );
        $api_name = array_merge( $api_name, $list );
        return $api_name;
    }
    
    /**
     * Display external SMS Gateway fields.
     *
     * @return void
     */
    public static function add_external_api_fields()
    {
        ?>
		<div id="woo_usn_api_kivalo" class="wrap" data-name="kivalo" style="display:none;">
			<?php 
        Woo_Usn_UI_Fields::format_html_fields( 'Set Kivalo by providing information necessary in the fields below.<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_kivalo_from_number', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Kivalo From Phone number : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'placeholder' => '233xxxxxxx',
            'required'    => true,
            'default'     => esc_attr( get_option( 'woo_usn_kivalo_from_phone_number' ) ),
            'description' => __( "You can retrieve it from your <a href='https://sms.kivalosolutions.com' target='__blank'>Kivalo account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_kivalo_api_key', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Kivalo API Key : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => '000000xxxxxxxxxx',
            'default'     => esc_attr( get_option( 'woo_usn_kivalo_api_key' ) ),
            'description' => __( "You can retrieve it from your <a href='https://sms.kivalosolutions.com' target='__blank'>Kivalo account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( "You will need a Kivalo From Phone Number and API Key in order to use Kivalo’s API. If you already have an account you can retrieve\n\t\tthem from your account dashboard within the  <a href='https://sms.kivalosolutions.com'>Portal</a>. If you have not signed up\n\t\tyet, sign up <a href='https://sms.kivalosolutions.com/signup'>here</a>." );
        ?>
		</div>

		<div id="woo_usn_api_waapi" class="wrap" data-name="kivalo" style="display:none;">
			<?php 
        Woo_Usn_UI_Fields::format_html_fields( 'Set Waapi by providing information necessary in the fields below.<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_waapi_client_id', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Waapi Client ID : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'placeholder' => '0ABCxxxxxxxxxx',
            'required'    => true,
            'default'     => esc_attr( get_option( 'woo_usn_waapi_client_id' ) ),
            'description' => __( "You can retrieve it from your <a href='https://checkout.waapi.co/api/affurl/0elOEJ0M4As1ENZ1n/HLKBxj10HmbMiA8o?target=Vpas2KguYoFAg08o' target='__blank'>Waapi account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_waapi_instance_id', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Waapi Instance ID : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => '000000xxxxxxxxxx',
            'default'     => esc_attr( get_option( 'woo_usn_waapi_instance_id' ) ),
            'description' => __( "You can retrieve it from your <a href='https://checkout.waapi.co/api/affurl/0elOEJ0M4As1ENZ1n/HLKBxj10HmbMiA8o?target=Vpas2KguYoFAg08o' target='__blank'>Waapi account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_waapi_domain_url', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Waapi Domain URL : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => 'https://apiv3.waapi.co',
            'default'     => esc_attr( get_option( 'woo_usn_waapi_domain_url' ) ),
            'description' => __( "You can retrieve it from your <a href='https://checkout.waapi.co/api/affurl/0elOEJ0M4As1ENZ1n/HLKBxj10HmbMiA8o?target=Vpas2KguYoFAg08o' target='__blank'>Waapi account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( "You will need a Waapi Client ID and Instance ID in order to send Whatsapp messages. If you already have an account you can retrieve\n\t\tthem from your account <a href='https://checkout.waapi.co/api/affurl/0elOEJ0M4As1ENZ1n/HLKBxj10HmbMiA8o?target=Vpas2KguYoFAg08o' target='__blank'>dashboard</a>  or watching this video <a href ='https://www.youtube.com/watch?v=zoaGETg0eZY'>here</a> to see how to do." );
        ?>
		</div>
		<div id="woo_usn_api_messagebird" class="wrap" data-name="messagebird" style="display:none;">
			<?php 
        Woo_Usn_UI_Fields::format_html_fields( 'Set Message Bird by providing information necessary in the fields below.<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_message_bird_from_number', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Message Bird Originator : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'placeholder' => 'My Shop',
            'required'    => true,
            'default'     => esc_attr( get_option( 'woo_usn_message_bird_from_number' ) ),
            'description' => __( "You can retrieve it from your <a href='https://dashboard.messagebird.com/en/developers/settings' target='__blank'>Message Bird account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_message_bird_api_key', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Message Bird API Key : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => 'ABCDEFGxxxxxxxxxx',
            'default'     => esc_attr( get_option( 'woo_usn_message_bird_api_key' ) ),
            'description' => __( "You can retrieve it from your <a href='https://dashboard.messagebird.com/en/developers/settings' target='__blank'>Message Bird account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( "You will need a Message Bird <a href='https://dashboard.messagebird.com/en/developers/settings' target='__blank'> API Key </a> and <a href='https://support.messagebird.com/hc/en-us/articles/115002628665-What-is-the-originator-#:~:text=An%20originator%20is%20the%20name,which%20it%20has%20been%20sent'> Originator </a> in order to send SMS. " );
        ?>
		</div>
		<div id="woo_usn_api_sendchamp" class="wrap" data-name="sendchamp" style="display:none;">
			<?php 
        Woo_Usn_UI_Fields::format_html_fields( 'Set SendChamp by providing information necessary in the fields below.<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_sendchamp_domain_url', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'SendChamp Domain URL : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => 'https://api.sendchamp.com/api/v1/sms/send/',
            'default'     => esc_attr( get_option( 'woo_usn_sendchamp_domain_url', 'https://api.sendchamp.com/api/v1/sms/send/' ) ),
            'description' => __( "You can retrieve it from your <a href='https://my.sendchamp.com/accountSettings' target='__blank'>SendChamp account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_sendchamp_from_number', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'SendChamp Channel ID : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'placeholder' => 'My Shop',
            'required'    => true,
            'default'     => esc_attr( get_option( 'woo_usn_sendchamp_from_number' ) ),
            'description' => __( "You can retrieve it from your <a href='https://my.sendchamp.com/' target='__blank'>SendChamp account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields( 'woo_usn_sendchamp_api_key', array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'SendChamp API Key : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => 'ABCDEFGxxxxxxxxxx',
            'default'     => esc_attr( get_option( 'woo_usn_sendchamp_api_key' ) ),
            'description' => __( "You can retrieve it from your <a href='https://my.sendchamp.com/' target='__blank'>SendChamp account</a>.", 'ultimate-sms-notifications' ),
        ) );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( "You will need a SendChamp <a href='https://my.sendchamp.com/' target='__blank'> API Key </a> and <a href='https://support.sendchamp.com/article/13-a-guide-on-how-to-get-your-api-keys' target='__blank'> Channel ID </a> in order to send SMS. " );
        ?>
		</div>
		<div id="woo_usn_api_avlytext" class="wrap" data-name="avlytext" style="display:none;">
		<?php 
        $api_choosed = get_option( 'woo_usn_api_choosed' );
        Woo_Usn_UI_Fields::format_html_fields( 'Set AvlyText by providing information necessary in the fields below.<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        $woo_usn_creds = get_option( 'woo_usn_creds', false );
        $first_data = '';
        $second_data = '';
        
        if ( 'AvlyText' == $api_choosed ) {
            $first_data = $woo_usn_creds['first'];
            $second_data = $woo_usn_creds['second'];
        }
        
        homescript_input_fields(
            'woo_usn_creds[avlytext][first]',
            array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'AvlyText API Key : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => 'dMoSJgddqOQyB1tir3cnk5jm2eDNzezesbgpvZ8Knwd58ZDU1FlSTClJgaZupwr4K00',
            'description' => __( "You can retrieve it from your <a href='https://www.avlytext.com/en/login' target='__blank'>AvlyText account</a>.", 'ultimate-sms-notifications' ),
        ),
            esc_attr( $first_data ),
            true
        );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields(
            'woo_usn_creds[avlytext][second]',
            array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'AvlyText Sender Name : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'placeholder' => 'My Shop',
            'required'    => true,
            'default'     => esc_attr( $second_data ),
            'description' => __( 'Define the name of the sender you would like to have displayed when sending SMS.', 'ultimate-sms-notifications' ),
        ),
            esc_attr( $second_data ),
            true
        );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        ?>
		</div>
		<div id="woo_usn_api_octopush" class="wrap" data-name="octopush" style="display:none;">
		<?php 
        $api_choosed = get_option( 'woo_usn_api_choosed' );
        Woo_Usn_UI_Fields::format_html_fields( 'Set Octopush by providing information necessary in the fields below.<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        $woo_usn_creds = get_option( 'woo_usn_creds', false );
        $first_data = '';
        $second_data = '';
        
        if ( 'Octopush' == $api_choosed ) {
            $first_data = $woo_usn_creds['first'];
            $second_data = $woo_usn_creds['second'];
        }
        
        homescript_input_fields(
            'woo_usn_creds[octopush][first]',
            array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Octopush API Key : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => 'dMoSJgddqOQyB1tir3cnk5jm2eDNzezesbgpvZ8Knwd58ZDU1FlSTClJgaZupwr4K00',
            'description' => __( "You can retrieve it from your <a href='https://client.octopush.com/api-credentials' target='__blank'>Octopush credentials account page</a>.", 'ultimate-sms-notifications' ),
        ),
            $first_data,
            true
        );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields(
            'woo_usn_creds[octopush][second]',
            array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Octopush Login : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'placeholder' => 'noreply@ultimatesmsnotifications.com',
            'required'    => true,
            'description' => __( "You can retrieve it from your <a href='https://client.octopush.com/api-credentials' target='__blank'>Octopush credentials account page</a>.", 'ultimate-sms-notifications' ),
        ),
            $second_data,
            true
        );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        ?>
		</div>
		<div id="woo_usn_api_tyntecsms" class="wrap" data-name="tyntecsms" style="display:none;">
		<?php 
        $api_choosed = get_option( 'woo_usn_api_choosed' );
        Woo_Usn_UI_Fields::format_html_fields( 'Set Tyntec SMS by providing information necessary in the fields below.<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        $woo_usn_creds = get_option( 'woo_usn_creds', false );
        $first_data = '';
        $second_data = '';
        
        if ( 'tyntecsms' == $api_choosed ) {
            $first_data = $woo_usn_creds['first'];
            $second_data = $woo_usn_creds['second'];
        }
        
        homescript_input_fields(
            'woo_usn_creds[tyntecsms][first]',
            array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Tyntec SMS API Key : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => 'UiuUezeMTX7chPtTDJPY7vNBQM4hPOOaz',
            'description' => __( "You can retrieve it from your <a href='https://my.tyntec.com/api-settings' target='__blank'>Tyntec SMS credentials account page</a>.", 'ultimate-sms-notifications' ),
        ),
            esc_attr( $first_data ),
            true
        );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        homescript_input_fields(
            'woo_usn_creds[tyntecsms][second]',
            array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Tyntec SMS Sender Name : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'placeholder' => 'My Shop',
            'required'    => true,
            'description' => __( 'Define the SMS Sender Name.', 'ultimate-sms-notifications' ),
        ),
            esc_attr( $second_data ),
            true
        );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        ?>
		</div>

		<div id="woo_usn_api_fast2sms" class="wrap" data-name="fast2sms" style="display:none;">
		<?php 
        $api_choosed = get_option( 'woo_usn_api_choosed' );
        Woo_Usn_UI_Fields::format_html_fields( 'Set Fast2SMS by providing information necessary in the fields below.<br/>' );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        $woo_usn_creds = get_option( 'woo_usn_creds', false );
        $first_data = '';
        $second_data = '';
        
        if ( 'fast2sms' == $api_choosed ) {
            $first_data = $woo_usn_creds['first'];
            $second_data = $woo_usn_creds['second'];
        }
        
        homescript_input_fields(
            'woo_usn_creds[fast2sms][first]',
            array(
            'type'        => 'text',
            'label'       => '<strong>' . __( 'Fast2SMS API Key : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-text-customs-api' ),
            'required'    => true,
            'placeholder' => 'UiuUezeMTX7chPtTDJPY7vNBQM4hPOOaz',
            'description' => __( "You can retrieve it from your <a href='https://www.fast2sms.com/dashboard/dev-api' target='__blank'>Tyntec SMS credentials account page</a>.", 'ultimate-sms-notifications' ),
        ),
            esc_attr( $first_data ),
            true
        );
        Woo_Usn_UI_Fields::format_html_fields( '<br/>' );
        ?>
		</div>


		<?php 
    }
    
    /**
     * This allows to send SMS from the dashboard.
     *
     * @return void
     */
    public static function send_sms()
    {
        ?>
		<div>
			<h3><?php 
        Woo_Usn_UI_Fields::format_html_fields( 'Send a Quick SMS' );
        ?></h3>
			<br/>
			<?php 
        
        if ( isset( $_GET['page'] ) && 'ultimate-sms-notifications-send-sms' === $_GET['page'] ) {
            ?>
						<span><?php 
            echo  esc_html__( 'Send a Quick SMS to a relative, family, customer in less than a second from your WordPress dashboard.', 'ultimate-sms-notifications' ) ;
            ?></span>
					<?php 
        }
        
        ?>
			<br/>
			<br/>
		</div>
		<div id="sms-block">
			<?php 
        $sms_options_list = apply_filters( 'woo_usn_qs_selection_mode', array(
            'use-phone-number'         => __( 'Using Phone Number', 'ultimate-sms-notifications' ),
            'use-contact-list-premium' => wp_kses_post( '<span>' . __( 'Using Contact List ( Pro Feature )', 'ultimate-sms-notifications' ) . ' ' . wp_sprintf( "<a href='%s'>%s</a>", admin_url( 'admin.php?page=ultimate-sms-notifications-pricing' ), __( 'Click here to upgrade', 'ultimate-sms-notifications' ) ) . '</span>' ),
        ) );
        ?>
				<?php 
        homescript_input_fields( 'woo_usn_qs_pn', array(
            'type'        => 'radio',
            'required'    => true,
            'label'       => '<strong>' . __( 'Recipient Selection Mode : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-textarea', 'woo-usn-testing-messages' ),
            'options'     => $sms_options_list,
            'default'     => 'use-phone-number',
        ) );
        ?>
					<br/>
					<br/>
			
			<div class="woo-usn-use-phone-number woo-usn-use-contact-list-premium woo-usn-qs-class" >
			<?php 
        homescript_input_fields( 'woo_usn_testing_numbers', array(
            'required'    => true,
            'label'       => '<strong>' . __( 'Enter Phone Number : ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woo-usn-testing-numbers', 'woousn-text-customs' ),
        ) );
        ?>
				</div>
				<div class="woo-usn-use-contact-list  woo-usn-qs-class" style='display:none;'>
					<?php 
        ?>
				</div>
				<br/>
				<br/>
			<?php 
        homescript_input_fields( 'woo_usn_testing_messages', array(
            'type'        => 'textarea',
            'required'    => true,
            'label'       => '<strong>' . __( 'Message to send: ', 'ultimate-sms-notifications' ) . '</strong>',
            'input_class' => array( 'woousn-textarea', 'woo-usn-testing-messages' ),
            'placeholder' => __( 'Type your message here.', 'ultimate-sms-notifications' ),
        ) );
        homescript_input_fields( 'woo_usn_testing_status', array(
            'type'        => 'textarea',
            'required'    => true,
            'id'          => 'woo-usn-response-status',
            'input_class' => array( 'woousn-textarea', 'woo-usn-response-status' ),
            'placeholder' => __( 'Type your message here.', 'ultimate-sms-notifications' ),
        ) );
        
        if ( isset( $_GET['page'] ) && 'ultimate-sms-notifications-send-sms' === $_GET['page'] ) {
            $sms_message_text = __( 'Try SMS Sending', 'ultimate-sms-notifications' );
        } else {
            $sms_message_text = __( 'Send Test SMS', 'ultimate-sms-notifications' );
        }
        
        submit_button(
            $sms_message_text,
            'primary',
            '',
            false,
            array(
            'id' => 'woo_usn_testing',
        )
        );
        ?>
		</div>
		<br/>
		<div class="woousn-cl-status" style="display: none;"></div>

		<div class="woousn-body-cl-status">
		</div>
		</div>
		<?php 
    }
    
    public static function after_customer_sign()
    {
        $options = get_option( 'woo_usn_options' );
        $checked = 0;
        if ( !empty($options['woo_usn_messages_after_customer_signup']) ) {
            $checked = 1;
        }
        homescript_input_fields( 'woo_usn_options[woo_usn_messages_after_customer_signup]', array(
            'type'        => 'checkbox',
            'label'       => __( 'Enable/Disable', 'ultimate-sms-notifications' ),
            'description' => __( '<br/>By enabling it, an automated SMS will be sent to the new customer.', 'ultimate-sms-notifications' ),
        ), $checked );
    }
    
    public static function default_signup_message()
    {
        $options = get_option( 'woo_usn_options' );
        homescript_input_fields( 'woo_usn_options[woo_usn_defaults_customer_signup]', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woousn-textarea' ),
            'placeholder' => __( 'Please put the default sms to send.', 'ultimate-sms-notifications' ),
            'default'     => $options['woo_usn_defaults_customer_signup'] ?? '',
        ) );
    }
    
    public static function display_customer_signup_tags()
    {
        Woo_Usn_UI_Fields::format_html_fields( '<p> Use these tags to customize your message : </p>' );
        Woo_Usn_UI_Fields::format_html_fields( '<strong style="background-color : #5ce1e6;">%store_name%</strong> : Store Name <br/> <strong style="background-color : #5ce1e6;">%customer_name%</strong> : Customer Name  <br/> <strong style="background-color : #5ce1e6;">%customer_phone_number%</strong> : Customer Phone Number <br/>' );
    }
    
    /**
     * Display metabox for sending message from the orders.
     */
    public static function message_from_orders_metabox()
    {
        $options = get_option( 'woo_usn_options' );
        if ( isset( $options['woo_usn_messages_from_orders'] ) && 1 == $options['woo_usn_messages_from_orders'] ) {
            add_meta_box(
                'woo_usn_send_messages',
                __( 'Send SMS', 'ultimate-sms-notifications' ),
                __CLASS__ . '::message_box_for_orders',
                'shop_order',
                'side',
                'high'
            );
        }
    }
    
    /**
     * Display a message box who allows shop owner/manager to send SMS
     * directly from customer orders.
     *
     * @param object $order_id WooCommerce Order ID.
     */
    public static function message_box_for_orders( $order_id )
    {
        $order = new WC_Order( $order_id );
        $id = $order->get_id();
        $order_status = $order->get_status();
        homescript_input_fields( 'woo_usn_messages_to_send', array(
            'type'        => 'textarea',
            'required'    => true,
            'input_class' => array( 'woo_usn_messages_to_send', 'woousn-textarea' ),
            'placeholder' => __( 'Type your message here.', 'ultimate-sms-notifications' ),
            'maxlength'   => 160,
        ) );
        ?>
		<input type="submit" name="woo_usn_sms_submit" id="woo_usn_sms_submit" class="button button-primary" value="<?php 
        esc_html_e( 'Send', 'ultimate-sms-notifications' );
        ?>" style="width:80px; word-wrap: break-word;">
		<br/>
		<br/>
		<textarea id="phone_number" class="woousn-textarea" maxlength='160' order_id='<?php 
        echo  esc_attr( $id ) ;
        ?>' order_status='<?php 
        echo  esc_attr( $order_status ) ;
        ?>' rows="5" style="display : none; height:83px; width : 254px;" readonly></textarea>
		<br/>
		<div class="woousn-cl-loader" style="display: none;"></div>
		<?php 
    }
    
    public static function get_un_reasons( $reasons )
    {
        // Woo_Usn_Utility::log_errors( print_r( $reasons, true ));
        return $reasons;
    }

}