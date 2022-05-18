<template>
  <div>
    <v-container>
      <v-row>
        <v-col cols="12">
          <h3 class="mb-5">Mes voyages</h3>
        </v-col>
      </v-row> 
      <v-row>
        <v-col cols="12" md="4">
          <v-card outlined>
            <v-card-text>
              <v-btn text @click="$router.push({ name: 'CreatePackage'})">
              <v-icon>mdi-plus</v-icon>Créer nouveau</v-btn>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="4" v-for="tour in packagesTours" :key="tour.id">
          <v-card
            class="mx-auto"
            max-width="100%"
            outlined
          >
            <v-list-item three-line>
              <v-list-item-content>
                <v-list-item-title class="text-body-1 mb-1">
                  {{ tour.name }}
                </v-list-item-title>
                <v-list-item-subtitle>Greyhound divisely hello coldly fonwderfully</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-card-actions>
              <v-btn
                outlined
                rounded
                text
                @click="$router.push({ name: 'SinglePackage', params: { id: tour.id}})"
              >
                Voir
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>  
    </v-container>
  </div>
</template>

<script>

import axios from 'axios'

export default {
  name: 'MyPackages',
  components: {

  },
  data: () => ({
    packagesTours: []
  }),
  created(){
    /*for( var i = 0; i < 2; i++ ){
      this.packagesTours.push({
        id: i+1,
        name: `My tour ${i+1}`,
      });
    }*/
    axios.get(`${window.wpData.rest_url}/wp/v2/dc_tour`, {
      params: { user_id: 1}
    })
    .then( (response) => {
      console.log(response)
      for( const tour of response.data ){
        this.packagesTours.push({
          id: tour.id,
          name: tour.title.rendered,
        });
      }
    })
    .catch( error => console.log(error) )
    
  },
  computed: {
   
  },
  methods: {
    
  },  
}
</script>