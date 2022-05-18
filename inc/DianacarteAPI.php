<?php 

/**
 * 
 */
class DianacarteAPI
{
  
  function __construct()
  {
    add_action('rest_api_init', array($this, 'add_tours_api'));
    add_action('rest_api_init', function () {           
        $tour_controller = new TourControllerApi();
        $tour_controller->register_routes();
        $it = new ItineraireControllerApi();
        $it->register_routes();
        $cmd = new CommandeControllerApi();
        $cmd->register_routes();
    });
  }
  public function add_tours_api()
  {
    register_rest_route( 
      'dianacarte', '/listings', 
      array(
        'methods'         => WP_REST_Server::READABLE,
        'callback'       => array($this, 'get_listings'),
      )
    );
  }

  public function get_listings()
  {
    $data = Listing::all();
    $response = new WP_REST_Response($data, 200);
    return $response;
  }


  public function get_tours()
  {
    $data = Tour::get( 1 );
    $response = new WP_REST_Response($data, 200);
    return $response;
  }

  public function show_tour( $request )
  {
    $data['tour'] = Tour::find( (int) $request['id'] );
    $data['itineraires'] = Itineraire::get($data['tour']->id, true );
    $response = new WP_REST_Response($data, 200);
    return $response;
  }
}