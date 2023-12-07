<template>
    <div>
        <ul
            ref="list"
            class="chat"
        >
            <li
                v-for="msg in messageCompute"

                class="left clearfix"
            >
                <div class="chat-body clearfix">
                    <div class="header p-3">
                        <div class="row ">
                            <div

                                v-for="image in msg.images"
                                class="col"
                            >
                                <img
                                    class="img-thumbnail"
                                    :src="image.path"
                                    @click="showImageFun(image.path)"
                                >
                            </div>
                        </div>
                        <p>{{ msg.body }}</p>
                    </div>
                </div>

                <div
                    class="flex-row justify-content-between pr-4 align-items-end"
                    style="display: flex; align-items: end"
                >
          <span class="chat-img float-left pl-3">

            <div>{{ msg.name }}</div>
             <small v-if="msg.status === 'sent'">Sent</small>
    <small v-if="msg.status === 'queued'">Queued</small>
    <small v-if="msg.status === 'undelivered'">Undelivered</small>
                <small v-if="msg.status === 'delivered'">Delivered</small>
          </span>

                    <span class="text-muted timestamp">
            <small>{{ msg.created_at }}</small>
          </span>
                </div>
            </li>
        </ul>
        <MazDialog
            v-model="showImage"
            :no-header="true"
            :no-confirm="true"
        >
            <!--Show clicked on image within dialog-->
            <div class="row">
                <div class="col-12">
                    <img
                        class="img-thumbnail"
                        :src="image"
                    >
                </div>
            </div>
        </MazDialog>
    </div>
</template>

<script>
export default {
    name: 'MessageBox',
    props: {
        messages: Array
    },
    data () {
        return {
            showImage: false,
            image: ''
        }
    },
    computed: {
        messageCompute () {
            return this.messages.map((b) => {
                b.created_at = this.$date(b.created_at).format('MM/DD h:mm a')
                return b
            })
        }
    },
    mounted () {
        // scrollToBottom()
    },
    watch: {
        messages: {
            deep: true,
            handler() {
                console.log('messages changed'); // Adjusted for clarity
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            }
        }
    },

    methods: {
        showImageFun (image) {
            this.showImage = true
            this.image = image
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.list.lastChild) {
                    this.$refs.list.lastChild.scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            });
        }
    }
}
</script>
<style scoped>
.chat {
    list-style: none;
    margin: 0;
    padding: 0;
    height: 600px;
}

.chat li {
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px dotted #B3A9A9;
}

.chat li .chat-body p {
    margin: 0;
    color: #777777;
}

.panel .slidedown .glyphicon, .chat .glyphicon {
    margin-right: 5px;
}

.panel-body {
    overflow-y: scroll;
    height: 600px;
}

::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    background-color: #F5F5F5;
}

::-webkit-scrollbar {
    width: 12px;
    background-color: #F5F5F5;
}

::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
    background-color: #555;
}

</style>
