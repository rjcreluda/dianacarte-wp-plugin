<template>
  <div class="about">
    <div class="map-area">
      <div class="btn-container">
        <v-btn
          light
          @click="showed = !showed"
          class="rounded-r-xl"
          small
        >
        <v-icon>mdi-arrow-right-bold</v-icon>
        </v-btn>
      </div>
      <v-expand-x-transition>
        <aside class="map-sidebar elevation-10 pa-0" v-show="showed">
          <v-card flat>
            <v-toolbar
              color="cyan"
              dark
              flat
            >
              <v-app-bar-nav-icon></v-app-bar-nav-icon>

              <v-toolbar-title>Navigation</v-toolbar-title>

              <v-spacer></v-spacer>

              <v-btn icon @click="showed = !showed">
                <v-icon>mdi-close-thick</v-icon>
              </v-btn>
              <template v-slot:extension>
              <v-tabs
                v-model="tab"
                icons-and-text
                show-arrows
              >
                <v-tabs-slider></v-tabs-slider>

                <v-tab href="#tab-1">
                  Points d'intérêts
                  <v-icon>mdi-view-grid-outline</v-icon>
                </v-tab>

                <v-tab href="#tab-3">
                  Proches
                  <v-icon>mdi-map-marker-radius</v-icon>
                </v-tab>
                <v-tab href="#tab-info" v-if="showInfoTab">
                  Info
                  <v-icon>mdi-alert-circle-outline</v-icon>
                </v-tab>
              </v-tabs>
              </template>
            </v-toolbar>
            
            <!-- TAB ITEMS LISTS -->
            <v-tabs-items v-model="tab">

              <!-- Recents listings TAB -->
              <v-tab-item value="tab-1">
                <v-card flat>
                  <v-card-text>
                    <span class="text-subtitle-1">Catégories:</span>
                    <v-btn 
                      color="orange lighten-2"
                      text
                      @click="showFilterSelection = !showFilterSelection"
                    >{{ selectedCategory }}</v-btn>
                    <div v-if="pagination.length > 1">
                      <v-pagination
                        v-model="pagination.page"
                        :length="pagination.length"
                        circle
                        color="orange"
                      ></v-pagination>
                    </div>
                    <v-list dense>
                      <div v-for="listing in paginatedList(filteredListings)" :key="listing.id">
                        <ListingItem :listing="listing" @showInMap="showInfo($event)" @askDirection="showDirection($event)" />
                      </div>
                    </v-list>
                  </v-card-text>
                </v-card>
              </v-tab-item>

              <!-- Neary By TAB -->
              <v-tab-item value="tab-3">
                <v-card flat>
                  <v-card-title>
                    <span class="text-h6">Les alentours</span>
                    <v-spacer></v-spacer>
                    <span class="my-3 text-subtitle-1">Rayon: {{ nearByRadius }} km</span>
                  </v-card-title>
                  <v-card-text>
                    <v-list dense>
                      <div v-for="listing in nearByPlaces" :key="listing.id">
                        <ListingItem :listing="listing" @showInMap="showInfo($event)" @askDirection="showDirection($event)" />
                      </div>
                    </v-list>
                  </v-card-text>
                </v-card>
              </v-tab-item>

              <!-- Listing Information TAB -->
              <v-tab-item value="tab-info">
                <ListingItemInfo v-if="activeListing" :listing="activeListing" @askDirection="showDirection($event)" />
                <Itinerary v-if="activeDirection" :place="activeDirection.name" :data="data" />
              </v-tab-item>
            </v-tabs-items>
          </v-card>
          <div v-if="showFilterSelection" class="filter-selection-wrapper">
            <!-- Filter Option Popup -->
            <v-card flat>
              <v-toolbar
                color="cyan"
                dark
                flat
              >
                <v-btn icon @click="showFilterSelection = !showFilterSelection">
                  <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
                <v-toolbar-title>Filtrer les affichages</v-toolbar-title>
              </v-toolbar>
              <v-card-text>
                <v-btn v-for="category in listing_cats" :key="category.term_id"
                  @click.stop="load_category(category)"
                  class="ma-1"
                  light
                  outlined
                  color="pink"
                  rounded
                  small>
                  {{ category.name }} ({{ category.count}})
                </v-btn>
              </v-card-text>
            </v-card>
            <!-- end filter option -->
          </div>
        </aside>
      </v-expand-x-transition>

      <!-- The Map Component -->
      <DianaMap :listings="listings" :center="mapCenter" @directionAsked="getDirection($event)">
      </DianaMap>
      <MessageBar text="Bienvenu à la carte de navigation" />
      <!-- <v-tour name="myTour" :steps="tourSteps" :options="tourOptions"></v-tour> -->
    </div>
  </div>
