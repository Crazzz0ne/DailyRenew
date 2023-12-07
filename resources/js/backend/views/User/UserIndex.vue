<template>
  <div class="mb-5 pb-5">
    <div class="card">
      <!--{{leads}}-->
      <div class="card-header">
        <div class="row">
          <div class="col">
            <MazInput
              v-model="tableData.search"
              placeholder="Search"
              @input="getUsers"
            />
          </div>
          <div class="col">
            <MazSelect
              v-model="position"
              :options="[
                { label: 'None', value: null },
                { label: 'Opener', value: 'canvasser' },
                { label: 'sp1', value: 'sp1' },
                { label: 'sp2', value: 'sp2' }
              ]"
              placeholder="Filter by position"
              @input="getUsers"
            />
          </div>
          <div
            v-if="$can('administrate company')"
            class="col"
          >
            <MazSelect
              v-model="office"
              :options="officeList"
              placeholder="Filter by Office"
              @input="getUsers"
            />
          </div>
          <div
            class="col"
          >
            Terminated
            <MazSwitch
              v-model="terminated"
              :color="'danger'"
              @input="getUsers()"
            />
          </div>
          <div class="col text center">
            <h5> Total: <br>{{ count }}</h5>
          </div>
          <div class="col">
            <MazBtn
              :icon-name="'add_circle_outline'"
              fab
              @click="newUserModel = true"
            />
          </div>
        </div>
      </div>
      <DataTable
        :columns="columns"
        :sort-key="sortKey"
      >
        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
            style="cursor: pointer;"
          >
            <td
              class="text-black-100"
              @click="openUser(user.id)"
            >
              {{ user.fullName }}
            </td>
            <td
              class="text-black-100"
              @click="openUser(user.id)"
            >
              {{ user.phone }}
            </td>
            <td
              class="text-black-100"
              @click="openUser(user.id)"
            >
              {{ user.email }}
            </td>

            <td
              class="text-capitalize"
              @click="openUser(user.id)"
            >
              <template v-for="role in user.roles">
                {{ role.name }}
              </template>
            </td>
            <td
              class="text-black-100"
              @click="openUser(user.id)"
            >
              {{ user.office.name }}
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

              @page="getUsers()"
            />
          </div>
          <div class="col">
            <MazSelect
              v-model="tableData.length"
              placeholder="Per Page"
              :options="perPage"
              @input="getUsers()"
            />
            <!--                        <label class="label">Per Search</label>-->
            <!--                        <select class="form-control form-control-sm w-25-md w-50-sm float-right"-->
            <!--                                v-model="tableData.length"-->
            <!--                                @change="getUsers()">-->
            <!--                            &lt;!&ndash;                            <option v-for="(records, index) in perPage" :key="index" :value="records">{{ records }}&ndash;&gt;-->
            <!--                            &lt;!&ndash;                            </option>&ndash;&gt;-->
            <!--                        </select>-->
          </div>
        </div>
      </div>
    </div>
    <MazDialog
      v-model="newUserModel"
      @confirm="addNewUser"
    >
      <div slot="title">
        Invite New Users
      </div>
      <p>User will be invited via email, assigned default role</p>
      <MazInput
        v-model="emails"
        placeholder="Scout@socal.com, charles@scout.com, you@...."
      />
    </MazDialog>
  </div>
</template>

<script>
import Filters from '../../components/dataTable/filters'
import { mapActions, mapGetters } from 'vuex'
import axios from 'axios'
import DataTable from '../../components/dataTable/DataTable'

export default {
	name: 'UserIndex',
	components: { DataTable, Filters },
	data () {
		return {
			count: 0,
			officeList: [],
			office: null,
			position: null,
			emails: '',
			tableData: { length: 10 },
			perPage: [
				{ label: '10', value: 10 },
				{ label: '25', value: 25 },
				{ label: '50', value: 50 },
				{ label: '100', value: 100 }
			],
			pageCount: 1,
			currentPage: 1,
			users: {},
			sortKey: null,
			pageRange: 0,
			terminated: false,
			newUserModel: false

		}
	},

	created () {
		this.fetchUsers().then(() => {
			this.getUsers()
		})
		this.getOffice()
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),

		addNewUser () {
			const data = {
				users: this.emails
			}

			axios.post('/api/user', data
			).then((response) => {
				Swal.fire('User(s) Invited')
				this.emails = ''
				this.newUserModel = false
			})
		},

		openUser (id) {
			this.$router.push({ path: `/dashboard/user/${id}` })
		},
		getCount () {
			axios.post('/api/user/count',
				{
                    	terminated: this.terminated,
                    	draw: this.tableData.draw,
                    	length: this.tableData.length,
                    	search: this.tableData.search,
                    	column: this.tableData.column,
                    	dir: this.tableData.dir,
                    	officeId: this.office,
                    	position: this.position
				}
			).then((response) => {
				this.count = response.data
			})
				.catch((errors) => {
					console.log(errors)
				})
		},

		getUsers () {
			this.tableData.draw++
			axios.get(`/api/user?page=${this.currentPage}`, {
				params:
                    {
                    	terminated: this.terminated,
                    	draw: this.tableData.draw,
                    	length: this.tableData.length,
                    	search: this.tableData.search,
                    	column: this.tableData.column,
                    	dir: this.tableData.dir,
                    	officeId: this.office,
                    	position: this.position
                    }
			}).then((response) => {
				this.getCount()
				this.openFilters = false
				this.currentPage = response.data.meta.current_page
				this.pageCount = response.data.meta.last_page
				this.pageRange = response.data.meta.last_page
				this.users = response.data.data

				this.configPagination(response.data.meta)
			})
				.catch((errors) => {
					console.log(errors)
				})
		},

		getOffice () {
			axios.get('/api/office')
				.then((response) => {
					const officeList = response.data.data
					// console.log(officeList)
					const payload = [{
						label: 'Select Office',
						value: null
					}]
					$.each(officeList, function (key, value) {
						const obj = {
							label: value.name,
							value: value.id
						}

						payload.push(obj)
					})
					this.officeList = payload
					this.labelLoaded = true
				})
				.catch(function (error) {
					console.log(error)
				})
		}

	},
	computed: {
		...mapGetters([
			'getUser'
		]),
		columns () {
			const sortOrders = {}
			let columns = []

			columns = [
				{ label: 'Name', name: 'fullName' },
				{ label: 'Phone', name: 'phone' },
				{ label: 'Email', name: 'email' },
				{ label: 'Position', name: 'roles' },
				{ label: 'Office', name: 'office' }
			]

			return columns
		}
	}

}
</script>

<style scoped>

</style>
