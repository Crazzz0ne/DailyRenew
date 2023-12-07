<script>
import { Pie } from 'vue-chartjs'

export default {
	extends: Pie,

	props: {
		chartdata: {
			type: Object,
			default: null
		},
		options: {
			type: Object,
			default: null
		}
	},
	mounted () {
		this.renderChart(this.chartdata, this.options)
	},
	destroyed () {
		console.log('Destroyed component, width null?: ', this.$data._chart.width)
	},
	beforeDestroy () {
		this.$data._chart.destroy()
	}
}
</script>

<style>
</style>
