<template>
  <div v-if="!loading && epcCreditStatus">
    <div class="row justify-content-between">
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazSelect
            v-model="customer.language"
            :placeholder="'Language'"
            :options="langaugeLable"
            @close="updateLanguage(customer.language)"
          />
        </div>
      </div>
      <div
        v-if="$can('edit credit')"
        class="col-md-6 col-sm-12"
      >
        <div class="row  justify-content-between">
          <div class="col-md-6 col-sm-12">
            <template v-if="validCustomerNumber">
              <MazBtn @click="openChat = true">
                Customer Text
              </MazBtn>
            </template>
            <template v-else>
              <p>Enter a valid cell phone</p>
            </template>
            <MazDialog
              v-model="openChat"
              :no-confirm="true"
            >
              <div slot="title">
                <h2 class="text-white">
                  Customer Text
                </h2>
              </div>
              <customer-messages
                :customer-id="customer.id"
                :phone-number="customer.twilio_number"
              />
            </MazDialog>
          </div>
        </div>
      </div>
      <!--First Name -->
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="customer.first_name"
            :placeholder="'First Name'"
            :debounce="true"
            @input="updateCustomerFirstName(customer.first_name)"
          />
        </div>
      </div>
      <!--Last Name -->
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="customer.last_name"
            :placeholder="'Last Name'"
            :debounce="true"
            @input="updateCustomerLastName(customer.last_name)"
          />
        </div>
      </div>
      <!--Spouse-->
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="customer.spouse_name"
            :placeholder="'Spouse'"
            :debounce="true"
            @input="updateSpouse(customer.spouse_name)"
          />
        </div>
      </div>
      <!-- Address-->
      <div class="col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="customer.street_address"
            :placeholder="'Street Address'"
            :debounce="true"
            @input="updateStreetAddress(customer.street_address)"
          />
        </div>
      </div>
      <!--City-->
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="customer.city"
            :placeholder="'City'"

            :debounce="true"
            @input="updateCity(customer.city)"
          />
        </div>
      </div>
      <!--Zip Code-->
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="customer.zip_code"
            :placeholder="'Zip Code'"
            :debounce="true"
            @blur="updateZipcode(customer.zip_code)"
          />
          <MazSelect
            v-model="customer.state"
            class="mt-3"
            :placeholder="'State'"
            :options="states"
            @input="updateState($event)"
          />
        </div>
      </div>
      <!--Cell phone-->
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span
                id="cellPhone"
                class="input-group-text"
              >Cell Phone</span>
            </div>
            <input
              v-model="customer.cell_phone"
              class="form-control"
              aria-label="Default"
              aria-describedby="cellPhone"
              placeholder="Cell Phone"
              type="tel"
              @blur="updateCellPhone(customer.cell_phone)"
            >
              <span
                v-if="!validCustomerNumber"
                class="input-group-text">
                  <input type="checkbox" aria-label="Checkbox for following text input">
              </span>
          </div>
        </div>
      </div>
      <!--home phone-->
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span
                id="HomePhone"
                class="input-group-text"
              >Home Phone</span>
            </div>
            <input
              v-model="customer.home_phone"
              class="form-control"
              aria-label="Default"
              aria-describedby="inputGroup-sizing-default"
              placeholder="Home Phone"

              type="tel"
              @blur="updateHomePhone(customer.home_phone)"
            >
          </div>
        </div>
      </div>
      <!--Email-->
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="customer.email"
            :placeholder="'Email'"
            @blur="updateEmail(customer.email)"
          />
        </div>
      </div>
      <!--Credit Status-->
      <div class="col-md-6 col-sm-12">
        <div
          v-if="$can('edit credit')"
          class="form-group"
        >
          <div class="form-group">
            <MazSelect
              v-model="lead.credit_status_id"
              :placeholder="'Credit Status'"
              :options="epcCreditStatus"
              @input="updateCreditStatus($event)"
            />
            <RequestLineButton
              v-if="lead.credit_status_id === 1"
              :request-needed="requestNeededCreditApp"
              :lead-id="lead.id"
              :type="'credit_app'"
              @queue="queue = $event"
            />
          </div>
        </div>
        <div v-else>
          <h5>Credit Status</h5>
          <p>{{ epcCreditStatus.find(d => d.value === lead.credit_status_id).label }}</p>
          <RequestLineButton
            v-if="lead.credit_status_id === 1"
            :request-needed="requestNeededCreditApp"
            :lead-id="lead.id"
            :type="'credit_app'"
            @queue="queue = $event"
          />
        </div>
        <div v-if="$can('edit credit') && user.office.canSunRun || $can('edit proposal')">
          <template v-if="updateSalesForceURLBtn">
            <MazInput
              v-model="customer.sales_force_url"
              placeholder="Sun Run Opportunity"
              @blur="updateSalesForceURL(customer.sales_force_url)"
            />
            <MazBtn
              :icon-name="'edit'"
              fab
              @click="updateSalesForceURLBtn = false"
            />
          </template>
          <template v-else>
            <MazBtn
              v-if="customer.sales_force_url"
              fab
              @click="goToProposal(customer.sales_force_url)"
            >
              SR
            </MazBtn>
            <MazBtn
              :icon-name="'edit'"
              fab
              @click="updateSalesForceURLBtn = true"
            />
          </template>
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="customer.last_four"
            :placeholder="'Last Four of SSN'"
            @blur="updateLastFour(customer.last_four)"
          />
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <MazPicker
          v-model="customer.dob"
          placeholder="DOB"
          formatted="ll"
          :no-time="true"
          @input="updatedob(customer.dob)"
        />
      </div>
    </div>
    <div
      v-if="leadRoof"
      class="col-sm-12"
    >
      <div class="form-group mt-3">
        <div class="d-flex justify-content-between">
          <label>Under 1 Year</label>
          <h3><b>Roof Age {{ leadRoof.age }}</b></h3>
          <label>30+ Years</label>
        </div>

        <CustomSlider
          v-model="leadRoof.age"
          :value="leadRoof.age"
          :max="30"
          :enabled="canEditRoof"
          @input="saveRoofHistory"
        />
        <MazSelect
          v-model="leadRoof.type"
          :placeholder="'Roof Type'"
          :options="roofTypes"
          @input="saveRoofHistory"
        />
      </div>
    </div>
    <div class="row justify-content-end py-3 pl-1 my-3">
      <div class="col-4">
        <MazBtn
          v-if="!leadRoof"
          @click="addRoofHistory"
        >
          Add Roof Info
        </MazBtn>
        <MazBtn
          v-if="leadRoof"
          @click="removeRoofHistory"
        >
          Remove Roof Info
        </MazBtn>
      </div>
      <div class="col-4">
        <MazBtn
          :loading="creditAuditLoading"
          @click="openCreditAudit"
        >
          Credit History
        </MazBtn>
      </div>
      <div class="col-4">
        <MazBtn
          :loading="cellPhoneAuditLoading"
          @click="openCellPhoneAudit"
        >
          Cell Phone History
        </MazBtn>
      </div>
        <div class="col-4 p5" v-if="user.office_id === 5">
            <MazBtn @click="router().push({path: `/dashboard/geo-code/${customer.id}`})" >Around Here</MazBtn>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
      <MazBtn
        @click="$emit('hide')"
      >
        Hide
      </MazBtn>
    </div>
    <!--Phone Audit-->
    <MazDialog
      v-model="cellPhoneAuditModel"
      fullsize
      title="Cell Phone History"
    >
      <div class="row">
        <div
          v-for="audit in cellPhoneAudits"
          class="col--md-6 col-sm-12"
        >
          <div class="card">
            <div class="card-header">
              {{ $date(audit.time).format('MM/DD/YY h a') }}
            </div>
            <div
              class="card-body"
            >
              <p>Changed by: {{ audit.user }}</p>
              <p>Old number: {{ audit.oldStatus }}</p>
              <p>New number: {{ audit.newStatus }}</p>
            </div>
          </div>
        </div>
      </div>
    </MazDialog>
    <!--        Credit Audit-->
    <MazDialog
      v-model="creditAuditModel"
      fullsize
      title="Credit Pass History"
    >
      <div class="row">
        <div
          v-for="audit in creditAudit"
          class="col--md-6 col-sm-12"
        >
          <div class="card">
            <div class="card-header">
              {{ $date(audit.time).format('MM/DD/YY h a') }}
            </div>
            <div
              class="card-body"
            >
              <p>Changed by: {{ audit.user }}</p>
              <p>Old status: {{ audit.oldStatus }}</p>
              <p>New Status: {{ audit.newStatus }}</p>
            </div>
          </div>
        </div>
      </div>
    </MazDialog>
  </div>
  <div v-else>
    <MazLoader />
  </div>
