<template>
  <div v-if="loading">
    <h4>
      {{ $date(appointment.start_time).format('MMMM D, YYYY h:mm ') }} - {{
        $date(appointment.end_time).format('h:mm A')
      }}
    </h4>
    <p>{{ appointment.lead.customer.city }}, {{ appointment.lead.customer.zip_code }}</p>
    <p class="text-capitalize">
      Language: {{ appointment.lead.customer.language }}
    </p>
    <div class="row py-3">
      <div class="col-3">
        <p>Remote</p>
        <MazCheckbox
          v-model="appointment.remote"
          :disabled="true"
        />
      </div>
    </div>

    <div class="row">
      <div
        v-for="rep in repsWithComp "
        class="col-4"
      >
        <div class="card">
          <div class="card-header">
            <span> {{ rep.name.first }} {{ rep.name.last }}</span>
            <br>
          </div>
          <div class="card-body">
            <p>{{ rep.phoneNumber }}</p>

            <button
              class="btn-success"
              @click="submit(rep.id)"
            >
              Assign
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as dayjs from 'dayjs'

export default {
	name: 'LeadAssignAppointment',
	data () {
		return {
			reps: {},
			appointment: {},
			today: dayjs(),
			dateContext: dayjs(),
			selection: '',
			loading: false
		}
	},
	computed: {
		repsWithComp () {
			return this.reps.map((b) => {
				const x = b.phone.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/)
				b.phoneNumber = b.phone = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '')

				return b
			})
		}

	},
	created () {

	},

	mounted () {
		axios.get(`/api/salesflow/lead/${this.$route.params.leadId}/appointment?type=assignsp1`)
			.then((response) => {
				this.reps = response.data.data
				console.log('get ', this.reps)
				// console.log('re', this.reps);
				if (response.data === 'taken') {
					const swalWithBootstrapButtons = Swal.mixin({
						customClass: {
							confirmButton: 'btn btn-success',
							cancelButton: 'btn btn-danger'
						},
						buttonsStyling: false
					})
					swalWithBootstrapButtons.fire({
						title: 'We can\'t find the appointment you are looking for',
						text: 'Maybe its already approved?',
						type: 'error',
						showCancelButton: true,
						confirmButtonText: 'Back to the Leads?',
						cancelButtonText: 'To the Calender',
						reverseButtons: true
					}).then((result) => {
						if (result.value) {
							return this.$router.push({ path: '/dashboard/lead/' })
						}
						if (!result.value) {
							return this.$router.push({ path: '/dashboard/calender/' })
						}
					})
				} else {
					this.loading = true
				}

				// console.log(moment().format());
			})
			.catch(function (error) {
				console.log(error)
			})

		axios.get(`/api/salesflow/lead/${this.$route.params.leadId}/appointment/${this.$route.params.appointmentId}?noUser=1`)
			.then((response) => {
				this.appointment = response.data.data

				console.log(response, 'appointment')
			})
			.catch(function (error) {
				console.log(error)
			})
	},
	methods: {
		submit (id) {
			console.log(this.$route.params, 'route')
			const data = {
				newUser: id,
				changeUser: true
			}
			axios.patch(`/api/salesflow/lead/${this.$route.params.leadId}/appointment/${this.$route.params.appointmentId}`, data)
				.then((response) => {
					console.log(response, 'appointment')
					this.$router.push('/dashboard/lead')
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		acceptNumber (phone) {
			const x = this.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/)
			return this.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '')
		}

	}
}

</script>

<style scoped>

</style>
