<template>
  <div
    v-if="!loading"
    class="card"
  >
    <div class="card-header">
      <div class="row h-100 justify-content-center align-items-center">
        <div class="col-md-6 col-sm-12 pt-2">
          <h2 class="display-5">
            Lead: {{ lead.id }} - {{ lead.customer.full_name }}
          </h2>
        </div>
        <div class="col-md-6 col-sm-12">
          <template v-if="false">
            <template v-if="viewGoBackToggle && !sp1Involved">
              <book-sp2
                :user-id="getUser.data.id"
                :lead-id="lead.id"
                :editing="false"
                :canbook-own="false"
                :booking-type="8"
                @appointment="goBackToggle()"
              />
            </template>
          </template>
          <!--                    <template v-else>-->
          <!--                        <h4> Go Back Booked</h4>-->
          <!--                    </template>-->
        </div>
      </div>
    </div>
    <div class="row pt-3">
      <div class="col-sm-12 col-md-6">
        <div class="container text-center">
          <transition name="fade">
            <div
              key="1"
              class="row"
            >
              <div class="col-12 pb-4">
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
                  <p>{{ stage.spinnerOneSubH1 }}</p>
                  <template v-if="stage.showIntegrationsQueue">
                    <p v-if="stage.integrationsQueueCount">
                      Position in Queue: <strong>
                        {{ stage.integrationsQueueCount }} </strong>
                    </p>
                    <p v-if="!stage.integrationsQueueCount">
                      Your Next
                    </p>
                    <p v-if="counts">
                      Average Wait time: <strong>
                        {{ counts.integrations.waitTime }}</strong>
                    </p>
                  </template>
                  <MazSpinner
                    :size="120"
                    :color="stage.requestingIntegrationsColor"
                  />
                  <div class="row justify-content-center pt-3">
                    <div class="col">
                      <MazBtn
                        v-if="queue.filledRep"
                        @click="callIntegrations"
                      >
                        <span class="pr-1">{{ queue.filledRep.phone }}  </span>
                        <i
                          class="fa fa-phone"
                          aria-hidden="true"
                        />
                      </MazBtn>
                    </div>
                    <div
                      v-if="!panic"
                      class="col"
                    >
                      <template
                        v-if="!sp1Queue"
                        class="pt-3"
                      >
                        <MazBtn
                          :color="'danger'"
                          :loading="disableSp1Request"
                          @click="requestSp1()"
                        >
                          <span class="pr-1">Help  </span>
                          <i class="fas fa-ambulance" />
                        </MazBtn>
                      </template>
                    </div>
                  </div>
                </template>

                <template v-if="sp1Queue">
                  <div class="py-5">
                    <template v-if="sp1Queue.filledRep">
                      <h3 class="text-capitalize">
                        {{ sp1Queue.filledRep.fullName }} is on the way
                      </h3>
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
                    <template v-else-if="!sp1Queue.filledRep">
                      <h4>Searching for SP1</h4>
                      <p v-if=" sp1Queue.position">
                        Position in Queue: <strong>
                          {{ sp1Queue.position }} </strong>
                      </p>
                      <p v-if="!sp1Queue.position">
                        Your Next!
                      </p>
                      <p v-if="counts.sp1">
                        Average Wait time: <strong>
                          {{ counts.sp1.waitTime }}</strong>
                      </p>
                      <MazSpinner
                        :size="120"
                      />
                    </template>
                  </div>
                </template>
              </div>
            </div>
          </transition>
        </div>
      </div>
      <div class="col-sm-12 col-md-6">
        <div class="container text-center">
          <h3 class="text-center">
            Upload
          </h3>
          <DropZone
            v-if="lead.id && getUser.data.id"
            :lead-id="lead.id"
            :user-id="getUser.data.id"
            @upload="lead.uploads.unshift($event)"
          />
        </div>
      </div>
    </div>
    <div class="container border-info">
      <ShowUpload
        v-if="lead.id && getUser.data.id"
        :lead-id="lead.id"
        :user-id="getUser.data.id"
        :uploads="lead.uploads"
        :can-sales-rep="false"
        :can-sp1="false"
        :can-sp2="false"
        :can-build="false"
      />
    </div>
    <div class="container">
      <Note
        v-if="lead.id && getUser.data.id"
        :is-new-lead="false"
        :lead-id="lead.id"
        :user="getUser.data"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { Howl, Howler } from 'howler'
import Note from '../../components/lead/note/Note'
import DropZone from '../../components/lead/upload/DropZone'
import ShowUpload from '../../components/lead/upload/OldShowUpload'
import BookSp2 from '../../components/lead/appointment/BookSp2'

