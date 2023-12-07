<template>
    <div class="row">
        <div class="col-12">
            <div class="text-center">
                <h5>Sit Ratio</h5>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="row py-3" />
            <div class="text-center">
                <template v-if="!pieLoading && pieTotal">
                    <PieChart
                        v-if="!pieLoading"
                        :chartdata="pieSitRatio"
                        :options="options"
                        :style="`height: 400px`"
                    />

                    <template v-else>
                        <div class="text-center container">
                            No Data
                        </div>
                    </template>
                </template>
            </div>
            <div class="text-center">
                Total {{ pieTotal }}
            </div>
            <!--        <div class="row justify-content-between py-3" v-if="!positionsLoading">-->
            <!--            <div class="col-sm-6 col-md-4 py-1" v-for="value in values">-->
            <!--                {{ value.label }}<br> <strong>{{ value.percent }}%</strong>-->
            <!--            </div>-->
            <!--        </div>-->
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="pb-5">
                <div class="text-center pt-2">
                    <p>{{ pickerRangeValuesFormatted }}</p>
                </div>
                <h5>{{ selectedUserName }}</h5>
                <p><strong>{{ selectedOfficeName }}</strong></p>
                <ul>
                    <li>
                        Total per appointment within date range
                    </li>
                </ul>
                <div
                    v-if="pieRatioValues"
                    class="row py-3"
                >
                    <div
                        v-for="value in pieRatioValues"
                        class="col-sm-6 py-1 col-md-4"
                    >
                        {{ value.label }}<br> <strong>{{ value.percent }}%</strong>
                    </div>
                </div>
                <div class="row">
                    <MazBtn
                        class="maz-mr-2 maz-mb-2"
                        @click="getDetails()"
                    >
                        Details
                    </MazBtn>
                    <MazDialog
                        v-model="detailsModel"
                    >
                        <div slot="title">
                            Details
                        </div>

                        <template v-if="!detailsLoading">
                            <h5>Sits</h5>
                            <p v-for="(key, detail) in satDetails">
                                {{ key }} {{ detail }}
                                <MazBtn
                                    :size="'mini'"
                                    fab
                                    @click="openLead(detail)"
                                >
                                    <i class="fa fa-eye" />
                                </MazBtn>
                            </p>
                            <h5>Didn't sit</h5>
                            <p v-for="(key, detail) in didntSitDetails">
                                {{ key }} {{ detail }}
                                <MazBtn
                                    :size="'mini'"
                                    fab
                                    @click="openLead(detail)"
                                >
                                    <i class="fa fa-eye" />
                                </MazBtn>
                            </p>
                        </template>
                        <template v-else>
                            <MazLoading />
                        </template>
                    </MazDialog>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import PieChart from '../chart/PieChart'

export default {
    name: 'SitRate',
    components: { PieChart },
    props: {
        selectedUser: Number,
        selectedOffice: Number,
        dateRange: Object,
        pickerRangeValuesFormatted: String,
        selectedUserName: String,
        selectedOfficeName: String,
        selectedRegion: Number

    },
    data () {
        return {
            didntSitDetails: {},
            satDetails: {},
            detailsLoading: true,
            detailsModel: false,
            creditPass: true,
            values: null,
            pieLoading: true,
            pieTotal: 0,

            pieSitRatio: {
                datasets: [
                    {
                        data: [9, 1],
                        backgroundColor: [
                            'green',
                            'red'
                        ]
                    }
                ],
                labels: ['Sat', 'Didn\'t Sit']

            },
            options: {
                maintainAspectRatio: false,
                animation: {
                    duration: 0
                },
                hover: {
                    animationDuration: 0
                }
            }
        }
    },
    computed: {
        pieRatioValues () {
            let i = 0
            const array = []
            const labels = this.pieSitRatio.labels
            const total = this.pieTotal
            if (!this.loading && this.pieSitRatio.labels) {
                $.each(this.pieSitRatio.datasets[0].data, function (key, value) {
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
        selectedOffice: function (newVal, oldVal) {
            this.getPie()
        },
        dateRange: function (newVal, oldVal) {
            this.getPie()
        },
        selectedUser: function (newVal, oldVal) {
            this.getPie()
        },
        selectedRegion: function () {
            this.getPie()
        }
    },
    created () {
        this.getPie()
    },
    methods: {

        openLead: function (id) {
            const routeData = this.$router.resolve({ path: `/dashboard/lead/${id}` })
            window.open(routeData.href, '_blank')
        },

        getDetails () {
            this.detailsModel = true
            this.detailsLoading = true

            let urlss = null

            if ((this.$can('administrate company') || this.$can('administrate office')) &&
                this.selectedUser === null) {
                urlss = '/api/salesflow/reporting/sit-ratio-detailed'
            } else {
                urlss = '/api/salesflow/reporting/sit-ratio-detailed'
            }

            axios.post(urlss,
                {
                    params: {
                        office_id: this.selectedOffice,
                        region_id: this.selectedRegion,
                        user_id: this.selectedUser,
                        status: this.selectedStatus,
                        timeRangeValues: this.dateRange,
                        creditpass: this.creditPass

                    }
                })
                .then((response) => {
                    this.didntSitDetails = response.data.didntSit
                    this.satDetails = response.data.sat
                    this.detailsLoading = false
                })
                .catch(function (error) {
                    console.log(error)
                })
        },
        getPie () {
            this.pieLoading = true

            axios.post('/api/salesflow/reporting/sit-ratio',
                {
                    params: {
                        user_id: this.selectedUser,
                        office_id: this.selectedOffice,
                        region_id: this.selectedRegion,
                        status: this.selectedStatus,
                        timeRangeValues: this.dateRange,
                        creditpass: this.creditPass
                    }
                })
                .then((response) => {
                    console.log(response.data, 'sit pie')
                    this.pieSitRatio.datasets[0] = response.data.datasets
                    this.pieSitRatio.labels = response.data.labels
                    this.pieTotal = response.data.total
                    this.pieLoading = false
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
