import axios from 'axios'

const state = {
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
		editing: false,
		epc: 'sun run',
		login: {
			user_name: ' ',
			password: ' '
		},
		customer: {
			id: null,
			language: 'english',
			full_name: null,
			first_name: '',
			last_name: '',
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
		notes: [],
		disposition: [{
			id: 1,
			status: '',
			reason: ''
		}]

	},
	actions: {
		updateEditing ({ commit }, event) {
			commit('UPDATE_LEAD_EDITING', event)
		}
	}
}

const getters = {
	getLead: state => state.lead
}

const actions = {

	/*
         * Crud methods for single lead plus fetch
         */

	async fetchLead ({ commit }, id) {
		const response = await axios.get(`/api/salesflow/lead/${id}`)
			.then(function (response) {
				commit('SET_LEAD', response.data.data)
			})
			.catch(function (error) {
				console.log(error)
			})
	},

	async createLead ({ commit }, id) {
		const response = await axios.post(`/api/salesflow/lead/${id}`)
			.then(function (response) {
				// Todo: this
				// commit('CREATE_LEAD', response.data.data);
			})
			.catch(function (error) {
				console.log(error)
			})
	},
	async updateLead ({ commit }, id) {
		const response = await axios.put(`/api/salesflow/lead/${id}`)
			.then(function (response) {
				// Todo: this
				// commit('UPDATE_LEAD', response.data.data);
			})
			.catch(function (error) {
				console.log(error)
			})
	},
	async deleteLead ({ commit }, id) {
		const response = await axios.delete(`/api/salesflow/lead/${id}`)
			.then(function (response) {
				// Todo: this
				// commit('DELETE_LEAD', response.data.data);
			})
			.catch(function (error) {
				console.log(error)
			})
	},

	/*
         * Non Crud specific properties
         */

	setUserAddress ({ commit }, place) {
		commit('SET_USER_ADDRESS', place)
	},

	updateCustomerName ({ commit }, event) {
		commit('SET_USER_ADDRESS', event.target.value)
	},
	updateEditing ({ commit }, event) {
		commit('UPDATE_LEAD_EDITING', event.target.value)
	}

}

const mutations = {
	SET_LEAD: (state, data) => (state.lead = data),

	CREATE_LEAD: (state, todo) => state.leads.unshift(todo),
	UPDATE_LEAD: (state, updLead) => {
		const index = state.leads.findIndex(lead => lead.id === updLead.id)
		if (index !== -1) {
			state.leads.splice(index, 1, updLead)
		}
	},
	DELETE_LEAD: (state, id) => {
		(state.leads = state.leads.filter(todo => todo.id !== id))
	},

	SET_USER_ADDRESS: (state, place) => {
		state.lead.customer.address.street_address = place.address_components[0].short_name + ' ' + place.address_components[1].long_name
		state.lead.customer.address.city = place.vicinity
		state.lead.customer.address.state = place.address_components[5].short_name
		state.lead.customer.address.zip = place.address_components[7].long_name

		if (place.address_components[6].types[0] === 'postal_code') {
			state.lead.customer.address.zip = place.address_components[6].long_name
		} else {
			state.lead.customer.address.zip = place.address_components[7].long_name
		}
	},

	UPDATE_CUSTOMER_NAME: (state, value) => {
		const temp = value.split(' ')

		state.lead.customer.full_name = value

		if (temp) { state.lead.customer.first_name = temp[0] }
		state.lead.customer.last_name = temp[1]
	},
	UPDATE_CUSTOMER_STREET_ADDRESS: (state, value) => {
		state.lead.customer.street_address = value
	},
	UPDATE_CUSTOMER_CITY: (state, value) => {
		state.lead.customer.city = value
	},
	UPDATE_CUSTOMER_HOMEPHONE: (state, value) => {
		state.lead.customer.home_phone = value
	},
	UPDATE_CUSTOMER_CELLPHONE: (state, value) => {
		state.lead.customer.cell_phone = value
	},
	UPDATE_CUSTOMER_EMAIL: (state, value) => {
		state.lead.customer.email = value
	},
	UPDATE_COMPANY_COST_PER_WATT: (state, value) => {
		state.lead.company_cost_per_watt = value
	},
	UPDATE_CUSTOMER_COST_PER_WATT: (state, value) => {
		state.lead.customer_cost_per_watt = value
	},
	UPDATE_CUSTOMER_POWER_COMPANY: (state, value) => {
		state.lead.power_company = value
	},
	UPDATE_CUSTOMER_ACCOUNT_NUMBER: (state, value) => {
		state.lead.account_number = value
	},
	UPDATE_LEAD_STATUS: (state, value) => {
		state.lead.status = value
	},
	UPDATE_CUSTOMER_RATE_PLAN: (state, value) => {
		state.lead.rate_plan = value
	},
	UPDATE_CUSTOMER_DISCOUNT_PLAN: (state, value) => {
		state.lead.discount_plan = value
	},
	UPDATE_CUSTOMER_AVERAGE_BILL: (state, value) => {
		state.lead.average_bill = value
	},
	UPDATE_CUSTOMER_USERNAME: (state, value) => {
		state.lead.login.user_name = value
	},
	UPDATE_CUSTOMER_PASSWORD: (state, value) => {
		state.lead.login.password = value
	},
	UPDATE_CUSTOMER_SYSTEM_SIZE: (state, value) => {
		state.lead.system_size = value
	},
	UPDATE_CUSTOMER_UTILITY_BILL: (state, value) => {
		value = value === true ? 1 : 0
		state.lead.utility_bill = value
	},
	UPDATE_CUSTOMER_CPUC_DOC: (state, value) => {
		value = value === true ? 1 : 0
		state.lead.cpuc_doc_signed = value
	},
	UPDATE_CUSTOMER_NEM_SIGNED: (state, value) => {
		value = value === true ? 1 : 0
		state.lead.nem_doc_signed = value
	},
	UPDATE_CUSTOMER_SOLAR_AGREEMENT: (state, value) => {
		value = value === true ? 1 : 0
		state.lead.solar_agreement_signed = value
	},
	UPDATE_CUSTOMER_SYSTEM_BUILT: (state, value) => {
		value = value === true ? 1 : 0
		state.lead.system_built = value
	},
	UPDATE_CUSTOMER_CREDIT_PASS: (state, value) => {
		value = value === true ? 1 : 0
		state.lead.credit_pass = value
	},
	UPDATE_CUSTOMER_CREDIT_STATUS: (state, value) => {
		state.lead.credit_status = value
	},
	UPDATE_LEAD_EDITING: (state, value) => {
		value = !value
		state.lead.editing = value
	}

	// sortLeads: (state, data) => (state.leads = {myLeads: data}),
	// setCurrentLeadId: (state, id) => (state.lead.lead.id = id),
	// setCurrentCustomerId: (state, id) => (state.lead.customer.id = id),
}

export default {
	state,
	getters,
	actions,
	mutations
}
