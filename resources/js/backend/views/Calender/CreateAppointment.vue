<template />

<script>
export default {
	name: 'CreateAppointment',

	data () {
		return {
			appointment: {},
			lead: {},
			loaded: false,
			mapUrl: ''
		}
	},
	methods: {
		getAppointment: function ($id) {
			axios.get(`/api/salesflow/calender/${$id}`)
				.then((response) => {
					this.appointment = response.data.data
					console.log(this.appointment.lead_id)
					this.getLead(this.appointment.lead_id)
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		getLead: function ($id) {
			axios.get(`/api/salesflow/lead/${$id}`)
				.then((response) => {
					this.lead = response.data.data
					console.log(this.customer)
					this.mapUrl = `https://www.google.com/maps/dir/?api=1&destination=${this.lead.customer.street_address.split(' ').join('+')},+${this.lead.customer.city.split(' ').join('+')}`
					this.loaded = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		openMaps: function () {
			window.open(this.mapUrl, '_blank')
		}
	}
}
</script>

<style scoped>

</style>
