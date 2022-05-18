<template>
  <v-card>
    <v-card-title>
      Itinéraire vers {{ place }} 
      <v-spacer></v-spacer>
      <v-icon>mdi-call-split</v-icon>
    </v-card-title>
    <v-card-text v-if="data">
      <span class="duration">
        Durée du trajet: <Time :second="data.duration" /> environ <br>
        Longueur du trajet: {{ data.distance }} m
        <ul>
          <li v-for="(instruction, i) in data.instructions" :key="i">
            {{ instruction }}
          </li>
        </ul>
      </span>
    </v-card-text>
  </v-card>
</template>

<script>
  import Time from '@/components/Time.vue'
  export default {
    name: 'Itinerary',
    props: {
      place: { type: String, required: true },
      data: { type: Object, required: true }
    },
    components: {
      Time
    }
  }
</script>