<template>
  <div>
    <MazList
      class="scrollable-list "
      :style="'max-height:500px;'"
    >
      <MazListItem
        v-for="(city, index) in allCities"
        :key="index"
      >
        <div class="py-2">
          {{ city.name.en }}
          <MazBtn
            class="float-right"
            color="danger"
            icon-name="delete"
            size="sm"
            fab
            @click="deleteCity(city.id)"
          />
        </div>
      </MazListItem>
    </MazList>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'EligibleCities',
	components: {},
	props: {
		officeId: String
	},
	data () {
		return {
			allCities: []
		}
	},
	created () {
		this.getCities()
		Echo.private('settings.eligibleCity.1')
			.listen('Backend.Settings.EligibleCityAddedEvent', (e) => {
				this.allCities.push(e.payload)
			})
	},
	methods: {
		getCities () {
			axios.get('/api/settings/eligible-city')
				.then((response) => {
					console.log(response)
					this.allCities = response.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		deleteCity (city) {
			Swal.fire({
				title: 'Delete?',
				text: 'Are you sure?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.value) {
					const data = {
						city: city
					}
					axios.delete('/api/settings/eligible-city', { data })
						.then((response) => {
							this.getCities()
							Swal.fire(
								'Deleted!',
								'The city has been removed.',
								'success'
							)
						}).catch((e) => {
							console.log(e, 'customer update error')
						})
				}
			})
		}

	}

}
</script>

<style scoped>

</style>
