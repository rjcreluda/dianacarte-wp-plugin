<?php
/**
 * @package CityBook Add-Ons
 * @description A custom plugin for CityBook - Directory Listing WordPress Theme
 * @author CTHthemes - http://themeforest.net/user/cththemes
 * @date 26-06-2019
 * @version 1.3.5
 * @copyright Copyright ( C ) 2014 cththemes.com . All rights reserved.
 * @license GNU General Public License version 3 or later; see LICENSE
 */




get_header(  );


/* Start the Loop */
while ( have_posts() ) : the_post();
// set view count
citybook_addons_set_post_views(get_the_ID());


$headertype = get_post_meta( get_the_ID(), '_cth_headertype', true );
$headerImages = get_post_meta( get_the_ID(), '_cth_headerimgs', true );

$listing_cats = get_the_terms(get_the_ID(), 'listing_cat');
// return ;
?>
<?php 
if($headertype == 'bgimage') : 
    
    $working_hours = citybook_addons_get_working_hours(get_the_ID());


    $bgimg = '';
    if(!empty($headerImages)){
        // reset($headerImages);
        // var_dump(key($headerImages));
        $bgimg = citybook_addons_get_attachment_thumb_link( reset($headerImages), 'full' );

        // var_dump($bgimg);
    } 
?>
<!--  section  --> 
<section class="parallax-section single-par list-single-section" data-scrollax-parent="true" id="sec1">
    <div class="bg par-elem" data-bg="<?php echo esc_url( $bgimg );?>" data-scrollax="properties: { translateY: '30%' }"></div>
    <div class="overlay"></div>
    <div class="bubble-bg"></div>
    <div class="list-single-header absolute-header fl-wrap">
        <div class="container">
            <div class="list-single-header-item">
                <div class="list-single-header-item-opt fl-wrap">
                    <div class="list-single-header-cat fl-wrap">
                        <?php
                        
                        if ( $listing_cats && ! is_wp_error( $listing_cats ) ){
                            // echo '<div class="list-single-header-cat fl-wrap">';
                            foreach( $listing_cats as $key => $cat){

                                echo sprintf( '<a href="%1$s" class="listing-cat">%2$s</a> ',
                                    esc_url( get_term_link( $cat->term_id, 'listing_cat' ) ),
                                    esc_html( $cat->name )
                                );
                            }
                            // echo '</div>';
                        }
                        // end check cat
                        ?>

                        <span> <?php echo $working_hours['statusText'];?><?php if($working_hours['status'] == 'opening') echo ' <i class="fa fa-check"></i>';?></span>
                    </div>
                </div>
                <h2>
                    <?php the_title( ) ;?>
                    <?php if( get_post_meta( get_the_ID(), P_META_PREFIX.'verified', true ) ) echo '<span class="listing-verified tooltipwrap tooltip-center"><i class="fa fa-check"></i><span class="tooltiptext">'.__('Verified','citybook-add-ons').'</span></span>'; ?>
                    <?php if( false == citybook_addons_check_package_single_field( 'hide_author_info' ) ): ?>
                    <span><?php esc_html_e( ' - Hosted By ', 'citybook-add-ons' );?></span><?php the_author_posts_link( );?> 
                    <?php endif; ?>
                    <?php citybook_addons_edit_listing_link(get_the_ID());?>
                </h2>
                
                <?php 
                if(citybook_addons_get_option('listing_event_date') == 'yes'): 
                $levent_date = get_post_meta( get_the_ID(), P_META_PREFIX.'levent_date', true );
                $levent_time = get_post_meta( get_the_ID(), P_META_PREFIX.'levent_time', true );
                if($levent_date != ''): ?>
                <div class="single-event-date"><?php echo sprintf( __( 'Will begin on <span>%s</span> at <span>%s</span>', 'citybook-add-ons' ), date_i18n( get_option( 'date_format' ), strtotime( $levent_date.' '.$levent_time ) ), date_i18n( get_option( 'time_format' ), strtotime( $levent_date.' '.$levent_time ) ) ); ?></div>
                <?php 
                    endif;
                endif; ?>

                <span class="section-separator"></span>
                <?php citybook_addons_get_template_part( 'templates-inner/listing-rating');?>
                <div class="list-post-counter single-list-post-counter"><?php echo citybook_addons_get_likes_button(get_the_ID());?></div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-6">
                        <?php citybook_addons_get_template_part( 'templates-inner/listing-contacts');?>
                    </div>
                    <div class="col-md-6">
                        <?php citybook_addons_get_template_part( 'templates-inner/listing-share');?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--  section end --> 
