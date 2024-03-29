<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Woo_Usn_Subscribers extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => __( 'Subscriber', 'ultimate-sms-notifications' ), // singular name of the listed records
				'plural'   => __( 'Subscribers', 'ultimate-sms-notifications' ), // plural name of the listed records
				'ajax'     => false, // does this table support ajax?
			)
		);

		add_action(
			'admin_enqueue_scripts',
			function() {
				wp_enqueue_script( 'fontawesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.0/js/fontawesome.min.js', array(), uniqid(), true );
			}
		);

	}

	/**
	 * Check if subscribers table exist or not.
	 */
	public static function check_if_table_exists() {
		global $wpdb;
		$sql = "SHOW TABLES LIKE '{$wpdb->prefix}_woousn_subscribers_list'";

		$query = $wpdb->get_results( $sql, 'ARRAY_A' );
		$table_name      = $wpdb->prefix . '_woousn_subscribers_list';

		if ( ! function_exists('dbDelta') ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }


		if ( count( $query ) < 1 ) {
			global $woo_usn_db_subscribers_version;
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (id mediumint(20) NOT NULL AUTO_INCREMENT,customer_id mediumint(255) NOT NULL UNIQUE,customer_consent text NOT NULL,customer_registered_page text NOT NULL,date datetime DEFAULT '2022-12-12 00:00:00' NOT NULL,PRIMARY KEY  (id)) $charset_collate;";
			dbDelta( $sql );
			add_option( 'woo_usn_subscribers_db_version', $woo_usn_db_subscribers_version );
		}

		$column_sql = "SHOW COLUMNS from `$table_name` LIKE 'customer_order_id';";
		$query = $wpdb->get_results( $column_sql, 'ARRAY_A' );

		if ( count( $query ) < 1 ) {
			$sql = "ALTER TABLE `$table_name` ADD COLUMN customer_order_id VARCHAR(255) AFTER customer_registered_page;";
			dbDelta( $sql );
		}		

		$column_sql = "SHOW INDEX FROM `$table_name`";
		$query = $wpdb->get_results( $column_sql, 'ARRAY_A' );
		
		if ( in_array( 'customer_id', array_column( $query, 'Table' ) )  ) {
			$sql = "ALTER TABLE `$table_name` DROP INDEX customer_id;";
			dbDelta( $sql );
		}
	}


	/**
	 * Retrieve customers data from the database.
	 *
	 * @param mixed $per_page
	 * @param mixed $page_number
	 * @param mixed $pagination_args
	 *
	 * @return array|object|stdClass[]|null
	 */
	public static function get_customers( $per_page = 5, $page_number = 1, $pagination_args = false ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}_woousn_subscribers_list";

		if ( $pagination_args ) {
			$sql .= ' ORDER BY date DESC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public static function get_customer_choices( $choice = 'on' ) {

		global $wpdb;

		$sql = "SELECT customer_id FROM {$wpdb->prefix}_woousn_subscribers_list where customer_consent LIKE '%" . $choice . "%'";

		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	public static function get_orders_ids( $choice = 'on' ) {
		global $wpdb;

		$sql = "SELECT customer_order_id FROM {$wpdb->prefix}_woousn_subscribers_list where customer_consent LIKE '%" . $choice . "%'";

		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}_woousn_subscribers_list",
			array( 'ID' => $id ),
			array( '%d' )
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}_woousn_subscribers_list";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		esc_html_e( 'No logs avaliable.', 'ultimate-sms-notifications' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array  $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'customer_id':
				if ( $item['customer_id'] == 0 ) {
					$order_id    = $item['customer_order_id'];
					if ( $order_id ) {
						$order_obj = wc_get_order( $order_id );
						$fname = $order_obj->get_billing_first_name() . ' ' . $order_obj->get_billing_last_name();
						echo wp_kses_post( '<a href="' . admin_url( "post.php?post=$order_id&action=edit" ) . '">' . $fname . '</a>' );
					} else {
						echo "Guest Customer with missing details";
					}
				} else {
					$customer_id = $item['customer_id'];
					$customer    = new WC_Customer( $customer_id );
					$fname       = $customer->get_billing_first_name() . ' ' . $customer->get_billing_last_name();
					echo wp_kses_post( '<a href="' . admin_url( 'user-edit.php?user_id=' . $customer_id ) . '">' . $fname . '</a>' );
				}
				
				break;

			case 'customer_registered_page':
				echo esc_attr( ucfirst( $item['customer_registered_page'] ) );
				break;

			case 'customer_consent':
				$consent = $item['customer_consent'];
				if ( 'on' == $consent ) {
					echo '' . esc_html__( 'Subscribed', 'ultimate-sms-notifications' );
				} else {
					echo '' . esc_html__( 'Unsubscribed', 'ultimate-sms-notifications' );
				}

				break;

			case 'order_related':
				$order_id    = $item['customer_order_id'];
				if ( $order_id ) {
					$order_obj = wc_get_order( $order_id );
					$fname = $order_obj->get_billing_first_name() . ' ' . $order_obj->get_billing_last_name();
					echo wp_kses_post( '<a href="' . admin_url( "post.php?post=$order_id&action=edit" ) . '">' . $fname . '</a>' );
				}
				break;

			case 'date':
				echo wp_date( 'm/d/Y H:i:s', strtotime( $item['date'] ) );
				break;

			default:
				return print_r( $item, true );
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />',
			$item['id']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'woo_usn_sms_logs_delete_nonce' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&_woousn_sms_logs=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce ),
		);

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'                       => '<input type="checkbox" />',
			'customer_id'              => __( 'Customer Name', 'ultimate-sms-notifications' ),
			'customer_registered_page' => __( 'Customer Registration Page', 'ultimate-sms-notifications' ),
			'customer_consent'         => __( 'Customer consent', 'ultimate-sms-notifications' ),
			'order_related'            => __( 'Order Related', 'ultimate-sms-notifications' ),
			'date'                     => __( 'Date', 'ultimate-sms-notifications' ),
		);

		return $columns;
	}


	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete',
		);

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		self::check_if_table_exists();

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'sms_logs_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // WE have to calculate the total number of items.
				'per_page'    => $per_page, // WE have to determine how many items to show on a page.
			)
		);	

		$this->items = self::get_customers( $per_page, $current_page, true );
	}

	public function process_bulk_action() {

		// Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'woo_usn_sms_logs_delete_nonce' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				if ( isset( $_GET['customer'] ) ) {
					self::delete_customer( absint( $_GET['customer'] ) );
					wp_safe_redirect( esc_url_raw( add_query_arg() ) );
					exit;
				}
			}
		}

		// If the delete bulk action is triggered.
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
			 || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = filter_input_array( INPUT_POST );
			// loop over the array of record IDs and delete them.
			foreach ( $delete_ids['bulk-delete'] as $id ) {
				self::delete_customer( $id );

			}
			wp_safe_redirect( esc_url_raw( add_query_arg() ) );
			exit;
		}
	}
}

class Woo_Usn_Subscribers_Loader {

	// class instance.
	static $instance;

	// customer WP_List_Table object.
	public $customers_obj;

	// class constructor.
	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
	}


	public static function set_screen( $status, $option, $value ) {
		return $value;
	}


	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		?>
		<div class="wrap">
			<h2>Subscribers</h2>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post" class="woousn-custom-tables">
								<?php
								$this->customers_obj->prepare_items();
								$this->customers_obj->display();
								?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = array(
			'label'   => 'Subscribers',
			'default' => 7,
			'option'  => 'sms_logs_per_page',
		);

		add_screen_option( $option, $args );

		$this->customers_obj = new Woo_Usn_Subscribers();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}


add_action(
	'plugins_loaded',
	function () {
		Woo_Usn_Subscribers_Loader::get_instance();
	}
);
