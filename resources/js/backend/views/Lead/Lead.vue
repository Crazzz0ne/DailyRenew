<template>
    <div v-if="$can('view lead')">
        <div
            v-if="!loading && epc"
            class="card"
        >
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <h2>{{ lead.id }} {{ lead.customer.first_name }}</h2>
                    </div>
                    <div class="col-sm-12 col-md-4 py-sm-3">
                        <!--            <switch-e-p-c-->
                        <!--              :lead="lead"-->
                        <!--              @changelead="fetchLead"-->
                        <!--            />-->
                    </div>
                    <div class="col-sm-12 col-md-3  pt-sm-3 text-center">
                        <template  v-if="$can('administrate company')">
                            <MazSelect
                                placeholder="Status"
                                :options="compStatusList"
                                v-model="lead.status_id"
                                @blur="updateStatus"
                            >
                            </MazSelect>
                        </template>
                        <template v-else>
                            <h3>Status</h3>
                            <h4 class="text-capitalize text-black">
                                {{ lead.status }}
                            </h4>

                        </template>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-sm-12 order-md-2">
                        <div class="card">
                            <div class="card-body">
                                <div
                                    class="row justify-content-end"
                                    style="fontSize:12px;"
                                >
                                    <div class="col">
                                        <p class="text-capitalize">
                                            Office Name: <strong>{{ lead.office_name }} </strong>
                                        </p>
                                    </div>
                                    <div
                                        v-if="lead.origin_office_id"
                                        class="col"
                                    >
                                        <p class="text-capitalize">
                                            Origin Office: <strong>{{ lead.origin_office_name }} </strong>
                                        </p>
                                    </div>
                                    <div class="col">
                                        <p class="text-capitalize">
                                            Source: <strong>{{ lead.source }}</strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="row justify-content-md-between">
                                    <div
                                        v-if="lead.requiredIntegrations"
                                        class="col-12 py-3 mb-3"
                                    >