<?php elseif($headertype == 'carousel'): ?>
<!--  carousel--> 
<div class="list-single-carousel-wrap fl-wrap" id="sec1">
    <?php if($headerImages){ ?>
    <div class="fw-carousel fl-wrap full-height lightgallery">
        <?php
        
            foreach ($headerImages as $id ) {
                ?>
                <!-- slick-slide-item -->
                <div class="slick-slide-item">
                    <div class="box-item">
                        <?php echo wp_get_attachment_image( $id, 'full' ); ?>
                        <a href="<?php echo wp_get_attachment_url( $id );?>" class="gal-link popup-image" data-sub-html=".listing-caption">
                            <i class="fa fa-search"></i>
                            <?php 
                            $image = get_post($id);
                            $image_title = $image->post_title;
                            $image_caption = $image->post_excerpt;
                            ?>
                            <div class="listing-caption">
                                <h3><?php echo esc_html( $image_title ); ?></h3>
                                <?php echo $image_caption; ?>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- slick-slide-item end -->
                <?php
            }

        ?>
        
    </div>
    <div class="swiper-button-prev sw-btn"><i class="fa fa-long-arrow-left"></i></div>
    <div class="swiper-button-next sw-btn"><i class="fa fa-long-arrow-right"></i></div>
    <?php } ?>
</div>
<!--  carousel  end-->
<?php elseif($headertype == 'bgvideo'): // bgvideo
    
    $working_hours = citybook_addons_get_working_hours(get_the_ID());


    $bgimg = '';
    if(!empty($headerImages)){
        reset($headerImages);
        // var_dump(key($headerImages));
        $bgimg = citybook_addons_get_attachment_thumb_link( key($headerImages), 'full' );

        // var_dump($bgimg);
    } 
    $youtube_bg = get_post_meta( get_the_ID(), '_cth_headerbg_youtube', true ); // _cth_headerbg_vimeo - _cth_headerbg_mp4
