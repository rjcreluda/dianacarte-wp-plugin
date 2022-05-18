<?php 
// Get current post category
$products = get_the_terms( $post, 'listing_cat' );

$slugs = array('02-restaurants', '03-hotels');

if( $products != null && !is_wp_error( $products ) ): 
  switch( $products[0]->slug ){
    case '02-restaurants':
      $post_type = 'dc_menus';
      $meta_key = 'dc_menus_resto_id';
      $section_title = 'Nos Menus';
      $template_part = DC_PATH . 'templates/parts/menus_list_table.php';
      break;
    case '03-hotels':
      $post_type='dc_chambre';
      $meta_key = 'dc_room_hotel_id';
      $section_title = 'Nos Chambres';
      $template_part = DC_PATH . 'templates/parts/room_list_table.php';
      break;
	default:
		$post_type = '';
		break;
  }
  if( in_array($products[0]->slug, $slugs) ):
  // Get product lists for that post
  $query_args = array(
    'post_type'   => $post_type,
    'meta_query'  => array(
      array(
        'key'   => $meta_key,
        'value' => $post->ID
      )
    )
  );

  $products = new WP_Query( $query_args );
  //print_r($products);
  if( count($products->posts) > 0 ):
?>
    <div class="list-single-main-item fl-wrap">
      <div class="list-single-main-item-title fl-wrap">
          <h3>
            <?php esc_html_e( $section_title, 'dianacarte' ); ?>
          </h3>
      </div>
      <div class="list-single-main-item fl-wrap">
        <?php include($template_part); ?>
      </div>
    </div>
<?php 
  endif;
  endif;
endif;
?>