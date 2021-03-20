<template>
  <card class="flex flex-col items-center justify-center">
    <div class="w-full p-4">
      <div class="text-90 font-normal text-2xl p-4 inline">
        {{ card.title }}
      </div>
      <div class="right inline float-right">
        <select
          @change="filter($event)"
          class="select-box-sm ml-auto min-w-24 h-6 text-xs appearance-none bg-40 pl-2 pr-6 active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline"
        >
          <option
            v-for="(filter, index) in card.timeFilters"
            :key="index"
            :value="filter.key"
          >
            {{ filter.value }}
          </option>
        </select>
      </div>
    </div>
    <table class="table w-full">
      <thead>
        <th v-for="(head, index) in card.heads" :key="index" class="text-left">
          {{ head }}
        </th>
      </thead>
      <tbody>
        <tr v-for="row in card.rows" :key="row.id">
          <td v-for="(value, index) in row" :key="row.id + '-' + index">
            {{
              index === "total_sum"
                ? Math.round((value + Number.EPSILON) * 100) / 100
                : value
            }}
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

  methods: {
    filter(event) {
      if (this.card.title === "Most popular products") {
        axios
          .get("/api/popularProducts/" + event.target.value)
          .then((response) => (this.card = response.data));
      }

      if (this.card.title === "Most popular first order products") {
        axios
          .get("/api/deepDive/1/" + event.target.value)
          .then((response) => (this.card = response.data));
      }

      if (this.card.title === "Most popular secound order products") {
        axios
          .get("/api/deepDive/2/" + event.target.value)
          .then((response) => (this.card = response.data));
      }

      if (this.card.title === "Most popular third order products") {
        axios
          .get("/api/deepDive/3/" + event.target.value)
          .then((response) => (this.card = response.data));
      }

      if (this.card.title === "Most popular fourd order products") {
        axios
          .get("/api/deepDive/4/" + event.target.value)
          .then((response) => (this.card = response.data));
      }

      if (this.card.title === "Most popular fifth order products") {
        axios
          .get("/api/deepDive/5/" + event.target.value)
          .then((response) => (this.card = response.data));
      }
    },
  },

  mounted() {
    //
  },
};
</script>
