<?php 

/**
 * Wrapper Class for TOUR CPT
 */
class Tour
{

  function __construct( $tour = null )
  {
    // load existing tour
    if( is_int($tour) ){
      $this->getPost($tour);
    }
    else if( is_array($tour)  && !empty($tour) ){
      if( array_key_exists('id', $tour) ){
        $this->id = $tour['id'];
      }
      else{
        $this->id = 0;
      }
      $this->name = $tour['name'];
      $this->budjet = $tour['budjet'];
      $this->persons = $tour['persons'];
    }
  }

  /** 
   * Populate Service object with post properties
   * @param int $id: the post id
   * @return null
   */
  private function getPost( $id )
  {
    $this->post = get_post($id);
    if ( !empty( $this->post ) ) {
      $this->id = $this->post->ID;
      $this->name = $this->post->post_title;
      $this->user = $this->post->post_author;
      $this->budjet = $this->post->budjet;
      $this->persons = $this->post->persons;
      $this->itineraires = Itineraire::get( $this->id );
    }
  }

  /** 
   * Save new entry
   * @param null
   * @return null
   */
  public function save()
  {
    if( $this->id == 0 ){
      // Insert Mode
      $data = array(
        'post_title'   => $this->name,
        'post_type'    => "dc_tour",
        'post_status'  => 'publish'
      );
      $tour_id = wp_insert_post($data ,true );
      update_post_meta( $tour_id, 'budjet', $this->budjet );
      update_post_meta( $tour_id, 'persons', $this->persons );
      return $tour_id;
    }
    /*global $wpdb;
    $table_name = $wpdb->prefix . self::$table_name;
    $format = [ '%d', '%d', '%d', '%d', '%d', '%f' ];
    $wpdb->insert( $table_name, $this->data, $format );*/
  }

  public function update()
  {
    if( $this->id != 0 ){
      // Updating Tour
      $args = array(
        'ID'          => $this->id,
        'post_title' => $this->name,
      );
      if( wp_update_post( $args ) ){
        update_post_meta( $this->id, 'persons', $this->persons );
        return true;
      }
      else{ return false; }
    }
  }

  /** 
   * Get list of itinerary for a particular tour
   * @param $tour_id: integer
   * @return Object: array
   */
  public static function itineraries($tour_id = null)
  {
    if( !is_int($tour_id) )
      return;
    global $wpdb;
    $table_name = $wpdb->prefix . Itineraire::$table_name;
    $query = "SELECT * FROM $table_name WHERE tour_id = %d";
    $safe_query = $wpdb->prepare( $query, $tour_id );
    return $wpdb->get_results( $safe_query ); // return object
  }

  /** 
   * Get list of tours of a given user
   * @param $user_id: integer
   * @return Object: array
   */
  public static function get( $user_id )
  {
    $tours = get_posts( array(
      'author'    => $user_id,
      'posts_per_page' => -1,
      'post_type'      => 'dc_tour',
    ) );
    $data = array();
    if( count($tours) > 0 ){
      foreach( $tours as $tour ){
        $t = new Tour($tour->ID); 
        $t->itineraire = Itineraire::get($t->id);
        array_push($data, $t);
      }
    }
    return $data;
  }

  public static function find( $tour_id )
  {
    return new Tour( $tour_id );
  }
}