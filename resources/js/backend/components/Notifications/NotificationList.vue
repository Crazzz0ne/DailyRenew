<template>
  <div class="parent h-100 p-1">
    <div class="header">
      <div class="m-1">
        <NotificationListButtons
          :owner="owner"
        />
        <slot name="header" />
      </div>
    </div>
    <div class="notification-list scrollable-list m-2">
      <div
        v-for="(notification) in notifications"
        :key="notification.id"
        class="notification-row"
      >
        <notification-item
          :class="notification.read_at ? '' : 'highlight'"
          :loaded-notification="notification"
          @close="close"
        />
      </div>

      <div
        v-if="notifications.length <= 0"
        class="notification-row"
      >
        <h4>No notifications to display at this time.</h4>
      </div>
    </div>
    <slot
      class="notification-row"
      name="footer"
    />
  </div>
</template>

<script>
import NotificationItem from './NotificationItem'
import NotificationListButtons from './NotificationListButtons'

export default {
	name: 'NotificationList',
	components: { NotificationItem, NotificationListButtons },
	props: {
		notifications: {
			type: Array,
			required: true
		},
		owner: {
			type: Object,
			required: true,
			id: 0
		}
	},
	methods: {
		close () {
			this.$emit('close')
		}
	}
}
</script>

<style scoped>
.parent{
    background: #ffffff;
    border: #1e90ff solid 2px;
    border-radius: 20px;
}

.notification-list {
    z-index: 50;
    display: flex;
    flex-direction: column;
    position: relative;
    border-bottom: #1e90ff solid 2px;
}

.scrollable-list {
    overflow-x: hidden;
    overflow-y: auto;
    height: 500px;
}

.scrollable-list::-webkit-scrollbar {
    width: 1em;
}

.scrollable-list::-webkit-scrollbar-track {
    box-shadow: inset 0 0 6px #1e90ff;
    border-bottom-right-radius: 20px;
    border-top-right-radius: 20px;
}

.scrollable-list::-webkit-scrollbar-thumb {
    background-color: #1e90ff;
    outline: none;
    border-radius: 20px;
}

.header {
    background: #ffffff;
    border-radius: 20px;
}
</style>
