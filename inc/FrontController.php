<?php 

class FrontController
{
	public function __construct()
	{
		add_action( 'init', array($this, 'manage_user_routes') );
		add_filter( 'query_vars', array( $this, 'manage_user_routes_query_vars' ) );

		// Controller
		add_action( 'template_redirect', array( $this, 'front_controller'));
		add_action( 'template_redirect', array( $this, 'front_reservation_controller'));

		// User Registration
		add_action( 'dc_register_user', array( $this,  'register_user') );
		add_action( 'dc_login_user', array( $this,  'login_user') );
		//add_action( 'init', array($this, 'custom_login') );
		add_filter( 'login_redirect', function( $url, $request, $user ) {
			if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
				if ( $user->has_cap( 'administrator' ) ) {
					$url = admin_url();
				} else {
					$url = home_url();
				}
			}
			return $url;
		}, 10, 3 );

		// Booking
		//add_action( 'dc_new_reservation', array( $this,  'new_reservation') );
	}
	

	public function front_reservation_controller(){
		global $wp_query;
		$control_action = isset ( $wp_query->query_vars['listing_id'] ) ? $wp_query->query_vars['listing_id'] : '';
		if( (int) $control_action != 0 ){
			include DC_PATH . '/templates/login-template.php';
			exit;
		}
	}

	public function manage_user_routes() {
		add_rewrite_rule( '^user/([^/]+)/?', 'index.php?control_action=$matches[1]', 'top' );
		add_rewrite_rule( '/reservation/([^/]+)/?', 'index.php?name=reservation&listing_id=$matches[1]', 'top' );
	}

	public function manage_user_routes_query_vars( $query_vars ) {
		$query_vars[] = 'control_action';
		$query_vars[] = 'listing_id';
		return $query_vars;
	}

	public function front_controller() {
		global $wp_query;
		$control_action = isset ( $wp_query->query_vars['control_action'] ) ? $wp_query->query_vars['control_action'] : '';
		switch ( $control_action ) {
			case 'register':
				do_action( 'dc_register_user' );
				break;
			case 'login':
				do_action( 'dc_login_user' );
    			break;
		}

	}

	public function register_user()
	{
		if ( !is_user_logged_in() ) {
			if( $_POST ){
				$errors = array();
				$user_login = ( isset ( $_POST['user_login'] ) ? $_POST['user_login'] : '' );
				$user_email = ( isset ( $_POST['user_email'] ) ? $_POST['user_email'] : '' );
				$user_password  = ( isset ( $_POST['user_password'] ) ? $_POST['user_password'] : '' );
				// Validating user data
				if ( empty( $user_login ) )
					array_push($errors, __( 'Please enter your username.', 'citybook-add-ons' ) );
				if ( empty( $user_email ) )
					array_push( $errors, __( 'Please enter your email address.', 'citybook-add-ons' ) );
				if ( empty( $user_password ) )
					array_push( $errors, __( 'Please enter your password.', 'citybook-add-ons' ) );
				$sanitized_user_login = sanitize_user( $user_login );
				if ( !empty($user_email) && !is_email( $user_email ) )
					array_push( $errors, __('Please enter valid email.','citybook-add-ons'));
				elseif ( email_exists( $user_email ) )
					array_push( $errors, __('User with this email already registered.','citybook-add-ons'));
				if ( empty( $sanitized_user_login ) || !validate_username( $user_login ) )
					array_push( $errors,  __('Invalid username.','citybook-add-ons') );
				elseif ( username_exists( $sanitized_user_login ) )
				  array_push( $errors, __('Username already exists.','citybook-add-ons') );

				if ( empty( $errors ) ) {
					#$user_pass  = wp_generate_password();
					$user_id    = wp_insert_user( array(
						'user_login' => $sanitized_user_login,
						'user_email' => $user_email,
    					'role' => 'l_customer',
    					'user_pass' => $user_password
    				));
    				if ( !$user_id ) {
						array_push( $errors, __('Registration failed.','wpwa') );
						} 
					else {
						$success_message = __('Registration completed successfully.  
						Please check your email for activation link.','wpwa');
						if ( !is_user_logged_in() ) {
							//wp_set_auth_cookie($user_id, false, is_ssl());
							include DC_PATH . '/templates/login-template.php';
							exit;
						}
					}
				}
			}
			include DC_PATH . '/templates/register-template.php';
			exit;
		}
	}

	public function login_user(){
		if( ! is_user_logged_in() ){
			if ( $_POST ) {
				$errors = array();
				$username = isset ( $_POST['user_login'] ) ? $_POST['user_login'] : '';
				$password = isset ( $_POST['user_password'] ) ? $_POST['user_password'] : '';
				if ( empty( $username ) )
					array_push( $errors, __('Please enter a username.','citybook-add-ons') );
				if ( empty( $password ) )
					array_push( $errors, __('Please enter password.','citybook-add-ons') );
				if(count($errors) > 0){
					include DC_PATH . '/templates/login-template.php';
					exit;
				}
				$credentials = array();
				$credentials['user_login']      = $username;
				$credentials['user_login']      = sanitize_user( $credentials['user_login'] );
				$credentials['user_password']   = $password;
				$credentials['remember']        = false;
				$user = wp_signon( $credentials, false );
				if( is_wp_error( $user ) )
					array_push( $errors, $user->get_error_message() );
				else
					wp_redirect( home_url() );
			}
			include DC_PATH . '/templates/login-template.php';
		}
		else
			wp_redirect( home_url() );
		exit;	
	}

	public function custom_login(){
		global $pagenow;
		//  URL for the HomePage. You can set this to the URL of any page you wish to redirect to.
		$blogHomePage = get_bloginfo('url');
		//  Redirect to the Homepage, if if it is login page. Make sure it is not called to logout or for lost password feature
		if( 'wp-login.php' == $pagenow && $_GET['action']!="logout" && $_GET['action']!="lostpassword") {
			wp_redirect($blogHomePage);
			exit();
		}
	}
}