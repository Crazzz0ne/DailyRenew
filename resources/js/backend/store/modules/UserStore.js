import axios from 'axios'

const state = {
	data: {},
	id: null,
	apiToken: window.apiKey
}

const getters = {
	getUser: state => state
}

const actions = {
	async fetchUsers ({ commit }) {
		const response = await axios.get(
			`/api/salesflow/user/api?api_token=${state.apiToken}`
		).then(function (response) {
			commit('SET_USER', response.data.data)
		})
			.catch(function (error) {
				console.log(error)
			})
	}
}

const mutations = {
	SET_USER: (state, user) => {
		state.data = user
		state.id = user.id
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
