<template>
  <MazDialog
    v-model="makeNew"
    :no-confirm="canSubmit"
    @confirm="createCommission"
  >
    <div slot="title">
      Create Commission
    </div>
    <div>
      <div class="py-2">
        <MazSelect
          v-model="userId"
          :placeholder="'Sales Rep'"
          :search="true"
          :options="compUserList"
          :required="true"
          size="sm"
        />
      </div>
      <div
        v-if="commissionTypes.length"
        class="py-2"
      >
        <MazSelect
          v-model="selectedType"
          :placeholder="'Commission Type'"
          :options="commissionTypes"
          :required="true"
          size="sm"
        />
      </div>
      <div class="py-2">
        <MazInput
          v-model="leadId"
          :placeholder="'Lead Id'"
          :type="'number'"
          :required="true"
        />
      </div>
      <div class="py-2">
        <MazInput
          v-model="amount"
          :placeholder="'amount'"
          :type="'number'"
          :required="true"
        />
      </div>
    </div>
  </MazDialog>
</template>

<script>
import axios from 'axios'

export default {
	name: 'CreatePayroll',
	props: {
		user: {
			type: Object,
			required: true
		},
		makeNew: Boolean
	},
	data () {
		return {
			userId: null,
			amount: null,
			leadId: null,
			selectedType: null,
			commissionTypes: [],
			userList: []
		}
	},
	computed: {
		compUserList () {
			return this.userList.map((b) => {
				return { label: b.name, value: b.id }
			})
		},
		canSubmit () {
			if (this.userId && this.amount && this.leadId && this.selectedType) {
				return false
			} else {
				return true
			}
		}
	},
	created () {
		this.getCommissionTypes()
	},
	mounted () {
		this.getUserList()
	},

	methods: {
		createCommission () {
			const data = {
				userId: this.userId,
				amount: this.amount,
				leadId: this.leadId,
				typeId: this.selectedType
			}
			this.$emit('close-model', true)
			axios.post('/api/commission', data)
				.then((response) => {
					this.$emit('new-commission', response.data.data)
					console.log(response)
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		getCommissionTypes () {
			axios.get('/api/commission/types')
				.then((response) => {
					this.commissionTypes = response.data.data
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		getUserList () {
			axios.get('/api/region/14/users')
				.then((response) => {
					// console.log('Region 14 User Response: ', response.data)
					this.userList = response.data
				})
				.catch(function (error) {
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>

</style>
