<template>
  <div class="d-flex">
    <MazSelect
      v-model="user"
      :options="users"
    />
    <MazBtn
      class="ml-2"
      color="success"
      :loading="loading"
      :disabled="disableSubmit"
      icon-name="grading"
      size="sm"
      fab
      @click="addUser()"
    />
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'UserSelectByOffice',
	props: [
		'officeId'
		// 'users'
	],
	data () {
		return {
			users: [],
			user: null,
			loading: false
		}
	},
	computed: {
		disableSubmit () {
			if (this.user === null) {
				return true
			} else {
				return false
			}
		},
		userList () {
			const payload = []
			this.users.forEach(function (value) {
				payload.push({ label: value.name, value: value })
			})
			return payload
		}
	},
	created () {
		this.getUsers()
	},
	methods: {
		getUsers () {
			this.loading = true
			axios.get(`/api/salesflow/user/position?officeId=${this.officeId}&position=sp2`)
				.then((response) => {
					this.users = response.data
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
			this.userLoaded = true
		},
		addUser () {
			axios.post(`/api/office/${this.officeId}/round-robin`, { userId: this.user })
				.then((response) => {
					this.$emit('newUser')
					this.user = null
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		selectedUser () {

		}
	}
}
</script>

<style scoped>

</style>
