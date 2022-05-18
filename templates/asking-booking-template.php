<?php 

if( !isset( $_POST['ask_booking_nonce_name'] ) ){
    return;
}
if ( ! wp_verify_nonce( $_POST['ask_booking_nonce_name'], 'ask_booking_nonce_action' ) ){ 
    die ( '<p class="error">Security checked!, Cheatn huh?</p>' );
}

get_header();

$sb_w = citybook_get_option('blog-sidebar-width','4');

$show_page_header = get_post_meta(get_the_ID(),'_cth_show_page_header',true );

if($show_page_header == 'yes') :
    $show_page_title = get_post_meta(get_the_ID(),'_cth_show_page_title',true );
?>
<!--section -->
<section class="parallax-section" data-scrollax-parent="true" id="sec1">
    <div class="bg par-elem"  data-bg="<?php echo esc_url( get_post_meta( get_the_ID(), '_cth_page_header_bg', true ) );?>" data-scrollax="properties: { translateY: '30%' }"></div>
    <div class="overlay"></div>
    <div class="container">
        <div class="section-title center-align">
            <h3>S'inscrire</h3>
            <?php 
                echo wp_kses_post( get_post_meta(get_the_ID(),'_cth_page_header_intro',true ) );
            ?>
            <span class="section-separator"></span>
        </div>
    </div>
</section>
<!-- section end -->
<?php endif;?>
<!--section -->   
<section class="gray-section" id="sec1">
    <div class="container">
    	<div class="row">
            <?php if( citybook_get_option('blog_layout') ==='left_sidebar' && is_active_sidebar('sidebar-1')):?>
            <div class="col-md-<?php echo esc_attr($sb_w );?> blog-sidebar-column">
                <div class="blog-sidebar box-widget-wrap fl-wrap left-sidebar">
                    <?php 
                        get_sidebar(); 
                    ?>                 
                </div>
            </div>
            <?php endif;?>
            <?php if( citybook_get_option('blog_layout') ==='fullwidth' || !is_active_sidebar('sidebar-1')):?>
            <div class="col-md-12 display-post nosidebar">
            <?php else:?>
            <div class="col-md-<?php echo (12 - $sb_w);?> col-wrap display-post hassidebar">
            <?php endif;?>
                <div class="list-single-main-wrapper fl-wrap" id="sec2">
                	<?php
						if( isset($errors) && count( $errors ) > 0) {
							foreach( $errors as $error ){
								echo '<p class="dc_frm_error">'. $error .'</p>';
							}
						}
					?>
					<form id='registration-form' method='post' action='<?php echo get_site_url() . '/user/register'; ?>'>
						<div class="custom-form">
	                        <label><?php _e( 'Username', 'citybook-add-ons' );?></label>
	                        <input type="text" class="has-icon" name="user_login" placeholder="<?php esc_attr_e( 'Enter your username', 'citybook-add-ons' );?>" value="" required data-msg="<?php esc_attr_e( 'Please enter your username.', 'citybook-add-ons' ); ?>"/>
	                        <label><?php _e( 'Your Email ', 'citybook-add-ons' );?></label>
	                        <input type="email" class="has-icon" name="user_email" placeholder="<?php esc_attr_e( 'Enter your email address', 'citybook-add-ons' );?>" value="" required data-msg="<?php esc_attr_e( 'Please enter your email address.', 'citybook-add-ons' ); ?>"/>
	                        <label><?php _e( 'Your Password ', 'citybook-add-ons' );?></label>
	                        <input type="password" class="has-icon" name="user_password" placeholder="<?php esc_attr_e( 'Enter your password', 'citybook-add-ons' );?>" value="" required data-msg="<?php esc_attr_e( 'Please enter your password.', 'citybook-add-ons' ); ?>"/>
	                        <button type="submit" class="log-submit-btn"><span>S'inscrire maintenant<i class="fa fa-spinner fa-pulse"></i></span></button>
	                    </div>
					</form>
                </div>
                <!-- end list-single-main-wrapper -->
            </div>
            <!-- end display-posts col-md-8 -->

            <?php if( citybook_get_option('blog_layout') === 'right_sidebar' && is_active_sidebar('sidebar-1')):?>
            <div class="col-md-<?php echo esc_attr($sb_w );?> blog-sidebar-column">
                <div class="blog-sidebar box-widget-wrap fl-wrap right-sidebar">
                    <?php 
                        get_sidebar(); 
                    ?>                 
                </div>
            </div>
            <?php endif;?>

        </div>
        <!-- end row -->

    </div>
    <!-- end container -->

</section>
<!-- section end -->

<?php get_footer(); ?>