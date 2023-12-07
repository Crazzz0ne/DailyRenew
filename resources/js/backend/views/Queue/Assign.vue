<template>
  <div
    v-if="show"
    class="card"
  >
    <div class="card-header">
      <div class="row h-100 justify-content-center align-items-center">
        <div class="col-md-6 col-sm-12 pt-2">
          <h2 class="display-5 text-white">
            New Queue
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
              >{{ queue.lead.customer.language }}</span>
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
              >{{ queue.lead.customer.full_name }}</span>
            </div>
          </div>
        </div>
        <template>
          <div class="row d-flex justify-content-around text-center">
            <div class="col-md-6 col-sm-12">
              <button
                data-toggle="tooltip"
                title=""
                class="btn btn-lg btn-danger ml-1"
                @click="decline"
              >
                <span class="pr-1">Decline</span>
                <i class="fas fa-minus-circle" />
              </button>
            </div>
            <div class="col-md-6 col-sm-12">
              <button
                data-toggle="tooltip"
                title=""
                class="btn btn-lg btn-success ml-1"
                @click="acceptQueue"
              >
                <span class="pr-1">Accept</span>
                <i class="fas fa-plus-circle" />
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
    <div
      v-if="queue.type === 'build_proposal'"
      class="mt-5 px-2"
    >
      <h5 class="h2 text-center">
        Other Proposal Builders
      </h5>
      <div class="row">
        <div
          v-for="otherRep in otherReps"
          class="col"
        >
          <div class="card">
            <div class="card-header">
              <h4 class="text-capitalize">
                {{ otherRep.fullName }}
              </h4>
            </div>
            <div class="card-body">
              <MazAvatar
                :src="otherRep.gravatar"
              />
            </div>
            <div class="card-footer">
              <div class="row justify-content-between">
                <div class="col-6" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Note from '../../components/lead/note/Note'
import { mapActions, mapGetters } from 'vuex'

export default {
	name: 'Assign',
	components: {
		Note
	},
	data: function () {
		return {
			show: false,
			mapUrl: '',
			notes: [],
			queue: {},
			location: {},
			loaded: false,
			otherReps: []
		}
	},
	created () {
		this.fetchQueue()
		this.fetchOtherBuilders()
		// this.fetchQueue();
		this.fetchUsers()
		this.getLocation()
	},
	mounted () {

	},
	computed: {
		...mapGetters(['getLead', 'getUser']),
		isProposal () {
			return this.queue.type === 'build_proposal'
		}

	},
	methods: {
		...mapActions(['fetchLead', 'fetchUsers']),

		getLocation () {
			if (navigator.geolocation) {
				console.log(navigator.geolocation)

				// timeout at 60000 milliseconds (60 seconds)
				const options = { timeout: 60000 }

				navigator.geolocation.getCurrentPosition(pos => {
					this.gettingLocation = false
					this.location.long = pos.coords.longitude
					this.location.lat = pos.coords.latitude
					this.loaded = true
				}, err => {
					this.gettingLocation = false
					this.errorStr = err.message
				})
			} else {
				alert('Sorry, browser does not support geolocation!')
			}
		},

		decline () {
			this.$router.push('/dashboard/lead')
		},
		accept () {
			this.attachSp1()
		},
		fetchOtherBuilders () {
			axios.get(`/api/salesflow/line/${this.$route.params.queueId}/others`)
				.then((response) => {
					console.log(response, 'fetch lead')
					this.otherReps = response.data.data
				}).catch(function (error) {
					console.log('lead error ', error)
				})
		},

		fetchQueue () {
			axios.get(`/api/salesflow/line/${this.$route.params.queueId}`)
				.then((response) => {
					console.log(response, 'fetch lead')
					this.queue = response.data.data

					this.show = true
				}).catch(function (error) {
					console.log('lead error ', error)
				})
		},

		acceptQueue () {
			const data = {
				type: this.queue.type,
				location: this.location
			}

			axios.put(`/api/salesflow/line/${this.queue.id}`, data)
				.then((response) => {
					if (response.data.ok === true || response.data.progress === true) {
						Swal.fire({
							type: 'success',
							title: 'Approved'
						})
						this.$router.push(`/dashboard/lead/${this.queue.lead.id}`)
					} else {
						console.log(response)
						Swal.fire({
							title: 'Sorry',
							text: `${response.data.taken_name} has taken this.`
						})
							.then((result) => {
								if (result.value) {
									this.$router.push('/dashboard/lead')
								}
							})
					}
				})
				.catch((error) => {
					Swal.fire({
						icon: 'error',
						title: 'Yikes',
						text: 'There was an error, Please let us know on the support channel'
					})
					console.log(error)
				})
		},
		openLead () {
			this.$router.push(`/dashboard/lead/${this.queue.lead.id}`)
		}
	}
}
</script>

<style scoped>

</style>
