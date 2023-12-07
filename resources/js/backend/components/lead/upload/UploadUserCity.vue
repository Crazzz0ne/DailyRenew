<template>
    <div class="container py-5">
        Upload a csv of cities to sync to user.
        <label
            for="file-upload"
            class="custom-file-upload"
        >

            <input
                id="file-upload"
                type="file"
                @change="uploadFile"
            >
            <span>Choose file</span>
        </label>
        <p v-if="message">
            {{ message }}
        </p>
    </div>
</template>

<script>
export default {
    name: 'UploadUserCity',
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    data () {
        return {
            message: ''
        }
    },
    methods: {
        async uploadFile (event) {
            const file = event.target.files[0]

            if (!file) {
                this.message = 'No file selected.'
                return
            }

            const formData = new FormData()
            formData.append('cities', file)

            try {
                const response = await axios.post(`/api/user/${this.user.id}/batch-city`, formData, {
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'multipart/form-data'
                        // Add any necessary headers, e.g. for authentication
                    }
                })

                if (response.status === 200) {
                    this.message = 'File uploaded successfully!'
                } else {
                    this.message = 'Failed to upload the file.'
                }
            } catch (error) {
                this.message = 'An error occurred: ' + error.message
                console.log(error)
            }
        }
    }
}
</script>

<style scoped>
.custom-file-upload {
    display: inline-block;
    position: relative;
    cursor: pointer;
}

input[type="file"] {
    display: none;
}

.custom-file-upload span {
    padding: 10px 20px;
    background-color: #3b3b3b;
    color: #ffffff;
    border: 1px solid #3b3b3b;
    border-radius: 5px;
    font-weight: bold;
}
</style>
