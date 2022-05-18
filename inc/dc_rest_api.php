<?php 

/**
 * Manage REST API for custom post type in the parent theme
 */
class DC_Rest_Api
{
	public static function init(){

		// Add API Support for Map Page, post type: listing
		add_filter( 'register_post_type_args', 'listing_post_type_args', 10, 2 );
		function listing_post_type_args( $args, $post_type ){
			if ( 'listing' === $post_type ) {
		        $args['show_in_rest'] = true;
		        // Optionally customize the rest_base or rest_controller_class
		        $args['rest_base']             = 'listings';
		        $args['rest_controller_class'] = 'WP_REST_Posts_Controller';
		    }
		    return $args;
		}

		//Add REST API support to an already registered taxonomy.
		add_filter( 'register_taxonomy_args', 'listing_taxonomy_args', 10, 2 );

		function listing_taxonomy_args( $args, $taxonomy_name ) {
			if ( 'listing_cat' === $taxonomy_name ) {
			$args['show_in_rest'] = true;
			// Optionally customize the rest_base or rest_controller_class
			$args['rest_base']             = 'listing_cat';
			$args['rest_controller_class'] = 'WP_REST_Terms_Controller';
			}
			return $args;
		}
	}
}