</template>
<script>
  import Mapbox from "mapbox-gl";
  import 'mapbox-gl/dist/mapbox-gl.css';

  import DianaMap from '@/components/DianaMap.vue'
  import ListingItem from '@/components/ListingItem.vue'
  import Itinerary from '@/components/Itinerary.vue'
  import ListingItemInfo from '@/components/ListingItemInfo.vue'
  import MessageBar from '@/components/MessageBar'
  import {getDistanceBetween} from '@/map_tools.js'
  // https://soal.github.io/vue-mapbox/guide/composition.html

  export default{
    name: 'About',
    components: {
      DianaMap,
      ListingItem,
      ListingItemInfo,
      Itinerary,
      MessageBar
    },
    data: () => ({
      tab: null, // tab component
      showInfoTab: false, // Hide info tab by default
      mapStyle: 'mapbox://styles/mapbox/streets-v11',
      accessToken: 'pk.eyJ1IjoiamluZHMiLCJhIjoiY2tzMGVjcG5wMG1tbDJvcGp1d29lMnE5NyJ9.mADwLymdXk6vjhAxH0qxqA',
      mapCenter: [49.29149481988657, -12.277539696362235],
      zoom: 14, // map zoom level
      data: null, // map route instruction data,
      routeLoaded: false, // check if route request is loaded
      tripInstructions: [], // Itinerary list for a asked direction
      showed: true, // toggle Panel
      listing_cats: window.wpData.listing_cats, // listing categories from back-end
      listings: window.wpData.listings, // listing list from back-end
      filteredListings: null,
      selectedCategory: 'Tout',
      activeListing: null, // current listing to show in info tab
      activeDirection: null, // current listing direction to show in info tab
      pagination: {
        page: 1,
        perPage: 4,
        length: 0
      },
      tourSteps: [
        {
          target: '.mapboxgl-ctrl-top-right',  // We're using document.querySelector() under the hood
          content: `Controller la carte`
        },
        {
          target: '.mdi-close-thick',
          content: 'Masque le panel'
        },
        {
          target: '.mdi-map-marker',
          content: 'Afficher l\'emplacement sur la carte'
        },
        {
          target: '.mdi-call-split',
          content: 'Demander itinéraire'
        }
      ],
      tourOptions: {
          labels: {
            buttonSkip: 'Sauter',
            buttonPrevious: 'Précédent',
            buttonNext: 'Suivant',
            buttonStop: 'Terminé'
          }
      },
      showFilterSelection: false,
      userLocation: null,
      nearByRadius: 2, // Distace radius for near by (in km)
      nearByPlaces: []
    }),
    computed: {
      visiblePages(){
        return this.listings.slice((this.pagination.page - 1)* this.pagination.perPage, this.pagination.page* this.pagination.perPage)
      },
    },
    created(){
      this.mapbox = Mapbox;
      this.map = null;
      this.filteredListings = this.listings
      //console.log( getDistanceBetween([-12.277539696362235, 49.29149481988657], [-12.2427, 49.3460]) )
      this.userLocation = this.mapCenter
      this.getNearby()
      // Vuex store
      this.$store.dispatch('get_listings', `${window.wpData.rest_url}/dianacarte/listings`);
    },
    mounted(){
      // drive map to the user location
      //this.getUserLocation();
    },
    methods: {
      getNearby(){
        let startPoint = [this.mapCenter[1], this.mapCenter[0]]
        // startPoint = this.userLocation
        // looping thought listings
        for( const listing of this.listings ){
          let endPoint = [listing.location.latitude, listing.location.longitude]
          if( getDistanceBetween(startPoint, endPoint) <= this.nearByRadius ){
            this.nearByPlaces.push( listing )
          }
        }
      },  
      togglePanel(){
        this.showed = !this.showed
      },
      load_category( cat ){
        this.selectedCategory = cat.name
        this.filteredListings = this.listings.filter( (listing) => listing.category == cat.name )
        this.showFilterSelection = !this.showFilterSelection
        this.pagination.page = 1
      },
      paginatedList( lists ){
        this.pagination.length = Math.ceil(lists.length/this.pagination.perPage)
        return lists.slice((this.pagination.page - 1)* this.pagination.perPage, this.pagination.page* this.pagination.perPage)
      },
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
        //console.log(event.location)
        // Show it on the Map
        for (var i = 0; i < this.listings.length; i++) {
          if( event.name === this.listings[i].name ){
            this.listings[i].showed = !this.listings[i].showed
            break
          }
        }
        // Move the map to the target place
        this.$store.map.flyTo({
          center: [event.location.longitude, event.location.latitude],
          zoom: 10,
          pitch: 45,
          essential: true
        });
        // Information Tab
        this.showInfoTab = true // show the info tab
        this.tab = 'tab-info' // switch to the info tab
        this.activeListing = event

        if( this.activeDirection.name != event.name ){
          this.activeDirection = null
        }

      },
      showDirection( event ){
        // Information Tab
        this.showInfoTab = true // show the info tab
        this.tab = 'tab-info' // switch to the info tab
        this.activeDirection = event

        this.$store.commit('toggleAskDirection', true);
        // Get listing coordinate
        const coords = [event.location.longitude, event.location.latitude]
        // fire click event to get direction
        this.$store.map.fire('click', {lngLat: coords});

        if( this.activeListing.name != event.name ){
          this.activeListing = null
        }
        
      },
      getDirection( event ){
        this.routeLoaded = true
        this.data = event;
      }
    } // methods
  }
</script>
<style scoped>
  html, body {
    height: 100%;
  }
  .about{
    display: flex;
    flex-direction: row;
    height: 100%;
    position: relative;
  }
  .map-sidebar {
    flex-basis: 260px;
    flex-shrink: 0; /* just one way to do it */
    width: 35%;
    height: 100%;
    overflow: auto;
    padding: 10px;
    position: absolute;
    left: 0;
    z-index: 10;
    background-color: #fff;
  }
  .map-area {
    flex-basis: 100%;
    position: relative;
  }
  .btn-container{
    position: absolute;
    left: 0;
    /*top: calc(50% - 24px);*/
    top: 0;
    z-index: 1;
  }
  .mapboxgl-map, #map{
    width: 100%;
    height: 450px;
    position: relative;
  }
  .duration {
    font-size: .9em;
  }
  .filter-selection-wrapper{
    position: absolute;
    top: 0;
    left: 0;
    z-index: 100;
    width: 100%;
    height: 100%;
    background-color: #fff;
  }
  @media screen and (max-width: 1024px) {
    .map-sidebar{
      width: 45%;
    }
  }
  @media screen and (max-width: 750px) {
    .map-sidebar{
      width: 90%;
    }
  }
  
</style>
