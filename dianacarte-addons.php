<?php
/*
* Plugin Name: Diana Carte Addon
* Description: Extension du theme Diana Carte
* Author: Mada-Digital
* Author URI: http://mada-digital.com
* Text Domain: dianacarte
* Version: 1.0.1
*/

if ( ! defined('ABSPATH') ) {
    die('Please do not load this file directly!');
}

/* DC is an abbreviation of DianaCarte */
define('DC_URL', plugin_dir_url(__FILE__));
define('DC_PATH', plugin_dir_path(__FILE__));
define('DC_PRODUCTION_MODE', true);



/**
 * Diana carte plugin main class
 */
class DianaCarte
{
	public function __construct()
	{
		// Plugin activation hook
		register_activation_hook( __FILE__, array($this,'activate_dianacarte_plugin'));
		
		// Controller & auth manager
		$controller = new FrontController();
		// Hotel room
		$chambre_model = new DC_Chambre();
		// Restaurant menu food
		$resto_menu = new DC_Menus();
		// Init Service Etablissement
		$service = new DC_Service();
		// Init Tour package
		$tour = new DC_Tour();

		// Create databse table
		//Itineraire::init();
		//CommandeService::init();
		
		new DianacarteAPI();

		// Map
		add_shortcode('diana-carte', array($this, 'dianacarte_shortcode_content') );
		add_action('wp_ajax_dianacarte', array($this, 'dc_map_ajax'));

		add_action('wp_enqueue_scripts', array($this, 'dc_enqueue_map_scripts'));
		add_action('wp_ajax_listing_by_cats', array($this, 'dc_ajax_listing_by_cats'));
		add_action('wp_ajax_nopriv_listing_by_cats', array($this, 'dc_ajax_listing_by_cats'));

		// Add content to Single Listing template
		add_action( 'citybook_addons_listing_content_after', array($this, 'dc_listing_single_content_end') );

		// Override the template for Single Listing
		add_filter( 'single_template', array($this, 'dc_listing_single_template') );

		// Booking system, add send email booking 
		add_action('dianacarte_insert_booking_after', array($this, 'dc_booking_send_email'));

	}

	// Runs on plugin activation hook
	public function activate_dianacarte_plugin()
	{
		// User management
		$this->remove_application_user_roles();
		$this->add_application_user_roles();

		// Route feature activation
		$this->flush_application_rewrite_rules();

		// Service commande table
		CommandeService::init();

		// Itineraire table
		Itineraire::init();
	}

