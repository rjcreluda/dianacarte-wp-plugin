<?php 
class DC_Restaurant
{
	private $post_type;
	private $template_parser;

	public function __construct() 
	{
		global $wpwa_template_loader;
		$this->template_parser = $wpwa_template_loader;
		// Initialization code goes here
		$this->post_type = "dc_resto";
    	add_action('init', array($this, 'create_post_type'));
    	add_action( 'add_meta_boxes', array($this, 'dc_add_meta_box') );
    	add_action( 'save_post', array($this, 'dc_save_meta'));
    	// Custom template for rendering
    	//add_filter('single_template', array($this, 'dc_resto_template'));
    	//add_action('init', array($this, 'dc_register_taxonomy'));

    	// Add Javascript and CSS for admin screens
        add_action('admin_enqueue_scripts', array($this,'enqueue_admin'));

        // Add Javascript and CSS for front-end display
        //add_action('wp_enqueue_scripts', array($this,'enqueue'));

        // Show mresto menus lists 
        add_action('add_meta_boxes', array($this, 'dc_add_menus_meta_box'));
        add_action('citybook_addons_listing_content_after',array($this,  'show_resto_menu_in_front'));
	}

	/**
	 * Get menu list for the restaurant
	 * @return array 
	 */
	private function get_menus($id)
	{
		$resto_id = (int) $id;
		$query_args = array(
			'post_type' 	=> 'dc_menus',
			'meta_query'	=> array(
				array(
					'key' 	=> 'dc_menus_resto_id',
					'value' => $resto_id
				)
			)
		);
		return new WP_Query( $query_args );
	}

	public function show_resto_menu_in_front(){
		global $post;
		if( $post->post_type != $this->post_type )
			return;
		$query = $this->get_menus($post->ID);
		if( $query->posts ){
			?>
			<div class="list-single-main-item fl-wrap">
			<table class="form-table">
				<tr>
					<th>Nom du menu</th>
					<th>Prix</th>
				</tr>
			<?php
			foreach( $query->posts as $menus ){
				$price = get_post_meta( $menus->ID, 'dc_menus_price', true );
				echo '<tr>';
				echo '<td>'.$menus->post_title.'</td>';
				echo '<td>'.$price.'€</td>';
				echo '</tr>';
			} ?>
			</table>
			</div>
			<?php
		}
	}

	public function dc_add_menus_meta_box($post_type)
	{
		$post_types = array($this->post_type);
		//limit meta box to certain post types
		if (in_array($post_type, $post_types)) {
			add_meta_box('dc-resto-menus',
			'Liste des produits',
			array($this, 'dc_menus_meta_box_function'),
			$post_type,
			'normal',
			'high');
		}
	}
	public function dc_menus_meta_box_function($post)
	{
		// Getting menu list
		$query = $this->get_menus($post->ID);
		if( $query->posts ){
			?>
			<table class="form-table">
				<tr>
					<th>Nom du menu</th>
					<th>Prix</th>
					<th>Action</th>
				</tr>
			<?php
			foreach( $query->posts as $menus ){
				$price = get_post_meta( $menus->ID, 'dc_menus_price', true );
				echo '<tr>';
				echo '<td>'.$menus->post_title.'</td>';
				echo '<td>'.$price.'€</td>';
				echo '<td><a href="#">Supprimer</a></td>';
				echo '</tr>';
			} ?>
			</table>
			<?php
		}
	}

	public function enqueue_admin()
	{
		// These two lines allow you to only load the files on the relevant screen, in this case, the editor for a "books" custom post type
    	$screen = get_current_screen();
    	if (!($screen->base == 'post' && $screen->post_type == $this->post_type)) return;

    	wp_enqueue_script('dc-vue', DC_URL . 'assets/js/vue.min.js', array('jquery'), '1.0', true);
    	// Actual enqueues, note the files are in the js and css folders
    	// For scripts, make sure you are including the relevant dependencies (jquery in this case)
    	//wp_enqueue_script('very-descriptive-name', plugins_url('js/books-post-editor.js', __FILE__), array('jquery'), '1.0', true);
    	//wp_enqueue_style('very-exciting-name', plugins_url('css/books-post-editor.css', __FILE__), null, '1.0');
	}

