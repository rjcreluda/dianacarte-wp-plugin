import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    map: null,
    askDirection: false, // check whether run ask direction on map click
    packagesTour: [],
    listings: [],
  },
  mutations: {
    set_listings( state, data ){
      state.listings = data
    },
    set_page_title(state, payload){
      state.pageTitle = String(payload)
    },
    add_tour(state, tour){
      state.packagesTour.push(tour)
    },
    toggleAskDirection( state, val ){
      state.askDirection = Boolean(val) 
    }
  },
  actions: {
    get_listings( context, url ){
      axios.get( url )
      .then( response => {
        context.commit('set_listings', response.data)
      })
      
    }
  },
  modules: {
  }
})
