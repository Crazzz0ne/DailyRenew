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
          {{ city }}
          <!--                    <MazBtn class="float-right" @click="deleteCity(city.id)" color="danger"-->
          <!--                            iconName="delete" size="sm" fab></MazBtn>-->
        </div>
      </MazListItem>
    </MazList>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'EligibleCitiesByOffice',
	components: {},
	props: ['officeId', 'allCities'],
	data () {
		return {

		}
	},

	created () {

	},

	methods: {

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
