<?php 

/**
 * Booked room item
 */
class RoomItemBooking
{
	private $fields = array('booking_id', 'item_id', 'item_type', 'item_qty', 'item_price');
	private $table_name = 'wp_booking_item';
	function __construct($data)
	{
		if( is_array($data) ){
			$wrong_data = false;
			foreach ($this->fields as $field) {
				if( !array_key_exists($field, $data) ){
					$wrong_data = true;
				}
			}
			if( !$wrong_data ){
				$this->data = $data;
			}
		}
	}
	public function save(){
		if( !isset($this->data) )
			return;
		global $wpdb;
		$format = [ '%d', '%d', '%s', '%d', '%d' ];
		$wpdb->insert( $this->table_name , $this->data, $format );
	}
}