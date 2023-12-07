<template>
  <div v-if="system && financeOptions">
    <div class="row justify-content-between">
      <div class="col-md-6 col-sm-12">
        <div
          v-if="!editing"
          class="form-group"
        >
          <h5>Proposal ID</h5>
          <p>{{ system.external_proposal_id }}</p>
        </div>
        <div v-else>
          <MazInput
            v-model="system.external_proposal_id"
            type="text"
            class="form-group"
            placeholder="Proposal ID"
            :disabled="!editing"
            @blur="updateProposalId(system.external_proposal_id)"
          />
          <div>
            <template v-if="updateHelioTrackBtn">
              <MazInput
                v-model="system.complete_url"
                placeholder="Helio Track Job"
                @blur="updateCompleteURL(system.complete_url)"
              />
              <MazBtn
                :icon-name="'edit'"
                fab
                @click="updateHelioTrackBtn = false"
              />
            </template>
            <template v-else>
              <MazBtn
                v-if="system.complete_url"

                fab

                @click="goToProposal(system.complete_url)"
              >
                HT
              </MazBtn>
              <MazBtn
                :icon-name="'edit'"
                fab
                @click="updateHelioTrackBtn = true"
              />
            </template>
          </div>
        </div>
      </div>
      <div
        v-if="financeOptions && system.epc_finance_id"
        class="col-md-6 col-sm-12"
      >
        <div
          v-if="!editing"
          class="form-group"
        >
          <h5>Finance</h5>
          <span>{{ showFinance.label }} </span>
        </div>
        <div
          v-else
          class="form-group"
        >
          <MazSelect
            v-model="system.epc_finance_id"
            :placeholder="'Finance'"
            class="form-group"
            :no-label="true"
            :search="true"
            :options="financeOptions"
            :debounce="true"
            @input="updateFinance($event)"
          />
        </div>
      </div>

      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput
            v-model="system.system_size"
            :placeholder="'System Size'"
            type="text"
            :disabled="!editing"
            @blur="updateSystemSize(system.system_size)"
          />
        </div>
        <div
          v-else
          class="form-group"
        >
          <div
            v-if="system.system_size"
            class="form-group"
          >
            <h5>Size</h5>
            <p>{{ system.system_size.toLocaleString() }} KW </p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-sm-12">
        <div
          v-if="!editing"
          class="form-group"
        >
          <h5>Yearly Production</h5>
          <p>{{ system.yearly_production }} kWh </p>
        </div>

        <div
          v-else
          class="form-group"
        >
          <MazInput
            v-model="system.yearly_production"
            type="number"
            placeholder="Yearly Production"
            :disabled="!editing"
            @blur="updateYearlyProduction(system.yearly_production)"
          />
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          v-show="showSolarRate"
          class="form-group"
        >
          <MazInput
            v-model="system.solar_rate"
            type="text"
            placeholder="Solar Rate"
            :disabled="!editing"
            @blur="updateSolarRate(system.solar_rate)"
          />
        </div>
        <div v-else>
          <h5>Solar Rate</h5>
          <p>${{ system.solar_rate }}</p>
        </div>
      </div>

      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput
            v-model="system.monthly_payment"
            type="text"
            placeholder="Monthly Payment"
            :disabled="!editing"
            @input="updateMonthlyPayment(system.monthly_payment)"
          />
        </div>
        <div v-else>
          <template v-if="system.monthly_payment">
            <h5>Monthly Payment</h5>
            <p>${{ monthlyPayment }}</p>
          </template>
        </div>
      </div>
      <template v-show="$can('view ppw')|| $can('proposal builder') || $can('administrate company')">
        <div
          v-if="editing"
          class="col-md-6 col-sm-12"
        >
          <div class="form-group">
            <MazInput
              v-model="system.ppw"
              placeholder="Price Per Watt"
              @blur="updatePpw(system.ppw)"
            />
          </div>
        </div>
        <div
          v-else
          class="col-md-6 col-sm-12"
        >
          <h5>Price Per watt</h5>
          <p>${{ system.ppw }} </p>
        </div>
      </template>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput
            v-model="system.offset"
            type="text"

            placeholder="Offset"
            :disabled="!editing"
            @blur="updateOffset(system.offset)"
          />
        </div>
        <div v-else>
          <h5>Offset</h5>
          <p>
            {{ system.offset }}%
          </p>
        </div>
      </div>
      <div
        v-if="editing "
        class="col-md-6 col-sm-12"
      >
        <div class="form-group">
          <MazInput
            v-model="system.contract_amount"
            type="text"
            placeholder="Contract Amount"
            :disabled="!editing"
            @blur="updateContractAmount(system.contract_amount)"
          />
        </div>

      </div>
        <div
           v-else
            class="col-md-6 col-sm-12"
        >

            <div>
                <h5>Contract Amount</h5>
                <p>
                    ${{ system.contract_amount }}
                </p>
            </div>
        </div>
      <div
        v-if="system"
        class="col-12"
      >
        <SystemEquipment
          :epc-systems="epcSystems"
          :selected-system="system"
          :can-edit="editing"
          :modules="modules"
          :inverters="inverters"
          @updateModules="updateModules(event)"
          @updateInverter="updateInverter(event)"
          @updateModulesCount="updateModulesCount(event)"
        />
      </div>
    </div>

    <template v-if="editing">
      <MazSelect
        v-model="system.adders"
        :placeholder="'Adders'"
        multiple
        :options="adders"
        search
        :size="'sm'"
        clearable
        :disabled="!editing"
        @input="updateNewAdders($event)"
      >
        <i
          slot="icon-left"
          class="material-icons"
        >
          add_circle_outline
        </i>
      </MazSelect>
    </template>
    <AddersList
      :system_size="system.system_size"
      :selected-new-adders="system.adders"
      :epc-adders="epcAdders"
      :roof-work="system.roof_work"
    />
    <SystemBox
      :apr="showFinance.rate"
      :utility="averageBill"
      :monthly-payment="system.monthly_payment"
    />

    <InstallQuestionsDisplay
      :questions="lead.siteSurveyQuestions"
    />
  </div>
