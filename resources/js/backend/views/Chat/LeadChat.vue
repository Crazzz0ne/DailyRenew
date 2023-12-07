<template>
  <div
    v-if="chatList.length"
    class=""
  >
    <div
      v-if="chatList.length"
      class="row rounded-lg overflow-hidden shadow "
    >
      <!-- Users box-->
      <div class="col-5 px-0">
        <div class="bg-white">
          <div class=" px-4 py-2  bg-orange">
            <p class="h5 mb-0 py-1">
              Recent
            </p>
          </div>
          <div class="messages-box">
            <div
              v-if="chatList.length"
              class="list-group rounded-0 w-100"
            >
              <a
                v-for="(lead, key) in chatList"
                :id="lead + '_'+ key"
                ref="leads"
                :key="key"
                class="list-group-item list-group-item-action rounded-0"
                :class="[ key === selected ? activeClass : '' ]"
                @click="selectLead(key)"
              >
                <div
                  class="media d-flex flex-row justify-content-between align-content-center"
                >
                  <div
                    id="chat-list"
                    class="col-4 d-flex flex-grow-1 flex-column"
                  >
                    <img
                      :src="lead.notes[0].user.gravatar"
                      alt="user"
                      width="50"
                      class="rounded-circle mx-auto"
                    >
                    <h6 class="mb-0 text-break text-center">{{ lead.notes[0].user.fullName }}</h6>
                    <small
                      class="small font-weight-bold text-center"
                    >{{ displayTime(lead.notes[0].created_at) }}</small>
                  </div>
                  <div class="media-body ml-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="mb-0 font-weight-bold"><u>{{
                        lead.customer.fullName
                      }}</u></span>
                    </div>
                    <p class="font-italic mb-0 text-small text-center">{{ lead.notes[0].note }}</p>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- Chat Box-->
      <div
        class="col-7 px-0"
      >
        <div class="bg-primary px-4 pb-1 bg-light">
          <p
            class="text-black-50"
          >
            {{ selectedCustomerName }}
            <MazBtn
              v-if="selectedCustomerName"
              fab
              :icon-name="'visibility'"
              size="mini"
              @click="goToLead"
            />
          </p>
        </div>
        <div
          id="chat-box"
          class="px-4 py-5 chat-box bg-white"
        >
          <div
            v-if="chats.length"
            class="w-100"
          >
            <div
              v-for="chat in chats"
              :id="'message - ' + chat.note"
              :key="chat.id"
              ref="chats"
              class="media mb-3 w-50"
              :class="[ chat.self ? 'ml-auto' : 'mr-auto' ]"
            >
              <img
                v-if="!chat.self && chat.user"
                :src="chat.user.gravatar"
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
          <div v-else>
            <div class="text-center">
              <MazLoader />
              <h3>Loading</h3>
            </div>
          </div>
        </div>

        <!-- Typing area -->
        <div

          class="bg-light"
        >
          <div class="input-group">
            <input
              v-model="note"
              type="text"
              placeholder="Type a message"
              aria-describedby="button-addon2"
              class="form-control rounded-0 border-0 py-4 bg-light"
              :disabled="loading"
              @keyup.enter="submitNote"
            >
            <div class="input-group-append">
              <button
                id="button-addon2"
                type="submit"
                class="btn btn-link"
                @click="submitNote"
              >
                <i class="fa fa-paper-plane" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import dayjs from 'dayjs'

