<template>
  <v-row justify="center">
    <v-dialog
      v-model="dialog"
      scrollable
      max-width="700px"
    >
      <v-card>
        <v-card-title>Itinéraire</v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-0">
          <v-sheet>
            <div class="my-3 ml-4">
              <h4>Selectionner emplacement</h4>
              <span v-if="edit">{{ itinerary.place.name }}</span>
              <span v-else>{{ tour.place.name }}</span>
            </div>
            <v-slide-group
              v-model="tour.place"
              mandatory
              show-arrows
            >
              <v-slide-item
                v-for="(city, index) in places"
                :key="city.id"
                v-slot="{ active, toggle }"
                :value="index"
              >
                <v-btn
                  class="mx-2"
                  :input-value="active"
                  active-class="purple white--text"
                  depressed
                  rounded
                  @click="toggle"
                >
                  {{ city.name }}
                </v-btn>
              </v-slide-item>
            </v-slide-group>
          </v-sheet>

          <!-- HOTEL SELECTION SECTION -->
          <div class="my-3 ml-4">
            <h4>
            Selectionner Hotel</h4>
            <span v-if="tour.hotel != null">{{ tour.hotel.name }}</span>
          </div>
          <v-slide-group
            v-model="tour.hotel"
            center-active
            show-arrows
          >
            <v-slide-item
              v-for="(hotel, index) in hotels"
              :key="hotel.id"
              v-slot="{ active, toggle }"
              :value="index"
            >
              <v-card
                :color="active ? 'primary' : 'grey lighten-1'"
                class="ma-4"
                height="200"
                width="100"
                @click="toggle"
              >
              <v-img
                :src="hotel.thumbnail_url"
                class="white--text align-center"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="190px"
              >
                <v-row
                  class="fill-height"
                  align="center"
                  justify="center"
                >
                <v-card-text>
                  {{ hotel.name }}
                  <v-rating
                    v-model="hotel.rating.average"
                    background-color="yellow"
                    color="yellow accent-4"
                    dense
                    small
                    readonly
                    :value="Number(hotel.rating.average)"
                  ></v-rating>
                </v-card-text>
                  <v-scale-transition>
                    <v-icon
                      v-if="active"
                      color="white"
                      size="48"
                      v-text="'mdi-close-circle-outline'"
                    ></v-icon>
                  </v-scale-transition>
                </v-row>
              </v-img>
              </v-card>
            </v-slide-item>
          </v-slide-group>

          <!-- RESTAURANT SELECTION SECTION -->
          <div class="my-3 ml-4">
            <h4>
            Selectionner Restaurant</h4>
            <span v-if="tour.resto != null">{{tour.resto.name }}</span>
          </div>
          <v-slide-group
            v-model="tour.resto"
            center-active
            show-arrows
          >
            <v-slide-item
              v-for="(resto, index) in restos"
              :key="resto.id"
              v-slot="{ active, toggle }"
              :value="index"
            >
              <v-card
                :color="active ? 'primary' : 'grey lighten-1'"
                class="ma-4"
                height="200"
                width="100"
                @click="toggle"
              >
              <v-img
                :src="resto.thumbnail_url"
                class="white--text align-center"
                gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                height="190px"
              >
                <v-row
                  class="fill-height"
                  align="center"
                  justify="center"
                >
                <v-card-text>
                  {{ resto.name }}
                  <v-rating
                    v-model="resto.rating.average"
                    background-color="yellow"
                    color="yellow accent-4"
                    dense
                    small
                    readonly
                    :value="Number(resto.rating.average)"
                  ></v-rating>
                </v-card-text>
                  <v-scale-transition>
                    <v-icon
                      v-if="active"
                      color="white"
                      size="48"
                      v-text="'mdi-close-circle-outline'"
                    ></v-icon>
                  </v-scale-transition>
                </v-row>
              </v-img>
              </v-card>
            </v-slide-item>
          </v-slide-group>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-btn
            color="blue darken-1"
            text
            @click="$emit('cancelled')"
          >
            Fermer
          </v-btn>
          <v-btn
            color="blue darken-1"
            text
            @click="saveItinerary()"
          >
            Ok
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
  export default {
    name: 'TourDialog',
    props: {
      dialog: { type: Boolean, required: true },
      hotels: Array,
      restos: Array,
      itinerary: Object,
      places: Array,
      edit: Boolean,
    },
    data: () => ({
      dialogm1: '',
      //cities: ['Antsiranana', 'Ramena'],
      tour: {
        place: null,
        hotel: null,
        resto: null,
        car: null,
      },
    }),
    computed: {
      editMode() {
        return this.edit
      }
    },
    mounted(){
      /*if( this.edit ){
        this.tour = this.itinerary
      }
      else{
        this.tour = {
          place: 1,
          hotel: 1,
          resto: 1,
          car: null,
        }
      }*/
    },
    created(){
/*      this.hotels = this.listings.filter( listing => listing.category == 'Hotels' )
      this.restos = this.listings.filter( listing => listing.category == 'Restaurants' )*/

      // Default value
      if( this.itinerary.place != null ){
        this.tour = this.itinerary
      }
      else{
        this.tour = this.defaultItinerary()
      }

    },
    methods: {
      saveItinerary(){
        /*let tour = {
          place: this.tour.place,
          hotel: this.getItem(this.hotels, this.tour.hotel),
          resto: this.getItem(this.restos, this.tour.resto),
          car: null
        }*/
        //console.log(this.tour)
        this.$emit('filled', this.tour);
      },
      defaultItinerary(){
        return {
          place: 0,
          hotel: undefined,
          resto: undefined,
          car: null,
        }
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
    },
    watch: {
      /* Since we can't watch props, we copy the prop to 
      a computed property "editMode" and then watch it
      for change*/
      editMode: { // detect insert or edit mode
        handler: function(newValue, oldValue){
          if( newValue == true ){
            this.tour = this.itinerary
          }
          else{
            this.tour = this.defaultItinerary()
          }
        }
      }
    },
  }
</script>