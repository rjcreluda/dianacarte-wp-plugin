<template>
  <div class="home">
    <v-container>
      <v-row>
        <!-- First Column -->
        <v-col cols="12" md="5">
          <v-row>
            <v-col cols="12">
              <h3 class="mb-5">Edition de voyage</h3>
              <p class="orange--text lighten-5">Status: {{ statusMessage }}</p>
            </v-col>
          </v-row> 
          <v-row class="mb-5">
            <v-col cols="12" md="6">
              Nom du voyage
              <v-text-field v-model="packageTour.name"></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              Nombre de personne
              <v-text-field 
                v-model="packageTour.persons"
                readonly
                type="text"
                append-outer-icon="mdi-plus"
                @click:append-outer="packageTour.persons++"
                prepend-icon="mdi-minus"
                @click:prepend="packageTour.persons--">
              </v-text-field>
            </v-col>
            <v-col cols="12">
              <div class="font-weight-bold">
                Description <br>
                Budget estimatif Total: {{ totalPrice }} €
              </div>
            </v-col>
          </v-row>
          <v-row class="my-3">
            <v-col>
              <TourDialog 
                :dialog="showDialog" 
                :places="places" 
                :hotels="hotels" 
                :restos="restaurants" 
                :itinerary="itinerary" 
                :edit="edit"
                @filled="addToItineraries($event)" 
                @cancelled="cancelDialog()"
              />
              <v-btn 
                class="mr-2"
                depressed 
                @click="showDialog = true">
                <v-icon left>
                  mdi-plus
                </v-icon>
                Ajouter Itinéraire
              </v-btn>
              <v-btn 
                depressed 
                color="orange lighten-1"
                :loading="loading"
                @click="saveItinerary" 
                :disabled="tourDays.length < 2">
                <v-icon left>
                  mdi-check
                </v-icon>
                Terminé
              </v-btn>
            </v-col>
          </v-row>
        </v-col><!-- ./first col -->

        <!--  Second column -->
        <v-col cols="12" md="7">
          <v-row>
            <v-col cols="12"><h3 class="mb-5 ml-6">Les itinéraires</h3></v-col>
          </v-row>
          <div v-if="tourDays.length">
            <v-timeline dense>
              <v-slide-x-reverse-transition
                group
                hide-on-leave
              >
                <v-timeline-item
                  v-for="(tour, index) in tourDays" :key="index"
                  :small="index % 2 != 0"
                  fill-dot
                  :color="index % 2 == 0 ? 'orange' : 'blue'"
                >
                  <template v-slot:icon>
                    <span class="white--text text-caption">{{ index+1 }}</span>
                  </template>
                  <v-card class="grow">
                    <v-card-subtitle class="text-overline d-flex">
                      <span v-if="index==0">
                        Départ: {{ places[tour.place].name }}
                      </span>
                      <span v-else>
                        {{ places[tour.place].name }}
                      </span>
                      <v-spacer></v-spacer>
                      <div>
                        <v-icon class="mr-5" @click="editItinerary( index, tour )">mdi-pencil</v-icon>
                        <v-icon @click="deleteItinerary( index )">mdi-delete</v-icon>
                      </div>
                    </v-card-subtitle>
                    <v-card-text>
                      <ul>
                        <li>Hotel: 
                          <span v-if="tour.hotel != undefined">{{ hotels[tour.hotel].name }}</span>
                          <span v-else>n/a</span>
                        </li>
                        <li>
                          Restaurant:
                          <span v-if="tour.resto != undefined">{{ restaurants[tour.resto].name }}</span>
                          <span v-else>n/a</span>
                        </li>
                      </ul>
                    </v-card-text>
                  </v-card>
                </v-timeline-item>
              </v-slide-x-reverse-transition>
            </v-timeline>
          </div>
          <div v-else>
            Aucun
          </div>
        </v-col><!-- ./second col -->
      </v-row>
    </v-container>
  </div>
</template>

<script>

import wpData from '@/dummy_data.json'
import TourDialog from '@/components/TourDialog'
import axios from 'axios'

