<template>
  <div id="app">
    <div class="container">
      <div>
        <select
          v-model="selected"
          class="mb-2"
          @select="selected"
        >
          <option :value="'bill'">
            <strong>Bill</strong>
          </option>
          <option
            v-if="$can('sp2') ||
              $can('administrate leads')"
            :value="'cpuc'"
          >
            <strong>CPUC</strong>
          </option>
          <option
            v-if="$can('sp2') ||
              $can('integrations') ||
              $can('administrate leads')"
            :value="'nem doc'"
          >
            <strong>NEM Document</strong>
          </option>
          <option
            v-if="$can('integrations') ||
              $can('administrate leads')"
            :value="'savings breakdown'"
          >
            <strong>Savings Breakdown</strong>
          </option>
          <option
            v-if="$can('sp2') ||
              $can('integrations') ||
              $can('administrate leads')"
            :value="'solar agreement'"
          >
            <strong>Solar Agreement</strong>
          </option>
          <option :value="'survey pictures'">
            <strong>Survey Pictures</strong>
          </option>
        </select>
      </div>
      <!--UPLOAD-->
      <form
        v-if="isInitial || isSaving"
        enctype="multipart/form-data"
        novalidate
      >
        <h1>Upload File</h1>
        <div class="dropbox">
          <input
            type="file"
            multiple
            :name="uploadFieldName"
            :disabled="isSaving"
            accept="image/*"
            class="input-file"
            @change="uploadFiles"
          >
          <p v-if="isInitial">
            Drag your file(s) here to begin<br> or click to browse
          </p>
          <p v-if="isSaving">
            Uploading {{ fileCount }} files...
          </p>
        </div>
      </form>
      <!--SUCCESS-->
      <div v-if="isSuccess">
        <h2>Uploaded {{ uploadedFiles.length }} file(s) successfully.</h2>
        <p>
          <a
            href="javascript:void(0)"
            @click="reset()"
          >Upload again</a>
        </p>
        <ul class="list-unstyled">
          <li v-for="item in uploadedFiles">
            <img
              :src="item.url"
              class="img-responsive img-thumbnail"
              :alt="item.originalName"
            >
          </li>
        </ul>
      </div>
      <!--FAILED-->
      <div v-if="isFailed">
        <h2>Uploaded failed.</h2>
        <p>
          <a
            href="javascript:void(0)"
            @click="reset()"
          >Try again</a>
        </p>
        <pre>{{ uploadError }}</pre>
      </div>
    </div>
  </div>
</template>

<script>

const STATUS_INITIAL = 0; const STATUS_SAVING = 1; const STATUS_SUCCESS = 2; const STATUS_FAILED = 3
export default {
	name: 'FileUpload',
	data () {
		return {
			uploadFiles: [],
			uploadError: null,
			currentStatus: null,
			uploadFieldName: 'photos',
			selection: '',
			selected: 'bill',
			leadId: 1,
			userId: 1
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
		}
	},
	mounted () {
		this.reset()
	},
	methods: {
		reset () {
			// reset form to initial state
			this.currentStatus = STATUS_INITIAL
			this.uploadedFiles = []
			this.uploadError = null
		},
		save (formData) {
			// upload data to the server
			this.currentStatus = STATUS_SAVING

			this.uploading = true

			axios.post(`/api/salesflow/lead/${this.leadId}/upload`, formData)
				.then((response) => {
					console.log(response)
					this.uploading = false
					this.currentStatus = STATUS_SUCCESS
					Swal.fire({
						type: 'success',
						title: 'File Uploaded'
						// text: `${this.files.name} uploaded!`,
					}
					)
					// this.files = "";
				})
				.catch(function (error) {
					// this.currentStatus = STATUS_FAILED;
					console.log(error)
				})

			// upload(formData, this.leadId)
			//     // .then(wait(1500)) // DEV ONLY: wait for 1.5s
			//     .then(x => {
			//         this.uploadedFiles = [].concat(x);
			//         this.currentStatus = STATUS_SUCCESS;
			//         // console.log(x, ' responce')
			//     })
			//     .catch(err => {
			//         this.uploadError = err.response;
			//         this.currentStatus = STATUS_FAILED;
			//         console.log(err.response, ' err.response')
			//     });
		},
		filesChange (fieldName, fileList, selected) {
			// handle file changes
			const formData = new FormData()
			if (!fileList.length) return
			// append the files to FormData

			$.each(this.uploadFiles, function ($key, file) {
				formData.append(`file[${key}]`, file)
			})
			formData.append('type', this.selected)
			formData.append('lead_id', this.leadId)
			formData.append('user_id', this.userId)
			console.log(formData, ' formdData ')

			/// //////////

			axios.post(`/api/salesflow/lead/${this.leadId}/upload`, formData)
				.then((response) => {
					console.log(response)
					this.uploading = false
					Swal.fire({
						type: 'success',
						title: 'File Uploaded',
						text: `${this.uploadFiles} uploaded!`
					}
					)
					this.files = ''
				})
				.catch(function (error) {
					this.uploading = false
					this.files = ''
					console.log(error)
					Swal.fire({
						type: 'error',
						title: 'We need a file to upload'
					})
				})

			/// /////////////

			// save it
			// this.save(formData);
		}
	}
}
</script>

<style lang="scss">
    .dropbox {
        outline: 2px dashed grey; /* the dash box */
        outline-offset: -10px;
        background: lightcyan;
        color: dimgray;
        padding: 10px 10px;
        min-height: 200px; /* minimum height */
        position: relative;
        cursor: pointer;
    }

    .input-file {
        opacity: 0; /* invisible but it's there! */
        width: 100%;
        height: 200px;
        position: absolute;
        cursor: pointer;
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