</template>

<script>
import axios from 'axios'
import RequestedSystem from './OldRequestedSystem'
import AddersList from './AddersList'
import SystemEquipment from './SystemEquipment'
import SystemBox from './systemBox/SystemBox'
import InstallQuestionsDisplay from './InstallQuestionsDisplay'

export default {
	name: 'System',
	components: { InstallQuestionsDisplay, SystemBox, SystemEquipment, AddersList },
	props: {
		financeOptions: Array,
		epcSystems: Array,
		epcAdders: Array,
		lead: Object,
		modules: Array,
		inverters: Array,
		onActiveCount: Number,
		averageBill: Number

	},
	data () {
		return {
			updateHelioTrackBtn: false,
			selectedNewAdders: [],
			neededBy: null,
			disApproveOpenDialog: false,
			approveOpenDialog: false,
			commentValue: null,
			disabledHours: ['00', '01', '02', '03', '04', '05', '06', '07',
				'22', '23'],
			disabled: false,
			system: {}
		}
	},
	computed: {
		// systemSize() {
		//     if (this.system.modules_id) {
		//
		//         return (this.modules.find(d => d.value === this.system.modules_id).watts * this.system.modules_count) / 1000;
		//     }
		// },
		adders () {
			const x = []
			$.each(this.epcAdders, function (key, value) {
				x.push({ label: value.name, value: value.id })
				x.value = value.id
			})
			return x
		},
		editing () {
			return this.$can('edit system')
		},

		showSolarRate () {
			const sunrunids = [8,
				9,
				10,
				71,
				79,
				91,
				92,
				93,
				112,
				113]
			if (sunrunids.includes(this.system.epc_finance_id)) {
				return true
			} else {
				return false
			}
		},
		monthlyPayment () {
			return this.system.monthly_payment.toLocaleString()
		},
		showFinance () {
			return this.financeOptions.find(d => d.value === this.system.epc_finance_id)
		},

		selectedAddersCost () {
			const adder = this.selectedAdders
			return _.sumBy(adder, 'cost')
		},
		selectedAdders () {
			const array = []
			if (this.system.adders) {
				const epcAdders = this.epcAdders
				$.each(this.system.adders, function (skey, value) {
					$.each(epcAdders, function (okey, oAdder) {
						if (oAdder.id === value) {
							array.push(oAdder)
						}
					})
				})
				return array
			} else {
				return array
			}
		},

		systemsArray () {
			const x = []
			$.each(this.epcSystems, function (key, value) {
				x.push({ label: value.name, value: value.id })
				x.value = value.id
			})
			return x
		}
	},
	watch: {
		onActiveCount: function (newValue, oldValue) {
			this.getSystems()
		}
	},
	created () {
		this.getSystems()
	},
	methods: {
		goToProposal (path) {
			window.open(path)
		},

		updateInverter () {
			const payload = {}
			payload.inverter_id = this.system.inverter_id
			this.updateSystem(payload)
		},
		updateNewAdders (event) {
			const payload = {}
			payload.adders = event

			this.updateSystem(payload)
			if (!event) {
				this.selectedNewAdders = []
			}
		},
		updateModulesCount () {
			const payload = { modules_count: this.system.modules_count }
			this.updateSystem(payload)
		},
		updateModules () {
			const payload = {}
			payload.modules_id = this.system.modules_id
			this.updateSystem(payload)
		},
		updateContractAmount (event) {
			const payload = {}
			payload.contract_amount = event
			this.updateSystem(payload)
		},
		updateOffset (event) {
			const payload = {}
			payload.offset = event
			this.updateSystem(payload)
		},
		updateCompleteURL (event) {
			const payload = {}
			payload.complete_url = event
			this.updateSystem(payload)
		},
		updateFinance (event) {
			const payload = {}
			payload.epc_finance_id = event
			this.updateSystem(payload)
		},
		updateSystemSize (event) {
			const payload = {}
			payload.system_size = event
			this.updateSystem(payload)
		},
		updatePpw (event) {
			const payload = {}
			payload.ppw = event
			this.updateSystem(payload)
		},

		updateProposalId (event) {
			const payload = {}
			payload.external_proposal_id = event
			this.updateSystem(payload)
		},

		updateYearlyProduction (event) {
			const payload = {}
			payload.yearly_production = event
			this.updateSystem(payload)
		},
		updateMonthlyPayment (event) {
			const payload = {}
			payload.monthly_payment = event
			this.updateSystem(payload)
		},
		updateSolarRate (event) {
			const payload = {}
			payload.solar_rate = event
			this.updateSystem(payload)
		},

		updateSystem (payload) {
			axios.put(`/api/salesflow/lead/${this.lead.id}/system/${this.system.id}`, payload)
				.then((response) => {
					console.log(response)
				})
		},

		getSystems () {
			axios.get(`/api/salesflow/lead/${this.$route.params.leadId}/system`)
				.then((response) => {
					console.log(response, 'fetch systems')
					this.system = response.data.data
					this.$emit('newSystem', this.system)
					this.loading = false
				}).catch(function (error) {
					console.log('lead error ', error)
				})
			t
		},

		approveOrNot (e) {
			if (this.market !== 'sub dealership') {
				if (e) {
					this.changeStatus(e)

					// let sp2Appointment = this.appointments.find(e => e.type_id === 3);
					// console.log(sp2Appointment);
					// if (sp2Appointment){
					//     this.neededBy = sp2Appointment;
					//
					//     this.changeStatus(e);
					//
					// }
				} else {
					this.$emit('editing', true)
					this.disApproveOpenDialog = true
				}
			} else {
				if (e) {
					this.approveOpenDialog = true
				} else {
					this.$emit('editing', true)
					this.disApproveOpenDialog = true
				}
			}
		},
		changeStatus (decision) {
			let data = null
			let messageTitle = 'On it!'
			if (decision) {
				data = {
					type: 'sales rep approved',
					neededBy: this.neededBy
				}
			} else {
				messageTitle = 'We are working on the changes.'

				data = {
					type: 'sales rep denied',
					proposal: this.proposal,
					comment: this.commentValue,
					userId: this.userId
				}
			}

			axios.put(`/api/salesflow/lead/${this.$route.params.leadId}/status`, data)
				.then((response) => {
					this.disabled = true
					this.approveOpenDialog = false
					this.disApproveOpenDialog = false
					this.$emit('lead-update', response.data.data)
					this.$emit('editing', false)
					this.$emit('sent-back', true)

					Swal.fire({
						type: 'success',
						title: messageTitle,
						text: 'We will get back to you soon'
					})
				})
				.catch(function (error) {
					this.$emit('editing', false)
					this.approveOpenDialog = false
					this.disApproveOpenDialog = false

					console.log(error)
					Swal.fire({
						type: 'error',
						title: 'Oops...',
						text: 'Something went wrong!',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Submit an issue?',
						cancelButtonText: 'No Thanks'
					}).then((result) => {
						if (result.value) {
							document.getElementById('submitIssueButton').click()
						}
					})
				})
		}
	}

}
</script>

<style scoped>

</style>
