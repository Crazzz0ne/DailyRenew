<template>
  <div>
    <div v-if="appointmentDate">
      <MazPicker
        v-model="appointmentDate.start_time"
        :disabled-hours="disabledHours"
        :minute-interval="15"
        :disabled="!disabled"
        :no-now="true"
        unique-id="1"
        @validate="updateAppointment(appointmentType, appointmentDate.start_time, userId, appointmentDate.id)"
      />
    </div>
    <div v-else>
      <template v-if="canCreate">
        <MazPicker
          v-model="appointmentTime"
          :no-now="true"
          :disabled-hours="disabledHours"
          :minute-interval="15"
          :disabled="!disabled"
          unique-id="2"
          @validate="createAppointment(appointmentType, appointmentTime, userId)"
        />
      </template>
    </div>
  </div>
</template>

<script>
export default {
	name: 'Appointment',
	props: {
		appointmentDate: Object,
		type: String,
		leadId: Number,
		userId: Number,
		disabled: Boolean
	},
	data () {
		return {
			value: false,
			appointmentTime: null,
			disabledHours: ['00', '01', '02', '03', '04', '05', '06', '07',
				'22', '23']

		}
	},
	computed: {
		appointmentType () {
			if (this.type === 'install') {
				return 5
			} else if (this.type === 'site survey') {
				return 4
			}
		},
		canCreate () {
			console.log(this.getUser)
			if (this.appointmentType && this.userId) {
				return true
			} else {
				return false
			}
		}
	},
	watch: {},
	methods: {
		createAppointment (appointmentType, appointmentTime, userId) {
			const data = {
				user_id: userId,
				start: appointmentTime,
				type_id: appointmentType
			}
			if (appointmentType && appointmentTime && userId && this.disabled) {
				axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/appointment`, data)
					.then((response) => {
						console.log(response, 'appointment')
						Swal.fire({
							title: 'Appointment Has Been Set',
							text: 'Thank you for helping remove carbon',
							icon: 'success',
							confirmButtonColor: '#3085d6'
						})
					}).catch((e) => {
						console.log(e)
					})
			} else {
				console.log('missing something')
			}
		},
		updateAppointment (appointmentType, appointmentTime, userId, appointmentId) {
			const data = {
				user_id: userId,
				start: appointmentTime,
				type_id: appointmentType
			}
			if (this.disabled) {
				axios.put(`/api/salesflow/lead/${this.$route.params.leadId}/appointment/${appointmentId}`, data)
					.then((response) => {
						console.log(response)
						Swal.fire({
							title: 'Appointment Has Been Updated',
							text: 'Thank you for helping remove carbon',
							icon: 'success',
							confirmButtonColor: '#3085d6'
						})
					}).catch((e) => {
						console.log(e)
					})
			}
		}
	}
}
</script>

<style scoped>

</style>
