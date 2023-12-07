<template>
  <div v-if="loaded">
    <div>
      <p><strong>Created:</strong> {{ leadCreatedAt }}</p>
      <p v-if="leadClosedAt">
        <strong>Closed:</strong> {{ leadClosedAt }}
      </p>
    </div>
    <div class=" py-3">
      <p>Customer Sat</p>
      <MazSwitch
        v-model="salesPacket.sat"
        :color="'success'"
        :disabled="!$can('closer')"
        @input="updateSat"
      />
    </div>
    <!--            <h3 class="text-center py-3">Project Coordinator</h3>-->
    <div class="text-center py-3">
      <h4 style="color: #D87A34">
        Sales Packet
      </h4>
    </div>
    <div class="row justify-content-lg-start">
      <div class="col-12 text-center pb-2">
        <p>Solar Agreement</p>
        <template v-if="salesPacket.solar_agreement_signed">
          <strong>{{ $date(salesPacket.solar_agreement_signed).format('MMM D, YYYY h:mm A') }}</strong>
        </template>
        <template v-else>
          <strong>Not Yet </strong>
        </template>
      </div>
      <div class="col-12 text-center pb-2">
        <p>CPUC Doc Signed</p>
        <template v-if="salesPacket.cpuc_doc_signed">
          <strong>{{ $date(salesPacket.cpuc_doc_signed).format('MMM D, YYYY h:mm A') }}</strong>
        </template>
        <template v-else>
          <strong>Not Yet </strong>
        </template>
      </div>
      <div
        v-if="lead.epc_id === 2"
        class="col-12 text-center pb-2"
      >
        <p>Credit Doc Signed</p>
        <template v-if="salesPacket.credit_doc_signed">
          <strong>{{ $date(salesPacket.credit_doc_signed).format('MMM D, YYYY h:mm A') }}</strong>
        </template>
        <template v-else>
          <strong>Not Yet </strong>
        </template>
      </div>
      <div class=" col-12 pb-2">
        <p>ACH Doc Signed</p>
        <MazSwitch
          v-model="salesPacket.ach_doc_signed"
          :color="'success'"
          :disabled="!canEditACH"
          @input="updateACHDoc"
        />
      </div>
      <div
        v-show="lead.epc_id !== 1"
        class=" col-12-md text-center pb-2"
      >
        <p>NEM Doc Signed</p>
        <template v-if="salesPacket.nem_doc_signed">
          <strong>{{ $date(salesPacket.nem_doc_signed).format('MMM D, YYYY h:mm A') }}</strong>
        </template>
        <template v-else>
          <strong>Not Yet </strong>
        </template>
      </div>
      <div class="col-4-sm col-12-md col-lg-4 pb-2">
        <p>Submitted To EPC</p>
        <MazSwitch
          v-model="salesPacket.submitted"
          :color="'success'"
          :disabled="!$can('edit NTS')"
          @input="updateSubmitted"
        />
      </div>
    </div>
    <div
      v-if="lead.status !== 'New Lead'"
      class="row justify-content-lg-start"
    >
      <div class="col-12 text-center py-3">
        <h4 style="color: #D87A34">
          Customer Approval
        </h4>
      </div>
      <div class="col-12 text-center py-3">
        <p>Site Survey Date</p>
        <template
          v-if="(!salesPacket.site_survey_date
            && ($can('proposal build') || $can('administrate company')
              || $can('accept sp2')) && lead.epc_owner_id)"
        >
          <SiteSurveyCompleteModel
            v-if="canRemoteSurvey"
            class="mt-2"
            :lead="lead"
          />
          <MazBtn
            class="mt-2"
            :color="'secondary'"
            :loading="openSelfSurveyLoading"
            @click="openSelfSurvey"
          >
            Self Site Survey
          </MazBtn>
          <div />
        </template>
        <template v-else-if="salesPacket.site_survey_date">
          <strong> {{ $date(salesPacket.site_survey_date.start_time).format('MMM D, YYYY h:mm A') }} </strong>
        </template>
        <template v-else>
          <p>Not set yet</p>
        </template>
      </div>
      <div class="col-12">
        <div class="form-group">
          <template
            v-if="$can('edit proposal') || $can('create change order') || $can('administrate company')"
          >
            <MazInput
              v-model="salesPacket.site_survey_note"
              :textarea="true"
              :placeholder="'Super Secret notes'"
              :debounce="true"
              rows="3"
              @change="updateSiteSurveyNote(salesPacket.site_survey_note)"
            />
          </template>
          <template v-else>
            <p>{{ salesPacket.site_survey_note }}</p>
          </template>
        </div>
      </div>
      <div
        v-show="$can('create change order')"
        class="col-12 text-center pb-3"
      >
        <div class="row">
          <div class="col-lg-5 col-sm-12 mt-sm-3">
            <MazBtn
              v-if="lead.status_id !== 9"
              :color="'danger'"
              :loading="changeOrderLoading"
              @click="createChangeOrder()"
            >
              Change order
            </MazBtn>
            <MazBtn
              v-if="lead.status_id === 9"
              color="success"
              :loading="changeOrderLoading"
              @click="cancelChangeOrder()"
            >
              Cancel Change Order
            </MazBtn>
          </div>
          <div class="col-lg-5 col-sm-12 mt-sm-3">
            <RequestLineButton
              class=""
              :request-needed="true"
              :lead-id="lead.id"
              :type="'roof'"
            />
          </div>
        </div>
      </div>
      <div class="col-12 text-center pb-2 ">
        <p>Perfect Packet</p>
        <template v-if="salesPacket.converted">
          <strong>{{ $date(salesPacket.converted).format('MMM D, YYYY h:mm A') }}</strong>
        </template>
        <template v-else>
          <strong>Not yet</strong>
        </template>
      </div>
      <div class="col-12 text-center pb-2">
        <p>Site Plan Sent</p>
        <template v-if="salesPacket.design_plan_sent_date">
          <strong> {{ $date(salesPacket.design_plan_sent_date).format('MMM D, YYYY h:mm A') }}</strong>
        </template>
        <template v-else>
          <strong>Not yet</strong>
        </template>
      </div>
      <div class="col-12 text-center pb-2">
        <p>Site Plan Signed</p>
        <template v-if="salesPacket.design_plan_sent_date">
          <strong> {{ $date(salesPacket.site_plan).format('MMM D, YYYY h:mm A') }}</strong>
        </template>
        <template v-else>
          <strong>Not yet</strong>
        </template>
      </div>
      <div class="col-12  text-center pb-2">
        <p>Submitted For Permitting</p>
        <template v-if="salesPacket.submitted_for_permitting_date">
          <strong> {{
            $date(salesPacket.submitted_for_permitting_date).format('MMM D, YYYY h:mm A')
          }}</strong>
        </template>
        <template v-else>
          <strong> Not yet</strong>
        </template>
      </div>
      <div class="col-12 text-center">
        <p>Received Permitting</p>
        <template v-if="salesPacket.permitting_received_date">
          <strong>{{ $date(salesPacket.permitting_received_date).format('MMM D, YYYY h:mm A') }}</strong>
        </template>
        <template v-else>
          <strong> Not yet</strong>
        </template>
      </div>

      <div class="col-12 text-center py-3">
        <h4 style="color: #D87A34">
          Install & Power On
        </h4>
      </div>
      <div class="col-12 py-3 text-center w-100">
        <p>Install Date</p>

        <template v-if="salesPacket.install_date">
          <strong> {{ $date(salesPacket.install_date.start_time).format('MMM D, YYYY') }} </strong>
        </template>
        <template v-else>
          <p>Not set yet</p>
        </template>
      </div>
      <div class="col-12 ">
        <p>Permission To Operate</p>
        <MazSwitch
          v-model="salesPacket.pto"
          :color="'success'"
          :disabled="!canEditInstall"
        />
      </div>
    </div>
    <div>
      <MazBtn
        @click="$emit('hide')"
      >
        Hide
      </MazBtn>
    </div>
  </div>
  <div v-else>
    <MazLoader />
  </div>