	// Booking, send email
	public function dc_booking_send_email( $booking_id ){
		//$book = get_post($booking_id);
		//update_post_meta( $booking_id, P_META_PREFIX.'lb_name', 'Jojo Boyz' );
		$listing = get_post( get_post_meta( $booking_id, P_META_PREFIX.'listing_id', true ) );
		$booking = (object) [
            'name' => get_post_meta( $booking_id, P_META_PREFIX.'lb_name', true ),
            'email' => get_post_meta( $booking_id, P_META_PREFIX.'lb_email', true ),
            'listing' => $listing->post_title,
            'persons' => get_post_meta( $booking_id, P_META_PREFIX.'lb_quantity', true ),
            'date' => get_post_meta( $booking_id, P_META_PREFIX.'lb_date', true ),
            'time' => get_post_meta( $booking_id, P_META_PREFIX.'lb_time', true ),
            'additional_info' => get_post_meta( $booking_id, P_META_PREFIX.'lb_add_info', true )
		];
		$cmdSer = CommandeService::get($booking_id);
		$services = CommandeService::formattedText($cmdSer);
		
		/* PREPARE EMAIL */
		$confirm_code = wp_generate_password(20, false, false);
		update_post_meta( $booking_id, 'confirm_code', $confirm_code );
		
		$confirm_link = '<a target="_blank" href="'.home_url().'/validation-commande/?book='.$booking_id.'&confirm_code='.$confirm_code.'&action=confimer">Confirmer</a>';
		$cancel_link = '<a target="_blank" href="'.home_url().'/validation-commande/?book='.$booking_id.'&confirm_code='.$confirm_code.'&action=annuler">Annuler, non disponible</a>';
		
		global $current_user;
		$client_email = (string) $current_user->user_email;
		
		$to = bloginfo('admin_email');
		$to = 'jindessi.rabe@gmail.com';
		
		
		$subject = 'Réservation #'.$booking_id.' depuis Dianacarte';
		$message = '<h2 style="color: orange;">DIANA CARTE</h2>';
		$message .= '<h3>Nouveau reservation de '.$booking->name.' sur '.$booking->listing.'</h3>';
		$message .= 'Email du client: ' . $client_email;
		$message .= '<p>Date à réserver: '.$booking->date.' '.$booking->time.'</p>';
		$message .= '<p>Nombre des personnes: '.$booking->persons.'</p>';
		$message .= '<p>Services commandés: <ul>';
		foreach( $services as $cmd ){
			$message .= '<li>'.$cmd["service"]->name.'</li>';
		}
		$message .= "</ul></p>";
		if( strlen($booking->additional_info) > 0 ){
			$message .= '<strong>Message: </strong>';
			$message .= "<p>$booking->additional_info</p>";
		}
		$message .= "<p><strong>Action à faire:</strong></p>";
		$message .= '<p>'.$confirm_link.' ou '.$cancel_link.' ou <a href="mailto:'.$client_email.'">Ecrire message personnalisé</a> </p>';
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($to, $subject, $message, $headers);

		// Todo: Sending SMS
	}

	// Add content in the end of single listing
	public function dc_listing_single_content_end()
	{
		include( DC_PATH . 'templates/listing-services-template.php');
	}

	// Override template for single listing page
	public function dc_listing_single_template($single_template) {
	    global $post;

	    if ($post->post_type == 'listing') {
	        $single_template = DIANACARTE_THEME_DIR . 'single-listing.php';
	    }
	    return $single_template;
	}

	// Ajax get listing by categories
	public function dc_ajax_listing_by_cats(){
		$term_id = (int) $_REQUEST['category_id'];
		$json = array(
	        'success' => false,
	        'data' => null
	    );
		$listings = Listing::category($term_id);
		if( count($listings) > 0 ){
			$json['success'] = true;
			$json['data'] = $listings;
		}
		wp_send_json( $json );
		die();
	}