?>
<!--  section  --> 
<section class="parallax-section single-par list-single-section" data-scrollax-parent="true" id="sec1">
    <div class="media-container video-parallax">
        <div class="bg mob-bg" data-bg="<?php echo esc_url( $bgimg );?>"></div>
    <?php 
        if(get_post_meta( get_the_ID(), '_cth_headerbg_youtube', true ) != '') : 

            $mute = '1';
            $quality = 'highres'; // 'default','small','medium','large','hd720','hd1080'
            $fittobackground = '1';
            $pauseonscroll = '0';
            $loop = '1';
            // Hg5iNVSp2z8
        ?>
        <div  class="background-youtube-wrapper" data-vid="<?php echo esc_attr( get_post_meta( get_the_ID(), '_cth_headerbg_youtube', true ) );?>" data-mt="<?php echo esc_attr( $mute );?>" data-ql="<?php echo esc_attr( $quality );?>" data-ftb="<?php echo esc_attr( $fittobackground );?>" data-pos="<?php echo esc_attr( $pauseonscroll );?>" data-rep="<?php echo esc_attr( $loop );?>"></div>
    <?php 
        elseif(get_post_meta( get_the_ID(), '_cth_headerbg_vimeo', true ) != '') : 
            $dataArr = array();
            $dataArr['video'] = get_post_meta( get_the_ID(), '_cth_headerbg_vimeo', true );
            $dataArr['quality'] = '1080p'; // '4K','2K','1080p','720p','540p','360p'
            $dataArr['mute'] = '1';
            $dataArr['loop'] = '1';
            // 97871257
            ?>
        <div class="video-holder">
            <div  class="background-vimeo" data-opts='<?php echo json_encode( $dataArr );?>'></div>
        </div>
    <?php else : 
        $video_attrs = ' autoplay';
        $video_attrs .=' muted';
        $video_attrs .=' loop';

        // http://localhost:8888/citybook/wp-content/uploads/2018/03/3.mp4
    ?>
        <div class="video-container">
            <video<?php echo esc_attr( $video_attrs );?> class="bgvid">
                <source src="<?php echo esc_url( get_post_meta( get_the_ID(), '_cth_headerbg_mp4', true ) );?>" type="video/mp4">
            </video>
        </div>
    <?php endif; ?>
    </div>

    <div class="overlay"></div>
    <div class="bubble-bg"></div>
    <div class="list-single-header absolute-header fl-wrap">
        <div class="container">
            <div class="list-single-header-item">
                <div class="list-single-header-item-opt fl-wrap">
                    <div class="list-single-header-cat fl-wrap">
                        <?php
                        // $cats = get_the_terms(get_the_ID(), 'listing_cat');
                        if ( $listing_cats && ! is_wp_error( $listing_cats ) ){
                            // echo '<div class="list-single-header-cat fl-wrap">';
                            foreach( $listing_cats as $key => $cat){

                                echo sprintf( '<a href="%1$s" class="listing-cat">%2$s</a> ',
                                    esc_url( get_term_link( $cat->term_id, 'listing_cat' ) ),
                                    esc_html( $cat->name )
                                );
                            }
                            // echo '</div>';
                        }
                        // end check cat
                        ?>

                        <span> <?php echo $working_hours['statusText'];?><?php if($working_hours['status'] == 'opening') echo ' <i class="fa fa-check"></i>';?></span>
                    </div>
                </div>
                <h2>
                    <?php the_title( ) ;?>
                    <?php if( get_post_meta( get_the_ID(), P_META_PREFIX.'verified', true ) ) echo '<span class="listing-verified tooltipwrap tooltip-center"><i class="fa fa-check"></i><span class="tooltiptext">'.__('Verified','citybook-add-ons').'</span></span>'; ?>
                    <?php if( false == citybook_addons_check_package_single_field( 'hide_author_info' ) ): ?>
                    <span><?php esc_html_e( ' - Hosted By ', 'citybook-add-ons' );?></span><?php the_author_posts_link( );?> 
                    <?php endif; ?>
                    <?php citybook_addons_edit_listing_link(get_the_ID());?>
                </h2>
                <?php 
                if(citybook_addons_get_option('listing_event_date') == 'yes'): 
                $levent_date = get_post_meta( get_the_ID(), P_META_PREFIX.'levent_date', true );
                $levent_time = get_post_meta( get_the_ID(), P_META_PREFIX.'levent_time', true );
                if($levent_date != ''): ?>
                <div class="single-event-date"><?php echo sprintf( __( 'Will begin on <span>%s</span> at <span>%s</span>', 'citybook-add-ons' ), date_i18n( get_option( 'date_format' ), strtotime( $levent_date.' '.$levent_time ) ), date_i18n( get_option( 'time_format' ), strtotime( $levent_date.' '.$levent_time ) ) ); ?></div>
                <?php 
                    endif;
                endif; ?>

                <span class="section-separator"></span>
                <?php citybook_addons_get_template_part( 'templates-inner/listing-rating');?>
                <div class="list-post-counter single-list-post-counter"><?php echo citybook_addons_get_likes_button(get_the_ID());?></div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-6">
                        <?php citybook_addons_get_template_part( 'templates-inner/listing-contacts');?>
                    </div>
                    <div class="col-md-6">
                        <?php citybook_addons_get_template_part( 'templates-inner/listing-share');?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--  section end --> 
