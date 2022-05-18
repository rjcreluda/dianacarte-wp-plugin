<template>
  <div class="home">
    Categories
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
  mounted(){
    this.$store.commit('set_page_title', 'Categories')
  },
}
</script>
