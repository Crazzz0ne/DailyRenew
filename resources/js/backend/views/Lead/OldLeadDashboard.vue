<template>
  <div class="card">
    <div class="card-header">
      <!--    Top header section        -->
      <div class="row h-100 justify-content-center align-items-center text-center">
        <div class="col-12">
          <h1 class="text-capitalize">
            Overview
          </h1>
        </div>
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6 col-sm-12">
              <div
                class="container-fluid"
                style="padding: 0px"
              >
                <div class="row">
                  <div class="col-lg-4 col-sm-4 mt-2">
                    <h4>Status</h4>
                    <select
                      id="status"
                      v-model="filterStatus"
                      name="status"
                      class="form-control form-control-sm w-75 text-capitalize selectpicker"
                      style="margin-left: 35px;"
                    >
                      <option
                        class="text-capitalize"
                        value="all"
                      >
                        All
                      </option>
                      <option
                        class="text-capitalize"
                        value="new lead"
                      >
                        New Lead
                      </option>
                      <option
                        class="text-capitalize"
                        value="pending paperwork"
                      >
                        Pending Paperwork
                      </option>
                      <option
                        class="text-capitalize"
                        value="missed opportunity"
                      >
                        Missed Opportunity
                      </option>
                      <option
                        class="text-capitalize"
                        value="close"
                      >
                        Closed
                      </option>
                      <option
                        class="text-capitalize"
                        value="installed"
                      >
                        Installed
                      </option>
                      <option
                        class="text-capitalize"
                        value="lost"
                      >
                        Lost
                      </option>
                    </select>
                  </div>
                  <!--      TODO: Make this sort          -->
                  <div class="col-lg-4 col-sm-4 mt-2">
                    <h4>Month</h4>
                    <select
                      id="category"
                      name="category"
                      class="form-control form-control-sm w-75 text-capitalize"
                      style="margin-left: 35px;"
                      @change="updateFilterMonth($event)"
                    >
                      <option
                        v-for="month in months"
                        :value="JSON.stringify(month)"
                      >
                        {{ month.name }}
                      </option>
                    </select>
                  </div>
                  <div class="col-lg-4 col-sm-4 mt-2">
                    <template v-if="$can('administrate leads')">
                      <h4>Type</h4>
                      <select
                        id="leadType"
                        v-model="selectedDropdownLead"
                        name="leadType"
                        class="form-control form-control-sm w-75 text-capitalize"
                        style="margin-left: 35px;"
                      >
                        <option value="all">
                          All
                        </option>
                        <option
                          value="myLeads"
                          selected
                        >
                          My Clients
                        </option>
                        <option value="officeLeads">
                          Office Clients
                        </option>
                        <option value="companyLeads">
                          Company Clients
                        </option>
                      </select>
                    </template>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-sm-12">
              <div
                class="container-fluid"
                style="padding: 0px"
              >
                <div class="row">
                  <div class="col-md-9 col-sm-12 mt-2">
                    <!--                                        <div>-->
                    <!--                                            <h4>Search</h4>-->
                    <!--                                        </div>-->
                    <!--                                        <form class="form-inline d-flex justify-content-center md-form form-sm mt-0">-->
                    <!--                                            <i class="fas fa-search" aria-hidden="true" style="margin-left: -20px"></i>-->
                    <!--                                            <input class="form-control form-control-sm ml-3 w-75" type="text" placeholder="Search"-->
                    <!--                                                   aria-label="Search">-->
                    <!--                                        </form>-->
                  </div>
                  <div class="col-md-3 col-sm-3 mt-2">
                    <div
                      class="float-right"
                      style="height: 25px"
                    />
                    <router-link
                      to="/dashboard/lead/create"
                      data-toggle="tooltip"
                      title=""
                      class="btn btn-third ml-1"
                    >
                      <span class="pr-1">New</span>
                      <i class="fas fa-plus-circle" />
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="mx-4">
        <div class="row justify-content-md-start">
          <template
            v-for="parentLeads in getLeadDashboard"
            v-if="shouldShow(parentLeads)"
          >
            <div
              v-show="selectedDropdownLead === getTableName(parentLeads) || selectedDropdownLead === 'all'"
              style="display: contents"
            >
              <h2>{{ getTableNameForHeader(parentLeads) }}</h2>
              <table class="sheru-cp-section responsive-table py-3">
                <thead class="responsive-table__head">
                  <tr class="responsive-table__row">
                    <th
                      v-for="header in tableHeaders"
                      :key="header.id"
                      class="responsive-table__cell responsive-table__cell--head"
                      @click="sort(header, parentLeads)"
                    >
                      <!--                                    <div class="triangle" :class="[header.sort !== 'asc' ? '' : 'rotate']"></div>-->
                      <span class="text-capitalize">{{ header.name }}</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="responsive-table__body">
                  <!--                        <div v-for="lead in parentLeads">{{ lead}}</div>-->

                  <template
                    v-for="leads in parentLeads"
                    v-if="leads !== null"
                  >
                    <tr
                      v-for="lead in leads"
                      v-show="filterStatus === lead.status || filterStatus === 'all'"
                      class="responsive-table__row"
                      style="cursor: pointer;"
                      @click="openLead(lead.id)"
                    >
                      <td
                        v-if="lead.customer !== null"
                        class="responsive-table__cell"
                        data-title="Name"
                      >
                        {{ getName(lead.customer) }}
                      </td>
                      <td
                        v-else
                        class="responsive-table__cell"
                        data-title="Name"
                      >
                        No Name
                      </td>
                      <td
                        v-if="lead.status !== null"
                        class="responsive-table__cell"
                        data-title="Stage"
                      >
                        {{
                          lead.status }}
                      </td>
                      <td
                        v-else
                        class="responsive-table__cell"
                        data-title="Stage"
                      >
                        No Status
                      </td>
                      <td
                        v-if="lead.created_at"
                        class="responsive-table__cell"
                        data-title="Date"
                      >
                        {{
                          dateFormat(lead.created_at) }}
                      </td>
                      <td
                        v-else
                        class="responsive-table__cell"
                        data-title="Date"
                      >
                        No Created Date
                      </td>
                      <td
                        v-if="lead.reps !== null"
                        class="responsive-table__cell"
                        data-title="Reps"
                      >
                        {{
                          getRepNames(lead.reps) }}
                      </td>
                      <td
                        v-else
                        class="responsive-table__cell"
                        data-title="Reps"
                      >
                        {{ lead.reps }}
                      </td>

                      <td
                        v-if="lead.customer !== null && lead.customer !== undefined"
                        class="responsive-table__cell"
                        data-title="City"
                      >
                        {{
                          lead.customer.city }}
                      </td>
                      <td
                        v-else
                        class="responsive-table__cell"
                        data-title="City"
                      >
                        Error Loading Data
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
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
	name: 'LeadDashboard',
	data: function () {
		return {
			selectedDropdownLead: 'all',
			tableHeaders: [
				{
					id: 0,
					name: 'name',
					sort: 'asc'
				},
				{
					id: 1,
					name: 'stage',
					sort: 'asc'
				},
				{
					id: 2,
					name: 'date',
					sort: 'asc'
				},
				{
					id: 3,
					name: 'reps',
					sort: 'asc'
				},
				{
					id: 4,
					name: 'city',
					sort: 'asc'
				}
			],
			filterStatus: 'all',
			filterMonth:
                    {
                    	name: 'January',
                    	short: 'Jan',
                    	number: 1,
                    	days: 31
                    },
			months: [
				{
					name: 'January',
					short: 'Jan',
					number: 1,
					days: 31
				},
				{
					name: 'February',
					short: 'Feb',
					number: 2,
					days: 28
				},
				{
					name: 'March',
					short: 'Mar',
					number: 3,
					days: 31
				},
				{
					name: 'April',
					short: 'Apr',
					number: 4,
					days: 30
				},
				{
					name: 'May',
					short: 'May',
					number: 5,
					days: 31
				},
				{
					name: 'June',
					short: 'Jun',
					number: 6,
					days: 30
				},
				{
					name: 'July',
					short: 'Jul',
					number: 7,
					days: 31
				},
				{
					name: 'August',
					short: 'Aug',
					number: 8,
					days: 31
				},
				{
					name: 'September',
					short: 'Sep',
					number: 9,
					days: 30
				},
				{
					name: 'October',
					short: 'Oct',
					number: 10,
					days: 31
				},
				{
					name: 'November',
					short: 'Nov',
					number: 11,
					days: 30
				},
				{
					name: 'December',
					short: 'Dec',
					number: 12,
					days: 31
				}
			]
		}
	},
	created () {
		this.fetchUsers()
			.then(() => {
				const userInfo = { type: 'user', id: this.getUser.id }
				const officeInfo = { type: 'office', id: this.getUser.data.office.id }
				const companyInfo = { type: 'company', id: this.getUser.id }

				this.fetchLeadDashboardMyLeads(userInfo)
				this.fetchLeadDashboardOfficeLeads(officeInfo)
				this.fetchLeadDashboardCompanyLeads(companyInfo)
			})
	},
	computed: {
		...mapGetters([
			'getLeadDashboard',
			'getUser'
		])
	},
	methods: {
		...mapActions([
			'fetchLeadDashboardMyLeads',
			'fetchLeadDashboardOfficeLeads',
			'fetchLeadDashboardCompanyLeads',
			'fetchUsers'
		]),
		fetchLeadDashboard: function () {

		},
		openLead: function (id) {
			this.$router.push({ path: `/dashboard/lead/${id}` })
		},
		dateFormat: function (date) {
			const raw = new Date(date)
			const formatted = `${raw.getMonth() + 1}-${raw.getUTCDate()}-${raw.getFullYear()}`
			return formatted
		},
		getName: function (customer) {
			if (_.isNil(customer)) {
				return 'No customer name set'
			} else {
				return `${customer.first_name} ${customer.last_name}`
			}
		},
		getRepNames: function (reps) {
			let names = ''

			for (let i = 0; i < reps.length; i++) {
				names += `${reps[i].first_name} ${reps[i].last_name}, `
			}
			return names
		},
		getTableName: function (table) {
			const tableName = Object.keys(table)

			return tableName[0]
		},
		sort: function (sortable, toSortData) {
			let sorted = null
			let item = null

			const clickedParentArray = Object.keys(toSortData)[0]
			const sortingThing = _.cloneDeep(toSortData[Object.keys(toSortData)[0]])

			// console.log(sorted);

			switch (sortable.name) {
			case 'name':
				sorted = _.orderBy(sortingThing, function (e) {
					return e.customer.first_name
				}, [sortable.sort])

				sortable.sort = sortable.sort === 'asc' ? 'desc' : 'asc'
				item = this.tableHeaders.findIndex(tableHeaders => tableHeaders.id === sortable.id)
				if (item !== -1) this.tableHeaders.splice(item, 1, sortable)
				break
			case 'stage':
				sorted = _.orderBy(sortingThing, function (e) {
					return e.status
				}, [sortable.sort])

				sortable.sort = sortable.sort === 'asc' ? 'desc' : 'asc'
				item = this.tableHeaders.findIndex(tableHeaders => tableHeaders.id === sortable.id)
				if (item !== -1) this.tableHeaders.splice(item, 1, sortable)
				break
			case 'date':
				sorted = _.orderBy(sortingThing, function (e) {
					return e.created_at
				}, [sortable.sort])

				sortable.sort = sortable.sort === 'asc' ? 'desc' : 'asc'
				item = this.tableHeaders.findIndex(tableHeaders => tableHeaders.id === sortable.id)
				if (item !== -1) this.tableHeaders.splice(item, 1, sortable)
				break
			case 'reps':
				sorted = _.orderBy(sortingThing, function (e) {
					return e.customer
				}, [sortable.sort])

				sortable.sort = sortable.sort === 'asc' ? 'desc' : 'asc'
				item = this.tableHeaders.findIndex(tableHeaders => tableHeaders.id === sortable.id)
				if (item !== -1) this.tableHeaders.splice(item, 1, sortable)
				break
			case 'city':
				sorted = _.orderBy(sortingThing, function (e) {
					return e.customer.city
				}, [sortable.sort])

				sortable.sort = sortable.sort === 'asc' ? 'desc' : 'asc'
				item = this.tableHeaders.findIndex(tableHeaders => tableHeaders.id === sortable.id)
				if (item !== -1) this.tableHeaders.splice(item, 1, sortable)
				break
			default:
				console.log('Sorry, we are out of ' + expr + '.')
			}

			// console.log(sorted);
			// console.log('head thing' + clickedParentArray);

			switch (clickedParentArray) {
			case 'myLeads':
				this.$store.commit('SET_MY_LEADS', sorted)
				break
			case 'officeLeads':
				this.$store.commit('SET_OFFICE_LEADS', sorted)
				break
			case 'companyLeads':
				this.$store.commit('SET_COMPANY_LEADS', sorted)
				break
			default:
				break
			}
		},
		getTableNameForHeader: function (table) {
			const tableName = Object.keys(table)

			switch (tableName[0]) {
			case 'myLeads':
				return 'My Clients'
				break
			case 'officeLeads':
				return 'Office Clients'
				break
			case 'companyLeads':
				return 'Company Clients'
				break
			}
		},
		shouldShow: function (table) {
			const tableName = Object.keys(table)

			switch (tableName[0]) {
			case 'myLeads':
				return true
				break
			case 'officeLeads':
				if (_.includes(window.Permissions, 'administrate office')) {
					return true
				}
				break
			case 'companyLeads':
				if (_.includes(window.Permissions, 'administrate company')) {
					return true
				}
				break
			}
		}
	}
}
</script>

<style scoped>
    .rotate {
        transform: rotate(180deg);
    }
</style>
