<template>
  <div class="row pt-3">
    <div class="col-md-6 col-sm-12">
      <template v-if="selectedAdders.length">
        <h5 class="text-center">
          Selected Adders
        </h5>
        <MazList
          no-shadow
          class="scrollable-list"
          :style="'max-height:220px;'"
        >
          <MazListItem
            v-for="(item) in selectedAdders"
            :key="item.id"
            tag="button"
          >
            <p>{{ item.name }}</p>
            <p class="maz-text-muted">
              ${{ item.cost.toLocaleString() }}
              <span v-if="!item.flat_cost">
                Per Kw
              </span>
            </p>
            <template v-if="!item.flat_cost && item.total">
              <p><strong>${{ item.total.toLocaleString() }} </strong></p>
            </template>
          </MazListItem>
        </MazList>
      </template>
    </div>
    <div
      v-if="totalCost"
      class="col-md-6 col-sm-12 text-center"
    >
      <template v-if="selectedAddersCost">
        <h5>Cost</h5>
        <label>
          Adders
        </label>
        <p>${{ selectedAddersCost.toLocaleString() }}</p>
      </template>
      <template v-if="roofWork">
        <label>Roof Work</label>
        <p>${{ roofWork.toLocaleString() }}</p>
      </template>
      <h5>Total</h5>
      <p> ${{ totalCost.toLocaleString() }}</p>
    </div>
  </div>
</template>

<script>
export default {
	name: 'AddersList',
	props: [
		'roofWork',
		'selectedNewAdders',
		'epcAdders',
		'system_size'
	],
	computed: {
		selectedAdders () {
			// console.log('system size', this.system_size)
			const array = []
			console.log('running')
			const systemSize = Number(this.system_size).toFixed(3)
			console.log('systemSize', systemSize)
			if (this.epcAdders && this.selectedNewAdders) {
				const epcAdders = this.epcAdders

				const total = 0
				const newAdders = Object.values(this.selectedNewAdders)

				newAdders.forEach(function (skey, value) {
					let adder = null
					adder = epcAdders.find((ad) => {
						console.log('ad', ad)
						console.log('value', value)
						console.log('skey', skey)
						if (ad.id === skey) {
							console.log('it found one')
							return true
						}
					})
					if (adder.flat_cost === 0) {
						const total = (adder.cost * systemSize) * 1000
						console.log('value,cost', skey.cost)
						console.log('systemSize', systemSize)
						console.log('total', total)
						adder.total = total
					}
					array.push(adder)
					console.log('adder', adder)
					// $.each(epcAdders, function (okey, oAdder) {
					//     let temp = oAdder;
					//     if (oAdder.id === value) {
					//         if (oAdder.flat_cost === 0 && systemSize) {
					//             console.log('adder cost', oAdder.cost);
					//             console.log('system size', systemSize);
					//             temp.total = (oAdder.cost * systemSize);
					//             oAdder.total = temp.total;
					//         }
					//         array.push(oAdder)
					//     }
					// })
				})
			}
			return array
		},
		selectedAddersCost () {
			const adder = this.selectedAdders
			const adder1 = _.sumBy(adder, 'cost')
			const adder2 = _.sumBy(adder, 'total')
			if (adder1 && adder2) {
				return Number(adder1 + adder2)
			} else if (adder1) {
				return Number(adder1)
			} else {
				return 0
			}
		},
		totalCost () {
			const total = this.selectedAddersCost + Number(this.roofWork)
			const round = Math.round(total)
			return round
		}
	}
}
</script>

<style scoped>

</style>
