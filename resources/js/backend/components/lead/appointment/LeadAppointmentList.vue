<template>
  <div
    v-if="appointments.length"
    class="card mx-1 p-1 h-100"
  >
    <div class="container pt-4 px4">
      <div class="row align-content-center">
        <div
          v-for="appointment in appointments"
          class="col-md-6 col-lg-4 col-sm-12"
        >
          <div class="card">
            <div
              class="card-header"
              @click="goAppointment(appointment.id)"
            >
              {{ appointment.subject }}
            </div>
            <div class="card-body">
              <div>
                {{ $date(appointment.start_time).format('MM/DD/YYYY') }}
              </div>
              <div>
                {{ $date(appointment.start_time).format('h:mm a') }} - {{
                  $date(appointment.end_time).format('h:mm a') }}
              </div>
              <div>
                <p>{{ appointment.comment }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'LeadAppointmentList',
	props: {
		appointments: Array
	},

	data () {
		return {}
	},
	computed: {

	},

	created () {

	},
	mounted () {

	},

	methods: {
		goAppointment (id) {
			this.$router.push({ path: `/dashboard/calendar/${id}` })
		}

	}
}
</script>

<style scoped>
#notes {
    margin-top: 20px;
}
</style>
