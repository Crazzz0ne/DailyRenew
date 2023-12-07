<template>
  <div>
    <div
      v-if="loaded"
      v-show="currentView ==='appointment-card'"
      class="card mt-5"
    >
      <div class="card-header">
        <div class="container">
          <div class="row justify-content-between">
            <div class="col-md-6 col-sm-11">
              <h2 class="display-5 text-uppercase">
                {{ appointment.type }}
              </h2>
              <p>
                Lead Status
                <br>
                {{ appointment.lead.status }}
              </p>
              <p />
            </div>
            <div class="col-1">
              <div class="float-right">
                <MazBtn
                  :color="'danger'"
                  :icon-name="'delete'"
                  :size="'sm'"
                  fab
                  @click="deleteAppointment()"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <span class="float-right">
          <p
            v-if="appointment"
            class="float-right pr-2"
          >
            Created By: {{ appointment.created_by.fullName }}
          </p>
          <p>{{ $date(appointment.created_at).format('M/D/YY h:mm a') }}</p>
        </span>
      </div>
      <div class="card-body">
        <div class="row justify-content-between">
          <div class="col-4">
            <div class="w-50 " />
          </div>
        </div>
        <div class="row pb-3 text-center">
          <div class="col-12" />
          <div class="col-6">
            <office
              v-if="$can('administrate company')"
              class="pb-3 w-25 mx-auto"
              :office-id="appointment.lead.office_id"
              :lead-id="appointment.lead.id"
              @officeChange="appointment.lead.office_id = $event"
            />
            <h2>Lead {{ appointment.lead.id }} </h2>
            <button
              class="rounded btn btn-primary"
              @click="goToLead"
            >
              <i class="fas fa-eye" />
            </button>
          </div>
          <div class="col-6">
            <div class="w-50 mx-auto">
              <MazSelect
                v-if="canUpdateStatus"
                v-model="statusId"
                :options="statusOptions"
                size="sm"
                @blur="changeStatus(statusId)"
              />
              <h3 v-else>
                {{ appointment.status }}
              </h3>
              <p
                v-if="appointment.completed_at"
                class="py-2"
              >
                Status Change: {{ $date(appointment.completed_at).format('M/D/YY h:mm a') }}
              </p>
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-6  py-1 text-center">
            <template
              v-if="(appointment.type_id === 3 || appointment.type_id === 6 || appointment.type_id === 7 || appointment.type_id === 10)"
            >
              <h4>
                <span v-if="appointment.user">{{ appointment.user.fullName }}</span>
                <span
                  v-if="onLead"
                  @click="changeRepOnAppointmentList"
                >
                  <i class="fas fa-user-edit" /></span>
              </h4>
            </template>
            <h4>Time:</h4>
            <h6>
              {{ $date(appointment.start_time).format('M/D/YY') }}: <br>
              {{ $date(appointment.start_time).format('h:mm ') }} - {{
                $date(appointment.end_time).format('h:mm A')
              }}
            </h6>

            <div class="pt-3">
              <MazBtn
                v-if="onLead"
                @click="changeTimeModal = true"
              >
                Change Date Time
              </MazBtn>

              <MazBtn
                :color="'info'"
                @click="reBookModel = true"
              >
                Reschedule
              </MazBtn>
              <Jeopardy
                v-if="($can('edit JIJ') && appointment.lead.closedAt === null)
                  || $can('edit system') || $can('administrate company')"
                class="px-3"
                :jeopardy_id="appointment.lead.jeopardy_id"
                :user-id="getUser.data.id"
                :lead-id="appointment.lead.id"
              />
            </div>
            <div
              v-if="appointment.comment"
              class=" py-2 border mt-3"
            >
              <p
                class="pt-1 pl-1"
                style="font-size: smaller"
              >
                <strong>Comment:  </strong>{{ appointment.comment }}
              </p>
            </div>
            <div class="row">
              <div class="col">
                <div class="pb-4" />
              </div>
              <div class="col-3 pt-2" />
            </div>
          </div>
          <div class="col-md-6 mt-sm-3  py-1 text-center">
            <div>
              <div>
                <div class="row justify-content-center">
                  <h5 class="pr-2">
                    Remote
                  </h5>
                  <MazSwitch
                    v-model="appointment.remote"
                    @input="updateRemote()"
                  />
                </div>
              </div>
              <h4>Customer:</h4>
              <h6>
                {{ appointment.lead.customer.first_name }} {{
                  appointment.lead.customer.last_name
                }}
              </h6>
              <h4>Address:</h4>
              <h6>
                {{ appointment.lead.customer.street_address }}, <br> {{
                  appointment.lead.customer.city
                }} {{
                  appointment.lead.customer.zip_code
                }}
              </h6>
              <MazBtn
                :right-icon-name="'directions_car'"
                @click="openMaps"
              >
                Directions
              </MazBtn>
            </div>
          </div>
        </div>
        <hr>
        <div class="container">
          <div class="row">
            <div class="col-12">
              <Note
                v-if="notes"
                :is-new-lead="false"
                :lead-id="appointment.lead.id"
                :user="getUser.data"
                :notes="notes"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
    <MazDialog
      v-model="changeRepModal"
      @confirm="changeRepOnAppointment"
    >
      <div slot="title">
        Who Will be on this appointment?
      </div>
      <MazSelect
        v-model="newRep"
        :options="reps"
      />
    </MazDialog>
    <MazDialog
      v-model="changeTimeModal"
      @confirm="changeTimeOnAppointment"
    >
      <div slot="title">
        What time are we changing this to?
      </div>
      <MazPicker
        v-model="newTime"
        @formatted="pickerFormatted = $event"
        @validated="changeTimeOnAppointment"
      />
      <!--            <MazBtn @click="changeTimeOnAppointment">-->
      <!--                Submit-->
      <!--            </MazBtn>-->
    </MazDialog>
    <BookRRList
      v-if="appointment && reBookModel"
      :appointment-id="appointment.id"
      :model="reBookModel"
      :lead-id="appointment.lead_id"
      :re-book="true"
      @model="reBookModel = false"
      @appointment="updateAppointment($event)"
      @closeModel="reBookModel = false"
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import Note from '../../components/lead/note/Note'
import axios from 'axios'
import Office from '../../components/lead/Office/Office'
import Jeopardy from '../../components/lead/jnj/Jeopardy'
import BookRRList from '../../components/Calender/BookRRList'

