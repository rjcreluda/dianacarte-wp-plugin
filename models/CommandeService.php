<?php 

/**
 * Class for commanded service
 */
class CommandeService
{
	private static $table_name = 'commande_service';
	private $fields = array('service_id', 'commande_id', 'qty');

	function __construct( $data = array() )
	{

		if( is_array($data) && !empty($data) ){
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
		else{
			foreach ( $this->fields as $field ) {
				$this->$field = '';
			}
		}
	}

	public function save()
	{
		if( !isset($this->data) )
			return;
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$format = [ '%d', '%d', '%d' ];
		$wpdb->insert( $table_name, $this->data, $format );
	}

	public static function init()
	{
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$table_name = $wpdb->prefix . self::$table_name;
		if($wpdb->get_var("show tables like '$table_name'")  != $table_name){
			$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			service_id mediumint(9) NOT NULL,
			commande_id mediumint(9) NOT NULL,
			qty mediumint(9) NOT NULL,
			UNIQUE KEY id (id)
			);";
			dbDelta( $sql );
		}
	}

	/** 
   * Get list by command id
   * @param int $id: command id
   * @return Object results
   */
	public static function get($id = null)
	{
		if( !is_int($id) )
			return;
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$query = "SELECT * FROM $table_name WHERE commande_id = %d";
		$safe_query = $wpdb->prepare( $query, $id );
		return $wpdb->get_results( $safe_query ); // return object
	}

	/** 
   * Replace ids properties with object value
   * @param int $commandes: service command list
   * @return arrat $data
   */
  public static function formattedText( $commandes )
  {
  	$data = array();
  	foreach( $commandes as $cmd ){
  		$data[] = array(
  			'id' => $cmd->id,
  			'service' => new Service( (int) $cmd->service_id ),
  			'qty' => $cmd->qty,
  		);
  	}
  	return $data;
  }
}