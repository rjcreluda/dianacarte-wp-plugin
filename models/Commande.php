<?php

/**
 * Class for managing lbooking CPT
 */
class Commande
{
	
	function __construct($id = null)
	{
		if( $id && is_int($id) )
			$this->getPost($id);
	}

	private function getPost( $post_id )
	{
		$this->post = get_post($post_id);
		if ( !empty( $this->post ) ) {
			$this->id = $this->post->ID;
			$this->client_id = $this->post->_cth_user_id;
			$this->title = $this->post->post_title;
			$this->date = $this->post->_cth_lb_date;
			$this->time = $this->post->_cth_lb_time;
			$this->total_price = $this->post->total_price;
			$this->status = $this->post->_cth_lb_status;
			$this->listing = get_the_title( $this->post->_cth_listing_id );
			$this->persons = $this->post->_cth_lb_quantity;
		}
	}

	public function services()
	{
		// get services list attached to this command
		$cmdSer = CommandeService::get($this->id);
		return CommandeService::formattedText($cmdSer);
	}


	/** 
   * Save command
   * @param int $id: the post id
   * @return null
   */
	public function save( $data = null)
	{
		// Insert new command
		if( $data ){
			return wp_insert_post($data ,true );
		}
		else{
			return 0;
		}
	}

	/** 
   * get all command
   * @param null
   * @return Commande $commandes command lists
   */
	public static function all()
	{
		$args = array(
    'post_type' => 'lbooking',
    'posts_per_page' => -1,
    );
    $data = new WP_Query( $args );
    $commandes = array();
    if( count($data->posts) > 0 ){
      foreach($data->posts as $cmd){
        $commandes[] = new Commande($cmd->ID);
      }
    }
    return $commandes;
	}

	/** 
   * get all command for a user
   * @param int $id user id
   * @return Commande $commandes command lists
   */
	public static function for_user( $id )
	{
		$args = array(
    'post_type' => 'lbooking',
    'posts_per_page' => -1,
    'author'  => $id
    );
    $data = new WP_Query( $args );
    $commandes = array();
    if( count($data->posts) > 0 ){
      foreach($data->posts as $cmd){
        $commandes[] = new Commande($cmd->ID);
      }
    }
    return $commandes;
	}

	public static function find( $id )
	{
		return new Commande( $id );
	}
}