export default {
	name: 'LeadChat',
	data () {
		return {
			id: 0,
			loading: true,
			customerItem: 'list-group-item list-group-item-action list-group-item-light rounded-0',
			activeClass: 'active text-white',
			selected: 0,
			leftPanelChats: [],
			count: 0,
			note: ''
		}
	},
	computed: {
		...mapGetters([
			'getUser'
		]),
		chatList () {
			return this.leftPanelChats.sort((a, b) => {
				return b.latestNoteID - a.latestNoteID
			})
		},
		chats () {
			return this.chatList[this.selected].notes.map((b) => {
				if (b.user) {
					b.self = b.user.id === this.getUser.data.id
					const newDate = this.$date(b.created_at).format('MM/DD h:mm a')
					if (newDate !== 'Invalid Date') b.created_at = newDate
				}
				return b
			}).reverse()
		},
		selectedLeadId () {
			if (this.chatList.length) {
				return this.chatList[this.selected].id
			}
			return 0
		},
		selectedCustomerName () {
			if (this.chatList.length) {
				return this.chatList[this.selected].customer.fullName ?? ''
			}
			return ''
		}

	},

	watch: {
		selected ($new, $old) {
			if (this.chatList[$old].id !== this.chatList[$new].id) {
				this.checkForNew(this.chatList[$new].id, this.chatList[$new].latestNoteID)
			}

			this.scrollToEnd()
		}
	},
	mounted () {
		this.getAllNotes()
		this.startPusher()
		this.fetchUsers()
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
		displayTime (time) {
			return this.$date(time).format('MM/DD h:mm a')
		},
		checkForNew (leadId, maxId) {
			const data = {
				maxId: maxId
			}

			axios.post(`/api/notes/lead/${leadId}/latest`, data)
				.then((response) => {
					console.log(response.data)
					if (response.data.data.length) {
						this.newMessages(response.data.data)
					}
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		async startPusher () {
			let id = this.getUser.id

			if (!id) { await this.fetchUsers() }

			id = this.getUser.id

			Echo.private('App.Models.Auth.User.' + id)
				.listen('Backend.SalesFlow.Lead.Note.NewUserLeadChatEvent', (e) => {
					console.log('payload from pusher', e.payload)
					this.newMessages([e.payload])
					this.scrollToEnd()
				})
		},
		goToLead () {
			this.$router.push({ path: `/dashboard/lead/${this.selectedLeadId}` })
		},
		submitNote () {
			if (!this.note) {
				return null
			}
			const tempNote = this.note
			this.note = null
			const data = {
				user_id: this.getUser.id,
				lead_id: this.selectedLeadId,
				note: tempNote,
				urgent: false
			}
			this.loading = true

			let gravatar = null
			if (this.getUser.data.id === 1) {
				gravatar = '/img/backend/avatars/avatar.jpg'
			} else {
				gravatar = this.getUser.data.gravatar
			}
			const newMessage = {
				note: tempNote,
				createdAt: dayjs(),
				id: this.chatList[0].latestNoteID + 1,
				lead_id: this.selectedLeadId,
				user: {
					id: this.getUser.data.id,
					self: true,
					fullName: this.getUser.data.fullName,
					gravatar: gravatar
				}
			}
			// console.log(newMessage, noteIndex)

			console.log(newMessage, 'new message')
			this.newMessages([newMessage])
			this.selectLead(0)
			this.scrollToEnd()

			axios.post(`/api/salesflow/lead/${this.selectedLeadId}/note`, data)
				.then((response) => {
					this.urgent = false
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		newMessages (newChats) {
			const chatIndex = this.leftPanelChats.findIndex(o => o.id === newChats[0].lead_id)
			newChats.forEach((x) => {
				if (this.leftPanelChats[chatIndex]) {
					this.leftPanelChats[chatIndex].notes.unshift(x)
					this.leftPanelChats[chatIndex].latestNoteID = x.id
				} else {
					this.getMessages(newChats[0].lead_id)
				}
			})
		},
		getMessages (id) {
			console.log('getting id', id)
			axios.get(`/api/notes/lead/${id}`)
				.then((response) => {
					console.log('res', response)
					this.leftPanelChats.unshift(response.data.data)
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		selectLead (id) {
			this.selected = id
		},
		scrollToEnd () {
			this.delay(250).then(() => {
				this.$refs.chats[this.$refs.chats.length - 1].scrollIntoView()
			})
		},
		scrollToTop () {
			this.delay(250).then(() => {
				this.$refs.leads[0].scrollIntoView()
			})
		},
		getAllNotes () {
			this.loading = true
			axios.get('/api/notes/leads')
				.then((response) => {
					this.leftPanelChats = response.data.data
					this.loading = false

					this.delay(250).then(() => {
						this.scrollToEnd()
					})
				})
				.catch(function (error) {
					console.log(error)
				})

			return true
		},
		delay (time) {
			return new Promise(resolve => setTimeout(resolve, time))
		}
	}
}
</script>

<style scoped>

body {
    background-color: #74EBD5;
    background-image: linear-gradient(90deg, #74EBD5 0%, #9FACE6 100%);

    min-height: 100vh;
}

::-webkit-scrollbar {
    width: 5px;
}

::-webkit-scrollbar-track {
    width: 5px;
    background: #f5f5f5;
}

::-webkit-scrollbar-thumb {
    width: 1em;
    background-color: #ddd;
    outline: 1px solid slategrey;
    border-radius: 1rem;
}

.text-small {
    font-size: 0.9rem;
}

.messages-box,
.chat-box {
    height: 75vh;
    max-width: 100vw;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    overflow-y: scroll;
}

.rounded-lg {
    border-radius: 0.5rem;
}

input::placeholder {
    font-size: 0.9rem;
    color: #999;
}

</style>
