<template>
  <div>
    <div class="text-center">
      <h4>Installer Questions</h4>
    </div>
    <div class="row justify-content-between pt2-5 border-bottom border-info">
      <div class="col-md-6 col-sm-12 py-3 col-xl-4 ">
        <label class=" my-auto">Has any unpermitted work been done to this home?</label>
      </div>
      <div class="col-md-6 col-sm-12 py-3 col-xl-4">
        <MazSelect
          v-model="modelQuestions.un_permitted_work"
          :options="options"
          size="sm"
          @input="updatePermitedWork"
        />
      </div>
    </div>
    <div class="row pt-3 justify-content-between  border-bottom border-info">
      <div class="col-md-6 col-sm-12 py-3 col-xl-4 ">
        <label class=" my-auto">Is tree removal part of this project?</label>
      </div>
      <div class="col-md-6 col-sm-12 py-3 col-xl-4">
        <div class="mx-auto">
          <MazSelect
            v-model="modelQuestions.tree_removal"
            :options="options"
            size="sm"
            @input="updateTreeRemoval"
          />
        </div>
      </div>
    </div>
    <div class="row pt-3 justify-content-between  border-bottom border-info">
      <div class="col-md-6 col-sm-12 py-3 col-xl-4 ">
        <label class=" my-auto">Does the home have working Smoke/Carbon Monoxide Detectors?</label>
      </div>
      <div class="col-md-6 col-sm-12 py-3 col-xl-4">
        <div class="mx-auto">
          <MazSelect
            v-model="modelQuestions.alarms_working"
            :options="options"
            size="sm"
            @input="UpdateAlarmsWorking"
          />
        </div>
      </div>
    </div>
    <div class="row pt-3 justify-content-between  border-bottom border-info">
      <div class="col-md-6 col-sm-12 py-3 col-xl-4">
        <label class=" my-auto">Are there any access issues we should be aware of? </label>
      </div>
      <div class="col-md-6 col-sm-12 py-3 col-xl-4">
        <div class="mx-auto">
          <MazSelect
            v-model="modelQuestions.access_issues"
            :options="options"
            size="sm"
            @input="updateAccessIssues"
          />
          <div class="pt-3">
            <MazInput
              v-if="modelQuestions.access_issues === 'yes'"
              v-model="modelQuestions.access_issues_details"
              textarea
              @input="updateAccessIssuesDetails"
            />
          </div>
        </div>
      </div>
    </div>
      <div class="row pt-3 justify-content-between  border-bottom border-info ">
          <div class="col-md-6 col-sm-12 py-3 col-xl-4">
              <label class=" my-auto">is there a HOA?</label>
          </div>
          <div class="col-md-6 col-sm-12 py-3 col-xl-4">
              <div class="mx-auto">
                  <MazSelect
                      v-model="modelQuestions.hoa"
                      :options="options"
                      size="sm"

                      @input="updateHoa"
                  />

              </div>
          </div>
      </div>
      <div class="row pt-3 justify-content-between  border-bottom border-info " v-if="modelQuestions.hoa === 'yes'">
          <div class="col-md-6 col-sm-12 py-3 col-xl-4">
              <label class=" my-auto">HOA Name</label>
          </div>
          <div class="col-md-6 col-sm-12 py-3 col-xl-4">
              <div class="mx-auto">
                  <div v-if="modelQuestions.hoa === 'yes'"
                       class="pt-3">
                      <MazInput
                          v-model="modelQuestions.hoa_name"

                          @blur="updateHoaName"
                      />
                  </div>

              </div>
          </div>
      </div>
      <div class="row pt-3 justify-content-between  border-bottom border-info " v-if="modelQuestions.hoa === 'yes'">
          <div class="col-md-6 col-sm-12 py-3 col-xl-4" >
              <label class=" my-auto">HOA contact</label>
          </div>
          <div class="col-md-6 col-sm-12 py-3 col-xl-4" >
              <div class="mx-auto">
                  <div
                       class="pt-3">
                      <MazInput
                          v-model="modelQuestions.hoa_contact"

                          @blur="updateHoaContact"
                      />
                  </div>

              </div>
          </div>
      </div>
    <div class="row pt-3 justify-content-between  border-bottom border-info">
      <div class="col-md-6 col-sm-12 py-3 col-xl-4">
        <label class=" my-auto">Have you made any promises other than what is in the agreement?</label>
      </div>
      <div class="col-md-6 col-sm-12 py-3 col-xl-4">
        <div class="mx-auto">
          <MazSelect
            v-model="modelQuestions.promises"
            :options="options"
            size="sm"

            @input="updatePromise"
          />
          <div class="pt-3">
            <MazInput
              v-if="modelQuestions.promises === 'yes'"
              v-model="modelQuestions.promises_details"
              textarea
              @input="updatePrommisesDetails"
            />
          </div>
        </div>
      </div>
    </div>
    <div>
      <!--          button to save install questions-->
      <div class="text-center float-right pt-3">
        <MazBtn
          v-if="!modelQuestions.questions_confirmed"
          :loading="loading"
          @click="saveInstallQuestions"
        >
          Save Install Questions
        </MazBtn>
      </div>
    </div>
  </div>
</template>

<script>
export default {
	name: 'SiteSurveyQuestions',
	props: ['questions'],
	data () {
		return {
			loading: false,
			modelQuestions: this.questions,
			options: [
				{ value: 'yes', label: 'Yes' },
				{ value: 'no', label: 'No' }
			]
		}
	},
	methods: {

		UpdateAlarmsWorking (value) {
			// eslint-disable-next-line no-undef
			axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
				alarms_working: value
			}).then((response) => {
				this.modelQuestions = response.data
			})
		},
		saveInstallQuestions () {
			this.loading = true
			axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
				questions_confirmed: true
			}).then(response => {
				this.modelQuestions.questions_confirmed = true
				this.$emit('saveInstallQuestions', true)

				console.log(response)
			})
		},
		updatePermitedWork (value) {
			// eslint-disable-next-line no-undef
			axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
				un_permitted_work: value
			}).then(response => {
				console.log(response)
			})
		},
		updateAccessIssues (value) {
			// eslint-disable-next-line no-undef
			axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
				access_issues: value
			}).then(response => {
				console.log(response)
			})
		},
		updateAccessIssuesDetails (value) {
			// eslint-disable-next-line no-undef
			axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
				access_issues_details: value
			}).then(response => {
				console.log(response)
			})
		},
		updatePrommisesDetails (value) {
			// eslint-disable-next-line no-undef
			axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
				promises_details: value
			}).then(response => {
				console.log(response)
			})
		},
		updatePromise (value) {
			// eslint-disable-next-line no-undef
			axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
				promises: value
			}).then(response => {
				console.log(response)
			})
		},
		updateTreeRemoval (value) {
			// eslint-disable-next-line no-undef
			axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
				tree_removal: value
			}).then(response => {
				console.log(response)
			})
		},
        updateHoa(value) {
            // eslint-disable-next-line no-undef
            axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
                hoa: value
            }).then(response => {
                console.log(response)
            })
        },
        updateHoaName(value) {
            // eslint-disable-next-line no-undef
            axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
                hoa_name: value
            }).then(response => {
                console.log(response)
            })
        },
        updateHoaContact(value) {
            // eslint-disable-next-line no-undef
            axios.put('/api/site-survey-question/' + this.modelQuestions.id, {
                hoa_contact: value
            }).then(response => {
                console.log(response)
            })
        },
	}
}
</script>

<style scoped>

</style>
