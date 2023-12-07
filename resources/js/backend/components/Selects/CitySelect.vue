<template>
  <div v-if="allCities.length">
    <div>
      <h5>Current Cities</h5>
      <p> {{ cityListString }}</p>
    </div>
    <MazSelect
      v-model="selectedCities"
      :options="allCities"
      multiple
      :list-height="420"
      clearable
      search
      search-placeholder="Search for city"
      size="sm"
      color="info"
      @input="saveChanges"
    >
      <i
        slot="icon-left"
        class="material-icons"
      >
        add_circle_outline
      </i>
    </MazSelect>
  </div>
</template>

<script>
import { MazSelect } from 'maz-ui'
import axios from 'axios'

export default {
	name: 'CitySelect',
	components: { MazSelect },
	props: [
		'officeId',
		'selectedCities',
		'allCities',
		'userId'
	],
	data () {
		return {
			placeholder: 'Select Service City',
			propSelectedCities: this.selectedCities
		}
	},
	computed: {
		cityListString () {
			return this.selectedCities.join(', ')
		}
	},
	watch: {
		// officeId: function (newVal, oldVal) {
		//     this.getOfficeCities();
		// },
	},
	created () {
		// this.fetchUsers();
		// this.getOfficeCities();
		// this.getAllCities();
	},
	methods: {
		// ...mapActions([
		//     "fetchUsers"
		// ]),
		// getOfficeCities(){
		//
		//     axios.get(`/api/office/${this.officeId}/eligible-city-tags`)
		//         .then((response) => {
		//             console.log(response)
		//             this.selectedCities = response.data
		//             this.$emit('selected-cities', this.selectedCities)
		//         })
		//         .catch(function (error) {
		//             console.log(error);
		//         });
		//
		// },

		saveChanges () {
			if (this.userId) {
				axios.post(`/api/user/${this.userId}/eligible-city-tags`,
					{
						city: this.selectedCities
					})
					.then((response) => {
						console.log(response)
						// this.$emit('selected-cities', this.selectedCities)
					})
					.catch(function (error) {
						console.log(error)
					})
			} else {
				axios.post(`/api/office/${this.officeId}/eligible-city-tags`,
					{
						city: this.selectedCities
					})
					.then((response) => {
						console.log(response)
						// this.$emit('selected-cities', this.selectedCities)
					})
					.catch(function (error) {
						console.log(error)
					})
			}
			this.$emit('selected-cities', this.selectedCities)
		}

	}

}
</script>

<style scoped>

</style>
