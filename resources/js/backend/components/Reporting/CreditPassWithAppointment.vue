<template>
  <div class="row">
    <div class="col text-center">
      <p> Appointments With Credit Pass:</p>
      <p>{{ count.creditPassesWithAppointment }}</p>
    </div>
    <div class="col text-center">
      <p> Closes Scheduled:</p>
      <p>{{ count.closesScheduled }}</p>
    </div>

    <div class="col text-center">
      <p> Closed:</p>
      <p>{{ count.closed }}</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'CreditPassWithAppointment',
	props: [
		'selectedOffice',
		'pickerRangeValues',
		'dateRangeFormatted',
		'selectedUserName',
		'selectedOfficeName',
		'selectedRegion',
		'selectedUser'
	],
	data () {
		return {
			loading: false,
			count: {}

		}
	},
	watch: {
		selectedOffice: function (newVal, oldVal) {
			this.getCount()
		},
		pickerRangeValues: function (newVal, oldVal) {
			this.getCount()
		},
		selectedUser: function (newVal, oldVal) {
			this.getCount()
		},
		selectedRegion: function (newVal, oldVal) {
			this.getCount()
		}
	},
	created () {
		this.getCount()
	},
	methods: {
		getCount () {
			this.loading = true
			let urlss = null

			urlss = '/api/salesflow/reporting/credit-pass-appointments'

			axios.get(urlss,
				{
					params: {
						office_id: this.selectedOffice,
						user_id: this.selectedUser,
						region_id: this.selectedRegion,
						pickerRangeValues: this.pickerRangeValues

					}
				})
				.then((response) => {
					this.count = response.data

					this.loading = false
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
