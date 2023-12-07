<template>
    <div>
        <div
            v-if="!loading"
            class="card"
        >
            <div class="card-header">
                <div class="row h-100 justify-content-center align-items-center">
                    <div class="col-md-6 col-sm-12 pt-2">
                        <h2 class="display-5">
                            New Client
                        </h2>
                    </div>
                    <div class="col-md-6 col-sm-12"/>
                </div>
            </div>
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Language</label>

                                <MazSelect
                                    v-model="customer.language"
                                    :options="languages"
                                    size="sm"
                                    color="info"
                                >
                                    <i
                                        slot="icon-left"
                                        class="material-icons"
                                    >
                                        add_circle_outline
                                    </i>
                                </MazSelect>
                                <h1/>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input
                                    id="name"
                                    v-model="customer.full_name"
                                    type="text"
                                    name="name"
                                    placeholder="John Smith"
                                    maxlength="80"
                                    required="required"
                                    class="form-control"
                                >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">

                                <label>Address</label>
                                <AutoComplete
                                    :user-long="getUser.data.latLong.long"
                                    :user-lat="getUser.data.latLong.lat"
                                    @place="updateAddress($event)"
                                />

                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="name">Email</label>
                                <input
                                    id="email"
                                    v-model="customer.email"
                                    type="email"
                                    name="email"
                                    placeholder="SolarSaver@gmail.com"
                                    maxlength="80"
                                    required="required"
                                    class="form-control"
                                >
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="name">Cell Phone</label>
                                <MazPhoneNumberInput
                                    v-model="customer.cell_phone"
                                    :default-country-code="'US'"
                                    :no-country-selector="true"
                                    :no-flags="true"
                                    :no-example="true"
                                    :only-countries="['US']"
                                    @blur="updateCellPhone(customer.cell_phone)"
                                />
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="name">Home Phone</label>

                                <MazPhoneNumberInput
                                    v-model="customer.home_phone"
                                    :default-country-code="'US'"
                                    :no-country-selector="true"
                                    :no-flags="true"
                                    :no-example="true"
                                    :only-countries="['US']"
                                    @blur="updateHomePhone(customer.home_phone)"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-end pt-5">
                        <div class="col-6">
                            <div
                                v-if="$can('create close date')"
                                class="form-group"
                            >
                                <MazSelect
                                    v-model="opener"
                                    :options="openerList"
                                    clearable
                                    :placeholder="'Opener'"
                                    search
                                    search-placeholder="Search"
                                    size="sm"
                                    color="info"
                                >
                                    <i
                                        slot="icon-left"
                                        class="material-icons"
                                    >
                                        add_circle_outline
                                    </i>
                                </MazSelect>
                                <MazBtn
                                    class="float-right mt-3"
                                    fab
                                    @click="setSelf"
                                >
                                    Self
                                </MazBtn>
                            </div>
                        </div>
                        <div class="col-6"/>
                    </div>
                    <div class="row d-flex justify-content-end">
                        <div class="col-6"/>
                        <div class="col-6">
                            <div class="row text-center">
                                <div
                                    v-show="!disabled"
                                    class="col"
                                >
                                    <button
                                        class="btn btn-primary"
                                        :disabled="disabled"
                                        @click="startSelfGen"
                                    >
                                        <span class="pr-1">Create Lead</span>
                                        <i class="fas fa-plus-circle"/>
                                    </button>
                                </div>
                                <div
                                    v-show="disabled"
                                    class="col"
                                >
                                    <MazBtn loading>
                                        Loading
                                    </MazBtn>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            v-else
            class="text-center"
        >
            <MazLoader/>
            <h3>Loading</h3>
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'
import AutoComplete from '../../components/lead/location/AutoComplete'
import ManualForm from '../../components/lead/location/ManualForm'

