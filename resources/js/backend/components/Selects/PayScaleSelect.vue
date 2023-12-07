<template>
  <div v-if="user">
    <label style="padding-top: 2%">Payscale: </label>
    <MazSelect
      v-model="selectedPayscale"
      :options="options"
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
	name: 'PayScaleSelect',
	components: { MazSelect },
	props: {
		user: {
			type: Object,
			required: true,
			default: () => ({ payscale: 'mxn' })
		},
		options: {
			type: Array,
			required: false,
			default () {
				return [
					{ label: 'MXN', value: 'mxn' },
					{ label: 'USD', value: 'usd' }
				]
			}
		}
	},
	data () {
		return {
			placeholder: 'Select Payscale',
			selectedPayscale: null
		}
	},
	mounted () {
		this.selectedPayscale = this.user.payscale
	},
	methods: {

		saveChanges () {
			console.log(this.selectedPayscale)
			axios.post(`/api/user/${this.user.id}/payscale`, { payscale: this.selectedPayscale })
				.then((response) => {
				}).catch(function (error) {
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>

</style>
