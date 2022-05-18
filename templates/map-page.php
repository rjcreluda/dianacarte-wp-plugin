<?php 
$query_args = array(
      'post_type'   => 'dc_hotel',
    );
$data = new WP_Query( $query_args );
$hotels = array();
foreach($data->posts as $hotel){
  /*$all_meta = get_post_meta( $hotel->ID );
        echo '<pre>';
        print_r( $all_meta );
        echo '</pre>';*/
  $post_id = $hotel->ID;
  $latitude = get_post_meta( $post_id, '_cth_contact_infos_latitude', true );
  $longitude = get_post_meta( $post_id, '_cth_contact_infos_longitude', true );

  $hotels[] = (object) [
      'name' => $hotel->post_title,
      'address' => get_post_meta( $post_id, '_cth_contact_infos_address', true ),
      'phone' => get_post_meta( $post_id, '_cth_contact_infos_phone', true ),
      'email' => get_post_meta( $post_id, '_cth_contact_infos_email', true ),
      'price_from' => get_post_meta( $post_id, '_cth_price_from', true ),
      'price_to' => get_post_meta( $post_id, '_cth_price_to', true ),
      'latitude' => $latitude,
      'longitude' => $longitude
  ];
}
?>
<div id="dianacarte-app" class="container py-5">
    <div class="row">
        <div class="col-md-4">
          <v-row no-gutters>
            <v-col cols="10">
              <v-text-field 
                placeholder="Que cherchez vous?"
                dense
                clearable>
              </v-text-field>
            </v-col>
            <v-col cols="2" align-self="end">
              <v-btn primary @click="searchFor()">Go</v-btn>
            </v-col>
          </v-row>
          <div class="d-flex flex-wrap mt-3">
            <span class="ma-3" v-for="category in listing_cats">
              <v-btn  
                light
                outlined
                color="indigo"
                rounded
                small>
                {{ category }}
              </v-btn>
            </span>
          </div><br>
          <div class="mt-3">
            <h5>Résultats</h5>
            <v-list>
              <div v-for="listing in listings">
                <v-list-item @click="showPopup(listing)">
                  <v-list-iem-content>
                    <v-list-item-title>{{ listing.name }}</v-list-item-title>
                    <v-list-item-subtitle>
                      lorem ispum
                    </v-list-item-subtitle>
                    </v-list-iem-content>
                </v-list-item>
                <v-divider></v-divider>
              </div>
            </v-list>
          </div>
        </div>
        <div class="col-md-8">
          <div id='map' style='width: 100%; height: 500px;'></div>
        </div>
    </div>
</div>
<script type="text/javascript">
  var dc_listings = <?php echo json_encode($hotels); ?>;
  var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
</script>