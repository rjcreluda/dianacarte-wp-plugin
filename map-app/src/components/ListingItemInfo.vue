<template>
  <v-card flat>
    <v-card-title>{{ listing.name }}</v-card-title>
    <v-card-text>
      <v-row
        align="center"
        class="mx-0"
      >
        <v-rating
          v-model="listing.rating.average"
          :value="listing.rating.average"
          color="amber"
          dense
          readonly
          size="14"
        ></v-rating>

        <div class="grey--text ms-4">
          {{ listing.rating.average }} <!-- ({{ listing.rating.count }}) -->
        </div>
      </v-row>
      <div class="text-subtitle-1 my-4">
        • {{ listing.category }}
      </div>
      <div>{{ description }}</div>
      <v-divider class="mt-5"></v-divider>
      <v-list dense>
        <v-list-item>
          <v-list-item-icon>
            <v-icon color="indigo">
              mdi-phone
            </v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>{{ listing.contact.phone }}</v-list-item-title>
            <v-list-item-subtitle>Phone</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
        <v-divider inset></v-divider>
        <v-list-item>
          <v-list-item-icon>
            <v-icon color="indigo">
              mdi-map-marker
            </v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ listing.contact.address }}</v-list-item-title>
            <v-list-item-subtitle>Antsiranana</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-card-text>
    <v-card-actions>
      <v-btn
        color="deep-purple lighten-2"
        text
        @click="findMore( listing.link )"
      >
        Voir la page
      </v-btn>
      <v-btn
        color="deep-purple lighten-2"
        text
        @click="passEvent( listing )"
      >
        Itinéraire
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
  export default{
    name: 'ListingItemInfo',
    props: {
      listing: { required: true }
    },
    computed: {
      description(){
        let text = this.listing.content.replace(/(<([^>]+)>)/gi, "")
        if( text.length > 200 )
          text = text.substring(0, 200) + '...'
        return text
      }
    },
    methods: {
      findMore( link ){
        window.location = link
      },
      passEvent( listing ){
        this.$emit('askDirection', this.listing)
      }
    }
  }
</script>