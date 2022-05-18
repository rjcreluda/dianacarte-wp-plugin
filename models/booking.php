<?php

class Booking{

	public function __construct()
	{
		// booking listing
		add_action('wp_ajax_nopriv_citybook_addons_booking_listing', 'booking_listing_callback');
		add_action('wp_ajax_citybook_addons_booking_listing', 'booking_listing_callback');
	}

	public function booking_listing_callback(){
		$nonce = $_POST['_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'citybook-add-ons' ) ){
	        $json['success'] = false;
	        $json['data']['error'] = esc_html__( 'Security checked!, Cheatn huh?', 'citybook-add-ons' ) ;
	        wp_send_json($json );
	    }
	    $rooms = $_POST['rooms'];
	}


	public static function create_custom_table()
	{
		$table_name = 'booking';
		// creating database tables
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$booking_table = $wpdb->prefix.$table_name;
		$booking_item_table = $wpdb->prefix.$table_name.'_item';

		if($wpdb->get_var("show tables like '$booking_table'")  != $booking_table){
			$sql = "CREATE TABLE $booking_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			client VARCHAR(255) DEFAULT '' NOT NULL,
			check_in datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			check_out datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			booking_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			total_amount mediumint(9) NOT NULL,
			UNIQUE KEY id (id)
			);";
			dbDelta( $sql );
		}

		if($wpdb->get_var("show tables like '$booking_item_table'")  != $booking_item_table){
			$sql = "CREATE TABLE $booking_item_table (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			booking_id mediumint(9) NOT NULL,
			item_id mediumint(9) NOT NULL,
			item_type VARCHAR(20) DEFAULT '' NOT NULL,
			item_qty mediumint(9) NOT NULL,
			item_price mediumint(9) NOT NULL,
			UNIQUE KEY id (id)
			);";
			dbDelta( $sql );
		}

		// 
	}
}