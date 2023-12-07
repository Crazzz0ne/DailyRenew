<template>
  <div>
    <div class="row justify-content-end">
      <div class="col-12 py-2">
        <MazBtn
          class="float-right"
          icon-name="update"
          :loading="loading"
          fab
          @click="getOfficeRR()"
        />
      </div>
    </div>
    <div>
      <MazList>
        <MazListItem

          v-for="(office, index) in calcOfficeRR"
          :key="index"
        >
          <p>{{ index + 1 }} {{ office.name }}</p>
          <UserSelectByOffice
            class="my-3"
            :users="office.userList"
            :office-id="office.officeId"
            @newUser="getOfficeRR()"
          />
          <h5>Cities</h5>
          <city-select
            class="mb-2"
            :office-id="office.officeId"
            :selected-cities="office.cities"
            :all-cities="allCities"
          />

          <div class="row">
            <div
              v-for="user in office.userList"
              class="col-sm-6 col-md-2 text-center"
              style="display: flex;
                         justify-content: center;"
            >
              <div class="text-center">
                <button
                  type="button"
                  class="close"
                  aria-label="Close"
                  @click="deleteUser(user, office.officeId)"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
                <MazAvatar
                  style="margin: auto;"
                  :size="avatarSize"
                  :src="user.gravatar"
                />
                {{ user.name }}
              </div>
            </div>
          </div>
        </MazListItem>
      </MazList>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Office from '../lead/Office/Office'
import UserSelectByOffice from '../Selects/UserSelectByOffice'

export default {
	name: 'CallCenterList',
	components: { UserSelectByOffice, Office },
	props: [
		'draw'
	],
	data () {
		return {
			roundRobins: [],
			users: [],
			loading: false,
			avatarSize: 100,
			allCities: []
		}
	},
	computed: {
		calcOfficeRR () {
			const array = []

			return this.roundRobins.map((office) => {
				const users = []

				office.list.forEach(index => {
					// console.log(index, 'this is it')
					users.push(this.users.find(u => u.id === index))
				})

				return {
					id: office.id,
					officeId: office.office_id,
					name: office.name,
					userList: users,
					cities: office.cities

				}
			})
		}

	},
	watch: {
		draw: function (newVal, oldVal) {
			this.getOfficeRR()
		}
	},
	created () {
		window.addEventListener('resize', this.setAvatarSize)
		this.getOfficeRR()
		this.getAllCities()
	},
	destroyed () {
		window.removeEventListener('resize', this.setAvatarSize)
	},
	methods: {
		getAllCities () {
			axios.get('/api/office/all-eligible-city-tags')
				.then((response) => {
					console.log(response)
					this.allCities = response.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		getOfficeRR () {
			this.loading = true
			axios.get('/api/round-robin')
				.then((response) => {
					this.roundRobins = response.data.roundRobins
					this.users = response.data.users
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		setAvatarSize (e) {
			console.log(e.target.outerWidth)
			if (e.target.outerWidth > 600) {
				this.avatarSize = 90
			} else if (e.target.outerWidth > 380) {
				this.avatarSize = 60
			} else {
				this.avatarSize = 60
			}
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
