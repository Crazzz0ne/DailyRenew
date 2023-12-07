<template>
  <div v-if="!loading && epcPower">
    <div class="row justify-content-between">
      <div class="col-sm-12">
        <div class="form-group">
          <MazSelect
            v-model="lead.integrations_approved"
            :placeholder="'Integrations Status'"
            :options="integrationsStatus"
            :disabled="!$can('edit integrations status')"
            @input="updateIntegrationsStatus($event)"
          />
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazInput
            v-model="utility.name_on_bill"
            :placeholder="'Name on Bill'"
            @blur="updateNameOnBill(utility.name_on_bill)"
          />
          <InputErrorDisplay
            v-if="containsNameError"
            :field-name="'name_on_bill'"
            :loaded-messages="getNameError"
          />
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <MazSelect
            v-model="utility.power_company_id"
            :placeholder="'Power Company'"
            :options="powerCompaniesDropDown"
            :disabled="false"
            @close="updatePowerCompany(utility.power_company_id)"
          />
          <InputErrorDisplay
            v-if="containsPowerCompanyError"
            :field-name="'power_company_id'"
            :loaded-messages="getPowerCompanyError"
          />
        </div>
      </div>

      <div
        v-if="PowerPrograms"
        class="col-md-6 col-sm-12"
      >
        <div class="form-group">
          <MazSelect
            v-model="utility.power_program_id"
            :placeholder="'Rate Plan'"
            :options="PowerPrograms.plan"
            :disabled="false"
            @input="updatePowerProgram($event)"
          />
        </div>
      </div>
      <div
        v-if="PowerPrograms"
        class="col-md-6 col-sm-12"
      >
        <div class="form-group">
          <MazSelect
            v-model="utility.power_discount_id"
            :placeholder="'Discount Plan'"
            :options="PowerPrograms.discount"
            :disabled="false"
            @input="updatePowerDiscount($event)"
          />
        </div>
      </div>
      <div class="col-md-6 col-sm-12">
        <MazInput
          v-model="utility.average_bill"
          class="two-third-width"
          :style="'display: inline-block'"
          :type="'number'"
          :size="'sm'"
          :placeholder="'Average Bill'"
          @blur="updateAvarageBill(utility.average_bill)"
        />
        <MazBtn
          :style="'display: inline-block'"
          fab
          @click="showModel = true"
        >
          <span class="material-icons">
            batch_prediction
          </span>
        </MazBtn>
      </div>
      <div class="col-md-6 col-sm-12">
        <MazInput
          v-model="utility.kw_year_usage"
          class="two-third-width"
          :type="'number'"
          :placeholder="'kWh Yearly Usage'"
          :style="'display: inline-block'"
          @blur="updateYearlyKwh(utility.kw_year_usage)"
        />
        <MazBtn
          :style="'display: inline-block'"
          :size="'sm'"
          fab
          @click="showModel = true"
        >
          <span class="material-icons">
            batch_prediction
          </span>
        </MazBtn>
      </div>
      <UtilityUsage
        :lead-id="lead.id"
        :utility-id="utility.id"
        :show-model="showModel"
        @updateUsage="updateUtility($event)"
        @showModelToggle="showModel = false"
        @updateTotalUsage="utility.kw_year_usage = $event"
        @updateBill="utility.average_bill = $event"
      />
    </div>
    <div class="pt-3">
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
import InputErrorDisplay from '../../Errors/InputErrorDisplay'
import UtilityUsage from './UtilityUsage'

