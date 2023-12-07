<template>
  <ul
    ref="list"
    class="chat container-sm"
  >
    <li
      v-for="msg in chat"
      :key="msg.id"
      ref="list"
    >
      <ChatMessage :message="msg" />
    </li>
  </ul>
</template>

<script>
import ChatMessage from './ChatMessage'

export default {
	name: 'AllChatComp',
	components: { ChatMessage },
	props: {
		notes: { type: Array, required: true }
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
		}
	}

}
</script>

<style scoped>
.chat {
    list-style: none;
    margin: 0;
    padding: 0;

}

.chat li {
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px dotted #B3A9A9;
}

.panel .slidedown .glyphicon, .chat .glyphicon {
    margin-right: 5px;
}

.panel-body {
    overflow-y: scroll;
    height: 100%;
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

.bottom {
    position: absolute;
    bottom: 0;
    right: 0;
}

</style>
