<template>
  <div class="row">
    <div class="col-md-6 col-sm-12">
      <table class="table table-sm">
        <thead>
          <tr>
            <th scope="col">
              Year
            </th>
            <th>Traditional power yearly</th>
            <th>Traditional power monthly</th>
            <th>NEM</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(value, index) in powerYearlyMonthlyArray">
            <th class="smallFont">
              {{ index }}
            </th>
            <th class="smallFont">
              ${{ value.tPYear }}
            </th>
            <th class="smallFont">
              ${{ value.tPMonthly }}
            </th>
            <th
              style="color: darkgreen"
              class="smallFont"
            >
              ${{ value.nemMonthly }}
            </th>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-6 col-sm-12">
      <line-chart
        v-if="datacollection"
        class="small"
        :chartdata="datacollection"
        :options="options"
      />
      <div class="row justify-content-around">
        <div class="col">
          <h5>Cost</h5>
          <p>Traditional Power: <br> ${{ totalTP.formatted }}</p>
          <!--                    <p>Total NEM: ${{ totalNEM.formatted }}</p>-->
        </div>

        <div class="col-md-6 col-sm-12">
          <h5>Savings</h5>
          <p style="color: darkgreen">
            ${{ savings.formatted }} {{ savings.percentSaved }}%
          </p>
          <span style="color: darkgreen" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LineChart from '../../chart/LineChart'

export default {
	name: 'SavingsBreakDown',
	components: { LineChart },
	props: [
		'oldBillAverage',
		'newBill',
		'apr'

	],
	data () {
		return {
			datacollection: null,
			options: {
				responsive: true,
				plugins: {
					title: {
						display: true,
						text: 'Savings over time'
					}
				},
				interaction: {
					mode: 'index',
					intersect: false
				},
				scales: {
					x: {
						display: true,
						title: {
							display: true,
							text: 'Year'
						}
					},
					y: {
						display: true,

						title: {
							display: true,
							text: 'Cost'
						}
					}
				}
			}
		}
	},
	computed: {
		totalTP () {
			let yearly = 0
			this.powerYearlyMonthlyArray.forEach(value => {
				yearly = parseFloat(yearly) + parseFloat(value.tPYearRaw)
			})
			return {
				raw: yearly,
				formatted: yearly.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
			}
		},
		totalNEM () {
			let yearly = 0
			this.powerYearlyMonthlyArray.forEach(value => {
				yearly = parseFloat(yearly) + parseFloat(value.nemYearly)
			})
			return {
				raw: yearly,
				formatted: yearly.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
			}
		},
		nemComputedArray () {
			const array = []
			for (let i = 0; i < 26; i++) {
				array.push(this.powerYearlyMonthlyArray[i].nemMonthlyRaw)
			}
			return array
		},
        powerYearlyMonthlyArray() {
            const inflation = 1.042;
            let yearly = this.oldBillAverage * 12;
            const array = [];
            let nemYearly = this.newBill * 12;
            const rate = this.apr / 100;

            const formatNumber = (num) => {
                return num.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            };

            for (let i = 0; i < 26; i++) {
                let monthly = yearly / 12;
                let nemMonthly = nemYearly / 12;

                const yearlyData = {
                    tPYearRaw: yearly,
                    tPMonthlyRaw: monthly,
                    nemMonthlyRaw: nemMonthly,
                    nemYearlyRaw: nemYearly,
                    tPYear: formatNumber(yearly),
                    tPMonthly: formatNumber(monthly),
                    nemMonthly: formatNumber(nemMonthly),
                    nemYearly: formatNumber(nemYearly)
                };

                array.push(yearlyData);

                // Apply inflation and rate for the next iteration
                yearly *= inflation;
                nemYearly += nemYearly * rate;
            }

            return array;
        },

		powerMonthlyArray () {
			const array = []
			if (this.powerYearlyMonthlyArray.length) {
				for (let i = 0; i < 26; i++) {
					array.push(this.powerYearlyMonthlyArray[i].tPMonthlyRaw)
				}
				return array
			}
		},
		savings () {
			const dollarSavings = this.totalTP.raw - this.totalNEM.raw
			const percentSaved = (dollarSavings / this.totalTP.raw) * 100
			return {
				raw: dollarSavings,
				formatted: dollarSavings.toLocaleString('en-US',
					{ minimumFractionDigits: 2, maximumFractionDigits: 2 }),
				percentSaved: percentSaved.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
			}
		}

	},
	watch: {
		powerYearlyMonthlyArray () {
			this.fillData()
		}
	},
	mounted () {
		this.fillData()
	},
	methods: {
		fillData () {
			this.datacollection = {
				labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25],
				datasets: [
					{
						label: 'NEM',
						borderColor: 'green',
						data: this.nemComputedArray,
						fill: false
					}, {
						label: 'Traditional Power',
						backgroundColor: 'red',
						data: this.powerMonthlyArray,
						fill: false
					}
				]
			}
		},
		getRandomInt () {
			return Math.floor(Math.random() * (50 - 5 + 1)) + 5
		}
	}
}
</script>

<style scoped>
.small {
    max-width: 500px;
    margin: auto;
}

.smallFont {
    font-size: 12px;
}
</style>
