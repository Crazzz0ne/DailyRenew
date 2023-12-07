<template>
  <div class="container">
    {{ rCatch }}
    {{ re }}
    <div
      v-show="alertSuccess"
      class="fade show p-2 bg-green rounded span"
      role="alert"
    >
      <strong>File Uploaded</strong>

      <MazBtn
        type="button"
        @click="alertSuccess = false"
      >
        <span aria-hidden="true">&times;</span>
      </MazBtn>
    </div>

    <div
      v-show="!uploading || !error.status"
      class="row pt-5"
    >
      <div class="col-12">
        <DocumentTypeSelect
          :selected="selected"
          @select="updateSelection"
        />
      </div>
      <div class="col-12 py-3">
        <!--                TODO:make this a drag and drop with the MAZ UI Or get DropZone to work from MAz-->
        <!--                <input  type="file" id="files" ref="files" multiple v-on:change="handleFilesUpload()"  style="display: none"/>-->
        <!--                <MazBtn :color="'primary'" @click="selectFiles">Choose Files</MazBtn>-->
        <input
          id="files"
          ref="files"
          type="file"
          multiple
          @change="handleFilesUpload()"
        >
        <!--                <MazBtn v-on:click="submitFiles()" >Submit</MazBtn>-->
      </div>
      <div
        v-show="files.length > 0"
        class="col-12 py-3"
        style="background: gray; border-radius: 20px; border: #0ED2F7 2px solid; color:white"
      >
        <p
          v-for="file in files"
          v-text="file.name"
        />
      </div>

      <div class="col-12 py-3">
        <MazBtn
          class="mt-3"
          :loading="uploading"
          :color="shadeButton"
          :disabled="shouldBlockSubmission"
          @click="submitFiles()"
        >
          Submit
        </MazBtn>
      </div>
    </div>
    <div v-show="uploading">
      <div class="text-center">
        <MazLoader />
        <!--                <h3>Uploading, Files Left <strong>{{ uploadCount }}</strong></h3>-->
      </div>
    </div>
    <div v-show="error.status">
      <div class="error-container">
        <p> {{ error.message }} </p>
      </div>
      <MazBtn
        type="button"
        icon-name="restart_alt"
        @click="selfReset"
      />
    </div>
  </div>
</template>

