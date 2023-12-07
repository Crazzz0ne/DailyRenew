<template>
  <div v-show="currentSelection">
    <h1 class="pb-2">
      {{ name }}
    </h1>
    <table
      class="table shadow "
      style="border: 2px solid rgb(200, 206, 211);border-radius: 10px; background-color: white"
    >
      <thead>
        <tr>
          <template v-for="(heads, index) in headers">
            <th @click="sort(heads, index)">
              {{ heads }}
            </th>
          </template>
        </tr>
      </thead>
      <tbody>
        <template v-for="(row, index) in rows">
          <tr
            v-if="row['Monthly Rank'] === 1"
            class="blue"
          >
            <template v-for="(entries, index) in row">
              <td>{{ entries }}</td>
            </template>
          </tr>
          <tr
            v-else-if="row['Yearly Rank'] === 1"
            class="gold"
          >
            <template v-for="(entries, index) in row">
              <td>{{ entries }}</td>
            </template>
          </tr>
          <tr
            v-else-if="row['Yearly Rank'] === 2"
            class="silver"
          >
            <template v-for="(entries, index) in row">
              <td>{{ entries }}</td>
            </template>
          </tr>
          <tr
            v-else-if="row['Yearly Rank'] === 3"
            class="bronze"
          >
            <template v-for="(entries, index) in row">
              <td>{{ entries }}</td>
            </template>
          </tr>
          <tr v-else>
            <template v-for="(entries, index) in row">
              <td>{{ entries }}</td>
            </template>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
	name: 'StandingTable',
	props: {
		tableData: Array,
		name: String,
		selectedStanding: String
	},
	data () {
		return {
			headers: this.$props.tableData[0],
			rows: _.orderBy(this.$props.tableData.splice(1), ['Yearly Rank'], ['desc']),
			topEntries: []
		}
	},
	computed: {
		currentSelection: function () {
			if (this.selectedStanding === null || this.selectedStanding == 0) {
				return true
			} else if (this.selectedStanding === this.name) {
				return true
			} else {
				return false
			}
		}
	},
	methods: {
		sort (heads, index) {
			if (heads == 'Yearly Rank' || heads == 'Monthly Rank') {
				this.rows = _.orderBy(this.rows, [heads], ['asc'])
			} else {
				this.rows = _.orderBy(this.rows, [heads], ['desc'])
			}
		}
	}
}
</script>
<style scoped>
    tr:first-child td {
        font-weight: bold;
        font-size: 16px;
    }

    .gold {
        background: rgba(248, 209, 3, .8) !important;
    }

    .silver {
        background: rgba(176, 176, 176, .5) !important
    }

    .bronze {
        background: RGBA(243, 206, 162, .8) !important;
    }

    .blue {
        background-color: #0ED2F7;
        color: white;
    }
</style>
