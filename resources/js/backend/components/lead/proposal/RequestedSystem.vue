<template>
  <div v-if="!loading">
    <div class="py-3">
      <div class="row justify-content-between">
        <div class="col">
          <MazStepper
            v-model="activeStep"
            :steps="systems"
            :size="40"
            show-step-number
            shadow
          />
        </div>
        <div
          v-if="canCreateNew"
          class="col"
        >
          <MazBtn
            :loading="requesting"
            dab

            @click="createRequestedSystem"
          >
            New
          </MazBtn>
        </div>
        <div class="col" />
      </div>
    </div>

    <div
      v-if="requestedSystems.length !== 0"
      class="row"
    >
      <div class="col-12">
        <div
          v-if="canEdit"
          class="row"
        >
          <div class="col m-4">
            <label>Urgent</label>
            <MazSwitch
              v-model="urgent"
              :color="'danger'"
            />
          </div>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="canEdit"
          class="form-group"
        >
          <MazSelect
            v-model="selectedSystem.epc_finance_id"
            :placeholder="'Finance'"
            :no-label="true"
            :search="true"
            :options="financeOptions"
            :debounce="true"
            @input="updateFinance($event)"
          />
        </div>
        <div
          v-else
          class="form-group"
        >
          <template v-if="selectedSystem.epc_finance_id">
            <h5>Finance</h5>
            <template>
              <span>{{ showFinance }} </span>
            </template>
          </template>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="canEdit"
          class="form-group"
        >
          <h5>Size</h5>
          <span>{{ systemSize }} Kw</span>
          <!--                    <MazInput-->
          <!--                        :placeholder="'System Size'"-->
          <!--                        :disabled="!canEdit"-->
          <!--                        :debounce="true"-->
          <!--                        @input="updateSystemSize(selectedSystem.system_size)"-->
          <!--                        v-model="selectedSystem.system_size"/>-->
        </div>
        <div
          v-else-if="selectedSystem.system_size"
          class="form-group"
        >
          <h5>Size</h5>
          <span>{{ systemSize }} Kw</span>
        </div>
      </div>

      <div class="col-md-6 col-sm-12">
        <div
          v-if="canEdit"
          class="form-group"
        >
          <MazSelect
            v-model="selectedSystem.solar_rate"
            :search="true"
            :search-placeholder="'Solar Rate'"
            :options="solarRateArray"
            :placeholder="'Solar Rate'"
            :disabled="!canEdit"
            @input="updateSolarRate(selectedSystem.solar_rate)"
          />
        </div>
        <div
          v-else-if="selectedSystem.solar_rate"
          class="form-group"
        >
          <h5>Solar Rate</h5>
          <span>${{ selectedSystem.solar_rate }} </span>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="canEdit"
          class="form-group pb-4"
        >
          <template
            v-if="canEdit"
            class=""
          >
            <vue-slider
              v-model="selectedSystem.offset"
              :max="200"
              :marks="offsetRangeArray"
              :disabled="!canEdit"
              @drag-end="updateOffset(selectedSystem.offset)"
            />
          </template>
        </div>
        <div class="row">
          <div
            v-if="selectedSystem.offset > 0"
            class="col"
          >
            <h5>Offset</h5>
            <span>{{ selectedSystem.offset }}% </span>
          </div>
          <div
            v-if="YearlyGenerated !== '0'"
            class="col"
          >
            <h5>Production per year</h5>
            <span>{{ YearlyGenerated }} kWh </span>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="canEdit"
          class="form-group"
        >
          <MazInput
            v-model="selectedSystem.monthly_payment"
            :placeholder="'Monthly Payment'"
            :disabled="!canEdit"
            :debounce="true"
            @input="updateMonthlyPayment(selectedSystem.monthly_payment)"
          />
        </div>
        <div
          v-else-if="selectedSystem.monthly_payment"
          class="form-group"
        >
          <h5>Monthly Payment</h5>
          <span>${{ formatMonthlyPayment }} </span>
        </div>
      </div>
      <div
        v-show="$can('view ppw') || $can('edit proposal')||
          $can('administrate company')"
        class="col-md-6 col-sm-12"
      >
        <div
          v-if="canEdit"
          class="form-group"
        >
          <MazInput

            v-model="selectedSystem.ppw"
            :placeholder="'Price Per Watt'"
            :disabled="!canEdit"
            :debounce="true"
            @input="updatePpw(selectedSystem.ppw)"
          />
        </div>
        <div
          v-else-if="selectedSystem.ppw"
          class="form-group"
        >
          <h5>Price Per Watt</h5>
          <span>${{ selectedSystem.ppw }} </span>
        </div>
      </div>

      <div class="col-md-6 col-sm-12">
        <div
          v-if="($can('edit proposal') || $can('administrate company')) && lead.status === 'Change Order'"
          class="form-group"
        >
          <MazInput
            v-model="contractAmount"
            :type="'string'"
            :placeholder="'Contract Amount'"
            :disabled="!canEdit"
            :debounce="true"
          />
        </div>
      </div>

      <div class="col-md-6 col-sm-12">
        <div
          v-if="canEdit"
          class="form-group"
        >
          <MazInput
            v-model="selectedSystem.roof_work"
            :type="'number'"

            :placeholder="'Roof Work'"
            :disabled="!canEdit"
            :debounce="true"
            @input="updateRoofWork(selectedSystem.roof_work)"
          />
        </div>
        <div
          v-else-if="selectedSystem.roof_work"
          class="form-group"
        >
          <h5>Roof Work</h5>
          <span v-if="selectedSystem.roof_work">${{ selectedSystem.roof_work }} </span>
        </div>
      </div>

      <div class="col-12">
        <SystemEquipment
          :epc-systems="epcSystems"
          :selected-system="selectedSystem"
          :can-edit="canEdit"
          :modules="modules"
          :inverters="inverters"
          @updateModules="updateModules"
          @updateInverter="updateInverter"
          @updateModulesCount="updateModulesCount"
        />
      </div>
      <div class="col-sm-12">
        <div class="form-group">
          <template v-if="canEdit">
            <MazSelect
              v-model="selectedNewAdders"
              :placeholder="'Adders'"
              multiple
              :options="adders"
              search
              :size="'sm'"
              clearable
              :disabled="!canEdit"
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
          <div class="row pt-3">
            <div class="col">
              <AddersList
                :system_size="parseInt(selectedSystem.system_size)"
                :selected-new-adders="selectedNewAdders"
                :epc-adders="epcAdders"
                :roof-work="selectedSystem.roof_work"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="selectedSystem"
      class="row text-center pb-5"
    >
      <div
        v-if="$can('edit requested system') || $can('administrate company')"
        class="col-md-6 col-sm-12"
      >
        <h4>Sales Rep</h4>
        <MazInput
          v-model="selectedSystem.sales_rep_note"
          placeholder="Comment"
          autocomplete="new-comment"
          left-icon-name="comment"
          textarea
          :debounce="true"
          @input="updateSalesRepNote"
        />
      </div>
      <div
        v-else
        class="col-md-6 col-sm-12"
      >
        <h4>Sales Rep</h4>
        <p>{{ selectedSystem.sales_rep_note }}</p>
      </div>
      <div
        v-if="$can('edit proposal') || $can('administrate company')"
        class="col-md-6 col-sm-12"
      >
        <h4>Proposal Builder</h4>
        <MazInput
          v-model="selectedSystem.pb_note"
          placeholder="Comment"
          autocomplete="new-comment"
          left-icon-name="comment"
          textarea
          :debounce="true"
          @input="updatePBNote"
        />
      </div>
      <div
        v-else
        class="col-md-6 col-sm-12"
      >
        <h4>Proposal Builder</h4>
        <p>{{ selectedSystem.pb_note }}</p>
      </div>
    </div>
    <div
      v-if="!selectedSystem.approved && ($can('edit requested system'))"
      class="text-center"
    >
      <MazBtn
        :loading="requesting"
        @click="requestSystem"
      >
        Request
      </MazBtn>
    </div>
    <div class="pt-3">
      <MazBtn @click="$emit('hide')">
        Hide
      </MazBtn>
    </div>
  </div>
  <div v-else>
    <MazLoader />
    <div class="pt-3">
      <MazBtn

        :icon-name="'restart_alt'"
        fab
        @click="getRequestedSystems()"
      />
    </div>
  </div>
