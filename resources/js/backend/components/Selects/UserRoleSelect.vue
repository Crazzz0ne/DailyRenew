<template>
  <div>
    <label style="padding-top: 2%">Positions:</label>
    <MazSelect
      v-model="roles"
      placeholder="Select Positions"
      :multiple="true"
      :options="positionListComp"
      @input="updateRoles()"
    />
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'UserRoleSelect',
	props: {
		user: {
			type: Object,
			required: true,
			default: () => ({
				id: 0,
				office: { call_center: false }
			})
		}
	},
	data () {
		return {
			roles: ['sp1', 'sp2'],
			positionList: [
				{ label: 'Canvasser', value: 'canvasser' },
				{ label: 'Call Center Opener', value: 'opener' },
				{ label: 'Sales Person One', value: 'sp1' },
				{ label: 'Door 2 Door Sales Person One', value: 'door 2 door sp1' },
				{ label: 'Sales Person Two', value: 'sp2' },
				{ label: 'Team Captain', value: 'team captain' },
				{ label: 'Manager', value: 'manager' }
			]

		}
	},
	computed: {
		positionListComp () {
			if (!this.user.office.call_center) {
				return this.positionList.filter((b) => {
					return b.value !== 'door 2 door sp1'
				})
			}
			return this.positionList
		}
	},
	watch: {

	},
	created () {
		this.setRoles()
	},
	methods: {
		setRoles () {
			const array = []
			this.user.roles.forEach((e) => {
				array.push(e.name)
			})
			this.roles = array
		},
		updateRoles () {
			console.log(this.roles)
			const data = {
				roles: this.roles
			}
			axios.post(`/api/user/${this.user.id}/roles`, data)
				.then((response) => {
				}).catch((errors) => {
					console.log(errors)
				})
		}
	}
}
</script>

<style scoped>

</style>
