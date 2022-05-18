<template>
  <div class="home">
    <h4 class="ma-4">DIRECTION MAP</h4>
    <div>
      <mapbox-map
      :accessToken="accessToken"
      :mapStyle="mapStyle"
      :center="center"
      :zoom="zoom"
      @mb-created="(mapboxInstance) => map = mapboxInstance">
      <mapbox-geocoder countries="Madagascar" limit="10" />
      <mapbox-geolocate-control />
      <mapbox-navigation-control position="bottom-right" />
      <div v-if="userLocation">
        <mapbox-popup :lng-lat="[userLocation[1], userLocation[0]]">
          <p>Vous êtes ici</p>
        </mapbox-popup>
      </div>
      </mapbox-map> 
      <div id="map-instructions" v-if="data">
        <span class="duration">
          Durée du trajet: <Time :second="tripDuration" /> environ <br>
          Longueur du trajet: {{ tripDistance }} m
        </span>
        <ul>
          <li v-for="(instruction, i) in tripInstructions" :key="i">
            {{ instruction }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src

//import MapboxDirections from '@mapbox/mapbox-gl-directions/dist/mapbox-gl-directions';
import '@mapbox/mapbox-gl-directions/dist/mapbox-gl-directions.css';
import '@mapbox/mapbox-gl-geocoder/lib/mapbox-gl-geocoder.css';

import Time from '@/components/Time.vue';

export default {
  name: 'Direction',
  components: {
    Time
  },
  data: () => ({
      mapStyle: 'mapbox://styles/mapbox/streets-v11',
      accessToken: 'pk.eyJ1IjoiamluZHMiLCJhIjoiY2tzMGVjcG5wMG1tbDJvcGp1d29lMnE5NyJ9.mADwLymdXk6vjhAxH0qxqA',
      center: [49.29149481988657, -12.277539696362235],
      dest: [49.29256, -12.27831], // end point for route
      zoom: 14, // map initial zoom
      map: null, // map object
      canevas: null, // map's canvas
      data: null, // map's route data,
      tripInstructions: [], // Map instruction text
      tripDuration: 0, // trip duration from start & end point route
      tripDistance: 0,
      userLocation: null, // user coordinate,
    }),
  methods: {

    /* Get route from start point adn end point */
    getRoute( end ){
      let start = this.center
      let url = 'https://api.mapbox.com/directions/v5/mapbox/cycling/' + start[0] + ',' + start[1] + ';' + end[0] + ',' + end[1] + '?steps=true&geometries=geojson&access_token=' + this.accessToken;
      //axios.get()
      var req = new XMLHttpRequest();
      req.open('GET', url, true);
      req.onload = () => {
        var json = JSON.parse(req.response);
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
        if (this.map.getSource('route')) {
          this.map.getSource('route').setData(geojson);
        } else { // otherwise, make a new request
          this.map.addLayer({
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
        // add turn instructions here at the end
      }; // end onload
      req.send();
    },

    /* Add itinerary instructions to view */
    addInstructions(){
      // remove last instructions & duration
      this.tripInstructions = [];
      let steps = this.data.legs[0].steps;
      //let tripInstructions = [];
      for (let i = 0; i < steps.length; i++) {
        this.tripInstructions.push(steps[i].maneuver.instruction);
        //this.tripDuration = Math.floor(this.data.duration / 60);
      }
      this.tripDuration = this.data.duration;
      this.tripDistance = this.data.distance;
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
        });
      }
      else{
        console.log('Your browser does not support geolocation api');
      }
    },

    /* Get Address from coordinate */
    /*getAddressFrom(lat, long){
      // Mapbox geocoding service here
      axios
        .get('https://api.mapbox.com/geocoding/v5/mapbox.places/-122.463%2C%2037.7648.json?access_token=YOUR_MAPBOX_ACCESS_TOKEN')
        .then()
    }*/
  },
  mounted(){
    /*let directions = new MapboxDirections({
      accessToken: this.accessToken,
      unit: "metric",
      profile: "mapbox/driving",
      alternatives: false,
      geometries: "geojson",
      controls: { instructions: false },
      flyTo: false
    });*/
    //this.map.addControl(directions, "top-left");
    this.getUserLocation();

    this.canvas = this.map.getCanvasContainer();

    /******************************************
     * Init Starting point and destination point on load of map
     ******************************************/
    this.map.on('load', () => {
      // starts and ends at the same location
      //this.getRoute(this.center);
      // Add starting point to the map
      this.map.addLayer({
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

      // default destination point
      /*
      this.getRoute(this.dest);
      this.addInstructions();
      this.map.addLayer({
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
                  coordinates: this.dest
                }
              }
              ]
            }
          },
          paint: {
            'circle-radius': 10,
            'circle-color': '#f30'
          }
        }); */

    }); // end map onload

 
    /******************************************
     * Add destination point on map click
     ******************************************/
    this.map.on('click', (e) => {
      //console.log(e.lngLat);
      var coordsObj = e.lngLat;
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
      if (this.map.getLayer('end')) {
        this.map.getSource('end').setData(end);
      } else {
        this.map.addLayer({
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
      }
      this.getRoute(coords);
      this.addInstructions();
    }); // end onclick map

  } // end mounted()
}
</script>

<style>
  .mapboxgl-map{
    width: 100%;
    height: 500px;
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
    font-size: .9em;
  }
</style>
