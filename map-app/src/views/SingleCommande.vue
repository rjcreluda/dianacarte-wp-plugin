<template>
  <div>
    <v-container>
      <v-row>
        <v-col cols="12">
          <h3 class="mb-5">Commande #{{ commande.id }}</h3>
        </v-col>
      </v-row> 
      <v-row>
        <v-col cols="12">Les services commandés</v-col>
        <v-col cols="12" v-for="service in commande.services" :key="service.id">
          <v-card
            class="mx-auto"
            max-width="100%"
            outlined
          >
            <v-list-item three-line>
              <v-list-item-content>
                <v-list-item-title class="text-body-1 mb-1">
                  Id #{{ service.id }} {{ service.service }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  <p>Quantité: {{ service.qty }}</p>
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card>
        </v-col>
      </v-row>  
    </v-container>
  </div>
</template>

<script>

import axios from 'axios'

export default {
  name: 'Commandes',
  components: {

  },
  data: () => ({
    commande: {
        "id": 6270,
        "client_id": "1",
        "title": "Grande Hotel booking from admin",
        "date": "09-13-2021",
        "time": "15:51",
        "total_price": "",
        "status": "pending",
        "listing": "Grande Hotel",
        "services": [
            {
                "id": "9",
                "service": "4wd Fortuner",
                "qty": "1"
            },
            {
                "id": "10",
                "service": "Suites",
                "qty": "1"
            }
        ]
    },
  }),
  created(){
    /*for( var i = 0; i < 2; i++ ){
      this.packagesTours.push({
        id: i+1,
        name: `My tour ${i+1}`,
      });
    }*/
    /*axios.get(`${window.wpData.rest_url}/wp/v2/dc_tour`, {
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
    .catch( error => console.log(error) )*/
    
  },
  computed: {
   
  },
  methods: {
    
  },  
}
</script>