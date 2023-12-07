<template>
  <div class="row pt-5">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <div class="row align-content-between">
            <div class="col">
              <h3>Commissions</h3>
            </div>
            <div
              v-if="$can('administrate company') || $can('administrate payroll')"
              class="col"
            >
              <MazBtn
                :color="'warning'"
                :icon-name="'add'"
                @click="uploadCommission = true"
              >
                Upload Complete Payroll
              </MazBtn>
            </div>
            <div class="col">
              <div
                v-if="$can('edit commission') || $can('administrate company')
                  || $can('administrate payroll')"
                class="float-right"
              >
                <MazBtn
                  :color="'success'"
                  :icon-name="'add'"
                  fab
                  @click="makeNew = true"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="container py-2">
            <user-office-select
              :can-view-regions="true"
              :can-view-offices="true"
              :can-view-users="$can('view office')"
              @officeChange="selectedOffice = $event"
              @userChange="selectedUser = $event"
              @selectedRegionId="selectedRegion = $event"
            />
          </div>
          <div class="table-responsive">
            <table
              v-if="!loading"
              class="table is-bordered data-table"
            >
              <thead>
                <tr>
                  <th>#</th>
                  <th>Type</th>
                  <th>Date</th>
                  <th v-if="!selectedUser">
                    Sales Rep
                  </th>
                  <th>Customer Name</th>
                  <th>Lead ID</th>
                  <th v-if="!selectedOffice">
                    Office
                  </th>
                  <th>Amount</th>
                  <th v-if="canEdit">
                    Delete
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(recent, index) in recents"
                  :key="recent.id"
                >
                  <th scope="row">
                    {{ recent.id }}<br>
                    <MazBtn
                      :icon-name="'visibility'"
                      :size="'mini'"
                      fab
                      @click="openLead(recent.lead_id)"
                    />
                  </th>
                  <td>{{ recent.type }}</td>
                  <td>{{ $date(recent.created_at).format('M/D/YY h:m a') }}</td>
                  <td
                    v-if="!selectedUser && recent.user"
                    class="col-3"
                  >
                    {{ recent.user.fullName }}
                  </td>
                  <td v-else-if="!selectedUser && !recent.user">
                    No User Found
                  </td>
                  <td>{{ recent.customer }}</td>
                  <td>{{ recent.lead_id }}</td>
                  <td v-if="!selectedOffice">
                    <p v-if="recent.office">
                      {{ recent.office.name }}
                    </p>
                    <p v-else>
                      No Office Found
                    </p>
                  </td>
                  <td>
                    <EditableLabel
                      :value="recent.amount"
                      :format="'usd'"
                      :editable="canEdit"
                      @input="updateCommissionAmount(index, $event)"
                    />
                  </td>
                  <td v-if="canEdit">
                    <MazBtn
                      :color="'danger'"
                      :icon-name="'delete'"
                      fab
                      @click="deleteCommission(recent.id)"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="py-2">
          <MazPagination
            v-model="currentPage"
            :page-count="pageCount"
            :page-range="pageRange"
            @page="getRecent()"
          />
        </div>
      </div>
    </div>

    <create-payroll
      v-if="userList"
      :make-new="makeNew"
      :user-list="userList"
      :user="getUser.data"
      @new-commission="recents.unshift($event)"
      @close-model="makeNew = false"
    />

    <UploadCompleteCommissions
      :open="uploadCommission"
      @new-commission="getRecent"
      @close-model="uploadCommission = false"
    />
  </div>
</template>

<script>
import axios from 'axios'
import { mapActions, mapGetters } from 'vuex'
import UserOfficeSelect from '../Selects/UserOfficeSelect'
import CreatePayroll from './CreatePayroll'
import UploadCompleteCommissions from '../../views/Commission/UploadCompleteCommissions'
import EditableLabel from '../EditableLabel'

export default {
	name: 'RecentCommission',
	components: {
		UploadCompleteCommissions,
		CreatePayroll,
		UserOfficeSelect,
		EditableLabel
	},
	data () {
		return {
			pageRange: 3,
			pageCount: 0,
			currentPage: 1,
			recents: null,
			selectedOffice: null,
			selectedUser: null,
			loading: true,
			userList: [],
			makeNew: false,
			uploadCommission: false,
			selectedRegion: null
		}
	},
	computed: {
		...mapGetters([
			'getUser'
		]),
		canEdit () {
			return this.$can('edit commission') || this.$can('administrate company') || this.$can('administrate payroll')
		}
	},
	watch: {
		selectedRegion ($old, $new) {
			this.getRecent()
		},
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
		})
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),

		deleteCommission (id) {
			Swal.fire({
				title: 'Delete?',
				text: 'Are you sure?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/commission/${id}`)
						.then((response) => {
							console.log(response)
							this.getRecent()
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		},
		approve (id, approveds) {
			const data = {
				approved: approveds
			}
			axios.post(`/api/commission/${id}/approve`, data)
				.then((response) => {

				})
				.catch(function (error) {
					console.log(error)
				})
		},
		openLead: function (id) {
			this.$router.push({ path: `/dashboard/lead/${id}` })
		},
		getRecent () {
			this.loading = true
			let urlss = null
			if ((this.$can('administrate company') || this.$can('administrate office')) &&
                this.selectedUser === null) {
				urlss = `/api/commission/transaction/office/${this.selectedOffice}?page=${this.currentPage}`
			} else {
				urlss = `/api/commission/transaction/rep/${this.selectedUser}?page=${this.currentPage}`
			}
			urlss = `/api/commission/transaction?page=${this.currentPage}`

			const data = {
				params: {
					office_id: this.selectedOffice,
					user_id: this.selectedUser,
					market_id: this.selectedRegion
				}
			}
			axios.get(urlss, data)
				.then((response) => {
					this.recents = response.data.data
					this.currentPage = response.data.meta.current_page
					this.pageCount = response.data.meta.last_page
					this.loading = false
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		updateCommissionAmount (index, amount) {
			if (amount < 0) {
				amount = 0
			}

			const currentAmount = this.recents[index].amount
			this.recents[index].amount = amount

			axios.put(`/api/commission/${this.recents[index].id}`, { amount: amount })
				.then((response) => {
					if (!response.data) {
						this.recents[index].amount = currentAmount
					}
				})
				.catch(function (error) {
					console.log('Commission Update Error: ', error)
				})
		},
		userChanged (event) {
			this.selectedUser = event
			this.getRecent()
		},
		userLists (event) {
			this.userList = event
		},
		officeChanged (event) {
			this.selectedOffice = event
			this.getRecent()
		},
		regionChanged (event) {
			this.selectedRegion = event
			this.getRecent()
		}
	}
}
</script>

<style scoped>

</style>
