<template>
  <nav
    v-if="!client"
    class="Page navigation"
  >
    <ul class="pagination justify-content-center">
      <li class="page-item disabled">
        <a
          v-if="pagination.prevPageUrl"
          class="button is-small pagination-previous"
          @click="$emit('prev');"
        >Previous</a>
      </li>
    </ul>
    <span class="page-stats">{{ pagination.from }} - {{ pagination.to }} of {{ pagination.total }}</span>
    <a
      v-if="pagination.prevPageUrl"
      class="button is-small pagination-previous"
      @click="$emit('prev');"
    >
      Prev
    </a>
    <a
      v-else
      class="button is-small pagination-previous"
      :disabled="true"
    >
      Prev
    </a>

    <a
      v-if="pagination.nextPageUrl"
      class="button is-small pagination-next"
      @click="$emit('next');"
    >
      Next
    </a>
    <a
      v-else
      class="button is-small pagination-next"
      :disabled="true"
    >
      Next
    </a>
  </nav>

  <nav
    v-else
    class="Page navigation"
  >
    <span class="page-stats">
      {{ pagination.from }} - {{ pagination.to }} of {{ filtered.length }}
      <span v-if="filtered.length < pagination.total">(filtered from {{ pagination.total }} total entries)</span>
    </span>
    <a
      v-if="pagination.prevPage"
      class="button is-small pagination-previous"
      @click="$emit('prev');"
    >
      Prev
    </a>
    <a
      v-else
      class="button is-small pagination-previous"
      :disabled="true"
    >
      Prev
    </a>

    <a
      v-if="pagination.nextPage"
      class="button is-small pagination-next"
      @click="$emit('next');"
    >
      Next
    </a>
    <a
      v-else
      class="button is-small pagination-next"
      :disabled="true"
    >
      Next
    </a>
  </nav>
</template>

<script>
export default {
	name: 'Pagination',
	props: ['pagination', 'client', 'filtered']
}
</script>

<style lang="scss">
    .pagination {
        justify-content: flex-end !important;
        .page-stats {
            align-items: center;
            margin-right: 5px;
        }
        i {
            color: #3273dc !important;
        }
    }
</style>
