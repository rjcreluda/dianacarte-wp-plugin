<?php 

/**
 * 
 */
class CommandeControllerApi extends WP_REST_Controller
{
  
  public function register_routes() {
    $namespace = 'dianacarte';
    //$path = 'latest-posts/(?P<category_id>\d+)';

    register_rest_route( 
      $namespace, '/commandes', 
      array(
        'methods'         => WP_REST_Server::READABLE,
        'callback'       => array($this, 'get_items'),
        'permission_callback' => array( $this, 'get_items_permissions_check' ),
      )
    );
    /*register_rest_route( 
      $namespace, '/commandes'.'/(?P<id>[\d]+)', 
      array(
        'methods'         => WP_REST_Server::READABLE,
        'callback'       => array($this, 'show_tour'),
        'args'            => array(
          'id'  => [ 'required' => true, 'type' => 'number']
        )
      )
    );*/

    register_rest_route( $namespace, '/commandes'.'/(?P<id>[\d]+)', [
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
  public function get_items( $request )
  {
    $user_id = get_current_user_id();
    if( $user_id == 0 ){
      return new WP_Error( 'cant-list', 'User not logged in or no nonce header', array( 'status' => 500 ) );
    }
    $data = Commande::for_user( $user_id );
    $response = new WP_REST_Response( $data, 200);
    return $response;
  }

  public function get_item($request) {
    $user_id = get_current_user_id();
    if( $user_id == 0 ){
      return new WP_Error( 'cant-show', 'User not logged in or no nonce header', array( 'status' => 500 ) );
    }
    $data['commande'] = Commande::find( (int) $request['id'] );
    $data['commande']->services = $data['commande']->services();
    $response = new WP_REST_Response($data, 200);
    return $response;

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
    /*$items = [];
    foreach( $request as $key => $value ){
      array_push($items, array($key => $value));
    }*/
    /*$item = $request['id'];
    $arr = (object) array('nom' => 'john', 'age' => 123);
    return new WP_REST_Response( $request['id'], 200 );*/
    $tour = new Tour( (int) $request['id'] );
    $tour->name = $request['name'];
    //$tour->budjet = $request['budjet'];
    $tour->persons = $request['persons'];
    $update = $tour->update(); // return boolean
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
    $item = $this->prepare_item_for_database( $request );
 
    if ( function_exists( 'slug_some_function_to_delete_item' ) ) {
      $deleted = slug_some_function_to_delete_item( $item );
      if ( $deleted ) {
        return new WP_REST_Response( true, 200 );
      }
    }
 
    return new WP_Error( 'cant-delete', __( 'message', 'text-domain' ), array( 'status' => 500 ) );
  }

}