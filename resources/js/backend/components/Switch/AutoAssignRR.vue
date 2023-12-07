<template>
  <div v-if="user">
    <label style="padding-top: 2%">Auto Assign Round Robin:</label>
    <MazSwitch
      v-model="value"

      @input="saveChanges"
    >
      <i
        slot="icon-left"
        class="material-icons"
      >
        add_circle_outline
      </i>
    </MazSwitch>
  </div>
</template>

<script>

import axios from 'axios'

export default {
	name: 'AutoAssignRR',
	props: [
		'user'
	],

	data () {
		return {
			value: null

		}
	},
	computed: {

	},

	mounted () {
		this.value = this.user.auto_assign_rr
	},
	methods: {

		saveChanges () {
			axios.post(`/api/user/${this.user.id}/update-auto-rr`,
				{
					auto_assign_rr: this.value
				}).then((response) => {
				console.log(response)
			}).catch(function (error) {
				console.log(error)
			})
		}
	}
}
</script>

<style scoped>

</style>