export default {
  name: 'EditPackage',
  components: {
    TourDialog
  },
  data: () => ({
    panels: [], // lists of expanded panel
    showDialog: false,
    tourIndex: 1, // Id for new itinerary
    listings: wpData.listings, // list of point of interest
    places: [],
    hotels: [],
    restaurants: [],
    tourDays: [], // lists of itineraries
    itinerary: { // Default itinerary to pass to the Dialog box
        place: null,
        hotel: null,
        resto: null,
        car: null,
      },
    edit: false, // Mode edition d'itinéraire
    packageTour: {
      name: 'My tour',
      persons: 1,
      itineraries: []
    },
    statusMessage: 'en attente',
    post_url: window.wpData.ajax_url,
    loading: false // Model for button load state
  }),
  created(){
    //console.log(window.wpData)
    this.hotels.splice(0) // remove defaults
    this.hotels = window.wpData.listings.filter( listing => listing.category == 'Hotels' )
    this.restaurants = window.wpData.listings.filter( listing => listing.category == 'Restaurants' )
    this.places = window.wpData.listing_location
    this.loadTour()
  },
  computed: {
    totalPrice(){
      let price = 0;
      for( let tour of this.tourDays ){
        let hotel, resto = undefined;
        if( tour.hotel !== undefined ){
          hotel = this.hotels[tour.hotel]
        }
        if( tour.resto !== undefined ){
          resto = this.restaurants[tour.resto]
        }
        let hotel_price = 0;
        let resto_price = 0;
        if (typeof hotel !== 'undefined') {
          let {min, max} = hotel.price
          if( min != '' && max != ''){
            hotel_price = parseFloat(min) + parseFloat(max) / 2;
          }
        }
        if (typeof resto !== 'undefined') {
          let r_min = resto.price.min,
          r_max = resto.price.max;
          if( r_min != '' && r_max != ''){
            resto_price = parseFloat(r_min) + parseFloat(r_max) / 2;
          }
        }
        price += ( hotel_price + resto_price)
      }
      return price * parseInt(this.packageTour.persons)
    },
  },
  methods: {
    /* From dialog, we got only index of element (hotel, resto, ..) in their array
     * This function get Id from their index 
     */
    itinerary_index_to_id( itinerary ){
      let hotel, resto = undefined;
      if( itinerary.hotel !== undefined ){
        hotel = this.hotels[itinerary.hotel]
      }
      if( itinerary.resto !== undefined ){
        resto = this.restaurants[itinerary.resto]
      }
      let hotel_price = 0;
      let resto_price = 0;
      if (typeof hotel !== 'undefined') {
        hotel_price = parseFloat(hotel.price.min) + parseFloat(hotel.price.max) / 2;
      }
      if (typeof resto !== 'undefined') {
        resto_price = parseFloat(resto.price.min) + parseFloat(resto.price.max) / 2;
      }
      let id = 0
      if( itinerary.id != null && itinerary.id != undefined ){
        id = itinerary.id
      }

      let data = {
        id: id,
        tour_id: this.packageTour.id,
        place_id: this.places[itinerary.place].id,
        hotel_id: null,
        resto_id: null,
        vehicle_id: null,
        budjet: hotel_price + resto_price
      };
      if( hotel )
        data.hotel_id = hotel.id
      if( resto )
        data.resto_id = resto.id
      return data;
    },
    /* Save Tour to database */
    update_tours(){
      this.statusMessage = 'Chargement ...'
      this.loading = true
      axios.put(`${window.wpData.rest_url}/dianacarte/tours/${this.packageTour.id}`, {
        id: this.packageTour.id,
        name: this.packageTour.name,
        persons: this.packageTour.persons
      })
      .then( (response) => {
        console.log(response.data)
        this.statusMessage = 'Succès'
      })
      .catch( error => {
        this.statusMessage = 'Echec de l\'opération'
        console.log(error)
      } )
      .finally( () => this.loading = false )
    },
    /* Save packageTour */
    saveItinerary(){
      //this.$store.commit('add_tour', this.)
      this.packageTour.itineraries.splice(0);
      let packageTour = [];
      for( let tour of this.tourDays ){
        let hotel, resto = undefined;
        if( tour.hotel !== undefined ){
          hotel = this.hotels[tour.hotel]
        }
        if( tour.resto !== undefined ){
          resto = this.restaurants[tour.resto]
        }
        let hotel_price = 0;
        let resto_price = 0;
        if (typeof hotel !== 'undefined') {
          hotel_price = parseFloat(hotel.price.min) + parseFloat(hotel.price.max) / 2;
        }
        if (typeof resto !== 'undefined') {
          resto_price = parseFloat(resto.price.min) + parseFloat(resto.price.max) / 2;
        }
        let id = 0
        if( tour.id != null && tour.id != undefined ){
          id = tour.id
        }

        let data = {
          id: id,
          tour_id: this.packageTour.id,
          place_id: this.places[tour.place].id,
          hotel_id: null,
          resto_id: null,
          budjet: hotel_price + resto_price
        };
        if( hotel )
          data.hotel_id = hotel.id
        if( resto )
          data.resto_id = resto.id
        //console.log(data);
        this.packageTour.itineraries.push( data );
      }
      //console.log(this.packageTour);
      this.update_tours();
      // to do: save to db
      // Compute price
    },
    cancelDialog(){
      this.showDialog = false
      this.edit = false
    },
    editItinerary( index, itinerary ){
      this.edit = true
      this.itinerary = this.tourDays[index]
      this.showDialog = true
      console.log(this.tourDays[index])
    },
    deleteItinerary( index ){
      console.log('Deleting item in index ',index)
      let id = 0
      if( this.tourDays[index].id != null ){
        id = this.tourDays[index].id
      }
      if( id != 0 ){
        // Removing existing item from Database
        axios.delete(`${window.wpData.rest_url}/dianacarte/itineraire/${id}`)
        .then( (response) => {
          console.log(response.data)
          this.statusMessage = 'Succès'
          this.tourDays.splice(index, 1)
        })
        .catch( error => {
          this.statusMessage = 'Echec de l\'opération'
          console.log(error)
        } )
      }
      else{
        this.tourDays.splice(index, 1)
      }
    },
    /* Save itinerary from dialog box to the tourDays (itineraires) list*/
    addToItineraries( tour ){
      if( this.edit == true ){
        // Update itinerary
        const { place, hotel, resto, car } = tour
        if( tour.id != undefined ){
          console.log('updating itinerary id ', tour.id)
          // Getting currently edited itinerary
          const editedItinerary = this.tourDays.find( (el) => {
            return el.id == tour.id
          })
          const id = editedItinerary.id
          // indexed value, not id 
          const newValue = {
            id, place, hotel, resto, car
          }
          // Updating that itinerary
          console.log(newValue)
          this.statusMessage = 'Chargement ...'
          axios.put(`${window.wpData.rest_url}/dianacarte/itineraire/${id}`, this.itinerary_index_to_id(newValue))
          .then( (response) => {
            console.log(response.data)
            this.statusMessage = 'Succès'
            this.tourDays.splice( this.tourDays.indexOf(editedItinerary), 1, newValue)
          })
          .catch( error => {
            this.statusMessage = 'Echec de l\'opération'
            console.log(error)
          } )
        }
         
      }
      else{
        // Insert itinerary
        console.log('Inserting new itinerary')
        console.log(tour)
        const { place, hotel, resto, car } = tour
        const newItinerary = {
          id: null,
          place: place,
          hotel: hotel,
          resto: resto,
          car: car
        }
        this.statusMessage = 'Chargement ...'
        axios.post(`${window.wpData.rest_url}/dianacarte/itineraire`, this.itinerary_index_to_id(newItinerary))
        .then( (response) => {
          console.log(response.data)
          this.tourDays.push(newItinerary)
          this.panels.push(this.tourDays.indexOf(newItinerary))
          this.tourIndex++
          this.statusMessage = 'Succès'
        })
        .catch( error => {
          this.statusMessage = 'Echec de l\'opération'
          console.log(error)
        } )
      }
      
      this.showDialog = false
      this.edit = false
    },
    filteredHotel( place ){
      let hotels = this.hotels.filter( hotel => hotel.place == place)
      let arr = []
      for(let i = 0; i < hotels.length; i++){
        arr.push(hotels[i].name)
      }
      return arr
    },
    filteredResto( place ){
      let list = this.restos.filter( resto => resto.place == place)
      let arr = []
      for(let i = 0; i < list.length; i++){
        arr.push(list[i].name)
      }
      return arr
    },
    filteredRental( place ){
      return this.rentals.filter( el => el.place == place)
    },
    addDefaultTour( c ){
      let count = parseInt( c )
      if( count < 1)
        count = 1
      for( let i = 0; i < count; i++ ){
        let place = i % 2 == 0 ? 0 : 1
        this.tourDays.push({
          id: this.tourIndex, 
          place: place,
          hotel: 2,
          resto: 1,
          car: null
        })
        this.tourIndex++;
      }
      //this.showDialog = true;
    },
    loadTour( ){
      let tour_id = this.$route.params.id
      axios.get(`${window.wpData.rest_url}/dianacarte/tours/${tour_id}`)
      .then( (response) => {
        let tourData = response.data.tour
        this.packageTour.id = tourData.id
        this.packageTour.name= tourData.name
        this.packageTour.persons= tourData.persons
        for( let me of tourData.itineraires ){
          //console.log(me)
          let place_index = this.places.filter( (p) => { return p.id == me.place_id} )[0]
          let hotel = this.hotels.filter( (p) => { return p.id == me.hotel_id} )[0]
          let hotel_index = this.hotels.indexOf(hotel) != -1 ? this.hotels.indexOf(hotel) : undefined
          let resto = this.restaurants.filter( (p) => { return p.id == me.resto_id} )[0]
          let resto_index = this.restaurants.indexOf(resto) != -1 ? this.restaurants.indexOf(resto) : undefined
          this.tourDays.push({
            id: Number(me.id), 
            place: this.places.indexOf(place_index),
            hotel: hotel_index,
            resto: resto_index,
            car: null
          })
          this.statusMessage = 'prêt'
          //this.tourIndex++;
        }
      })
      .catch( error => console.log(error) )
      //this.showDialog = true;
    },
  },  
}
</script>

