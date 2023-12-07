<template>
  <div
    v-if="!loading"
    class="text-center"
  >
    <div v-if="shouldShow">
      <div
        v-if="queue"
        class="row justify-content-center"
      >
        <div class="col">
          <template v-if="lead.integrations_approved === 3">
            <h4>Integration Approved!</h4>
            <i
              class="fa fa-check fa-4x"
              style="color: #78E50F"
              aria-hidden="true"
            />
          </template>
          <template v-else-if="lead.integrations_approved === 2">
            <h4>Integration Not Approved</h4>
            <p> Sorry Not Approved. Usage and Billing amounts are too low 😞</p>
            <router-link
              to="/dashboard/lead/create"
              data-toggle="tooltip"
              title=""
              class="btn btn-third btn-lg mt-3"
            >
              <span class="pr-1">New Lead</span>
              <i class="fas fa-plus-circle" />
            </router-link>
          </template>
          <template v-if="lead.integrations_approved === 1">
            <h4>{{ stage.spinnerOneH1 }}</h4>
            <p v-if="stage.integrationsQueueCount">
              Position in Queue: {{ stage.integrationsQueueCount }}
            </p>
            <template v-if="stage.showIntegrationsQueue">
              <!--                            <p v-if="stage.integrationsQueueCount">Position in Queue: <strong>-->
              <!--                                {{ stage.integrationsQueueCount }} </strong></p>-->
              <!--                            <p v-if="!stage.integrationsQueueCount">Your Next</p>-->
              <p v-if="counts.integrations">
                Average Wait time: <strong>
                  {{ counts.integrations.waitTime }}</strong>
              </p>
            </template>
            <MazSpinner
              :size="120"
              :color="stage.RequestingIntegrationsColor"
            />
            <div
              v-if="queue.filledRep"
              class="row justify-content-center pt-3"
            >
              <div class="col">
                <h5>{{ queue.filledRep.fullName }}</h5>
                <MazBtn @click="callIntegrations">
                  <span class="pr-1">{{ queue.filledRep.phone }}  </span>
                  <i
                    class="fa fa-phone"
                    aria-hidden="true"
                  />
                </MazBtn>
              </div>
            </div>
          </template>
        </div>
      </div>
      <div v-show="!queue">
        <RequestLineButton
          :lead-id="lead.id"
          :type="'integration'"
          @queue="queue = $event"
        />
      </div>
    </div>
  </div>
</template>

<script>

import RequestLine from '../line/RequestLineButton'
import RequestLineButton from '../line/RequestLineButton'

export default {

	name: 'SelfGenIntegration',
	components: { RequestLineButton, RequestLine },
	props: {
		lead: Object,
		user: Object,
		status: String,
		integrator: Object,
		onActiveCount: Number
	},
	data () {
		return {
			integrationName: '',
			requesting: false,
			queue: null,
			showRequest: false,
			queueButtonLoading: false,
			loading: true

		}
	},
	computed: {

		shouldShow () {
			switch (this.lead.status) {
			case 'New Lead':
				return true
			case 'Pending Credit Check':
				return true
			case 'Credit Pass':
				return true
			default:
				return false
			}
		},
		stage () {
			const s = {
				RequestingIntegrationsColor: 'primary',
				spinnerOneH1: '',
				spinnerOneSubH1: '',
				spinnerTwoH1: '',
				spinnerTwoSub: '',
				canRequestSp1: false,
				integratorNumber: '',
				integrationsQueueCount: null
			}

			if (this.lead.integrations_approved === 1) {
				if (!this.queue.filledRep) {
					s.RequestingIntegrationsColor = 'primary'
					s.spinnerOneH1 = 'Requesting an Integrator'
					s.spinnerOneSubH1 = ''
					if (this.queue.position) {
						s.integrationsQueueCount = this.queue.position
					} else {
						s.integrationsQueueCount = 'First'
					}
				} else {
					s.integrationsQueueCount = ''
					s.RequestingIntegrationsColor = 'secondary'
					s.spinnerOneH1 = 'Awaiting Integrations Approval'
					s.integratorNumber = this.queue.filledRep.filledRep
					s.spinnerOneSubH1 = this.queue.filledRep.fullName
				}
			}

			return s
		}
	},
	watch: {
		onActiveCount: function (newValue, oldValue) {
			this.getQueue()
		}
	},
	created () {
		this.integratorPresent()

		this.getQueue()

		Echo.private('Queue')
			.listen('Backend.SalesFlow.Queue.ElevatorEvent', (e) => {
				if (e.type === 'integrations' && (e.elevator < 0)) {
					const count = this.queue.position
					this.queue.position = count + e.elevator
				}
			})
			.listen('Backend.SalesFlow.Queue.QueuePageEvent', (e) => {
				if (this.queue) {
					if (this.queue.id === e.payload.id && e.payload.type === 'integrations') {
						this.queue = e.payload
					} else if (this.queue.id === e.payload.id && e.payload.type === 'sp1') {

					}
				}
			})
	},
	methods: {

		getQueue () {
			axios.get(`/api/salesflow/line?type=integrations&lead_id=${this.lead.id}`)
				.then((response) => {
					this.loading = false
					if (response.data.data.length) {
						this.queue = response.data.data[0]
						this.queue.position = this.queue.position + 1
						this.showRequest = true
					}
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		integratorPresent () {
			if (this.integrator || this.requesting) {
				this.startRequesting = true
			}
		},
		requestIntegrations () {
			const data = {
				leadId: this.lead.id,
				type: 'integration'
			}
			this.queueButtonLoading = true
			// console.log('running check on integration request ', data)
			axios.post('/api/salesflow/line', data)
				.then((response) => {
					this.queue = response.data.data
					this.queue.position = this.queue.position + 1
				})
				.catch(function (error) {
					this.queueButtonLoading = false
					console.log(error)
				})
		},

		callIntegrations () {
			const call = `tel:${this.integrator.phone_number}`
			window.open(call)
		}
	}

}
</script>

<style scoped>

</style>
