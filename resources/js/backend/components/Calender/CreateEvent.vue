<template>
  <div>
    <MazBtn
      class="maz-mr-2 maz-mb-2"
      @click.stop="toggle = true"
    >
      Schedule New Event
    </MazBtn>
    <MazDialog
      v-model="toggle"
      :max-width="800"
      :no-confirm="hideConfirm"
      title="Schedule Event"
      @confirm="createEvent"
    >
      <div class="container">
        <div class="row py-3">
          <div class="col">
            <MazSelect
              v-model="selectedEvent"
              placeholder="Select Event"
              :options="eventOptions"
            />
          </div>
          <div class="col">
            <MazSelect
              v-if="userList.length > 1"
              v-model="selectedUser"
              :search="true"
              :search-placeholder="'who?'"
              placeholder="Select Who"
              :options="userList"
            />
            <p v-else-if="userList.length === 1">
              {{ userList[0].label }}
            </p>
          </div>
        </div>
        <div v-if="showRest">
          <div class="row py-3">
            <div class="col">
              <MazPicker

                v-model="selectedTime"
                :minute-interval="30"
                :min-date="timestamp"
              />
            </div>
            <div
              v-if="selectedEvent === 'close'"
              class="col"
            >
              <div>
                <p>Remote</p>
                <MazCheckbox
                  v-model="remote"
                />
              </div>
            </div>
          </div>
          <div>
            <MazInput
              v-model="commentValue"
              placeholder="Comment"
              autocomplete="new-comment"
              left-icon-name="comment"
              textarea
            />
          </div>
        </div>
      </div>
    </MazDialog>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'CreateEvent',
	props: ['leadId'],
	data () {
		return {
			userList: [],
			remote: false,
			loading: false,

			commentValue: '',
			selectedUser: null,
			selectedEvent: null,
			toggle: false,
			selectedTime: null,
			timestamp: ''
		}
	},
	computed: {
		showRest () {
			return this.userList.length >= 1
		},
		eventOptions () {
			if (this.$can('edit requested system')) {
				return [
					{ label: 'Select Event', value: null },
					{ label: 'Close', value: 'close' },
					{ label: 'Follow up', value: 'follow-up' },
					{ label: 'Task', value: 'task' }
				]
			}
			return [
				{ label: 'Select Event', value: null },
				{ label: 'Follow up', value: 'follow-up' }
			]
		},
		hideConfirm () {
			if (!this.loading && this.selectedUser && this.selectedEvent && this.selectedTime) {
				return false
			}
			return true
		}

	},
	watch: {
		selectedEvent (oldValue, newValue) {
			this.getAvalibleUsers()
		}
	},
	mounted () {
		this.getNow()
		// this.getAvalibleUsers()
	},
	methods: {
		getNow: function () {
			const today = new Date()
			const date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate()
			const time = today.getHours() + ':' + today.getMinutes() + ':' + today.getSeconds()
			const dateTime = date + ' ' + time
			this.timestamp = dateTime
		},
		getAvalibleUsers () {
			this.userList = []
			axios.get(`/api/salesflow/user/event-list?event=${this.selectedEvent}&leadId=${this.leadId}`)
				.then((response) => {
					this.userList = response.data
					console.log(this.userList.length, 'userList')
					if (this.userList.length) {
						this.selectedUser = this.userList[0].value
					}
					console.log(response)

					// if (response.data.taken === true) {
					// 	Swal.fire({
					// 		type: 'warning',
					// 		title: 'Time is Already taken',
					// 		text: 'Sorry that time is no longer available, please try another time.',
					// 		confirmButtonColor: '#3085d6',
					// 		cancelButtonColor: '#d33',
					// 		confirmButtonText: 'Good Luck'
					//
					// 	})
					// } else {
					// 	this.$emit('appointment', response.data.data)
					// }
				})
		},
		createEvent () {
			this.loading = true
			const data = {
				userId: this.selectedUser,
				time: this.selectedTime,
				type: this.selectedEvent,
				comment: this.commentValue,
				remote: this.remote
			}
			axios.post(`/api/salesflow/lead/${this.leadId}/appointment/book-event`, data)
				.then((response) => {
					this.userList = []
					this.toggle = false
					this.loading = false
					this.$emit('appointment', response.data.data)

					// if (response.data.taken === true) {
					// 	Swal.fire({
					// 		type: 'warning',
					// 		title: 'Time is Already taken',
					// 		text: 'Sorry that time is no longer available, please try another time.',
					// 		confirmButtonColor: '#3085d6',
					// 		cancelButtonColor: '#d33',
					// 		confirmButtonText: 'Good Luck'
					//
					// 	})
					// } else {
					// 	this.$emit('appointment', response.data.data)
					// }
				})
		}
	}

}
</script>

<style scoped>

</style>
