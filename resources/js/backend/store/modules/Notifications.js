import axios from 'axios'
import dayjs from 'dayjs'

const state = {
	paginationObject: {
		data: []
	},
	currentPage: 1,
	displayedPerPage: 15
}

const getters = {
	notifications: state => state.paginationObject,
	currentPage: state => state.currentPage,
	displayedPerPage: state => state.displayedPerPage
}

const actions = {
	async pushNewNotification ({ commit }, data) {
		commit('ADD_NOTIFICATION', data)
	},
	async getNotifications ({ commit, getters }, userId) {
		const data = { per_page: getters.displayedPerPage }

		return await axios.post(`/api/notifications/${userId}/show-all`, data)
			.then((response) => {
				commit('SET_PAGINATION_DATA', response.data)
				return response.data
			}).catch(e => {
				console.log('Get Notifications Error: ', e)
			})
	},
	async getPaginatedNotifications ({ commit, getters }, direction) {
		let url = null
		switch (direction) {
		case 'first':
			url = getters.notifications.first_page_url
			break
		case 'previous':
			url = getters.notifications.prev_page_url
			break
		case 'next':
			url = getters.notifications.next_page_url
			break
		case 'last':
			url = getters.notifications.last_page_url
			break
		default:
			console.log('INVALID DIRECTION')
			break
		}

		const data = { per_page: getters.displayedPerPage }

		return await axios.post(url, data).then(response => {
			commit('SET_PAGINATION_DATA', response.data)
			return response.data
		}).catch((error) => {
			console.log('Paginate Notifications Error: ', error)
		})
	},
	async markNotificationAsRead ({ commit }, notificationId) {
		return await axios.post(`/api/notifications/${notificationId}/read`, {
			notification: notificationId
		}).then(response => {
			commit('MARK_SINGLE_NOTIFICATION_AS_READ', notificationId)
			return response
		}).catch(e => {
			console.log('Mark One Notification As Read Error: ', e)
		})
	},
	async markAllNotificationsAsRead ({ commit }, userId) {
		return await axios.post(`/api/notifications/${userId}/read-all`)
			.then((response) => {
				commit('MARK_ALL_NOTIFICATIONS_AS_READ', response.data)
				return response
			}).catch(e => {
				console.log('Mark All Notifications As Read Error: ', e)
			})
	},
	async markNotificationAsUnread ({ commit }, notificationId) {
		return await axios.post(`/api/notifications/${notificationId}/unread`, {
			notification: notificationId
		}).then(response => {
			commit('MARK_SINGLE_NOTIFICATION_AS_UNREAD', notificationId)
			return response
		}).catch(e => {
			console.log('Mark One Notification As Unread Error: ', e)
		})
	},
	async markAllNotificationsAsUnread ({ commit }, userId) {
		return await axios.post(`/api/notifications/${userId}/unread-all`)
			.then((response) => {
				commit('MARK_ALL_NOTIFICATIONS_AS_UNREAD', response.data)
				return response
			}).catch(e => {
				console.log('Mark All Notifications As Unread Error: ', e)
			})
	},
	async deleteNotification ({ commit }, notificationId) {
		return await axios.delete(`/api/notifications/${notificationId}/delete`)
			.then((response) => {
				if (response) {
					commit('REMOVE_NOTIFICATION', notificationId)
				}
				return response
			}).catch(e => {
				console.log('Delete Singular Notification Error: ', e)
			})
	},
	async deleteAllNotification ({ commit }, userId) {
		return await axios.delete(`/api/notifications/${userId}/delete-all`)
			.then((response) => {
				commit('CLEAR_PAGINATION_DATA')
				return response
			}).catch(e => {
				console.log('Delete All Notifications Error: ', e)
			})
	}
}

const mutations = {
	SET_PAGINATION_DATA (state, payload) {
		state.paginationObject = payload
	},
	CLEAR_PAGINATION_DATA (state) {
		state.paginationObject.data.splice(0)
		state.paginationObject.first_page_url = null
		state.paginationObject.prev_page_url = null
		state.paginationObject.next_page_url = null
		state.paginationObject.last_page_url = null
	},
	ADD_NOTIFICATION (state, payload) {
		state.paginationObject.data.unshift(payload)
	},
	REMOVE_NOTIFICATION (state, id) {
		const index = state.paginationObject.data.map(element => { return element.id }).indexOf(id)
		state.paginationObject.data.splice(index, 1)
	},
	MARK_ALL_NOTIFICATIONS_AS_READ (state) {
		state.paginationObject.data.forEach(notification => {
			notification.read_at = dayjs()
		})
	},
	MARK_SINGLE_NOTIFICATION_AS_READ (state, index) {
		state.paginationObject.data.find(x => x.id === index).read_at = dayjs()
	},
	MARK_ALL_NOTIFICATIONS_AS_UNREAD (state) {
		state.paginationObject.data.forEach(notification => {
			notification.read_at = null
		})
	},
	MARK_SINGLE_NOTIFICATION_AS_UNREAD (state, index) {
		state.paginationObject.data.find(x => x.id === index).read_at = null
	}
}

export default {
	namespaced: true,
	state,
	getters,
	actions,
	mutations
}
