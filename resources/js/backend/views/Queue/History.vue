<template>
  <div class="container">
    <div class="row pt-2">
      <div class="col">
        <MazBtn
          @click="navQueue"
        >
          Back
        </MazBtn>
      </div>
    </div>
    <div class="row pt-2">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h3>Queue History</h3>
          </div>
          <template v-if="!loading">
            <div class="card-body">
              <div class="container py-2" />
              <div class="table-responsive">
                <table class="table table-sm ">
                  <thead>
                    <tr class="d-flex">
                      <th class="col-2">
                        Type
                      </th>
                      <th class="col-2">
                        Time to fill
                      </th>
                      <th class="col-1">
                        Lead ID
                      </th>
                      <th class="col-3">
                        Requesting Rep
                      </th>
                      <th class="col-3">
                        Filled Rep
                      </th>
                      <th class="col-3">
                        Customer Name
                      </th>
                      <th class="col-3">
                        Requested Time
                      </th>
                      <th class="col-3">
                        Filled Time
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="queue in queuesComp"
                      class="d-flex"
                    >
                      <template v-if="queue.office_id === getUser.data.office_id || viewAll">
                        <td class="col-2">
                          {{ queue.type }}
                        </td>
                        <td class="col-2">
                          {{ queue.times.difference }} Min
                        </td>
                        <td
                          class="col-1"
                          @click="openLead(queue.lead.id)"
                        >
                          {{ queue.lead.id }}
                        </td>
                        <td class="col-3">
                          {{ queue.requestingRep.fullName }}
                        </td>
                        <td class="col-3">
                          {{ queue.filledRep.fullName }}
                        </td>
                        <td class="col-3">
                          {{ queue.lead.customer.full_name }}
                        </td>
                        <td class="col-3">
                          {{ $date(queue.times.rTime).format('h:mm a') }}
                        </td>
                        <td class="col-3">
                          {{ $date(queue.times.filledTime).format('h:mm a') }}
                        </td>
                      </template>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="py-2">
              <MazPagination
                v-model="currentPage"
                :page-count="pageCount"
                @page="getHistory(currentPage)"
              />
            </div>
          </template>
          <template v-else>
            <div class="container">
              <MazLoader />
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
	name: 'History',
	data () {
		return {
			currentPage: 1,
			pageCount: 1,
			loading: false,
			queues: null
		}
	},
	created () {
		this.getHistory()
	},
	methods: {
		...mapActions(['fetchUsers']),

		navQueue: function () {
			this.$router.push({ path: '/dashboard/lead/queue/' })
		},
		openLead: function (id) {
			this.$router.push({ path: `/dashboard/lead/${id}` })
		},
		getHistory: function () {
			this.loading = true
			axios.get(`/api/salesflow/line/history?page=${this.currentPage}`)
				.then((response) => {
					this.loading = false
					this.queues = response.data.data
					this.pageCount = response.data.meta.last_page
				})
				.catch(function (error) {
					console.log(error)
				})
		}
	},
	computed: {
		...mapGetters(['getUser']),

		viewAll () {
			if (this.$can('administrate company') ||
                this.$can('integrations') || this.$can('proposal builder')) {
				return true
			} else {
				return false
			}
		},

		queuesComp () {
			if (this.queues) {
				return this.queues.map((b) => {
					switch (b.type) {
					case 'integrations':
						b.name = 'Integrations'
						break
					case 'sp1':
						b.name = 'SP1'
						break
					case 'sp1 panic':
						b.name = 'SP1'
						break
					case 'build_proposal':
						b.name = 'Build Proposal'
						break
					case 'credit_app':
						b.name = 'Create Credit App'
						break
					case 'send_paperwork':
						b.name = 'Send Paperwork'
					default:
						b.name = 'hmm'
						break
					}
					return b
				})
			}
		}
	}
}
</script>

<style scoped>

</style>
