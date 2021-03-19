<template>
  <card class="flex flex-col items-center justify-center">
    <h1 class="text-90 font-normal text-2xl p-4">{{ card.title }}</h1>
    {{ card.head }}
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

  mounted() {
    //
  },
};
</script>
