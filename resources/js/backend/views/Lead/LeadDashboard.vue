<template>
  <div class="container-fluid">
    <div class="row justify-content-center py-4 text-center">
      <div class="col py-4">
        <MazBtn
          @click="openReports()"
        >
          Reports
        </MazBtn>
        <MazBtn
          :color="'danger'"
          @click="openPowerRanking()"
        >
          Power Ranking
        </MazBtn>
      </div>
      <div
        v-if="$can('view commission')"
        class="col py-4"
      >
        <!--        <MazBtn-->
        <!--          @click="openCommissions()"-->
        <!--        >-->
        <!--          Commissions-->
        <!--        </MazBtn>-->
      </div>

      <div
        class="col py-4"
      >
        <MazBtn
          @click="openClosed()"
        >
          Closed
        </MazBtn>
      </div>
    </div>

    <div
      class="card"
    >
      <!--{{leads}}-->
      <div class="card-header">
        <div class="py-3">
          <MazSelect
            v-if="statusLoaded"
            v-model="selectedStatus"
            :options="statusOptions"
            :size="'sm'"
            multiple
            clearable
            search
            search-placeholder="Search in status"
            color="info"
            @input="getProjects()"
          >
            <i
              slot="icon-left"
              class="material-icons"
            >
              add_circle_outline
            </i>
          </MazSelect>
          <div
            class="card-body my-3"
            style="background-color: whitesmoke"
          >
            <filters
              :credit-pass="creditPass"
              :low-usage="lowUsage"
              :passed-integrations="passedIntegrations"
              :jij="jij"
              :sat="sat"
              :region-id="14"
              @change="getProjects()"
              @selectedRegionId="selectedRegionId = $event"
              @callCenter="callCenter = $event"
              @inHouse="inHouse = $event"
              @creditPass="creditPass = $event"
              @passedIntegrations="passedIntegrations = $event"
              @lowUsage="lowUsage = $event"
              @jij="jij = $event"
              @close="closeDeal = $event"
              @user="selectedUser = $event"
              @office="selectedOffice = $event"
              @sat="sat = $event"
              @appointment="appointment = $event"
              @projectManager="projectManager = $event"
              @appointmentSet="appointmentSet = $event"
              @stale="stale = $event"
              @permitApproved="permitApproved = $event"
            />
          </div>
        </div>
        <div class="row">
          <div class="col">
            <input
              v-model="tableData.search"
              class="input form-control"
              type="text"
              placeholder="Search Table"
              @input="debounceInput"
            >
          </div>
          <div class="col">
            <div
              v-if="$can('create lead')"
              class="row align-content-between"
            >
              <div class="col mb-2-sm">
                <router-link
                  :to="{ name:'LeadCreate' }"
                  data-toggle="tooltip"
                  title=""
                  class="btn btn-third ml-1"
                >
                  <span class="pr-1">New Lead</span>
                  <i class="fas fa-plus-circle" />
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
      <DataTable
        :columns="columns"
        :sort-key="sortKey"
      >
        <tbody>
          <tr
            v-for="lead in leads"
            :key="lead.id"
            style="cursor: pointer;"
          >
            <td
              class="text-black-100"
              @click="openLead(lead.id)"
            >
              {{ lead.id }}<br>
              <template v-if="recordingUploaded(lead.uploads)">
                🎤
              </template>
              <template v-if="billUploaded(lead.uploads)">
                🧾
              </template>
              <template v-if="appointmentBooked(lead.appointments)">
                📅
                <p>{{ $date(appointmentBooked(lead.appointments).start_time).format('MM/DD/YY') }}</p>
              </template>
            </td>
            <td @click="openLead(lead.id)">
              {{ $date(lead.created_at).format('MM/DD/YY h a') }}
            </td>
            <td
              class="text-capitalize"
              @click="openLead(lead.id)"
            >
              {{ lead.customer.full_name }}
            </td>
            <td
              class="text-capitalize"
              @click="openLead(lead.id)"
            >
              {{ lead.customer.city }}
            </td>
              <td
                  class="text-capitalize"
                  @click="openLead(lead.id)"
              >
                  {{ lead.customer.phone }}
              </td>
              <td
                  class="text-capitalize"
                  @click="openLead(lead.id)"
              >
                  {{ lead.customer.email }}
              </td>
            <td
              v-show="seeOffice"
              class="text-capitalize"
              @click="openLead(lead.id)"
            >
              <p
                v-if="lead.origin_office_name !== lead.office.name"
              >
                {{ lead.origin_office_name }}
              </p>
              <p>{{ lead.office.name }}</p>
            </td>
            <td
              class="text-capitalize"
              @click="openLead(lead.id)"
            >
              {{ lead.status }}
              {{ creditColor(lead.credit_status_id) }}
            </td>


            <!--                <td @click="openLead(lead.id)" class="text-capitalize"> {{ epcs.find(d => d.id === lead.epc_id).name }}</td>-->
            <td @click="openLead(lead.id)">
              {{ lead.canvasser }}
            </td>
            <td
              class="text-capitalize"
              @click="openLead(lead.id)"
            >
              {{ lead.sp1 }}
            </td>
            <td
              class="text-capitalize"
              @click="openLead(lead.id)"
            >
              {{ lead.sp2 }}
            </td>
            <td
              v-show="$can('administrate company')"
              @click="deleteLead(lead.id)"
            >
              <i
                class="fas fa-trash-alt"
              />
            </td>
          </tr>
        </tbody>
      </DataTable>
      <div class="card-footer">
        <div class="row">
          <div class="col-sm-12">
            <MazPagination
              v-model="currentPage"
              :page-count="pageCount"
              @page="getProjects()"
            />
          </div>
          <div class="col">
            <label class="label">Per Search</label>
            <select
              v-model="tableData.length"
              class="form-control form-control-sm w-25-md w-50-sm float-right"
              @change="getProjects()"
            >
              <option
                v-for="(records, index) in perPage"
                :key="index"
                :value="records"
              >
                {{ records }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import DataTable from '../../components/dataTable/DataTable'
import Pagination from '../../components/dataTable/Pagination'
import Filters from '../../components/dataTable/filters'
import axios from 'axios'

// import Pagination from './Pagination.vue';
export default {
	name: 'LeadDashboard',
	components: { Filters, Pagination, DataTable },
	data () {
		return {
			stale: false,
			appointment: false,
			pageRange: 1,
			selectedRegionId: null,
			goBackToggle: false,
			statusLoaded: false,
			selectedStatus: [],
			statusOptions: null,
			selectedUser: null,
			epcs: [],
			leads: [],
			sortKey: 'date',
			perPage: ['10', '25'],
			tableData: {
				draw: 0,
				length: 10,
				search: '',
				column: 0,
				dir: 'desc'
			},
			pagination: {
				lastPage: '',
				currentPage: '',
				total: '',
				lastPageUrl: '',
				nextPageUrl: '',
				prevPageUrl: '',
				from: '',
				to: ''
			},
			pageCount: 1,
			currentPage: 1,
			timeout: null,
			disabled: false,
			openFilters: false,
			creditPass: false,
			lowUsage: false,
			passedIntegrations: false,
			jij: false,
			sat: false,
			selectedOffice: null,
			closeDeal: false,
			callCenter: false,
			inHouse: false,
			projectManager: false,
			appointmentSet: false,
            permitApproved: false,
			columns: [{ label: 'Id', name: 'id' },
				{ label: 'Date', name: 'created_at' },
				{ label: 'Name', name: 'name' },
				{ label: 'City', name: 'city' },
                { label: 'Phone', name: 'phone' },
                { label: 'Email', name: 'email' },
				{ label: 'Office Name', name: 'office' },
				{ label: 'Stage', name: 'stage' },
				{ label: 'Opener', name: 'canvasser' },
				{ label: 'Sp1', name: 'sp1' },
				{ label: 'Sp2', name: 'sp2' }
			]

		}
	},
	watch: {
		selectedRegionId ($old, $new) {
			this.getProjects()
		},
		selectedOffice () {
			this.getProjects()
		},
		selectedUser () {
			console.log('finding user')
			this.getProjects()
		}

	},
	// components: { datatable: Datatable, pagination: Pagination },
	created () {
		this.getStatus()
		this.fetchEpc()
		// this.fetchUsers().then(() => {
		//     // this.getProjects()
		//     // if (this.$can('sp1')) {
		//     //     this.checkGoBackStatus();
		//     // }
		//
		// })
		this.pollData()
		this.getProjects()
	},
	computed: {
		...mapGetters([
			'getUser'
		]),
		seeOffice () {
			return true
		}

		// sortOrders() {
		//     // if (this.sortKey) {
		//     //     let key = this.sortKey;
		//     //     this.sortOrders[key] = this.sortOrders[key] * -1;
		//     //     let sortOrders = [];
		//     //     this.columns.forEach((column) => {
		//     //         sortOrders[column.name] = -1;
		//     //     });
		//     // }
		//     let key = this.sortKey;
		//
		//     this.sortOrders[key] = this.sortOrders[key] * -1;
		//     let sortOrders = [];
		//     this.columns.forEach((column) => {
		//         sortOrders[column.name] = -1;
		//     });
		//     return sortOrders;
		// }

	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),

		creditColor (credit) {
			switch (credit) {
			case 2:
				return '🟢'
			case 9:
				return '🟡'
			case 7:
				return '🔴'
			case 10:
				return '🔵'
			}
		},

		recordingUploaded (lead) {
			let bills = null
			bills = lead.find((a) => {
				if (a.type === 'call center recording') {
					return true
				} else {
					return false
				}
			})
			return bills
		},
		appointmentBooked (lead) {
			let bills = null
			return bills = lead.find((a) => {
				if (a.type_id === 6) {
					return true
				} else {
					return false
				}
			})
		},

		billUploaded (lead) {
			let bills = null
			bills = lead.find((a) => {
				if (a.type === 'bill') {
					return true
				} else {
					return false
				}
			})
			return bills
		},

		pollData () {
			this.polling = setInterval(() => {
				this.getProjects()
			}, 60000)
		},

		checkGoBackStatus () {
			axios.post('/api/round-robin/go-back-toggle')
				.then((response) => {
					this.goBackToggle = response.data.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		getStatus () {
			axios.get('/api/salesflow/lead/status')
				.then((response) => {
					const options = response.data.data
					const payload = []
					$.each(options, function (key, value) {
						const obj = {
							label: value.name,
							value: value.id
						}
						payload.push(obj)
					})
					this.statusOptions = payload
					this.statusLoaded = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		deleteLead (ids) {
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
					axios.delete(`/api/salesflow/lead/${ids}`)
						.then((response) => {
							this.getProjects()
							console.log(response)
							Swal.fire(
								'Deleted!',
								'The Lead has been deleted.',
								'success'
							)
						}).catch((e) => {
							console.log(e, 'customer update error')
						})
				}
			})
		},

		fetchEpc () {
			axios.get('/api/epc/')
				.then((response) => {
					this.epcs = response.data.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		debounceInput: _.debounce(function (e) {
			this.getProjects()
		}, 400),

		search () {
			if (this.disabled === false) {
				this.getProjects()
			}
			this.disabled = true

			// Re-enable after 2 seconds
			this.timeout = setTimeout(() => {
				this.disabled = false
			}, 100)
		},
		getProjects (url = `/api/salesflow/lead-dashboard?page=${this.currentPage}`) {
			this.tableData.draw++
            console.log(this.selectedStatus)
			axios.post(url, {


                    	stale: this.stale,
                    	status: this.selectedStatus ?? [],
                    	draw: this.tableData.draw,
                    	length: this.tableData.length,
                    	search: this.tableData.search,
                    	column: this.tableData.column,
                    	dir: this.tableData.dir,

                    	// 'datatable': this.tableData,
                    	userId: this.getUser.id,
                    	officeId: this.selectedOffice ?? 0,
                    	jij: this.jij,
                    	sat: this.sat,
                    	creditPass: this.creditPass,
                    	passedIntegrations: this.passedIntegrations,
                    	lowUsage: this.lowUsage,
                    	selectedUser: this.selectedUser,
                    	appointment: this.appointment,
                    permitApproved: this.permitApproved,
                    	closed: this.closeDeal,
                    	callCenter: this.callCenter,
                    	inHouse: this.inHouse,
                    	regionId: this.selectedRegionId,
                    	projectManager: this.projectManager,
                    	appointmentSet: this.appointmentSet


			}).then((response) => {
				this.openFilters = false
				const data = response.data
				this.currentPage = response.data.meta.current_page
				this.pageCount = response.data.meta.last_page
				this.pageRange = response.data.meta.last_page
				this.leads = data.data
				this.position()
				this.configPagination(response.data.meta)
			})
				.catch((errors) => {
					console.log(errors)
				})
		},
		configPagination (data) {
			this.pagination.lastPage = data.last_page
			this.pagination.currentPage = data.current_page
			this.pagination.total = data.total
			this.pagination.lastPageUrl = data.last_page_url
			this.pagination.nextPageUrl = data.next_page_url
			this.pagination.prevPageUrl = data.prev_page_url
			this.pagination.from = data.from
			this.pagination.to = data.to
		},
		sortBy (key) {
			this.tableData.column = this.getIndex(this.columns, 'name', key)
			this.tableData.dir = 'asc'
			this.getProjects()
		},
		getIndex (array, key, value) {
			return array.findIndex(i => i[key] == value)
		},
		openLead: function (id) {
			const routeData = this.$router.resolve({ path: `/dashboard/lead/${id}` })
			window.open(routeData.href, '_blank')
		},
		openClosed () {
			this.$router.push({ path: '/dashboard/lead/closed' })
		},
		openCommissions () {
			this.$router.push({ path: '/dashboard/commission' })
		},
		openPowerRanking () {
			this.$router.push({ path: '/dashboard/report/power-rank' })
		},
		openReports () {
			this.$router.push({ path: '/dashboard/report' })
		},
		position: function () {
			this.leads.forEach((b) => {
				if (b.reps.length) {
					let canvasser = null
					canvasser = b.reps.filter(it => {
						return it.position_id === 1
					})
					if (canvasser.length) {
						b.canvasser = `${canvasser[0].first_name} ${canvasser[0].last_name}`
					}

					let sp1 = null
					sp1 = b.reps.filter(it => {
						return it.position_id === 2
					})
					if (sp1.length) {
						b.sp1 = `${sp1[0].first_name} ${sp1[0].last_name}`
					}

					let sp2 = null
					sp2 = b.reps.filter(it => {
						return it.position_id === 3
					})
					if (sp2.length) {
						b.sp2 = `${sp2[0].first_name} ${sp2[0].last_name}`
					}

					let integration = null
					integration = b.reps.filter(it => {
						return it.position_id === 4
					})
					if (integration.length) {
						b.integration = `${integration[0].first_name} ${integration[0].last_name}`
					}

					let salesRep = null
					salesRep = b.reps.filter(it => {
						return it.position_id === 5
					})
					if (salesRep.length) {
						b.salesRep = `${salesRep[0].first_name} ${salesRep[0].last_name}`
					}
				}
			})
		}
	}
}
</script>
