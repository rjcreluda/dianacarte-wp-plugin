<?php 
if( isset($_POST['ask_booking_nonce_name']) ){
  if ( wp_verify_nonce( $_POST['ask_booking_nonce_name'], 'ask_booking_nonce_action' ) ){
    $message = 'post ok';
    $message = $_POST;
    //$safe['age'] = absint( $_POST['age'] );
    /*wp_redirect( 'http://example.com' );
    exit;*/
    // $login    = esc_sql( 'hacker' ); // for string
    $slid = $_POST['slid'];
    $booking_title = __( '%1$s booking from %2$s', 'citybook-add-ons' );
    $booking_datas = array();
    $booking_metas_loggedin = array();
    if(isset($_POST['lb_name']) && isset($_POST['lb_email']) ){
        $booking_datas['post_title'] = sprintf( $booking_title, get_the_title( $slid ), $_POST['lb_name'] );
    }
    else{
      $current_user = wp_get_current_user();
      if(0 != $current_user->ID){ //logged in user and invalid form
        $booking_datas['post_title'] = sprintf( $booking_title, get_the_title( $slid ), $current_user->display_name );

        $booking_metas_loggedin['user_id'] = $current_user->ID;
        $booking_metas_loggedin['lb_name'] = $current_user->display_name;
        $booking_metas_loggedin['lb_email'] = $current_user->user_email;
        $booking_metas_loggedin['lb_phone'] = get_user_meta($current_user->ID,  P_META_PREFIX.'phone', true );
      }
    }
    $booking_datas['post_content'] = '';
    $booking_datas['post_status'] = 'publish';
    $booking_datas['post_type'] = 'lbooking';

    do_action( 'citybook_addons_insert_booking_before', $booking_datas );

    $booking_id = wp_insert_post($booking_datas ,true );

    if (!is_wp_error($booking_id)) {
      $meta_fields = array(
          // 'listing_id' => 'text', listing_id will be set manually
          'lb_name'               => 'text',
          'lb_email'              => 'text',
          'lb_phone'              => 'text',

          'lb_quantity'           => 'text',
          'lb_date'               => 'text',
          'lb_time'               => 'text',
          'lb_add_info'           => 'text',
          'lb_booking_type'       => 'text',
      );
      $booking_metas = array();
      foreach($meta_fields as $field => $ftype){
          if(isset($_POST[$field])) $booking_metas[$field] = $_POST[$field] ;
          else{
              if($ftype == 'array'){
                  $booking_metas[$field] = array();
              }else{
                  $booking_metas[$field] = '';
              }
          } 
      }
      $booking_metas['listing_id'] = $slid;
      $booking_metas['lb_status'] = 'pending';
      $booking_metas['user_id'] = 0; // for non logged in user
      // merge with logged in customser data
      $booking_metas = array_merge($booking_metas,$booking_metas_loggedin);
      foreach ($booking_metas as $key => $value) {
        if ( !update_post_meta( $booking_id, P_META_PREFIX.$key,  $value  ) ) {
            $json['data'][] = sprintf(__('Insert booking %s meta failure or existing meta value','citybook-add-ons'),$key);
            // wp_send_json($json );
        }
      }
      // Managin price for the booking
      $room = new Room( intval($_POST['lb_room']) );
      $room_qty = intval($_POST["lb_room_quantity"]);
      $total_price = $room->price * $room_qty;
      $listing_price = get_post_meta( $slid, P_META_PREFIX.'price_from', true );
      update_post_meta( $booking_id, '_price',  $listing_price );
      $message = __( 'Your booking is received. The listing author will contact with you soon.<br>You can also login with your email to manage bookings.<br>Thank you.', 'citybook-add-ons' );
      // Insert booked room to database
      $item_booked = new RoomItemBooking([
         'booking_id' => $booking_id,
         'item_id' => $room->id,
         'item_type' => $room->type,
         'item_qty' => $room_qty,
         'item_price' => $total_price
      ]);
      $item_booked->save();
      do_action( 'citybook_addons_insert_booking_after', $booking_id );
    }
  }
  else{
    $message = 'nonce false';
  }
}
/*if( isset($message) ){
  echo '<pre>';
    var_dump($message);
  echo '</pre>';
}*/
?>
<table class="form-table">
  <tr>
    <th>#</th>
    <th>Chambre</th>
    <th>Type</th>
    <th>Prix</th>
  </tr>
  <?php 
  foreach( $products->posts as $product):
    $room = new Room($product);
  ?>
  <tr>
    <td></td>
    <td><a href="<?php echo get_the_permalink( $product ); ?>"><?php echo $room->name; ?></a></td>
    <td><?php echo $room->type; ?></td>
    <td><?php echo $room->price; ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<div>
  <?php 
    // Add a nonce field so we can check for it later.
    //wp_nonce_field( 'ask_booking_nonce_action', 'ask_booking_nonce_name' );
   ?>
