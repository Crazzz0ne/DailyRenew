<template>
  <div
    v-if="loading && $can('view lead')"
  >
    <div
      id="notes"
      class="card p-sm-0 m-sm-0 mx-md-1 p-md-1"
    >
      <h4 class="py-2 text-center">
        Chat
      </h4>
      <AllChatComp
        :notes="notes"
        class="panel-body"
      />
      <!--      <ChatInput />-->
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
import AllChatComp from '../../components/Chat/AllChatComp'

export default {
	name: 'AllChat',
	components: { AllChatComp },
	data () {
		return {
			id: 0,
			loading: false,
			notes: []
		}
	},
	created () {
		this.fetchNotes()
		this.pollData()

		// Echo.private(`lead.` + this.leadId)
		//     .listen('Backend.SalesFlow.Lead.Note.NewNoteEvent', (e) => {
		//         this.notes.push(e.payload);
		//     })
	},
	mounted () {
		// Verbose Logging
		// console.log('Notes Mounted!');
		// console.log('Notes: isNewLead', this.isNewLead);
		// console.log('Notes: LeadId', this.leadId);
		// console.log('Notes: UserId', this.user);

	},
	methods: {
		pollData () {
			this.polling = setInterval(() => {
				this.fetchNotes()
			}, 60000)
		},
		fetchNotes: function () {
			axios.post('/api/all-notes')
				.then((response) => {
					this.notes = response.data.data
					this.loading = true
				})
				.catch(function (error) {
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
		}

	}
}
</script>

<style scoped>
#notes {
    margin-top: 20px;
}

</style>
