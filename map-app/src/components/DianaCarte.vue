<template>
	<div>
    <mapbox-map
    :accessToken="accessToken"
    :mapStyle="mapStyle"
    :center="center"
    :zoom="zoom"
    @mb-created="(mapboxInstance) => map = mapboxInstance">
      <div v-if="listings.length > 0">
        <mapbox-marker v-for="listing in listings" :lng-lat="[listing.location.longitude, listing.location.latitude]" :key="listing.id" />
      </div>
    </mapbox-map> 
  </div>
</template>
<script>

  export default {
    name: 'DianaCarte',
    props: ['listings'],
    /*component: {
      GlMap
    },*/
    data: () => ({
      mapStyle: 'mapbox://styles/mapbox/streets-v11',
      accessToken: 'pk.eyJ1IjoiamluZHMiLCJhIjoiY2tzMGVjcG5wMG1tbDJvcGp1d29lMnE5NyJ9.mADwLymdXk6vjhAxH0qxqA',
      center: [49.29149481988657, -12.277539696362235],
      zoom: 12,
      map: null
    }),
  }
</script>

<style>
  .mapboxgl-map{
    width: 100%;
    height: 600px;
  }
</style>