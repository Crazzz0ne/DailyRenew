<template>
  <div>
    <table class="sheru-cp-section responsive-table py-3">
      <thead class="responsive-table__head">
        <tr class="responsive-table__row">
          <th class="responsive-table__cell responsive-table__cell--head">
            Day
          </th>
          <th class="responsive-table__cell responsive-table__cell--head">
            Start
          </th>
          <th class="responsive-table__cell responsive-table__cell--head">
            End
          </th>
          <th class="responsive-table__cell responsive-table__cell--head">
            Book
          </th>
        </tr>
      </thead>
      <tbody class="responsive-table__body">
        <tr
          v-for="appointment in appointments"
          class="responsive-table__row"
        >
          <td
            class="responsive-table__cell"
            data-title="Day"
          >
            {{
              $date(appointment.start_time).format('MM/DD/YYYY')
            }}
          </td>
          <td
            v-model="selectedAppointment.start_time"
            class="responsive-table__cell"
            data-title="Start"
          >
            {{
              $date(appointment.start_time).format('h:mm a') }}
          </td>
          <td
            v-model="selectedAppointment.end_time"
            class="responsive-table__cell"
            data-title="End"
          >
            {{
              $date(appointment.end_time).format('h:mm a') }}
          </td>
          <td
            class="responsive-table__cell"
            data-title="Book"
          >
            <button
              class="btn-success"
              @click="submit(appointment.start_time, appointment.end_time, leadId)"
            >
              Book
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import * as dayjs from 'dayjs'

export default {
	name: 'AppointmentList',
	props: [
		'leadId',
		'userId',
		'officeId',
		'bookingType',
		'appointmentId'
	],
	events: ['new-appointment'],
	data () {
		return {
			appointments: [],
			selectedAppointment: {},
			today: dayjs(),
			selection: '',
			urlPram: ''
		}
	},
	computed: {
		currentDate: function () {
			const t = this
			return t.dateContext.get('date')
		}
	},
	mounted () {
		// TODO: Need to know position from state If we know this is sp1 or know that they are just trying to change
		// I think this is solved.
		// The appointment date/time we can run another function
		this.appointmentList(this.bookingType)
	},
	methods: {
		appointmentList: function (bookingType) {
			axios.get(`/api/salesflow/lead/${this.leadId}/appointment?type=${bookingType}&office_id=${this.officeId}&user_id=${this.userId}`)
				.then((response) => {
					// console.log(`/api/salesflow/lead/${this.leadId}/appointment?type=${bookingType}&office_id=${this.officeId}&user_id=${this.userId}`);
					console.log(response)
					this.appointments = response.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		submit: function (start, end, leadId) {
			const data = {
				user_id: this.userId,
				start: start,
				end: end,
				lead_id: leadId,
				type_id: 3
			}
			if (this.bookingType === 'bookSelfSP2' || this.bookingType === 'bookAnySP1') {
				axios.post(`/api/salesflow/lead/${leadId}/appointment`, data)
					.then((response) => {
						console.log(response, '1 ')
						Swal.fire({
							title: 'Appointment Has Been Set',
							text: 'Thank you for helping remove carbon',
							icon: 'success',
							confirmButtonColor: '#3085d6'
						}).then((result) => {
							if (result.value) {
								this.$emit('appointment-booked')
								this.$emit('new-appointment', data)
							}
						})
						// console.log(response)
					})
					.catch(function (error) {
						console.log(error)
					})
			} else {
				console.log('/api/salesflow/leads/${leadId}/appointment/1`')
				axios.put(`/api/salesflow/lead/${leadId}/appointment/${this.appointmentId}`, data)
					.then((response) => {
						console.log(response, ' 2')
						Swal.fire({
							title: 'Appointment Has Been Set',
							text: 'Thank you for helping remove carbon',
							icon: 'success',
							confirmButtonColor: '#3085d6'
						}).then((result) => {
							if (result.value) {
								this.$emit('appointment-booked')
								this.$emit('new-appointment', data)
							}
						})
						// console.log(response)
					})
					.catch(function (error) {
						console.log(error)
					})
			}
		}
	}
}
</script>

<style scoped>

</style>
