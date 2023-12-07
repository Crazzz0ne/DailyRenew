<template>
  <div class="card">
    <div class="card-header">
      <div class="row justify-content-between">
        <div class="col">
          <h3>Your Team</h3>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div
        class="card"
      >
        <div class="card-header">
          <div class="row justify-content-between">
            <div class="col">
              <h5>{{ team.name }}</h5>
            </div>
            <div class="col" />
          </div>
        </div>
        <div class="card-body">
          <span class="float-right"><MazBtn
            fab
            :icon-name="'add'"
            @click="openNewTeamMembers(team.id)"
          /></span>
          <div class="row">
            <div
              v-if="team.teamCaptain"
              class="col-3 text-center"
            >
              Team Captain
              <MazAvatar
                style="margin: auto;"
                :size="60"
                :src="teamCaptain(team.id).gravatar"
              />
              {{ teamCaptain(team.id).name.first }} {{ teamCaptain(team.id).name.last }}
            </div>
            <div
              v-else
              class="text-center"
            />
            <div
              v-for="member in team.teamMembers"
              v-if="member.id !== teamCaptainId()"
              class="col-sm-6 col-md-2 text-center pt-3"
              style="display: flex;
                         justify-content: center;"
            >
              <div
                class="text-center"
                @click="removeFromTeam(member.id)"
              >
                <button
                  type="button"
                  class="close"
                  aria-label="Close"
                >
                  <span aria-hidden="true">&times;</span>
                </button>
                <MazAvatar
                  style="margin: auto;"
                  :size="60"
                  :src="member.gravatar"
                />
                {{ member.name.first }} {{ member.name.last }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--      Add TeamMates-->
    <MazDialog
      v-model="crateNewTeamMemberModel"
      :no-confirm="!canAddUsers"
      @confirm="addTeamMember()"
    >
      <div slot="title">
        Add more team members
      </div>

      <MazSelect
        v-model="newUsers"
        class="m-3"
        :placeholder="'Select Reps'"
        :options="compUsers"
        :position="'top'"
        multiple
        search
      />
      <div />
    </MazDialog>
  </div>
</template>

<script>
import axios from 'axios'
import { mapActions, mapGetters } from 'vuex'

export default {
	name: 'TeamDashboard',
	data () {
		return {
			crateNewTeamMemberModel: false,
			newTeamMemberLoading: false,
			team: {},
			createTeamModel: false,
			newName: '',
			captainList: [],
			newTeamCaptain: null,
			users: [],
			newUsers: [],
			selectedTeam: 0
		}
	},
	computed: {
		...mapGetters([
			'getUser'
		]),
		canAddUsers () {
			return this.newUsers.length !== 0
		},
		canCreateNewTeam () {
			return !!(this.newName.length)
		},
		compUsers () {
			return this.users.map((b) => {
				return {
					value: b.id,
					label: b.fullName
				}
			})
		}

	},
	created () {
		this.fetchUsers()
		if (!this.getUser.data.id) {
			const timeout = setTimeout(() => {
				this.getTeam()
			}, 5000)
		} else {
			this.getTeam()
		}
	},
	methods: {
		...mapActions([
			'fetchUsers'
		]),
		addTeamMember () {
			const params = {
				teamMembers: this.newUsers
			}
			this.newTeamMemberLoading = true
			this.crateNewTeamMemberModel = false
			axios.post(`/api/office/${this.getUser.data.office_id}/team/${this.team.id}/users`, params)
				.then((response) => {
					this.getTeam()
					this.newTeamMemberLoading = false
					this.newUsers = []
					this.selectedTeam = 0
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		openNewTeamCaptainModel (teamId) {
			this.getTeamCaptains()
			this.createTeamCaptainModel = true
			this.selectedTeam = teamId
		},
		openNewTeamMembers (teamId) {
			this.getUsers()
			this.selectedTeam = teamId
			this.crateNewTeamMemberModel = true
		},
		addTeamCaptain () {
			this.createTeamCaptainModel = false
			axios.put(`/api/office/${this.officeId}/team/${this.selectedTeam}/team-captain/${this.newTeamCaptain}`)
				.then((response) => {
					this.getTeam()
					this.newTeamCaptain = null
					this.selectedTeam = 0
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		teamCaptainId () {
			const member = this.team.teamMembers.find((member) => {
				if (member.id === this.team.teamCaptain) {
					return member
				}

				return 0
			})
			if (member) {
				return member.id
			}
			return 0
		},
		teamCaptain (id) {
			return this.team.teamMembers.find((member) => {
				if (member.id === this.team.teamCaptain) {
					return member
				}
			})
		},
		removeFromTeam (userId) {
			Swal.fire({
				title: 'Delete?',
				text: 'Are you sure want to remove from the team?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, remove them!'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/office/${this.getUser.data.office_id}/team/${this.team.id}/user/${userId}`)
						.then((response) => {
							this.getTeam()
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		},
		getTeam () {
			axios.get(`/api/office/${this.getUser.data.office.id}/team/${this.getUser.data.team_id}`)
				.then((response) => {
					this.team = response.data.data
				})
		},

		getTeamCaptains: function () {
			const params = {
				officeId: this.officeId,
				position: 'team captain'
			}

			axios.get('/api/salesflow/user/position', { params })
				.then((response) => {
					this.captainList = response.data
					// this.splitUser(this.officeUsers);
					this.loading = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		getUsers () {
			axios.get(`/api/office/${this.getUser.data.office_id}/user?team=true`)
				.then((response) => {
					this.users = response.data.data
				})
		}
	}
}
</script>

<style scoped>

</style>