<!--                                        <SelfGenIntegration-->
<!--                                            :on-active-count="onActiveCount"-->
<!--                                            :lead="lead"-->
<!--                                            :integrator="integrator"-->
<!--                                            :user="getUser.data"-->
<!--                                            :status="lead.status"-->
<!--                                        />-->
                                    </div>
                                    <div class="col-12 buttons-container">
                                        <MazBtn
                                            :color="'warning'"
                                            fab
                                            @click="openMaps"
                                        >
                                            <i class="fas fa-directions"/>
                                        </MazBtn>
                                        <div
                                            v-if="($can('edit JIJ') && lead.closedAt === null)
                        || $can('edit system') || $can('administrate company')"
                                        >
                                            <Jeopardy
                                                :jeopardy_id="lead.jeopardy_id"
                                                :user-id="getUser.data.id"
                                                :lead-id="lead.id"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <!--   Customer area-->
                                    <div class="row justify-content-between">
                                        <div class="col-12">
                                            <div class="separator my-3">
                                                <strong>Customer</strong>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <MazTransitionExpand>
                                                <div
                                                    v-show="toggleCustomer"
                                                >
                                                    <Customer
                                                        :user="getUser.data"
                                                        :on-active-count="onActiveCount"
                                                        :valid-customer-number="validCustomerNumber"
                                                        :lead="lead"
                                                        :epc-credit-status="epc.creditStatus"
                                                        :editing="true"
                                                        @customer="customer = $event"
                                                        @update-credit="updateLead($event)"
                                                        @hide="toggleCustomer = false"
                                                    />
                                                </div>
                                            </MazTransitionExpand>
                                            <div
                                                v-show="!toggleCustomer"
                                                class="text-center"
                                            >
                                                <MazBtn
                                                    :icon-name="'add'"
                                                    fab
                                                    @click="toggleCustomer = true"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row justify-content-between">
                                        <!--   Utility area-->
                                        <div class="col-sm-12">
                                            <div class="separator my-3">
                                                <strong>Utility</strong>
                                            </div>
                                            <MazTransitionExpand>
                                                <div
                                                    v-show="toggleUtility"
                                                >
                                                    <Utility
                                                        :editing="true"
                                                        :lead="lead"
                                                        :epc-power="epc.powerCompany"
                                                        @utility="utility = $event"
                                                        @update-integrations-status="updateLead($event)"
                                                        @hide="toggleUtility = false"
                                                    />
                                                </div>
                                            </MazTransitionExpand>
                                            <div
                                                v-show="!toggleUtility"
                                                class="text-center"
                                            >
                                                <MazBtn
                                                    :icon-name="'add'"
                                                    fab
                                                    @click="toggleUtility = true"
                                                />
                                            </div>
                                        </div>
                                        <!--   Proposal/System area-->
                                        <template
                                            v-if="($can('view request-proposed'))"
                                        >
                                            <template
                                                v-if="lead.status === 'Change Order' || lead.status === 'New Lead'
                          || lead.status === 'Pending Credit Check' || lead.status === 'Credit Pass' || lead.status === 'Negotiating System'"
                                            >
                                                <!-- RequestedSystem -->
                                                <div class="col-12-md col-sm-12">
                                                    <div class="separator my-3">
                                                        <strong>Requested System</strong>
                                                    </div>
                                                    <MazTransitionExpand>
                                                        <div v-show="toggleRequestedSystem">
                                                            <RequestedSystem
                                                                :on-active-count="onActiveCount"
                                                                :current-usage="utility.kw_year_usage"
                                                                :lead="lead"
                                                                :solar-rate-array="solarRateArray"
                                                                :epc-adders="epc.adders"
                                                                :epc-systems="epc.systems"
                                                                :finance-options="financeOptions"
                                                                :modules="modulesOptions"
                                                                :inverters="inverterOptions"
                                                                @showProposedSystem="showProposedSystem = true"
                                                                @hide="toggleRequestedSystem = false"
                                                            />
                                                        </div>
                                                    </MazTransitionExpand>

                                                    <div
                                                        v-show="!toggleRequestedSystem"
                                                        class="text-center"
                                                    >
                                                        <MazBtn
                                                            :icon-name="'add'"
                                                            fab
                                                            @click="toggleRequestedSystem = true"
                                                        />
                                                    </div>
                                                </div>
                                                <!--Proposed System-->
                                                <div
                                                    v-show="showProposedSystem"
                                                    class="col-12-md col-sm-12"
                                                >
                                                    <div class="separator my-3">
                                                        <strong>Proposed System</strong>
                                                    </div>
                                                    <ProposedSystem
                                                        v-show="toggleProposedSystem"
                                                        :on-active-count="onActiveCount"
                                                        :lead="lead"
                                                        :utility="utility"
                                                        :customer="customer"
                                                        :solar-rate-array="solarRateArray"
                                                        :user-id="getUser.data.id"
                                                        :user="getUser.data"
                                                        :epc-adders="epc.adders"
                                                        :epc-systems="epc.systems"
                                                        :modules="modulesOptions"
                                                        :inverters="inverterOptions"
                                                        :finance-options="financeOptions"
                                                        :market="lead.market_name"
                                                        @show-me="showProposedSystem = $event"
                                                        @closed="closedActive = 1"
                                                        @hide="toggleProposedSystem = false"
                                                        @active-draw="onActiveCount++"
                                                    />
                                                    <div
                                                        v-if="!toggleProposedSystem"
                                                        class="text-center"
                                                    >
                                                        <MazBtn
                                                            :icon-name="'add'"
                                                            fab
                                                            @click="toggleProposedSystem = true"
                                                        />
                                                    </div>
                                                </div>
                                            </template>
                                            <!--System-->
                                            <div
                                                v-if="lead.system_id"
                                                class="col-6-lg col-sm-12"
                                            >
                                                <div class="separator my-3">
                                                    <strong>System</strong>
                                                </div>
                                                <System
                                                    :on-active-count="onActiveCount"
                                                    :editing="false"
                                                    :lead="lead"
                                                    :average-bill="utility.average_bill"
                                                    :inverters="inverterOptions"
                                                    :modules="modulesOptions"
                                                    :epc-systems="epc.systems"
                                                    :epc-adders="epc.adders"
                                                    :user-id="getUser.data.id"
                                                    :finance-options="financeOptions"
                                                    @newSystem="lead.system = $event"
                                                />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 order-md-1">
                        <div class="card justify-content-between">
                            <div class="card-body">
                                <h3 class="text-center py-3">
                                    Project Coordinator
                                </h3>
                                <MazTransitionExpand>
                                    <p-j-card
                                        v-show="toggleProjectCoord"
                                        :on-active-count="onActiveCount"
                                        :closed-active="closedActive"
                                        :lead="lead"
                                        :user-id="getUser.data.id"
                                        :state="customer.state"
                                        :system="leadSystem.finance_id"
                                        :close-appointment="closeAppointment"
                                        @showProposedSystem="showProposedSystem = true"
                                        @appointment="lead.appointments.push($event)"
                                        @hide="toggleProjectCoord = false"
                                    />
                                </MazTransitionExpand>
                                <div
                                    v-show="!toggleProjectCoord"
                                    class="text-center"
                                >
                                    <MazBtn
                                        :icon-name="'add'"
                                        fab
                                        @click="toggleProjectCoord = true"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <custom-tabs-header
                    class="py-3"
                    :items="lowerTabs"
                />
                <MazTabsContent>
                    <MazTabsContentItem>
                        <div class="container py-5">
                            <Note
                                v-if="lead.id && getUser.data.id"
                                :is-new-lead="false"
                                :lead-id="lead.id"
                                :user="getUser.data"
                            />
                        </div>
                    </MazTabsContentItem>
                    <MazTabsContentItem>
                        <div class="form-group align-content-center text-center py-5">
                            <div class="card">
                                <div class="card-body">
                                    <DropZone
                                        v-if="lead.id && getUser.data.id"
                                        :lead-id="lead.id"
                                        :user-id="getUser.data.id"
                                    />
                                    <ShowUpload
                                        v-if="lead.id && getUser.data.id"
                                        :lead-id="lead.id"
                                        :user-id="getUser.data.id"
                                        :on-active-count="onActiveCount"
                                        :can-sales-rep="canSalesRep"
                                        :can-sp1="canSp1"
                                        :can-sp2="canSp2"
                                        :can-build="builder"
                                    />
                                </div>
                            </div>
                        </div>
                    </MazTabsContentItem>
                    <MazTabsContentItem>
                        <RepList
                            v-if="lead.reps"
                            class="py-5"
                            :on-active-count="onActiveCount"
                            :lead-id="lead.id"
                            :source="lead.source"
                            :office-id="lead.office_id"
                            @reps="lead.reps = $event"
                        />
                    </MazTabsContentItem>
                    <MazTabsContentItem>
                        <h4 class="py-5 text-center">
                            Appointments
                        </h4>
                        <div class="row justify-content-center py-3 mx-3">
                            <div v-if="canBookClose" class="px-3">
                                <template v-if="lead.source === 'call center' || lead.source === 'external' || lead.source === 'D2D'">
                                    <BookRoundRobin
                                        :on-active-count="onActiveCount"
                                        :user-id="getUser.data.id"
                                        :lead-id="lead.id"
                                        :customer-phone="validCustomerNumber"
                                        :valid-customer-name="validCustomerName"
                                        @appointment="lead.appointments.push($event)"
                                    />
                                </template>
                                <template v-else>
                                    <div class="row">
                                        <template v-if="lead.office_id === 5">
                                            <div class="col">
                                                <RoundRobinBookingOffice
                                                    :on-active-count="onActiveCount"
                                                    :user-id="getUser.data.id"
                                                    :lead-id="lead.id"
                                                    :customer-phone="validCustomerNumber"
                                                    :valid-customer-name="validCustomerName"
                                                    @appointment="lead.appointments.push($event)"
                                                />
                                            </div>
                                        </template>
                                        <div class="col">
                                            <BookSp2
                                                :user-id="getUser.data.id"
                                                :lead-id="lead.id"
                                                :canbook-own="canSp2"
                                                :booking-type="6"
                                                @appointment="lead.appointments.push($event)"
                                            />
                                        </div>
                                    </div> <!-- Closing the row div -->
                                </template>
                            </div>
                        </div>
                        <div class="px-3">
                            <CreateEvent
                                :lead-id="lead.id"
                                @appointment="lead.appointments.push($event)"
                            />
                            <!--                <BookSp2-->
                            <!--                  :user-id="getUser.data.id"-->
                            <!--                  :lead-id="lead.id"-->
                            <!--                  :canbook-own="true"-->
                            <!--                  :booking-type="7"-->
                            <!--                  @appointment="lead.appointments.push($event)"-->
                            <!--                />-->
                        </div>

            <LeadAppointmentList

                v-if="lead.appointments"
                class="py-5"
                :appointments="lead.appointments"
                :office-id="lead.office_id"
            />
            </MazTabsContentItem>
            </MazTabsContent>
        </div>
    </div>
    <div
        v-else-if="lead === undefined"
        class="text-center"
    >
        <iframe
            width="560"
            height="315"
            src="https://www.youtube.com/embed/3xYXUeSmb-Y"
            frameborder="0"
            allow="accelerometer;  clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
        />
    </div>
    <div v-else>
        <div class="text-center">
            <MazLoader/>
            <h3>Loading</h3>
        </div>
    </div>
    </div>
