<table class="form-table">
  <tr>
    <th colspan="2">Menus</th>
    <th>Prix (€)</th>
  </tr>
  <?php foreach( $products->posts as $product): ?>
  <tr>
    <td>
      <img src="<?php echo $product->dc_menus_image; ?>" alt="img" width="80">
    </td>
    <td>
      <?php echo $product->post_title; ?>
    </td>
    <td>
      <?php echo $product->dc_menus_price; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<button class="btn big-btn color-bg flat-btn lbooking-submit open-booking-modal">
  Demander un reservation<i class="fa fa-angle-right"></i>
</button>

<!-- Begin Modal -->
<div class="ctb-modal-wrap ctb-modal ctb-modal-lg" id="ctb-new-campaign-modal">
    <div class="ctb-modal-holder">
        <div class="ctb-modal-inner">
            <div class="ctb-modal-close"><i class="fa fa-times"></i></div>
            <h3>Nouveau reservation</h3>
            <div class="ctb-modal-content">
                BOOKING
                <form class="new-ad-canpaign-form custom-form" action="" method="post">
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