<template>
  <v-card
    :loading="loading"
    class="mx-auto pa-0"
    width="250"
    flat
  >
    <template slot="progress">
      <v-progress-linear
        color="deep-purple"
        height="10"
        indeterminate
      ></v-progress-linear>
    </template>

    <v-img
      height="150"
      :src="listing.thumbnail_url"
      lazy-src="https://picsum.photos/id/143/300/200"
    >
      <template v-slot:placeholder>
        <v-row
          class="fill-height ma-0"
          align="center"
          justify="center"
        >
          <v-progress-circular
            indeterminate
            color="grey lighten-5"
          ></v-progress-circular>
        </v-row>
      </template>
    </v-img>
    <v-card-title>{{ listing.name }}</v-card-title>
    <v-card-text>
      <div class="text-body-2">
        {{ listing.category }}
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
  export default {
    name: 'ListinItemInfoMap',
    props: ['listing'],
    data: () => ({
      loading: false,
      selection: 1
    }),
  }
</script>