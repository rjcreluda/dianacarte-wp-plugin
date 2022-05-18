<?php

class Listing_Metabox
{
	public function create_menus_metabox()
	{
		$cmb = new_cmb2_box( array(
	        'id'            => 'dc_menus_metabox',
	        'title'         => __( 'Information du menu', 'cmb2' ),
	        'object_types'  => array( $this->post_type ), // Post type
	        'context'       => 'normal',
	        'priority'      => 'high',
	        'show_names'    => true, // Show field names on the left
	        // 'cmb_styles' => false, // false to disable the CMB stylesheet
	        // 'closed'     => true, // Keep the metabox closed by default
	    ) );

	    // Getting List of Restaurant
	    $args = array(
        'post_type' => 'listing',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
            'taxonomy' => 'listing_cat',
            'field' => 'slug',
            'terms' => '02-restaurants'
             )
          )
        );
        $restos = new WP_Query( $args );
        //wp_die( var_dump($restos));

	    $resto_options = array();
	    foreach($restos->posts as $resto){
	    	$resto_options[$resto->ID] = $resto->post_title;
	    }
	    $cmb->add_field( array(
			'name'             => esc_html__( 'Resto propriétaire', 'cmb2' ),
			'desc'             => esc_html__( "Selectionner le resto", 'cmb2' ),
			'id'               => 'dc_menus_resto_id',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => $resto_options,
		) );
		$cmb->add_field( array(
			'name' => esc_html__( 'Prix', 'cmb2' ),
			'desc' => esc_html__( 'prix du menu', 'cmb2' ),
			'id'   => 'dc_menus_price',
			'type' => 'text_money',
			'before_field' => '€',
		) );
		$cmb->add_field( array(
			'name' => esc_html__( 'Photos', 'cmb2' ),
			'desc' => esc_html__( 'Upload an image or enter a URL.', 'cmb2' ),
			'id'   => 'dc_menus_image',
			'type' => 'file',
		) );
	}
}