<?php 

class DC_Menus
{
	private $post_type;
	private $template_parser;

	public function __construct() 
	{
		// Initialization code goes here
		$this->post_type = "dc_menus";
    	add_action('init', array($this, 'create_post_type'));
    	//add_action( 'add_meta_boxes', array($this, 'dc_add_meta_box') );
    	//add_action( 'save_post', array($this, 'dc_save_meta'));
    	
    	// Custom template for rendering
    	//add_filter('single_template', array($this, 'dc_hotel_template'));
    	
    	add_action( 'cmb2_admin_init', array($this, 'create_menus_metabox') );
    	
    	/* Add column to Dashboard chambre list */
    	add_filter('manage_'.$this->post_type.'_posts_columns', array($this, 'add_custom_column'));
    	add_action('manage_'.$this->post_type.'_posts_custom_column', array($this, 'add_custom_column_value'), 10, 2);

    	/* Make column sortable */
    	add_filter('manage_edit-'.$this->post_type.'_sortable_columns', function($columns){
    		$columns['dc_hotel'] = 'dc_menus_hotel_id';
    		return $columns;
    	});
    	add_action('pre_get_posts', array($this, 'sort_room_by_hotel'));

	}

	public function create_post_type()
	{
		//global $wpwa_custom_post_types_manager;
		$params = array();
		$params['post_type'] = $this->post_type;
		$params['singular_post_name'] = __('Menu Resto','dianacarte');
		$params['plural_post_name'] = __('Menus Resto','dianacarte');
		$params['description'] = __('Menus','dianacarte');
		$params['supported_fields'] = array('title');
		$params['exclude_from_search'] = true;
		$params['has_archive'] = false;
		DC_Custom_Post_Type_Manager::create_post_type($params);
		$args = array();
		$args['name'] = 'Type de Menu';
		$args['taxonomy'] = 'dc_menus_type';
		$args['post_type'] = array($this->post_type);
		$args['slug'] = 'dc_menus_type';
		DC_Custom_Post_Type_Manager::create_taxonomy($args);
	}


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
	
	/* Add custom column to items list in Admin */
	public function add_custom_column($columns){
		$columns['dc_hotel'] = 'Restaurant';
		$columns['price'] = 'Prix (€)';
		// Move date column to the last
		$take_out = $columns['date'];
		unset($columns['date']);
		$columns['date'] = $take_out;
		return $columns;
	}

	public function add_custom_column_value($column_key, $post_id)
	{
		if( $column_key == 'dc_hotel'){
			$hotel_name = get_post(get_post_meta($post_id, 'dc_menus_hotel_id', true))->post_title;
			echo '<strong>'.$hotel_name.'</strong>';
		}
		else if( $column_key == 'price'){
			echo get_post_meta($post_id, 'dc_menus_price', true);
		}
	}

	public function sort_room_by_hotel($query)
	{
		if( !is_admin() ){
			return;
		}
		$orderby = $query->get('orderby');
		if( $orderby == 'dc_menus_hotel_id' ){
			$query->set('meta_key', 'dc_menus_hotel_id');
			$query->set('orderby', 'meta_value_num');
		}
	}
	
}