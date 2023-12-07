<template>
  <div class="row">
    <div
      v-show="compCanViewRegions"
      class="col-sm-12 pb-2"
    >
      <MazSelect
        v-if="regionList"
        v-model="selectedRegion"
        :options="regionList"
        :size="'sm'"
        :placeholder="'Select Region'"
        :position="compDropDownLocation"
        search
        search-placeholder="Region Search"
        color="info"
      >
        <i
          slot="icon-left"
          class="material-icons"
        >
          public
        </i>
      </MazSelect>
    </div>
    <div
      v-show="compCanViewOffices && selectedRegion"
      class="col-sm-12 pb-2"
    >
      <MazSelect
        v-model="selectedOffice"
        :options="officeList"
        :size="'sm'"
        :placeholder="'Office'"
        :position="compDropDownLocation"
        search
        search-placeholder="Office Search"
        color="info"
        @input="changeOffice()"
      >
        <i
          slot="icon-left"
          class="material-icons"
        >
          store
        </i>
      </MazSelect>
    </div>
    <div class="col-sm-12">
      <MazSelect
        v-if="selectedOffice && $can('view office') && canViewUsers"
        v-model="selectedUser"
        :options="userList"
        :size="'sm'"
        :placeholder="'Agent'"
        clearable
        search
        :position="compDropDownLocation"
        search-placeholder="Select Agent"
        color="info"
      >
        <i
          slot="icon-left"
          class="material-icons"
        >
          account_circle</i>
      </MazSelect>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapActions, mapGetters } from 'vuex'

export default {
	name: 'UserOfficeSelect',
	props: {
		office: Number,
		canViewUsers: Boolean,
		canViewOffices: Boolean,
		canViewRegions: Boolean,
		dropdownLocation: {
			type: String,
			required: false,
			default: () => 'bottom'
		},
		userMarketId: {
			type: Number,
			required: false,
			default: () => 0
		}

	},
	data () {
		return {
			regionList: [
				{ label: 'Select One', value: null }
			],
			userList: [{ label: 'loading', value: null }],
			selectedUser: null,
			selectedOffice: null,
			selectedRegion: this.userMarketId,
			officeList: [{ label: 'loading', value: null }],
			labelLoaded: false,
			userLoaded: false,
			draw: 0
		}
	},
	watch: {
		selectedRegion ($old, $new) {
			this.selectedOffice = null
			this.getOffice()
			this.$emit('selectedRegionId', this.selectedRegion)
		    this.draw++
		},
		selectedUser ($old, $new) {
			if (this.userList.length > 1) {
				this.$emit('userNameChange', this.userList.filter(user => user.value === this.selectedUser)[0].label)
			}
			this.$emit('userChange', this.selectedUser)
		},
		selectedOffice () {
			this.$emit('officeChange', this.selectedOffice)
			if (this.officeList.length > 1) {
				this.$emit('officeNameChange', this.officeList.filter(office => office.value === this.selectedOffice)[0].label)
			}
			this.$emit('office', this.office)
			this.getUsers()
		}
	},
	created () {

	},

	mounted () {
		this.fetchUsers().then(() => {
			// if (!this.canViewUsers && !this.canViewUsers) {
			//     this.selectedUser = this.getUser.data.office_id;
			// }

			if (this.$can('administrate company') || this.$can('view all leads')) {

			} else if (this.compCanViewOffices) {

			} else if (this.$can('view office')) {
				this.selectedOffice = this.getUser.data.office_id
			} else {
				console.log('should change office_id', this.getUser.data.office_id)
				this.selectedUser = this.getUser.data.id
				this.selectedOffice = this.getUser.data.office_id
			}

			if (this.$can('view office')) {

			}

			if (this.compCanViewRegions) {
				this.getRegions()
			} else {
				this.getOffice()
				this.selectedRegion = this.getUser.office.region_id
			}
			if (this.canViewUsers) {
			    console.log('users loaded')
				this.getUsers()
			}
			// this.primeOffice();
		}
		)
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),

		changeUser () {
			// this.$emit('userChange', this.selectedUser)
			// if (this.selectedUser) {
			//     this.$emit('userNameChange', this.userList.filter(user => user.value === this.selectedUser)[0].label);
			// } else {
			//     this.$emit('userNameChange', null);
			// }

		},
		changeOffice () {

		},
		primeOffice () {
			this.selectedOffice = this.getUser.data.office_id
		},
		getRegions () {
			axios.get('/api/region')
				.then((response) => {
					this.regionList = response.data.data
					this.regionList.unshift({ label: 'Select One', value: null })

					if (this.selectedRegion !== null) {
						this.getOffice()
					}
					this.regionLableLoaded = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		getOffice () {
			axios.get(`/api/office?market_id=${this.selectedRegion}`)
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
		},
		getUsers () {
			let officeId = null
			if (this.selectedOffice === null && (!this.$can('administrate company') && !this.$can('accept proposal builder'))) {
				officeId = this.getUser.data.office_id
				this.selectedOffice = this.getUser.data.office_id
			} else {
				officeId = this.selectedOffice
			}
			axios.get(`/api/salesflow/user/office?office_id=${officeId}`)
				.then((response) => {
					const options = response.data.data
					const payload = [{
						label: 'Select User',
						value: null
					}]
					$.each(options, function (key, value) {
						const obj = {
							label: value.fullName,
							value: value.id
						}
						payload.push(obj)
					})
					this.userList = payload
					this.userLoaded = true
					this.$emit('userList', this.userList)
				})
				.catch(function (error) {
					console.log(error)
				})
			this.userLoaded = true
		}
	},
	computed: {
		...mapGetters([
			'getUser'
		]),
		compCanViewRegions () {
			if (this.canViewRegions) {
				if (this.$can('administrate company') || this.$can('accept proposal builder')) {
					return true
				}
				return false
			}
			return false
		},
		compCanViewOffices () {
			if (this.canViewOffices) {
				if (this.$can('view offices') || this.$can('administrate company') || this.$can('accept proposal builder') || this.$can('manage region')) {
					return true
				}
			}
		},
		compDropDownLocation () {
			if (this.dropdownLocation) {
				return this.dropdownLocation
			} else {
				return 'left bottom'
			}
		}
	}
}
</script>

<style scoped>

</style>
