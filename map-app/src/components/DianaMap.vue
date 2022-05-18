<template>
  <div class="fill-height">
    <!-- <div id="map"> -->
      <MglMap 
        :accessToken="accessToken" 
        :mapStyle.sync="mapStyle" 
        container="map"
        :zoom="zoom"
        :center="center"
        @load="onMapLoaded"
        @click="onMapClick">
        <MglNavigationControl position="top-right"/>
        <div v-if="listings.length > 0">
          <div v-for="listing in listings" :key="listing.id" >
            <MglMarker 
              v-if="listing.location.longitude != ''"
              :coordinates="[listing.location.longitude, listing.location.latitude]" 
              color="orange">
              <MglPopup :showed="listing.showed">
                <ListingItemInfoMap :listing="listing" />
              </MglPopup>
            </MglMarker>
          </div>
        </div>
      </MglMap>
  </div>
</template>
<script>
  import Mapbox from "mapbox-gl";
  import { 
    MglMap, 
    MglNavigationControl, 
    MglMarker,
    MglPopup
  } from "vue-mapbox";
  import 'mapbox-gl/dist/mapbox-gl.css';
  import ListingItemInfoMap from '@/components/ListingItemInfoMap.vue';
  import { getDistanceBetween } from '@/map_tools.js';
  // https://soal.github.io/vue-mapbox/guide/composition.html

  export default{
    name: 'About',
    props: {
      listings: Array,
      center: { default: [0,0] },
      direction: { default: null }
    },
    components: {
      MglMap, 
      MglNavigationControl, 
      MglMarker, 
      MglPopup,
      ListingItemInfoMap,
    },
    data: () => ({
      mapStyle: 'mapbox://styles/mapbox/streets-v11',
      //mapStyle: 'mapbox://styles/jinds/ckt4lfjv416c718mhexrwewwe',
      mapSatelliteStyle: 'mapbox://styles/mapbox/satellite-v9',
      accessToken: 'pk.eyJ1IjoiamluZHMiLCJhIjoiY2tzMGVjcG5wMG1tbDJvcGp1d29lMnE5NyJ9.mADwLymdXk6vjhAxH0qxqA',
      zoom: 8,
      data: null, // map route instruction data,
      routeLoaded: false, // check if route request is loaded
      tripInstructions: [],
      userLocation: null,
      geoJsonSource: null,
      geoJsonlayer: null
    }),
    created(){
      this.mapbox = Mapbox;
      this.map = null;
      this.getUserLocation();
    },
    mounted(){
      //this.addListingPointLayer();
      
    },
    methods: {
      passEvent(){
        const data = { 
          duration: this.data.duration, 
          distance: this.data.distance,
          instructions: this.tripInstructions
        }
        this.$emit('directionAsked', data)
      },
      onMapLoaded( event ){
        // Setting up custom control for the map
        
        // in component
        this.$store.map = event.map;

        /* layer remove */
        this.$store.map.style.stylesheet.layers.forEach( (layer) => {
            /*if (layer.type === 'symbol') {
                this.$store.map.removeLayer(layer.id);
                //settlement-label
                //poi-label
                //map.removeLayer("place_label");
            }*/
            if(layer.id === 'poi-label'){
              console.log('removing', layer.id);
              this.$store.map.removeLayer(layer.id);
            }
        });
        /* end layer remove */

        this.canvas = this.$store.map.getCanvasContainer();
        this.getRoute(this.center);
        this.$store.map.addLayer({
          id: 'point',
          type: 'circle',
          source: {
            type: 'geojson',
            data: {
              type: 'FeatureCollection',
              features: [{
                type: 'Feature',
                properties: {},
                geometry: {
                  type: 'Point',
                  coordinates: this.center
                }
              }
              ]
            }
          },
          paint: {
            'circle-radius': 10,
            'circle-color': '#3887be'
          }
        }); // end adding starting point

        const a = [this.center[1], this.center[0]];
        const b = [this.userLocation[0], this.userLocation[1]];
        if( getDistanceBetween(a, b) <= 300 ){
          this.$store.map.panTo([this.userLocation[1], this.userLocation[0]], {duration: 2000});
        }

        const popup = new Mapbox.Popup({ closeOnClick: false })
        .setLngLat(this.center)
        .setHTML('<strong>Centre ville</strong>');
        popup.addTo(this.$store.map);

        // simulate click
        //this.$store.map.fire('click', {lngLat: [49.29257, -12.27834]});

        this.$tours['myTour'].start() 

      },
      onMapClick( e ){
        // Disable click if not clicking on ask Direction button
        if( this.$store.askDirection === false ){
          return;
        }
        
        this.routeLoaded = false;
        var coordsObj = e.mapboxEvent.lngLat;
        this.canvas.style.cursor = '';
        var coords = Object.keys(coordsObj).map(function(key) {
          return coordsObj[key];
        });
        var end = {
          type: 'FeatureCollection',
          features: [{
            type: 'Feature',
            properties: {},
            geometry: {
              type: 'Point',
              coordinates: coords
            }
          }
          ]
        };
        if (this.$store.map.getLayer('end')) {
          this.$store.map.getSource('end').setData(end);
        } else {
          this.$store.map.addLayer({
            id: 'end',
            type: 'circle',
            source: {
              type: 'geojson',
              data: {
                type: 'FeatureCollection',
                features: [{
                  type: 'Feature',
                  properties: {},
                  geometry: {
                    type: 'Point',
                    coordinates: coords
                  }
                }]
              }
            },
            paint: {
              'circle-radius': 10,
              'circle-color': '#f30'
            }
          });
        } // end else
        this.getRoute(coords);
        this.routeLoaded = true;

        this.$store.commit('toggleAskDirection', false);
      },
      /* Get route from start point adn end point */
      getRoute( end ){
        let start = this.center
        let url = 'https://api.mapbox.com/directions/v5/mapbox/driving/' + start[0] + ',' + start[1] + ';' + end[0] + ',' + end[1] + '?steps=true&language=fr&geometries=geojson&access_token=' + this.accessToken;

        var req = new XMLHttpRequest();
        req.open('GET', url, true);
        req.onload = () => {
          var json = JSON.parse(req.response);
          // Set route data
          this.data = json.routes[0];
          var route = this.data.geometry.coordinates;
          var geojson = {
            type: 'Feature',
            properties: {},
            geometry: {
              type: 'LineString',
              coordinates: route
            }
          };
          // if the route already exists on the map, reset it using setData
          if (this.$store.map.getSource('route')) {
            this.$store.map.getSource('route').setData(geojson);
          } else { // otherwise, make a new request
            this.$store.map.addLayer({
              id: 'route',
              type: 'line',
              source: {
                type: 'geojson',
                data: {
                  type: 'Feature',
                  properties: {},
                  geometry: {
                    type: 'LineString',
                    coordinates: geojson
                  }
                }
              },
              layout: {
                'line-join': 'round',
                'line-cap': 'round'
              },
              paint: {
                'line-color': '#3887be',
                'line-width': 5,
                'line-opacity': 0.75
              }
            });
          }
          // Adding turn instructions here at the end
          // remove last instructions & duration
          this.tripInstructions = [];
          let steps = json.routes[0].legs[0].steps;
          //let tripInstructions = [];
          for (let i = 0; i < steps.length; i++) {
            this.tripInstructions.push(steps[i].maneuver.instruction);
            //this.tripDuration = Math.floor(this.data.duration / 60);
          }
          this.passEvent();
        }; // end onload
        req.send();
      },
      /* Get current user Location coordinate */
      getUserLocation(){
        if( navigator.geolocation ){
          navigator.geolocation.getCurrentPosition( position => {
            this.userLocation = [
              position.coords.latitude, 
              position.coords.longitude
            ];
          }, error => {
            console.log(error.message);
            this.userLocation = this.center
          });
        }
        else{
          console.log('Your browser does not support geolocation api');
          this.userLocation = this.center;
        }
      },
      addListingPointLayer(){
        let features = [];
        for( const listing of this.listings ){
          features.push({
            'type': 'Feature',
            'properties': {
              'name': listing.name
            },
            'geometry': {
              'type': 'Point',
              'coordinates': [listing.location.latitude, listing.location.longitude]
            }
          });
        }
        this.geoJsonSource = {
          id: 'listing_source',
          data: {
            'type': 'FeatureCollection',
            'features': features
          }

        }
        this.geoJsonlayer = {
          'id': 'listing_places',
          'type': 'circle',
          'paint': {
            'circle-color': 'red',
            'circle-radius': 6,
            'circle-stroke-width': 2,
            'circle-stroke-color': '#ffffff'
          }
        }
      },
    } // methods
  }
</script>
<style>
  .mapboxgl-map, #map{
    width: 100%;
    height: 100% !important;
    position: relative;
  }
  #map-instructions {
    position: absolute;
    margin: 20px;
    width: 25%;
    top: 50px;
    bottom: 20%;
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.9);
    overflow-y: auto;
    font-family: sans-serif;
    font-size: 0.8em;
    line-height: 2em;
    box-shadow: 0 0 10px #ccc;
  }
  .duration {
    font-size: 1em;
  }
</style>