	// Enqueue scripts for Interactive Map page
	public function dc_enqueue_map_scripts()
	{
		global $post;
		if( !$post )
			return;
		if( has_shortcode( $post->post_content, 'diana-carte' ) ){
			wp_enqueue_style( 'materialdesignicons', DC_URL . 'assets/font/css/materialdesignicons.min.css' );
			if( DC_PRODUCTION_MODE ){
				// APP CSS
				wp_enqueue_style( 'app', DC_URL . 'map-app/dist/css/app.567dd7e7.css' );
				wp_enqueue_style( 'app-vendor', DC_URL . 'map-app/dist/css/chunk-vendors.6acd2f2a.css' );
				// APP JS
				wp_register_script( // the app build script generated by Webpack.
					'wp-vue-vendors',
					DC_URL . 'map-app/dist/js/chunk-vendors.1a472ebd.js',
					array(),
					false,
					true
				);
				wp_register_script( // the app build script generated by Webpack.
					'wp-vue',
					DC_URL . 'map-app/dist/js/app.61c40f32.js',
					array(),
					false,
					true
				);
				
			}
			else{
				wp_register_script( // the app build script generated by Webpack.
					'wp-vue-vendors',
					'http://localhost:8080/js/chunk-vendors.js',
					array(),
					false,
					true
				);
				wp_register_script( // the app build script generated by Webpack.
					'wp-vue',
					'http://localhost:8080/js/app.js',
					array(),
					false,
					true
				);
			}
			
			wp_enqueue_script( 'wp-vue-vendors' );

			wp_localize_script(
			  'wp-vue', // vue script handle defined in wp_register_script.
			  'wpData', // javascript object that will made availabe to Vue.
			  array( // wordpress data to be made available to the Vue app in 'wpData'
				'rest_url' => untrailingslashit( esc_url_raw( rest_url() ) ), // URL to the REST endpoint.
				'app_path' => $post->post_name, // page where the custom page template is loaded.
				'listings' => Listing::all(),
				'listing_cats' => get_terms('listing_cat', array(
					'taxonomy' => 'listing_cat',
					'orderby' => 'name',
					'hide_empty' => false
				)),
				'listing_location' => Listing::locationList(),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'wp_rest' )
			) );
			wp_enqueue_script( 'wp-vue' );
		}
	}

	// Content of Map page
	public function dianacarte_shortcode_content()
	{
		global $post;
		if( has_shortcode( $post->post_content, 'diana-carte' ) ){
			include( DC_PATH . 'templates/map.php');
		}
	}
	public function dc_map_ajax()
	{
		if( isset( $_POST['search']) ){
			echo $_POST['search'];
			die();
		}
	}

	/* User & Roles management */
	public function add_application_user_roles() {
		add_role( 'customer', 'Diana Carte Client', array( 'read' => true ));
	}

	public function remove_application_user_roles(){
		//remove_role( 'author' );
		//remove_role( 'editor' );
		//remove_role( 'contributor' );
		//remove_role( 'subscriber' );
	}

	public function flush_application_rewrite_rules() {
		$this->manage_user_routes();
		flush_rewrite_rules();
	}

	public function manage_user_routes() {
		add_rewrite_rule( '^user/([^/]+)/?', 'index.php?control_action=$matches[1]', 'top' );
	}

}
// end Main Class


/* lOAD CLASSES AUTOMATICALLY */
$dirs = ['inc', 'models', 'cpt'];
spl_autoload_register( function($class_name) use($dirs) {
	$base_path = plugin_dir_path(__FILE__);
	foreach($dirs as $dir){
		//$file_path = $base_path . $dir . "/". strtolower($class_name) . '.php';
		$file_path = $base_path . $dir . "/". $class_name . '.php';
		if (file_exists($file_path) && is_readable($file_path)) {
			include $file_path;
		}
	}
});

function print_var( $var ){
	echo '<pre>';
		print_r( $var );
	echo '</pre>';
}



function dc_rm_menu_items() {
    if( current_user_can( 'administrator' ) ):
        remove_menu_page( 'edit.php?post_type=cthclaim' );
		remove_menu_page( 'edit.php?post_type=member' );
		remove_menu_page( 'edit.php?post_type=cthinvoice' );
    endif;
}
//add_action( 'admin_menu', 'dc_rm_menu_items' );
function post_object_label() {
	global $wp_post_types;
	$labels = &$wp_post_types['listing']->labels;
	$labels->name = 'Dianacarte Etablissement';
	$labels->singular_name = 'Etablissement';
	$labels->add_new = 'Ajouter Etablissement';
	$labels->add_new_item = 'Ajouter Etablissement';
	$labels->edit_item = 'Modifier Etablissement';
	$labels->new_item = 'Nouveau';
	$labels->view_item = 'Voir Etablissement';
	$labels->search_items = 'Charcher Etablissement';
	$labels->not_found = 'Aucun etablissement trouvé';
	$labels->not_found_in_trash = 'Aucun etablissement trouvé dans la corbeille';
}
add_action( 'init', 'post_object_label' );


/* BOOKING SYSTEM PARENT PLUGIN CODE */
// citybook-addons/posttypes/citybook-booking.php, 
// template-parts/widget-booking.php


/* EXECUTE OUR PLUGIN */

$dianacarte = new DianaCarte();
DC_Rest_Api::init();

// Security Login
require(DC_PATH .'inc/LoginSecurity.php');

// Testing query purpose
// add menu page to admin
if ( defined('WP_DEBUG') && true === WP_DEBUG ) {
	new TestPage();
}