</template>

<script>
import axios from 'axios'
import SiteSurveyCompleteModel from '../../Epc/SiteSurveyCompleteModel'
import RequestLineButton from '../line/RequestLineButton'

export default {
	name: 'PJCard',
	components: { RequestLineButton, SiteSurveyCompleteModel },

	props: {
		editing: Boolean,
		lead: Object,
		userId: Number,
		onActiveCount: Number,
		closedActive: Number,
		state: String,
		closeAppointment: Object

	},
	data () {
		return {
			value: false,
			installDate: null,
			siteSurveyDate: null,
			salesPacket: { pto: false, sat: false, submitted: false, ach_doc_signed: false },
			loaded: false,
			siteSurveyResult: null,
			changeOrderLoading: false,
			openSelfSurveyLoading: false
		}
	},
	computed: {

		canRemoteSurvey () {
			if (!this.lead.system) {
				return false
			}
			if (this.lead.system.epc_finance_id === 8 || this.lead.system.epc_finance_id === 9 || this.lead.system.epc_finance_id === 10) {
				return true
			}
            if (!this.closeAppointment){
                return false
            }
			if (this.state === 'CA' && this.closeAppointment.remote) {
				return true
			}
			return false
		},
		leadClosedAt () {
			if (this.lead.closedAt) {
				return this.$date(this.lead.closedAt).format('MM/DD')
			} else {
				return false
			}
		},
		leadCreatedAt () {
			return this.$date(this.lead.created_at).format('MM/DD')
		},
		canEditACH () {
			if (this.$can('edit ach')) {
				return true
			} else {
				return false
			}
		},

		canChangeOrder () {
			if (this.lead.closedAt && this.salesPacket.site_survey_note) {
				return true
			}
			return false
		},
		canEditSiteSurvey () {
			if (this.$can('edit proposal') || this.$can('administrate company')) {
				return true
			}
			return false
			// if ((this.$can('administrate company') || this.$can('proposal builder')) && this.lead.epc_id === 1) {
			//   return true;
			// } else {
			//   return false;
			// }
		},
		canEditInstall () {
			if (this.editing && (this.$can('integrations') || this.$can('proposal builder'))) {
				return true
			} else {
				return false
			}
		}

	},
	watch: {
		onActiveCount: function (newValue, oldValue) {
			this.getSalesPacket()
		}
	},

	created () {
		Echo.private('lead.' + this.$route.params.leadId)
			.listen('Backend.SalesFlow.Lead.SalesPacketEvent', (e) => {
				this.salesPacketEvent(e)
			})
		this.getSalesPacket()
	},

	mounted () {
		this.initInstall()
		this.initSiteSurvey()
	},
	methods: {
		openSelfSurvey () {
			this.openSelfSurveyLoading = true
			axios.get(`/api/epc/lead/${this.lead.id}/self-survey`)
				.then((response) => {
					window.open(response.data, '_blank')
					this.openSelfSurveyLoading = false
				}).catch((e) => {
				})
		},
		updateSiteSurveyDate (event) {
			this.salesPacket.site_survey_date = event
		},
		updateSiteSurveyNote (event) {
			const payload = {}
			payload.site_survey_note = event
			this.updateSalesPacket(payload)
		},
		salesPacketEvent (e) {
			const temp = e.data
			const salesPacket = this.salesPacket

			const tmp = {
				...salesPacket,
				...temp
			}
			this.salesPacket = tmp
		},

		hideSubmit (arg) {
			this.$emit('hide-submit', arg)
		},

		setInstall () {
			this.salesPacket.install_date = { start_time: this.installDate }
		},

		setSiteSurvey () {
			this.salesPacket.site_survey_date = { start_time: this.siteSurveyDate }
		},

		initInstall () {
			if (this.salesPacket.install_date) {
				this.installDate = this.salesPacket.install_date.start_time
			} else {
				this.salesPacket.install_date = {
					start_time: null
				}
			}
		},

		initSiteSurvey () {
			if (this.salesPacket.site_survey_date) {
				this.siteSurveyDate = this.salesPacket.site_survey_date.start_time
			} else {
				this.salesPacket.site_survey_date = {
					start_time: null
				}
			}
		},

		cancelChangeOrder () {
			this.changeOrderLoading = true

			axios.post(`/api/salesflow/lead/${this.lead.id}/sales-packet/${this.lead.sales_packet_id}/cancel-change-order`)
				.then((response) => {
					this.changeOrderLoading = false

					this.$emit('showProposedSystem')
				}).catch((e) => {
					console.log(e, 'customer update error')
				})
		},

		createChangeOrder () {
			this.changeOrderLoading = true

			axios.post(`/api/salesflow/lead/${this.lead.id}/sales-packet/${this.lead.sales_packet_id}/create-change-order`)
				.then((response) => {
					this.changeOrderLoading = false

					this.$emit('showProposedSystem')
				}).catch((e) => {
					console.log(e, 'customer update error')
				})
		},

		updateSiteSurveyResult (data) {
			this.creatingChangeOrder = true
			const payload = { result: data }

			axios.post(`/api/salesflow/lead/${this.lead.id}/sales-packet/${this.lead.sales_packet_id}/site-survey`, payload)
				.then((response) => {
					this.creatingChangeOrder = false
					this.$emit('showProposedSystem')
				}).catch((e) => {
					console.log(e, 'customer update error')
				})
		},

		getSalesPacket () {
			axios.get(`/api/salesflow/lead/${this.$route.params.leadId}/sales-packet/${this.lead.sales_packet_id}`)
				.then((response) => {
					this.salesPacket = response.data.data
					this.loaded = true
				}).catch(function (error) {
					console.log('lead error ', error)
				})
		},

		updateSolarAgreement (event) {
			const payload = {}
			payload.solar_agreement_signed = event
			this.updateSalesPacket(payload)
		},

		updateACHDoc (event) {
			const payload = {}
			payload.ach_doc_signed = event
			this.updateSalesPacket(payload)
		},

		updateSubmitted (event) {
			const payload = {}
			payload.submitted = event
			this.updateSalesPacket(payload)
		},

		updateCreditDoc (event) {
			const payload = {}
			payload.credit_doc_signed = event
			this.updateSalesPacket(payload)
		},

		updateSat (event) {
			const payload = {}
			payload.sat = event
			this.updateSalesPacket(payload)
		},

		updateSalesPacket (payload) {
			axios.put(`/api/salesflow/lead/${this.lead.id}/sales-packet/${this.lead.sales_packet_id}`, payload)
				.then((response) => {

				}).catch((e) => {
					console.log(e, 'customer update error')
				})
		}

	}
}
</script>

<style scoped>
#notes {
    margin-top: 20px;
}
</style>
