<template>
  <div
    v-if="uploadsComputed"
    class="pt-3"
  >
    <h3
      class="text-center"
      style="color: #ff8400"
    >
      Uploads
    </h3>

    <div class="row align-content-between">
      <div
        v-for="upload in uploadsComputed"
        class="col-md-4  col-sm-6 mt-2 py-sm-3"
      >
        <template v-if="upload.isRecording">
          <img
            class="img-thumbnail "
            :src="`${recordingImage}`"
            @click="openFile(upload.path)"
          >
        </template>
        <template v-else-if="!upload.isPDF">
          <img
            class="img-thumbnail "
            :src="`${upload.path}`"
            @click="openFile(upload.path)"
          >
        </template>
        <template v-else>
          <img
            class="img-thumbnail "
            :src="`${pdfImage}`"
            @click="openFile(upload.path)"
          >
        </template>
        <div class="row">
          <div class="col">
            <template v-if="upload.user_id === 1">
              Scout
            </template>
            <template v-else>
              {{ upload.user.fullName }}
            </template>
          </div>

          <div class="col">
            {{ createdAt(upload.created_at) }}
          </div>
        </div>
        <div class="pt-2">
          <MazBtn
            :size="'mini'"
            fab
            @click="openFile(upload.path)"
          >
            <i class="fa fa-eye" />
          </MazBtn>
          <span class="text-uppercase">{{ upload.type }}</span>
          <MazBtn
            :color="'danger'"
            :size="'mini'"
            fab
            @click="deleteNote(upload.id)"
          >
            <i class="fa fa-trash-alt" />
          </MazBtn>
        </div>
      </div>
    </div>
    <div
      v-if="uploads.length > 1"
      class="float-right"
    >
      <MazBtn
        class="btn-default btn-success"
        :loading="downloadAlls"
        @click="downloadAll()"
      >
        Download All
      </MazBtn>
    </div>
  </div>
</template>

<script>
import dayjs from 'dayjs'

export default {
	name: 'OldShowUpload',
	props: {
		newUpload: null,
		leadId: Number,
		userId: Number,
		canSalesRep: Boolean,
		canSp1: Boolean,
		canSp2: Boolean,
		canBuild: Boolean

	},
	data () {
		return {
			downloadAlls: false,
			loading: false,
			uploads: [],
			pdfImage: '/img/backend/props/pdf.png',
			recordingImage: '/img/backend/props/recording.png'
		}
	},
	computed: {
		uploadsComputed () {
			if (this.uploads) {
				return this.uploads.map((b, canSalesRep, canSp2, canSp1, canBuild, canCanvas) => {
					const reg = /(.pdf)/

					if (b.path.search(reg) > 0) {
						b.isPDF = true
					} else {
						b.isPDF = false
					}

					if (b.type === 'call center recording' || b.type === 'welcome call recoding') {
						b.isRecording = true
					} else {
						b.isRecording = false
					}

					if (('signed CPUC' || b.type === 'signed NEM doc' ||
                        b.type === 'signed solar agreement' || b.type ===
                        'test contract' || b.type === 'proposal' || b.type === 'signed ACH' ||
                        b.type === 'signed credit check' || b.type === 'quote' || b.type === 'CCA') && canBuild) {
						b.canView = true
					} else if (('signed CPUC' || b.type === 'signed NEM doc' ||
                        b.type === 'signed solar agreement' || b.type ===
                        'test contract' || b.type === 'proposal' || b.type === 'quote' || b.type === 'CCA' || b.type === 'survey pictures') && (canSalesRep || canSp2)) {
						b.canView = true
					} else if (('bill' || b.type === 'savings breakdown' ||
                        b.type === 'survey-pictures') && (canCanvas || canSp1)) {
						b.canView = true
					} else {
						b.canView = false
					}

					return b
				})
			} else {
				return ''
			}
		}

	},
	watch: {
		newUpload ($old, $new) {
			this.uploads.push($new)
		},
		onActiveCount: function (newValue, oldValue) {
			this.fetchUpload()
		}
	},
	mounted () {
		this.fetchUpload()
	},
	created () {
		Echo.private('lead.' + this.$route.params.leadId)
			.listen('Backend.SalesFlow.LeadFileUploadEvent', (e) => {
				console.log(e)
				this.uploads.unshift(e.data)
			})
	},
	methods: {
		createdAt (createdAt) {
			return	dayjs(createdAt).format('MMM D, YYYY h:mm A')
		},
		deleteNote (uploadId) {
			Swal.fire({
				type: 'warning',
				title: 'Delete Upload?',
				text: 'Are you sure you want to delete that?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes',
				cancelButtonText: 'No'
			}).then((result) => {
				if (result.value) {
					axios.delete(`/api/salesflow/lead/${this.leadId}/upload/${uploadId}`)
						.then((response) => {
							console.log(response)
							this.fetchUpload()
							this.message = ''
						})
						.catch(function (error) {
							console.log(error)
						})
				}
			})
		},
		fetchUpload () {
			axios.get(`/api/salesflow/lead/${this.leadId}/upload/1`)
				.then((response) => {
					this.uploads = response.data.data

					this.loading = true
				})
				.catch(function (error) {
					console.log(error)
				})
		},
		downloadAll () {
			this.downloadAlls = true

			axios({
				url: `/api/salesflow/lead/${this.leadId}/upload/download-all`,
				method: 'post',
				responseType: 'blob'
			}).then((response) => {
				const fileURL = window.URL.createObjectURL(new Blob([response.data]))
				const fileLink = document.createElement('a')

				fileLink.href = fileURL
				fileLink.setAttribute('download', 'file.zip')
				document.body.appendChild(fileLink)

				fileLink.click()
				this.downloadAlls = false
			})
		},
		openFile: function (urlPath) {
			window.open(urlPath, '_blank')
		}
	}
}
</script>

<style scoped>

</style>
