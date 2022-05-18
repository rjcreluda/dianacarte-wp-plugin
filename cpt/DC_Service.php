<?php

/**
 * Implementing custom post type for Service of entreprise
 */
class DC_Service
{
	
	public function __construct()
	{
		$this->post_type = "dc_service";
    	add_action('init', array($this, 'create_post_type'));
    	add_action( 'cmb2_admin_init', array($this, 'add_metabox') );
    	add_action( 'init', array($this, 'register_taxonomy_service_cat') );
    	add_action( 'init', array($this, 'register_taxonomy_service_feature') );
    	add_action( 'save_post', array($this, 'save_service_type'));
		add_filter( 'single_template', array($this, 'service_single_template') );
		add_action( 'add_meta_boxes', array($this, 'add_header_type_metabox') );
	}

	/* Add citybook header type metabox
	 * Type: carousel of big image */
	public function add_header_type_metabox( $post )
	{
		$screen = $this->post_type ;
		$new_post = true;
        add_meta_box(
            'listing_content_widgets',
            __( 'Content Widgets', 'citybook-add-ons' ),
            'citybook_addons_listing_meta_box_content_widgets_callback',
            $screen, // for listing post
            'normal',
            'default',
            //,'normal', //('normal', 'advanced', or 'side')
            //'core'//('high', 'core', 'default' or 'low') 
            array('new_post' => $new_post)
        );
	}

	/* We override templace for rendering single service */
	public function service_single_template($single_template) {
	    global $post;

	    if ( $post->post_type == $this->post_type ) {
	        $single_template = DIANACARTE_THEME_DIR . 'single-'.$this->post_type.'.php';
	    }
	    return $single_template;
	}

	/* We didn't show the service category metabox
	 * instead we put it in the post metabox
	 * so we need to assign programmatically the type of the service (Category)
	 * according to the value in Type Metabox
	*/
	public function save_service_type( $post_id )
	{
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		// check if correct post type and that the user has correct permissions
		if ( isset($_POST['post_type']) && $this->post_type == $post->post_type ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
			    return $post_id;
		} 
		else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				 return $post_id;
		}
		// Set the type of the service ( Category )
		$term_id = get_post_meta( $post_id, 'type', true );
		wp_set_post_terms( $post_id, [$term_id], 'service_cat', false );
		citybook_addons_do_save_listing_meta($post_id, true, true);
	}

	public function register_taxonomy_service_cat()
	{
		$args = array();
		$args['name'] = 'Type de service';
		$args['taxonomy'] = 'service_cat';
		$args['post_type'] = array($this->post_type);
		$args['label'] = __('Types de service', 'dianacarte');
		$args['slug'] = 'service_cat';
		$args['meta_box_cb'] = false;
		$args['default_term'] = array(
			[ 'name' => 'Hebergement', 'slug' => 'hebergement' ],
			[ 'name' => 'Restauration', 'slug' => 'resto' ],
			[ 'name' => 'Location', 'slug' => 'location' ]
		);
		DC_Custom_Post_Type_Manager::create_taxonomy( $args );
		/*$default_services = array('Hebergement', 'Restauration', 'Location vehicule', 'Excursion');
		foreach ($default_services as $service ) {
			if( !term_exists( strtolower($service), $args['taxonomy'] ) ){
				wp_insert_term( $service, $args['taxonomy'], array( '' ) );
			}
		}*/
	}

	public function register_taxonomy_service_feature()
	{
		$args = array();
		$args['name'] = 'Equipements';
		$args['taxonomy'] = 'service_feature';
		$args['post_type'] = array($this->post_type);
		$args['label'] = __('Types de service', 'dianacarte');
		$args['slug'] = 'service_feature';
		DC_Custom_Post_Type_Manager::create_taxonomy( $args );
		$default_feature = array('Climatisation', 'Télévision');
		foreach ($default_feature as $feature ) {
			if( !term_exists( strtolower($feature), $args['taxonomy'] ) ){
				wp_insert_term( $feature, $args['taxonomy'], array( '' ) );
			}
		}
	}

	public function add_metabox()
	{
		$cmb = new_cmb2_box( array(
	        'id'            => 'service_metabox',
	        'title'         => __( 'Information de service', 'cmb2' ),
	        'object_types'  => array( $this->post_type ), // Post type
	        'context'       => 'normal',
	        'priority'      => 'high',
	        'show_names'    => true, // Show field names on the left
	        'cmb_styles' => true, // false to disable the CMB stylesheet
	        // 'closed'     => true, // Keep the metabox closed by default
	    ) );
	    // Getting List of entreprise
        $entreprises = Listing::all();

	    $listing_options = array();
	    foreach($entreprises as $listing){
	    	$listing_options[$listing->id] = $listing->name;
	    }
	    $cmb->add_field( array(
			'name'             => esc_html__( 'Etablissement', 'cmb2' ),
			'desc'             => esc_html__( "Selectionner un établissemebt", 'cmb2' ),
			'id'               => 'listing_id',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => $listing_options,
		) );
		// Type de service
	    $service_type = Service::types();
	    $service_options = array();
	    foreach( $service_type as $type ){
	    	$service_options[$type->id] = $type->name;
	    }
	    $cmb->add_field( array(
			'name'             => esc_html__( 'Type de service', 'cmb2' ),
			'desc'             => esc_html__( "Hébergement, restauration, ...", 'cmb2' ),
			'id'               => 'type',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => $service_options,
		) );
		$cmb->add_field( array(
			'name' => esc_html__( 'Prix', 'cmb2' ),
			'desc' => esc_html__( 'prix du service', 'cmb2' ),
			'id'   => 'prix',
			'type' => 'text_money',
			'before_field' => '€',
		) );
		$service_unite = array(
	    	'1' => 'nuité', 
	    	'2' => 'plat', 
	    	'3' => 'jour',
	    	'4' => 'élément'
	    );
	    $cmb->add_field( array(
			'name'             => esc_html__( 'Unité', 'cmb2' ),
			'id'               => 'unite',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => $service_unite,
		) );
		$cmb->add_field( array(
			'name' => esc_html__( 'Photos', 'cmb2' ),
			'desc' => esc_html__( 'Téléverser un image ou depuis URL.', 'cmb2' ),
			'id'   => 'photo',
			'type' => 'file',
		) );
		$cmb->add_field( array(
		'name'    => esc_html__( 'Description', 'cmb2' ),
		'id'      => 'description',
		'type'    => 'wysiwyg',
		'options' => array(
			'textarea_rows' => 4,
		),
	) );
	}

	public function create_post_type()
	{
		//global $wpwa_custom_post_types_manager;
		$params = array();
		$params['post_type'] = $this->post_type;
		$params['singular_post_name'] = __('Service Etablissement','dianacarte');
		$params['plural_post_name'] = __('Services Etablissement','dianacarte');
		$params['description'] = __('Service offert par un Etablissement','dianacarte');
		$params['supported_fields'] = array('title', 'thumbnail');
		$params['exclude_from_search'] = false;
		$params['has_archive'] = false;
		$params['taxonomies'] = array('listing_cat');
		DC_Custom_Post_Type_Manager::create_post_type($params);
	}
}