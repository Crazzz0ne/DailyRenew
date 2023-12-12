<template>
  <div v-if="proposedSystems.length !== 0">
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
      </div>
    </div>
    <div
      v-if="canView"
      class="row"
    >
      <div
        v-if="editing"
        class="col-md-6 col-sm-12"
      >
        <div class="form-group">
          <MazInput
            v-model="selectedSystem.external_proposal_id"
            :placeholder="'External Proposal ID'"
            :disabled="!editing"
            :debounce="true"
            @input="updateExternalProposalId(selectedSystem.external_proposal_id)"
          />
        </div>

        <MazInput
          v-model="selectedSystem.path"
          :placeholder="'URL'"
          :disabled="!editing"
          :debounce="true"
          class="two-third-width"
          :style="'display: inline-block'"
          @input="updateExternalProposalPath(selectedSystem.path)"
        />
        <MazBtn
          v-if="selectedSystem.path"
          :style="'display: inline-block'"
          :icon-name="'login'"

          fab
          @click="goToProposal(selectedSystem.path)"
        />
      </div>
      <div
        v-else
        class="col-md-6 col-sm-12 form-group"
      >
        <h5>Proposal ID</h5>
        <p>
          {{ selectedSystem.external_proposal_id }}
          <MazBtn
            :style="'display: inline-block'"
            :icon-name="'login'"
            fab

            @click="goToProposal(selectedSystem.path)"
          />
        </p>
      </div>
      <div
        v-if="editing"
        class="col-md-6 col-sm-12"
      >
        <!--                <div class="form-group">-->
          <MazSelect
              v-model="selectedSystem.epc"
              :placeholder="'EPC'"
              :required="true"
              :search="true"
              class="two-third-width"
              :style="'display: inline-block'"
              :options="[{
                    label: 'Freedom',
                    value: 'freedom'
                }, {
                    label: 'Bright Planet',
                    value: 'bright planet'
              }]"
              :debounce="true"
              @input="updateEPC($event)"
          />
        <MazSelect
          v-model="selectedSystem.epc_finance_id"
          :placeholder="'Finance'"
          :required="true"
          :search="true"
          class="two-third-width"
          :style="'display: inline-block'"
          :options="financeOptions"
          :debounce="true"
          @input="updateFinance($event)"
        />
        <MazBtn
          v-if="sunlightLoan"
          :style="'display: inline-block'"
          :icon-name="'verified'"
          fab

          @click="goToProposal('https://slfportal.com/orange/#/welcome')"
        />

        <!--                </div>-->
      </div>
      <div
        v-else
        class="col-md-6 col-sm-12"
      >
        <div class="form-group">
            <h5>EPC</h5>
            <span>{{ selectedSystem.epc.toUpperCase() }}    </span>
          <h5>Finance</h5>
          <span>{{ showFinance.label }}    </span>

          <MazBtn
            v-if="sunlightLoan"
            :icon-name="'verified'"
            fab

            @click="goToProposal('https://slfportal.com/orange/#/welcome')"
          />
        </div>
      </div>
      <div
        v-if="editing"
        class="col-md-6 col-sm-12"
      >
        <!--                <div class="form-group">-->
        <!--                    <MazInput-->

        <!--                        :placeholder="'Size'"-->
        <!--                        :debounce="true"-->
        <!--                        @input="updateSystemSize(selectedSystem.system_size)"-->
        <!--                        v-model="selectedSystem.system_size"/>-->
        <!--                </div>-->
        <div class="form-group">
          <h5>Size</h5>
          <span>{{ systemSize }} Kw </span>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput

            v-model="selectedSystem.yearly_production"
            :placeholder="'Yearly Production'"
            :debounce="true"
            @input="updateYearlyProduction(selectedSystem.yearly_production)"
          />
        </div>
        <div v-else-if="selectedSystem.monthly_payment">
          <h5>Yearly Production</h5>
          <p>{{ selectedSystem.yearly_production }}</p>
        </div>
      </div>

      <!--            <div class="col-md-6 col-sm-12"-->
      <!--                 v-else>-->
      <!--                <div class="form-group">-->
      <!--                    <h5>Size</h5>-->
      <!--                    <span>{{ systemSize }} Kw </span>-->
      <!--                </div>-->
      <!--            </div>-->
      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazSelect
            v-model="selectedSystem.solar_rate"
            :search="true"
            :no-label="true"
            :search-placeholder="'Solar Rate'"
            :options="solarRateArray"
            :placeholder="'Solar Rate'"
            :disabled="!editing"
            @input="updateSolarRate($event)"
          />
        </div>
        <div v-else>
          <h5>Solar Rate</h5>
          <p>${{ selectedSystem.solar_rate }}</p>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput

            v-model="selectedSystem.monthly_payment"
            :placeholder="'Monthly Payment'"
            :debounce="true"
            @input="updateMonthlyPayment(selectedSystem.monthly_payment)"
          />
        </div>
        <div v-else-if="selectedSystem.monthly_payment">
          <h5>Monthly Payment</h5>
          <p>${{ formatMonthlyPayment }}</p>
        </div>
      </div>
      <div
        v-if="$can('view ppw') || ($can('proposal builder')
          || $can('administrate company'))"
        class="col-md-6 col-sm-12"
      >
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput
            v-model="selectedSystem.ppw"
            :placeholder="'Price Per Watt'"
            :disabled="!editing"
            :debounce="true"
            @input="updatePpw(selectedSystem.ppw)"
          />
        </div>
        <div v-else>
          <h5>Price Per Watt</h5>
          <p>${{ selectedSystem.ppw }}</p>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput
            v-model="selectedSystem.offset"
            :type="'number'"
            :placeholder="'Offset'"
            :debounce="true"
            @input="updateOffset(selectedSystem.offset)"
          />
        </div>
        <div v-else>
          <h5>Offset</h5>
          {{ selectedSystem.offset }}%
        </div>
      </div>

      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput
            v-model="selectedSystem.roof_work"
            :type="'number'"
            :placeholder="'Roof Work'"
            :debounce="true"
            @input="updateRoofWork(selectedSystem.roof_work)"
          />
        </div>
        <div v-else>
          <h5>Roof Work</h5>
          <span v-if="selectedSystem.roof_work">${{ selectedSystem.roof_work }}</span>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazInput
            v-model="selectedSystem.contract_amount"
            :placeholder="'Contract Amount'"
            :debounce="true"
            @input="updateContractAmount(selectedSystem.contract_amount)"
          />
        </div>
      </div>
      <div class="col-12">
        <SystemEquipment
          :epc-systems="epcSystems"
          :selected-system="selectedSystem"
          :can-edit="editing"
          :modules="modules"
          :inverters="inverters"
          @updateModules="updateModules"
          @updateInverter="updateInverter"
          @updateModulesCount="updateModulesCount"
        />
      </div>
      <div class="col-sm-12">
        <div
          v-if="editing"
          class="form-group"
        >
          <MazSelect
            v-model="selectedNewAdders"
            :placeholder="'Adders'"
            multiple
            :options="adders"
            search
            :size="'sm'"
            clearable
            @input="updateNewAdders($event)"
          >
            <i
              slot="icon-left"
              class="material-icons"
            >
              add_circle_outline
            </i>
          </MazSelect>
        </div>

        <div class="row pt-3">
          <div class="col">
            <AddersList
              :system_size="parseInt(selectedSystem.system_size)"
              :selected-new-adders="selectedNewAdders"
              :roof-work="selectedSystem.roof_work"
              :epc-adders="epcAdders"
            />
          </div>
        </div>
      </div>
      <div
        v-if="$can('view contract amount')"
        class="col-md-6 col-sm-12"
      >
        <div>
          <h5>Contract Amount</h5>
          <p>${{ contractAmount }}</p>
        </div>
      </div>
      <div class="col-12">
        <h5 class="text-center">
          Documents
        </h5>
        <div class="row justify-content-between pt-5">
          <template>
            <div class="col-md-4 col-sm-6 text-center">
              <MazBtn
                @click="showModel = true"
              >
                Savings Break Down
              </MazBtn>
            </div>
          </template>
          <!-- Site Plan -->
          <div
            v-if="shittyObject(selectedSystem.sitePlanDoc)"
            class="col-md-3 col-sm-6"
            @click="openFile(selectedSystem.sitePlanDoc.path)"
          >
            <img
              class="img-thumbnail"
              src="/img/backend/props/pdf.png"
            >
            <p class="text-center">
              Final Design Plan
            </p>
          </div>
          <div
            v-else-if="$can('edit proposal')"
            class="col-md-4 col-sm-6 text-center"
          >
            <proposal-upload
              :type="'Design Plan'"
              :selected-system-id="selectedSystem.id"
              @response="doStuffWithUpload($event)"
            />
          </div>

          <!-- Proposal-->
          <div
            v-if="shittyObject(selectedSystem.proposalDoc)"
            class="col-md-3 col-sm-6"
            @click="openFile(selectedSystem.proposalDoc.path)"
          >
            <img
              class="img-thumbnail"
              src="/img/backend/props/pdf.png"
            >
            <p class="text-center">
              Proposal
            </p>
          </div>
          <div
            v-else-if="$can('edit proposal')"
            class="col-md-4 col-sm-6 text-center"
          >
            <proposal-upload
              :type="'Proposal'"
              :selected-system-id="selectedSystem.id"
              @response="doStuffWithUpload($event)"
            />
          </div>
        </div>
      </div>
    </div>
    <div
      v-else
      class="container pt-4"
    >
      <div>
        <p> Requested time: {{ $date(lineDisplay.times.rTime).format('M/D/YY h:mm a') }}</p>
        <template v-if="lineDisplay.times.filledTime">
          <p> Filled time: {{ $date(lineDisplay.times.filledTime).format('M/D/YY h:mm a') }}</p>
        </template>
      </div>
      <div
        v-if="lineDisplay.urgent"
        class="float-right"
      >
        <span style="color: red"><strong>URGENT </strong></span>
      </div>
      <template v-if="lineDisplay.filledRep">
        <p> {{ lineDisplay.filledRep.fullName }} has taken your proposal request</p>
        <p> We are working on the <strong>best</strong> proposal and will get back to you soon</p>
        <div>
          <MazLoader
            :color="'secondary'"
          />
        </div>
      </template>
      <template v-else>
        <p> Searching for proposal builder</p>
        <MazLoader />
      </template>
    </div>

    <div class="row py-5">
      <div
        v-if="!selectedSystem.pb_design_approved && $can('edit proposal')"
        class="col text-center"
      >
        <h5 class="">
          Submit to Sales Rep
        </h5>
        <MazBtn
          :loading="btnLoading"
          @click="submitToSalesRep"
        >
          Submit
        </MazBtn>
      </div>
      <div
        v-if="($can('edit requested system')) &&
          (!selectedSystem.rep_design_approved && selectedSystem.pb_design_approved ) && lead.status === 'Change Order'"
        class="col text-center"
      >
        <h5>{{ repSendMessage }}</h5>
        <MazBtn
          :loading="btnLoading"
          @click="confirmChangeOrder()"
        >
          Submit
        </MazBtn>
      </div>
      <!--            // TODO: rep approval change-->
      <div
        v-if="($can('edit requested system') &&
          (!selectedSystem.rep_design_approved && selectedSystem.pb_design_approved ) &&
          lead.status === 'Negotiating System' )&& user.office.canSunRun"
        class="col text-center">
        <h5>{{ repSendMessage }}</h5>
        <MazBtn
          :color="'danger'"
          :left-icon-name="'park'"
          :loading="btnLoading"
          :disabled="!checkListFilled"
          @click="confirmRequest()"
        >
          Close
        </MazBtn>

      </div>
      <div
        v-else-if="!user.office.canSunRun && selectedSystem.pb_design_approved "
        class="col-12 text-center"
      >
        <div class="card-body">
          <div class="row">
            <div class="col">
              <RequestLineButton
                :request-needed="true"
                :lead-id="lead.id"
                :type="'sun_run_runner'"
                :type-id="selectedSystem.id"
                :can-revert="true"
                @queue="getProposedSystems"
              />
            </div>
            <div class="col" v-if="lead.status !== 'Change Order'">
              <template v-if="!checkListFilled">
                <p style="color: red">
                  Check list must be filled in before we can Close
                </p>
              </template>
              <MazBtn
                :color="'secondary '"
                :left-icon-name="'done'"
                :loading="btnLoading"
                :disabled="!checkListFilled"
                @click="confirmRequest()"
              >
                Close
              </MazBtn>

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col">
        <h5 class="text-center">
          Check List
        </h5>
        <form class="text-center">
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.firstName"
              class="px-1"
              :disabled="true"
            >
              First Name
            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.lastName"
              class="px-1"
              :disabled="true"
            >
              Last Name
            </MazCheckbox>
          </label>

          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.email"
              class="px-1"
              :disabled="true"
            >
              Email
            </MazCheckbox>

          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.cellPhone"
              class="px-1"
              :disabled="true"
            >
              Cell Phone
            </MazCheckbox>
          </label>

          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.powerCompany"
              class="px-1"
              :disabled="true"
            >
              Power Company

            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.externalProposal"
              class="px-1"
              :disabled="true"
            >
              Proposal ID
            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.offset"
              class="px-1"
              :disabled="true"
            >
              Offset
            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.solarRate"
              class="px-1"
              :disabled="true"
            > Solar Rate
            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.modules"
              class="px-1"
              :disabled="true"
            >
              Module
            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.modulesCount"
              class="px-1"
              :disabled="true"
            >
              Modules Count
            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.inverter"
              class="px-1"
              :disabled="true"
            >
              Inverter

            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="checkList.contractAmount"
              class="px-1"
              :disabled="true"
            >
              Contract Amount

            </MazCheckbox>
          </label>
          <label class="checkbox-inline">
            <MazCheckbox
              v-model="theSiteSurveyQuestion"
              class="px-1"
              :disabled="true"
            >
              Pre Install Questions

            </MazCheckbox>
          </label>
        </form>
      </div>
    </div>
    <SiteSurveyQuestions
      :questions="lead.siteSurveyQuestions"
      @saveInstallQuestions="theSiteSurveyQuestion = $event"
    />

    <div class="pt-3">
      <MazBtn @click="$emit('hide')">
        Hide
      </MazBtn>
    </div>

    <MazDialog
      v-model="submitModal"
      @confirm="readySystem()"
    >
      <div slot="title">
        <h5 class="text-white">
          Welcome Call
        </h5>
      </div>
      <p>Please call the number below to start the welcome call</p>
      <p> <a :href="`tel:${welcomeCallNumber}`">{{ welcomeCallNumber }}</a></p>
    </MazDialog>
    <!--        <MazDialog-->
    <!--            v-model="siteSurveyModal"-->
    <!--        >-->
    <!--            <div slot="title">-->
    <!--                Site Survey-->
    <!--            </div>-->
    <!--            <p>Don't forget to book the site survey!</p>-->
    <!--            <SiteSurveyCompleteModel-->
    <!--                :lead="lead"-->
    <!--            >-->
    <!--            </SiteSurveyCompleteModel>-->
    <!--        </MazDialog>-->
    <MazDialog
      v-model="showModel"
      fullsize
      title="Savings Break Down"
    >
      <SavingsBreakDown
        :apr="showFinance.rate"
        :old-bill-average="utility.average_bill"
        :new-bill="selectedSystem.monthly_payment"
      />

      <div />
    </MazDialog>
  </div>
  <div v-else>
    <MazLoader />
  </div>
