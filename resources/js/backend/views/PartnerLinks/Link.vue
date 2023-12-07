<template v-if="renderComponent">
  <tr
    v-show="showCurrentCompany"
    class="responsive-table__row"
  >
    <!--        <th class="responsive-table__cell responsive-table__cell&#45;&#45;head" scope="row" data-title="ID">-->
    <!--            {{this.$props.link.sort_id}}-->
    <!--        </th>-->
    <td
      class="responsive-table__cell"
      data-title="Link"
      style="display: flex;"
      @click="openLink"
    >
      <div
        class="logo"
        style="cursor: pointer;"
      >
        <img
          :src="`${s3Base}${$props.link.vendors.picture}`"
          :alt="$props.link.vendors.company_name + ' Logo'"
        >
      </div>
    </td>
    <td
      :class="{canClick : true}"
      class="responsive-table__cell"
      data-title="Company"
      @click="viewVendor"
    >
      {{ $props.link.vendors.company_name }}
    </td>
    <td
      :class="{canClick : true}"
      class="responsive-table__cell"
      data-title="Representative"
      @click="viewLink"
    >
      {{ $props.link.representative }}
    </td>
    <td
      :class="{canClick : true}"
      class="responsive-table__cell"
      data-title="Notes"
      @click="viewLink"
    >
      {{ $props.link.notes }}
    </td>
    <td
      class="responsive-table__cell"
      data-title="Email"
    >
      <a :href="'mailto:'+$props.link.email">{{ $props.link.email }}</a>
    </td>
    <td
      class="responsive-table__cell"
      data-title="Cell Phone"
    >
      <a :href="'tel:'+$props.link.cell_phone">{{ $props.link.cell_phone }}</a>
    </td>
    <td
      class="responsive-table__cell"
      data-title="Office Phone"
    >
      <template v-if="$props.link.extension">
        <a :href="`tel:${$props.link.office_phone},${$props.link.extension}`">{{ $props.link.office_phone }}
          x{{ $props.link.extension }}</a>
      </template>
      <template v-else>
        <a :href="'tel:'+$props.link.office_phone">{{ $props.link.office_phone }} </a>
      </template>
    </td>
  </tr>
</template>

<script>
export default {
	name: 'Link',
	props: ['company', 'link', 'sortSelections', 'currentURL'],
	data () {
		return {
			s3Base: 'https://solcalenergy-dashboard.s3.us-west-1.amazonaws.com/'
		}
	},
	computed: {
		showCurrentCompany: function () {
			if (this.$props.sortSelections.currentCompany == 0) {
				return true
			} else if (this.$props.sortSelections.currentCompany == this.$props.link.vendors.id) {
				return true
			} else {
				return false
			}
		}
	},
	methods: {
		openLink: function () {
			if (this.$props.link.web_address !== null) {
				if (this.$props.link.web_address) {
					window.open(`https://${this.$props.link.web_address}`)
				} else {
					window.open(`https://${this.$props.link.vendors.web_address}`)
				}
			}
		},

		viewLink: function () {
			window.location.href = `${this.$props.currentURL.path}/link/${this.$props.link.id}`
		},
		viewVendor: function () {
			return window.location.href = `${this.$props.currentURL.path}/vendor/${this.$props.link.vendors.id}`
		}
	}
}
</script>

<style scoped>
    .logo {
        width: 50px;
        height: 50px;
        border: 2px solid gainsboro;
        border-radius: 15px;
        justify-content: center;
        display: flex;
        flex-direction: row;
        overflow: hidden;
    }

    img {
        flex: 1;
        height: 100%;
    }

    .canClick {
        cursor: pointer
    }
</style>
