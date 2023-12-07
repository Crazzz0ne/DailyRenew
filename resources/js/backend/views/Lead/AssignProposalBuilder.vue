<template>
  <div class="card">
    <div class="card-header">
      <div class="row h-100 justify-content-center align-items-center">
        <div class="col-md-6 col-sm-12 pt-2">
          <h2 class="display-5 text-white">
            New Proposal
          </h2>
        </div>
        <div class="col-md-6 col-sm-12" />
      </div>
    </div>
    <div class="card-body">
      <div class="container">
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <div class="form-group">
              <h4 for="language">
                Language
              </h4>
              <span
                id="language"
                name="language"
                class="text-capitalize form-control"
              >{{ getLead.customer.language }}</span>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="form-group">
              <h4 for="name">
                Name
              </h4>
              <span
                id="name"
                type="text"
                name="name"
                class="form-control"
              >{{ getLead.customer.first_name }} {{ getLead.customer.last_name }}</span>
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-around text-center">
          <div class="container">
            <div class="row">
              <div class="col-md-6 col-sm-12" />
              <div
                v-if="!show"
                class="col-10 div-wrapper d-flex justify-content-center align-items-center"
              >
                <button
                  data-toggle="tooltip"
                  title=""
                  class="btn btn-lg btn-danger mr-4"
                  @click="decline"
                >
                  <span class="pr-1">Decline</span>
                  <i class="fas fa-minus-circle" />
                </button>
                <button
                  data-toggle="tooltip"
                  title=""
                  class="btn btn-lg btn-success ml-4"
                  @click="attachSp1"
                >
                  <span class="pr-1">Accept</span>
                  <i class="fas fa-plus-circle" />
                </button>
              </div>
              <div
                v-else
                class="col-md-6 col-sm-12 div-wrapper d-flex justify-content-center align-items-center"
              >
                <div>
                  <h1>Congratulations!</h1>
                  <h3>
                    {{ getLead.customer.street_address }}, {{ getLead.customer.city }}, {{
                      getLead.customer.zip_code }}
                  </h3>
                  <button
                    data-toggle="tooltip"
                    title="Get Directions with Google Maps"
                    class="btn btn-lg btn-primary mt-2"
                    @click="openMaps"
                  >
                    <span class="pr-1">Get Directions</span>
                    <i
                      class="fa fa-map-marker"
                      aria-hidden="true"
                    />
                  </button>
                  <button
                    data-toggle="tooltip"
                    title="Open Lead"
                    class="btn btn-lg btn-third mt-2"
                    @click="openLead"
                  >
                    <span class="pr-1">Open Lead</span>
                    <i class="far fa-sticky-note" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import Note from '../../components/lead/note/Note'

export default {
	name: 'AssignProposalBuilder',
	components: {
		Note
	},
	data: function () {
		return {
			show: false,
			mapUrl: ''
		}
	},
	created () {
		this.fetchLead(this.$route.params.leadId)
		this.fetchUsers()
	},
	computed: {
		...mapGetters(['getLead', 'getUser']),
		gMap: function () {

		}
	},
	methods: {
		...mapActions(['fetchLead', 'fetchUsers']),

		decline () {
			this.$router.push('/dashboard/lead')
		},

		checkSp1Assigned: function (checkSp1) {
			const method = 'checkSp1'
			const data = {
				user: this.getUser.id,
				lead: this.getLead.id,
				type: 'checkSp1',
				market: window.LeadMarket
			}

			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/proposal-builder`, data)
				.then((response) => {
					console.log(response)
					if (!response.data.ok) {
						this.attachSp1()
					} else {
						Swal.fire({
							title: 'Too Slow',
							text: `${response.data.user_name} is already on it`
						})
							.then((result) => {
								if (result.value) {
									this.$router.push('/dashboard/lead')
								}
							})
					}
					console.log('polling axios')
				})
				.catch((error) => {
					console.log(error)
				})
		},

		attachSp1: function () {
			const method = 'assignSP1'
			const data = {
				user: this.getUser.id,
				lead: this.getLead.id,
				type: 'assignPB',
				market: window.LeadMarket
			}

			axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/proposal-builder`, data)
				.then((response) => {
					console.log(response.data.progress)
					if (response.data.ok || response.data.progress) {
						Swal.fire({
							type: 'success',
							title: 'Approved'
						})
						this.$router.push(`/dashboard/lead/${this.$route.params.leadId}`)
						// this.mapUrl = `https://www.google.com/maps/dir/?api=1&destination=${this.getLead.customer.street_address.split(' ').join('+')},+${this.getLead.customer.city.split(' ').join('+')}`;
						// this.show = true;
					} else {
						// console.log(response);
						Swal.fire({
							title: 'Too Slow',
							text: `${response.data.user_name} is already on the way`
						})
							.then((result) => {
								if (result.value) {
									this.$router.push('/dashboard/lead')
								}
							})
					}
				})
				.catch((error) => {
					console.log(error)
				})
		},
		openMaps: function () {
			window.open(this.mapUrl, '_blank')
		},
		openLead: function () {
			this.$router.push(`/dashboard/lead/${this.getLead.id}`)
		}
	}
}
</script>

<style scoped>

</style>
