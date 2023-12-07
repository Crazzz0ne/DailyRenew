<template>
  <div class="panel-footer pt-3">
    <div class="row align-content-between px-2">
      <div class="col-12">
        <MazInput
          v-model="message"
          left-icon-name="comment"
          placeholder="Type your message here..."
          textarea
          @keyup.enter="submitNote"
        />
      </div>
      <div class="flex justify-content-between bottom-container py-2 px-5">
        <MazCheckbox
          v-model="urgent"
          :color="'danger'"
        >
          Urgent
        </MazCheckbox>
        <MazBtn
          id="btn-chat"
          :disabled="!message"
          class="btn btn-warning btn-sm"
          @click="submitNote"
        >
          Send
        </MazBtn>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'ChatInput',
	data () {
		return {
			user: null,
			message: '',
			urgent: false
		}
	},
	methods: {
		submitNote: function () {
			const data = {
				user_id: this.user.id,
				note: this.message,
				urgent: this.urgent
			}

			this.message = ''
			// TODO: CMS - Make this route work.
			axios.post(`/api/salesflow/leads/${data.user_id}note`, data)
				.then((response) => {
					this.notes.push(response.data.data)
					this.urgent = false
				})
				.catch(function (error) {
					this.message = 'There was an error'
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>

</style>
