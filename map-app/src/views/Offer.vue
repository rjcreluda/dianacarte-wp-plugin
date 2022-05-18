<template>
  <div class="home">
    <h3>Développeur JS</h3>
    <p>Par: iDev Company</p><br>
    <p>
      Lorem, ipsum dolor sit amet consectetur, adipisicing elit. Ab mollitia delectus ad quibusdam natus rem a dolorum, esse, tempore vel, deserunt hic sit cumque, voluptatibus? Adipisci quasi dolorum, ducimus neque.
    </p>
    <p>
      Type de contrat: CDI
    </p>
    <h5>Missions</h5>
    <ul>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
    </ul>
    <h5>Compétences requis</h5>
    <ul>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
    </ul>
    <h5>Expériences requis</h5>
    <ul>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
      <li>lorem natus dolor</li>
    </ul>
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
    this.$store.commit('set_page_title', 'Développeur JS')
  },
  created(){ 
    //console.log(this.$router.params.id)
  }
}
</script>
