<template>
  <div>
    <v-container v-if="tourData">
      <v-row>
        <v-col cols="12">
          <h3 class="mb-5">{{ tourData.tour.name }} 
            <v-btn @click="editTour()" text>
              <v-icon>mdi-pen</v-icon>
            </v-btn>
          </h3>
          <p>Personnes: {{ tourData.tour.persons }}</p>
        </v-col>
      </v-row> 
      <v-row>
        <v-col cols="12">
          <v-timeline dense>
              <v-slide-x-reverse-transition
                group
                hide-on-leave
              >
                <v-timeline-item
                  v-for="(itineraire, index) in tourData.itineraires" :key="index"
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
                        Départ: {{ itineraire.place.name }}
                      </span>
                      <span v-else>
                        {{ itineraire.place.name }}
                      </span>
                    </v-card-subtitle>
                    <v-card-text>
                      <ul>
                        <li>Hotel: 
                          <span v-if="itineraire.hotel != false">{{ itineraire.hotel.name }}</span>
                          <span v-else>n/a</span>
                        </li>
                        <li>
                          Restaurant:
                          <span v-if="itineraire.resto != false">{{ itineraire.resto.name }}</span>
                          <span v-else>n/a</span>
                        </li>
                      </ul>
                    </v-card-text>
                  </v-card>
                </v-timeline-item>
              </v-slide-x-reverse-transition>
            </v-timeline>
        </v-col>
      </v-row>  
    </v-container>
  </div>
</template>

<script>

import axios from 'axios'

export default {
  name: 'SinglePackage',
  components: {

  },
  data: () => ({
    tourData: undefined
  }),
  created(){
    /*for( var i = 0; i < 2; i++ ){
      this.packagesTours.push({
        id: i+1,
        name: `My tour ${i+1}`,
      });
    }*/
    let tour_id = Number(this.$route.params.id);
    axios.get(`${window.wpData.rest_url}/dianacarte/tours/${tour_id}`)
    .then( (response) => {
      this.tourData = response.data
      console.log(this.tourData)
    })
    .catch( error => console.log(error) )
    
  },
  computed: {
   
  },
  methods: {
    editTour(){
      this.$router.push({name: 'EditPackage', params: {id: this.$route.params.id}})
    }
  },  
}
</script>