<template>
  <div>
    <div class="container-root">
      <button
        class="notifications-button justify-content-center"
        @click.stop="toggleVisibility"
      >
        <span class="notifications-button">
          <span class="notifications-number">{{ countNotifications }}</span>
          <i class="fas fa-bell notification-icon fa-lg" />
        </span>
      </button>
      <div
        v-show="visible"
        class="notification-container"
        :class="visible ? 'd-inline-block' : ''"
      >
        <NotificationsList
          v-click-outside="hide"
          :notifications="notifications"
          :owner="owner"
          @close="visible = false"
        >
          <template #footer>
            <PaginationButtons
              v-if="visible"
              @updated="notifications = $event"
            />
          </template>
        </NotificationsList>
      </div>
    </div>
  </div>
</template>

<script>
import NotificationsList from './NotificationList'
import PaginationButtons from './PaginationButtons'

export default {
	name: 'NotificationContainer',
	components: { PaginationButtons, NotificationsList },
	props: {
		owner: {
			type: Object,
			required: true,
			id: 0
		}
	},
	data () {
		return {
			notifications: [],
			visible: false,
			count: 0,
            isTabActive: document.visibilityState === 'visible',
            lastHidden: localStorage.getItem('lastHidden')
		}
	},
	computed: {
		countNotifications () {
			if (this.notifications.length > 0) {
				return this.notifications.filter(function (element) {
					return element.read_at === null
				}).length
			}
			return 0
		}
	},
    created () {
        this.handleVisibilityChange = this.handleVisibilityChange.bind(this)
        document.addEventListener('visibilitychange', this.handleVisibilityChange)
    },
    beforeDestroy () {
        document.removeEventListener('visibilitychange', this.handleVisibilityChange)
    },
	mounted () {
		window.addEventListener('keydown', (e) => {
			if (e.key === 'Escape') {
				this.visible = false
			}
		})

		this.subscribeToUserNotifications()
		this.getUsersNotifications()
	},
	methods: {
        handleVisibilityChange () {
            this.isTabActive = document.visibilityState === 'visible'
            if (!this.isTabActive) {
                this.lastHidden = Date.now()
                localStorage.setItem('lastHidden', this.lastHidden)
            }
        },

		toggleVisibility () {
			this.visible = !this.visible
		},
		hide () {
			this.visible = false
		},
		getUsersNotifications: async function () {
			await this.$store.dispatch('Notifications/getNotifications', this.owner.id)
				.then(() => {
					this.notifications = this.$store.getters['Notifications/notifications'].data
				})
		},
        async subscribeToUserNotifications () {
            await Echo.private('App.Models.Auth.User.' + this.owner.id)
                .notification((data) => {
                    this.$store.dispatch('Notifications/pushNewNotification', data)
                    this.checkIfNotificationIsForThisLead(data)
                    if (this.isTabActive) {
                        // Play sound
                        this.playSound()
                    } else {
                        // Check if this is the oldest hidden tab
                        const oldestHidden = localStorage.getItem('lastHidden')
                        if (this.lastHidden === oldestHidden) {
                            // Play sound
                            this.playSound()
                        }
                    }
                })
        },
		checkIfNotificationIsForThisLead (data) {
			if (data) {
				const id = data.payload.lead_id
				const param = parseInt(this.$route.params.leadId)
				if (id === param) {
					this.$store.dispatch('Notifications/markNotificationAsRead', data.id)
				}
			}
		},
        playSound () {
            const sound = new Howl({
                src: '/sounds/notification.mp3',
                volume: 0.2
            })
            sound.play()
        }
	}
}
</script>

<style scoped>
.container-root {
    width: 100%;
    z-index: 75;
    display: inline-block;
    position: relative;
}

.notification-container {
    margin: 5px;
    padding: 5px;
    position: absolute;
    z-index: 1000;
}

/*Mobile*/
@media only screen and (max-width: 500px) {
    .notification-container {
        right: -60px;
        margin: auto;
        padding: 0;
        width: 80vw;
    }
}

/*Desktop*/
@media only screen and (min-width: 601px) {
    .notification-container {
        right: -100px;
        width: 30vw;
    }
}

.notifications-button {
    background: #00B7FF;
    padding: 5px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 98;
}

.notification-icon {
    color: white;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    right: 30%;
}

.notifications-number {
    background: #00B7FF;
    color: white;
    position: relative;
    bottom: 5px;
    left: 20px;
    padding: 2px;
    border-radius: 50%;
    line-height: 12px;
    text-align: center;
    z-index: 80;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bolder;
}

button:hover {
    background: #ffffff;
    color: #00B7FF;
    box-shadow: 0 12px 16px 0 rgba(0, 0, 0, 0.24), 0 17px 50px 0 rgba(0, 0, 0, 0.19);
    outline: none;
}

button {
    outline: none;
    border: none;
}
</style>
