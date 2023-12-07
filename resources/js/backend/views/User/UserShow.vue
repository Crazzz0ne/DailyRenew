<template>
  <div class="container">
    <MazBtn
      class="my-2"
      @click="goBack"
    >
      Back
    </MazBtn>
    <div
      v-if="!loading"
      class="card"
    >
      <div class="card-header">
        <h3>{{ user.fullName }}</h3>
        <strong>Hire Date: {{ $date(user.created_at).format('MM/DD/YYYY') }}</strong>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 col-sm-12">
            <MazAvatar
              class="mb-5"
              :src="user.gravatar"
              no-elevation
              square
              :size="160"
            />
            <div class="mb-5">
              Terminated
              <span v-if="user.terminated">
                {{ $date(user.terminated).format('MMM D, YYYY') }}
              </span>
              <MazSwitch
                :value="isTerminated"
                :color="'danger'"
                @input="toggleTerminated"
              />
            </div>
            <MazBtn
              v-if="canLoginAs"
              class="mt-1"
              @click="logInAsUser"
            >
              Log in as
            </MazBtn>
          </div>
          <div class="col-12">
            <p> Phone: {{ user.phone }}</p>
            <p> Email: {{ user.email }}</p>

            <!--            <remote-select :user="user" />-->
            <language-select :user="user" />
<!--            <PayScaleSelect :user="user" />-->
            <TimezoneSelect :user="user" />
          </div>
          <div class="col-12">
            <UserRoleSelect :user="user" />
            <label style="padding-top: 2%">Home Office:</label>
            <MazSelect
              v-model="user.office_id"
              :options="officeList"
              :size="'sm'"
              :placeholder="'Office'"
              @input="changeOffice"
            />
              <UploadUserCity :user="user" />
            <add-city-to-rep
              :user="user"
            />
            <AvailableList
              :user-id="user.id"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import axios from 'axios'
import AvailableList from '../../components/Calender/AvailableList'
import PayScaleSelect from '../../components/Selects/PayScaleSelect'
import TimezoneSelect from '../../components/Selects/TimezoneSelect'
import UserRoleSelect from '../../components/Selects/UserRoleSelect'
import dayjs from 'dayjs'
import UploadUserCity from "../../components/lead/upload/UploadUserCity.vue";

export default {
	name: 'UserShow',
	components: {UploadUserCity, UserRoleSelect, TimezoneSelect, PayScaleSelect, AvailableList },
	data () {
		return {
			user: {
				terminated: false,
				roles: [{ label: 'Default Role', value: null }],
				office_id: -1
			},
			pong: [
				{ label: 'Canvasser', value: 'canvasser' },
				{ label: 'Call Center Opener', value: 'opener' },
				{ label: 'Sales Person One', value: 'sp1' },
				{ label: 'Sales Person Two', value: 'sp2' },
				{ label: 'Team Captain', value: 'team captain' },
				{ label: 'Manager', value: 'manager' }],
			loading: true,
			officeList: []
		}
	},
	computed: {
		...mapGetters([
			'getUser'
		]),
		canLoginAs () {
			if (this.user.id === 1 || this.user.id === 3) {
				return this.getUser.data.id === 1 || this.getUser.data.id === 3
			} else {
				return true
			}
		},

		isTerminated () {
			return this.user.terminated !== null
		}
	},
	created () {
		this.fetchUsers().then(() => {
			this.getUsers()
			this.getOffice()
		})
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
		getOffice () {
			axios.get('/api/office')
				.then((response) => {
					const officeList = response.data.data
					const payload = []
					$.each(officeList, function (key, value) {
						const obj = {
							label: value.name,
							value: value.id
						}
						payload.push(obj)
					})
					this.officeList = payload
					this.labelLoaded = true
					this.selectedOffice = this.officeId
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		changeOffice () {
			const data = {
				office_id: this.user.office_id
			}
			axios.put(`/api/user/${this.$route.params.userId}/office`, data)
				.catch((errors) => {
					console.log(errors)
				})
		},
		logInAsUser () {
			this.$router.push(`/dashboard/auth/user/${this.user.id}/login-as`)
			this.$router.go()
		},

		goBack (id) {
			this.$router.push({ path: '/dashboard/user' })
		},
		getUsers () {
			axios.get(`/api/user/${this.$route.params.userId}`).then((response) => {
				this.user = response.data.data
				// this.getOfficeRoles()
				this.loading = false
			})
				.catch((errors) => {
					console.log(errors)
				})
		},

		getOfficeRoles () {
			axios.get(`/api/office/${this.user.office_id}/role`).then((response) => {
				this.roles = response.data
			}).catch((errors) => {
				console.log(errors)
			})
		},
		toggleTerminated () {
			axios.post(`/api/user/${this.$route.params.userId}/terminate`).then((response) => {
				if (response.data.terminated) {
					this.user.terminated = dayjs()
				} else this.user.terminated = false
			}).catch((errors) => {
				console.log(errors)
			})
		}

	}
}
</script>

<style scoped>

</style>
