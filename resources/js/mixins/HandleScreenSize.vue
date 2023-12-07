<script>
export default {
	data: function () {
		return {
			windowWidth: 0
		}
	},
	computed: {
		isExtraSmall () {
			return this.windowWidth < 500
		},
		isSmall () {
			return this.windowWidth > 500 && this.windowWidth < 768
		},
		isMedium () {
			return this.windowWidth > 768 && this.windowWidth < 1024
		},
		isLarge () {
			return this.windowWidth > 1024 && this.windowWidth < 1500
		},
		isExtraLarge () {
			return this.windowWidth > 1500
		}
	},
	created () {
		this.setWidth()
		window.addEventListener('resize', this.setWidth)
	},
	destroyed () {
		window.removeEventListener('resize', this.setWidth)
	},
	methods: {
		setWidth () {
			this.windowWidth = window.innerWidth
		}
	}
}

</script>