</template>

<script>
import 'dayjs/locale/es'
import axios from 'axios'
import CustomerMessages from './CustomerMessages'
import CustomSlider from '../CustomSlider'
import RequestLineButton from '../line/RequestLineButton'
import GeoCustomer from "./GeoCustomer.vue";
import router from "../../../routes/routes";

export default {
	name: 'Customer',
	components: {GeoCustomer, CustomSlider, CustomerMessages, RequestLineButton },
	props: {
		user: Object,
		editing: Boolean,
		lead: {
			type: Object,
			required: true,
			default: () => {
			}
		},
		epcCreditStatus: Array,
		validCustomerNumber: Boolean,
		onActiveCount: Number

	},
	data () {
		return {
			openChat: false,
			queue: {},
			cellPhoneAuditModel: false,
			cellPhoneAuditLoading: false,
			creditAuditLoading: false,
			creditAuditModel: false,
			creditAudit: {},
			cellPhoneAudits: {},
			customer: {},
			leadRoof: this.lead.roof,
			roofTypes: [],
			langaugeLable: [
				{ label: 'Select One', value: null },
				{ label: 'English', value: 'english' },
				{ label: 'Spanish', value: 'spanish' }
			],
			loading: true,
			updateSalesForceURLBtn: false,
			states: [
				{
					value: 'AL',
					label: ' Alabama'
				},
				{
					value: 'AK',
					label: ' Alaska'
				},
				{
					value: 'AZ',
					label: ' Arizona'
				},
				{
					value: 'AR',
					label: ' Arkansas'
				},
				{
					value: 'CA',
					label: ' California'
				},
				{
					value: 'CO',
					label: ' Colorado'
				},
				{
					value: 'CT',
					label: ' Connecticut'
				},
				{
					value: 'DE',
					label: ' Delaware'
				},
				{
					value: 'FL',
					label: ' Florida'
				},
				{
					value: 'GA',
					label: ' Georgia'
				},
				{
					value: 'HI',
					label: ' Hawaii'
				},
				{
					value: 'ID',
					label: ' Idaho'
				},
				{
					value: 'IL',
					label: ' Illinois'
				},
				{
					value: 'IN',
					label: ' Indiana'
				},
				{
					value: 'IA',
					label: ' Iowa'
				},
				{
					value: 'KS',
					label: ' Kansas'
				},
				{
					value: 'KY',
					label: ' Kentucky[E]'
				},
				{
					value: 'LA',
					label: ' Louisiana'
				},
				{
					value: 'ME',
					label: ' Maine'
				},
				{
					value: 'MD',
					label: ' Maryland'
				},
				{
					value: 'MA',
					label: ' Massachusetts[E]'
				},
				{
					value: 'MI',
					label: ' Michigan'
				},
				{
					value: 'MN',
					label: ' Minnesota'
				},
				{
					value: 'MS',
					label: ' Mississippi'
				},
				{
					value: 'MO',
					label: ' Missouri'
				},
				{
					value: 'MT',
					label: ' Montana'
				},
				{
					value: 'NE',
					label: ' Nebraska'
				},
				{
					value: 'NV',
					label: ' Nevada'
				},
				{
					value: 'NH',
					label: ' New Hampshire'
				},
				{
					value: 'NJ',
					label: ' New Jersey'
				},
				{
					value: 'NM',
					label: ' New Mexico'
				},
				{
					value: 'NY',
					label: ' New York'
				},
				{
					value: 'NC',
					label: ' North Carolina'
				},
				{
					value: 'ND',
					label: '  North Dakota'
				},
				{
					value: 'OH',
					label: ' Ohio'
				},
				{
					value: 'OK',
					label: ' Oklahoma'
				},
				{
					value: 'OR',
					label: ' Oregon'
				},
				{
					value: 'PA',
					label: ' Pennsylvania[E]'
				},
				{
					value: 'RI',
					label: ' Rhode Island[F]'
				},
				{
					value: 'SC',
					label: ' South Carolina'
				},
				{
					value: 'SD',
					label: ' South Dakota'
				},
				{
					value: 'TN',
					label: ' Tennessee'
				},
				{
					value: 'TX',
					label: ' Texas'
				},
				{
					value: 'UT',
					label: ' Utah'
				},
				{
					value: 'VT',
					label: ' Vermont'
				},
				{
					value: 'VA',
					label: ' Virginia[E]'
				},
				{
					value: 'WA',
					label: ' Washington'
				},
				{
					value: 'WV',
					label: ' West Virginia'
				},
				{
					value: 'WI',
					label: ' Wisconsin'
				},
				{
					value: 'WY',
					label: ' Wyoming'
				}
			]
		}
	},
	computed: {
		requestNeededCreditApp () {
			return this.lead.credit_status_id === 1
		},
		canRequest () {
			if (this.customer.email && (this.customer.home_phone || this.customer.home_phone) &&
                this.customer.first_name && this.customer.last_name) {
				return true
			}
		},
		showCreditRequestButton () {
			return this.lead.credit_status_id === 4
		},
		canEditCredit () {
			return (
				(this.$can('sp1') ||
                        this.$can('sp2') ||
                        this.$can('sales rep')) &&
                    this.lead.epc_id === 1) ||
                (this.$can('proposal builder') || this.$can('administrate company'))
		},
		canEditRoof () {
			return this.$can('create requested system') || this.$can('edit proposal')
		}

	},
	watch: {
		onActiveCount: function (newValue, oldValue) {
			if (newValue !== 1) {
				console.log('running in a prop')
			}
		}
	},

	created () {
		this.getCustomer()
		this.getRoofTypes()

		Echo.private('lead.' + this.$route.params.leadId)
			.listen('Backend.SalesFlow.Lead.CustomerEvent', (e) => {
				this.customerEvent(e)
			})

		if (this.showCreditRequestButton) {
			this.getQueue()
		}
	},
	methods: {
        router() {
            return router
        },
		goToProposal (path) {
			window.open(path)
		},
		openCreditAudit () {
			this.creditAuditLoading = true
			axios.get(`/api/payrolls/audits/customer/${this.lead.id}/credit`)
				.then((response) => {
					this.creditAuditModel = true
					this.creditAuditLoading = false
					this.loading = false
					this.creditAudit = response.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		openCellPhoneAudit () {
			this.cellPhoneAuditLoading = true
			console.log('im running')
			axios.get(`/api/payrolls/audits/customer/${this.customer.id}/cell-phone`)
				.then((response) => {
					this.cellPhoneAuditModel = true
					this.cellPhoneAuditLoading = false
					this.loading = false
					this.cellPhoneAudits = response.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		customerEvent (data) {
			if (data.data.id === this.customer.id) {
				this.customer = _.merge(this.customer, _.pick(data.data, _.keys(this.customer)))
			}
		},
		updateLanguage (event) {
			const payload = {}
			payload.language = event

			this.updateCustomer(payload)
		},
		updatedob (event) {
			const payload = {}
			payload.dob = event

			this.updateCustomer(payload)
		},

		updateCustomerFirstName (event) {
			const payload = {}
			payload.first_name = event
			this.updateCustomer(payload)
		},

		updateCustomerLastName (event) {
			const payload = {}
			payload.last_name = event

			this.updateCustomer(payload)
		},

		updateSpouse (event) {
			const payload = {}
			payload.spouse_name = event

			this.updateCustomer(payload)
		},

		updateStreetAddress (event) {
			const payload = {}
			payload.street_address = event

			this.updateCustomer(payload)
		},
		updateCity (event) {
			const payload = {}
			payload.city = event

			this.updateCustomer(payload)
		},
		updateZipcode (event) {
			const payload = {}
			payload.zip_code = event

			this.updateCustomer(payload)
		},
		updateState (event) {
			const payload = {}
			payload.state = event
			this.updateCustomer(payload)
		},
		updateCellPhone (event) {
			const phone = event.replace(/[^0-9]/g, '')

			const payload = {
				cell_phone: phone

			}

			this.updateCustomer(payload)
		},

		updateHomePhone (event) {
			const phone = event.replace(/[^0-9]/g, '')

			const payload = {
				home_phone: phone

			}

			this.updateCustomer(payload)
		},
		updateCreditStatus (event) {
			const payload = {}
			payload.creditReview = true
			payload.credit_status_id = event
			this.$emit('update-credit', payload)
		},
		updateEmail (event) {
			const payload = {}
			payload.email = event

			this.updateCustomer(payload)
		},
		updateSalesForceURL (event) {
			const payload = {}
			payload.sales_force_url = event

			this.updateCustomer(payload)
		},
		updateLastFour (event) {
			const payload = {}
			payload.last_four = event

			this.updateCustomer(payload)
		},

		updateCustomer (payload) {
			axios.put(`/api/salesflow/customer/${this.customer.id}`, payload)
				.then((response) => {

				}).catch((e) => {
					console.log(e, 'customer update error')
				})
			this.$emit('customer', this.customer)
		},

		getCustomer () {
			axios.get(`/api/salesflow/customer/${this.lead.customer_id}`)
				.then((response) => {
					this.customer = response.data.data
					this.$emit('customer', this.customer)
					this.loading = false
				}).catch(function (error) {
					console.log('customer error ', error)
				})
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

		openFile: function (urlPath) {
			window.open(urlPath, '_blank')
		},
		addRoofHistory () {
			if (this.leadRoof === null) {
				this.leadRoof = { age: 1, type: 1 }
			}

			this.saveRoofHistory()
		},
		removeRoofHistory () {
			if (this.leadRoof.id) {
				this.deleteRoofHistory()
			}
			this.leadRoof = null
		},
		saveRoofHistory () {
			const data = this.leadRoof

			axios.post(`/api/salesflow/lead/${this.lead.id}/roof`, data)
				.then((response) => {
					this.leadRoof.age = response.data.age
					this.leadRoof.type = response.data.roof_type_id
					console.log('Roof data response ', response)
				}).catch(function (error) {
					console.log('Roof data error ', error)
				})
		},
		deleteRoofHistory () {
			console.log(this.leadRoof.id)
			axios.post(`/api/salesflow/lead/${this.lead.id}/roof/delete`)
				.then((response) => {
					console.log('Roof data delete response ', response)
				}).catch(function (error) {
					console.log('Roof data delete error ', error)
				})
		},
		getRoofTypes () {
			console.log('Getting Roof Types...')
			axios.get('/api/salesflow/lead/roof/types')
				.then((response) => {
					console.log('Get Roof Types response ', response)
					this.roofTypes = response.data
				}).catch(function (error) {
					console.log('Get Roof Types error ', error)
				})
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

</style>
