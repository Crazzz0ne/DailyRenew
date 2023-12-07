<template>
  <div class="list">
    <button
      class="btn btn-sm btn-primary"
      @click="markNotificationAsRead"
    >
      Mark Read
    </button>
    <button
      class="btn btn-sm btn-primary"
      @click="markNotificationAsUnRead"
    >
      Mark Unread
    </button>
    <button
      class="btn btn-sm btn-danger"
      @click="deleteNotification"
    >
      Delete
    </button>
  </div>
</template>

<script>
export default {
	name: 'NotificationSettings',
	props: {
		notification: {
			type: Object,
			required: true
		}
	},
	data: function () {
		return {
			visible: false
		}
	},
	methods: {
		toggle () {
			this.visible = !this.visible
		},
		markNotificationAsRead: async function () {
			await this.$store.dispatch('Notifications/markNotificationAsRead', this.notification.id)
		},
		markNotificationAsUnRead: async function () {
			await this.$store.dispatch('Notifications/markNotificationAsUnread', this.notification.id)
		},
		deleteNotification: async function () {
			await this.$store.dispatch('Notifications/deleteNotification', this.notification.id)
		}
	}
}
</script>

<style scoped>
.list {
    width: 100%;
    z-index: 2;
    display: flex;
    flex-direction: row;
    justify-content: center;
    padding: 1%;
}

button{
    font-weight: bolder;
    margin: 10px;
}
</style>
