<template>
    <div class="container-fluid">
        <div class="row">
            <div class="">
                <p
                    v-if="!customerPhone"
                    style="color: red"
                >
                    Invalid Cell phone number
                </p>
                <p
                    v-if="!validCustomerName"
                    style="color: red"
                >
                    Please Update the First Name
                </p>
                <MazBtn
                    :disabled="!customerPhone && !validCustomerName"
                    @click="openDialog"
                >
                    Schedule RR
                </MazBtn>
                <MazDialog
                    :input="bookOpenDialog"
                    :value="bookOpenDialog"
                    :fullsize="true"
                    :no-close="true"
                >
                    <div
                        slot="title"
                        class="text-white"
                    >
                        <h4>What time?</h4>
                    </div>
                    <div slot="default">
                        <div class="d-flex justify-content-center">
                            <div>
                                <MazBtn
                                    v-if="daysOut > 1"
                                    :loading="loading"
                                    :icon-name="'remove_circle_outline'"
                                    fab
                                    @click="changeDay(-1)"
                                />
                            </div>
                            <div class="text-center px-3">
                                <p>Days Out</p>
                                <h2> {{ daysOut }}</h2>
                                <template v-if="appointments.length > 0">
                                    <h4> {{ $date(appointments[0].start_time ).format("MM-DD") }}</h4>
                                    <h5> {{ $date(appointments[0].start_time ).format("dddd") }}</h5>
                                </template>
                            </div>
                            <div>
                                <MazBtn
                                    :icon-name="'add_circle_outline'"
                                    :loading="loading"
                                    fab
                                    @click="changeDay(1)"
                                />
                            </div>
                        </div>

                        <div
                            v-if="!loading && AppointmentCards.length"
                            class="row pt-4"
                        >
                            <div
                                v-for="available in appointments"
                                class="col-md-4 col-sm-6"
                            >
                                <div class="card">
                                    <div class="card-header">
                                        <h5> {{ available.startTime }} </h5>
                                        <p>{{ timeZones }}</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="pb-2">
                                            <p>Available slots {{ available.count }}</p>

                                            <p
                                                v-if="available.preferred"
                                                style="color:green"
                                            >
                                                Preferred
                                            </p>
                                        </div>
                                        <MazBtn
                                            :loading="btnLoading"
                                            @click="bookAppointment(available)"
                                        >
                                            Book Appointment
                                        </MazBtn>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="!appointments.length && !loading">
                            <h5>Sorry no one is available today, try another day or remote.</h5>
                            <!--                            <template v-if="userId !== 91">-->
                            <!--                                <a href="https://msgsndr.com/widget/booking/6H8DRyxO1aMCyTdNWPUw" target="_blank">Book-->
                            <!--                                    Appointment</a>-->
                            <!--                                <BookSp2-->
                            <!--                                    :user-id="3"-->
                            <!--                                    :lead-id="leadId"-->
                            <!--                                    :canbookOwn="true"-->
                            <!--                                    :booking-type="9"-->
                            <!--                                    @appointment="appointmentBooked($event)">-->
                            <!--                                </BookSp2>-->
                            <!--                            </template>-->
                        </div>
                        <div v-else>
                            <MazLoader />
                        </div>
                    </div>

                    <div slot="footer">
                        <MazBtn
                            :color="'danger'"
                            @click="bookOpenDialog = false"
                        >
                            Close
                        </MazBtn>
                    </div>
                </MazDialog>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import dayjs from 'dayjs'

export default {
    name: 'RoundRobinBookingOffice',
    props: [
        'userId',
        'leadId',
        'customerPhone',
        'validCustomerName',
        'onActiveCount'
    ],
    data () {
        return {
            activeStep: 1,
            appointments: [1],
            appointmentTime: null,
            bookOpenDialog: false,
            loading: true,
            disableModel: false,
            btnLoading: false,
            daysOut: 1
        }
    },
    computed: {
        AppointmentCards () {
            return this.appointments.map((b) => {
                b.startTime = this.$date(b.start_time).format('MM/DD h:mm a')

                return b
            })
        },
        timeZones () {
            const d = new Date()
            return /\(([^)]+)\)/.exec(d.toTimeString())[1]
        },

        // validCustomerNumber() {
        //     if (this.customerPhone) {
        //         if (this.customerPhone.length === 10 || this.customerPhone.length === 12) {
        //             return true
        //         }
        //     }
        //     return false;
        // },
        groupedCount () {
            return this.appointments.length
        },
        stepperSteps () {
            const step = this.appointments.length / 12
            return parseInt(step.toFixed(0))
        },
        showAppointments () {
            let i = 0
            let tempArray
            const payloadArray = []
            let stepArray = 0
            for (i = 0; i < this.appointments.length; i += 12) {
                tempArray = this.appointments.slice(i, i + 12)
                payloadArray.push(tempArray)
            }

            stepArray = this.activeStep - 1
            return payloadArray[stepArray]
        }

    },
    watch: {
        onActiveCount: function (newValue, oldValue) {
            if (this.bookOpenDialog === true) {
                this.getQueue()
            }
        }
    },
    created () {
        // this.getAppointments();
    },
    methods: {

        changeDay (change) {
            this.daysOut += change
            this.getAppointments()
        },
        appointmentBooked (event) {
            this.$emit('appointment', event)
            this.bookOpenDialog = false
            this.getAppointments()
        },
        bookAppointment (time) {
            const data = {
                remote: this.remoteClose,
                start_time: time.start_time,
                userId: time.userId,
                userList: time.userList,
                backUpPreferred: time.backUpPreferred

            }
            this.btnLoading = true
            axios.post(`/api/salesflow/lead/${this.leadId}/appointment/available`, data)
                .then((response) => {
                    console.log(response)
                    this.bookOpenDialog = false
                    this.loading = false
                    this.btnLoading = false

                    if (response.data.taken === true) {
                        Swal.fire({
                            type: 'warning',
                            title: 'Time is Already taken',
                            text: 'Sorry that time is no longer available, please try another time.',
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Good Luck'

                        })
                    } else {
                        this.$emit('appointment', response.data.data)
                    }
                })
        },
        openDialog () {
            this.bookOpenDialog = true
            this.loading = true
            this.getAppointments()
        },
        getAppointments () {
            this.loading = true
            axios.get(`/api/salesflow/lead/${this.leadId}/appointment/available-office?day=${this.daysOut}`)
                .then((response) => {
                    this.appointments = response.data
                    this.loading = false
                })
        },


    }
}
</script>

<style scoped>

</style>
