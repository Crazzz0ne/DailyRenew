<template>
  <MazBtn
    :loading="loading"
    @click="getJwt"
  >
    Set Site Survey
  </MazBtn>
</template>

<script>
import axios from 'axios'

export default {
	name: 'SiteSurveyCompleteModel',
	props: [
		'lead',
		'closedActive'
	],
	data () {
		return {
			model: false,
			jwt: '',
			loading: false,
			production: false
		}
	},
	computed: {
		iframe () {
			return `https://heliotrack.completesolar.com/job/${this.lead.epc_owner_id}/questionnaire-scheduler?jwt=${this.jwt}`
		}
	},
	watch: {
		closedActive: function (newValue, oldValue) {
			this.getJwt()
		}
	},
	methods: {
		getJwt () {
			this.loading = true
			axios.get(`/api/epc/jwt/${this.lead.id}`)
				.then((response) => {
					this.jwt = response.data.token
					this.production = response.data.production
					this.model = true
					this.loading = false
					this.openScheduler()
				}).catch(function (error) {
					// this.loading = false;
					console.log('lead error ', error)
				})
		},
		openScheduler () {
			if (this.production) {
				const url = `https://heliotrack.completesolar.com/job/${this.lead.epc_owner_id}/questionnaire-scheduler?jwt=${this.jwt}`
				window.open(url)
			} else {
				const url = `https://heliotrack.completesolar.biz/job/${this.lead.epc_owner_id}/questionnaire-scheduler?jwt=${this.jwt}`
				window.open(url)
			}
		}
	}
}
</script>

<style scoped>

</style>
