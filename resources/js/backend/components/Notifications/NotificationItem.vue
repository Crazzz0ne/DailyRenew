<template>
  <div>
    <template>
      <div
        class="notification-banner"
        @click="goToNotification"
      >
        <img
          v-if="getImage"
          class="notification-image"
          :src="getImage"
          alt="Image Error"
          width="50"
          height="50"
        >
        <div class="notification-text py-2 px-3">
          {{ getBody }}
        </div>
      </div>
    </template>
    <div class="notification-time">
      {{ format_date(notification.created_at || notification.time) }}
    </div>
    <div class="notification-button-container">
      <notification-settings
        :notification="loadedNotification"
      />
    </div>
  </div>
</template>

<script>
import NotificationSettings from './NotificationSettings'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)

export default {
	name: 'NotificationItem',
	components: { NotificationSettings },
	props: {
		loadedNotification: {
			type: Object,
			id: 0,
			url: '#',
			img: {},
			body: 'Body Text Content Placeholder',
			time: 'Time Posted',
			read_at: null,
			default: () => {}
		}
	},
	data: function () {
		return {
			notification: this.loadedNotification
		}
	},
	computed: {
		UrlPath () {
			if (this.notification.url) {
				return _.replace(this.notification.url, `${process.env.MIX_APP_URL}`, '')
			} else {
				return _.replace(this.notification.data.url, `${process.env.MIX_APP_URL}`, '')
			}
		},
		getImage () {
			if (!this.notification) { return null }
			let path = this.notification.img
			if (this.notification.data) { path = this.notification.data.img }
			if (!path) { path = '/img/backend/brand/scout124x124.png' }
			return path
		},
		getBody () {
			if (!this.notification) { return null }
			let message = this.notification.message || this.notification.body
			if (this.notification.data) { message = this.notification.data.body }
			if (!message) { message = 'Unable to load preview. Please click here to see more information.' }
			return message
		}
	},
	methods: {
		async goToNotification () {
			await this.markNotificationAsRead()
			window.location.href = this.UrlPath
			this.$emit('close')
		},
		markNotificationAsRead: async function () {
			await this.$store.dispatch('Notifications/markNotificationAsRead', this.notification.id)
		},
		format_date (value) {
			if (value) {
				return dayjs(value).fromNow()
			}
		}
	}
}
</script>

<style scoped>
.notification-banner {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    padding: 1%;
    border-top: 2px #1e90ff solid;
}

.notification-button-container {
    display: flex;
    align-items: center;
    flex-shrink: inherit;
}

.notification-text {
    font-size: small;
    font-weight: 500;
    text-overflow: ellipsis;
    flex-shrink: inherit;
    color: black;
}

.notification-time {
    font-size: x-small;
    flex-shrink: inherit;
    display: block;
    padding-bottom: 1%;
    padding-left: 2%;
}

a {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    height: fit-content;
}

.notification-image {
    border-radius: 20px;
}

.highlight { background: #96ffff; }
</style>