export default {
    name: 'LeadCreate',
    components: {
        ManualForm,
        AutoComplete
    },
    data() {
        return {
            opener: null,
            openerList: [],
            loading: false,
            notes: [],
            disabled: false,
            permissions: window.Permissions,
            epc: 1,
            tabs: [
                {label: 'Autocomplete'},
                {label: 'Manual'}
            ],
            languages: [
                {label: 'Select One', value: null},
                {label: 'English', value: 'english'},
                {label: 'Spanish', value: 'spanish'}
            ],
            customer: {
                id: null,
                language: 'english',
                full_name: null,
                first_name: '',
                last_name: '',
                rep: null,
                address: {
                    street_address: null,
                    city: null,
                    state: null,
                    zip: null
                },
                cell_phone: null,
                home_phone: null,
                email: null
            }
        }
    },
    created() {
        this.loading = true
        this.fetchUsers()
            .then(() =>
                this.loading = false
            ).then(() => {
            if (this.getUser.data.office.call_center) {
                this.customer.language = null
            }
        })
        this.getOpeners()
    },
    computed: {
        ...mapGetters(['getLead', 'getUser']),
        openerSelected() {
            return this.opener === null && this.$can('create close date')
        },

        selfGen() {
            if (!this.$can('create sp1 request') || this.getUser.data.office_id === 10) {
                return true
            }
        }

    },
    methods: {
        ...mapActions(['updateCustomerName', 'fetchUsers']),

        updateCellPhone(event) {
            if (event.length !== 10) {
                this.customer.cell_phone = event.substring(2)
            }
        },

        updateHomePhone(event) {
            if (event.length !== 10) {
                this.customer.home_phone = event.substring(2)
            }
        },

        setSelf() {
            this.openerList.push({value: this.getUser.data.id, label: this.getUser.data.fullName})
            this.opener = this.getUser.data.id
        },
        getOpeners() {
            axios.get('/api/salesflow/user/position?position=canvasser')
                .then((response) => {
                    this.openerList = response.data
                })
                .catch((error) => {
                    console.log(error)
                })
        },
        updateAddress(place) {
            this.customer.address = place
        },

        noteAdded: function (note) {
            this.notes.push(note)
        },
        noteDeleted: function (noteId) {
            this.notes = _.remove(this.notes, function (a) {
                return a.id !== noteId
            })
        },
        startIntergration: function () {
            const hasCustomer = false
            const hasAddress = false
            let sendIt = true

            if (this.customer.address.street_address !== '' && this.customer.address.street_address !== null &&
                this.customer.address.zip !== '' && this.customer.address.zip !== null &&
                this.customer.address.city !== '' && this.customer.address.city !== null
            ) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hold up',
                    text: 'Where do they live?'
                })
                sendIt = false
            } else if (this.customer.language === null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Language',
                    text: 'What Language do they speak?'
                })
                sendIt = false
            } else if (this.opener === null && this.getUser.data.office_id === 10) {
                sendIt = false
            }

            if (sendIt) {
                console.log()
                this.disabled = true
                const data = {
                    customer: this.customer,
                    user: this.getUser.data,
                    office_id: this.getUser.data.office_id,
                    notes: this.notes,
                    market: this.market,
                    selfGen: false,
                    proposal: this.proposalOnly,
                    epc: this.epc
                }
                axios.post('/api/salesflow/customer', data)
                    .then((response) => {
                        this.disabled = false
                        console.log(response)
                        const leadId = response.data.lead_id
                        if (response.data.lead_id) {
                            this.$router.push({
                                path: `/dashboard/lead/await/${response.data.queue_id}/${response.data.lead_id}`

                            })
                        }
                    })
                    .catch((error) => {
                        this.disabled = false
                        console.log(error)
                    })
            }
        },
        startSelfGen: function () {
            const hasCustomer = false
            const hasAddress = false
            let sendIt = true

            if (this.customer.address.street_address === null || this.customer.address.zip == null) {
                console.log('yyy')
                Swal.fire({
                    icon: 'error',
                    title: 'Hold up',
                    text: 'Where do they live?'
                })
                sendIt = false
            } else if (this.customer.language === null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Language',
                    text: 'What Language do they speak?'
                })
                sendIt = false
            } else if (this.openerSelected) {
                sendIt = false
                Swal.fire({
                    icon: 'error',
                    title: 'Opener',
                    text: 'Who Opened it?'
                })
            }
            if (sendIt) {
                console.log()
                this.disabled = true
                const data = {
                    customer: this.customer,
                    user: this.getUser.data,
                    office_id: this.getUser.data.office_id,
                    market: this.market,
                    epc: this.epc,
                    opener: this.opener

                }
                axios.post('/api/salesflow/customer', data)
                    .then((response) => {
                        console.log(response, 'self gen')
                        if (response.data.lead_id) {
                            this.$router.push({path: `/dashboard/lead/${response.data.lead_id}`})
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oh no',
                                text: 'Something went wrong, Refresh the page and try again'
                            })
                            this.disabled = false
                        }
                    })
                    .catch((error) => {
                        this.disabled = false
                        Swal.fire({
                            icon: 'error',
                            title: 'There was an error',
                            text: 'Try again, if the problem persists please contact support '
                        })
                    })
            }
        },
        startProposal: function () {
            const hasCustomer = false
            let hasAddress = false
            if (this.customer.address.street_address !== '' && this.customer.address.zip_code !== '') hasAddress = true

            if (!hasAddress) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hold up',
                    text: 'Where do they live?'
                })
            } else {
                console.log()
                this.disabled = true
                const data = {
                    customer: this.customer,
                    user: this.getUser.data,
                    office_id: this.getUser.data.office_id,
                    market: this.market,
                    proposal: true,
                    epc: this.epc
                }
                axios.post('/api/salesflow/customer', data)
                    .then((response) => {
                        console.log(response)
                        if (response.data.lead_id) {
                            this.$router.push({path: `/dashboard/lead/${response.data.lead_id}`})
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oh no',
                                text: 'Something went wrong, Refresh the page and try again.'
                            })
                            this.disabled = false
                        }
                    })
                    .catch((error) => {
                        this.disabled = false
                        console.log(error)
                    })
            }
        },
        updateCustomerName: function (event) {
            this.$store.commit('UPDATE_CUSTOMER_NAME', event.target.value)
        },
        updateCustomerEmail: function (event) {
            this.$store.commit('UPDATE_CUSTOMER_EMAIL', event.target.value)
        },
        updateCustomerPhone: function (event) {
            this.$store.commit('UPDATE_CUSTOMER_CELLPHONE', event.target.value)
        }
    }
}
</script>

<style scoped>

</style>
