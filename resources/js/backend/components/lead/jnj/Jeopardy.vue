<template>
  <span>
    <template v-if="!inJeopardy">
      <MazBtn
        :loading="disableButtons"
        :color="'danger'"
        @click="openDanger = true"
      >
        JIJ
      </MazBtn>
    </template>
    <template v-else>
      <MazBtn
        v-if="true"
        :loading="disableButtons"
        :color="'success'"
        @click="submit"
      >
        Job Saved
      </MazBtn>
    </template>
    <MazDialog
      v-model="openDanger"
      danger
      title="Job in Jeopardy?"
      :no-confirm="!reason"
      @confirm="submit"
      @close="openDanger = false"
    >
      <p>
        This will place the lead into the status below, all reps will be emailed. Please leave detailed notes so
        we can help save this job
      </p>
      <div class="py-3">
        <MazSelect
          v-model="reason"
          :options="jijOptions"
        />
      </div>

      <MazInput
        v-model="note"
        placeholder="Whats wrong?"
        autocomplete="new-comment"
        left-icon-name="comment"
        textarea
      />
    </MazDialog>
  </span>
</template>

<script>
export default {
	name: 'Jeopardy',
	props: {
		userId: Number,
		jeopardy_id: Number,
		leadId: Number
	},
	data () {
		return {
			disableButtons: false,
			reason: null,
			jebToggle: false,
			openDanger: false,
			note: null,
			jijOptions: [
				{ label: 'Select One', value: null },
				{ label: 'Job in Jeopardy', value: 16 },
				{ label: 'No Show', value: 20 },
				{ label: 'Cancelled Appointment', value: 14 },
				{ label: 'Soft No', value: 23 },
				{ label: 'Hard No', value: 17 },
				{ label: 'Cancelled', value: 21 }
			]

		}
	},
	computed: {
		inJeopardy () {
			if (this.jeopardy_id === null) {
				return false
			} else {
				return true
			}
		}

	},
	watch: {

		jeopardy_id: function (newVal, oldVal) {
			if (newVal !== null) {
				console.log('new value', newVal)
				this.jebToggle = false
			} else {
				this.jebToggle = true
			}
		}
	},
	mounted () {
		if (!this.jeopardy_id) {
			this.jebToggle = true
		}
	},

	methods: {
		submit () {
			this.disableButtons = true
			this.openDanger = false
			const data = {
				reason: this.reason,
				userId: this.userId,
				note: this.note
			}

			axios.post(`/api/salesflow/lead/${this.leadId}/jnj`, data)
				.then((response) => {
					console.log(response.data.data)
					this.$emit('lead', response.data.data)
					this.disableButtons = false
					this.jebToggle = !this.jebToggle
				})
				.catch(function (error) {
					console.log(error)
					this.disableButtons = false
					Swal.fire({
						type: 'error',
						title: 'Oops...',
						text: 'Something went wrong!',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Submit an issue?',
						cancelButtonText: 'No Thanks'
					}).then((result) => {
						if (result.value) {
							document.getElementById('submitIssueButton').click()
						}
					})
				})
		}

	}
}
</script>

<style scoped>

</style>
