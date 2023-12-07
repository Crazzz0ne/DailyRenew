<template>
  <div class="row pt-5">
    <div class="col-sm-12">
      <h5>Choose the cities you would like to service</h5>

      <city-select
        class="mb-2"
        :user-id="user.id"
        :selected-cities="selectedCities"
        :all-cities="allCities"
        @selected-cities="selectedCities = selectedCities"
      />
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'AddCityToRep',
	props: [
		'user'

	],
	data: function () {
		return {
			selectedCities: [],
			allCities: []
		}
	},
	computed: {

	},
	created () {
		this.getAllCities()
		this.getSelectedCities()
	},
	methods: {
		getAllCities () {
			axios.get(`/api/office/${this.user.office_id}/eligible-city-tags`)
				.then((response) => {
					this.allCities = response.data
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		getSelectedCities () {
			axios.get(`/api/user/${this.user.id}/get-selected-cities`)
				.then((response) => {
					this.selectedCities = response.data
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>

</style>
