<template>
  <ul
    ref="list"
    class="chat"
  >
    <li
      v-for="msg in chat"
      ref="list"
      class="left clearfix"
    >
      <div class="chat-body clearfix">
        <div class="header p-3">
          <p>{{ msg.note }}</p>
        </div>
      </div>

      <div
        class="flex-row justify-content-between pr-4 align-items-end"
        style="display: flex; align-items: end"
      >
        <span
          v-if="msg.user"
          class="chat-img float-left pl-3"
        >
          <MazAvatar
            :src="`${msg.user.gravatar}`"
            :size="avatarSize"
            :style="{margin: 'auto'}"
          />
          <div>{{ msg.user.name.first }} {{ msg.user.name.last }}</div>
        </span>

        <span class="text-muted timestamp">
          <small>{{ msg.created_at }}</small>
        </span>
      </div>
    </li>
  </ul>
</template>

<script>
export default {
	name: 'Messages',
	props: {
		notes: Array,
		isNewLead: Boolean,
		user: Object
	},
	data: function () {
		return {
			avatarSize: 100
		}
	},
	computed: {
		chat () {
			return this.notes.map((b) => {
				if (b.user) {
					if (b.user.id === 1) {
						b.user.gravatar = '/img/backend/avatars/avatar.jpg'
						b.user.name.first = 'Scout'
						b.user.name.last = ''
					}
					b.created_at = this.$date(b.created_at).format('MM/DD h:mm a')
				}

				return b
			})
		}
	},
	mounted () {
		this.scrollToEnd()
	},
	created () {
		this.scaleAvatarToDisplay()
		window.addEventListener('resize', this.scaleAvatarToDisplay)
	},
	destroyed () {
		window.removeEventListener('resize', this.scaleAvatarToDisplay)
	},
	updated () {
		// whenever data changes and the component re-renders, this is called.
		this.$nextTick(() => this.scrollToEnd())
	},
	methods: {
		scrollToEnd: function () {
			if (this.$el.lastElementChild != null) {
				this.$el.scrollTop = this.$el.lastElementChild.offsetTop
				// scroll to the start of the last message
				// this.$refs.list.scrollTop = this.$refs.list.lastElementChild.offsetTop;
			}
		},
		scaleAvatarToDisplay (e) {
			if (window.screen.width > 600) {
				this.avatarSize = 90
			} else if (window.screen.width < 380) {
				this.avatarSize = 60
			} else {
				this.avatarSize = 60
			}
		}
	}

}
</script>

<style scoped>
.chat {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 600px;
}

.chat li {
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px dotted #B3A9A9;
}

.chat li .chat-body p {
    margin: 0;
    color: #777777;
}

.panel .slidedown .glyphicon, .chat .glyphicon {
    margin-right: 5px;
}

.panel-body {
    overflow-y: scroll;
    height: 600px;
}

::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    background-color: #F5F5F5;
}

::-webkit-scrollbar {
    width: 12px;
    background-color: #F5F5F5;
}

::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
    background-color: #555;
}

</style>
