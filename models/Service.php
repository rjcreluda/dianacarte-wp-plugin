<?php

/**
 * Wrapper class for Service CPT
 */
class Service
{
	
	function __construct( $id = null)
	{
		$id = (int) $id;
		if( is_int($id) && $id  != 0 ){
			$this->getPost($id);
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
			$this->type = get_post_meta( $id, 'type', true );
			$this->price = $this->post->prix;
			$this->unite = $this->post->unite;
			$this->owner = new Listing( $this->post->listing_id );
			$this->features = wp_get_object_terms( $this->id, 'service_feature', array('fields' => 'names') );
			$this->viewCount = citybook_addons_get_post_views($this->id);
			$this->url = get_permalink( $this->post, false );
			$this->serviceType = get_term($this->type, 'service_cat')->name;
		}
	}


	/** 
   * Get service owner
   * @param $service_id:int
   * @return Listing:array
   */
	public static function owner( $service_id )
	{
		return new Listing( get_post_meta( $service_id, 'listing_id', true ) );
	}


	/** 
   * Get list of services category
   * @param null
   * @return Object:array
   */
	public static function types()
	{
		$cats = get_terms('service_cat', array(
			'orderby' => 'name',
			'hide_empty' => false
		));
		$data = array();
		foreach( $cats as $cat ){
			array_push( $data, (object)['id' => $cat->term_id, 'name' => $cat->name] );
		}
		return $data;
	}
}