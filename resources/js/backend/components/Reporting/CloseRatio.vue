<template>
    <div class="row pt-5">
        <div class="col-12">
            <div class="text-center">
                <h5>Close Ratio</h5>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <template v-if="!pieClosedLoading && pieClosedTotal">
                <PieChart
                    v-if="!pieClosedLoading"
                    :chartdata="pieClosedRatio"
                    :options="options"
                    :style="`height: 400px`"
                />
            </template>
            <template v-else>
                <MazLoader />
            </template>
        </div>

        <div class="col-md-6 col-sm-12">
            <div>
                <div class="text-center pt-2">
                    <p>{{ dateRangeFormatted }}</p>
                </div>
                <h5>{{ selectedUserName }}</h5>
                <p><strong>{{ selectedOfficeName }}</strong></p>
                <MazSelect
                    v-model="type"
                    class="py-3"
                    :options="optionsLabel"
                    :size="'sm'"
                    placeholder="Position"
                    color="info"
                    @input="getPieClosed()"
                >
                    <i
                        slot="icon-left"
                        class="material-icons"
                    >
                        add_circle_outline
                    </i>
                </MazSelect>
                <MazSelect
                    v-model="source"
                    class="py-3"
                    :options="sourceList"
                    :size="'sm'"
                    placeholder="Source"
                    color="info"
                    @input="getPieClosed()"
                >
                    <i
                        slot="icon-left"
                        class="material-icons"
                    >
                        add_circle_outline
                    </i>
                </MazSelect>
                <p>
                    Total {{ pieClosedTotal }}
                </p>
            </div>

            <div
                v-if="!pieClosedLoading"
                class="row py-3"
            >
                <div
                    v-for="value in pieClosedValues"
                    class="col-sm-6 py-1 col-md-4"
                >
                    {{ value.label }}<br> <strong>{{ value.percent }}%</strong>
                </div>
            </div>
            <ul>
                <!--                <li>-->
                <!--                    <strong>Qualified</strong> is any lead that has passed credit-->
                <!--                </li>-->
                <!--                <li>-->
                <!--                    <strong>Closed</strong> is any closed lead not in Jeopardy-->
                <!--                </li>-->
                <!--                <li>-->
                <!--                    <strong>Lost</strong> is any qualified that we have Lost-->
                <!--                </li>-->
            </ul>
            <div>
                <MazBtn
                    class="maz-mr-2 maz-mb-2"
                    @click="detailsModel = true"
                >
                    Details
                </MazBtn>
                <MazDialog
                    v-model="detailsModel"
                >
                    <div slot="title">
                        Details
                    </div>
                    <div>
                        <MazSelect
                            v-model="selectedType"
                            :options="detailsOptions"
                        />
                    </div>
                    <div>
                        <div
                            v-for="d in selectedDetail"
                            class="pt-3"
                        >
                            LeadId: {{ d.leadId }}
                            <MazBtn
                                :size="'mini'"
                                fab
                                @click="openLead(d.leadId)"
                            >
                                <i class="fa fa-eye" />
                            </MazBtn>
                            <br>
                            Customer Name: {{ d.customerName }}
                            <br>
                        </div>
                    </div>
                </MazDialog>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import PieChart from '../chart/PieChart'

export default {
    name: 'CloseRatio',
    components: { PieChart },
    props: {
        selectedUser: Number,
        selectedOffice: Number,
        dateRange: Object,
        dateRangeFormatted: String,
        selectedUserName: String,
        selectedOfficeName: String,
        selectedRegion: Number
    },
    data () {
        return {
            details: {},
            sourceList: [
                { value: null, label: 'All Sources' },
                { value: 'self-gen', label: 'Self Gen' },
                { value: 'call-center', label: 'Call Center' },
                { value: 'canvasser', label: 'Canvasser' }
            ],
            source: null,
            selectedType: null,
            optionsLabel: [
                { label: 'From Sits', value: 'sits-to-close' },
                { label: 'From Credit Passes', value: 'credit-pass-to-close' },
                { label: 'From Appointments', value: 'appointments-to-close' }
            ],
            detailsModel: false,
            pieTotal: 69,
            creditOnly: false,
            pieClosedTotal: null,
            pieClosedRatio: {
                datasets: [
                    {
                        data: [1, 1, 1, 2],
                        backgroundColor: [
                            'green',
                            'blue',
                            'red',
                            'brown'
                        ]
                    }
                ],
                labels: []
            },
            detailsOptions: [
                { label: 'Select One', value: null },
                { label: 'Closed', value: 'closed' },
                { label: 'Qualified', value: 'qualified' },
                { label: 'Lost', value: 'Lost' }
            ],
            pieClosedLoading: true,
            type: 'sits-to-close',

            pieLoading: true,
            positionsLoading: true,

            options: {
                maintainAspectRatio: false,
                animation: {
                    duration: 0
                },
                hover: {
                    animationDuration: 0
                }
            },
            total: 0,
            labelLoaded: false
        }
    },
    computed: {
        selectedDetail () {
            switch (this.selectedType) {
                case 'lost':
                    return this.details.lost
                case 'closed':
                    return this.details.closed
                case 'qualified':
                    return this.details.qualified
                default:
                    return null
            }
        },
        pieClosedValues () {
            let i = 0
            const array = []
            const labels = this.pieClosedRatio.labels
            const total = this.pieClosedTotal
            if (!this.loading && this.pieClosedRatio.labels && this.pieClosedRatio.labels) {
                $.each(this.pieClosedRatio.datasets[0].data, function (key, value) {
                    const percent = (value / total) * 100
                    const temp = {
                        label: labels[i],
                        percent: percent.toFixed(1)
                    }
                    i++
                    array.push(temp)
                })
            }

            return array
        }
    },
    watch: {
        creditOnly: function (newVal, oldVal) {
            this.getPieClosed()
        },

        selectedOffice: function (newVal, oldVal) {
            this.getPieClosed()
        },
        dateRange: function (newVal, oldVal) {
            this.getPieClosed()
        },
        selectedUser: function (newVal, oldVal) {
            this.getPieClosed()
        },
        selectedRegion: function (newVal, oldVal) {
            this.getPieClosed()
        }
    },
    created () {
        this.getPieClosed()
    },
    methods: {
        openLead: function (id) {
            const routeData = this.$router.resolve({ path: `/dashboard/lead/${id}` })
            window.open(routeData.href, '_blank')
        },
        getPieClosed () {
            this.pieClosedLoading = true
            let urlss = null
            const param = {}
            urlss = '/api/salesflow/reporting/closed-ratio'

            axios.post(urlss,
                {
                    params: {
                        office_id: this.selectedOffice,
                        user_id: this.selectedUser,
                        region_id: this.selectedRegion,
                        dateRange: this.dateRange,
                        type: this.type,
                        source: this.source
                    }
                })
                .then((response) => {
                    this.details = response.data.details
                    this.pieClosedRatio.datasets[0] = response.data.datasets
                    this.pieClosedRatio.labels = response.data.labels
                    this.pieClosedTotal = response.data.total
                    this.pieClosedLoading = false
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
