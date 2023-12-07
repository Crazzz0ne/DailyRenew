<template>
  <div class="bg-red rounded w-100 font-weight-bold">
    <p
      v-for="(message, index) in messages"
      class="p-2 m-2 text-white"
    >
      {{ message[0] }}
    </p>
  </div>
</template>

<script>
export default {
	name: 'InputErrorDisplay',
	props: {
		fieldName: {
			type: String(),
			require: true
		},
		loadedMessages: {}
	},
	data: function () {
		return {
			messages: this.loadedMessages,
			enabled: false
		}
	}
}
</script>

<style scoped>

</style>
