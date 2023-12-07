import axios from 'axios'

const state = {
	leads: [
		{ myLeads: null },
		{ officeLeads: null },
		{ companyLeads: null }
	]
}

const getters = {
	getLeadDashboard: state => state.leads
}

const actions = {
	async fetchLeadDashboardMyLeads ({ commit }, input) {
		const response = await axios.get(`/api/salesflow/lead?type=${input.type}&id=${input.id}`)
			.then(function (response) {
				commit('SET_MY_LEADS', response.data.data)
			})
			.catch(function (error) {
				console.log(error)
			})
	},
	async fetchLeadDashboardOfficeLeads ({ commit }, input) {
		const response = await axios.get(`/api/salesflow/lead?type=${input.type}&id=${input.id}`)
			.then(function (response) {
				commit('SET_OFFICE_LEADS', response.data.data)
			})
			.catch(function (error) {
				console.log(error)
			})
	},
	async fetchLeadDashboardCompanyLeads ({ commit }, input) {
		const response = await axios.get(`/api/salesflow/lead?type=${input.type}&id=${input.id}`)
			.then(function (response) {
				commit('SET_COMPANY_LEADS', response.data.data)
			})
			.catch(function (error) {
				console.log(error)
			})
	}
}

const mutations = {
	// TODO: Why do we have to use [0]
	SET_MY_LEADS: (state, data) => (state.leads[0].myLeads = data),
	SET_OFFICE_LEADS: (state, data) => (state.leads[1].officeLeads = data),
	SET_COMPANY_LEADS: (state, data) => (state.leads[2].companyLeads = data)
}

export default {
	state,
	getters,
	actions,
	mutations
}
