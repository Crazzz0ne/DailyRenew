<template>
  <div>
    <label>Remote Availability</label>
    <MazSwitch
      v-model="value"
      :value="user.remote_option"
      @input="changeRemote"
    />
  </div>
</template>

<script>
import { MazSwitch } from 'maz-ui'
import axios from 'axios'

export default {
	name: 'RemoteSelect',
	components: { MazSwitch },
	props: {
		user: {
			type: Object,
			required: true,
			default: () => ({ remote_option: false })
		}
	},
	data () {
		return {
			value: this.user.remote_option
		}
	},
	methods: {
		changeRemote () {
			axios.post(`/api/user/${this.user.id}/update-remote`,
				{
					remote_option: this.value
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
