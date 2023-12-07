<template>
  <div

    class="px-4 py-5 chat-box bg-white"
  >
    <div
      v-for="chat in chats"
      v-if="chats.length"
      id="chat-box"
      class="media  mb-3"
      :class="[ chat.self ? 'ml-auto' : 'mr-auto' ]"
    >
      <img
        v-if="!chat.self"
        :src="chat.user.avatar"
        alt="user"
        width="50"
        class="rounded-circle"
      >

      <div class="media-body ml-3">
        <div
          class="rounded py-2 px-3 mb-2"
          :class="[ chat.self ? 'bg-primary' : 'bg-light' ]"
        >
          <p
            :class="[ chat.self ? 'text-white' : 'text-muted' ]"
            class="text-small mb-0 "
          >
            {{ chat.note }}
          </p>
        </div>
        <p class="small text-muted">
          {{ chat.user.fullName }} - {{ chat.created_at }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'ChatMessages',
	props: ['chats'],
	watch: {
		chats () {
			this.scrollToEnd()
		}
	},
	created () {
		// this.scrollToEnd()
	},
	methods: {
		scrollToEnd: function () {
			console.log(this.$el, 'el')
			if (this.$el.lastElementChild != null) {
				this.$el.scrollTop = this.$el.lastElementChild.offsetTop
				// scroll to the start of the last message
				// this.$refs.list.scrollTop = this.$refs.list.lastElementChild.offsetTop;
			} else {
				const container = this.$el.querySelector('#chat-box')
				console.log(this.$el.children)
				container.scrollTop = container.offsetTop
			}
		}
	}
}
</script>

<style scoped>

</style>
