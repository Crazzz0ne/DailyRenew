<template>
  <div class="row pb-5">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3>Payroll</h3>
        </div>
        <div
          class="card-body"
        >
          <div class="container py-2">
            <user-office-select
              :can-view-regions="true"
              :can-view-offices="true"
              :can-view-users="$can('view office')"
              @officeChange="selectedOffice = $event"
              @userChange="selectedUser = $event"
            />
          </div>
          <div
            v-if="!loading"
            class="table-responsive"
          >
            <table
              v-if="!loading"
              class="table is-bordered data-table"
            >
              <thead>
                <tr>
                  <th>View</th>
                  <th>#</th>
                  <th>Date</th>
                  <th v-if="!selectedUser">
                    Sales Rep
                  </th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="recent in recents"
                  :key="recent.id"
                >
                  <td>
                    <div>
                      {{ recent.id || 'N/AS' }}
                      <MazBtn
                        :icon-name="'visibility'"
                        :size="'mini'"
                        fab
                        @click="openPayload(recent.id)"
                      />
                    </div>
                  </td>
                  <td>
                    <div v-if="recent.id">
                      {{ recent.id }}
                    </div>
                    <div v-else>
                      N/A
                    </div>
                  </td>
                  <td>
                    <div v-if="recent.created">
                      {{ $date(recent.created).format('M/D/YY') }}
                    </div>
                    <div v-else>
                      N/A
                    </div>
                  </td>

                  <td v-if="!selectedUser">
                    <div v-if="recent.created">
                      {{ recent.user.fullName }}
                    </div>
                  </td>
                  <div v-else>
                    N/A
                  </div>

                  <td>${{ recent.amount }}</td>
                </tr>
              </tbody>
            </table>
            <div
              v-if="loading"
              class="d-flex justify-content-center"
            >
              <MazSpinner
                :size="120"
              />
            </div>
          </div>
        </div>
        <div
          class="py-2"
        >
          <MazPagination
            v-model="currentPage"
            :page-count="pageCount"
            :page-range="pageRange"
            @page="getRecent"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import axios from 'axios'
import UserOfficeSelect from '../Selects/UserOfficeSelect'

export default {
	name: 'IndexPayroll',
	components: { UserOfficeSelect },

	data () {
		return {
			pageRange: 3,
			pageCount: 0,
			currentPage: 1,
			recents: [],
			selectedOffice: null,
			selectedUser: null,
			loading: true,
			preview: false,
			previewLoading: false,
			previewAmount: 0
		}
	},
	computed: {
		...mapGetters([
			'getUser'
		])
	},
	watch: {
		selectedOffice () {
			this.getRecent()
		},
		selectedUser () {
			this.getRecent()
		}
	},
	mounted () {
		this.fetchUsers().then(() => {
			if (!this.$can('administrate office') && !this.$can('administrate company')) {
				this.selectedUser = this.getUser.data.id
			}
			this.loading = false
		})
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
		getPreview () {
			this.previewLoading = true
			const data = {
				userId: this.selectedUser,
				officeId: this.selectedOffice
			}
			axios.post('/api/payroll/previews', data).then((response) => {
				this.loading = false
				this.preview = true
				if (response.data.length > 0) { this.recents.unshift(response.data) }
				this.previewAmount = response.data
			})
		},

		openPayload: function (id) {
			this.$router.push({ path: `/dashboard/commission/payroll/${id}` })
		},
		primeOffice () {
			this.selectedOffice = this.getUser.data.office_id
		},
		getRecent () {
			this.recents.splice(0)
			this.loading = true
			let urlss = null
			if ((this.$can('administrate company') || this.$can('administrate office')) &&
                this.selectedUser === null) {
				urlss = `/api/payroll/office/${this.selectedOffice}?page=${this.currentPage}`
			} else {
				urlss = `/api/payroll/rep/${this.selectedUser}?page=${this.currentPage}`
			}
			axios.get(urlss)
				.then((response) => {
					this.recents = response.data.data
					this.currentPage = response.data.meta.current_page
					this.pageCount = response.data.meta.last_page
					this.loading = false
					this.getPreview()
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		userChanged (event) {
			this.selectedUser = event
			this.getRecent()
		},
		officeChanged (event) {
			this.selectedOffice = event
			this.getRecent()
		}
	}
}
</script>

<style scoped>

</style>
