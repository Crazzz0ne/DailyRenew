<template>
  <div
    v-show="showCurrentCategory"
    class="mx-4"
  >
    <h3 class="text-capitalize py-2">
      {{ $props.category.name }}
    </h3>
    <div class="row justify-content-md-start">
      <table class="sheru-cp-section responsive-table">
        <thead class="responsive-table__head">
          <tr class="responsive-table__row">
            <th
              v-for="headers in $props.tableHeaders"
              :key="headers.id"
              class="responsive-table__cell responsive-table__cell--head"
              @click="sort(headers)"
            >
              <div
                v-show="headers.sortable"
                class="triangle"
                :class="{ flip: tableHeadersClicked.id }"
              />
              <span class="text-capitalize">{{ headers.name }}</span>
            </th>
          </tr>
        </thead>
        <tbody class="responsive-table__body">
          <template v-for="link in $props.category.links">
            <Link
              :company="company"
              :link="link"
              :sort-selections="sortSelections"
              :current-u-r-l="$props.currentURL"
            />
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import Link from './Link'

export default {
	name: 'Category',
	components: {
		Link
	},
	props: ['category', 'sortSelections', 'tableHeaders', 'currentURL'],
	data () {
		return {
			company: null,
			sortOrder: 'asc',
			tableHeadersClicked: {
				id: false
			}
		}
	},
	computed: {
		showCurrentCategory: function () {
			if (this.$props.sortSelections.currentCategory == 0) {
				return true
			} else if (this.$props.sortSelections.currentCategory == this.$props.category.id) {
				return true
			} else {
				return false
			}
		},
		getAllSortableHeads: function () {
			const sortable = _.cloneDeep(this.$props.tableHeaders)
			const sortState = []

			_.forEach(sortable, (item) => {
				sortState.push({ name: item.name, clicked: false, sort: 'asc' })
			})

			return sortState
		}
	},
	methods: {
		sort: function (header) {
			let links = _.cloneDeep(this.$props.category.links)
			let selection = _.filter(this.getAllSortableHeads, { name: header.name })
			selection = selection[0]

			if (selection.sort === 'asc') {
				links = _.orderBy(links, [header.sortName], ['desc'])
				this.$props.category.links = links

				_.forEach(this.getAllSortableHeads, (item) => {
					if (item.name === selection.name) {
						item.sort = 'desc'
					}
				})
			} else {
				links = _.orderBy(links, [header.sortName], ['asc'])
				this.$props.category.links = links

				_.forEach(this.getAllSortableHeads, (item) => {
					if (item.name === selection.name) {
						item.sort = 'asc'
					}
				})
			}
		}
	}
	// TODO: make this work as a computed property
	// sort: function (header) {
	//     let links = this.$props.category.links;
	//     if (this.sortOrder === 'asc') {
	//         links = _.orderBy(links, [header.name], ['desc']);
	//         this.$props.category.links = links;
	//         this.sortOrder = 'desc';
	//     } else {
	//         links = _.orderBy(links, [header.name], ['asc']);
	//         this.$props.category.links = links;
	//         this.sortOrder = 'asc';
	//     }
	// },

}
</script>

<style scoped>

</style>
