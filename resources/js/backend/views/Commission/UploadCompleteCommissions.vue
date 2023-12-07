<template>
  <MazDialog
    v-model="open"

    :no-confirm="true"
  >
    <div slot="title">
      Upload Commission
    </div>
    <div
      v-if="!uploading"
      class="row"
    >
      <div class="col">
        <p>Complete Payroll CSV</p>
        <input
          id="file"
          ref="file"
          type="file"
          @change="handleUpload()"
        >
      </div>
      <div class="col py-3">
        <MazPicker
          v-model="selectedDate"
          :no-time="true"

          :placeholder="'Select pay on date'"

          @formatted="pickerFormatted2 = $event"
        />
      </div>
      <MazBtn
        @click="uploadCommission"
      >
        Submit
      </MazBtn>
    </div>
    <div v-else>
      <MazLoader>
        Loading
      </MazLoader>
    </div>
  </MazDialog>
</template>

<script>
import axios from 'axios'

export default {
	name: 'UploadCompleteCommissions',
	props: ['open'],
	data () {
		return {
			file: null,
			uploading: false,
			selectedDate: ''
		}
	},
	methods: {
		handleUpload () {
			this.file = this.$refs.file.files[0]
		},
		uploadCommission () {
			this.uploading = true
			const formData = new FormData()
			const file = this.file

			formData.append('file', file)
			// formData.append('selectedDate', this.selectedDate);
			console.log('i run')
			axios.post(`/api/commissions/payroll-upload?selectedDate=${this.selectedDate}`, formData)
				.then((response) => {
					this.$emit('new-commission')
					this.uploading = false
				})
				.catch(function (error) {
					console.log(error)
				})
		}
	}
}
</script>

<style scoped>

</style>