</template>

<script>
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/antd.css'
import axios from 'axios'
import AddersList from './AddersList'
import { Howl } from 'howler'
import SystemEquipment from './SystemEquipment'

export default {
	name: 'RequestedSystem',
	components: { SystemEquipment, AddersList, VueSlider },
	props: {
		financeOptions: Array,
		lead: Object,
		market: String,
		epcAdders: Array,
		epcSystems: Array,
		solarRateArray: Array,
		inverters: Array,
		modules: Array,
		currentUsage: Number,
		onActiveCount: Number
	},
	data () {
		return {
			urgent: false,
			selectedNewAdders: [],
			loading: true,
			activeStep: 1,
			requestedSystems: [],
			proposal: {},
			proposalUploading: false,
			testDocUploading: false,
			createUploading: false,
			files: '',
			something: 'other',
			realPrice: {},
			exists: false,
			requesting: false,
			externalProposalId: null,
			contractAmount: '',
			offsetRangeArray: [0, 100, 150, 200]
		}
	},
	computed: {
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
			if (sunrunids.includes(this.selectedSystem.epc_finance_id)) {
				return true
			} else {
				return false
			}
		},

		YearlyGenerated () {
			let generated = 0
			generated = this.currentUsage * (this.selectedSystem.offset / 100)
			return generated.toLocaleString()
		},
		systemSize () {
			if (this.selectedSystem.modules_id) {
				return (this.modules.find(d => d.value === this.selectedSystem.modules_id).watts * this.selectedSystem.modules_count) / 1000
			}
		},

		formatMonthlyPayment () {
			return this.selectedSystem.monthly_payment.toLocaleString()
		},
		showFinance () {
			if (this.financeOptions) {
				return this.financeOptions.find(d => d.value === this.selectedSystem.epc_finance_id).label
			}
		},
		showSystem () {
			if (this.systemsArray) {
				return this.systemsArray.find(d => d.value === this.selectedSystem.epc_system_id).label
			}
		},

		canSubmit () {
			const system = this.selectedSystem
			if (system) {
				if (system.epc_finance_id && system.inverter_id && system.modules_id && system.system_size &&
                    system.solar_rate && system.offset && system.ppw && this.externalProposalId) {
					return true
				} else {
					return false
				}
			}
		},

		canCreateNew () {
			if (
				(this.lead.status === 'Change Order' && this.requestedSystems.length === 0) &&
                (this.$can('edit proposal') || this.$can('administrate company'))
			) {
				return true
			} else if (this.$can('edit requested system')) {
				const temp = this.requestedSystems.find(request => request.approved === null)
				return !temp
			} else if (this.$can('administrate company')) {
				return true
			}
			return false
		},

		canEdit () {
			if (this.requestedSystems.length > 0) {
				return !this.selectedSystem.approved && this.$can('edit requested system')
			}
		},
		// selectedSystems() {
		//     let array = [];
		//
		//     let epcAdders = this.epcSystems;
		//     $.each(this.selectedNewAdders, function (skey, value) {
		//         $.each(epcAdders, function (okey, oAdder) {
		//             if (oAdder.id === value) {
		//                 array.push(oAdder)
		//             }
		//         })
		//     })
		//     return array;
		//
		// },

		// selectedfinance() {
		//     let array = [];
		//     if (this.selectedNewAdders.length > 0) {
		//         let epcAdders = this.epcAdders;
		//         $.each(this.selectedNewAdders, function (skey, value) {
		//             $.each(epcAdders, function (okey, oAdder) {
		//                 if (oAdder.id === value) {
		//                     array.push(oAdder)
		//                 }
		//             })
		//         })
		//         return array;
		//     } else {
		//         return array;
		//     }
		// },

		selectedAddersCost () {
			const adder = this.selectedAdders
			return _.sumBy(adder, 'cost')
		},
		selectedSystem () {
			this.setSelectedAdders()
			const selected = this.activeStep - 1

			return this.requestedSystems[selected]
		},
		systems () {
			return this.requestedSystems.length
		},
		adders () {
			const x = []
			$.each(this.epcAdders, function (key, value) {
				x.push({ label: value.name, value: value.id })
				x.value = value.id
			})
			return x
		}
	},
	watch: {
		onActiveCount: function (newValue, oldValue) {
			console.log('running in a prop')
		}
	},
	created () {
		Echo.private('lead.' + this.$route.params.leadId)
			.listen('Backend.SalesFlow.Lead.RequestedSystemEvent', (e) => {
				console.log(e)
				this.proposalEvent(e)
			})
	},
	mounted () {
		this.getRequestedSystems()
	},
	methods: {
		changeOrder () {
			this.requesting = true
			const externalId = this.externalProposalId
			const data = {
				external_proposal_id: externalId,
				contract_amount: this.contractAmount
			}
			this.$emit('showProposedSystem')
			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/requested-system/${this.selectedSystem.id}/change-order`, data)
				.then((response) => {
					this.requesting = false
					console.log(response, 'approved')
					const selected = this.activeStep - 1
					this.requestedSystems[selected].approved = true
				}).catch(function (error) {
					this.requesting = false

					console.log('lead error ', error)
				})
			this.requesting = false
		},

		requestSystem () {
			this.requesting = true
			const data = {
				urgent: this.urgent
			}
			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/requested-system/${this.selectedSystem.id}/request`, data)
				.then((response) => {
					const selected = this.activeStep - 1
					this.requestedSystems[selected].approved = true

					this.requesting = false
					console.log(response, 'approved')
				}).catch(function (error) {
					this.requesting = false

					console.log('lead error ', error)
				})
		},

		setSelectedAdders () {
			const selected = this.activeStep - 1

			if (this.requestedSystems[selected]) {
				if (!this.requestedSystems[selected].adders) {
					this.selectedNewAdders = []
				} else {
					// this.selectedNewAdders = [];
					this.selectedNewAdders = this.requestedSystems[selected].adders
				}
			} else {
				this.selectedNewAdders = []
			}
		},

		proposalEvent (data) {
			console.log(data, 'data from compute')
			const change = []
			let i = 0
			$.each(this.requestedSystems, function (skey, value) {
				if (data.data.id === value.id) {
					i++
					const temp = data.data

					console.log('found the data')

					if (temp.adders) {
						const temps = {}
						temps.adders = temp.adders.replace('[', '')
						temps.adders = temps.adders.replace(']', '')
						temps.adders = temps.adders.split(',')
						temp.adders = []
						$.each(temps.adders, function (s, v) {
							console.log(s, v)
							temp.adders.push(Number(v))
						})
					}

					const obj = {
						...value,
						...temp
					}
					change.push(obj)
				} else {
					change.push(value)
				}
			})

			if (i) {
				this.requestedSystems = change
			} else {
				console.log('it was pushed')
				this.requestedSystems.push(data.data)
			}
		},

		checkIfExists (itemId) {
			this.exists = this.requestedSystems.some((item) => {
				return item.id === itemId
			})
		},
		updateRoofWork (event) {
			const payload = {}
			payload.roof_work = event

			this.updateRequestedSystem(payload)
		},

		updateSystemSize (event) {
			const payload = {}
			payload.system_size = event

			this.updateRequestedSystem(payload)
		},
		updateModules (event) {
			console.log(event)
			const payload = {}
			payload.modules_id = this.selectedSystem.modules_id
			payload.system_size = this.systemSize
			this.updateRequestedSystem(payload)
		},
		updateInverter (event) {
			const payload = {}
			payload.inverter_id = event
			this.updateRequestedSystem(payload)
		},
		updateModulesCount (event) {
			const payload = {}
			payload.modules_count = event
			payload.system_size = this.systemSize
			this.updateRequestedSystem(payload)
		},

		updateSalesRepNote (event) {
			const payload = {}
			payload.userNote = true
			payload.sales_rep_note = event

			this.updateRequestedSystem(payload)
		},

		updatePBNote (event) {
			const payload = {}
			payload.userNote = true
			payload.pb_note = event

			this.updateRequestedSystem(payload)
		},

		updateNewAdders (event) {
			const payload = {}
			payload.adders = event

			this.updateRequestedSystem(payload)
			if (!event) {
				this.selectedNewAdders = []
			}
		},

		updateSolarRate (event) {
			const payload = {}
			payload.solar_rate = event

			this.updateRequestedSystem(payload)
		},
		updateMonthlyPayment (event) {
			const payload = {}
			payload.monthly_payment = event
			this.updateRequestedSystem(payload)
		},
		updateOffset (event) {
			const payload = {}
			payload.offset = event
			this.updateRequestedSystem(payload)
		},
		updateSystem (event) {
			const payload = {}
			payload.epc_system_id = event
			this.updateRequestedSystem(payload)
		},
		updateFinance (event) {
			const payload = {}
			payload.epc_finance_id = event
			this.updateRequestedSystem(payload)
		},
		updatePpw (event) {
			const payload = {}
			payload.ppw = event
			this.updateRequestedSystem(payload)
		},

		updateRequestedSystem (payload) {
			axios.put(`/api/salesflow/lead/${this.lead.id}/requested-system/${this.selectedSystem.id}`,
				payload).then((response) => {
				console.log(response)
			})
		},

		getRequestedSystems () {
			axios.get(`/api/salesflow/lead/${this.$route.params.leadId}/requested-system`)
				.then((response) => {
					this.requestedSystems = response.data.data

					this.activeStep = 1

					this.loading = false
				}).catch(function (error) {
					console.log('lead error ', error)
				})
			this.setSelectedAdders()
		},

		createRequestedSystem () {
			this.requesting = true
			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/requested-system`)
				.then((response) => {
					this.requesting = false

					this.requestedSystems.push(response.data.data)
					this.activeStep = this.requestedSystems.length
				}).catch(function (error) {
					this.requesting = false
					console.log('lead error ', error)
				})
		},

		createProposedSystem () {
			this.createUploading = true

			const payload = {
				userId: this.userId
			}

			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/proposed-system`, payload)
				.then((response) => {
					this.createUploading = false

					const selected = this.activeStep - 1
					this.requestedSystems[selected].proposed_system_id
				}).catch(function (error) {
					this.createUploading = false
					console.log('lead error ', error)
				})
		},

		openFile: function (urlPath) {
			window.open(urlPath, '_blank')
		}

	}
}
</script>

<style scoped>

</style>
