<?php 

/**
 * 
 */
class ItineraireControllerApi extends WP_REST_Controller
{
  
  public function register_routes() {
    $namespace = 'dianacarte';
    //$path = 'latest-posts/(?P<category_id>\d+)';
    /*register_rest_route( 
      $namespace, '/tours'.'/(?P<id>[\d]+)', 
      array(
        'methods'         => WP_REST_Server::READABLE,
        'callback'       => array($this, 'show_tour'),
        'args'            => array(
          'id'  => [ 'required' => true, 'type' => 'number']
        )
      )
    );*/

    register_rest_route( $namespace, '/itineraire', [
      array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => array( $this, 'create_item' ),
        'permission_callback' => array( $this, 'create_items_permissions_check' )
      )
    ]);

    register_rest_route( $namespace, '/itineraire'.'/(?P<id>[\d]+)', [
      array(
        'methods'             => 'GET',
        'callback'            => array( $this, 'get_item' ),
        'permission_callback' => array( $this, 'get_items_permissions_check' )
      ),
      array(
        'methods'             => WP_REST_Server::EDITABLE,
        'callback'            => array( $this, 'update_item' ),
        'permission_callback' => array( $this, 'update_item_permissions_check' ),
        'args'                => $this->get_endpoint_args_for_item_schema( false ),
      ),
      array(
        'methods'             => WP_REST_Server::DELETABLE,
        'callback'            => array( $this, 'delete_item' ),
        'permission_callback' => array( $this, 'delete_item_permissions_check' ),
        'args'                => array(
          'force' => array(
            'default' => false,
          ),
        ),
      ),

    ]);     
  }

  public function get_items_permissions_check($request) {
    return true;
  }
  public function update_item_permissions_check($request) {
    return true;
  }
  public function delete_item_permissions_check($request){
    return true;
  }
  public function create_items_permissions_check($request){
    return true;
  }

  public function create_item( $request )
  {
    $data = array(
      'tour_id' => (int) $request['tour_id'],
      'place_id' => (int) $request['place_id'],
      'hotel_id' => (int) $request['hotel_id'],
      'resto_id' => (int) $request['resto_id'],
      'vehicle_id' => (int) $request['vehicle_id']
    );
    //return $request->get_body_params();
    $it = new Itineraire( $data );
    $state = $it->save();
    return new WP_REST_Response($state, 200);
  }

  public function get_item($request) {
    $data = new Itineraire( (int) $request['id'] );
    $response = array('data' => $data);
    //return $request->get_body_params();
    return new WP_REST_Response($response, 200);

    /*$posts = get_posts($args);
    if (empty($posts)) {

            return new WP_Error( 'empty_category', 'there is no post in this category', array( 'status' => 404 ) );
    }
    return new WP_REST_Response($posts, 200);*/
  }

  /**
   * Update one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function update_item( $request ) {
    $data = array(
      'id' => (int) $request['id'],
      'tour_id' => (int) $request['tour_id'],
      'place_id' => (int) $request['place_id'],
      'hotel_id' => (int) $request['hotel_id'],
      'resto_id' => (int) $request['resto_id'],
      'vehicle_id' => (int) $request['vehicle_id']
    );
    $it = new Itineraire( $data );
    $update = $it->update();
    if ( $update ) {
      return new WP_REST_Response( $update, 200 );
    }
 
    return new WP_Error( 'cant-update', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }

  /**
   * Delete one item from the collection
   *
   * @param WP_REST_Request $request Full data about the request.
   * @return WP_Error|WP_REST_Response
   */
  public function delete_item( $request ) {
    $id = (int) $request['id'];
    $it = new Itineraire($id);
    $res = $it->delete();

    if ( $res ) {
        return new WP_REST_Response( true, 200 );
    }
 
    return new WP_Error( 'cant-delete', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }

}