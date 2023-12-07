<template>
  <div class="d-flex">
    <div
      class="d-flex flex-wrap justify-content-center bg-light border-right border-dark col-3"
    >
      <div class="d-flex justify-content-center w-100">
        <span class="text-info text-center mx-2">{{ message.lead.customer.full_name }}</span>
      </div>

      <div
        v-if="message.user"
        class="d-flex flex-column justify-content-center w-100"
      >
        <MazAvatar
          :size="scaleAvatarToDisplay"
          :src="`${message.user.gravatar}`"
          :style="{margin: 'auto'}"
        />

        <div class="d-flex justify-content-center w-100">
          <span class="text-center mx-2">{{ message.user.name.first }} {{ message.user.name.last }}</span>
        </div>
      </div>
      <MazBtn
        :icon-name="'visibility'"
        :size="'mini'"
        fab
        @click="openLead(message.lead.id)"
      />
    </div>
    <div class="flex-grow-1 p-3">
      <p>{{ message.note }}</p>
      <small class="text-muted text-center d-flex flex-wrap align-text-bottom align-items-end">
        {{ message.created_at }}
      </small>
    </div>
  </div>
</template>

<script>
import dayjs from 'dayjs'
import handleAvatars from '../../../mixins/HandleAvatars'
import handleScreenSize from '../../../mixins/HandleScreenSize'

export default {
	name: 'ChatMessage',
	mixins: [handleAvatars, handleScreenSize],
	props: {
		message: {
			type: Object,
			required: true
		}
	},
	computed: {

	},
	methods: {
		openLead: function (id) {
			const routeData = this.$router.resolve({ path: `/dashboard/lead/${id}` })
			window.open(routeData.href, '_blank')
		},
		getMessageDateTime (date) {
			const result = dayjs(date).fromNow()
			console.log(result)
			if (result === 'NaN years ago') {
				console.log('Invalid Date: ', date)
				return date
			}
			return result
		}
	}
}
</script>

<style scoped>
</style>
