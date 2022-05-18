mapboxgl.accessToken = 'pk.eyJ1IjoiamluZHMiLCJhIjoiY2tzMGVjcG5wMG1tbDJvcGp1d29lMnE5NyJ9.mADwLymdXk6vjhAxH0qxqA';
/*var map = new mapboxgl.Map({
	container: 'map',
	style: 'mapbox://styles/mapbox/streets-v11',
	center: [-74.5, 40], // starting position
	zoom: 9 // starting zoom
});*/
// Add zoom and rotation controls to the map.
//map.addControl(new mapboxgl.NavigationControl());

Vue.component('carte-app', {
	props: ['data'],
	template: `<div id='map' style='width: 100%; height: 500px;'></div>`,
	created(){
		//console.log(this.data);
	}
});

const vm = new Vue({
	el: '#dianacarte-app',
	components: {
	    'MglMap': window.VueMapbox.MglMap
	 },
	data() {
		return {
			message: 'MY MAP',
			//MglMap: window.VueMapbox.MglMap
			access_token: 'pk.eyJ1IjoiamluZHMiLCJhIjoiY2tzMGVjcG5wMG1tbDJvcGp1d29lMnE5NyJ9.mADwLymdXk6vjhAxH0qxqA',
			//mapStyle: 'mapbox://styles/mapbox/basic-v9'
			mapStyle: 'mapbox://styles/mapbox/streets-v11',
			map: undefined,
			userAddress: '',
			center: [49.29149481988657, -12.277539696362235],
			places: []
		}
	},
	mounted(){
		this.map = new mapboxgl.Map({
			container: 'map',
			style: 'mapbox://styles/mapbox/streets-v11',
			center: this.center, // starting position
			zoom: 12 // starting zoom
		})
		this.map.addControl(new mapboxgl.NavigationControl());
		// Create a new marker.
		const marker = new mapboxgl.Marker()
		    .setLngLat(this.center)
		    .addTo(this.map);
	}
});