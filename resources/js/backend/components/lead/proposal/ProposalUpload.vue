<template>
  <div>
    <form
      v-if="isInitial || isSaving"
      enctype="multipart/form-data"
      novalidate
    >
      <span><strong>{{ uploadType }}</strong></span>
      <div class="dropbox">
        <input
          type="file"
          :name="uploadFieldName"
          :disabled="isSaving"
          class="input-file"
          @change="filesChange($event.target.name, $event.target.files); fileCount = $event.target.files.length"
        >
        <p v-if="isInitial">
          Upload
        </p>
        <p v-if="isSaving">
          <MazLoader
            :color="'success'"
            :size="250"
          />
        </p>
      </div>
    </form>

    <!--FAILED-->
    <div v-if="isFailed">
      <h2>Uploading failed.</h2>
      <p>
        <a
          href="javascript:void(0)"
          @click="reset()"
        >Try again</a>
      </p>
      <pre>{{ uploadError }}</pre>
    </div>
  </div>
</template>

<script>

const STATUS_INITIAL = 0; const STATUS_SAVING = 1; const STATUS_SUCCESS = 2; const STATUS_FAILED = 3

export default {
	name: 'ProposalUpload',
	props: {
		type: String,
		selectedSystemId: Number
	},
	data () {
		return {
			uploadedFiles: [],
			uploadError: null,
			currentStatus: null,
			uploadFieldName: 'file'
		}
	},
	computed: {
		isInitial () {
			return this.currentStatus === STATUS_INITIAL
		},
		isSaving () {
			return this.currentStatus === STATUS_SAVING
		},
		isSuccess () {
			return this.currentStatus === STATUS_SUCCESS
		},
		isFailed () {
			return this.currentStatus === STATUS_FAILED
		},

		uploadType () {
			switch (this.type) {
			case 'test contract':
				return 'Upload Test Contract'
			case 'Design Plan':
				return 'Design Plan'
			case 'savings-breakdown':
				return 'Savings Breakdown'
			case 'Proposal':
				return 'proposal'
			}
		}
	},
	mounted () {
		this.reset()
	},
	methods: {
		upload (formData) {
			// Not only must you return the axios request, but you must also return the response in the .then function;
			// Otherwise the .then function inside of upload will have an undefined variable.

			const comp = this
			return axios.post(`/api/salesflow/lead/${this.$route.params.leadId}/proposed-system/upload`, formData)
			// get data
				.then((response) => {
					comp.$emit('response', response)
					comp.reset()
					return response.data
				}).catch(err => {
					comp.uploadError = err.response
					comp.currentStatus = STATUS_FAILED
				})
		},
		reset () {
			// reset form to initial state
			this.currentStatus = STATUS_INITIAL
			this.uploadedFiles = []
			this.uploadError = null
		},
		save (formData) {
			const comp = this
			// upload data to the server
			this.currentStatus = STATUS_SAVING

			this.upload(formData)
				.then(function (response) {
					console.log(response)
					comp.uploadedFiles = [].concat(response.data)
					comp.currentStatus = STATUS_SUCCESS
				})
		},
		filesChange (fieldName, fileList) {
			// handle file changes
			const formData = new FormData()

			if (!fileList.length) return

			// append the files to FormData
			Array.from(Array(fileList.length).keys())
				.map(x => {
					formData.append(fieldName, fileList[x], fileList[x].name)
				})
			const psId = this.selectedSystemId.toString()
			formData.append('type', this.type)
			formData.append('proposedsystemId', psId)
			formData.append('user_id', 1)
			// save it
			this.save(formData)
		}
	}
}

</script>

<style scoped>
.dropbox {
    outline: 2px dashed grey; /* the dash box */
    outline-offset: -10px;
    background: lightcyan;
    color: dimgray;
    padding: 10px 10px;
    min-height:100%; /* minimum height */
    position: relative;
    cursor: pointer;
}

.input-file {
    opacity: 0; /* invisible but it's there! */
    width: 100%;
    height: 100%;
    position: absolute;
    cursor: pointer;
    left: 0;
}

.dropbox:hover {
    background: lightblue; /* when mouse over to the drop zone, change color */
}

.dropbox p {
    font-size: 1.2em;
    text-align: center;
    padding: 50px 0;
}
</style>
