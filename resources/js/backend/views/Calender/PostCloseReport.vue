<template>
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="card ">
                    <div class="card-header">
                        Total Appointments
                    </div>
                    <div class="card-body">
                        {{ sortLeadsByAppointmentDate.length }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card ">
                    <div class="card-header">
                        Sat Percentage
                    </div>
                    <div class="card-body">
                        {{ satPercentageSoFar }}%
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <user-office-select
                            :can-view-regions="true"
                            :can-view-offices="true"
                            :can-view-users="$can('view office')"
                            @officeChange="selectedOffice = $event"
                            @userChange="selectedUser = $event"
                            @selectedRegionId="selectedRegionId = $event"
                        />
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <MazPicker
                            v-model="selectedDate"
                            no-time
                            size="sm"
                            placeholder="Select date"
                            formatted="ll"
                        />
                    </div>
                </div>
            </div>
            <div class="card-body">
                <DataTable :columns="columns">
                    <tbody>
                    <tr v-for="lead in sortLeadsByAppointmentDate" :key="lead.id" style="cursor: pointer;">
                        <td @click="openLead(lead.id)" v-for="column in columns" :key="column.name">
                            {{ getColumnData(lead, column.name) }}
                        </td>
                    </tr>
                    </tbody>
                </DataTable>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <MazPagination
                            v-model="currentPage"
                            :page-count="pageCount"
                            :page-range="pageCount"
                            @page="getLead"
                        />
                    </div>
                    <div class="col">
                        <label class="label">Per Search</label>
                        <select
                            v-model="tableData.length"
                            class="form-control form-control-sm w-25-md w-50-sm float-right"
                            @change="getLead"
                        >
                            <option v-for="(records, index) in perPage" :key="index" :value="records">
                                {{ records }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import axios from 'axios';
import dayjs from 'dayjs';

import DataTable from '../../components/dataTable/DataTable';
import UserOfficeSelect from '../../components/Selects/UserOfficeSelect';

export default {
    name: 'PostCloseReport',
    components: {UserOfficeSelect, DataTable},
    data() {
        return {
            selectedRegionId: null,
            selectedUser: null,
            leads: [],
            perPage: ['10', '25', '50', '100'],
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                column: 0,
                dir: 'desc'
            },
            pagination: {
                lastPage: '',
                currentPage: '',
                total: '',
                lastPageUrl: '',
                nextPageUrl: '',
                prevPageUrl: '',
                from: '',
                to: ''
            },
            pageCount: 1,
            currentPage: 1,
            selectedOffice: null,
            selectedDate: null,
            columns: [
                {label: 'Id', name: 'id'},
                {label: 'Date', name: 'time'},
                {label: 'Email', name: 'email'},
                {label: 'Name', name: 'name'},
                {label: 'Office Name', name: 'office'},
                {label: 'Stage', name: 'stage'},
                {label: 'Sat', name: 'sat'},
                {label: 'Cell Phone', name: 'cell phone'},
            ]
        };
    },
    watch: {
        selectedRegionId: "getLead",
        selectedDate: "getLead",
        selectedOffice: "getLead",
        selectedUser: "getLead"
    },
    created() {
        this.selectedDate = dayjs().format('YYYY-MM-DD');
        this.getLead();
    },
    methods: {
        ...mapActions(['fetchUsers']),
        getColumnData(lead, columnName) {
            switch (columnName) {
                case 'id':
                    return lead.id;
                case 'name':
                    return lead.customer.full_name;
                case 'office':
                    if (lead.origin_office_name !== lead.office.name) {
                        return lead.origin_office_name;
                    }
                    return lead.office.name;
                case 'stage':
                    return lead.status;
                case 'sat':
                    return lead.salesPacket.sat ? '✔' : '';
                case 'cell phone':
                    return lead.customer.cell_phone;
                case 'email':
                    return lead.customer.email;
                case 'time':
                    return this.findAppointmentTime(lead);
                default:
                    return '';
            }
        },
        findAppointmentTime(lead) {
            const appointment = lead.appointments.find(appointment => appointment.type_id === 6)
            return dayjs(appointment.start_time).format('hh:mm A')
        },
        getLead() {
            const data = {
                page: this.currentPage,
                selectedDate: this.selectedDate,
                draw: this.tableData.draw,
                length: this.tableData.length,
                userId: this.getUser.id,
                officeId: this.selectedOffice,
                selectedUser: this.selectedUser,
                regionId: this.selectedRegionId
            };

            axios.post('/api/salesflow/post-appointment', data).then((response) => {
                this.leads = response.data.data;
                this.configPagination(response.data.meta);
            }).catch((errors) => {
                console.log(errors);
            });
        },
        configPagination(data) {
            this.pagination = {...data};
            this.pageCount = data.last_page;
        },
        openLead(id) {
            const routeData = this.$router.resolve({path: `/dashboard/lead/${id}`});
            window.open(routeData.href, '_blank');
        }
    },
    computed: {
        ...mapGetters(['getUser']),
        sortLeadsByAppointmentDate() {
            return this.leads.sort((a, b) => {
                const aAppointment = a.appointments.find(appointment => appointment.type_id === 6)
                const bAppointment = b.appointments.find(appointment => appointment.type_id === 6)
                return new Date(aAppointment.start_time) - new Date(bAppointment.start_time);
            });
        },
        //     Appointments that have sat so far today measure by current time and if appointment start time is after  %

        satPercentageSoFar() {
            // find appointments that have happened so far today
            const now = dayjs();
            const appointmentsSoFar = this.leads.filter(lead => {
                const appointment = lead.appointments.find(appointment => appointment.type_id === 6);
                return dayjs(appointment.end_time).isBefore(now);
            });

            console.log(appointmentsSoFar);
            // find appointments that have sat
            const satAppointments = appointmentsSoFar.filter(lead => lead.salesPacket.sat);
            return (satAppointments.length / this.leads.length) * 100;
        },
        satCount() {
            return this.leads.filter(lead => lead.salesPacket.sat).length;
        }

    }
}
</script>