export default {
	name: 'CalendarAppointmentView',
	components: { BookRRList, Jeopardy, Office, Note },
	data () {
		return {
			reBookModel: false,
			lead: {},
			loaded: false,
			mapUrl: '',
			changeRepAppointmentSlot: false,
			currentView: 'appointment-card',
			appointment: {},
			notes: [],
			fillerId: null,
			permissions: window.Permissions,
			changeRepModal: false,
			reps: [
				{ label: 'Error', value: null }
			],
			newRep: null,
			changeTimeModal: false,
			newTime: null,
			statusOptions: [
				{ label: 'Pending', value: 1 },
				{ label: 'Completed', value: 3 },
				{ label: 'Reopened', value: 2 }
			],
			statusId: 1
		}
	},

	watch: {
		appointment: function (newVal, oldVal) {
			// this.fetchUser();
		}
	},
	beforeMount () {

	},

	mounted () {
		this.getAppointment(this.$route.params.calendarId)
		this.fetchNotes()
	},
	created () {
		this.fetchUsers()
	},
	computed: {
		...mapGetters([
			'getUser'
		]),
		integrationsStatus () {
			if (this.appointment.user) {
				this.fillerId = this.appointment.user
			} else {
				this.fillerId = null
			}
		},
		onLead () {
			if (this.getUser.id === this.appointment.user.id || this.$can('administrate company') || this.$can('edit user on lead')) {
				return true
			}
		},
		canUpdateStatus () {
			if (this.getUser.id === this.appointment.created_by.id || this.$can('administrate company')) {
				return true
			}
			return false
		}

	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
        updateAppointment (appointment) {
            this.appointment = appointment
        },
		openListener () {
			Echo.private('lead.' + this.appointment.lead_id)
				.listen('Backend.SalesFlow.LeadNewMessage', (e) => {
					const note = {
						id: e.id,
						note: e.message,
						user: {
							name: {
								first: e.firstName,
								last: e.lastName
							}
						}
					}
					this.notes.push(note)
				}).listen('Backend.SalesFlow.Lead.LeadUpdateEvent', (e) => {
					this.leadUpdateEvent(e.data)
				})
		},

		leadUpdateEvent (data) {
			console.log(data, 'lead update event')
			this.appointment.lead = _.merge(this.appointment.lead, _.pick(data, _.keys(this.appointment.lead)))
			console.log(this.appointment.lead, 'more updates')
		},

		updateRemote () {
			const data = {
				remote: this.appointment.remote
			}
			axios.put(`/api/salesflow/lead/${this.appointment.lead_id}/appointment/${this.appointment.id}/update-remote`, data)
				.then((response) => {
					console.log(response)
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		goToLead () {
			this.$router.push({ path: `/dashboard/lead/${this.appointment.lead_id}` })
		},

		changeStatus (decision) {
			console.log(decision)
			const data = {
				statusId: decision
			}
			axios.put(`/api/salesflow/lead/${this.appointment.lead_id}/appointment/${this.appointment.id}/update-status`, data)
				.then((response) => {
					this.appointment = response.data.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		changeTimeOnAppointment () {
			const data = {
				startTime: this.newTime,
				changeTime: true
			}
			// const formData = new FormData();
			// formData.append('appointment_id', appointment_id);
			// formData.append('user_id', '1');
			// formData.append('test', 'test');
			axios.put(`/api/salesflow/lead/${this.appointment.lead_id}/appointment/${this.appointment.id}`, data)
				.then((response) => {
					console.log(response)
					this.appointment = response.data.data
					console.log(this.appointment)
					this.changeTimeModal = false
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		changeRepOnAppointment () {
			const data = {
				changeUser: true,
				newUser: this.newRep,
				start: false
			}
			// const formData = new FormData();
			// formData.append('appointment_id', appointment_id);
			// formData.append('user_id', '1');
			// formData.append('test', 'test');

			axios.put(`/api/salesflow/lead/${this.appointment.lead_id}/appointment/${this.appointment.id}`, data)
				.then((response) => {
					console.log(response)
					this.appointment = response.data.data
					console.log(this.appointment)
					this.changeRepModal = false
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		deleteAppointment () {
			Swal.fire({
				title: 'Delete?',
				text: 'Are you sure?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/salesflow/lead/${this.appointment.lead_id}/appointment/${this.appointment.id}`)
						.then((response) => {
							if (response) {
								Swal.fire(
									'Deleted!',
									'The appointment has been deleted.',
									'success'
								)
							}
							this.$router.push({ path: '/dashboard/calendar' })
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		},

		changeRepOnAppointmentList () {
			this.changeRepModal = true
			axios.get(`/api/salesflow/lead/${this.appointment.lead_id}/appointment/${this.appointment.id}/change-rep`)
				.then((response) => {
					this.reps = response.data
				}).then(() => {
					this.loaded = true
				}).catch(function (error) {
					console.log(error)
				})
		},

		newAppointment (value) {
			console.log(value)
			this.appointment.start_time = value.start
			this.appointment.end_time = value.end
		},
		getAppointment ($id) {
			axios.get(`/api/salesflow/calender/${$id}`)
				.then((response) => {
					console.log(response, ' yeah ')
					this.appointment = response.data.data
					this.statusId = this.appointment.status_id
				}).then(() => {
					this.newTime = this.appointment.start_time
					this.loaded = true
					this.openListener()
				}).catch(function (error) {
					console.log(error)
				})
		},
		fetchNotes () {
			axios.get(`/api/salesflow/lead/${this.appointment.lead_id}/note/1`)
				.then((response) => {
					this.notes = response.data.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		openMaps () {
			this.mapUrl = `https://www.google.com/maps/dir/?api=1&destination=${this.appointment.lead.customer.street_address.split(' ').join('+')},+${this.appointment.lead.customer.city.split(' ').join('+')}`
			window.open(this.mapUrl, '_blank')
		},

		changeRep (appointment) {
			const data = {
				appointment_id: appointment,
				user: 'test2'
			}
			// const formData = new FormData();
			// formData.append('appointment_id', appointment_id);
			// formData.append('user_id', '1');
			// formData.append('test', 'test');
			axios.put(`/api/salesflow/calendar/${this.appointment.id}`, data)
				.then((response) => {
					// this.appointment = response.data.data;
					console.log('changeRep', response)
				})
				.catch(function (error) {
					console.log(error)
				})
			this.changeRepAppointmentSlot = true
		}
	}
}
</script>

<style scoped>

</style>
