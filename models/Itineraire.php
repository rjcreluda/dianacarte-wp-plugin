<?php 

/**
 * Class for commanded service
 */
class Itineraire
{
  public static $table_name = 'itineraire';
  private $fields = array('tour_id','place_id', 'hotel_id', 'resto_id', 'vehicle_id' );

  function __construct( $data = null )
  {
    if( is_array($data) && !empty($data) ){
      $wrong_data = false;
      foreach ($this->fields as $field) {
        if( !array_key_exists($field, $data) ){
          $wrong_data = true;
        }
        $this->$field = $data[$field];
        if( array_key_exists('id', $data) )
          $this->id = $data['id'];
      }
      if( !$wrong_data ){
        $this->data = $data;
      }
    }
    else if( is_int($data) ){
      $this->load($data);
    }
  }

  private function load($id)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::$table_name;
    $query = "SELECT * FROM $table_name WHERE id = %d";
    $safe_query = $wpdb->prepare( $query, $id );
    $data = $wpdb->get_results( $safe_query );
    if( isset($data[0]) ){
      foreach ($data[0] as $key => $value) {
        $this->$key = $value;
      }
    }
  }

  /** 
   * Save new entry
   * @param null
   * @return $state: integer success or not
   */
  public function save()
  {
    if( !isset($this->data) )
      return;
    global $wpdb;
    $table_name = $wpdb->prefix . self::$table_name;
    $format = [ '%d', '%d', '%d', '%d', '%d' ];
    $state = $wpdb->insert( $table_name, $this->data, $format );
    return $state;
  }

  public function update()
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::$table_name;
    $data = array();
    foreach ($this->data as $key => $value) {
      $data[$key] = $value;
    }
    $where = [ 'id' => $this->id ]; 
    $data = $wpdb->update( $table_name, $data, $where );
    return $data;
  }

  public function delete()
  {
    global $wpdb;
    $table_name = $wpdb->prefix . self::$table_name;
    $query = "DELETE from $table_name 
          WHERE id = %d";
    $safe_query = $wpdb->prepare( $query, $this->id  );
    return $wpdb->query( $safe_query );
  }

  public static function init()
  {
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $table_name = $wpdb->prefix . self::$table_name;
    if($wpdb->get_var("show tables like '$table_name'")  != $table_name){
      $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      tour_id mediumint(9) NOT NULL,
      place_id mediumint(9) NOT NULL,
      hotel_id mediumint(9),
      resto_id mediumint(9),
      vehicle_id mediumint(9),
      UNIQUE KEY id (id)
      );";
      dbDelta( $sql );
    }
  }

  /** 
   * Get list of itinerary for a particular tour
   * @param $tour_id: integer
   * @return Object: array
   */
  public static function get($tour_id = null, $embed = false)
  {
    if( !is_int($tour_id) )
      return;
    global $wpdb;
    $table_name = $wpdb->prefix . self::$table_name;
    $query = "SELECT * FROM $table_name WHERE tour_id = %d";
    $safe_query = $wpdb->prepare( $query, $tour_id );
    $data = $wpdb->get_results( $safe_query );
    if( !$embed )
      return $data; // return object
    // Embeding data
    $itineraries = array();
    foreach( $data as $it ){
      $obj = new stdClass();
      $obj->tour_id = $it->tour_id;
      $obj->place = get_term($it->place_id, 'listing_location');
      $obj->hotel = $it->hotel_id ? new Listing($it->hotel_id) : false;
      $obj->resto = $it->resto_id ? new Listing($it->resto_id) : false;
      $obj->vehicle = $it->vehicle_id ? new Listing($it->vehicle_id) : false;
      array_push($itineraries, $obj);
    }
    return $itineraries;

  }
}