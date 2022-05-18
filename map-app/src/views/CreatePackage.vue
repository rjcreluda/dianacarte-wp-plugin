<template>
  <div class="home">
    <v-container>
      <v-row>
        <!-- First Column -->
        <v-col cols="12" md="4">
          <v-row>
            <v-col cols="12">
              <h3 class="mb-5">Création de voyage</h3>
              <p>Status: {{ statusMessage }}</p>
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
                class="mr-5"
                depressed 
                @click="showDialog = true">
                <v-icon left>
                  mdi-plus
                </v-icon>
                Ajouter Itinéraire
              </v-btn>
              <v-btn 
                depressed 
                color="green lighten-1"
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
        <v-col cols="12" md="8">
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

export default {
  name: 'CreatePackage',
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
      name: '',
      persons: 1,
      itineraries: []
    },
    statusMessage: '',
    post_url: window.wpData.ajax_url,
  }),
  created(){
    //this.addDefaultTour(2)
    //console.log(window.wpData)
    this.hotels.splice(0) // remove defaults
    this.hotels = window.wpData.listings.filter( listing => listing.category == 'Hotels' )
    this.restaurants = window.wpData.listings.filter( listing => listing.category == 'Restaurants' )
    this.places = window.wpData.listing_location
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
    /* Save to database */
    save_tours(){
      this.statusMessage = 'Loading ...'
      //console.log(this.packageTour)
      window.jQuery.ajax({
        url: this.post_url,
        type: 'POST',
        timeout: 10000,
        data: {
          action: 'save_tour',
          tour: this.packageTour
        },
        error: (err) => {
          this.statusMessage = 'Erreur:' + err.statusText
          console.log(err)
          //this.loading = false
        },
        success: (response) => {
          console.log(response)
          this.statusMessage = 'Done'
        }
      })
    },
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

        /*packageTour.push({
          id: tour.id,
          place: tour.place,
          hotel: hotel,
          resto: resto,
          price: hotel_price + resto_price
        });*/
        let data = {
          tour_id: 0,
          place_id: this.places[tour.place].id,
          hotel_id: 0,
          resto_id: 0,
          budjet: hotel_price + resto_price
        };
        if( hotel )
          data.hotel_id = hotel.id
        if( resto )
          data.resto_id = resto.id
        this.packageTour.itineraries.push( data );
      }
      console.log(this.packageTour);
      this.save_tours();
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
      console.log(index)
      this.tourDays.splice(index, 1);
    },
    /* Save itinerary from dialog to the tour list*/
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
          const newValue = {
            id, place, hotel, resto, car
          }
          // Updating that itinerary
          console.log(newValue)
          this.tourDays.splice( this.tourDays.indexOf(editedItinerary), 1, newValue)
        }
         
      }
      else{
        // Insert itinerary
        console.log('Inserting new itinerary')
        console.log(tour)
        const { place, hotel, resto, car } = tour
        const newItinerary = {
          id: this.tourIndex,
          place: place,
          hotel: hotel,
          resto: resto,
          car: car
        }
        this.tourDays.push(newItinerary)
        this.panels.push(this.tourDays.indexOf(newItinerary))
        this.tourIndex++
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
  },  
}
</script>






<!-- Axian group
https://gallery-cdn.breezy.hr/2e6ce/Annonce%20Ingnieur%20Dveloppeur%20Applicatif-1.png
 -->