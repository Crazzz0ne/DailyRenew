<template>
  <MazDialog
    v-model="showModel"
    fullsize
    title="Full Usage"
    @confirm="updateUsages"
  >
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="row">
            <div class="col">
              Total Usage {{ totalUsage }} KwH
            </div>
            <div class="col">
              Avarage Usage {{ averageKwh }} KwH
            </div>
          </div>
        </div>
        <div class="col">
          <div class="row">
            <div class="col">
              Total Bill ${{ totalBill }}
            </div>
            <div class="col">
              Average Bill ${{ averageBill }}
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-6">
          <MazInput
            v-model="usage.jan.kwh"
            class="my-2"
            :placeholder="'Jan KwH'"
            :hint="'Jan KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.jan.bill"
            class="my-2"
            :placeholder="'Jan bill'"
            :hint="'Jan bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.feb.kwh"
            class="my-2"
            :placeholder="'Feb KwH'"
            :hint="'Feb KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.feb.bill"
            class="my-2"
            :placeholder="'Feb bill'"
            :hint="'Feb bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.mar.kwh"
            class="my-2"
            :placeholder="'Mar KwH'"
            :hint="'Mar KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.mar.bill"
            class="my-2"
            :placeholder="'Mar bill'"
            :hint="'Mar bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.apr.kwh"
            class="my-2"
            :placeholder="'Apr KwH'"
            :hint="'Apr KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.apr.bill"
            class="my-2"
            :placeholder="'Apr bill'"
            :hint="'Apr bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.may.kwh"
            class="my-2"
            :placeholder="'May KwH'"
            :hint="'May KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.may.bill"
            class="my-2"
            :placeholder="'May bill'"
            :hint="'May bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.jun.kwh"
            class="my-2"
            :placeholder="'Jun KwH'"
            :hint="'Jun KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.jun.bill"
            class="my-2"
            :placeholder="'Jun bill'"
            :hint="'Jun bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.jul.kwh"
            class="my-2"
            :placeholder="'Jul KwH'"
            :hint="'Jul KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.jul.bill"
            class="my-2"
            :placeholder="'Jul bill'"
            :hint="'Jul bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.aug.kwh"
            class="my-2"
            :placeholder="'Aug KwH'"
            :hint="'Aug KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.aug.bill"
            class="my-2"
            :placeholder="'Aug bill'"
            :hint="'Aug bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.sep.kwh"
            class="my-2"
            :placeholder="'Sep KwH'"
            :hint="'Sep KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.sep.bill"
            class="my-2"
            :placeholder="'Sep bill'"
            :hint="'Sep bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.oct.kwh"
            class="my-2"
            :placeholder="'Oct KwH'"
            :hint="'Oct KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.oct.bill"
            class="my-2"
            :placeholder="'Oct bill'"
            :hint="'Oct bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.nov.kwh"
            class="my-2"
            :placeholder="'Nov KwH'"
            :hint="'Nov KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.nov.bill"
            class="my-2"
            :placeholder="'Nov bill'"
            :hint="'Nov bill'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.dec.kwh"
            class="my-2"
            :placeholder="'Dec KwH'"
            :hint="'Dec KwH'"
          />
        </div>
        <div class="col-6">
          <MazInput
            v-model="usage.dec.bill"
            class="my-2"
            :placeholder="'Dec bill'"
            :hint="'Dec bill'"
          />
        </div>
      </div>
    </div>
  </MazDialog>
</template>

<script>
import axios from 'axios'

