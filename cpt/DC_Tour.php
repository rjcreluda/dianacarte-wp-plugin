<?php

/**
 * Wrapper class for Tour CPT
 */
class DC_Tour
{
  
  function __construct()
  {
    $this->post_type = "dc_tour";
    add_action('init', array($this, 'create_post_type'));
    add_action( 'cmb2_admin_init', array($this, 'add_metabox') );
    add_action('wp_ajax_save_tour', array($this, 'ajax_save_tour'));
    add_action('wp_ajax_nopriv_save_tour', array($this, 'ajax_save_tour'));
  }

  /** 
   * Get tours list ajax call
   * @param $user_id: int
   * @return $json
   */
  public function ajax_list_tour()
  {
    $id = (int) $_GET['user_id'];
    $user_id = get_current_user_id();
    $json = array(
          'success' => false,
          'data' => null
      );
    $tours = Tour::get( 1 );
    if( count($tours) > 0 ){
      $json['success'] = true;
      $json['data'] = $tours;
    }
    wp_send_json( $json );
    wp_die();
  }

  /** 
   * Save new tour form ajax call
   * @param null
   * @return $json response
   */
  public function ajax_save_tour(){
    $data = $_POST['tour'];
    $json = array(
          'success' => false,
          'data' => $data
      );
    /*$listings = Listing::category($term_id);
    if( count($listings) > 0 ){
      $json['success'] = true;
      $json['data'] = $listings;
    }*/
    $tour = new Tour([
      'name' => $data['name'],
      'persons' => $data['persons'],
      'budjet' => $data['budjet'],
    ]);
    $tour_id = $tour->save();
    // saving itinerary
    foreach( $data['itineraries'] as $itinerary){
      $it = new Itineraire( array(
        'tour_id' => $tour_id, 
        'place_id' => $itinerary['place_id'], 
        'hotel_id' => $itinerary['hotel_id'],
        'resto_id' => $itinerary['resto_id'],
        'vehicle_id' => $itinerary['vehicle_id'],
      ));
      $it->save();
	  	// Begin email
		if( get_post( (int) $itinerary['hotel_id']) ){
			$hotel = get_post( (int) $itinerary['hotel_id']);
		}
		if( get_post( (int) $itinerary['resto_id']) ){
			$resto = get_post( (int) $itinerary['resto_id']);
		}
		if( get_post( (int) $itinerary['vehicle_id']) ){
			$vehicle = get_post( (int) $itinerary['vehicle_id']);
		}
		global $current_user;
		$client_email = (string) $current_user->user_email;
		$to = bloginfo('admin_email');
		$to = 'jindessi.rabe@gmail.com';		

		$subject = 'Réservation depuis Dianacarte';
		$message = '<h2 style="color: orange;">DIANA CARTE</h2>';
		$message .= '<h3>Nouveau reservation de '.$current_user->display_name.'</h3>';
		$message .= 'Email du client: ' . $current_user->user_email;
		$message .= '<p>Nombre des personnes: '.$data['persons'].'</p>';
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($to, $subject, $message, $headers);
		// end email
    }
    $json['success'] = true;
    wp_send_json( $json );

	
    die();
  }

  public function create_post_type()
  {
    //global $wpwa_custom_post_types_manager;
    $params = array();
    $params['post_type'] = $this->post_type;
    $params['singular_post_name'] = __('Tour','dianacarte');
    $params['plural_post_name'] = __('Tours','dianacarte');
    $params['description'] = __('Package créer par les clients','dianacarte');
    $params['supported_fields'] = array('title', 'thumbnail');
    $params['exclude_from_search'] = true;
    $params['has_archive'] = false;
    $params['show_in_rest'] = true;
    DC_Custom_Post_Type_Manager::create_post_type($params);
  }

  public function add_metabox()
  {
    $cmb = new_cmb2_box( array(
          'id'            => 'tour_metabox',
          'title'         => __( 'Information', 'cmb2' ),
          'object_types'  => array( $this->post_type ), // Post type
          'context'       => 'normal',
          'priority'      => 'high',
          'show_names'    => true, // Show field names on the left
          'cmb_styles' => true, // false to disable the CMB stylesheet
          // 'closed'     => true, // Keep the metabox closed by default
      ) );
      // Getting List of itineraries
    
    $cmb->add_field( array(
      'name'    => esc_html__( 'Budjet necessaire', 'cmb2' ),
      'id'      => 'budjet',
      'type'    => 'text',
    ) );
    $cmb->add_field( array(
      'name'    => esc_html__( 'Nombre de personnes', 'cmb2' ),
      'id'      => 'persons',
      'type'    => 'text',
    ) );
  }
}