</template>

<script>
import 'dayjs/locale/es'

import ProposalUpload from './ProposalUpload'
import AddersList from './AddersList'
import { Howl } from 'howler'
import SystemEquipment from './SystemEquipment'
import SavingsBreakDown from '../savingsBreakdown/SavingsBreakDown'
import RequestLineButton from '../line/RequestLineButton'
import SiteSurveyCompleteModel from '../../Epc/SiteSurveyCompleteModel'
import SiteSurveyQuestions from './SiteSurveyQuestions'

export default {
	name: 'ProposedSystem',
	components: {
		SiteSurveyQuestions,
		RequestLineButton,
		SavingsBreakDown,
		SystemEquipment,
		AddersList,
		ProposalUpload
	},
	props: {
		user: Object,
		userId: Number,
		lead: Object,
		customer: Object,
		utility: Object,
		market: String,
		epcAdders: Array,
		epcSystems: Array,
		financeOptions: Array,
		solarRateArray: Array,
		inverters: Array,
		modules: Array,
		onActiveCount: Number

	},
	data () {
		return {
            othercloseLoading:false,
			siteSurveyModal: false,
			line: [],
			submitModal: false,
			btnLoading: false,
			selectedNewAdders: [],
			loading: true,
			activeStep: 1,
			proposedSystems: [],
			proposal: {},
			proposalUploading: false,
			savingsBreakDownUploading: false,
			createUploading: false,
			files: '',
			something: 'other',
			realPrice: {},
			exists: false,
			showModel: false,
			siteSurveyModal2: false,
			theSiteSurveyQuestion: this.lead.siteSurveyQuestions.questions_confirmed
		}
	},
	computed: {
		welcomeCallNumber () {
			const financeId = this.financeOptions.find(f => f.value === this.selectedSystem.epc_finance_id).finance_id
			if (financeId === 1) {
				return '844-321-2353'
			}
			if (financeId === 2) {
				return '417-217-7555'
			}
			if (financeId === 3) {
				return '800-480-9145'
			}
		},
		sunlightLoan () {
			const sunlightIds = [
				131,
				132,
				133,
				134,
				71,
				135,
				136,
				137,
				112,
				79,
				138,
				139,
				140,
				141,
				142,
				143]
			if (sunlightIds.includes(this.selectedSystem.epc_finance_id)) {
				return true
			} else {
				return false
			}
		},
		showSolarRate () {
			const sunrunids = [
				8,
				9,
				10,
				71,
				79,
				91,
				92,
				93,
				112,
				113

			]
			if (sunrunids.includes(this.selectedSystem.epc_finance_id)) {
				return true
			} else {
				return false
			}
		},
		contractAmount () {
			return this.selectedSystem.contract_amount.toLocaleString('en-US')
		},
		systemSize () {
			if (this.selectedSystem.modules_id) {
				return (this.modules.find(d => d.value === this.selectedSystem.modules_id).watts * this.selectedSystem.modules_count) / 1000
			}
		},
		formatMonthlyPayment () {
			return this.selectedSystem.monthly_payment.toLocaleString()
		},

		lineDisplay () {
			const selected = this.activeStep - 1
			const line = this.line[selected]
			return line
		},

		showFinance () {
			if (this.financeOptions) {
				return this.financeOptions.find(d => d.value === this.selectedSystem.epc_finance_id)
			}
		},
		// showSystem() {
		//     return this.systemsArray.find(d => d.value === this.selectedSystem.epc_system_id).label
		// },

		repSendMessage () {
			if (this.lead.epc_id === 1) {
				if (this.lead.status === 'Change Order') {
					return 'Change Order'
				}

				return 'Closed'
			} else {
				return 'Send Documents to customer for close'
			}
		},
		canView () {
			if (this.$can('edit proposal') || this.selectedSystem.pb_design_approved) {
				return true
			}
		},
		editing () {
			if (this.selectedSystem) {
				if (this.selectedSystem.pb_design_approved && !this.$can('edit proposal')) {
					return false
				} else {
					return true
				}
			}
		},

		selectedAdders () {
			const array = []
			if (this.selectedNewAdders.length > 0) {
				const epcAdders = this.epcAdders
				$.each(this.selectedNewAdders, function (skey, value) {
					$.each(epcAdders, function (okey, oAdder) {
						const temp = oAdder
						if (oAdder.id === value) {
							if (!oAdder.flat_cost && this.selectedSystem) {
								temp.cost = oAdder.cost * this.selectedSystem.system_size
							}
							array.push(temp)
						}
					})
				})
				return array
			} else {
				return array
			}
		},

		selectedAddersCost () {
			const adder = this.selectedAdders
			return _.sumBy(adder, 'cost')
		},
		selectedSystem () {
			this.setSelectedAdders()
			const selected = this.activeStep - 1

			return this.proposedSystems[selected]
		},
		systems () {
			return this.proposedSystems.length
		},
		adders () {
			const x = []
			$.each(this.epcAdders, function (key, value) {
				x.push({ label: value.name, value: value.id })
				x.value = value.id
			})
			return x
		},
		checkListFilled () {
			if (this.selectedSystem.epc_finance_id && this.selectedSystem.modules_count && this.selectedSystem.modules_id &&
            this.selectedSystem.inverter_id && this.selectedSystem.solar_rate && this.selectedSystem.offset &&
               this.selectedSystem.external_proposal_id && this.customer.first_name && this.customer.last_name && this.customer.cell_phone &&
            this.customer.email && this.utility.power_company_id && this.selectedSystem.contract_amount && this.theSiteSurveyQuestion) {
				return true
			}

			return false
		},

		checkList () {
			const checkList = {
				finance: false,
				modulesCount: false,
				modules: false,
				solarRate: false,
				offset: false,
				ppw: false,
				externalProposal: false,
				firstName: false,
				lastName: false,
				cellPhone: false,
				powerCompany: false,
				contractAmount: false
			}
			checkList.finance = !!this.selectedSystem.epc_finance_id
			checkList.modulesCount = !!this.selectedSystem.modules_count
			checkList.modules = !!this.selectedSystem.modules_id
			checkList.inverter = !!this.selectedSystem.inverter_id
			checkList.solarRate = !!this.selectedSystem.solar_rate
			checkList.offset = !!this.selectedSystem.offset
			checkList.ppw = !!this.selectedSystem.ppw
			checkList.externalProposal = !!this.selectedSystem.external_proposal_id
			checkList.firstName = !!this.customer.first_name
			checkList.lastName = !!this.customer.last_name
			checkList.cellPhone = !!this.customer.cell_phone
			checkList.email = !!this.customer.email
			checkList.powerCompany = !!this.utility.power_company_id
			checkList.contractAmount = !!this.selectedSystem.contract_amount
			return checkList
		},
		canSubmit () {
			const system = this.selectedSystem
			if (system) {
				if (system.epc_finance_id && system.modules_count &&
                    system.solar_rate && system.offset && system.ppw && system.external_proposal_id && this.customer.first_name &&
                    this.customer.last_name && this.customer.cell_phone && this.utility.power_company_id) {
					return true
				} else {
					return false
				}
			}
		}
		// systemsArray() {
		//     let x = [];
		//     $.each(this.epcSystems, function (key, value) {
		//
		//         x.push({label: value.name, value: value.id})
		//         x.value = value.id;
		//
		//     })
		//     return x;
		//
		// }
	},
	watch: {
		onActiveCount: function (newValue, oldValue) {
			this.getProposedSystems()
		}
	},
	created () {
		this.getProposedSystems()

		Echo.private('lead.' + this.$route.params.leadId)
			.listen('Backend.SalesFlow.Lead.ProposedSystemEvent', (e) => {
				console.log(e)
				if ('pb_design_approved' in e.data) {
					const shouldSounds = this.proposedSystems.filter((item) => {
						return (item.id === e.data.id)
					})
					console.log(Object.keys(e.data).length, 'sounds lengths')
					if (!shouldSounds.pb_design_approved && Object.keys(e.data).length < 5) {
						console.log('approve sound')
						if (!this.$can('build proposal')) {
							this.playSound('/sounds/queue-filled.mp3')
						}
					}
				}

				this.proposalAddEvent(e)
			})

		Echo.private('Queue')
			.listen('Backend.SalesFlow.Queue.QueuePageEvent', (e) => {
				console.log(e, 'the queue')
				if (this.lead.id === e.payload.lead.id) {
					if (e.payload.type === 'build_proposal') {
						const index = this.line.findIndex((el) => el.id === e.payload.id)
						if (index !== -1) {
							this.line[index] = e.payload

							this.activeStep = this.activeStep + 1
							this.activeStep = this.activeStep - 1
							if (e.payload.filledRep) {
								console.log('queued sound')
								this.playSound('/sounds/queue-filled.mp3')
							}
						} else {
							console.log('it was  -1? and was pushed', index)
							this.line.push(e.payload)
						}
					}
					// else if (e.payload.type === 'build_proposal') {
					//     console.log('its brand new', e)
					//     this.line.push(e.payload)
					// }
				}
			})
	},
	mounted () {

	},

	system_size: {
		officeId: function (newVal, oldVal) {
			this.fetchUser()
		}
	},
	methods: {
        closeDealNoComplete () {
            this.othercloseLoading = true
            axios.post(`/api/salesflow/lead/${this.lead.id}/system`)
                .then(response => {
                    this.lead.status_id = 11
                    this.lead.status = 'Pending Install'
                    this.lead.system_id = response.data.data.id
                })
        },
		goToProposal (path) {
			window.open(path)
		},
		playSound (file_path) {
			const sound = new Howl({
				src: file_path,
				volume: 0.25
			})
			sound.play()
		},
        confirmRequestss(){
            this.btnLoading = true
            this.submitModal = false
            axios.post(`/api/salesflow/lead/${this.lead.id}/proposed-system/${this.selectedSystem.id}/rep-approved-ss`).then((response) => {
                this.btnLoading = false
                this.submitModal = false
                this.siteSurveyModal = true

                this.getProposedSystems()

                if (response.data === 'submitted') {
                    const active = this.activeStep - 1
                    this.proposedSystems[active].pb_design_approved = this.userId
                }
                this.$emit('activeDraw')
            }).catch((response) => {
                this.btnLoading = false
                console.log(response, 'FAILURE!!')
            })
        },
		confirmRequest () {
			if (this.lead.epc_id === 1) {
				this.submitModal = true
			} else {
				this.readySystem()
			}
			// this.readySystem
		},
		updateModules (event) {
			const payload = {}
			payload.modules_id = event
			payload.system_size = this.systemSize
			this.updateProposedSystem(payload)
		},
		updateInverter (event) {
			const payload = {}
			payload.inverter_id = event
			this.updateProposedSystem(payload)
		},
		updateModulesCount (event) {
			const payload = {}
			payload.modules_count = event
			payload.system_size = this.systemSize

			this.updateProposedSystem(payload)
		},

		confirmChangeOrder () {
			this.btnLoading = true
			axios.post(
				`/api/salesflow/lead/${this.lead.id}/proposed-system/${this.selectedSystem.id}/rep-approved-change-order`)
				.then((response) => {
					this.btnLoading = false
					this.getProposedSystems()
					if (response.data === 'submitted') {
						const active = this.activeStep - 1
						this.proposedSystems[active].pb_design_approved = this.userId
					}
				}).catch((response) => {
					this.btnLoading = false
					console.log(response, 'FAILURE!!')
				})
		},

		readySystem () {
			this.btnLoading = true
			this.submitModal = false
			axios.post(`/api/salesflow/lead/${this.lead.id}/proposed-system/${this.selectedSystem.id}/rep-approved`).then((response) => {
				this.btnLoading = false
				this.submitModal = false
				this.siteSurveyModal = true

				this.getProposedSystems()

				if (response.data === 'submitted') {
					const active = this.activeStep - 1
					this.proposedSystems[active].pb_design_approved = this.userId
				}
				this.$emit('activeDraw')
			}).catch((response) => {
				this.btnLoading = false
				console.log(response, 'FAILURE!!')
			})
		},

		submitToSalesRep () {
			this.btnLoading = true
			axios.post(`/api/salesflow/lead/${this.lead.id}/proposed-system/${this.selectedSystem.id}/submit-to-rep`).then((response) => {
				console.log(response, 'response from system request')

				this.btnLoading = false
				const active = this.activeStep - 1
				this.proposedSystems[active].rep_design_approved = true
			}).catch((response) => {
				this.btnLoading = false
				console.log(response, 'FAILURE!!')
			})
		},
		updateExternalProposalPath (event) {
			const payload = {}
			payload.path = event
			this.updateProposedSystem(payload)
		},
		updateExternalProposalId (event) {
			const payload = {}
			payload.external_proposal_id = event
			this.updateProposedSystem(payload)
		},

		updatePpw (event) {
			const payload = {}
			payload.ppw = event
			this.updateProposedSystem(payload)
		},

		updateRoofWork (event) {
			const payload = {}
			payload.roof_work = event
			this.updateProposedSystem(payload)
		},

		setSelectedAdders () {
			const selected = this.activeStep - 1

			if (this.proposedSystems[selected]) {
				if (!this.proposedSystems[selected].adders) {
					this.selectedNewAdders = []
				} else {
					// this.selectedNewAdders = [];
					this.selectedNewAdders = this.proposedSystems[selected].adders
				}
			} else {
				this.selectedNewAdders = []
			}
		},

		proposalAddEvent (data) {
			console.log(data, 'data from event')
			const change = []
			let i = 0
			// updates and deals with weird array/string
			$.each(this.proposedSystems, function (skey, value) {
				if (data.data.id === value.id) {
					i++
					const temp = data.data

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
			// sets the correct update or add new
			if (i) {
				this.proposedSystems = change
			} else {
				console.log('it was pushed', data)
				this.proposedSystems.push(data.data)
			}

			// if (this.lead.proposed_system_id !== null) {
			//     this.activeStep = this.proposedSystems.findIndex(system => system.id === this.lead.proposed_system_id) + 1;
			//
			// } else {
			//     this.activeStep = this.proposedSystems.length;
			// }
			this.$emit('show-me', true)
		},

		checkIfExists (itemId) {
			this.exists = this.proposedSystems.some((item) => {
				return item.id === itemId
			})
		},

		updateSystemSize (event) {
			const payload = {}
			payload.system_size = event

			this.updateProposedSystem(payload)
		},

		updateNewAdders (event) {
			const payload = {}
			payload.adders = event

			this.updateProposedSystem(payload)
			if (!event) {
				this.selectedNewAdders = []
			}
		},

		updateSolarRate (event) {
			const payload = {}
			payload.solar_rate = event

			this.updateProposedSystem(payload)
		},
		updateYearlyProduction (event) {
			const payload = {}
			payload.yearly_production = event

			this.updateProposedSystem(payload)
		},

		updateMonthlyPayment (event) {
			const payload = {}
			payload.monthly_payment = event
			this.updateProposedSystem(payload)
		},

		updateContractAmount (event) {
			const payload = {}
			payload.contract_amount = event
			this.updateProposedSystem(payload)
		},

		updateOffset (event) {
			const payload = {}
			payload.offset = event
			this.updateProposedSystem(payload)
		},

		updateFinance (event) {
			const payload = {}
			payload.epc_finance_id = event
			this.updateProposedSystem(payload)
		},
        updateEPC (event) {
            const payload = {}
            payload.epc = event
            this.updateProposedSystem(payload)
        },

		updateSystem (event) {
			const payload = {}
			payload.epc_system_id = event
			this.updateProposedSystem(payload)
		},

		updateProposedSystem (payload) {
			axios.put(`/api/salesflow/lead/${this.lead.id}/proposed-system/${this.selectedSystem.id}`,
				payload).then((response) => {
				// console.log('its running')
			}).catch((response) => {
				console.log(response, 'FAILURE!!')
			})
		},

		setRealPrice () {

		},

		doStuffWithUpload (response) {
			const u = response.data.data
			this.$emit('upload', u)

			const selected = this.activeStep - 1

			if (u.type === 'proposal') {
				this.proposedSystems[selected].proposalDoc = u
			} else {
				this.proposedSystems[selected].savingsBreakDown = u
			}
		},

		handleFilesUpload (type) {
			this.files = this.$refs.files.files[0]

			this.submitFiles(type)
		},

		submitFiles (type) {
			if (type === 'proposal') {
				this.proposalUploading = true
			} else {
				this.savingsBreakDownUploading = true
			}
			this.uploading = true
			/*
              Initialize the form data
            */
			const formData = new FormData()

			/*
              Iteate over any file sent over appending the files
              to the form data.
            */

			const file = this.files

			formData.append('proposedsystemId', this.selectedSystem.id)
			formData.append('file', file)
			formData.append('type', type)
			formData.append('lead_id', this.lead.id)
			formData.append('user_id', 1)

			console.log(formData, 'formdata')
			/*
              Make the request to the POST /multiple-files URL
            */
			axios.post(`/api/salesflow/lead/${this.lead.id}/proposed-system/upload`,
				formData,
				{
					headers: {
						'Content-Type': 'multipart/form-data'
					}
				}
			).then((response) => {
				let i = 0

				const u = response.data.data
				this.$emit('upload', u)
				i++
				const selected = this.activeStep - 1

				if (type === 'proposal') {
					this.proposedSystems[selected].proposalDoc = response.data.data
				} else {
					this.proposedSystems[selected].savingsBreakDown = response.data.data
				}

				this.$emit('gambit')

				this.uploading = false
				this.alertSuccess = true
			}).catch((response) => {
				this.error = true
				this.uploading = false
				console.log(response, 'FAILURE!!')
			})
		},

		getProposedSystems () {
			axios.get(`/api/salesflow/lead/${this.$route.params.leadId}/proposed-system`)
				.then((response) => {
					if (response.data.proposedSystem.length) {
						this.proposedSystems = response.data.proposedSystem
						this.line = response.data.line
						this.$emit('show-me', true)
					}

					// if (this.proposedSystems.length >= 1) {
					//     this.activeStep = this.proposedSystems.length;
					// } else {
					//     this.activeStep = 1;
					// }
					this.loading = false
				}).catch(function (error) {
					console.log('lead error ', error)
				})
			this.setSelectedAdders()
		},

		createProposedSystems () {
			this.createUploading = true
			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/proposed-system`)
				.then((response) => {
					this.createUploading = false
					this.proposedSystems.push(response.data.data)
				}).catch(function (error) {
					this.createUploading = true
					console.log('lead error ', error)
				})
		},

		createRequestedSystems () {
			this.createUploading = true
			const payload = {
				requestedSystemId: this.selectedSystem.id,
				userId: this.userId
			}

			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/requested-system`, payload)
				.then((response) => {
					this.createUploading = false
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

.custom-file-input::before {
    content: 'Select some files';
    display: inline-block;
    background: blue;
    border: 3px solid #999;
    border-radius: 3px;
    padding: 5px 8px;
    outline: none;

    cursor: pointer;

}

.two-third-width {
    width: 66%;
}

</style>
