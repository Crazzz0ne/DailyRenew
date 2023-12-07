<template>
  <div class="d-flex align-items-end flex-row justify-content-between">
    <button
      class="btn  btn-secondary m-2 p-3"
      :disabled="!firstPageLink"
      @click="getPaginationInformation('first')"
    >
      <i class="fas fa-fast-backward" />
    </button>

    <button
      class="btn  btn-secondary m-2 p-3"
      :disabled="!prevPageLink"
      @click="getPaginationInformation('previous')"
    >
      <i class="fas fa-step-backward" />
    </button>

    <button
      class="btn  btn-secondary m-2 p-3"
      :disabled="!nextPageLink"
      @click="getPaginationInformation('next')"
    >
      <i class="fas fa-step-forward" />
    </button>

    <button
      class="btn  btn-secondary m-2 p-3"
      :disabled="!lastPageLink"
      @click="getPaginationInformation('last')"
    >
      <i class="fas fa-fast-forward" />
    </button>
  </div>
</template>

<script>

export default {
	name: 'PaginationButtons',
	computed: {
		firstPageLink () {
			return this.$store.getters['Notifications/notifications'].first_page_url ? this.$store.getters['Notifications/notifications'].first_page_url : null
		},
		prevPageLink () {
			return this.$store.getters['Notifications/notifications'].prev_page_url ? this.$store.getters['Notifications/notifications'].prev_page_url : null
		},
		nextPageLink () {
			return this.$store.getters['Notifications/notifications'].next_page_url ? this.$store.getters['Notifications/notifications'].next_page_url : null
		},
		lastPageLink () {
			return this.$store.getters['Notifications/notifications'].last_page_url ? this.$store.getters['Notifications/notifications'].last_page_url : null
		}
	},
	methods: {
		async getPaginationInformation (direction) {
			return await this.$store.dispatch('Notifications/getPaginatedNotifications', direction)
				.then((response) => {
					console.log('Pagination Comp Data: ', response.data)
					return this.$emit('updated', this.$store.getters['Notifications/notifications'].data)
				})
		}
	}
}
</script>

<style scoped>
.icon {
    width: 25%;
    height: auto;
}
</style>