<?php endif; ?>
<?php
$contents_order = array();
$widgets_order = array();
$term_contents_hide = array();
$term_widgets_hide = array();
if ( $listing_cats && ! is_wp_error( $listing_cats ) ){
    $cat = reset($listing_cats);
    $term_meta = get_term_meta( $cat->term_id, '_cth_term_meta', true );
    if(!empty($term_meta)){
        $contents_order = $term_meta['content-widgets-order'];
        $widgets_order = $term_meta['sidebar-widgets-order'];
        if( isset( $term_meta['content-widgets-hide'] ) ) $term_contents_hide = $term_meta['content-widgets-hide'];
        if( isset( $term_meta['sidebar-widgets-hide'] ) ) $term_widgets_hide = $term_meta['sidebar-widgets-hide'];
    }
}

if(empty($contents_order)) $contents_order = citybook_addons_get_listing_content_order_default();
if(empty($widgets_order)) $widgets_order = citybook_addons_get_listing_widget_order_default();

if(!isset($term_contents_hide['promo_video'])) $term_contents_hide['promo_video'] = '';
if(!isset($term_contents_hide['slider'])) $term_contents_hide['slider'] = '';
if(!isset($term_contents_hide['gallery'])) $term_contents_hide['gallery'] = '';
if(!isset($term_contents_hide['faqs'])) $term_contents_hide['faqs'] = '';

if(!isset($term_widgets_hide['wkhour'])) $term_widgets_hide['wkhour'] = '';
if(!isset($term_widgets_hide['countdown'])) $term_widgets_hide['countdown'] = '';
if(!isset($term_widgets_hide['booking'])) $term_widgets_hide['booking'] = '';
if(!isset($term_widgets_hide['addfeas'])) $term_widgets_hide['addfeas'] = '';
if(!isset($term_widgets_hide['contacts'])) $term_widgets_hide['contacts'] = '';
if(!isset($term_widgets_hide['author'])) $term_widgets_hide['author'] = '';
if(!isset($term_widgets_hide['moreauthor'])) $term_widgets_hide['moreauthor'] = '';
if(!isset($term_widgets_hide['price_range'])) $term_widgets_hide['price_range'] = '';
if(!isset($term_widgets_hide['weather'])) $term_widgets_hide['weather'] = '';
?>
<?php
// if(count($contents_order)){ ?>
<div class="scroll-nav-wrapper fl-wrap">
    <div class="container">
        <nav class="scroll-nav scroll-init">
            <ul>
                <li><a class="act-scrlink" href="#sec1"><?php esc_html_e( 'Header', 'citybook-add-ons' ); ?></a></li>
