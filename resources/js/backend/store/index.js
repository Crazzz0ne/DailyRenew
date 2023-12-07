import Vuex from 'vuex'
import Vue from 'vue'
import { getField, updateField } from 'vuex-map-fields'

import LeadDashboard from './modules/LeadDashboard'
import User from './modules/UserStore'
import Leads from './modules/LeadStore'
import Notifications from './modules/Notifications'

Vue.use(Vuex)

// Load Vuex
Vue.use(Vuex)

// Create Store
const store = new Vuex.Store({
	strict: true,
	modules: {
		LeadDashboard,
		Leads,
		User,
		Notifications
	},
	getters: {
		getField
	},
	mutations: {
		updateField
	}
})

export default store
