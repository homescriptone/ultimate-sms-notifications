<?php

/**
 * This class handle all the custom post types registered with the plugins.
 */
class Woo_Usn_CPT
{
    public static function init()
    {
    }
    
    /**
     * This function add metabox in other to use it with the plugin.
     *
     * @return void
     */
    public static function add_metabox()
    {
    }
    
    /**
     *
     * This function give the ability to easily create the contact list who
     * will help to send the bulk SMS.
     *
     * @param mixed $customer_list WP Post ID.
     * @return void
     */
    public static function compose_customer_list( $customer_list )
    {
    }
    
    /**
     * This function save the customer list data to the db.
     *
     * @param int $post_id Customer List ID.
     *
     * @return void
     */
    public static function save_customer_list( $post_id )
    {
    }

}