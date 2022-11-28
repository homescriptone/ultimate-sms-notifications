<?php

class Woo_Usn_Admin_Menu
{
    /**
     * This function add menus to WP Dashboard.
     *
     * @return void
     */
    public static function add_menus()
    {
        global  $submenu ;
        add_menu_page(
            __( 'SMS Notifications', 'ultimate-sms-notifications' ),
            __( 'SMS Notifications', 'ultimate-sms-notifications' ),
            'manage_options',
            'ultimate-sms-notifications',
            array( 'Woo_Usn_Admin_Settings', 'configure_woo_usn_settings' ),
            plugins_url( 'img/usn.svg', __FILE__ ),
            57
        );
        add_submenu_page(
            'ultimate-sms-notifications',
            __( 'Send a Quick SMS', 'ultimate-sms-notifications' ),
            __( 'Send a Quick SMS', 'ultimate-sms-notifications' ),
            'manage_options',
            'ultimate-sms-notifications-send-sms',
            array( 'Woo_Usn_Admin_Settings', 'send_sms' )
        );
        $submenu['ultimate-sms-notifications'][0][0] = __( 'SMS Settings', 'ultimate-sms-notifications' );
        $submenu['ultimate-sms-notifications'] = array_reverse( $submenu['ultimate-sms-notifications'] );
        add_submenu_page(
            'ultimate-sms-notifications',
            __( 'Contact Lists', 'ultimate-sms-notifications' ),
            __( 'Contact Lists', 'ultimate-sms-notifications' ),
            'manage_options',
            'edit.php?post_type=woo_usn-list',
            false
        );
        $submenu['ultimate-sms-notifications'][11] = $submenu['ultimate-sms-notifications'][2];
        unset( $submenu['ultimate-sms-notifications'][2] );
        $submenu['ultimate-sms-notifications'][13] = array( '<div >' . __( 'WhatsApp Settings', 'ultimate-sms-notifications' ) . '</div>', 'manage_options', admin_url( 'admin.php?page=ultimate-sms-notifications&tab=whatsapp-api' ) );
        $sub_log_loader = new Woo_Usn_Subscribers_Loader();
        $subhook = add_submenu_page(
            'ultimate-sms-notifications',
            __( 'Subscribers', 'ultimate-sms-notifications' ),
            __( 'Subscribers', 'ultimate-sms-notifications' ),
            'manage_options',
            'ultimate-sms-notifications-subscribers',
            array( $sub_log_loader, 'plugin_settings_page' )
        );
        add_action( "load-{$subhook}", array( $sub_log_loader, 'screen_option' ) );
        $submenu['ultimate-sms-notifications'][20] = array( '<div class="woo-usn-links">' . __( 'Documentations', 'ultimate-sms-notifications' ) . '</div>', 'manage_options', 'https://docs.ultimatesmsnotifications.com/?utm_source=' . get_site_url() );
        $submenu['ultimate-sms-notifications'][22] = array( '<div class="woo-usn-links">' . __( 'Knowledge Base', 'ultimate-sms-notifications' ) . '</div>', 'manage_options', 'https://help.ultimatesmsnotifications.com/?utm_source=' . get_site_url() );
        $submenu['ultimate-sms-notifications'][23] = array( '<div class="woo-usn-links">' . __( 'Contact us', 'ultimate-sms-notifications' ) . '</div>', 'manage_options', 'https://chatting.page/homescriptone?utm_source=' . get_site_url() );
        $first_menu = $submenu['ultimate-sms-notifications'][0];
        $new_menu = array( '<div>' . __( 'Logs', 'ultimate-sms-notifications' ) . '</div>', 'manage_options', admin_url( 'admin.php?page=ultimate-sms-notifications-pricing' ) );
        $bm = array( $first_menu, $new_menu );
        $setting_menu = array_slice( $submenu['ultimate-sms-notifications'], 1 );
        $submenu['ultimate-sms-notifications'] = array_merge_recursive( $bm, $setting_menu );
        $submenu['ultimate-sms-notifications'][3][2] = admin_url( 'admin.php?page=ultimate-sms-notifications-pricing' );
        $submenu['ultimate-sms-notifications'][4][2] = admin_url( 'admin.php?page=ultimate-sms-notifications-pricing' );
    }

}