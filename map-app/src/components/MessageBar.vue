<template>
  <v-snackbar
      v-model="snackbar"
      :timeout="timeout"
    >
      {{ text }}

      <template v-slot:action="{ attrs }">
        <v-btn
          text
          v-bind="attrs"
          @click="snackbar = false"
          :rounded="true"
        >
          Fermer
        </v-btn>
      </template>
    </v-snackbar>
</template>

<script>
export default {
  name: 'MessageBar',
  props: ['text'],
  data: () => ({
    snackbar: true,
    timeout: 5000, 
  }),
}
</script>