export default {
	name: 'Utility',
	components: { UtilityUsage, InputErrorDisplay },
	props: {
		lead: Object,
		epcPower: Array
	},
	data () {
		return {
			showModel: false,
			logins: {},
			utility: {},
			usage: {
				jan_kwh: null,
				jan_bill: 0,
				feb_kwh: 0,
				feb_bill: 0,
				mar_kwh: 0,
				mar_bill: 0,
				apr_kwh: 0,
				apr_bill: 0,
				may_kwh: 0,
				may_bill: 0,
				jun_kwh: 0,
				jun_bill: 0,
				jul_kwh: 0,
				jul_bill: 0,
				aug_kwh: 0,
				aug_bill: 0,
				oct_kwh: 0,
				oct_bill: 0,
				nov_kwh: 0,
				nov_bill: 0,
				dec_kwh: 0,
				dec_bill: 0
			},
			integrationsStatus: [
				{ label: 'Select One', value: 1 },
				{ label: 'Not Approved', value: 2 },
				{ label: 'Approved', value: 3 },
				{ label: 'Partial Qualified', value: 4 }
			],
			loading: true,
			inputErrors: []
		}
	},
	computed: {
		editing () {
			if (this.$can('sp1') || this.$can('sp2') || this.$can('sales rep')) {
				return true
			}
			return false
		},
		PowerPrograms () {
			if (this.epcPower) {
				const programs = this.epcPower.filter(company => company.id === this.utility.power_company_id)

				return programs[0]
			} else {
				return []
			}
		},
		powerCompaniesDropDown () {
			const array = []

			$.each(this.epcPower, function (key, value) {
				let obj = {}

				obj = {
					label: value.name,
					value: value.id
				}
				array.push(obj)
			})

			return array
		},
		containsNameError () {
			if (this.inputErrors.some(e => e.name_on_bill)) {
				return true
			}
		},
		containsPowerCompanyError () {
			if (this.inputErrors.some(e => e.power_company_id)) {
				return true
			}
		},
		getNameError () {
			return this.inputErrors.find(e => e.name_on_bill)
		},
		getPowerCompanyError () {
			return this.inputErrors.find(e => e.power_company_id)
		}
	},
	created () {
		this.getUtility()

		Echo.private('lead.' + this.$route.params.leadId)
			.listen('Backend.SalesFlow.Lead.UtilityUpdateEvent', (e) => {
				this.updateUtilityEvent(e)
			})

		// if (this.$can('view utility logins')) {
		// 	this.getLogins()
		// }
	},
	methods: {
		updateUsages () {
			const payload = {}
			// if ()
		},
		updateUtilityEvent (data) {
			if (data.data.id === this.utility.id) {
				this.utility = _.merge(this.utility, _.pick(data.data, _.keys(this.utility)))
			}
		},

		updatePowerDiscount (event) {
			const payload = {}
			payload.power_discount_id = event
			this.updateUtility(payload)
		},
		updatePowerProgram (event) {
			const payload = {}
			payload.power_program_id = event
			this.updateUtility(payload)
		},

		updatePowerCompany (event) {
			const payload = {}
			payload.power_company_id = event
			this.updateUtility(payload)
		},

		updateYearlyKwh (event) {
			const payload = {}
			payload.kw_year_usage = event
			this.updateUtility(payload)
		},
		updateNameOnBill (event) {
			const payload = {}
			payload.name_on_bill = event
			this.updateUtility(payload)
		},

		updateAvarageBill (event) {
			const payload = {}
			payload.average_bill = event
			this.updateUtility(payload)
		},

		updateIntegrationsStatus (event) {
			const payload = {}
			payload.usageReview = true
			payload.integrations_approved = event
			this.$emit('update-integrations-status', payload)
		},
		updatePassword (event) {
			const payload = {}
			payload.password = event
			this.updateLogin(payload)
		},

		updateuserName (event) {
			const payload = {}
			payload.user_name = event
			this.updateLogin(payload)
		},

		updateLogin (payload) {
			const comp = this
			axios.put(`/api/salesflow/lead/${this.lead.id}/utility/${this.lead.utility_id}/password/1`, payload)
				.then((response) => {
				}).catch((e) => {
					comp.inputErrors.push(error.response.data.errors)
				})
		},
		updateUtility (payload) {
			console.log('running update')
			const comp = this
			axios.put(`/api/salesflow/lead/${this.lead.id}/utility/${this.lead.utility_id}`, payload)
				.then((response) => {
				}).catch(function (error) {
					console.log(error.response.data.errors)
					comp.inputErrors.push(error.response.data.errors)
					// for(let i = 0; i < error.response.data.errors.length; i++)
					// {
					// }
				})
			this.$emit('utility', this.utility)
		},
		getUtility () {
			axios.get(`/api/salesflow/lead/${this.lead.id}/utility/${this.lead.utility_id}`)
				.then((response) => {
					this.utility = response.data.data
					if (this.utility.length === 0) {
						this.$emit('showMe', true)
					}
					this.$emit('utility', this.utility)
					this.loading = false
				}).catch(function (error) {
					console.log('customer error ', error)
				})
		},
		getLogins () {
			axios.get(`/api/salesflow/lead/${this.lead.id}/utility/${this.lead.utility_id}/password`)
				.then((response) => {
					this.logins = response.data.data
					this.loading = false
				}).catch(function (error) {
					console.log('customer error ', error)
				})
		}

	}
}
</script>

<style scoped>
.two-third-width{
    width: 66%;
}

</style>
