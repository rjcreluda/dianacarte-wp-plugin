<?php 

class DC_Custom_Post_Type_Manager
{
	public static function create_post_type($params)
	{
		extract($params);
		$labels = array(
		'name' => sprintf( __( '%s', 'dianacarte' ), $plural_post_name),
		'singular_name' => sprintf( __( '%s', 'dianacarte' ), $singular_post_name),
		'add_new' => __( 'Add New', 'dianacarte' ),
		'add_new_item' => sprintf( __( 'Add New %s ', 'dianacarte' ), $singular_post_name),
		'edit_item'  => sprintf( __( 'Edit %s ', 'dianacarte' ),$singular_post_name),
		'new_item' => sprintf( __( 'New  %s ', 'dianacarte' ),$singular_post_name),
		'all_items' => sprintf( __( 'All  %s ', 'dianacarte' ),$plural_post_name),
		'view_item' => sprintf( __( 'View  %s ', 'dianacarte' ),$singular_post_name),
		'search_items'  => sprintf( __( 'Search  %s ', 'dianacarte'), $plural_post_name),
		'not_found' => sprintf( __( 'No  %s found', 'dianacarte' ), $plural_post_name),
		'not_found_in_trash' => sprintf( __( 'No  %s  found in the Trash', 'dianacarte' ), $plural_post_name),
		'parent_item_colon' => '',
		'menu_name' => sprintf( __('%s', 'dianacarte' ),$plural_post_name),
		  );
		$args = array(
			'labels' => $labels,
			'hierarchical' => true,
			'description' => $description,
			'supports' => $supported_fields,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus'  => true,
			'publicly_queryable' => true,
			'exclude_from_search' => $exclude_from_search,
			'has_archive' => $has_archive,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => true,
			'capability_type' => 'post',
		);
		register_post_type( $post_type, $args );
	}

	public static function create_taxonomy( $params )
	{
		extract($params);
		register_taxonomy(
			$taxonomy,
			$post_type,
			array(
				'label' => $label,
				'rewrite' => array('slug' => $slug),
				'hierarchical' => true 
			)
		);
	}
}