<template>
  <div class="card">
    <div class="card-header">
      <div class="row justify-content-between">
        <div class="col">
          <h3>Teams</h3>
        </div>
        <div class="col">
          <div class="float-right">
            <MazBtn
              fab
              :icon-name="'add'"
              :loading="newTeamLoading"
              @click="newTeamModel"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div
        v-for="(team, index) in teams"
        :key="index"
        class="card"
      >
        <div class="card-header">
          <div class="row justify-content-between">
            <div class="col">
              <h5>{{ team.name }}</h5>
            </div>
            <div class="col">
              <MazBtn
                class="float-right"
                :color="'danger'"
                fab
                :icon-name="'remove'"
                @click="deleteTeam(team.id)"
              />
            </div>
          </div>
        </div>
        <div class="card-body">
          <MazList>
            <MazListItem :key="team.id">
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
                  <button
                    type="button"
                    class="close"
                    aria-label="Close"
                    @click="removeTeamCaptain(team.id, teamCaptain(team.id).id)"
                  >
                    <span aria-hidden="true">&times;</span>
                  </button>
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
                >
                  <MazBtn
                    :size="'sm'"
                    :icon-name="'add'"
                    :loading="newTeamLoading"
                    @click="openNewTeamCaptainModel(team.id)"
                  >
                    Add Team Captain
                  </MazBtn>
                </div>
                <div
                  v-for="member in team.teamMembers"
                  v-if="member.id !== teamCaptainId(team.id)"
                  class="col-sm-6 col-md-2 text-center pt-3"
                  style="display: flex;
                         justify-content: center;"
                >
                  <div
                    class="text-center"
                    @click="removeFromTeam(team.id, member.id)"
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
            </MazListItem>
          </MazList>
        </div>
      </div>
    </div>
    <!--      Create New Team-->
    <MazDialog
      v-model="createTeamModel"
      :no-confirm="!canCreateNewTeam"
      @confirm="createNewTeam()"
    >
      <div slot="title">
        Create Team
      </div>
      <div>
        Teams
      </div>
      <MazInput
        v-model="newName"
        class="m-3"
        placeholder="Team Name"
      />
      <MazSelect
        v-model="newTeamCaptain"
        class="m-3"
        :placeholder="'Select team Captain'"
        :options="captainList"
        :position="'top'"
        search
      />
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
    <!--      Add Team Captain-->
    <MazDialog
      v-model="createTeamCaptainModel"
      :no-confirm="newTeamCaptain === null"
      @confirm="addTeamCaptain()"
    >
      <div slot="title">
        Add Team Captain
      </div>

      <MazSelect
        v-model="newTeamCaptain"
        class="m-3"
        :placeholder="'Select team Captain'"
        :options="captainList"
        :position="'top'"
        search
      />
      <div />
    </MazDialog>
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

export default {
	name: 'Teams',
	props: ['officeId', 'user'],
	data () {
		return {
			crateNewTeamMemberModel: false,
			createTeamCaptainModel: false,
			newTeamLoading: false,
			newTeamMemberLoading: false,
			teams: [],
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
		this.getTeams()
	},
	methods: {
		addTeamMember () {
			const params = {
				teamMembers: this.newUsers
			}
			this.newTeamMemberLoading = true
			this.crateNewTeamMemberModel = false
			axios.post(`/api/office/${this.officeId}/team/${this.selectedTeam}/users`, params)
				.then((response) => {
					this.getTeams()
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
					this.getTeams()
					this.newTeamCaptain = null
					this.selectedTeam = 0
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		teamCaptainId (teamid) {
			const index = this.teams.findIndex((team) => {
				if (team.id === teamid) {
					return team
				}
			})
			const member = this.teams[index].teamMembers.find((member) => {
				if (member.id === this.teams[index].teamCaptain) {
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
			const index = this.teams.findIndex((team) => {
				if (team.id === id) {
					return team
				}
			})
			return this.teams[index].teamMembers.find((member) => {
				if (member.id === this.teams[index].teamCaptain) {
					return member
				}
			})
		},
		removeFromTeam (teamId, userId) {
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
					axios.delete(`/api/office/${this.officeId}/team/${teamId}/user/${userId}`)
						.then((response) => {
							this.getTeams()
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		},
		deleteTeam (id) {
			Swal.fire({
				title: 'Delete?',
				text: 'Are you sure want to delete the team?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/office/${this.officeId}/team/${id}`)
						.then((response) => {
							this.getTeams()
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		},
		removeTeamCaptain (teamId, userId) {
			Swal.fire({
				title: 'Delete?',
				text: 'Are you sure want to remove the team captain?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, remove them!'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/office/${this.officeId}/team/${teamId}/user/${userId}`)
						.then((response) => {
							this.getTeams()
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		},
		createNewTeam () {
			const params = {
				teamCaptain: this.newTeamCaptain,
				teamMembers: this.newUsers,
				teamName: this.newName

			}
			this.newTeamLoading = true
			this.createTeamModel = false
			axios.post(`/api/office/${this.officeId}/team`, params)
				.then((response) => {
					this.getTeams()
					this.newTeamLoading = false
					this.newTeamCaptain = null
					this.newUsers = []
				})
				.catch(function (error) {
					console.log(error)
				})
		},

		newTeamModel () {
			this.getTeamCaptains()
			this.getUsers()
			this.createTeamModel = true
		},

		getTeams () {
			axios.get(`/api/office/${this.officeId}/team`)
				.then((response) => {
					this.teams = response.data.data
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
			axios.get(`/api/office/${this.officeId}/user?team=true`)
				.then((response) => {
					this.users = response.data.data
				})
		}
	}

}
</script>

<style scoped>

</style>
