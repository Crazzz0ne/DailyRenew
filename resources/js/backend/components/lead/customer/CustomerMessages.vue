<template>
    <div>
        <div id="notes">

            <template v-if="notes.length">
                <MessageBox
                    class="panel-body"
                    :messages="notes"
                />
            </template>
            <template v-else>
                <MazLoader />
            </template>
        </div>
        <div class="panel-footer pt-3">
            <div class="row align-content-between px-2">
                <div class="col-12">
                    <MazInput
                        v-model="message"
                        placeholder="Type your message here..."
                        left-icon-name="comment"
                        textarea
                        @keyup.enter="submitNoteAndImage"
                    />
                    <div class="row justify-content-between">
                        <div class="col">
                            <input
                                id="files"
                                ref="files"
                                type="file"
                                multiple
                                accept="image/*"
                                @change="handleFilesUpload()"
                            >
                        </div>
                        <div class="col">
                            <MazBtn
                                id="btn-chat"
                                :disabled="!message && !disabled"
                                class="btn btn-warning btn-sm float-right mt-2"
                                @click="submitNoteAndImage"
                            >
                                Send
                            </MazBtn>
                        </div>
                    </div>
                    <MazBtn
                        v-if="phoneNumber"
                        :loading="calling"
                        icon-name="call"
                        fab
                        @click="callCustomer"
                    />
                    {{ phoneNumber }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MessageBox from './MessageBox'
import imageCompression from 'browser-image-compression'
import axiosRetry from 'axios-retry'
import { mapGetters } from 'vuex'

export default {
    name: 'CustomerMessages',
    components: { MessageBox },
    props: {
        user: Object,
        customerId: Number,
        phoneNumber: String
    },
    data () {
        return {
            calling: false,
            id: 0,
            message: '',
            loading: false,
            urgent: false,
            notes: [],
            disabled: false,
            files: [],
            uploadCount: 0,
            error: {
                status: false,
                message: 'Yikes looks like there was an error, save your work and reload the page. If the problem persists, please submit a ticket.'
            }
        }
    },
    computed: {
        // a computed getter
        ...mapGetters([
            'getUser'
        ])
    },
    mounted () {
        this.scrollToEnd()
    },
    created () {
        this.fetchMessages()
        Echo.private('customer.' + this.customerId)
            .listen('Backend.SalesFlow.Customer.CustomerMessageEvent', (e) => {
                this.notes.push(e.message);
            })
            .listen('Backend.SalesFlow.Customer.CustomerMessageStatusUpdatedEvent', (e) => {
                console.log(e);
                const indexToUpdate = this.notes.findIndex(note => note.id === e.customerMessage.id);
                if (indexToUpdate !== -1) {
                    this.$set(this.notes[indexToUpdate], 'status', e.customerMessage.status);
                }
            });
    },
    updated () {
        // whenever data changes and the component re-renders, this is called.
        this.$nextTick(() => this.scrollToEnd())
    },
    methods: {
        handleFilesUpload () {
            this.files = this.$refs.files.files
            // console.log(this.$refs.files.files)
        },
        async submitNoteAndImage () {
            const formData = new FormData()
            formData.append('body', this.message)

            for (let i = 0; i < this.files.length; i++) {
                // eslint-disable-next-line no-undef
                /*
           Initialize the form data
         */
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
                    let compressedFile
                    if (imageFile.type === 'image/jpeg') {
                        compressedFile = await imageCompression(imageFile, options)
                        console.log('compressedFile instanceof Blob', compressedFile instanceof Blob) // true
                        console.log(`compressedFile size ${compressedFile.size / 1024 / 1024} MB`) // smaller than maxSizeMB
                    } else {
                        compressedFile = imageFile
                    }

                    formData.append('images[' + i + ']', compressedFile)

                    this.uploadCount++
                    // eslint-disable-next-line no-undef
                } catch (e) {
                    this.re = e
                    console.log(e)
                }
            }
            this.submitNote(formData)
        },

        callCustomer () {
            this.calling = true
            axios.post(`/api/salesflow/customer/${this.customerId}/call`)
                .then((response) => {
                    this.calling = false
                    console.log(response)
                })
                .catch(function (error) {
                    this.calling = false
                    console.log(error)
                })
        },

        scrollToEnd: function () {
            if (this.$el.lastElementChild != null) {
                this.$el.scrollTop = this.$el.lastElementChild.offsetTop
                // scroll to the start of the last message
                // this.$refs.list.scrollTop = this.$refs.list.lastElementChild.offsetTop;
            }
        },
        fetchMessages () {
            axios.get(`/api/salesflow/customer/${this.customerId}/customer-message`)
                .then((response) => {
                    this.notes = response.data.data
                    this.loading = true
                })
                .catch(function (error) {
                    console.log(error)
                })
        },
        async submitNote (formData) {
            this.disable = true
            this.message = ''
            axiosRetry(axios, { retries: 3 })

            const response = await axios.post(`/api/salesflow/customer/${this.customerId}/customer-message `,
                formData,
                {
                    headers: {
                        'Access-Control-Allow-Origin': '*',
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then((response) => {
                this.notes.push(response.data.data)
            }).catch((response) => {
                this.rCatch = response

                this.error.status = true
                this.error.message = 'Looks like we lost connection'

                console.log(response)
            })
        }
    }
}
</script>

<style scoped>

.panel-body {
    overflow-y: scroll;
    height: 600px;
}

#notes {
    margin-top: 20px;
}

.bottom-container {
    display: flex;
    flex-direction: row;
    align-items: center;
    width: 100%;
}

</style>
