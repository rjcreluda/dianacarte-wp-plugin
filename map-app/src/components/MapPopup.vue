<template>
  <div class="map-popup-wrap">
    <div class="map-popup">
      <div class="infoBox-close">
        <i class="fa fa-times" aria-hidden="true"></i>
      </div>
      <div class="map-popup-category" v-if="listing.cats">
        {{ listing.cats }}
      </div>
      <a :href="listing.url" class="listing-img-content fl-wrap">
          <img :src="listing.thumbnail_url" :alt="listing.name">
          <div class="listing-featured" v-if="listing.featured">Featured</div>
      </a>
  
      <div class="listing-content fl-wrap">
        <div v-if="listing.rating" class="card-popup-raining map-card-rainting" :data-rating="listing.rating.rating" data-stars="<?php echo esc_attr( citybook_addons_get_option('rating_base') ); ?>"> 
          <span class="map-popup-reviews-count">{{ listing.rating.count }}</span> 
        </div> 
          <div class="listing-title fl-wrap"> 
              <h4><a :href="listing.url">{{listing.name}}</a></h4> 
              <div class="pricerange fl-wrap" v-if="listing.price.min">
                  <span class="lprice-from">{{ listing.price.min }}</span>
                  <span class="lprice-to" v-if="listing.price.max">{{ listing.price.min }}</span>
              </div>
              <span class="map-popup-contact-infos infos-address" v-if="listing.contact.address">  <i class="fa fa-map-marker" aria-hidden="true"></i>{{ listing.contact.address }}
              </span>  
              <span class="map-popup-contact-infos infos-phone" v-if="listing.contact.phone"> 
                  <i class="fa fa-phone" aria-hidden="true"></i>
                  <a :href="'tel:' + listing.contact.phone">{{ listing.contact.phone }}</a> 
              </span>  
          </div> 
          <div class="geodir-category-location listing-item-footer">
              <span :class="'wkhour-status wkhour-'+listing.workhour.status" v-if="listing.workhour">{{listing.workhour.statusText}}</span>
          </div>
      </div> 
    </div> 
  </div> 
</template>

<script>
  export default {
    name: 'MapPopup',
    props: ['listing']
  }
</script>



<style scoped>
  .map-popup-wrap {
  display: block;
  width: 300px !important;
  position: relative; }

.map-popup {
  display: block;
  width: 100%;
  position: relative;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 9px 16px rgba(58, 87, 135, 0.15);
  background: #464646; }

.map-popup-wrap:before {
  top: 100%;
  left: 50%;
  border: solid transparent;
  content: " ";
  height: 0;
  width: 0;
  position: absolute;
  pointer-events: none;
  margin-top: -1px;
  z-index: 20; }

.map-popup-wrap:before {
  border-color: transparent;
  border-top-color: #fff;
  border-width: 15px;
  margin-left: -15px; }

.map-popup, .map-popup-wrap {
    display: block;
    position: relative;
}

.map-popup img {
  width: 100%;
  height: auto;
  transform: translateZ(0);
  transition: all 2000ms cubic-bezier(0.19, 1, 0.22, 1) 0ms; }

  .map-popup {
    width: 100%;
    overflow: hidden;
    border-radius: 10px;
    -webkit-box-shadow: 0 9px 16px rgba(58,87,135,.15);
    box-shadow: 0 9px 16px 
rgba(58,87,135,.15);
background:
    #464646;
}
.map-popup-category {
    position: absolute;
    top: 20px;
    left: 20px;
    font-weight: 500;
    color: 
#fff;
z-index: 20;
padding: 10px 12px;
border-radius: 4px;
-webkit-box-shadow: 0 0 0 3px hsla(0,0%,100%,.2);
box-shadow: 0 0 0 3px
    hsla(0,0%,100%,.2);
}
.listing-img-content {
    overflow: hidden;
    display: block;
    min-height: 100px;
}
.listing-content {
    background: 
    #fff;
    padding: 25px 20px;
    z-index: 20;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}
.listing-item-footer {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
}
.map-popup-contact-infos {
    color: 
    #666;
    font-size: 13px;
    width: 100%;
    margin-bottom: 5px;
    display: block;
}
</style>