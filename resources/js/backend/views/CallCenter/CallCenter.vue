<template>
  <div class="container">
    <div class="card">
      <div class="card-header">
        Rehash get home phone
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p>Ready Mode</p>
            <MazInput
              v-model="fileName"
              :size="'sm'"
              placeholder="File Name"
            />
            <input
              id="xencallHome"
              ref="xencallhome"
              class="pt-2"
              type="file"
              multiple
              @change="handleReadyModeReHash()"
            >
          </div>
        </div>
        <MazBtn
          class="float-right"
          @click="submitHomeFile()"
        >
          Submit
        </MazBtn>
      </div>
    </div>
    <!--        <div class="card">-->
    <!--            <div class="card-header">Rehash get extra phone numbers</div>-->
    <!--            <div class="card-body">-->

    <!--                <div class="row">-->
    <!--                    <div class="col-md-6">-->
    <!--                        <p>Prop stream</p>-->
    <!--                        <input type="file" id="propstream" ref="propstream" v-on:change="handlePropStreamUpload()"/>-->
    <!--                    </div>-->
    <!--                    <div class="col-md-6">-->
    <!--                        <p>Xen Call</p>-->
    <!--                        <input type="file" id="xencall" ref="xencall" v-on:change="handleXenCallUpload()"/>-->
    <!--                    </div>-->

    <!--                </div>-->
    <!--                <MazBtn class="float-right" @click="submitFiles()">-->
    <!--                    Submit-->
    <!--                </MazBtn>-->
    <!--            </div>-->
    <!--        </div>-->
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
	name: 'CallCenter',
	data () {
		return {
			propStreamFile: null,
			xenCallFile: null,
			fileName: ''
		}
	},
	created () {
		this.fetchUsers()
	},

	methods: {
		...mapActions([
			'fetchUsers'
		]),

		handlePropStreamUpload () {
			// console.log(this.$refs.propstream.files[0]);
			this.propStreamFile = this.$refs.propstream.files[0]
		},
		handleXenCallUpload () {
			this.xenCallFile = this.$refs.xencall.files
			// console.log(this.$refs.files.files)
		},
		handleReadyModeReHash () {
			this.xenCallFile = this.$refs.xencallhome.files
			// console.log(this.$refs.files.files)
		},

		submitHomeFile () {
			// Send to Server
			this.uploading = true
			/*
              Initialize the form data
            */
			const formData = new FormData()

			/*
              Iteate over any file sent over appending the files
              to the form data.
            */

			for (let i = 0; i < this.xenCallFile.length; i++) {
				const file = this.xenCallFile[i]
				formData.append('xenCall[' + i + ']', file)
			}

			formData.append('fileName', this.fileName)
			formData.append('email', this.getUser.data.email)
			/*
              Make the request to the POST /multiple-files URL
            */
			axios.post('https://micro.scout.solar/api/ready-mode/upload',
				formData,
				{
					headers: {
						'Access-Control-Allow-Origin': '*',
						'Content-Type': 'multipart/form-data'
					},
					responseType: 'blob'
				}
			).then((response) => {
				// const blob = new Blob([response.data], { type: 'csv/txt' });
				// const link = document.createElement('a');
				// link.href = URL.createObjectURL(blob);
				// link.download = name;
				// link.click();
				// URL.revokeObjectURL(link.href);
				console.log(response)
			}).catch((response) => {
				this.error.status = true
				this.error.message = response
				this.uploading = false
				console.log(response)
			})
		},

		submitFiles () {
			// Send to Server
			this.uploading = true
			/*
              Initialize the form data
            */
			const formData = new FormData()

			/*
              Iteate over any file sent over appending the files
              to the form data.
            */

			const propStreamFile = this.propStreamFile
			const xencall = this.xenCallFile
			// if (!this.checkFileType(file))
			//     return;

			formData.append('propStream', propStreamFile)
			formData.append('xenCall', xencall)

			/*
              Make the request to the POST /multiple-files URL
            */
			axios.post('/api/call-center/rehash-files',
				formData,
				{
					headers: {
						'Access-Control-Allow-Origin': '*',
						'Content-Type': 'multipart/form-data'
					},
					responseType: 'blob'
				}
			).then((response) => {
				const FILE = window.URL.createObjectURL(new Blob([response.data]))

				const docUrl = document.createElement('x')
				docUrl.href = FILE
				docUrl.setAttribute('download', 'file.csv')
				document.body.appendChild(docUrl)
				docUrl.click()
				console.log(response)
			}).catch((response) => {
				this.error.status = true
				this.error.message = response
				this.uploading = false
				console.log(response)
			})
		}
	},
	computed: {
		...mapGetters(['getUser'])
	}
}
</script>

<style scoped>

</style>
