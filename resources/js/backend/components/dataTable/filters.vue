<template>
    <div class="row justify-content-between">
        <div class="col-md-3 col-sm-12">
            <MazCheckbox
                v-model="callCenter"
                @input="setCallCenter"
            >
                Call Center Only
            </MazCheckbox>
            <MazCheckbox
                v-model="inHouse"
                @input="setInHouse"
            >
                In House Only
            </MazCheckbox>

            <MazCheckbox
                v-model="lowUsage"
                @input="setLowUsage"
            >
                Low Usage
            </MazCheckbox>
            <!--            <MazCheckbox-->
            <!--                v-model="appointment"-->
            <!--                @input="setAppointment"-->
            <!--            >Appointment-->
            <!--            </MazCheckbox>-->
            <MazCheckbox
                v-model="passedIntegrations"
                @input="setPassedIntegrations"
            >
                Passed Integrations
            </MazCheckbox>
            <MazCheckbox
                v-model="creditPass"
                @input="setCreditPass"
            >
                Credit Pass
            </MazCheckbox>
            <MazCheckbox
                v-model="closed"
                @input="setDealClosed"
            >
                Closed
            </MazCheckbox>
            <MazCheckbox
                v-model="jij"
                @input="setJIJ"
            >
                JIJ
            </MazCheckbox>
            <MazCheckbox
                v-model="appointmentSet"
                @input="setAppointmentSet"
            >
                Close Scheduled
            </MazCheckbox>
            <MazCheckbox
                v-model="sat"
                @input="setSat"
            >
                Sat
            </MazCheckbox>
            <MazCheckbox
                v-model="stale"
                @input="setStale"
            >
                Stale
            </MazCheckbox>
            <MazCheckbox
                v-model="permitApproved"
                @input="setPermitApproved"
            >
                Permit Approved
            </MazCheckbox>
            <MazCheckbox
                v-if="$can('edit NTS')"
                v-model="projectManager"
                @input="setProjectManager"
            >
                My Projects
            </MazCheckbox>
        </div>
        <div class="col-md-5 col-sm-12">
            <user-office-select
                :can-view-regions="true"
                :can-view-offices="true"
                :user-market-id="regionId"
                :can-view-users="$can('view office')"
                @officeChange="$emit('office', $event)"
                @userChange="setUser($event)"
                @selectedRegionId="updateRegion($event)"
            />
        </div>
    </div>
</template>

<script>
import UserOfficeSelect from '../Selects/UserOfficeSelect'

export default {
    name: 'Filters',
    components: {UserOfficeSelect},
    props: [
        'regionId'
    ],
    data() {
        return {
            stale: false,
            creditPass: false,
            lowUsage: false,
            passedIntegrations: false,
            jij: false,
            sat: false,
            closed: false,
            callCenter: false,
            inHouse: false,
            appointment: false,
            projectManager: false,
            appointmentSet: false,
            permitApproved: false

        }
    },
    methods: {
        updateRegion(event) {
            // console.log(event)
            this.$emit('selectedRegionId', event)
        },

        setInHouse() {
            if (this.inHouse) {
                this.callCenter = false
                this.$emit('callCenter', this.callCenter)
            }
            this.$emit('inHouse', this.inHouse)
            this.$emit('change', true)
        },
        setAppointmentSet() {
            this.$emit('appointmentSet', this.appointmentSet)
            this.$emit('change', true)
        },
        setStale() {
            this.$emit('stale', this.stale)
            this.$emit('change', true)
        },
        setCallCenter() {
            if (this.callCenter) {
                this.inHouse = false
                this.$emit('inHouse', this.inHouse)
            }
            this.$emit('callCenter', this.callCenter)
            this.$emit('change', true)
        },
        setLowUsage() {
            this.$emit('lowUsage', this.lowUsage)
            this.$emit('change', true)
        },
        setPassedIntegrations() {
            this.$emit('passedIntegrations', this.passedIntegrations)
            this.$emit('change', true)
        },
        setCreditPass() {
            this.$emit('creditPass', this.creditPass)
            this.$emit('change', true)
        },
        setJIJ() {
            this.$emit('jij', this.jij)
            this.$emit('change', true)
        },
        setOffice(event) {
            this.$emit('office', event)
            this.$emit('change', true)
        },
        setUser(event) {
            this.$emit('user', event)
            this.$emit('change', true)
        },
        setDealClosed(event) {
            this.$emit('close', event)
            this.$emit('change', true)
        },
        setSat(event) {
            this.$emit('sat', event)
            this.$emit('change', true)
        },
        setProjectManager(event) {
            this.$emit('projectManager', event)
            this.$emit('change', true)
        },
        setPermitApproved (event) {
            this.$emit('permitApproved', event)
            this.$emit('change', true)
        }
    }

}
</script>

<style scoped>

</style>
