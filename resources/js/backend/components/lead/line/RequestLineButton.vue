<template>
  <div>
    <div
      v-if="!queue"
      class="text-center"
    >
      <MazBtn
        :loading="queueButtonLoading"
        data-toggle="tooltip"
        @click="requestIntegrations"
      >
        <span class="pr-1">{{ BtnText }}</span>
        <i class="fa fa-hand-paper" />
      </MazBtn>
    </div>
    <div v-else-if="!queue.filledRep">
      <MazLoader
        :size="20"
      />
      <p> {{ typeString }}</p>
    </div>
    <div v-else>
      <div class="row">
        <div class="col">
          <p> {{ filledResponseString }}</p>
        </div>
        <div
          v-if="canRevert"
          class="col"
        >
          <MazBtn
            :color="'danger'"
            :loading="queueButtonLoading"
            fab
            @click="deleteQueue()"
          >
            Undo
          </MazBtn>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import dayjs from 'dayjs'

export default {
	name: 'RequestLineButton',
	props: {
		disabled: Boolean,
		type: String,
		leadId: Number,
		typeId: Number,
		canRevert: Boolean
	},
	data () {
		return {
			integrationName: 'Dakota',
			requesting: false,
			queue: null,
			showRequest: false,
			queueButtonLoading: false,
			loading: true,
			travelTime: null
		}
	},
	computed: {
		filledTime () {
			return dayjs(this.queue.times.filledTime).fromNow()
		},
		BtnText () {
			switch (this.type) {
			case 'integration':
				return 'Request Integration'
			case 'credit_app':
				return 'Request Credit'
			case 'sp2':
				return 'Request SP2'
			case 'sp1':
				return 'Request SP1'
			case 'sun_run_runner':
				return 'Send Docs to Customer'
			case 'd2d_call_center':
				return 'Request Solar Expert'
			case 'roof':
				return 'Request Roof Assessor'
			default:
				return 'Error'
			}
		},
		typeString () {
			switch (this.type) {
			case 'integration':
				return 'Searching for Integrator'
			case 'credit_app':
				return 'Searching for Credit Runner'
			case 'sun_run_runner':
				return 'Paperwork is being prepared'
			case 'sp2':
				return 'Searching for SP2'
			case 'sp1':
				return 'Searching for SP1'
			case 'd2d_call_center':
				return 'Searching For Solar Expert'
			case 'roof':
				return 'Searching for Roof Assessor'
			default:
				return 'oh no'
			}
		},
		filledResponseString () {
			switch (this.type) {
			case 'integration':
				return 'Integrator'
			case 'credit_app':
				return `${this.queue.filledRep.name.first} is working on the credit application they started ${this.filledTime}`
			case 'sun_run_runner':
				return `Paperwork is on the way to your clients email, started ${this.filledTime}`
			case 'sp2':
				return 'SP2'
			case 'sp1':
				return `They are ${this.travelTime} out`
			case 'd2d_call_center':
				return `${this.queue.filledRep.name.first}  will be calling shortly`
			case 'roof':
				return `${this.queue.filledRep.name.first}  is on it!`
			default:
				return 'oh no'
			}
		}
	},
	created () {
		this.checkForCurrent()
	},
	methods: {
		checkForCurrent () {
			const data = {
				leadId: this.leadId,
				type: this.type,
				typeId: this.typeId
			}
			axios.get(`/api/salesflow/line/lead/${this.leadId}`, data)
				.then((response) => {
					const queues = response.data.data.findIndex((r) => {
						if (r.lead.id === this.leadId && r.type === this.type) {
							return true
						}
					})
					this.queue = response.data.data[queues]
					if (this.queue) {
						if (this.queue.filledRep !== null) {
							console.log('queue started')
						}

						this.startQueue()
					}
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		requestIntegrations () {
			const data = {
				leadId: this.leadId,
				type: this.type,
				typeId: this.typeId
			}
			this.queueButtonLoading = true
			// console.log('running check on integration request ', data)
			axios.post('/api/salesflow/line', data)
				.then((response) => {
					this.queue = response.data.data
					this.startQueue()
					this.$emit('queue', this.queue)

					this.queueButtonLoading = false
					// this.queue.position = this.queue.position + 1;
				})
				.catch(function (error) {
					this.queueButtonLoading = false
					console.log(error)
				})
		},
		startQueue () {
			Echo.private('Queue')
				.listen('Backend.SalesFlow.Queue.QueuePageEvent', (e) => {
					if (this.queue) {
						if (this.queue.id === e.payload.id) {
							this.queue = e.payload
							this.$emit('queue', this.queue)
							this.travelTime = e.location
						} else {

						}
					}
				})
		},
		deleteQueue () {
			this.queueButtonLoading = true
			axios.delete(`/api/salesflow/line/${this.queue.id}`)
				.then((response) => {
					this.queue = null
					this.queueButtonLoading = false
				})
				.catch(function (error) {
					this.queueButtonLoading = false
					console.log(error)
				})
		}
	}

}
</script>

<style scoped>

</style>
