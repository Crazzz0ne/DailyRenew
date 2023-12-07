<template>
  <div class="container-fluid">
    <div class="row">
      <div class="justify-content-between">
        <MazBtn
          :loading="loading"
          @click="bookOpenDialog = true"
        >
          {{ bookingTypeName }}
        </MazBtn>
        <MazDialog
          :input="bookOpenDialog"
          :value="bookOpenDialog"
          :no-close="true"
        >
          <div slot="title">
            What time?
          </div>
          <div slot="default">
            <MazPicker
              v-model="appointmentTime"
              :minute-interval="5"
              @validate="bookSp2"
            />
          </div>
          <div
            v-if="bookingType === 6"
            class="pt-3"
          >
            <p>Close Type</p>
            <div>
              <div class="col">
                Remote
                <MazSwitch
                  v-model="remoteClose"
                  @input="toggleWhere('remote')"
                />
              </div>
              <div class="col">
                In Person
                <MazSwitch
                  v-model="inPerson"
                  @input="toggleWhere('in person')"
                >
                  In Person
                </MazSwitch>
              </div>
            </div>
          </div>
          <div slot="footer">
            <MazBtn
              :color="'danger'"
              @click="bookOpenDialog = false"
            >
              Close
            </MazBtn>
          </div>
        </MazDialog>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
	name: 'BookSp2',
	props: {
		userId: Number,
		leadId: Number,

		canbookOwn: Boolean,
		market: String,
		appointment: Object,
		bookingType: Number
	},
	data () {
		return {

			appointmentTime: null,
			bookOpenDialog: false,
			loading: false,
			disableModel: false,
			inPerson: true,
			remoteClose: false
		}
	},
	computed: {

		noDate () {
			if (this.appointmentTime) {
				return false
			} else {
				return true
			}
		},

		bookingTypeName () {
			switch (this.bookingType) {
			case 6:
				return 'Close'
			case 7:
				return 'Follow Up'
			case 4:
				return 'Set Site Survey'
			case 5:
				return 'Set Install Date'
			case 9:
				return 'Outside'
			default :
				return 'Something is wrong'
			}
		}
	},

	methods: {
		toggleWhere (where) {
			if (where === 'in person') {
				this.inPerson = true
				this.remoteClose = false
			} else {
				this.inPerson = false
				this.remoteClose = true
			}
		},

		bookSp2: function () {
			this.disableModel = true
			this.loading = true
			this.bookOpenDialog = false
			let userId = null
			if (this.canbookOwn) {
				userId = this.userId
			}
			const data = {
				user_id: userId,
				start: this.appointmentTime,
				lead_id: this.leadId,
				type_id: this.bookingType,
				remote: this.remoteClose
			}
			let urls
			if (this.bookingType === 8) {
				urls = '/api/salesflow/line/go-back'
			} else {
				urls = `/api/salesflow/lead/${this.leadId}/appointment`
			}

			axios.post(urls, data)
				.then((response) => {
					console.log(response, 'appointment')

					this.loading = false
					this.$emit('appointment', response.data.data)
					Swal.fire({
						title: 'Appointment Has Been Set',
						text: 'Thank you for helping remove carbon!',
						icon: 'success',
						confirmButtonColor: '#3085d6'
					})
					this.disableModel = false
					// console.log(response)
				})
				.catch(function (error) {
					console.log(error)
					this.disableModel = false
					this.loading = false
				})
		}
	}
}
</script>

<style scoped>

</style>
