<template>
  <div>
    <v-list
      v-if="offers.length"
      class="pt-0"
      flat
      two-line
    >
      <div
        v-for="offer in offers"
        :key="offer.id"
      >
        <v-list-item
          @click="showOffer(offer.id)"
          :class="{ 'blue lighten-5' : offer.done }"
        >
          <template v-slot:default>
            <v-list-item-action>
              <v-checkbox
                :input-value="offer.done"
                color="primary"
              ></v-checkbox>
            </v-list-item-action>

            <v-list-item-content>
              <v-list-item-title
                :class="{ 'text-decoration-line-through' : offer.done }"
              >
                {{ offer.title }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ offer.description }}
              </v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn
                @click.stop="deleteoffer(offer.id)"
                icon
              >
                <v-icon color="primary lighten-1">mdi-delete</v-icon>
              </v-btn>
            </v-list-item-action>
          </template>

        </v-list-item>
        <v-divider></v-divider>
      </div>
    </v-list>
  </div>
</template>

<script>
// @ is an alias to /src
//import HelloWorld from '@/components/HelloWorld.vue'

export default {
  name: 'Accueil',
  components: {
    //HelloWorld
  },
  data: () => ({
    offers: [
        {
          id: 1,
          title: 'Développeur PHP',
          description: 'lorem ispum',
          done: false
        },
        {
          id: 2,
          title: 'Testeur Front End',
          description: 'lorem ispum',
          done: false
        },
        {
          id: 3,
          title: 'Administrateur de base de données',
          description: 'lorem ispum',
          done: false
        },
        {
          id: 4,
          title: 'Un webmaster',
          description: 'lorem ispum',
          done: false
        }
      ],
  }),
  methods: {
    showOffer( id ){
      let offer = this.offers.filter( (offer) => offer.id == id )[0]
      this.$router.push({ name: 'Offer', params: {id: offer.id}})
    }
  },
  mounted(){
    this.$store.commit('set_page_title', 'Mada Job')
  },
  created(){
    fetch('http://localhost:8000/api/offres')
    .then( (response) => {
      console.log(response)
    } )
  }
}
</script>