<script>
import DocumentTypeSelect from './DocumentTypeSelect'
import imageCompression from 'browser-image-compression'
import axiosRetry from 'axios-retry'
export default {
	components: { DocumentTypeSelect },
	/*
      Defines the data used by the component
    */
	props: {
		leadId: Number,
		userId: Number
	},
	data () {
		return {
			uploadCount: 0,
			files: [],
			selected: '',
			rGood: null,
			rCatch: null,
			re: null,

			error: {
				status: false,
				message: 'Yikes looks like there was an error, save your work and reload the page. If the problem persists, please submit a ticket.'
			},
			alertSuccess: false
		}
	},
	computed: {
		uploading () {
			if (this.uploadCount > 0) {
				return true
			} else {
				return false
			}
		},
		shouldBlockSubmission () {
			const cs = !(this.selected && this.files.length > 0)
			console.log(cs)
			return cs
		},
		shadeButton () {
			if (this.shouldBlockSubmission) { return 'transparent' } else return 'success'
		}
	},

	methods: {
		clearFilesAndReset () {
			this.files = []
			this.$refs.files.files = null
			this.selected = null
			this.uploading = false
			this.error.status = false
			this.error.message = ''
		},
		updateSelection (data) {
			this.selected = data
		},
		handleFilesUpload () {
			this.files = this.$refs.files.files
			// console.log(this.$refs.files.files)
		},
		submitFiles () {
			// Send to Server
			this.uploading = true

			/*
              Iteate over any file sent over appending the files
              to the form data.
            */

			this.uploadFiles()
			this.uploading = false
			/*
              Make the request to the POST /multiple-files URL
            */
		},

		async uploadFiles () {
			for (let i = 0; i < this.files.length; i++) {
				axiosRetry(axios, { retries: 3 })
				/*
           Initialize the form data
         */
				const formData = new FormData()
				const file = this.files[i]
				const imageFile = this.files[i]
				// if (!this.checkFileType(file))
				//     return;
				console.log('originalFile instanceof Blob', imageFile instanceof Blob) // true
				console.log(`originalFile size ${imageFile.size / 1024 / 1024} MB`)
				const options = {
					maxSizeMB: 0.3,
					maxWidthOrHeight: 1920,
					useWebWorker: true
				}
				try {
                    let compressedFile;
                    // Check if the imageFile size is less than 1MB
                    if (imageFile.size <= 1024 * 1024) {
                        // If less than or equal to 1MB, use the original file
                        compressedFile = imageFile;
                    } else {
                        // If greater than 1MB, proceed with compression
                        if (imageFile.type === 'image/jpeg') {
                            compressedFile = await imageCompression(imageFile, options);
                            console.log('compressedFile instanceof Blob', compressedFile instanceof Blob); // true
                            console.log(`compressedFile size ${compressedFile.size / 1024 / 1024} MB`); // smaller than maxSizeMB
                        } else {
                            compressedFile = imageFile;
                        }
                    }

					formData.append('files[' + i + ']', compressedFile)
					formData.append('type', this.selected)
					formData.append('lead_id', this.leadId)
					formData.append('user_id', this.userId)
					this.uploadCount++
					const response = await axios.post(`/api/salesflow/lead/${this.leadId}/upload`,
						formData,
						{
							headers: {
								'Access-Control-Allow-Origin': '*',
								'Content-Type': 'multipart/form-data'
							}
						}
					).then((response) => {
						this.rGood = response
						this.uploadCount--
						if (response.data.status === 69) {
							this.error.status = true
							this.error.message = 'There was a network error try again'
							this.uploading = false
						} else {
							console.log('status ', response.data.status)
							let uploads = null
							uploads = response.data.files

							let i = 0
							uploads.forEach((upload) => {
								this.$emit('upload', upload)
								i++
							})
							switch (this.selected) {
							case 'signed solar agreement':
								this.$emit('pj', 'signed solar agreement')
								break
							case 'signed nem':
								this.$emit('pj', 'signed nem')
								break
							case 'test contract':
								this.$emit('pj', 'test contract')
								break
							case 'proposal':
								this.$emit('pj', 'proposal')
								break
							case 'ach form':
								this.$emit('pj', 'ach form')
								break
							case 'signed CPUC':
								this.$emit('pj', 'signed CPUC')
								break
							case 'cca':
								this.$emit('pj', 'cca')
								break
							case 'quote':
								this.$emit('pj', 'quote')
								break
							}
						}
					}).catch((response) => {
						this.rCatch = response
						this.uploadCount--

						this.error.status = true
						this.error.message = 'Looks like we lost connection'

						console.log(response)
					})
				} catch (e) {
					this.re = e
					console.log(e)
				}
			}
		},
		selfReset () {
			this.files = null
			this.error.status = false
			this.alertSucces = false
			this.uploading = false
			this.selected = ''
			this.$refs.files.value = null
		},
		checkFileType (file) {
			if (!this.validateExtension(file)) {
				this.error.status = true
				this.error.message = 'Invalid file type! Supported file types are: ".jpg", ".jpeg", ".png", ".bmp", ".tiff", ".pdf"'
				this.uploading = false
				return false
			}
			return true
		},
		validateExtension (file) {
			const allowedFileTypes = ['.jpg', '.jpeg', '.png', '.bmp', '.tiff', '.pdf', '.mp3']
			const regex = new RegExp('([a-zA-Z0-9\s_\\.\-:])+(' + allowedFileTypes.join('|') + ')$')
			// console.log(regex, 'regex')
			const filename = file.name.toLowerCase().replace(/\s+/g, '')
			return regex.test(filename)
		},
		selectFiles () {
			return this.$refs.files.click()
		}
	}
}
</script>

<style scoped>
.error-container {
    color: white;
    background: #dc3545;
    border-radius: 20px;
    padding: 2%;
    margin: 2%;
}

.span {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

</style>
