module.exports = {
	root: true,
	env: {
		browser: true,
		es2021: true,
		commonjs: true,
		jquery: true
	},
	extends: [
		'plugin:vue/recommended',
		'standard'
	],
	parserOptions: {
		sourceType: 'module',
		ecmaFeatures: {
			modules: true
		}
	},
	plugins: [
		'vue'
	],

	rules: {
		indent: [2, 'tab'],
		'no-tabs': ['error', { allowIndentationTabs: true }],
		'no-console': 'off',
		'no-unused-vars': 'off',
		'vue/multi-word-component-names': 0
	},
	globals: {
		ga: true
	}
}
