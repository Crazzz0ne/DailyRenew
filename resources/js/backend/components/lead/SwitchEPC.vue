<template>
  <div
    v-if="!loading"
    class="row justify-content-center"
  >
    <div class="col pt-2">
      <MazBtnGroup
        v-model="btnGroupValue"
        :items="leads"
        @input="changeLead($event)"
      />
    </div>
    <!--        <div class="col pt-2" v-if="leads.length === 1">-->
    <!--            <MazBtn-->
    <!--                :loading="loadingNew"-->
    <!--                @click="createLaSolar()">-->
    <!--                New La Solar-->
    <!--            </MazBtn>-->
    <!--        </div>-->
  </div>
</template>

<script>
export default {
	name: 'SwitchEPC',
	props: {
		lead: Object
	},
	data () {
		return {
			loading: true,
			loadingNew: false,
			leads: [],
			btnGroupValue: null
		}
	},
	computed: {},
	watch: {

		lead: function (oldId, newId) {
			this.fetchOtherLeads()
		}
	},
	mounted () {
		this.fetchOtherLeads()
	},
	created () {
		Echo.private('lead.' + this.$route.params.leadId)
			.listen('Backend.SalesFlow.Lead.FailedCreditEvent', (e) => {
				console.log(e)
				this.leads.push(e.data)
			})
	},
	methods: {
		createLaSolar () {
			this.loadingNew = true
			const data = {
				leadId: this.lead.id
			}
			axios.post('/api/salesflow/lead', data)
				.then((response) => {
					console.log(response)
					this.fetchOtherLeads()
					this.loadingNew = false
					// console.log(typeof this.leads, 'type of array')
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		fetchOtherLeads () {
			axios.get(`/api/salesflow/customer/multi-lead/${this.lead.customer.id}`)
				.then((response) => {
					console.log(response, 'multi')

					this.leads = response.data.data
					this.btnGroupValue = this.lead.id
					this.loading = false
					// console.log(typeof this.leads, 'type of array')
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		changeLead (leadId) {
			if (this.lead.id === leadId) {

			} else {
				this.$router.push({ path: `/dashboard/lead/${leadId}` })
				this.$emit('changelead', true)
			}
		}

	}
}
</script>

<style scoped>

</style>
