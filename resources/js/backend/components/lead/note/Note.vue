<template>
  <div v-if="loading">
    <div
      id="notes"
      class="card p-sm-0 m-sm-0 mx-md-1 p-md-1 text-center"
    >
      <h4 class="py-2">
        Note
      </h4>
      <Messages
        class="panel-body"
        :is-new-lead="isNewLead"
        :notes="notes"
        :user="user"
      />
      <div class="panel-footer pt-3">
        <div class="row align-content-between px-2">
          <div class="col-12">
            <MazInput
              v-model="message"
              placeholder="Type your message here..."
              left-icon-name="comment"
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
    </div>
  </div>
  <div v-else>
    <div class="text-center">
      <MazLoader />
      <h3>Loading</h3>
    </div>
  </div>
</template>

<script>
import Messages from '../../includes/Messages'

export default {
	name: 'Note',
	components: { Messages },
	props: {
		user: Object,
		leadId: Number,
		isNewLead: Boolean
	},
	data () {
		return {
			id: 0,
			message: '',
			loading: false,
			urgent: false,
			notes: []
		}
	},
	created () {
		this.fetchNotes()

		Echo.private('lead.' + this.leadId)
			.listen('Backend.SalesFlow.Lead.Note.NewNoteEvent', (e) => {
				this.notes.push(e.payload)
			})
		// setInterval(() => {
		//     this.fetchNotes();
		// }, 30500);
	},
	mounted () {
		// Verbose Logging
		// console.log('Notes Mounted!');
		// console.log('Notes: isNewLead', this.isNewLead);
		// console.log('Notes: LeadId', this.leadId);
		// console.log('Notes: UserId', this.user);

	},
	methods: {
		fetchNotes: function () {
			axios.get(`/api/salesflow/lead/${this.leadId}/note/1`)
				.then((response) => {
					this.notes = response.data.data
					this.loading = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		submitNote: function () {
			const data = {
				user_id: this.user.id,
				lead_id: this.leadId,
				note: this.message,
				urgent: this.urgent
			}
			const tempMessage = this.message
			this.message = ''
			axios.post(`/api/salesflow/lead/${this.leadId}/note`, data)
				.then((response) => {
					this.notes.push(response.data.data)
					this.id++
					this.urgent = false
				})
				.catch(function (error) {
					this.message = 'There was an error'
					console.log(error)
				})
		},
		deleteNote: function (noteId) {
			Swal.fire({
				type: 'warning',
				title: 'Delete Note?',
				text: 'Are you sure you want to delete that?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/salesflow/lead/${this.leadId}/note/${noteId}`)
						.then((response) => {
							console.log(response)
							this.fetchNotes()
							this.message = ''
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		},
		storeNote: function (message) {
			if (message !== '') {
				const note = {
					id: this.id,
					note: message
				}

				this.$emit('note-added', note)
				this.notes.push(note)
				this.message = ''
				this.id++
			}
		},
		removeNote: function (noteId) {
			this.$emit('note-deleted', noteId)
			this.notes = _.remove(this.notes, function (a) {
				return a.id !== noteId
			})
		},
		scrollToBottom: function () {
			this.$el.scrollTop = this.$refs.chatbox.lastElementChild.offsetTop
		}

	}
}
</script>

<style scoped>
#notes {
    margin-top: 20px;
}

.bottom-container{
    display: flex;
    flex-direction: row;
    align-items: center;
    width: 100%;
}

</style>
