<script>
import { Line, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins
export default {
	name: 'LineChart',
	extends: Line,

	mixins: [reactiveProp],
	props: ['chartdata', 'options'],

	mounted () {
		// this.chartData is created in the mixin.
		// If you want to pass options please create a local options object
		this.renderChart(this.chartdata, this.options)
	}
}
</script>

<style scoped>

</style>
