<template />

<script>
export default {
	name: 'GeoLocate',
	methods: {
		getLocation: function () {
			const getPosition = () => {
				return new Promise((res, rej) => {
					navigator.geolocation.getCurrentPosition(res, rej)
				})
			}
			getPosition()
				.then((response) => {
					// console.log(response);
					this.location = response.coords

					axios.get(`/api/salesflow/location?rt=${window.user.remeber_token}&lat=${this.location.latitude}&lng=${this.location.longitude}`)
						.then((response) => {
							this.lead.address = response.data.results[0].address_components[0].short_name + ' ' + response.data.results[0].address_components[1].long_name
							this.lead.city = response.data.results[0].address_components[3].long_name
							this.lead.state = response.data.results[0].address_components[5].long_name
							this.lead.zip = response.data.results[0].address_components[7].long_name
						})
						.catch(function (error) {
							console.log(error)
						})
				})
				.catch(error => console.log(error))
		}
	}
}
</script>

<style scoped>

</style>
