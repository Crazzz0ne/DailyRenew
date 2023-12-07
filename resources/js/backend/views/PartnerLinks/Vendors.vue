<template>
  <div class="col">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div
            class="col-md-4 col-sm-12"
            style="margin-top: 3px;"
          >
            <div>
              <label for="items">Show</label>
            </div>
            <div
              id="items"
              class="btn-group"
              role="group"
              aria-label="Button group"
            >
              <div class="dropdown">
                <a
                  id="breadcrumb-dropdown-1"
                  class="btn dropdown-toggle"
                  href="#"
                  role="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                  style="background: rgb(248, 248, 248);margin-left: 5px;padding: 0px 15px;border-radius: 5px;border-color: #A6A6A6;"
                >Select</a>
                <div
                  class="dropdown-menu"
                  aria-labelledby="breadcrumb-dropdown-1"
                >
                  <a
                    class="dropdown-item"
                    href="/dashboard/partnerlinks/link/create"
                  >Create New
                    Link</a>
                  <a
                    class="dropdown-item"
                    href="/dashboard/partnerlinks/vendor"
                  >Partner</a>
                  <a
                    class="dropdown-item"
                    href="/dashboard/partnerlinks/category"
                  >Category</a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-sm-12 pt-2">
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div>
                  <label for="company">Companies</label>
                </div>
                <select
                  id="company"
                  v-model="sortSelections.currentCompany"
                  name="company"
                  class="text-capitalize"
                >
                  <option value="0">
                    Make A Selection
                  </option>
                  <option
                    v-for="company in companyNames"
                    :value="company.id"
                  >
                    {{ company.company_name }}
                  </option>
                </select>
              </div>
              <div class="col-md-6 col-sm-12">
                <div>
                  <label for="category">Category</label>
                </div>
                <select
                  id="category"
                  v-model="sortSelections.currentCategory"
                  name="category"
                  class="text-capitalize"
                >
                  <option value="0">
                    Make A Selection
                  </option>
                  <option
                    v-for="category in categoryNames"
                    :value="category.id"
                  >
                    {{ category.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <template v-for="category in categories">
          <Category
            :category="category"
            :sort-selections="sortSelections"
            :table-headers="tableHeaders"
            :current-u-r-l="currentURL"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import Category from './Category'

export default {
	name: 'Vendors',
	components: {
		Category
	},
	props: ['user'],
	data () {
		return {
			categories: null,
			companyNames: null,
			categoryNames: null,
			sortSelections: {
				currentCompany: 0,
				currentCategory: 0
			},
			tableHeaders: null,
			currentURL: {
				host: window.location.host,
				path: window.location.pathname
			}
		}
	},
	created () {
		axios.get('/api/vendors?token=97A1F62159F8CC6A450C30DBBCF4DABEEBF7881AA5D52C5619E3C4A0DF6F8AE9')
			.then((response) => {
				// set data in response
				this.categories = response.data.category
				this.companyNames = response.data.companyNames
				this.categoryNames = response.data.categoryNames
				this.tableHeaders = response.data.tableHeaders
			})
			.catch(error => console.log(error))
	}
}
</script>

<style scoped>

</style>
