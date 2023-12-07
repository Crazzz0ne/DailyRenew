<template>
  <div class="card">
    <div class="card-header">
      <h4>Round Robin</h4>
    </div>
    <div class="card-body">
      <UserSelectByOffice
        class="my-3"
        :office-id="office_id"
        @newUser="getRR()"
      />
      <h5>Cities</h5>
      <city-select
        class="mb-2"
        :office-id="office_id"
        :selected-cities="selectedCities"
        :all-cities="allCities"
      />
      <div class="row">
        <div
          v-for="user in users"
          class="col-sm-6 col-md-2 text-center"
          style="display: flex;
                         justify-content: center;"
        >
          <div class="text-center">
            <button
              type="button"
              class="close"
              aria-label="Close"
              @click="deleteUser(user, office_id)"
            >
              <span aria-hidden="true">&times;</span>
            </button>
            <MazAvatar
              style="margin: auto;"
              :size="avatarSize"
              :src="user.avatar"
            />
            {{ user.name }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import EligibleCity from '../../views/Settings/EligibleCity'

import axios from 'axios'
import UserSelectByOffice from '../Selects/UserSelectByOffice'

export default {
	name: 'EligibleCityByOfficeCitySelectGroup',
	components: { UserSelectByOffice, EligibleCity },
	props: [
		'office_id'
	],
	data: function () {
		return {
			allCities: [],
			users: {},
			avatarSize: 100,
			selectedCities: []
		}
	},
	created () {
		this.getRR()
		this.getAllCities()
	},
	methods: {
		getRR () {
			this.loading = true
			axios.get(`/api/office/${this.office_id}/city-list-for-user-r-r`)
				.then((response) => {
					this.users = response.data.usersWithList
					this.selectedCities = response.data.selectedCities
					console.log(response, 'res')
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
			this.userLoaded = true
		},
		getAllCities () {
			axios.get('/api/office/all-eligible-city-tags')
				.then((response) => {
					this.allCities = response.data
					console.log(response, 'res')
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		deleteUser (user, officeId) {
			Swal.fire({
				title: `Remove the ${user.name}?`,
				text: 'Are you sure?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, Remove it!'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/office/${officeId}/round-robin`, {
						params: {
							userId: user.id
						}
					})
						.then((response) => {
							this.draw++
							Swal.fire(
								'Deleted!',
								'The Lead has been deleted.',
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