</template>

<script>

import Note from '../../components/lead/note/Note'
import ShowUpload from '../../components/lead/upload/OldShowUpload'
import AppointmentList from '../../components/lead/appointment/AppointmentList'
import LeadAppointmentList from '../../components/lead/appointment/LeadAppointmentList'
import RepList from '../../components/lead/reps/RepList'

import {mapActions, mapGetters} from 'vuex'
import FileUpload from '../../components/lead/upload/FileUpload'
import SelfGenIntegration from '../../components/lead/reps/SelfGenIntegration'

import PJCard from '../../components/lead/ProjectCoordinator/PJCard'
import RequestedSystem from '../../components/lead/proposal/RequestedSystem'
import System from '../../components/lead/proposal/System'
import DropZone from '../../components/lead/upload/DropZone'
import BookSp2 from '../../components/lead/appointment/BookSp2'
import LeadNeededNext from '../../components/lead/status/LeadNeededNext'
import SwitchEPC from '../../components/lead/SwitchEPC'
import Jeopardy from '../../components/lead/jnj/Jeopardy'
import ProposedSystem from '../../components/lead/proposal/ProposedSystem'
import Customer from '../../components/lead/customer/Customer'

import Utility from '../../components/lead/Utility/Utility'
import BookRoundRobin from '../../components/lead/appointment/BookRoundRobin'
import CreateEvent from '../../components/Calender/CreateEvent'
import RoundRobinBookingOffice from "../../components/RoundRobin/RoundRobinBookingOffice.vue";

