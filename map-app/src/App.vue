<template>
  <v-app id="inspire">
    <v-card
    flat
    tile
  >
    <v-toolbar>
      <!-- <v-toolbar-title>App</v-toolbar-title> -->
      <!-- <v-spacer></v-spacer> -->
      <v-btn text v-for="(link, index) in links" :key="index" @click="navigateTo(link)">
        {{ link.title }}
      </v-btn>
      <v-btn icon>
        <v-icon>mdi-magnify</v-icon>
      </v-btn>
      <v-btn icon>
        <v-icon>mdi-heart</v-icon>
      </v-btn>
      <v-btn icon>
        <v-icon>mdi-dots-vertical</v-icon>
      </v-btn>
    </v-toolbar>
  </v-card>
    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
  export default {
    name: 'App',
    data: () => ({
      links: [
        { title: 'Carte', to: 'Map'},
        { title: 'Voyages', to: 'MyPackages'},
        { title: 'Reservation', to: 'Commandes'},
      ]
    }),
    methods: {
      navigateTo( link ){
        this.$router.push({ name: link.to })
      }
    }
  }
</script>