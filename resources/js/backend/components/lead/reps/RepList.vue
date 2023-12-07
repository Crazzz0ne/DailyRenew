<template v-if="this.canvasser && this.sp1">
  <div>
    <div class="card mx-1 p-1 h-100">
      <div class="container pt-3">
        <div class="row justify-content-between">
          <div
            v-if="$can('view offices')"
            class="col-md-3 col-sm-12"
          >
            <office
              :office-id="officeId"
              :lead-id="leadId"
            />
          </div>
          <div
            v-if="requestType"
            class="col-md-3 col-sm-12"
          >
            <RequestLineButton
              :request-needed="!hasSp2"
              :lead-id="leadId"
              :type="requestType"
              :can-revert="true"
              @queue="queue = $event"
            />
          </div>
        </div>
      </div>
      <!--        {{ this.reps }}-->
      <h4 class="pt-2 text-center">
        Team
      </h4>
      <div class="container pt-4 px4">
        <div class="row align-content-center">
          <div
            v-for="(rep, index) in reps"
            class="col-md-12 col-lg-6 "
          >
            <div class="card">
              <div class="card-header">
                <h5 class="text-capitalize">
                  {{ rep.position_name }}
                </h5>
              </div>
              <div class="card-body">
                <p class="text-capitalize">
                  {{ rep.first_name }} {{ rep.last_name }}
                </p>
                <!--                                <div>-->
                <!--                                    <label>Phone:</label> <a :href="`tel:${rep.phone_number}`">{{-->
                <!--                                        rep.phone_number-->
                <!--                                    }}</a>-->
                <!--                                </div>-->
                <!--                                <div>-->
                <!--                                    <label>email:</label> {{ rep.email }}-->
                <!--                                </div>-->
              </div>
              <div v-if="rep.login.length && $can('edit proposal')">
                <div
                  v-for="login in rep.login"
                  class="rounded container p-3 mt-3"
                >
                  <h5>Login Information</h5>
                  <div>
                    <label class="d-inline">User
                      Name:&ensp;</label>{{ login.passwords[0].user_name }}<br>

                    <label>Password:&ensp;</label>{{ login.passwords[0].password }}
                    <br>
                    <label>Website:&ensp;</label><span
                      style="color:blue"
                      @click="openLink(login.web_address)"
                    >{{ login.web_address }}</span>
                  </div>
                </div>
              </div>
              <div
                v-show="$can('edit user on lead')"
                class="card-footer"
              >
                <div class="float-right">
                  <button
                    type="button"
                    class="btn btn-danger"
                    aria-label="Close"
                    style="border-top-right-radius: 0.25rem;
                                           border-bottom-right-radius: 0.25rem;
                                            border-top-left-radius: 0px;
                                            border-bottom-left-radius: 0px;"
                    @click="removeRep(rep.id, rep.position_id, index)"
                  >
                    <i class="fas fa-trash" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <template v-if="$can('edit user on lead')">
          <div class="container">
            <h5 class="text-center my-4">
              Add A Rep
            </h5>
            <div class="row align-content-center">
              <div class="col-6">
                <div class="form-group">
                  <MazSelect
                    v-model="position"
                    :placeholder="'Select Position'"
                    :options="positions"
                    :position="'top'"

                    @input="fetchUser()"
                  />
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <MazSelect
                    v-model="newUser"
                    :placeholder="'Select Rep'"
                    :options="userList"
                    :position="'top'"
                    search
                  />
                </div>
              </div>
            </div>
            <MazBtn
              :disabled="!disabled"
              :loading="submitLoading"
              class="float-right"
              @click="submitUser"
            >
              Submit
            </MazBtn>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>
<script>
import Office from '../Office/Office'
import RequestLineButton from '../line/RequestLineButton'

export default {
	name: 'RepList',
	components: { RequestLineButton, Office },
	props: {
		leadId: Number,
		officeId: Number,
		market: String,
		source: String,
		onActiveCount: Number

	},
	data () {
		return {
			reps: [],
			submitLoading: false,
			officeUsers: {},
			sp1: [],
			sp2: [],
			salesRep: [],
			integrations: [],
			canvasser: [],
			newUser: null,
			userList: [],
			positions: [
				{ value: null, label: 'Select Position' },
				{ value: 'canvasser', label: 'Opener' },
				{ value: 'sp1', label: 'Sp1' },
				{ value: 'sp2', label: 'Sp2' },
				{ value: 'proposal builder', label: 'Proposal Builder' },
                { value: 'account manager', label: 'Account Manger' },
			],
			loading: false,
			selected: {},
			position: null,
			newRep: {}

		}
	},
	computed: {
		requestType () {
			if (this.$can('create sp1 request') && this.source === 'D2D') {
				return 'd2d_call_center'
			}
			if (this.source === 'call center') {
				return false
			}
			if (this.$can('create sp1 request') && !this.hasSp1) {
				return 'sp1'
			}
		},
		hasSp1 () {
			let present = null

			present = this.reps.find((e) => {
				return e.position_id === 2
			})

			return !!present
		},
		hasSp2 () {
			let present = null

			present = this.reps.find((e) => {
				return e.position_id === 3
			})

			return !!present
		},
		disabled () {
			if (this.newUser && this.newUser && this.position) {
				return true
			} else {
				return false
			}
		}
	},
	watch: {
		officeId: function (newVal, oldVal) {
			this.fetchUser()
		},
		onActiveCount: function (newValue, oldValue) {
			this.fetchRepsOnLead()
		}
	},
	created () {
		this.fetchRepsOnLead()
	},
	mounted () {
		Echo.private('lead.' + this.$route.params.leadId)
			.listen('Backend.SalesFlow.RepAddedToLeadEvent', (e) => {
				console.log('e', e)
				this.reps.push(e.rep)
				this.$emit('reps', this.reps)
			})
	},
	methods: {
		openLink (link) {
			window.open(`https://${link}`)
		},
		fetchRepsOnLead: function () {
			axios.get(`/api/salesflow/lead/${this.leadId}/rep`)
				.then((response) => {
					this.reps = response.data.data
					// this.splitUser(this.officeUsers);
					this.loading = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		fetchUser: function () {
			const params = {
				officeId: this.officeId,
				position: this.position
			}
			let url = '/api/salesflow/user/position'
			if (this.position === 'canvasser') {
				url = '/api/salesflow/user/position?position=canvasser'
			}
			axios.get(url, { params })
				.then((response) => {
					this.userList = response.data
					// this.splitUser(this.officeUsers);
					this.loading = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		submitUser: function () {
			const data = {
				user_id: this.newUser,
				lead_id: this.leadId,
				position: this.position
			}

			this.submitLoading = true
			axios.post(`/api/salesflow/lead/${this.leadId}/rep`, data)
				.then((response) => {
					this.reps.push(response.data.data)
					this.disabled = false
					this.position = null
					this.userList = []
					this.submitLoading = false
				})
				.catch(function (error) {
					this.disabled = false
					this.submitLoading = false
					console.log(error)
				})
		},
		removeRep: function (id, positionId, index) {
			Swal.fire({
				type: 'warning',
				title: 'Remove Rep',
				text: 'Are you sure you want to remove them?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.value) {
					const data = {
						id: id,
						position_id: positionId,
						action: 'delete'
					}
					axios.put(`/api/salesflow/lead/${this.$route.params.leadId}/rep/1`, data)
						.then((response) => {
							console.log(response, 'deleted')
							// this.fetchNotes();
							this.reps.splice(index, 1)
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		}
	}
}
</script>

<style scoped>
#notes {
    margin-top: 20px;
}
</style>
