<template>
  <card class="flex flex-col items-center justify-center">
    <div class="w-full p-4">
      <div class="text-90 font-normal text-2xl p-4 text-center">
        {{ card.title }}
      </div>
      <div>
        <div class="inline float-left my-input">
          <input
            id="least_no_of_orders"
            v-model="least_no_of_orders"
            type="number"
            min="1"
            step="1"
            placeholder="Least number of orders"
            class="w-full form-control form-input form-input-bordered inline"
          />
        </div>
        <div class="inline float-left my-input">
          <input
            id="popular_products_on_order"
            v-model="popular_products_on_order"
            type="number"
            min="1"
            step="1"
            placeholder="Popular products on order"
            class="w-full form-control form-input form-input-bordered inline"
          />
        </div>
        <div class="inline float-left my-input">
          <input
            id="sku_in_order_no"
            v-model="sku_in_order_no"
            type="number"
            min="1"
            step="1"
            placeholder="SKU in order"
            class="w-full form-control form-input form-input-bordered inline"
          />
        </div>
        <div class="inline float-left my-select">
          <multiselect
            v-model="selectedSku"
            id="ajax"
            label="sku"
            track-by="sku"
            placeholder="Type to search name and SKU"
            open-direction="bottom"
            :options="sku"
            :multiple="true"
            :searchable="true"
            :loading="isLoading"
            :internal-search="false"
            :clear-on-select="false"
            :close-on-select="false"
            :options-limit="300"
            :limit="15"
            :limit-text="limitText"
            :max-height="600"
            :show-no-results="false"
            :hide-selected="true"
            @search-change="asyncFind"
          >
            <span slot="noResult"
              >No elements found. Consider changing the search query.</span
            >
          </multiselect>
        </div>
        <button
          type="submit"
          @click="handleFilter"
          :disabled="loading"
          class="btn btn-default btn-primary inline-flex items-center relative"
        >
          <span class=""> Find </span>
        </button>
      </div>
    </div>

    <div
      v-if="card.rows.length == 0"
      class="text-80 font-normal p-6 text-center"
    >
      <h3>No records found. Consider changing the search query.</h3>
    </div>

    <table v-if="card.rows.length != 0" class="table w-full">
      <thead>
        <th v-for="(head, index) in card.heads" :key="index" class="text-left">
          {{ head }}
        </th>
      </thead>
      <tbody>
        <tr v-for="row in card.rows" :key="row.id">
          <td v-for="(value, index) in row" :key="row.id + '-' + index">
            {{ value }}
          </td>
          <td v-if="row.view" class="td-fit text-right pr-6 align-middle"></td>
        </tr>
      </tbody>
    </table>
  </card>
</template>

<script>
export default {
  props: [
    "card",

    // The following props are only available on resource detail cards...
    // 'resource',
    // 'resourceId',
    // 'resourceName',
  ],
  data() {
    return {
      least_no_of_orders: null,
      popular_products_on_order: null,
      sku_in_order_no: null,
      loading: false,
      selectedSku: [],
      sku: [],
      isLoading: false,
    };
  },
  methods: {
    handleFilter() {
      this.loading = true;
      axios
        .post("/api/filterData", {
          least_no_of_orders: this.least_no_of_orders,
          popular_products_on_order: this.popular_products_on_order,
          sku_in_order_no: this.sku_in_order_no,
          sku: this.selectedSku,
        })
        .then((response) => {
          this.loading = false;
          this.card = response.data;
        });
    },
    limitText(count) {
      return `and ${count} other`;
    },
    asyncFind(query) {
      if (!query) return;

      this.isLoading = true;
      axios
        .get("/api/getSelectData", {
          params: { sku: query },
        })
        .then((response) => {
          this.sku = response.data.items;
          this.isLoading = false;
        });
    },
    clearAll() {
      this.selectedSku = [];
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
.form-control {
  height: 40px;
}
.my-select {
  width: 35%;
}
.my-input {
  width: 18%;
}
.float-left {
  margin-left: 5px;
}
.btn-primary {
  margin-left: 5px;
}
</style>