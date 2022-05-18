import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify'

//import VueMapbox from '@studiometa/vue-mapbox-gl';
import 'mapbox-gl/dist/mapbox-gl.css'; 
import VueTour from 'vue-tour';

//Vue.use(VueMapbox);

require('vue-tour/dist/vue-tour.css')
Vue.use(VueTour)

Vue.config.productionTip = false

/**
* Warning: this is a dummy data for local test without server 
**/
import json_data from '@/dummy_data.json'
if (window.location.port === '8080' && json_data) {
	window.wpData = json_data
}


new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
