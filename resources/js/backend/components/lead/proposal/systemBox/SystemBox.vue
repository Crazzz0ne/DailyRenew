<template>
  <div>
    <div>
      <div class="row py-3">
        <!--        <div class="col">-->
        <!--          <mazBtn @click="showOldModel++">-->
        <!--            click me-->
        <!--          </mazBtn>-->
        <!--          <OldProposals :model="showOldModel" />-->
        <!--        </div>-->
        <div class="col">
          <MazBtn
            @click="showSavingsModel = true"
          >
            Savings Break Down
          </MazBtn>
          <MazDialog
            v-model="showSavingsModel"
            fullsize
            title="Savings Break Down"
          >
            <SavingsBreakDown
              :apr="apr"
              :old-bill-average="utility"
              :new-bill="monthlyPayment"
            />
          </MazDialog>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import OldProposals from './OldProposals'
import SavingsBreakDown from '../../savingsBreakdown/SavingsBreakDown'

export default {
	name: 'SystemBox',
	components: { SavingsBreakDown, OldProposals },
	props: ['apr', 'utility', 'monthlyPayment'],
	data () {
		return {
			showOldModel: 1,
			savingsBreakdownModel: 1,
			showSavingsModel: false

		}
	}
}
</script>

<style scoped>

</style>
