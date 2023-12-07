<template>
  <div class="table-responsive">
    <table class="table is-bordered data-table">
      <thead>
        <tr>
          <th
            v-for="column in columns"
            :key="column.name"
            :class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
            :style="'width:'+column.width+';'+'cursor:pointer;'"
            @click="$emit('sort', column.name)"
          >
            {{ column.label }}
          </th>
        </tr>
      </thead>
      <slot />
    </table>
  </div>
</template>

<script>
export default {
	name: 'DataTable',
	props: ['columns', 'sortKey', 'sortOrders']
}
</script>
