<template>
    <div v-if="queuesComp">
        <h1>Queue</h1>
        <template v-if="$can('administrate company')">

        <p>Urgent Queue Time </p>
        <p>Today {{ urgentQueueTime.today }}</p>
        <p>Yesterday {{ urgentQueueTime.yesterday }}</p>
        <p>Last 7 Days {{urgentQueueTime.sevenDayAvg }}</p>
        </template>

        <div
            v-if="separateQueueIntegrations"
            v-show="$can('accept integrations') || $can('administrate company')"
            class="row"
        >
            <div
                v-for="integration in separateQueueIntegrations"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ integration.lead.id }}
                            {{ integration.lead.customer.full_name }} --
                            {{ integration.lead.customer.language }}
                        </h4>
                        <h5> Integrations </h5>
                    </div>
                    <div class="card-body">
                        <MazAvatar
                            :src="integration.requestingRep.gravatar"
                        />
                        <p>{{ integration.requestingRep.fullName }}</p>
                        <p>{{ integration.lead.office_name }}</p>
                        <p>{{ integration.id }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: integration.id } }"
                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">{{ integration.buttonName }}</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="separateQueueSp1"
            v-show="$can('accept sp1')"
            class="row"
        >
            <div
                v-for="sp1 in separateQueueSp1"
                class="col-md-6 col-sm-12"
            >
                <div
                    v-if="sp1.lead.office_id === getUser.data.office_id || $can('administrate company')"
                    class="card"
                >
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ sp1.lead.id }}
                            {{ sp1.lead.customer.full_name }} --
                            {{ sp1.lead.customer.language }}
                        </h4>

                        <p>SP1</p>
                    </div>
                    <div class="card-body">
                        <MazAvatar
                            :src="sp1.requestingRep.gravatar"
                        />
                        <p>{{ sp1.requestingRep.fullName }}</p>
                        <p>{{ sp1.lead.office_name }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: sp1.id } }"

                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">{{ sp1.buttonName }}</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div
            v-if="separateQueueSp2"
            v-show="$can('accept sp2')"
            class="row"
        >
            <div
                v-for="sp2 in separateQueueSp2"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ sp2.lead.id }}
                            {{ sp2.lead.customer.full_name }} --
                            {{ sp2.lead.customer.language }}
                        </h4>
                        SP2
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <MazAvatar
                                    :src="sp2.requestingRep.gravatar"
                                />
                                <p>{{ sp2.requestingRep.fullName }}</p>
                                <p>{{ sp2.lead.office_name }}</p>
                                <p v-if="sp2.lead.source === 'call center'">
                                    <span>☎</span> Call Center
                                </p>
                                <p v-else-if="sp2.lead.source === 'call center'">
                                    🐺 Call center Alpha
                                </p>
                                <p
                                    v-else
                                    class="text-capitalize"
                                >
                                    {{ sp2.lead.source }}
                                </p>
                            </div>
                            <div class="col">
                                <p>{{ sp2.lead.appointment }}</p>
                                <div
                                    v-if="sp2.urgent"
                                    style="color: red"
                                >
                                    <h3><strong>Urgent</strong></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: sp2.id } }"

                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">{{ sp2.buttonName }}</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="separateQueueCreditRunner"
            v-show="$can('accept credit runner') || $can('administrate company')"
            class="row"
        >
            <div
                v-for="creditRun in separateQueueCreditRunner"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ creditRun.lead.id }}
                            {{ creditRun.lead.customer.full_name }} --
                            {{ creditRun.lead.customer.language }}
                        </h4>
                        <p>Credit application</p>
                    </div>
                    <div class="card-body">
                        <MazAvatar
                            :src="creditRun.requestingRep.gravatar"
                        />
                        <p>{{ creditRun.requestingRep.fullName }}</p>
                        <p>{{ creditRun.lead.office_name }}</p>
                        <p v-if="creditRun.lead.source === 'call center'">
                            <span>☎</span> Call Center
                        </p>
                        <p
                            v-else
                            class="text-capitalize"
                        >
                            {{ creditRun.lead.source }}
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: creditRun.id } }"
                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">Run Credit</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="separateQueueProposalBuilder"
            v-show="$can('accept proposal builder') || $can('administrate company')"
            class="row"
        >
            <div
                v-for="pBQ in separateQueueProposalBuilder"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ pBQ.lead.id }}
                            {{ pBQ.lead.customer.full_name }} --
                            {{ pBQ.lead.customer.language }} -
                            {{ pBQ.lead.customer.state }}
                        </h4>
                        Proposal Request
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <MazAvatar
                                    :src="pBQ.requestingRep.gravatar"
                                />

                                <p>{{ pBQ.requestingRep.fullName }}</p>
                                <p>{{ pBQ.lead.office_name }}</p>
                                <p>Requested
                                    <TimeDisplay :targetDate="pBQ.created_at"/>
                                </p>

                                <p v-if="pBQ.lead.source === 'call center'">
                                    <span>☎</span> Call Center
                                </p>
                                <p v-else-if="pBQ.lead.source === 'call center'">
                                    🐺 Call center Alpha
                                </p>
                                <p
                                    v-else
                                    class="text-capitalize"
                                >
                                    {{ pBQ.lead.source }}
                                </p>
                            </div>

                            <div class="col">
                                <p>{{ pBQ.lead.appointment }}</p>
                                <div
                                    v-if="pBQ.urgent"
                                    style="color: red"
                                >
                                    <h3><strong>Urgent</strong></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: pBQ.id } }"

                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">{{ pBQ.buttonName }}</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="separateQueueSalesForceRunner"
            v-show="$can('accept sales force runner') || $can('administrate company')"
            class="row"
        >
            <div
                v-for="sunrunRunner in separateQueueSalesForceRunner"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ sunrunRunner.lead.id }}
                            {{ sunrunRunner.lead.customer.full_name }} --
                            {{ sunrunRunner.lead.customer.language }}
                        </h4>
                        <p>Sales Force Paperwork</p>
                    </div>
                    <div class="card-body">
                        <MazAvatar
                            :src="sunrunRunner.requestingRep.gravatar"
                        />
                        <p>{{ sunrunRunner.requestingRep.fullName }}</p>
                        <p>{{ sunrunRunner.lead.office_name }}</p>
                        <p v-if="sunrunRunner.lead.source === 'call center'">
                            <span>☎</span> Call Center
                        </p>
                        <p
                            v-else
                            class="text-capitalize"
                        >
                            {{ sunrunRunner.lead.source }}
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: sunrunRunner.id } }"
                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">Send Paperwork to Customer</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="separateQueueNts"
            v-show="$can('accept NTS') || $can('administrate company')"
            class="row"
        >
            <div
                v-for="nts in separateQueueNts"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ nts.lead.id }}
                            {{ nts.lead.customer.full_name }} --
                            {{ nts.lead.customer.language }}
                            - {{ nts.lead.customer.state }}
                        </h4>
                        {{ nts.type }}
                    </div>
                    <div class="card-body">
                        <MazAvatar
                            :src="nts.requestingRep.gravatar"
                        />
                        <p>{{ nts.requestingRep.fullName }}</p>
                        <p>{{ nts.lead.office_name }}</p>
                        <p v-if="nts.lead.source === 'call center'">
                            <span>☎</span> Call Center
                        </p>
                        <p v-else-if="nts.lead.source === 'call center'">
                            🐺 Call center Alpha
                        </p>
                        <p
                            v-else
                            class="text-capitalize"
                        >
                            {{ nts.lead.source }}
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: nts.id } }"

                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">Submit Paperwork</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="separateQueueD2D"
            v-show="$can('accept d2d call center') || $can('administrate company')"
            class="row"
        >
            <div
                v-for="d2d in separateQueueD2D"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ d2d.lead.id }}
                            {{ d2d.lead.customer.full_name }} --
                            {{ d2d.lead.customer.language }}
                        </h4>
                        SP1
                    </div>
                    <div class="card-body">
                        <MazAvatar
                            :src="d2d.requestingRep.gravatar"
                        />
                        <p>{{ d2d.requestingRep.fullName }}</p>
                        <p>{{ d2d.lead.office_name }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: d2d.id } }"

                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">{{ d2d.buttonName }}</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="separateQueueRoof"
            v-show="$can('accept roof assessor') || $can('administrate company')"
            class="row"
        >
            <div
                v-for="roof in separateQueueRoof"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ roof.lead.id }}
                            {{ roof.lead.customer.full_name }} --
                            {{ roof.lead.customer.language }}
                        </h4>
                        Roof Assessor
                    </div>
                    <div class="card-body">
                        <MazAvatar
                            :src="roof.requestingRep.gravatar"
                        />
                        <p>{{ roof.requestingRep.fullName }}</p>
                        <p>{{ roof.lead.office_name }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: roof.id } }"

                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">{{ roof.buttonName }}</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-if="separateQueueChangeOrder"
            v-show="$can('accept change order') || $can('administrate company')"
            class="row"
        >
            <div
                v-for="change in separateQueueChangeOrder"
                class="col-md-6 col-sm-12"
            >
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-capitalize">
                            {{ change.lead.id }}
                            {{ change.lead.customer.full_name }} --
                            {{ change.lead.customer.language }}
                            {{ change.lead.customer.state }}
                        </h4>
                        Change Order
                    </div>
                    <div class="card-body">
                        <MazAvatar
                            :src="change.requestingRep.gravatar"
                        />
                        <p>{{ change.requestingRep.fullName }}</p>
                        <p>{{ change.lead.office_name }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-between">
                            <div class="col-6">
                                <router-link
                                    :to="{ name:'Queue Assign', params: { queueId: change.id } }"

                                    class="btn btn-primary"
                                >
                                    <span class="pr-2">{{ change.buttonName }}</span>
                                    <i class="fas fa-plus-circle"/>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!queues[0]">
            <p>So empty</p>
        </div>
        <div class="div pt-5">
            <router-link
                :to="{ name:'Queue History' }"

                class="btn btn-primary"
            >
                <span class="pr-2">History</span>
                <i class="fas fa-plus-circle"/>
            </router-link>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import {Howl} from 'howler'
import TimeDisplay from "../../components/TimeDisplay.vue";

export default {
    name: 'LeadQueue',
    components: {TimeDisplay},

    data() {
        return {
            queues: null,
            load: false,
            permissions: window.permissions,
            counts: [],
            polling: null,
            urgentQueueTime: null
        }
    },

    created() {
        this.fetchUsers()
        this.getQueueTime()
    },

    mounted() {
        this.checkQueue()
        this.pollData()

        Echo.private('Queue')
            .listen('Backend.SalesFlow.Queue.ElevatorEvent', (e) => {
                console.log(e)
                if (e.type === 'sp1') {
                    const count = this.counts.sp1.count
                    this.counts.sp1.count = count + e.elevator
                }
                if (e.type === 'integrations') {
                    const count = this.counts.integrations.count
                    this.counts.integrations.count = count + e.elevator
                }
            }).listen('Backend.SalesFlow.Queue.QueuePageEvent', (e) => {
            console.log(e)
            if (e.direction === 'assigned') {
                this.queues.push(e.payload)

                this.choseSong(e)
            } else if (e.direction === 'filled') {
                const queueId = e.payload.id
                const newQueue = []
                switch (e.payload.type) {
                    case 'integrations':
                        $.each(this.queues, function (key, value) {
                            if (value.id !== queueId) {
                                newQueue.push(value)
                            }
                        })
                        this.queues = newQueue
                        break
                    case 'sp1':
                        console.log(e)
                        $.each(this.queues, function (key, value) {
                            if (value.id !== queueId) {
                                newQueue.push(value)
                            }
                        })
                        this.queues = newQueue
                        break
                }
            }
        })
    },
    methods: {
        ...mapActions(['fetchUsers']),

        getQueueTime() {
            axios.get('/api/salesflow/line/time-to-fill')
                .then((response) => {
                    // console.log(response)
                    this.urgentQueueTime = response.data
                })
                .catch(function (error) {
                    console.log(error)
                })
        },
        choseSong(e) {
            switch (e.payload.type) {
                case 'integrations':
                    if (this.$can('accept integrations')) {
                        this.playSound('/sounds/new-queue.mp3')
                    }
                    break
                case 'sp1':
                    if (this.$can('accept sp1')) {
                        this.playSound('/sounds/new-queue.mp3')
                    }
                    break
                case 'build_proposal':
                    if (this.$can('accept proposal builder')) {
                        if (e.payload.urgent) {
                            this.playSound('/sounds/urgent_request.mp3')
                        } else {
                            this.playSound('/sounds/new_proposal_sound_notification.mp3')
                        }
                    }
                    break
                case 'credit_app':
                    if (this.$can('accept credit runner')) {
                        this.playSound('/sounds/Scout-Credit-Check.mp3')
                    }
                    break
                case 'NTS':
                    if (this.$can('accept NTS')) {
                        this.playSound('/sounds/NTS.mp3')
                    }
                    break
                case 'sun_run_runner':
                    if (this.$can('accept sales force runner')) {
                        this.playSound('/sounds/Scout-E-Signatures.mp3')
                    }
                    break
                case 'd2d_call_center':
                    if (this.$can('accept d2d call center')) {
                        this.playSound('/sounds/new-queue.mp3')
                    }
                    break
                case 'roof':
                    if (this.$can('accept roof assessor')) {
                        this.playSound('/sounds/new-queue.mp3')
                    }
                    break
                case 'change_order':
                    if (this.$can('accept change order')) {
                        this.playSound('/sounds/new-queue.mp3')
                    }
                    break
            }
        },
        pollData() {
            this.polling = setInterval(() => {
                this.checkQueue()
            }, 60000)
        },

        playSound(file_path) {
            if (this.$route.fullPath === '/dashboard/lead/queue') {
                const sound = new Howl({
                    src: file_path,
                    volume: 0.30
                })
                sound.play()
            }
        },
        checkQueue: function () {
            axios.get('/api/salesflow/line')
                .then((response) => {
                    // console.log(response)
                    this.queues = response.data.data
                    this.load = true
                })
                .catch(function (error) {
                    console.log(error)
                })
        },
        checkCount: function () {
            axios.get('/api/salesflow/line/count?type=all')
                .then((response) => {
                    // console.log(response)
                    this.counts = response.data
                }).catch(function (error) {
                console.log(error)
            })
        },
        separateQueue(type) {
            return this.queuesComp.filter(val => val.type === type)
        }
    },

    computed: {
        ...mapGetters(['getUser']),

        queuesComp() {
            const permissions = this.permissions
            if (this.queues) {
                return this.queues.map((b) => {
                    switch (b.type) {
                        case 'integrations':
                            b.buttonName = 'Accept Integrations'
                            break
                        case 'sp1':
                            b.buttonName = 'Accept SP1'
                            break
                        case 'sp1 panic':
                            b.buttonName = 'Accept SP1'
                            break
                        case 'build_proposal':
                            b.buttonName = 'Build Proposal'
                            break
                        case 'credit_app':
                            b.buttonName = 'Create Credit App'
                            break
                        case 'send_paperwork':
                            b.buttonName = 'Send Paperwork'
                            break
                        case 'sp2':
                            b.buttonName = 'Sell Solar!'
                            break
                        case 'd2d_call_center':
                            b.buttonName = 'Accept SP1'
                            break
                        case 'roof':
                            b.buttonName = 'Accept Roof Assessor'
                            break
                        case 'change_order':
                            b.buttonName = 'Accept Change Order'
                            break
                        default:
                            b.buttonName = 'hmm'
                            break
                    }
                    return b
                })
            }
        },
        separateQueueD2D() {
            return this.queuesComp.filter(val => val.type === 'd2d_call_center')
        },
        separateQueueSalesForceRunner() {
            return this.queuesComp.filter(val => val.type === 'sun_run_runner')
        },
        separateQueueSp2() {
            return this.queuesComp.filter(val => val.type === 'sp2')
        },
        separateQueueCreditRunner() {
            return this.queuesComp.filter(val => val.type === 'credit_app')
        },
        separateQueueRoof() {
            return this.queuesComp.filter(val => val.type === 'roof')
        },
        separateQueueChangeOrder() {
            return this.queuesComp.filter(val => val.type === 'change_order')
        },

        separateQueueSp1() {
            return this.queuesComp.filter(val => val.type === 'sp1')
        },
        separateQueueIntegrations() {
            return this.queuesComp.filter(val => val.type === 'integrations')
        },
        // separateQueueCreditApp() {
        //     return this.queuesComp.filter(val => val.type === 'credit_app')
        // },
        separateQueueProposalBuilder() {
            return this.queuesComp.filter(val => val.type === 'build_proposal')
        },

        separateQueueNts() {
            return this.queuesComp.filter(val => val.type === 'NTS' || val.type === 'nts')
        },
        separateQueueSendPaperWork() {
            return this.queuesComp.filter(val => val.type === 'send_paperwork')
        }

    },
    beforeDestroy() {
        clearInterval(this.polling)
    }
}
</script>

<style scoped>

</style>
