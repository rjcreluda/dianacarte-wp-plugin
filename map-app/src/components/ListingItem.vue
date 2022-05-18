<template>
  <v-lazy
        v-model="isActive"
        :options="{
          threshold: .5
        }"
        transition="fade-transition"
      >
  <v-card
    :loading="loading"
    class="rounded-0 mb-2"
    max-width="100%"
    outlined
    flat
  >
  <template slot="progress">
    <v-progress-linear
      color="deep-purple"
      height="10"
      indeterminate
    ></v-progress-linear>
  </template>
  <v-row no-gutters>
    <v-col cols="6" class="listing-item-img-col">
      <div class="text-hover orange darken-1">
        <span class="white--text">{{ listing.category }}</span>
      </div>
      <v-img
        :src="listing.thumbnail_url"
        width="100%"
        height="100%"
        lazy-src="https://picsum.photos/id/143/300/200"
      ></v-img>
    </v-col>
    <v-col>
      <v-card-title class="text-body-2 text-left">
        {{ listing.name }}
      </v-card-title>
      <v-card-subtitle class="text-caption text-left">
        {{ address }}
        <v-rating
          :value="rating"
          background-color="yellow"
          color="yellow accent-4"
          dense
          small
          readonly
        ></v-rating>
      </v-card-subtitle>
      <v-card-text>
        {{ listing.price.min }}€ - {{ listing.price.max }}€
      </v-card-text>
      <v-card-actions class="mt-5">
        <span class="text-body-2 green--text text-caption ml-2">Open</span>
        <v-spacer></v-spacer>
        <v-tooltip top v-for="button in buttons" :key="button.icon">
          <template v-slot:activator="{ on, attrs }">
            <v-btn icon 
              v-bind="attrs"
              v-on="on"
              @click="passEvent(button.icon)">
              <v-icon>{{ button.icon }}</v-icon>
            </v-btn>
          </template>
          <span>{{ button.label }}</span>
        </v-tooltip>
      </v-card-actions>
    </v-col>
  </v-row>
  </v-card>
</v-lazy>
</template>

<script>
  export default {
    name: 'ListinItem',
    props: ['listing'],
    data: () => ({
      show: false,
      loading: false,
      isActive: false,
      placeholder_image: 'http://via.placeholder.com/640x360',
      buttons: [
        { label: 'Localisation', icon: 'mdi-map-marker' },
        { label: 'Itinéraire', icon: 'mdi-call-split' },
      ]
    }),
    computed: {
      address(){
        if( this.listing.contact.address.length == 0 )
          return 'n/a'
        else
          return this.listing.contact.address
      },
      rating(){
        return Number(this.listing.rating.average)
      },
    },
    methods: {
      passEvent( buttonIcon ){
        if( buttonIcon == 'mdi-map-marker' ){
          console.log('click on marker')
          this.$emit('showInMap', this.listing)
          /*this.$store.map.jumpTo({
            center: [this.listing.location.longitude, this.listing.location.latitude],
            //zoom: 8,
            pitch: 45,
          });*/
          //panTo([this.listing.location.longitude, this.listing.location.latitude], {duration: 2000})
        }
        else if(buttonIcon == 'mdi-call-split'){
          this.$emit('askDirection', this.listing)
        }
      },
    }
  }
</script>

<style>
  .listing-item-img-col{
    position: relative;
  }
  .listing-item-img-col .text-hover{
    position: absolute;
    top: 20px;
    z-index: 2;
    font-size: 0.7em;
    text-transform: uppercase;
    margin-left: 20px;
    padding: 5px 8px;
    color: #fff;
    border-radius: 4px;
    z-index: 5;
    -webkit-box-shadow: 0 0 0 5px hsla(0,0%,100%,.4);
    box-shadow: 0 0 0 5px hsla(0,0%,100%,.4);
  }
</style>