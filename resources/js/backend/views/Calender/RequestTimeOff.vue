<template>
  <div>
    <div class="card">
      <div class="card-header">
        <h3>Manage Availability</h3>
      </div>
      <div class="card-body">
        <div class="container">
          <div class="row my-3">
            <div class="col-md-8 col-sm-12">
              <div class="row my-3">
                <div class="col">
                  <h4>Up Coming Time On</h4>
                </div>
                <div class="col">
                  <mazSelect
                    v-model="selectedType"
                    :options="type"
                    placeholder="Type"
                  />
                </div>
              </div>
              <MazList
                class="scrollable-list "
                :style="'max-height:500px;'"
              >
                <MazListItem
                  v-for="(timeOff, index) in currentRequestsComp"
                  :key="index"
                >
                  <div class="py-1 d-flex justify-content-between">
                    <p>{{ $date(timeOff.start).format('ddd, MMMM D') }}</p>
                    <p>{{ $date(timeOff.start).format('h:mm a') }}</p>
                    <p>to</p>
                    <p>{{ $date(timeOff.end).format('h:mm a') }}</p>
                    <p>{{ selectedType }}</p>
                    <MazBtn
                      class="float-right "
                      color="danger"
                      icon-name="delete"
                      size="sm"
                      fab
                      @click="deleteTimeOff(timeOff.id)"
                    />
                  </div>
                </MazListItem>
              </MazList>
            </div>
          </div>
          <h4>Set New Available Date/Time</h4>
          <div class="row">
            <div class="col-md-6 col-sm-12">
              <MazPicker
                v-model="selectedDate"
                placeholder="Date"
                inline
                no-time
                no-now
                format="DD-MM-YYYY"
                formatted="LL"
                @formatted="currentRequest = $event"
              />
            </div>
            <div class="col">
              <h5>Start Time</h5>
              <div class="row justify-content-center card">
                <div class="col">
                  <mazSelect
                    v-model="startHour"
                    :options="hoursList"
                    placeholder="Hour"
                  />
                </div>
                <div class="col">
                  <mazSelect
                    v-model="startMinute"
                    :options="minutesList"
                    placeholder="Minute"
                  />
                </div>
                <div class="col">
                  <mazSelect
                    v-model="startMeridiem"
                    :options="meridiems"
                    placeholder="Meridiem"
                  />
                </div>
              </div>
            </div>

            <div class="col">
              <h5>End Time</h5>
              <div class="row justify-content-center card">
                <div class="col">
                  <mazSelect
                    v-model="endHour"
                    :options="hoursList"
                    placeholder="Hour"
                  />
                </div>
                <div class="col">
                  <mazSelect
                    v-model="endMinute"
                    :options="minutesList"
                    placeholder="Minute"
                  />
                </div>
                <div class="col">
                  <mazSelect
                    v-model="endMeridiem"
                    :options="meridiems"
                    placeholder="Meridiem"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 pt-3">
          <div class="float-right">
            <MazBtn
              :disabled="!canSubmit"
              :loading="submitting"
              @click="submit"
            >
              Submit
            </MazBtn>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import axios from 'axios'
import moment from 'moment'

export default {
	name: 'RequestTimeOff',
	data () {
		return {
			currentRequest: moment().format('DD-MM-YYYY'),
			selectedDate: moment().format('DD-MM-YYYY'),
			timeOffRequest: {},
			currentRequests: [],
			submitting: false,
			disabledHours: [1, 2, 3, 4, 5, 6, 7],
			startHour: '08',
			startMinute: '00',
			endHour: '11',
			endMinute: '00',
			startMeridiem: 'AM',
			endMeridiem: 'PM',
			meridiems: [
				{ label: 'AM', value: 'AM' },
				{ label: 'PM', value: 'PM' }
			],
			hoursList: [
				{ label: '1', value: '01' },
				{ label: '2', value: '02' },
				{ label: '3', value: '03' },
				{ label: '4', value: '04' },
				{ label: '5', value: '05' },
				{ label: '6', value: '06' },
				{ label: '7', value: '07' },
				{ label: '8', value: '08' },
				{ label: '9', value: '09' },
				{ label: '10', value: '10' },
				{ label: '11', value: '11' },
				{ label: '12', value: '12' }
			],
			minutesList: [
				{ label: '00', value: '00' },
				{ label: '30', value: '30' }
			],
			type: [
				{ label: 'In person 🧍', value: 'in-person' },
				{ label: 'Virtual 🥽', value: 'virtual' }
			],
			selectedType: 'in-person'
		}
	},
	computed: {
		...mapGetters(['getUser']),
		canSubmit () {
			if (this.start && this.end) {
				return true
			} else {
				return false
			}
		},
		start () {
			return `${this.selectedDate} ${this.startHour}:${this.startMinute} ${this.startMeridiem}`
		},
		end () {
			return `${this.selectedDate} ${this.endHour}:${this.endMinute} ${this.endMeridiem}`
		},
		currentRequestsComp () {
			return this.currentRequests.filter(val => val.type === this.selectedType)
		},
		todaysDate () {
			return moment().format('DD-MM-YYYY')
		}
	},

	created () {
		this.fetchUsers().then(() =>
			this.getCurrentRequests())
	},
	methods: {
		// TODO we need to bring in the user state to know who this is.
		...mapActions(['fetchUsers']),

		getCurrentRequests () {
			axios.get(`/api/salesflow/user/${this.getUser.data.id}/availability`)
				.then((response) => {
					console.log(response)
					this.currentRequests = response.data.data
				})
		},

		deleteTimeOff (id) {
			Swal.fire({
				title: 'Delete?',
				text: 'Are you sure?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, remove it!'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/salesflow/user/${this.getUser.data.id}/availability/${id}`)
						.then((response) => {
							this.getCurrentRequests()

							Swal.fire(
								'Deleted!',
								'The Availability has been Removed.',
								'success'
							)
						}).catch((e) => {

						})
				}
			})
		},

		submit: function () {
			this.sendRequest()
		},
		sendRequest: function () {
			this.submitting = true
			const formData = new FormData()

			formData.append('endTime', this.end)
			formData.append('startTime', this.start)
			formData.append('type', this.selectedType)

			const comp = this

			axios.post(`/api/salesflow/user/${this.getUser.data.id}/availability`, formData)
				.then((response) => {
					this.currentRequests.push(response.data.data)
					this.submitting = false
				})
				.catch(function (error) {
					console.log(error)
					comp.submitting = false
				})
		}
	}
}
</script>

<style scoped>

</style>
