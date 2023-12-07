<template>
  <div class="list">
    <button
      class="btn btn-primary p-3"
      @click="markAllNotificationsAsRead"
    >
      <i class="fas fa-envelope" />
    </button>

    <button
      class="btn btn-primary p-3"
      @click="markAllNotificationsAsUnRead"
    >
      <i class="fas fa-envelope-open" />
    </button>

    <button
      class="btn btn-danger p-3"
      @click="deleteAllNotifications"
    >
      <i class="fas fa-trash" />
    </button>
  </div>
</template>

<script>
export default {
	name: 'NotificationListButtons',
	props: {
		owner: {
			type: Object,
			required: true,
			id: 0
		}
	},
	methods: {
		markAllNotificationsAsRead: async function () {
			await this.$store.dispatch('Notifications/markAllNotificationsAsRead', this.owner.id)
		},
		markAllNotificationsAsUnRead: async function () {
			await this.$store.dispatch('Notifications/markAllNotificationsAsUnread', this.owner.id)
		},
		deleteAllNotifications: async function () {
			await Swal.fire({
				title: 'Delete ALL Notifications?',
				text: 'Are you sure?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete them all!'
			}).then((result) => {
				if (result.value) {
					return this.$store.dispatch('Notifications/deleteAllNotification', this.owner.id)
				}
			})
		}
	}
}
</script>

<style scoped>
.dropdown-content {
    width: 100%;
    right: 50%;
    margin: 1%;
    z-index: 101;
}

.list {
    padding: 1%;
    width: 100%;
    display: flex;
    justify-content: space-between;
}

.list tr td {
    color: whitesmoke;
    display: inline-block;
    width: 100%;
}
</style>
