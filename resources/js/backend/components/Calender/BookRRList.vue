<template>
  <MazDialog
    :input="model"
    :value="model"
    :fullsize="true"
    :no-close="true"
  >
    <div
      slot="title"
      class="text-white"
    >
      <h4>What time?</h4>
    </div>
    <div slot="default">
      <div class="d-flex justify-content-center">
        <div>
          <MazBtn
            v-if="daysOut > 1"
            :loading="loading"
            :icon-name="'remove_circle_outline'"
            fab
            @click="changeDay(-1)"
          />
        </div>
        <div class="text-center px-3">
          <p>Days Out</p>
          <h2> {{ daysOut }}</h2>
          <template v-if="appointments.length > 0">
            <h4> {{ $date(appointments[0].start_time).format("MM-DD") }}</h4>
            <h5> {{ $date(appointments[0].start_time).format("dddd") }}</h5>
          </template>
        </div>
        <div>
          <MazBtn
            :icon-name="'add_circle_outline'"
            :loading="loading"
            fab
            @click="changeDay(1)"
          />
        </div>
      </div>
      <div class="pt-3">
        <p>Close Type</p>
        <div
          v-if="!reBook"
          class="row"
        >
          <div class="col">
            Remote
            <MazSwitch

              v-model="remoteClose"
              @input="changeRemote('remote')"
            />
          </div>
          <div class="col">
            In Person
            <MazSwitch
              v-model="inPerson"
              @input="changeRemote('in person')"
            >
              In Person
            </MazSwitch>
          </div>
        </div>
      </div>
      <div
        v-if="!loading && appointments.length"
        class="row pt-4"
      >
        <div
          v-for="available in appointmentCards"
          class="col-md-4 col-sm-6"
        >
          <div class="card">
            <div class="card-header">
              <h5> {{ available.startTime }}</h5>
                {{ timeZones }}
            </div>
            <div class="card-body">
              <div class="pb-2">
                <p>Available slots {{ available.count }}</p>
                <p
                  v-if="available.preferred"
                  style="color:green"
                >
                  Preferred
                </p>
              </div>
              <MazBtn
                :loading="btnLoading"
                @click="setAppointment(available)"
              >
                Book Appointment
              </MazBtn>
            </div>
          </div>
        </div>
      </div>
      <div v-else-if="!appointments.length && !loading">
        <h5>Sorry no one is available today, try another day or remote.</h5>
      </div>
      <div v-else>
        <MazLoader />
      </div>
    </div>

    <div slot="footer">
      <MazBtn
        :color="'danger'"
        @click="toggleModel"
      >
        Close
      </MazBtn>
    </div>
  </MazDialog>
</template>

<script>
import axios from 'axios'

export default {
	name: 'BookRRList',
	props: {
		model: { type: Boolean, required: true, default: false },
		leadId: { type: Number, required: true, default: null },
		reBook: { type: Boolean, required: false, default: false },
		appointmentId: { type: Number, required: false, default: null }
	},
	data () {
		return {
			daysOut: 0,
			appointments: [],
			remote: false,
			loading: false,
			inPerson: false,
			remoteClose: false,
			btnLoading: false,
		}
	},
	computed: {
        timeZones () {
            const d = new Date()
            return /\(([^)]+)\)/.exec(d.toTimeString())[1]
        },
		appointmentCards () {
			return this.appointments.map((b) => {
				b.startTime = this.$date(b.start_time).format('MM/DD h:mm a')

				return b
			})
		},
		url () {
			if (!this.reBook) {
				return `/api/salesflow/lead/${this.leadId}/appointment/available?remote=${this.remote}&day=${this.daysOut}`
			} else {
				return `/api/salesflow/lead/${this.leadId}/appointment/${this.appointmentId}/re-book?remote=${this.remote}&day=${this.daysOut}`
			}
		}
	},
	watch: {
		model ($old, $new) {
			if ($new) {
				this.getAppointments()
			}
		}
	},
	mounted () {
		this.setDaysOut()
		this.getAppointments()
	},
	methods: {
		toggleModel () {
			this.$emit('closeModel', false)
		},
		setAppointment (available) {
			if (this.reBook) {
				this.reBookAppointment(available)
			} else {
				this.bookAppointment(available)
			}
		},
		changeDay (change) {
			this.daysOut = this.daysOut + change
			this.getAppointments()
		},
		changeRemote (remote) {
			this.getAppointments()
		},
		getAppointments () {
			this.loading = true
			axios.get(this.url)
				.then((response) => {
					this.appointments = response.data
					this.loading = false
				})
		},
		toggleWhere (where) {
			if (where === 'in person') {
				this.inPerson = true
				this.remoteClose = false
			} else {
				this.inPerson = false
				this.remoteClose = true
				this.daysOut = 0
			}
			this.loading = true
			this.getAppointments()
			this.activeStep = 0
		},
		setDaysOut () {
			if (this.remote || this.reBook) {
				this.inPerson = false
				this.remoteClose = true
			} else {
				this.inPerson = true
				this.remoteClose = false
			}
			this.daysOut = 0
		},
		bookAppointment (time) {
			const data = {
				remote: this.remoteClose,
				start_time: time.start_time,
				userId: time.userId,
				userList: time.userList,
				backUpPreferred: time.backUpPreferred

			}
			this.btnLoading = true
			axios.post(`/api/salesflow/lead/${this.leadId}/appointment/available`, data)
				.then((response) => {

					this.bookOpenDialog = false
					this.loading = false
					this.btnLoading = false

					if (response.data.taken === true) {
						Swal.fire({
							type: 'warning',
							title: 'Time is Already taken',
							text: 'Sorry that time is no longer available, please try another time.',
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Good Luck'

						})
					} else {
                        console.log(response.data.data)
						this.$emit('appointment', response.data.data)
					}
				})
		},
		reBookAppointment (time) {
			const data = {
				start_time: time.start_time
			}
			this.btnLoading = true
			axios.put(`/api/salesflow/lead/${this.leadId}/appointment/${this.appointmentId}/re-book`, data)
				.then((response) => {
                    this.$emit('appointment', response.data.data)
                    console.log(response.data.data, 'response.data.data')

					this.$emit('closeModel')
					Swal.fire({
						type: 'success',
						title: 'We have rescheduled the customer',
						text: 'Thank you!',
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Close'

					}).then((result) => {
						this.bookOpenDialog = false

						this.loading = false
						this.btnLoading = false
					})
				})
		}
	}
}
</script>

<style scoped>

</style>