export default {
    name: 'Lead',
    components: {
        RoundRobinBookingOffice,
        CreateEvent,
        BookRoundRobin,
        Utility,
        Customer,
        ProposedSystem,
        Jeopardy,
        SwitchEPC,
        BookSp2,
        LeadAppointmentList,
        DropZone,
        System,
        RequestedSystem,
        PJCard,
        SelfGenIntegration,
        RepList,
        ShowUpload,
        Note
    },
    data() {
        return {
            leadSystem: {finance_id: null},
            appointmentClose: {remote: false},
            closedActive: 0,
            onActiveCount: 0,
            tempUpload: null,
            statusList: [],
            solarRateArray: [
                {label: '$0.13', value: 0.13},
                {label: '$0.135', value: 0.135},
                {label: '$0.14', value: 0.14},
                {label: '$0.145', value: 0.145},
                {label: '$0.15', value: 0.15},
                {label: '$0.155', value: 0.155},
                {label: '$0.16', value: 0.16},
                {label: '$0.165', value: 0.165},
                {label: '$0.17', value: 0.17},
                {label: '$0.175', value: 0.175},
                {label: '$0.18', value: 0.18},
                {label: '$0.185', value: 0.185},
                {label: '$0.19', value: 0.19},
                {label: '$0.195', value: 0.195},
                {label: '$0.20', value: 0.20},
                {label: '$0.205', value: 0.205},
                {label: '$0.21', value: 0.21},
                {label: '$0.215', value: 0.215},
                {label: '$0.22', value: 0.22},
                {label: '$0.225', value: 0.225},
                {label: '$0.23', value: 0.23},
                {label: '$0.235', value: 0.235},
                {label: '$0.24', value: 0.24},
                {label: '$0.245', value: 0.245},
                {label: '$0.25', value: 0.25},
                {label: '$0.255', value: 0.255},
                {label: '$0.26', value: 0.26},
                {label: '$0.265', value: 0.265},
                {label: '$0.27', value: 0.27},
                {label: '$0.28', value: 0.28}
            ],
            avatarSize: 100,
            toggleProposedSystem: false,
            toggleRequestedSystem: false,
            toggleUtility: true,
            toggleCustomer: true,
            toggleProjectCoord: false,
            showProposedSystem: false,
            epc: {},
            loading: true,
            editRequest: false,
            canNextStage: false,
            currentView: 'lead',
            appointmentType: '',
            powerCompanyName: '',
            powerDiscounts: [],
            permissions: window.Permissions,
            user: null,
            lead: {
                status: null,
                jeopardy: null,
                company_cost_per_watt: null,
                power_company: null,
                account_number: null,
                rate_plan: null,
                discount_plan: null,
                average_bill: null,
                office_id: null,
                system_size: null,
                credit_pass: null,
                market_name: null,
                credit_status: '',
                solar_agreement_signed: null,
                integrations_approved: null,
                proposal: {
                    his_license: null,
                    solar_rate: null,
                    system_size: null,
                    monthly_payment: null,
                    credit_score: null,
                    adders: null,
                    needed_by: null,
                    system: null,
                    offset: null
                },
                sales_packet: {
                    solar_agreement_signed: false,
                    design_approved: false,
                    credit_doc_signed: false,
                    nem_doc_signed: false,
                    cpuc_doc_signed: false,
                    ach_doc_signed: false,
                    proposal_doc_signed: false,
                    install_date: {
                        id: null,
                        start_time: null,
                        finish_time: null
                    },
                    site_survey_note: null,
                    site_survey_date: {
                        id: null,
                        start_time: null,
                        finish_time: null
                    },
                    pto: false,
                    converted: false
                },
                utility: {
                    power_company: null,
                    kw_year_usage: null,
                    rate_plan: null,
                    power_discount_plan: null,
                    integrations_approved: null,
                    average_bill: null
                },
                login: {
                    user_name: ' ',
                    password: ' '
                },
                customer: {
                    id: null,
                    language: 'english',
                    full_name: null,
                    rep: null,
                    address: {
                        street_address: null,
                        city: null,
                        state: null,
                        zip: null
                    },
                    cell_phone: null,
                    home_phone: null,
                    email: null,
                    spouse_name: null
                }

            },
            market: window.LeadMarket,
            appointments: [],
            activeTab: 0,
            tabs: [
                {label: 'Quick Facts'},
                {label: 'Edit'}
            ],
            lowerTabs: [
                {label: 'Notes'},
                {label: 'Uploads'},
                {label: 'On the Deal'},
                {label: 'Appointments'}
            ],
            customer: {
                id: null,
                language: 'english',
                full_name: null,
                rep: null,
                address: {
                    street_address: null,
                    city: null,
                    state: null,
                    zip: null
                },
                cell_phone: null,
                home_phone: null,
                email: null,
                spouse_name: null
            },
            utility: {
                power_company: null,
                kw_year_usage: null,
                rate_plan: null,
                power_discount_plan: null,
                integrations_approved: null,
                average_bill: null
            }
        }
    },

    onActive() {
        this.onActiveCount++
        this.fetchLead()
    },

    mounted() {
        document.addEventListener('visibilitychange', event => {
            if (document.visibilityState === 'visible') {
                this.onActiveCount++
            }
        })
        this.fetchLead()
        this.fetchStatus()
        // setInterval(() => {
        //     this.updateLead()
        // }, 30500);
    },

    created() {
        this.fetchUsers()
        this.checkWidth()

        Echo.private('lead.' + this.$route.params.leadId)
            .listen('Backend.SalesFlow.Lead.LeadUpdateEvent', (e) => {
                this.leadUpdateEvent(e.data)
            }).listen('Backend.SalesFlow.LeadNewMessage', (e) => {
            const note = {
                id: e.id,
                note: e.message,
                user: {
                    name: {
                        first: e.firstName,
                        last: e.lastName
                    },
                    gravatar: e.gravatar
                }
            }
            this.lead.notes.push(note)
        }).listen('Backend.SalesFlow.LeadNewAppointment', (e) => {
            const appointment = {
                id: e.appointment.id,
                end_time: e.appointment.finish_time,
                start_time: e.appointment.start_time,
                lead_id: e.appointment.lead_id,
                user: {
                    fullName: e.name
                }
            }

            this.lead.appointments.push(appointment)
        })
            //     .listen('Backend.SalesFlow.RepAddedToLeadEvent', (e) => {
            //     console.log(e, 'integrator added event')
            //     this.lead.reps.push(e.rep)
            //
            // })
            .listen('Backend.SalesFlow.SalesPacketEvent', (e) => {
                // console.log(e, 'salesPacket')
                this.lead.sales_packet = e.salesPacket
                console.log('sp', this.lead.sales_packet)
            })
    },
    computed: {
        closeAppointment() {
            return this.lead.appointments.find(appointment => appointment.type_id === 6)
        },
        validCustomerName() {
            if (this.customer.first_name !== 'not set' ||
                this.customer.last_name !== 'not set') {
                return true
            } else {
                return false
            }
        },

        validCustomerNumber() {
            if (this.customer.cell_phone !== null) {
                if (this.customer.cell_phone.length === 10 || this.customer.cell_phone.length === 12) {
                    return true
                }
            }
            return false
        },
        inverterOptions() {
            if (this.epc.inverters) {
                const epcOption = [{
                    label: 'Select one',
                    value: null
                }]

                let x
                this.epc.inverters.forEach(inverters => {
                    x = {}
                    x.label = inverters.name
                    x.value = inverters.id

                    epcOption.push(x)
                })
                return epcOption
            } else {
                return []
            }
        },
        modulesOptions() {
            if (this.epc.modules) {
                const epcOption = [{
                    label: 'Select one',
                    value: null
                }]

                let x
                this.epc.modules.forEach(modules => {
                    x = {}
                    x.label = modules.name
                    x.value = modules.id
                    x.watts = modules.watts

                    epcOption.push(x)
                })
                return epcOption
            } else {
                return []
            }
        },
        compStatusList() {
            return this.statusList.map(status => {
                let x = {}
                x = status
                x.label = status.name
                x.value = status.id
                return x
            })
        },
        financeOptions() {
            const epcOption = []

            if (this.epc.financeOptions) {
                // let epcOption;

                epcOption.push({
                    label: 'Select one',
                    value: null
                })
                let i = 0
                this.epc.financeOptions.forEach(financeOption => {
                    // x = financeOption;
                    const x = {}
                    x.label = financeOption.name
                    x.value = financeOption.id
                    x.rate = financeOption.rate
                    x.finance_id = financeOption.finance_id
                    i++
                    epcOption.push(x)
                    // return epcOption;
                })
                return epcOption
            } else {
                return []
            }
        },

        // customerCellPhone() {
        //     // console.log('phone number ', this.lead.customer.cell_phone)
        //     var cleaned = ('' + this.lead.customer.cell_phone).replace(/\D/g, '')
        //     var match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/)
        //     if (match) {
        //         var intlCode = (match[1] ? '+1 ' : '')
        //         return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('')
        //     }
        //     return null
        //
        // },
        // customerHomePhone() {
        //     var cleaned = ('' + this.lead.customer.home_phone).replace(/\D/g, '')
        //     var match = cleaned.match(/^(1|)?(\d{3})(\d{3})(\d{4})$/)
        //     if (match) {
        //         var intlCode = (match[1] ? '+1 ' : '')
        //         return [intlCode, '(', match[2], ') ', match[3], '-', match[4]].join('')
        //     }
        //     return null
        // },

        canBookClose() {
            if (this.$can('create close date')) {
                const ishere = this.lead.appointments.find(a => a.type_id === 6)
                if (ishere) {
                    return false
                }
                return true
            }
        },

        onLead() {
            if (this.permissions.find(element => element === 'administrate company')) {
                return true
            } else if (this.permissions.find(element => element === 'integrations')) {
                return true
            } else if (this.permissions.find(element => element === 'administrate office')) {
                if (this.lead.office_id === this.getUser.data.office_id) {
                    return true
                }
            } else if (element => element === 'proposal builder') {
                return true
            } else {
                if (this.lead.reps.find(e => e.id === this.getUser.data.id)) {
                    return true
                }
                // if (this.getUser.data.office_id ===)
            }
        },

        integrator() {
            const integrator = this.lead.reps.filter(function (rep) {
                if (rep.position_id === 4) {
                    return rep
                } else {
                    return null
                }
            })
            return integrator[0]
        },
        builder() {
            const builder = this.lead.reps.filter(function (rep) {
                if (rep.position_id === 6) {
                    return rep
                }
            })

            if (builder.length > 0) {
                return true
            } else {
                return false
            }
        },

        ...mapGetters(['getUser']),

        canSp1() {
            const integration = this.permissions.find(element => element === 'integrations')
            const executive = this.permissions.find(element => element === 'administrate company')
            const manager = this.permissions.find(element => element === 'administrate office')
            const sp1 = this.permissions.find(element => element === 'sp1')
            const sp2 = this.permissions.find(element => element === 'sp2')
            const proposal = this.permissions.find(element => element === 'proposal')

            if (integration || executive || manager || sp1 || sp2 || proposal) {
                return true
            } else {
                return false
            }
        },
        canSp2() {
            if (this.$can('create close date')) {
                return true
            } else {
                return false
            }
        },
        canSalesRep() {
            const executive = this.permissions.find(element => element === 'administrate company')
            const manager = this.permissions.find(element => element === 'administrate office')
            const sp2 = this.permissions.find(element => element === 'sp2')
            const salesRep = this.permissions.find(element => element === 'sales rep')
            if (this.editing && (executive || manager || sp2 || salesRep)) {
                return true
            } else {
                return false
            }
        },
        canBuild() {
            const executive = this.permissions.find(element => element === 'administrate company')
            const builder = this.permissions.find(element => element === 'proposal builder')
            if (executive || builder) {
                return true
            } else {
                return false
            }
        }

    },
    methods: {
        ...mapActions(['fetchUsers']),

        // updateStatus lead/{lead}/update-status
        updateStatus(status) {
            console.log(status, 'status')
            axios.post(`/api/salesflow/lead/${this.lead.id}/update-status`, {status: status})
                .then((response) => {
                })
                .catch(function (error) {
                    console.log(error)
                })
        },
        fetchStatus() {
            axios.get('/api/salesflow/lead/status')
                .then((response) => {
                    this.statusList = response.data.data
                })
                .catch(function (error) {
                    console.log(error)
                })
        },

        checkWidth() {
            if (screen.width <= 430) {
                this.toggleCustomer = false
                this.toggleUtility = false
                this.toggleProjectCoord = false
            } else {
                this.toggleCustomer = true
                this.toggleUtility = true
                this.toggleProjectCoord = true
            }
        },
        updateLead(payload) {
            axios.put(`/api/salesflow/lead/${this.lead.id}`, payload)
                .then((response) => {
                    console.log(response)
                }).catch((e) => {
                console.log(e, 'customer update error')
            })
        },

        leadUpdateEvent(data) {
            console.log(data, 'lead update event')
            this.lead = _.merge(this.lead, _.pick(data, _.keys(this.lead)))
            console.log(this.lead, 'more updates')
        },

        fetchEpc() {
            axios.get(`/api/epc/${this.lead.epc_id}`)
                .then((response) => {
                    this.epc = response.data.data
                })
                .catch(function (error) {
                    console.log(error)
                })
        },

        siteSurveyUpdate(siteSurveyResult) {
            const data = {
                user: this.user,
                currentLead: this.lead,
                changeEditing: false,
                market: this.market,
                siteSurveyResult: siteSurveyResult,
                siteSurveyOption: true
            }
            let messageTitle = 'Send Doc to the customer'
            let messageMessage = 'Site Plan'

            if (!siteSurveyResult) {
                messageTitle = 'Let\'s see how we can save this'
                messageMessage = 'Work out with the Sales Rep on how we can make this work for the customer.'
            }

            axios.put(`/api/salesflow/lead/${this.lead.id}`, data)
                .then((response) => {
                    window.scrollTo(0, 0)
                    console.log(response)

                    if (response.data === 'sent') {
                        this.lead.status = 'submitted to proposal'
                    }
                    Swal.fire({
                        type: 'success',
                        title: messageTitle,
                        text: messageMessage
                    })
                })
                .catch(function (error) {
                    console.log(error)

                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Submit an issue?',
                        cancelButtonText: 'No Thanks'
                    }).then((result) => {
                        if (result.value) {
                            document.getElementById('submitIssueButton').click()
                        }
                    })
                })
        },

        // fetchUpload() {
        //     axios.get(`/api/salesflow/lead/${this.$route.params.leadId}/upload/1`)
        //         .then((response) => {
        //             console.log(response)
        //
        //             this.uploads = response.data.data;
        //         })
        //         .catch(function (error) {
        //             console.log(error);
        //         });
        // },
        fetchLead() {
            axios.get(`/api/salesflow/lead/${this.$route.params.leadId}`)
                .then((response) => {
                    this.lead = response.data.data
                    this.fetchEpc()
                }).then(() => this.loading = false)
                .catch(function (error) {
                    console.log('lead error ', error)
                })
        },
        openMaps: function () {
            this.mapUrl = `https://www.google.com/maps/dir/?api=1&destination=${this.lead.customer.street_address.split(' ').join('+')},+${this.lead.customer.city.split(' ').join('+')}`
            window.open(this.mapUrl, '_blank')
        },

        validate: function () {
            const reason = null

            // if (this.lead.customer.first_name === "" || this.lead.customer.last_name === "") {
            //     reason = Swal.fire({type: 'error', title: 'Customer must have name!'});
            // } else {
            //     reason = true;
            // }
            // if (this.lead.customer.street_address === "" || this.lead.customer.city === "") {
            //     reason = Swal.fire({type: 'error', title: 'Lead must have address!'});
            // } else {
            //     reason = true;
            // }
            //
            // return reason;
            return true
        }

    }

}
</script>

<style scoped>
.divider {
    border-top: 3px double #8c8b8b;
}

.form-check-input {
    transform: scale(2);
}

.select-country-container {
    display: none;
}

::-webkit-input-placeholder { /* WebKit, Blink, Edge */
    color: #C8C8C8 !important;
}

:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
    color: #C8C8C8 !important;
    opacity: 1;
}

::-moz-placeholder { /* Mozilla Firefox 19+ */
    color: #C8C8C8 !important;
    opacity: 1;
}

:-ms-input-placeholder { /* Internet Explorer 10-11 */
    color: #C8C8C8 !important;
}

::-ms-input-placeholder { /* Microsoft Edge */
    color: #C8C8C8 !important;
}

::placeholder { /* Most modern browsers support this now. */
    color: #C8C8C8 !important;
}

body, input, textarea {
    color: black !important;
}

.separator {
    display: flex;
    align-items: center;
    text-align: center;
}

.separator::before, .separator::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #000;
}

.separator::before {
    margin-right: .25em;
}

.separator::after {
    margin-left: .25em;
}

div button span {
    color: pink;
    background: orange;
}

.buttons-container {
    justify-content: space-between;
    display: flex;
}

</style>
