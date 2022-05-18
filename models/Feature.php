<?php

/**
 * Wrapper Class for listing custom taxonomy
 * Taxonomy: listing_feature
 */

class Feature
{
	
	function __construct()
	{
		# code...
	}

	private function load_list()
	{
		// getting taxonomy terms list
		$terms = get_terms( array( 'listing_feature' ) );
		return $terms;
	}

	public static function get_name( $listing_id )
	{
		if( !is_int($listing_id) )
			return;
		return wp_get_object_terms( $listing_id, 'listing_feature', array('fields' => 'names') );
	}
}