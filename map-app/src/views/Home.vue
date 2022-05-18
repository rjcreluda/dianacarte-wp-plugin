<template>
  <div fluid class="grey lighten-5">
    <v-row no-gutters>
      <v-col>
        <v-card class="pa-2 rounded-0">
          <div class="text-body-1">
            <v-btn 
              text
              icon
              @click.stop="drawer = !drawer">
              <v-icon>mdi-apps</v-icon>
            </v-btn>
            <v-icon>mdi-magnify</v-icon> Que cherchez vous?
          </div>
          <div>
            <v-btn v-for="category in listing_cats" :key="category.term_id"
              @click.stop="get_listings_by_category(category)"
              class="ma-1"
              light
              outlined
              color="pink"
              rounded
              small>
              {{ category.name }} ({{ category.count}})
            </v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-sheet
        height="600"
        class="overflow-hidden"
        style="position: relative;">
      <v-container class="fill-height pa-0" fluid>
        <v-row align="center"
        justify="center" no-gutters class="fill-height">
          <v-col class="fill-height">
            <DianaMap :listings="listings" :center="mapCenter"></DianaMap>
          </v-col>
        </v-row>
      </v-container>
      <v-navigation-drawer
        width="30%"
        style="z-index: 1000;"
        v-model="drawer"
        absolute
        
        hide-overlay
      >
        <v-card class="pa-2">
          <div class="d-flex justify-space-between text-body-1 pa-2">
            <div>{{ sidebar_title }}</div>
            <v-spacer></v-spacer>
            <div v-show="loading">
              {{ statusMessage }}
            </div>
          </div>
          
          <v-list dense>
            <div v-for="listing in listings" :key="listing.id">
              <ListingItem :listing="listing" @showInMap="showInfo($event)" @askDirection="showDirection($event)" />
            </div>
          </v-list>
        </v-card>
      </v-navigation-drawer>
    </v-sheet>
  </div>
</template>

<script>
// @ is an alias to /src
//import DianaCarte from '@/components/DianaCarte.vue'

import DianaMap from '@/components/DianaMap.vue'
import ListingItem from '@/components/ListingItem.vue'

export default {
  name: 'Home',
  components: {
    //DianaCarte,
    ListingItem,
    DianaMap
  },
  data: () => ({
    drawer: true,
    listing_cats: window.wpData.listing_cats,
    listings: window.wpData.listings,
    sidebar_title: 'Les plus réçents',
    sidebar_default: true,
    post_url: window.wpData.ajax_url,
    statusMessage: '',
    loading: false,
    mapCenter: [49.29149481988657, -12.277539696362235]
  }),
  methods: {
    get_listings_by_category(category){
      this.statusMessage = 'Loading ...'
      if( !this.drawer )
        this.drawer = true
      this.loading = true
      this.sidebar_default = false;
      let term_id = category.term_id
      this.sidebar_title = category.name
      window.jQuery.ajax({
        url: this.post_url,
        type: 'GET',
        timeout: 10000,
        data: `action=listing_by_cats&category_id=${term_id}`,
        error: (err) => {
          this.statusMessage = 'Erreur:' + err.statusText
          console.log(err)
          this.loading = false
        },
        success: (response) => {
          this.listings = response.data
          console.log(response.data)
          this.loading = false
        }
      })
    },
    showInfo(event){
      console.log(event.location)

      for (var i = 0; i < this.listings.length; i++) {
        if( event.name === this.listings[i].name ){
          this.listings[i].showed = !this.listings[i].showed
          break
        }
      }
      // Move the map to the target place
      this.$store.map.flyTo({
        center: [event.location.longitude, event.location.latitude],
        //zoom: 8,
        pitch: 45,
        essential: true
      });

    },
    showDirection( event ){
      this.$store.commit('toggleAskDirection', true);
      // Get listing coordinate
      const coords = [event.location.longitude, event.location.latitude]
      // fire click event to get direction
      this.$store.map.fire('click', {lngLat: coords});
    }
  },
}
</script>
