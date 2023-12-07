<template>
    <div
        v-if="masterLoad"
        class="container"
    >
        <div class="border border-primary mx-4 px-3">
            <div class="h3 text-center">
                🌎 Filters
            </div>
            <div class="py-3">
                <user-office-select
                    :can-view-regions="true"
                    :can-view-offices="true"
                    :user-market-id="14"
                    :can-view-users="$can('view office')"
                    @officeNameChange="officeName = $event"
                    @officeChange="selectedOffice = $event"
                    @userChange="selectedUser = $event"
                    @userNameChange="selectedUserName = $event"
                    @selectedRegionId="regionId = $event"
                />
                <div
                    style="z-index: -1;"
                    class="py-1"
                >
                    <MazPicker
                        v-model="piePickerRangeValues"
                        placeholder="Select date range"
                        :no-time="true"
                        :formatted="'MMMM Do YYYY'"
                        range
                        :locale="'locale'"
                        format="YYYY-MM-DD"
                        @formatted="pickerRangeValuesFormatted = $event"
                        @validate="reRunAllGets()"
                    >
                        <i
                            slot="icon-left"
                            class="material-icons"
                        >
                            date_range
                        </i>
                    </MazPicker>
                </div>
            </div>
        </div>
        <div class="div">
            <!--        KPI-->
            <CreditPassWithAppointment

                :selected-region="regionId"
                class="my-4"
                :picker-range-values="piePickerRangeValues"
                :selected-office="selectedOffice"
                :date-range-formatted="pickerRangeValuesFormatted"
                :selected-office-name="officeName"
                :selected-user="selectedUser"
                :selected-user-name="selectedUserName"
            />
        </div>
        <!--King of the hill-->
        <closer-bar-chart

            :selected-region="regionId"
            class="my-4"
            :pie-picker-range-values="piePickerRangeValues"
            :selected-office="selectedOffice"
            :date-range-formatted="pickerRangeValuesFormatted"
            :selected-office-name="officeName"
            :selected-user-name="selectedUserName"
        />
        <CloseRatio
            v-if="$can('administrate company')"
            class="my-4"
            :selected-office-name="officeName"
            :selected-region="regionId"
            :selected-user-name="selectedUserName"
            :selected-office="selectedOffice"
            :selected-user="selectedUser"
            :date-range-formatted="pickerRangeValuesFormatted"
            :date-range="piePickerRangeValues"
        />

        <div>
            <SitRate
                v-if="$can('administrate company') "
                :selected-region="regionId"
                :selected-office-name="officeName"
                :selected-user-name="selectedUserName"
                :selected-office="selectedOffice"
                :selected-user="selectedUser"
                :date-range="piePickerRangeValues"
                :picker-range-values-formatted="pickerRangeValuesFormatted"
            />
        </div>
        <!--        <div v-if="$can('administrate company')">-->
        <!--            <OfficeCreditPassSitClose-->
        <!--                class="my-4"-->
        <!--                :selectedRegion="regionId"-->
        <!--                :pie-picker-range-values="piePickerRangeValues"-->
        <!--                :selected-office="selectedOffice"-->
        <!--                :dateRangeFormatted="pickerRangeValuesFormatted"-->
        <!--                :selected-office-name="officeName"-->
        <!--                :selectedUserName="selectedUserName"-->
        <!--            ></OfficeCreditPassSitClose>-->
        <!--        </div>-->
        <PostCloseRatio
            v-if="$can('administrate company')"
            class="my-4"
            :selected-office-name="officeName"
            :selected-region="regionId"
            :selected-user-name="selectedUserName"
            :selected-office="selectedOffice"
            :selected-user="selectedUser"
            :date-range-formatted="pickerRangeValuesFormatted"
            :date-range="piePickerRangeValues"
        />


                <lead-time-to-event
                    class="my-4"
                    :selectedRegion="regionId"
                    :pie-picker-range-values="piePickerRangeValues"
                    :selected-office="selectedOffice"
                    :dateRangeFormatted="pickerRangeValuesFormatted"
                    :selected-office-name="officeName"
                    :selectedUserName="selectedUserName"
                ></lead-time-to-event>

        <div style=" position:fixed; bottom:0; left:90px; width:100px; height:100px;">
            <MazBtn

                v-if="!isOpen"
                :icon-name="'filter_alt'"
                fab
                @click="isOpen = true"
            />
        </div>
    </div>
