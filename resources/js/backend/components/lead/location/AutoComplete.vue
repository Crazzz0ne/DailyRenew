<template>
  <div v-if="loaded">
    <gmap-autocomplete
      placeholder="Customer's Address"
      class="form-control"
      :options="{
        bounds: {
          north: northCord,
          south: southCord,
          east: eastCord,
          west: westCord
        },
        strictBounds: false
      }"
      @place_changed="setUserAddress"
    />
    <!--        <button class="btn btn-primary float-right" @click="usePlace">Add</button>-->
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
	name: 'AutoComplete',
	props: [
		'userLat',
		'userLong'
	],
	data () {
		return {
			markers: [],
			place: null,
			location: null,
			gettingLocation: false,
			errorStr: null,
			n: 1,
			s: 2,
			e: 3,
			w: 4,
			loaded: false
		}
	},
	mounted () {
		// At this point, the child GmapMap has been mounted, but
		// its map has not been initialized.
		// Therefore we need to write mapRef.$mapPromise.then(() => ...)

	},
	created () {
		if (!('geolocation' in navigator)) {
			this.errorStr = 'Geolocation is not available.'
			return
		}

		this.gettingLocation = true
		// get position
		navigator.geolocation.getCurrentPosition(pos => {
			console.log(pos)
			this.gettingLocation = false
			this.location = pos
			this.loaded = true
		}, err => {
			this.gettingLocation = false
			this.errorStr = err.message

			// Unable to get an exact location, so lets estimate one.
			this.location = {}
			const coords = {
				latitude: this.userLat,
				longitude: this.userLong
			}
			this.location = {
				coords
			}
			this.loaded = true
		})
	},

	computed: {
		...mapGetters(['getLead']),
		northCord: function () {
			return this.location.coords.latitude + 0.16
		},
		southCord: function () {
			return this.location.coords.latitude - 0.16
		},
		eastCord: function () {
			return this.location.coords.longitude + 0.16
		},
		westCord: function () {
			return this.location.coords.longitude - 0.16
		}

	},
	methods: {
		...mapActions(['setUserAddress)']),
		setDescription (description) {
			this.description = description
		},
		LatLng: function () {
			this.e = this.location.coords.longitude + 0.16
			this.w = this.location.coords.longitude - 0.16
			this.s = this.location.coords.latitude - 0.16
			this.n = this.location.coords.latitude + 0.16

			return []
		},

		setUserAddress (place) {
			console.log(place)
			const address = {}
			address.street_address = place.address_components[0].short_name + ' ' + place.address_components[1].short_name
			address.city = place.vicinity

            //filter address_components for administrative_area_level_1
            const state = place.address_components.filter(function (item) {
                return item.types[0] === 'administrative_area_level_1'
            })

            address.state = state[0].short_name


            //filter address_components for zip code
            const zip = place.address_components.filter((component) => {
                return component.types.includes('postal_code')
            })
            console.log('zip', zip)

			address.zip = zip[0]['short_name']
			address.lat = place.geometry.location.lat()
			address.lng = place.geometry.location.lng()

			// console.log(place.geometry.location, 'place!')
			// console.log('address', place)
			this.$emit('place', address)
			// this.$store.commit('SET_USER_ADDRESS', address);
		},
		usePlace (place) {
			if (this.place) {
				if (this.getUser.data.office_id === 10) {
					this.markers.push({

						position: {
							latitude: 34.04956189664171,
							longitude: -118.24488349487008
						}
					})
				} else {
					this.markers.push({

						position: {
							lat: this.place.geometry.location.lat(),
							lng: this.place.geometry.location.lng()
						}
					})
				}

				this.place = null
			}
		}
		// getLocation: function () {
		//     const getPosition = () => {
		//         return new Promise((res, rej) => {
		//             navigator.geolocation.getCurrentPosition(res, rej)
		//         });
		//     };
		//     getPosition()
		//         .then((response) => {
		//             // console.log(response);
		//             this.location = response.coords;
		//         });
		// },

	}

}
</script>

<style scoped>
input {
  /*width: 80%;*/
  /*display: inline;*/
}

.btn {
  margin-left: 5%;
}
</style>