<?php
    foreach ($contents_order as $widget) {
        switch ($widget) {
            case 'promo_video':
                if ( $term_contents_hide['promo_video'] != 'on' && false == citybook_addons_check_package_field( 'hide_content_video', true)  && get_post_meta( get_the_ID(), '_cth_promovideo_url', true ) !='' ){
                    echo '<li><a href="#sec_promo_video">'.esc_html__( 'Video', 'citybook-add-ons' ).'</a></li>';
                }
                break;
            case 'content':
                    echo '<li><a href="#sec_content">'.esc_html__( 'Details', 'citybook-add-ons' ).'</a></li>';
                break;
            case 'slider':
                if ( $term_contents_hide['slider'] != 'on' && false == citybook_addons_check_package_field( 'hide_content_slider', true) && get_post_meta( get_the_ID(), '_cth_slider_imgs', true ) != '' ) { 
                    echo '<li><a href="#sec_slider">'.esc_html__( 'Slider', 'citybook-add-ons' ).'</a></li>';
                }
                break;   
            case 'gallery':
                if ( $term_contents_hide['gallery'] != 'on' && false == citybook_addons_check_package_field( 'hide_content_gallery', true)  && get_post_meta( get_the_ID(), '_cth_gallery_imgs', true ) != '' ) { 
                    echo '<li><a href="#sec_gallery">'.esc_html__( 'Gallery', 'citybook-add-ons' ).'</a></li>';
                }
                break;    
                        
        }

    } 

    if ( comments_open() || get_comments_number() )  echo '<li><a href="#listing-add-review">'.esc_html__( 'Reviews', 'citybook-add-ons' ).'</a></li>';
    ?>
            </ul>
        </nav>
        <?php if(!is_user_logged_in()): ?>
        <a href="#" class="save-btn logreg-modal-open tooltipwrap" data-message="<?php esc_attr_e( 'Logging in first to bookmark this listing.', 'citybook-add-ons' ); ?>"><i class="fa fa-bookmark-o"></i><?php esc_html_e( ' Save ', 'citybook-add-ons' ); ?><span class="tooltiptext"><?php _e( 'Bookmark', 'citybook-add-ons' ); ?></span></a>
        <?php elseif( citybook_addons_already_bookmarked( get_the_ID() ) ): ?>
        <a href="javascript:void(0);" class="save-btn tooltipwrap" data-id="<?php the_ID(); ?>"><i class="fa fa-bookmark"></i><?php esc_html_e( ' Saved ', 'citybook-add-ons' ); ?><span class="tooltiptext"><?php _e( 'Bookmarked', 'citybook-add-ons' ); ?></span></a>
        <?php else: ?>
        <a href="#" class="save-btn bookmark-listing-btn tooltipwrap" data-id="<?php the_ID(); ?>"><i class="fa fa-bookmark-o"></i><?php esc_html_e( ' Save ', 'citybook-add-ons' ); ?><span class="tooltiptext"><?php _e( 'Bookmark', 'citybook-add-ons' ); ?></span></a>
        <?php endif; ?>
    </div>
