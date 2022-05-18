( function($){
    $(document).ready( () => {
      mapboxgl.accessToken = 'pk.eyJ1IjoiamluZHMiLCJhIjoiY2tzMGVjcG5wMG1tbDJvcGp1d29lMnE5NyJ9.mADwLymdXk6vjhAxH0qxqA';
      Vue.use(Vuetify);
      const vm = new Vue({
        el: '#dianacarte-app',
        components: {
            //'MglMap': window.VueMapbox.MglMap
         },
        data() {
          return {
            message: 'NAVIGATION A LA CARTE',
            //MglMap: window.VueMapbox.MglMap
            access_token: 'pk.eyJ1IjoiamluZHMiLCJhIjoiY2tzMGVjcG5wMG1tbDJvcGp1d29lMnE5NyJ9.mADwLymdXk6vjhAxH0qxqA',
            //mapStyle: 'mapbox://styles/mapbox/basic-v9'
            map: undefined,
            mapStyle: 'mapbox://styles/mapbox/streets-v11',
            center: [49.29149481988657, -12.277539696362235],
            listings: undefined,
            listing_cats: ['Hotel', 'Restaurant', 'Tour opérateur', 'Visite', 'Banque', 'Securité'],
            
          }
        },
        methods: {
          initMap(){
            this.map = new mapboxgl.Map({
              container: 'map',
              style: 'mapbox://styles/mapbox/streets-v11',
              center: this.center, // starting position
              zoom: 14 // starting zoom
            })
            this.map.addControl(new mapboxgl.NavigationControl());
            // Create a default marker.
            const marker = new mapboxgl.Marker()
                .setLngLat(this.center)
                .addTo(this.map);
          },
          loadListingIntoMap(){
            this.listings.forEach( (listing) => {
              let marker = new mapboxgl.Marker()
                .setLngLat([listing.longitude, listing.latitude])
                .setPopup(new mapboxgl.Popup().setHTML(`<strong>${listing.name}</strong>`))
              listing.marker = marker
              listing.category = this.listing_cats[0]
              listing.marker.addTo(this.map)
            });
          },
          showPopup(listing){
            listing.marker.togglePopup();
          },
          searchFor(){
            console.log('searching ...')
          }
        },
        created(){
          this.listings =  window.dc_listings;
        },
        mounted(){
          this.initMap();
          this.loadListingIntoMap();
        }
      });
    } )
  })(jQuery);