<button class="btn big-btn color-bg flat-btn lbooking-submit open-booking-modal">
  Demander un reservation<i class="fa fa-angle-right"></i>
</button>
</div>

<!-- Begin Modal -->
<div class="ctb-modal-wrap ctb-modal ctb-modal-lg" id="ctb-new-campaign-modal">
    <div class="ctb-modal-holder">
        <div class="ctb-modal-inner">
            <div class="ctb-modal-close"><i class="fa fa-times"></i></div>
            <h3>Nouveau reservation</h3>
            <div class="ctb-modal-content">
              <form method="post" action="" class="custom-form">
              <!-- <form class="listing-booking-form custom-form"> -->
                <?php wp_nonce_field( 'ask_booking_nonce_action', 'ask_booking_nonce_name' ); ?>
                <fieldset>
                <?php if ( !is_user_logged_in() ) { ?>
                    <label><i class="fa fa-user-o"></i></label>
                    <input name="lb_name" class="has-icon" type="text" placeholder="<?php esc_attr_e( 'Your Name*', 'citybook-add-ons' ); ?>" value="" required="required">
                    <div class="clearfix"></div>
                    <label><i class="fa fa-envelope-o"></i></label>
                    <input name="lb_email" class="has-icon" type="text" placeholder="<?php esc_attr_e( 'Email Address*', 'citybook-add-ons' ); ?>" value="" required="required">
                    <label><i class="fa fa-phone"></i></label>
                    <input name="lb_phone" class="has-icon" type="text" placeholder="<?php esc_attr_e( 'Phone', 'citybook-add-ons' ); ?>" value="">
                <?php } ?>
                    <div class="quantity fl-wrap clearfix">
                        <span><i class="fa fa-user-plus"></i><?php esc_html_e( 'Persons: ', 'citybook-add-ons' ); ?></span>
                        <div class="quantity-item" data-min="1">
                            <input type="button" value="-" class="minus">
                            <input type="text" name="lb_quantity" value="1" class="qty" size="4" required="required">
                            <input type="button" value="+" class="plus">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">                               
                            <label><i class="fa fa-calendar-check-o"></i></label>
                            <input name="lb_date" type="text" placeholder="<?php esc_attr_e( 'Date', 'citybook-add-ons' ); ?>" class="datepicker has-icon" data-large-mode="true" data-large-default="true" data-max-year="2050" data-min-year="2016" value=""  required="required">
                        </div>
                        <div class="col-md-6"> 
                            <label><i class="fa fa-clock-o"></i></label>
                            <input name="lb_time"  type="text" placeholder="<?php esc_attr_e( 'Time', 'citybook-add-ons' ); ?>" class="timepicker has-icon" data-init-set="true" value="" required="required">
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <label>Chambre</label>
                        <select name="lb_room">
                          <?php 
                          foreach( $products->posts as $product):
                            $room = new Room($product);
                            echo '<option value="'.$room->id.'">';
                              echo $room->name . ' ' . $room->price .' €';
                            echo '</option>';
                          endforeach;
                          ?>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label>Nombre</label>
                        <div class="quantity-item" data-min="1">
                            <input type="button" value="-" class="minus">
                            <input type="text" name="lb_room_quantity" value="1" class="qty" size="4" required="required">
                            <input type="button" value="+" class="plus">
                        </div>
                      </div>
                    </div>
                    
                    <textarea name="lb_add_info" cols="40" rows="1" placeholder="<?php esc_attr_e( 'Additional Information:', 'citybook-add-ons' ); ?>"></textarea>
                </fieldset>
                <input type="hidden" name="slid" value="<?php echo get_the_ID() ?>">
                <button class="btn big-btn color-bg flat-btn lbooking-submit" type="submit"><?php esc_html_e( 'Book Now', 'citybook-add-ons' ); ?><i class="fa fa-angle-right"></i></button>
            </form>
            </div>
            <!-- end modal-content -->
        </div>
    </div>
</div>
<!-- end modal --> 

<style>
  .scroll-nav-wrapper.fl-wrap.scroll-to-fixed-fixed{
    z-index: 1 !important;
    position: initial !important;
  }
</style>

<script type="text/javascript">
  ( function($){
    $('.open-booking-modal').click( (e) => {
      e.preventDefault();
      $("#ctb-new-campaign-modal").fadeIn();
      $("html, body").addClass("hid-body");
    });
  })(jQuery);
</script>