export default {
	name: 'UtilityUsage',
	props: [
		'showModel',
		'leadId',
		'utilityId'
	],
	data () {
		return {
			usage: {
				jan: {
					kwh: null,
					bill: null
				},
				feb: {
					kwh: null,
					bill: null
				},
				mar: {
					kwh: null,
					bill: null
				},
				apr: {
					kwh: null,
					bill: null
				},
				may: {
					kwh: null,
					bill: null
				},
				jun: {
					kwh: null,
					bill: null
				},
				jul: {
					kwh: null,
					bill: null
				},
				aug: {
					kwh: null,
					bill: null
				},
				sep: {
					kwh: null,
					bill: null
				},
				oct: {
					kwh: null,
					bill: null
				},
				nov: {
					kwh: null,
					bill: null
				},
				dec: {
					kwh: null,
					bill: null
				}
			}
		}
	},
	computed: {
		totalUsage () {
			let total = 0

			const usage = {
				jan_kwh: this.usage.jan.kwh ?? 0,
				jan_bill: this.usage.jan.bill ?? 0,
				feb_kwh: this.usage.feb.kwh ?? 0,
				feb_bill: this.usage.feb.bill ?? 0,
				mar_bill: this.usage.mar.bill ?? 0,
				mar_kwh: this.usage.mar.kwh ?? 0,
				apr_kwh: this.usage.apr.kwh ?? 0,
				apr_bill: this.usage.apr.bill ?? 0,
				may_kwh: this.usage.may.kwh ?? 0,
				may_bill: this.usage.may.bill ?? 0,
				jun_kwh: this.usage.jun.kwh ?? 0,
				jun_bill: this.usage.jun.bill ?? 0,
				jul_kwh: this.usage.jul.kwh ?? 0,
				jul_bill: this.usage.jul.bill ?? 0,
				aug_kwh: this.usage.aug.kwh ?? 0,
				aug_bill: this.usage.aug.bill ?? 0,
				sep_kwh: this.usage.sep.kwh ?? 0,
				sep_bill: this.usage.sep.bill ?? 0,
				oct_kwh: this.usage.oct.kwh ?? 0,
				oct_bill: this.usage.oct.bill ?? 0,
				nov_kwh: this.usage.nov.kwh ?? 0,
				nov_bill: this.usage.nov.bill ?? 0,
				dec_kwh: this.usage.dec.kwh ?? 0,
				dec_bill: this.usage.dec.bill ?? 0
			}

			total = (parseFloat(usage.jan_kwh) + parseFloat(usage.feb_kwh) + parseFloat(usage.mar_kwh) + parseFloat(usage.apr_kwh) +
                parseFloat(usage.may_kwh) + parseFloat(usage.jun_kwh) + parseFloat(usage.jul_kwh) + parseFloat(usage.aug_kwh) +
                parseFloat(usage.sep_kwh) + parseFloat(usage.oct_kwh) + parseFloat(usage.nov_kwh) + parseFloat(usage.dec_kwh))
			return total.toFixed(2)
		},
		totalBill () {
			let total = 0

			const usage = {
				jan_kwh: this.usage.jan.kwh ?? 0,
				jan_bill: this.usage.jan.bill ?? 0,
				feb_kwh: this.usage.feb.kwh ?? 0,
				feb_bill: this.usage.feb.bill ?? 0,
				mar_bill: this.usage.mar.bill ?? 0,
				mar_kwh: this.usage.mar.kwh ?? 0,
				apr_kwh: this.usage.apr.kwh ?? 0,
				apr_bill: this.usage.apr.bill ?? 0,
				may_kwh: this.usage.may.kwh ?? 0,
				may_bill: this.usage.may.bill ?? 0,
				jun_kwh: this.usage.jun.kwh ?? 0,
				jun_bill: this.usage.jun.bill ?? 0,
				jul_kwh: this.usage.jul.kwh ?? 0,
				jul_bill: this.usage.jul.bill ?? 0,
				aug_kwh: this.usage.aug.kwh ?? 0,
				aug_bill: this.usage.aug.bill ?? 0,
				sep_kwh: this.usage.sep.kwh ?? 0,
				sep_bill: this.usage.sep.bill ?? 0,
				oct_kwh: this.usage.oct.kwh ?? 0,
				oct_bill: this.usage.oct.bill ?? 0,
				nov_kwh: this.usage.nov.kwh ?? 0,
				nov_bill: this.usage.nov.bill ?? 0,
				dec_kwh: this.usage.dec.kwh ?? 0,
				dec_bill: this.usage.dec.bill ?? 0
			}

			total = (parseFloat(usage.jan_bill) + parseFloat(usage.feb_bill) + parseFloat(usage.mar_bill) + parseFloat(usage.apr_bill) +
                parseFloat(usage.may_bill) + parseFloat(usage.jun_bill) + parseFloat(usage.jul_bill) + parseFloat(usage.aug_bill) +
                parseFloat(usage.sep_bill) + parseFloat(usage.oct_bill) + parseFloat(usage.nov_bill) + parseFloat(usage.dec_bill))
			return total.toFixed(2)
		},
		averageBill () {
			let count = 0
			Object.values(this.usage).forEach(value => {
				// console.log(value)
				if (value.bill > 0) {
					count++
				} else {
					false
				}
			})
			let avgbill = 0
			avgbill = this.totalBill / count
			return avgbill.toFixed(2)
		},
		averageKwh () {
			let count = 0
			Object.values(this.usage).forEach(value => {
				if (value.kwh > 0) {
					count++
				}
			})
			let avgkwh = 0
			avgkwh = this.totalUsage / count
			return avgkwh.toFixed(2)
		}
	},
	watch: {
		showModel (oldValue, newValue) {
			// console.log('newValue', newValue)
			// console.log('oldValue', oldValue)
			// if (oldValue === true) {
			//     this.getUsage()
			// }else {
			//     this.$emit('showModelToggle')
			// }
		}
	},
	created () {
		this.getUsage()
	},
	methods: {
		updateUsages () {
			const payload = {}
			if (this.totalBill) {
				payload.average_bill = this.averageBill
				this.$emit('updateBill', this.averageBill)
			}
			if (this.totalUsage) {
				payload.kw_year_usage = this.totalUsage
				this.$emit('updateTotalUsage', this.totalUsage)
			}

			this.$emit('updateUsage', payload)
			this.$emit('showModelToggle')

			const usage = {
				jan_kwh: this.usage.jan.kwh ?? 0,
				jan_bill: this.usage.jan.bill ?? 0,
				feb_kwh: this.usage.feb.kwh ?? 0,
				feb_bill: this.usage.feb.bill ?? 0,
				mar_bill: this.usage.mar.bill ?? 0,
				mar_kwh: this.usage.mar.kwh ?? 0,
				apr_kwh: this.usage.apr.kwh ?? 0,
				apr_bill: this.usage.apr.bill ?? 0,
				may_kwh: this.usage.may.kwh ?? 0,
				may_bill: this.usage.may.bill ?? 0,
				jun_kwh: this.usage.jun.kwh ?? 0,
				jun_bill: this.usage.jun.bill ?? 0,
				jul_kwh: this.usage.jul.kwh ?? 0,
				jul_bill: this.usage.jul.bill ?? 0,
				aug_kwh: this.usage.aug.kwh ?? 0,
				aug_bill: this.usage.aug.bill ?? 0,
				sep_kwh: this.usage.sep.kwh ?? 0,
				sep_bill: this.usage.sep.bill ?? 0,
				oct_kwh: this.usage.oct.kwh ?? 0,
				oct_bill: this.usage.oct.bill ?? 0,
				nov_kwh: this.usage.nov.kwh ?? 0,
				nov_bill: this.usage.nov.bill ?? 0,
				dec_kwh: this.usage.dec.kwh ?? 0,
				dec_bill: this.usage.dec.bill
			}
			axios.put(`/api/salesflow/lead/${this.leadId}/utility/${this.utilityId}/usage/1`, usage)
				.then((response) => {
					// this.usage = response.data.data
				}).catch(function (error) {
					console.log('customer error ', error)
				})
		},
		getUsage () {
			axios.get(`/api/salesflow/lead/${this.leadId}/utility/${this.utilityId}/usage`)
				.then((response) => {
					this.usage = response.data.data
				}).catch(function (error) {
					console.log('customer error ', error)
				})
		}

	}
}
</script>

<style scoped>

</style>