export default {
	name: 'LeadAwait',
	components: {
		BookSp2,
		ShowUpload,
		DropZone,
		Note

	},
	data: function () {
		return {
			viewGoBackToggle: true,
			lead: {
				status: null,
				company_cost_per_watt: null,
				customer_cost_per_watt: null,
				power_company: null,
				account_number: null,
				rate_plan: null,
				discount_plan: null,
				average_bill: null,
				office_id: null,
				system_size: null,
				utility_bill: null,
				credit_pass: null,
				credit_status: '',
				solar_agreement_signed: null,
				integrations_approved: null,
				editing: false,
				login: {
					user_name: ' ',
					password: ' '
				},
				customer: {
					id: null,
					language: 'english',
					full_name: null,
					rep: null,
					address: {
						street_address: null,
						city: null,
						state: null,
						zip: null
					},
					cell_phone: null,
					home_phone: null,
					email: null
				},
				disposition: [{
					id: 1,
					status: '',
					reason: ''
				}],
				userEdit: {
					id: 0,
					fullName: ''
				},
				request_integrations: false

			},

			sp1Phone: '',
			integrationName: '',
			integrationNumber: '',
			disableSp1Request: false,
			panic: false,
			loading: true,
			queue: {
				filledRep: null
			},
			sp1Queue: null,
			RequestingColor: 'primary',
			sp1QueueCount: 1,
			counts: null
		}
	},
	created () {
		this.fetchUsers()
		this.getQueue()
		this.getLead()
		this.checkCount()
		this.getSp1Queue()
	},
	mounted () {
		Echo.private(`Queue.${this.$route.params.queueId}`)
		// .listen('Backend.SalesFlow.Queue.UpdateEvent', (e) => {
		//     console.log(e);
		//     this.queue = e.data;
		//     this.playSound('/sounds/power_on.mp3')
		//
		// });

		Echo.private('Queue')
			.listen('Backend.SalesFlow.Queue.ElevatorEvent', (e) => {
				console.log(e)
				if (e.type === 'sp1') {
					const count = this.sp1QueueCount
					this.sp1QueueCount = count + e.elevator
				}
				if (e.type === 'integrations') {
					const count = this.queue.position
					this.queue.position = count + e.elevator
				}
			}).listen('Backend.SalesFlow.Queue.QueuePageEvent', (e) => {
				console.log(e, 'NewQueueEvent')
				if (this.queue.id === e.payload.id && e.payload.type === 'integrations') {
					this.queue = e.payload
					if (e.direction === 'filled') {
						console.log('sound?')
						this.playSound('/sounds/queue-filled.mp3')
					}
				} else if (this.queue.lead.id === e.payload.lead.id && e.payload.type === 'sp1') {
					this.sp1Queue = e.payload
					if (e.direction === 'filled') {
						console.log('sound?')
						this.playSound('/sounds/queue-filled.mp3')
					}
				}
			})

		Echo.private(`lead.${this.$route.params.leadId}`)
			.listen('Backend.SalesFlow.Lead.LeadUpdateEvent', (e) => {
				console.log(e, ' queue update event')
				if (e.data.integrations_approved === 3) {
					// this.getQueue()
					// setTimeout(() => {
					//     this.lead.integrations_approved = 3;
					// }, 2000);
					this.lead.integrations_approved = 3
				} else if (e.data.integrations_approved === 2) {
					this.lead.integrations_approved = 2
					this.playSound('/sounds/deny.mp3')
				}
			})
			.listen('Backend.SalesFlow.RepAddedToLeadEvent', (e) => {
				this.lead.reps.push(e.rep)
			})

		// setInterval(() => {
		//     if (this.lead.integrations_approved === 1) {
		//         this.getLead();
		//     }
		//     this.getQueue();
		//
		// }, 10000);

		// this.checkCount();
	},
	computed: {

		...mapGetters(['getUser']),

		hasSp1 () {
			if (this.lead.reps) {
				const sp1 = this.lead.reps.filter(function (p) {
					return p.position_id === 2
				})
				if (sp1.length > 0) {
					return true
				} else {
					return false
				}
			}
		},
		sp1 () {
			return this.lead.reps.filter(function (p) {
				return p.position_id === 2
			})
		},

		sp1Involved () {
			if (this.hasSp1 || this.lead.integrations_approved === 3 || this.sp1Queue) {
				return true
			} else {
				return false
			}
		},

		stage () {
			const s = {
				requestingIntegrationsColor: 'primary',
				spinnerOneH1: '',
				spinnerOneSubH1: '',
				spinnerTwoH1: '',
				spinnerTwoSub: '',
				canRequestSp1: false,
				integratorNumber: '',
				integrationsQueueCount: null,
				secondSpinner: false,
				showIntegrationsQueue: true
			}

			if (this.lead.integrations_approved === 1) {
				if (!this.queue.filledRep) {
					s.requestingIntegrationsColor = 'primary'
					s.spinnerOneH1 = 'Requesting an integrator'
					s.spinnerOneSubH1 = ''
					s.integrationsQueueCount = this.queue.position

					if (this.hasSp1.length === 0) {
						s.canRequestSp1 = true
					}
				} else {
					s.requestingIntegrationsColor = 'secondary'
					s.spinnerOneH1 = 'Awaiting Integrations Approval'
					s.integratorNumber = this.queue.filledRep.phone
					s.spinnerOneSubH1 = this.queue.filledRep.fullName
					s.showIntegrationsQueue = false

					if (!this.hasSp1) {
						s.canRequestSp1 = false
						s.secondSpinner = false
					} else {
						s.secondSpinner = true
					}
				}
			}

			return s
			if (this.lead.integrations_approved === null && !this.lead.request_integrations) {
				return 2
			}
		},

		goBack () {
			if (this.queue) {
				if (this.queue.related) {
					const data = this.queue.related.filter(function (p) {
						return p.type === 'Go Back'
					})
					// return data;
					if (data.length > 0) {
						this.sp1QueueCount = data[0].position

						return true
					}
				}
				return false
			}
		},

		// sp1Queue() {
		//     if (this.queue) {
		//
		//         if (this.queue.related) {
		//             let data = this.queue.related.filter(function (p) {
		//                 return p.type === 'sp1';
		//             })
		//
		//             if (data.length > 0) {
		//                 console.log(data, 'lots of data')
		//                 this.sp1QueueCount = data[0].position
		//                 console.log(data[0], 'sp1 related')
		//                 return data[0];
		//             }
		//         }
		//         return null;
		//     }
		// },

		cellPhone () {
			console.log('phone number ', this.lead.customer.cell_phone)
			const cleaned = ('' + this.lead.customer.cell_phone).replace(/\D/g, '')
			const match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/)
			if (match) {
				const intlCode = (match[1] ? '+1 ' : '')
				return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('')
			}
			return null
		},
		homePhone () {
			const cleaned = ('' + this.lead.customer.home_phone).replace(/\D/g, '')
			const match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/)
			if (match) {
				const intlCode = (match[1] ? '+1 ' : '')
				return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('')
			}
			return null
		}

	},

	methods: {
		...mapActions(['fetchUsers']),

		playSound (file_path) {
			const sound = new Howl({
				src: file_path,
				volume: 0.25
			})
			sound.play()
		},

		goBackToggle () {
			this.viewGoBackToggle = false
		},
		updateLead (data) {
			if (data.data.id === this.lead.id) {
				console.log('u data update received', data)
				this.lead = _.merge(this.lead, _.pick(data.data, _.keys(this.lead)))
			}
		},

		checkCount: function () {
			axios.get('/api/salesflow/line/count?type=all')
				.then((response) => {
					console.log(response)
					this.counts = response.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		integrationsQueue (elevator) {
			const position = this.queue.position

			this.queue.position = position + elevator
		},
		getSp1Queue () {
			axios.get(`/api/salesflow/line/${this.$route.params.queueId}?&type=related`)
				.then((response) => {
					const sp1 = response.data.data

					const realSp1 = sp1.filter(function (p) {
						return p.type === 'sp1'
					})
					this.sp1Queue = realSp1[0]
				}).catch(function (error) {
					console.log(error)
				})
		},

		getQueue () {
			axios.get(`/api/salesflow/line/${this.$route.params.queueId}?&type=integrations`)
				.then((response) => {
					console.log(response)
					this.queue = response.data.data
				}).catch(function (error) {
					console.log(error)
				})
		},
		requestSp1: function () {
			const data = {
				leadId: this.lead.id,
				type: 'sp1 panic'
			}
			this.disableSp1Request = true
			axios.post('/api/salesflow/line', data)
				.then((response) => {
					this.sp1Queue = response.data
					this.panic = true
					this.startRequesting = true
					this.disableSp1Request = true
				})
				.catch(function (error) {
					this.disableSp1Request = false
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: 'Something went wrong!',
						footer: 'Submit a report in scout and we will get on this right away'
					})

					console.log(error)
				})
		},

		getLead () {
			axios.get(`/api/salesflow/lead/${this.$route.params.leadId}`)
				.then((response) => {
					console.log(response)
					this.lead = response.data.data
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		callIntegrations: function () {
			const call = `tel:${this.filledRep.phone}`
			window.open(call)
		}

	}

}
</script>

<style scoped>
/* Enter and leave animations can use different */
/* durations and timing functions.              */
.slide-fade-enter-active {
    transition: all .3s ease;
}

.slide-fade-leave-active {
    transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}

.slide-fade-enter, .slide-fade-leave-to
    /* .slide-fade-leave-active below version 2.1.8 */
{
    transform: translateX(10px);
    opacity: 0;
}
</style>
