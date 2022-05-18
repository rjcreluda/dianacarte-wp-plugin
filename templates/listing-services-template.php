<?php 
global $post;

$listing = new Listing( $post->ID );
$services = $listing->services();
if( count($services) > 0 ):
?>
    <div class="list-single-main-item fl-wrap">
      <div class="list-single-main-item-title fl-wrap">
          <h3>
            <?php esc_html_e( 'Nos services', 'dianacarte' ); ?>
          </h3>
      </div>
      <div class="list-single-main-item fl-wrap">
        <?php foreach($services as $service): ?>
          <div class="dashboard-list post-6205 listing type-listing status-publish has-post-thumbnail hentry listing_cat-03-hotels">
            <div class="dashboard-list-inner">
                <span class="service-list-item-price"><?php echo $service->price; ?>€</span>
                                <div class="dashboard-listing-table-image">
                    <a href="<?php echo $service->url; ?>"><img src="" class="respimg wp-post-image" alt="" width="800" height="530"></a>
                </div>
                                <div class="dashboard-listing-table-text">
                    <h4 class="entry-title"><a href="<?php echo $service->url; ?>" rel="bookmark"><?php echo $service->name; ?> [<?php echo $service->serviceType; ?>]</a></h4> 
                    <p>
                      <?php echo implode(',', $service->features); ?>
                    </p>
                    <div class="listing-rating">
                      <span class="viewed-counter">
                          <i class="fa fa-eye"></i> <?php echo $service->viewCount; ?>
                      </span>
                    </div>
                    <!-- <ul class="dashboard-listing-table-opt  fl-wrap">
                        <li><a href="/commander-un-service/">Demander réservation <i class="fa fa-pencil-square-o"></i></a></li> 
                    </ul> -->
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <form action="/reservation/" method="POST">
          <?php wp_nonce_field( 'ask_booking_nonce_action', 'ask_booking_nonce_name' ); ?>
          <input type="hidden" name="listing" value="<?php echo $listing->id; ?>">
          <a href="/reservation/<?php echo $listing->id; ?>" class="btn big-btn color-bg flat-btn lbooking-submit open-booking-modal">
            Demander un reservation<i class="fa fa-angle-right"></i>
          </a>
        </form>
      </div>
    </div>
<?php 
endif;
?>