</div>
<?php
// }
?>
<!--  section   --> 
<section class="gray-section no-top-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-8">

                <!-- list-single-main-wrapper -->
                <div class="list-single-main-wrapper fl-wrap" id="sec2">

                    <?php 

                    
                    do_action( 'citybook_addons_listing_content_before');
                    

                    ?>

                    <?php 
                    

                    citybook_addons_breadcrumbs('gradient-bg  fl-wrap');?>

                    <?php 
                    if ($headertype == 'carousel' ) {
                        citybook_addons_get_template_part( 'template-parts/content-head-info');
                    }
                    ?>
                    <?php 
                    // loop throw content widgets order
                    // if(count($contents_order)){
                        foreach ($contents_order as $key => $widget) {
                            do_action( 'citybook-addons-single-content-order', get_the_ID(), $key );
                            switch ($widget) {
                                case 'promo_video':
                                    if ( $term_contents_hide['promo_video'] != 'on' &&  false == citybook_addons_check_package_field( 'hide_content_video', true) && get_post_meta( get_the_ID(), '_cth_promovideo_url', true ) !='' ) {
                                        $promo_bg = get_post_meta( get_the_ID(), '_cth_promo_bg', true );
                                    
                                        if(empty( $promo_bg )){ ?>
                                        <div class="list-single-main-item fl-wrap" id="sec_promo_video">
                                            <div class="list-single-main-item-title fl-wrap">
                                                <h3><?php esc_html_e( 'Promo Video', 'citybook-add-ons' ); ?></h3>
                                            </div>
                                            <div class="iframe-holder fl-wrap">
                                                <div class="resp-video">
                                                    <?php echo wp_oembed_get(esc_url(get_post_meta(get_the_ID(), '_cth_promovideo_url', true) )); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php }else{ ?>
                                        <div class="list-single-main-media fl-wrap" id="sec_promo_video">
                                            <?php echo wp_get_attachment_image( reset($promo_bg), 'featured', false, array('class'=>'respimg') );?>
                                            <a href="<?php echo esc_url( get_post_meta( get_the_ID(), '_cth_promovideo_url', true ) ); ?>" class="promo-link gradient-bg image-popup"><i class="fa fa-play"></i><span><?php esc_html_e( 'Promo Video', 'citybook-add-ons' ); ?></span></a>
                                        </div>
                                        <?php } 
                                    }
                                    // end if promo_video 
                                    break;
                                // end promo_video
                                case 'content':
                                    ?>
                                    <div class="list-single-main-item fl-wrap" id="sec_content">
                                        <?php the_content(); 
                                        $features = get_the_terms(get_the_ID(), 'listing_feature');
                                        if ( $features && ! is_wp_error( $features ) ){ 
                                            $feature_group = array();
                                            foreach( $features as $key => $term){
                                                if(citybook_addons_get_option('feature_parent_group') == 'yes'){
                                                    if($term->parent){
                                                        if( !isset($feature_group[$term->parent]) || !is_array($feature_group[$term->parent]) ) $feature_group[$term->parent] = array();
                                                        $feature_group[$term->parent][$term->term_id] = $term->name;
                                                    }else{
                                                        if(!isset($feature_group[$term->term_id])) $feature_group[$term->term_id] = $term->name;
                                                    }
                                                }else{
                                                    if(!isset($feature_group[$term->term_id])) $feature_group[$term->term_id] = $term->name;
                                                }
                                                    
                                            }

                                            // echo '<pre>';
                                            // var_dump($feature_group);

                                            // feature_parent_group


                                        ?>
                                            <span class="fw-separator"></span>
                                            <div class="list-single-main-item-title fl-wrap">
                                                <h3><?php esc_html_e( 'Listing Features', 'citybook-add-ons' ); ?></h3>
                                            </div>
                                            <div class="listing-features fl-wrap">
                                                <ul class="fea-parent">
                                            <?php
                                                foreach( $feature_group as $tid => $tvalue){
                                                    // var_dump($tvalue);
                                                    if( is_array( $tvalue ) && count( $tvalue ) ){
                                                        $term = get_term_by( 'id', $tid , 'listing_feature' );
                                                        // var_dump($term);
                                                        if($term){
                                                            $term_meta = get_term_meta( $term->term_id, P_META_PREFIX.'term_meta', true );

                                                            echo sprintf( '<li class="fea-has-children"><a href="%1$s" class="album-cat">%2$s</a><ul class="fea-children">',
                                                                esc_url( get_term_link( $term->term_id, 'listing_feature' ) ),
                                                                isset($term_meta['icon_class'])? '<i class="'.$term_meta['icon_class'].'"></i>' . esc_html( $term->name ) : esc_html( $term->name )
                                                            );

                                                            foreach ($tvalue as $id => $name) {
                                                                $term_meta = get_term_meta( $id, P_META_PREFIX.'term_meta', true );

                                                                echo sprintf( '<li><a href="%1$s" class="album-cat">%2$s</a></li>',
                                                                    esc_url( get_term_link( $id, 'listing_feature' ) ),
                                                                    isset($term_meta['icon_class'])? '<i class="'.$term_meta['icon_class'].'"></i>' . esc_html( $name ) : esc_html( $name )
                                                                );
                                                            }

                                                            echo '</ul></li>';
                                                        }
                                                        
                                                    }else{
                                                        $term_meta = get_term_meta( $tid, P_META_PREFIX.'term_meta', true );

                                                        echo sprintf( '<li><a href="%1$s" class="album-cat">%2$s</a></li>',
                                                            esc_url( get_term_link( $tid, 'listing_feature' ) ),
                                                            isset($term_meta['icon_class'])? '<i class="'.$term_meta['icon_class'].'"></i>' . esc_html( $tvalue ) : esc_html( $tvalue )
                                                        );
                                                    }
                                                    
                                                        
                                                }


                                                // foreach( $features as $key => $term){

                                                //     echo '<pre>';
                                                //     var_dump($term);
                                                    
                                                //     $term_meta = get_term_meta( $term->term_id, '_cth_term_meta', true );

                                                //     echo sprintf( '<li><a href="%1$s" class="album-cat">%2$s</a> ',
                                                //         esc_url( get_term_link( $term->term_id, 'listing_feature' ) ),
                                                //         isset($term_meta['icon_class'])? '<i class="'.$term_meta['icon_class'].'"></i>' . esc_html( $term->name ) : esc_html( $term->name )
                                                //     );
                                                // }
                                            ?>
                                                </ul>
                                            </div>
                                        <?php
                                        }
                                        // end features check
                                        ?>
                                        <?php if( false == citybook_addons_check_package_field('hide_tags', true ) && get_the_tags( ) ) :?>
                                            <span class="fw-separator"></span>
                                            <div class="list-single-main-item-title fl-wrap">
                                                <h3><?php esc_html_e( 'Tags', 'citybook-add-ons' ); ?></h3>
                                            </div>
                                            <div class="list-single-tags tags-stylwrap">
                                                <?php the_tags('','','');?>                                                                              
                                            </div>
                                        <?php endif;?>
                                    </div>
                                    <?php
                                    break;
                                // end content
                                case 'slider':
                                  
                                    if ( $term_contents_hide['slider'] != 'on' && false == citybook_addons_check_package_field( 'hide_content_slider', true) && $slider_imgs = get_post_meta( get_the_ID(), '_cth_slider_imgs', true ) ) { 
                                    ?>
                                    <div class="list-single-main-media fl-wrap" id="sec_slider">
                                        <div class="single-slider-wrapper fl-wrap">
                                            <div class="single-slider fl-wrap"  >
                                                <?php
                                                foreach ($slider_imgs as $id ) {
                                                    ?>
                                                    <div class="slick-slide-item"><?php echo wp_get_attachment_image( $id, 'lslider' ); ?></div>
                                                    <?php
                                                }
                                                ?> 
                                            </div>
                                            <div class="swiper-button-prev sw-btn"><i class="fa fa-long-arrow-left"></i></div>
                                            <div class="swiper-button-next sw-btn"><i class="fa fa-long-arrow-right"></i></div>
                                        </div>
                                    </div>      
                                    <?php
                                    }
                                    // end if content_galleryimgs
                
                                    break;
                                // end slider
                                case 'gallery':
                                  
                                    if ( $term_contents_hide['gallery'] != 'on' &&  false == citybook_addons_check_package_field( 'hide_content_gallery', true) && $gallery_imgs = get_post_meta( get_the_ID(), '_cth_gallery_imgs', true ) ) { 
                                    ?>
                                    <div class="list-single-main-item fl-wrap" id="sec_gallery">
                                        <div class="list-single-main-item-title fl-wrap">
                                            <h3><?php esc_html_e( 'Gallery - Photos', 'citybook-add-ons' ); ?></h3>
                                        </div>
                                        <!-- gallery-items   -->
                                        <div class="gallery-items medium-pad  list-single-gallery three-columns lightgallery">
                                            <div class="grid-sizer"></div>
                                            <?php
                                            foreach ($gallery_imgs as $id ) {
                                                ?>
                                                <!-- 1 -->
                                                <div class="gallery-item">
                                                    <div class="grid-item-holder">
                                                        <div class="box-item">
                                                            <?php echo wp_get_attachment_image( $id, 'lgallery' ); ?>
                                                            <a href="<?php echo wp_get_attachment_url( $id );?>" data-sub-html=".listing-caption" class="gal-link popup-image">
                                                                <i class="fa fa-search"></i>
                                                                <?php 
                                                                $image = get_post($id);
                                                                $image_title = $image->post_title;
                                                                $image_caption = $image->post_excerpt;
                                                                ?>
                                                                <div class="listing-caption">
                                                                    <h3><?php echo esc_html( $image_title ); ?></h3>
                                                                    <?php echo $image_caption; ?>
                                                                </div>

                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- 1 end -->
                                                <?php
                                            }
                                            ?> 
                                        </div>
                                        <!-- end gallery items -->                                 
                                    </div>
                                    <!-- list-single-main-item end -->      
                                    <?php
                                    }
                                    // end if content_galleryimgs
                                    break;
                                // end gallery
                                case 'faqs':

                                    if( $term_contents_hide['faqs'] != 'on' &&  false == citybook_addons_check_package_field( 'hide_faqs_opt', true) && $lFAQs = get_post_meta( get_the_ID(), '_cth_lfaqs', true ) ){
                                    
                                    ?>
                                    <div class="accordion accordion-wrap" id="sec_faqs">
                                    <?php
                                        $key = 0;
                                        foreach ($lFAQs as $lfaq) {
                                            ?>
                                        <a class="toggle<?php if($key == 0) echo ' act-accordion';?>" href="#"> <?php echo esc_html($lfaq['title']); ?> <i class="fa fa-angle-down"></i></a>
                                        <div class="accordion-inner<?php if($key == 0) echo ' visible';?>">
                                            <?php echo $lfaq['content']; ?>
                                        </div>
                                        <?php
                                            $key++;
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    }
                                    break;
                                // end faqs
                            }
                            // end switch
                        }
                        // end foreach
                    // }
                    // end check contents order
                    ?>

                    <?php 
                    citybook_addons_post_nav( 'listing_cat' );

                    do_action( 'citybook_addons_listing_content_after');
                    ?>                      
                    <?php
                    // If comments are open or we have at least one comment, load up the comment template.
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                    ?> 
                    <?php 
                    do_action( 'citybook_addons_listing_content_end');
                    ?>                        
                </div>
                <?php 
                // endwhile;
                // end the loop
                ?>
            </div>
            <!--box-widget-wrap -->
            <div class="col-md-4">
                <div class="box-widget-wrap">
                    <?php
                    do_action( 'citybook_addons_listing_widgets_before');

                    // echo do_shortcode( '[ea_bootstrap worker="1" location="1" service="1"]' );
                    
                    // loop throw content widgets order
                    // if(count($widgets_order)){
                        foreach ($widgets_order as $key => $widget) {
                            do_action( 'citybook-addons-single-widget-order', get_the_ID(), $key );
                            switch ($widget) {
                                case 'wkhour':
                                    if ( $term_widgets_hide['wkhour'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_wkhour_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-wkhour');
                                    }
                                    break;
                                // end wkhour
                                case 'countdown':
                                    if ( $term_widgets_hide['countdown'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_counter_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-counter');
                                    }
                                    break;
                                // end countdown
                                case 'booking':
                                    if ( $term_widgets_hide['booking'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_booking_form_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-booking');
                                    }
                                    break;
                                // end booking
                                case 'addfeas':
                                    if ( $term_widgets_hide['addfeas'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_addfeatures_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-addfeas');
                                    }
                                    break;
                                // end addfeas
                                case 'contacts':
                                    if ( $term_widgets_hide['contacts'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_contacts_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-contacts');
                                    }
                                    break;
                                // end contacts
                                case 'author':
                                    if ( $term_widgets_hide['author'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_author_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-author');
                                    }
                                    break;
                                // end author
                                case 'moreauthor':
                                    if ( $term_widgets_hide['moreauthor'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_moreauthor_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-moreauthor');
                                    }
                                    break;
                                // end moreauthor
                                case 'price_range':
                                    if ( $term_widgets_hide['price_range'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_pricerange_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-price_range');
                                    }
                                    break;
                                // end price_range
                                case 'weather':
                                    if ( $term_widgets_hide['weather'] != 'on' && false == citybook_addons_check_package_single_field( 'hide_weather_widget' ) ) {
                                        citybook_addons_get_template_part( 'template-parts/widget-weather');
                                    }
                                    break;
                                // end price_range

                                    
                            }
                            // end switch
                        }
                        // end foreach
                    // }
                    // end check widgets order

                    do_action( 'citybook_addons_listing_widgets_after');

                    ?>
                    
                     
                </div>
            </div>
            <!--box-widget-wrap end -->
        </div>
    </div>
</section>
<!--  section  end--> 
<div class="limit-box fl-wrap"></div>
<?php 
citybook_addons_get_template_part( 'template-parts/mobile-btns');
?>
<?php 
if ( false == citybook_addons_check_package_single_field( 'hide_pricerange_widget' ) )
    citybook_addons_get_template_part('template-parts/listing-claim-modal');

endwhile;
// end the loop


get_footer(  );