	public function create_post_type()
	{
		//global $wpwa_custom_post_types_manager;
		$params = array();
		$params['post_type'] = $this->post_type;
		$params['singular_post_name'] = __('Restaurant','wpwa');
		$params['plural_post_name'] = __('Restaurants','wpwa');
		$params['description'] = __('Restaurants','wpwa');
		$params['supported_fields'] = array('title', 'editor', 'thumbnail');
		$params['exclude_from_search'] = false;
		$params['has_archive'] = true;
		DC_Custom_Post_Type_Manager::create_post_type($params);
		$args = array();
		$args['taxonomy'] = 'dc_resto_type';
		$args['post_type'] = $this->post_type;
		$args['label'] = __('Restaurant type', 'dianacarte');
		$args['slug'] = 'resto_type';
		DC_Custom_Post_Type_Manager::create_taxonomy($args);

	}

	public function dc_add_meta_box( $post )
	{
		$screen = $this->post_type ;
		$new_post = true;
		add_meta_box(
            'resto_loc_contacts',
            __( 'Contacts', 'citybook-add-ons' ),
            'citybook_addons_listing_meta_box_loc_contacts_callback',
            $screen, // for listing post
            'normal',
            'high',
            //,'normal', //('normal', 'advanced', or 'side')
            //'core'//('high', 'core', 'default' or 'low') 
            //array('new_post' => $new_post)
        );
        if(citybook_addons_get_option('submit_hide_price_opt') != 'yes'){
            add_meta_box(
                'listing_prices_opt',
                __( 'Price Options', 'citybook-add-ons' ),
                'citybook_addons_listing_meta_box_prices_opt_callback',
                $screen, // for listing post
                'normal',
                'high',
                //,'normal', //('normal', 'advanced', or 'side')
                //'core'//('high', 'core', 'default' or 'low') 
                array('new_post' => $new_post)
            );
        }
        add_meta_box(
            'listing_header_media',
            __( 'Header Media', 'citybook-add-ons' ),
            'citybook_addons_listing_meta_box_header_media_callback',
            $screen, // for listing post
            'normal',
            'high',
            //,'normal', //('normal', 'advanced', or 'side')
            //'core'//('high', 'core', 'default' or 'low') 
            array('new_post' => $new_post)
        );
        add_meta_box(
            'listing_content_widgets',
            __( 'Content Widgets', 'citybook-add-ons' ),
            'citybook_addons_listing_meta_box_content_widgets_callback',
            $screen, // for listing post
            'normal',
            'high',
            //,'normal', //('normal', 'advanced', or 'side')
            //'core'//('high', 'core', 'default' or 'low') 
            array('new_post' => $new_post)
        );
	}

	public function dc_save_meta( $post_id )
	{
		// don't save anything if WP is auto saving
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		// check if correct post type and that the user has correct permissions
		if ( isset($_POST['post_type']) && $this->post_type == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
		    return $post_id;
		} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			 return $post_id;
		}
		citybook_addons_do_save_listing_meta($post_id, true, true);
		// update homework meta data
		/*update_post_meta( $post_id, '_schoolpress_homework_is_required', $_POST['is_required']);
		update_post_meta( $post_id, '_schoolpress_homework_due_date', $_POST['due_date']);*/
	}

	public function dc_resto_template($single)
	{
		global $post;
	    /* Checks for single template by post type */
	    /*if ( $post->post_type == $this->post_type ) {
	        if ( file_exists( DC_PATH . '/templates/single-dc_resto.php' ) ) {
	            return DC_PATH . '/templates/single-dc_resto.php';
	        }
	    }
	    return $single;*/
	    if ( $this->post_type === $post->post_type && locate_template( array( 'single-'.$this->post_type.'.php' ) ) !== $template ) {
        /*
         * This is a $this->post_type post
         * AND a 'single $this->post_type template' is not found on
         * theme or child theme directories, so load it
         * from our plugin directory.
         */
        return DC_PATH . '/templates/single-'.$this->post_type.'.php';
    	}
    	return $single;
	}
}