</template>

<script>
import BarChart from '../chart/BarChart'
import PieChart from '../chart/PieChart'
import UserOfficeSelect from '../Selects/UserOfficeSelect'
import axios from 'axios'
import { mapActions, mapGetters } from 'vuex'
import * as dayjs from 'dayjs'
import SitRate from './SitRate'
import CloseRatio from './CloseRatio'
import SitByUser from './SitByUser'
import CloserBarChart from './CloserBarChart'
import LeadTimeToEvent from './LeadTimeToEvent'
import PostCloseRatio from './PostCloseRatio'
import OfficeCreditPassSitClose from './OfficeCreditPass-Sit-Close'
import CreditPassWithAppointment from './CreditPassWithAppointment'

export default {
    name: 'Charts',
    components: {
        CreditPassWithAppointment,
        OfficeCreditPassSitClose,
        PostCloseRatio,
        LeadTimeToEvent,
        CloserBarChart,
        SitByUser,
        CloseRatio,
        SitRate,
        BarChart,
        PieChart,
        UserOfficeSelect
    },
    data () {
        return {
            isOpen: true,
            regionId: null,
            selectedUserName: null,
            officeName: null,
            sitDateStart: null,
            sitDateEnd: null,
            pieTotal: 69,
            primedOffice: null,
            pieClosedTotal: null,
            pieClosedRatio: {
                datasets: [
                    {
                        data: [1, 1, 1, 2],
                        backgroundColor: [
                            'green',
                            'red',
                            'blue',
                            'brown'
                        ]
                    }
                ],
                labels: ['Closed', 'JIJ', 'Credit Pass', 'Negotiating System']

            },
            pieClosedLoading: true,
            userLoaded: false,
            masterLoad: false,
            pickerRangeValuesFormatted: '',

            selectedPosition: 1,
            userList: null,
            selectedUser: null,
            selectedOffice: 0,
            officeList: null,

            selectedStatus: [1, 2],
            selectedStatusOptions: null,
            pieLoading: true,
            positionsLoading: true,
            pieRatio: {
                datasets: [
                    {
                        data: [1, 1, 1, 2],
                        backgroundColor: [
                            'green',
                            'red',
                            'blue',
                            'brown'
                        ]
                    }
                ],
                labels: ['Closed', 'JIJ', 'Credit Pass', 'Negotiating System']

            },
            piePickerRangeValues: {
                end: '',
                start: ''
            },
            positionPickerRangeValues: {
                end: '',
                start: ''
            },

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
    watch: {
        selectedUser () {
            this.reRunAllGets()
        },
        selectedOffice () {
            this.reRunAllGets()
        },
        regionId () {
            this.reRunAllGets()
        }
    },
    async mounted () {
        await this.setTimeRange()
        await this.fetchUsers()

        if (this.$can('administrate company')) {

        } else if (this.$can('manage region')) {
            this.regionId = this.getUser.data.office.region_id
        } else if (this.$can('view office commission')) {
            this.selectedOffice = this.getUser.data.office_id
        } else {
            this.selectedUser = this.getUser.data.id
        }

        // || !this.$can('manage region') ||
        // !this.$can('view office commission')
        this.reRunAllGets()
        this.masterLoad = true
        this.getOffice()
    },

    methods: {
        ...mapActions([
            'fetchUsers'
        ]),
        primeOffice () {
            this.selectedOffice = this.getUser.data.office_id
        },

        officeNameChanged (event) {
            this.officeName = event
        },
        userNameChanged (event) {
            this.selectedUserName = event
        },

        // getStatus() {
        //     axios.get(`/api/salesflow/lead/status`)
        //         .then((response) => {
        //             let options = response.data.data;
        //             let payload = [];
        //             $.each(options, function (key, value) {
        //                 let obj = {
        //                     label: value.name,
        //                     value: value.id
        //                 };
        //                 payload.push(obj)
        //             })
        //             this.selectedStatusOptions = payload;
        //             this.labelLoaded = true;
        //         })
        //         .catch(function (error) {
        //             console.log(error);
        //         });
        // },

        getOffice () {
            axios.get('/api/office')
                .then((response) => {
                    const officeList = response.data.data
                    const payload = []
                    $.each(officeList, function (key, value) {
                        const obj = {
                            label: value.name,
                            value: value.id
                        }
                        payload.push(obj)
                    })
                    this.officeList = payload
                    this.labelLoaded = true
                })
                .catch(function (error) {
                    console.log(error)
                })
        },

        getUsers () {
            axios.get(`/api/salesflow/user/office?office_id=${this.selectedOffice}`)
                .then((response) => {
                    const options = response.data.data
                    const payload = []
                    $.each(options, function (key, value) {
                        const obj = {
                            label: value.fullName,
                            value: value.id
                        }
                        payload.push(obj)
                    })
                    this.userList = payload
                    this.userLoaded = true
                })
                .catch(function (error) {
                    console.log(error)
                })
            this.userLoaded = true
        },

        // getAllPie() {
        //     this.selectedStatus = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21]
        //     this.getPie();
        // },
        // getPie() {
        //     this.pieLoading = true;
        //     let urlss = null;
        //
        //     if ((this.$can('administrate company') || this.$can('administrate office')) &&
        //         this.selectedUser === null) {
        //         urlss = `/api/salesflow/reporting/pie-ratio?type=office&office_id=${this.selectedOffice}`
        //     } else {
        //         urlss = `/api/salesflow/reporting/pie-ratio?type=rep&user_id=${this.selectedUser}`
        //     }
        //
        //
        //     axios.get(urlss,
        //         {
        //             params: {
        //                 'status': this.selectedStatus,
        //                 'timeRangeValues': this.piePickerRangeValues
        //             }
        //         })
        //         .then((response) => {
        //             this.pieRatio.datasets[0] = response.data.datasets;
        //             this.pieRatio.labels = response.data.labels;
        //             this.pieTotal = response.data.total
        //             this.pieLoading = false;
        //
        //         })
        //         .catch(function (error) {
        //             console.log(error);
        //         });
        //
        // },

        reRunAllGets () {
            this.getUsers()
            // this.getPie();
        },

        setTimeRange () {
            this.piePickerRangeValues.start = this.$dayjs().startOf('month').format('YYYY-MM-DD')
            this.piePickerRangeValues.end = this.$dayjs().format('YYYY-MM-DD')

            this.positionPickerRangeValues.start = this.$dayjs().startOf('month').format('YYYY-MM-DD')
            this.positionPickerRangeValues.end = this.$dayjs().format('YYYY-MM-DD')
        }
    },
    computed: {
        ...mapGetters([
            'getUser'
        ]),

        pieEmpty () {
            if (this.pieRatio.labels.length) {
                return false
            } else {
                return true
            }
        },

        valuesWithName () {

        },

        values () {
            let i = 0
            const array = []
            const labels = this.pieRatio.labels
            const total = this.pieTotal
            if (!this.loading && this.pieRatio.labels && this.labelLoaded && this.selectedStatusOptions) {
                $.each(this.pieRatio.datasets[0].data, function (key, value) {
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
    }

}
</script>

<style>
.small {
    max-width: 600px;
}

.box {
    text-align: justify;
    font-size: 20px;
}

.text {
    display: block;
    display: -webkit-box;
    max-width: 400px;
    height: 50px;
    margin: 0 auto;
    line-height: 1.2;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.wrapper {
    display: flex;
}

.float {
    float: right;
    height: 100%;
    display: flex;
    align-items: flex-end;
    shape-outside: inset(calc(100% - 100px) 0 0);
}
</style>
