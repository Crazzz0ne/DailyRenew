import NotificationContainer from './components/Notifications/NotificationContainer'

// Loaded after CoreUI app.js
import Vue from 'vue'

import {
	MazPagination,
	MazPicker,
	MazPhoneNumberInput,
	MazSwitch,
	MazLoader,
	MazTabsBar,
	MazTabsContent,
	MazTabsContentItem,
	MazBtn,
	MazSpinner,
	MazDialog,
	MazInput,
	MazCheckbox,
	MazAvatar,
	MazCollapse,
	MazBtnGroup,
	MazSelect,
	MazStepper,
	MazDropzone,
	MazListItem,
	MazList,
	MazTransitionExpand,
	MazRadio,
	MazBottomSheet
} from 'maz-ui'

import VueTheMask from 'vue-the-mask'

// VueRouter
import router from './routes/routes'

// Vuex
import store from './store'

// import {Datetime} from 'vue-datetime';
//
// Vue.component('datetime', Datetime);
//
// import {Settings} from 'luxon';
// // import 'vue-datetime/dist/vue-datetime.css';
//
// Settings.defaultLocale = 'en';

import IdleVue from 'idle-vue'

import Permissions from '../mixins/Permissions'
import Market from '../mixins/Market'

import GFunction from '../mixins/GFunction'

// const LeadApp = () => import('./views/Lead/LeadApp');
// const CalenderApp = () => import('./views/Calender/CalenderApp');
// const PartnerLinksApp = () => import('./views/PartnerLinks/PartnerLinksApp');

import VueDayjs from 'vue-dayjs-plugin'

// https://www.npmjs.com/package/vue-chat-scroll
import VueChatScroll from 'vue-chat-scroll'

// google maps auto complete https://github.com/xkjyeah/vue-google-maps
import * as VueGoogleMaps from 'vue2-google-maps'

import axios from '../src/plugin/axios'

// Mount Each Apps entry point
import LeadApp from './views/Lead/LeadApp'
import ReportingApp from './views/Reporting/ReportingApp'
import CalenderApp from './views/Calender/CalenderApp'
import CommissionApp from './views/Commission/CommissionApp'
import PartnerLinksApp from './views/PartnerLinks/PartnerLinksApp'
import SettingsApp from './views/Settings/SettingsApp'
import TestListener from './views/TestListener'
import LanguageSelect from './components/Selects/LanguageSelect'
import CitySelect from './components/Selects/CitySelect'
import RemoteSelect from './components/Selects/RemoteSelect'
import RoundRobinApp from './views/RoundRobin/RoundRobinApp'
import EligibleCityByOfficeCitySelectGroup from './components/Settings/EligibleCityByOfficeCitySelectGroup'
import AudioPlayer from './components/AudioPlayer'

// Declare Casey's Components
import CustomTabsHeader from './components/CustomTabsHeader'
import AutoAssignRR from './components/Switch/AutoAssignRR'
import NotificationListButtons from './components/Notifications/NotificationListButtons'
import AddCityToRep from './components/RoundRobin/AddCityToRep'
import TimezoneSelect from './components/Selects/TimezoneSelect'
import Teams from './components/Teams/Office/Teams'

require('../directives/click-outside') // Adds custom directive to app (See NotificationContainer -> NotificationList -> v-click-outside="hide")

window.Vue = Vue
Vue.config.productionTip = false

Vue.use(MazPicker)
Vue.use(MazBtnGroup)
Vue.use(MazInput)
Vue.use(MazDialog)
Vue.use(MazSwitch)
Vue.use(MazPhoneNumberInput)
Vue.use(MazPagination)
Vue.use(MazLoader)
Vue.use(MazTabsBar)
Vue.use(MazTabsContent)
Vue.use(MazTabsContentItem)
Vue.use(MazBtn)
Vue.use(MazSpinner)
Vue.use(MazCheckbox)
Vue.use(MazAvatar)
Vue.use(MazCollapse)
Vue.use(MazSelect)
Vue.use(MazStepper)
Vue.use(MazDropzone)
Vue.use(MazListItem)
Vue.use(MazList)
Vue.use(MazTransitionExpand)
Vue.use(MazRadio)
Vue.use(MazBottomSheet)

Vue.use(VueTheMask)

const eventsHub = new Vue()
const options = {
	eventEmitter: eventsHub,
	idleTime: 60000
}

Vue.use(IdleVue, options)

Vue.mixin(Permissions)

Vue.mixin(Market)

Vue.mixin(GFunction)

Vue.use(VueDayjs)

Vue.use(VueChatScroll)

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
Vue.use(VueGoogleMaps, {
	load: {
		key: 'AIzaSyC-U-rgsR2zqwgbLyA6TX1AUcoNX6wT8LQ',
		libraries: 'places' // This is required if you use the Autocomplete plugin
		// OR: libraries: 'places,drawing'
		// OR: libraries: 'places,drawing,visualization'
		// (as you require)

		/// / If you want to set the version, you can do so:
		// v: '3.26',
	},

	/// / If you intend to programmatically custom event listener code
	/// / (e.g. `this.$refs.gmap.$on('zoom_changed', someFunc)`)
	/// / instead of going through Vue templates (e.g. `<GmapMap @zoom_changed="someFunc">`)
	/// / you might need to turn this on.
	// autobindAllEvents: false,

	/// / If you want to manually install components, e.g.
	/// / import {GmapMarker} from 'vue2-google-maps/src/components/marker'
	/// / Vue.component('GmapMarker', GmapMarker)
	/// / then set installComponents to 'false'.
	/// / If you want to automatically install all the components this property must be set to 'true':
	installComponents: true
})

Vue.component('AudioPlayer', AudioPlayer)

Vue.component('LanguageSelect', LanguageSelect)
Vue.component('RemoteSelect', RemoteSelect)
Vue.component('CitySelect', CitySelect)
Vue.component('EligibleCityByOfficeCitySelectGroup', EligibleCityByOfficeCitySelectGroup)
Vue.component('CustomTabsHeader', CustomTabsHeader)
Vue.component('AutoAssignRR', AutoAssignRR)
Vue.component('AddCityToRep', AddCityToRep)
Vue.component('NotificationContainer', NotificationContainer)
Vue.component('NotificationListButtons', NotificationListButtons)
Vue.component('TimezoneSelect', TimezoneSelect)
Vue.component('Teams', Teams)

new Vue({
	components: {
		LeadApp,
		CalenderApp,
		CommissionApp,
		PartnerLinksApp,
		TestListener,
		ReportingApp,
		CitySelect,
		SettingsApp,
		LanguageSelect,
		RemoteSelect,
		EligibleCityByOfficeCitySelectGroup,
		RoundRobinApp,
		CustomTabsHeader,
		AutoAssignRR,
		NotificationContainer,
		AddCityToRep
		// NotificationList,
		// NotificationItem,
		// NotificationSettings,
	},
	router,
	store
}).$mount('#app')

ga('set', 'page', router.currentRoute.path)
ga('send', 'pageview')
ga('send', 'title')
const DEFAULT_TITLE = 'Scout'
router.afterEach((to, from) => {
	ga('set', 'page', to.path)
	ga('set', 'title', to.name)
	ga('send', 'pageview')
	Vue.nextTick(() => {
		document.title = to.name || DEFAULT_TITLE
	})
})
