<template>
  <div>
    <div class="card-body">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <div class="container text-center">
              <transition name="fade">
                <div
                  v-show="proposalRequestingWheel ==='requesting'"
                  key="1"
                  class="row"
                >
                  <div class="col-12">
                    <h3>Requesting Proposal Builder</h3>
                    <MazSpinner
                      :color="'primary'"
                    />
                  </div>
                </div>
              </transition>
              <transition name="fade">
                <div
                  v-show="proposalRequestingWheel === 'assigned'"
                  key="1"
                  class="row"
                >
                  <div
                    v-if="builder"
                    class="col-12"
                  >
                    <div class="text-center">
                      <h3>Awaiting Building Proposal</h3>
                      <MazSpinner
                        :color="'warning'"
                      />
                      <h4>{{ builder.first_name }} {{ builder.last_name }}</h4>
                      <label class="strong">Call Integrations: </label>
                      <button
                        data-toggle="tooltip"
                        title="Call integrations"
                        class="btn btn-lg btn-primary mt-2"
                        @click="callIntegrations"
                      >
                        <span class="pr-1">{{ builder.phone_number }}</span>
                        <i
                          class="fa fa-phone"
                          aria-hidden="true"
                        />
                      </button>
                    </div>
                  </div>
                </div>
              </transition>
              <transition name="fade">
                <div
                  v-show="proposalRequestingWheel === 'approved'"
                  key="2"
                  class="row py-4"
                >
                  <div class="col-12">
                    <h3>Integration Approved!</h3>
                    <i
                      class="fa fa-check fa-4x"
                      style="color: #78E50F"
                      aria-hidden="true"
                    />
                  </div>
                </div>
              </transition>
              <transition name="fade">
                <div
                  v-show="proposalRequestingWheel === 'failed'"
                  key="3"
                  class="row py-4"
                >
                  <div class="col-12">
                    <h3>Integration Not Approved</h3>
                    <p> Sorry Not Approved. Usage and Billing amounts are too low 😞</p>
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </div>
    </div>
    <button
      data-toggle="tooltip"
      title="Start Submit Lead For Proposal"
      class="btn btn-lg btn-primary mt-2"
      @click="startRequesting = true"
    >
      <i class="fa fa-hand-paper fa-3x" />
      <span class="pr-1 ">Request Proposal</span>
    </button>
  </div>
</template>

<script>
import { DefaultLoader } from 'vue-spinners-css'

export default {
	name: 'ProposalStatus',
	components: {
		DefaultLoader
	},
	props: {
		leadId: Number,
		ApprovalStatus: null,
		builder: null,
		startRequesting: Boolean,
		user: null,
		leadStatus: String
	},
	data () {
		return {
			integrationName: '',
			integrationNumber: '',
			color: 'red'
		}
	},
	computed: {
		proposalRequestingWheel () {
			// console.log(this.ApprovalStatus, 'int status')
			if (this.ApprovalStatus === true) {
				return 'approved'
			} else if (this.ApprovalStatus === false) {
				return 'failed'
			}
			console.log(this.builder)
			if (this.builder) {
				return 'assigned'
			}
			if (this.startRequesting) {
				this.requestIntegrations()
			}
			return 'requesting'
		}
	},
	created () {
	},
	methods: {
		requestIntegrations: function () {
			const data = {
				user: this.user,
				lead: this.leadId,
				type: 'requestIntegration',
				market: window.LeadMarket,
				requestSp1: true
			}
			// console.log('running check on integration request ', data)
			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/integration`, data)
				.then((response) => {
					this.integrationRequesting = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		callIntegrations: function () {
			const call = `tel:${this.integrationNumber}`
			window.open(call)
		}
	}

}
</script>

<style scoped>

</style>
