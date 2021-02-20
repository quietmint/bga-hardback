<template>
  <div class="genreCounts rounded-bl-lg flex flex-grow overflow-hidden text-center">
    <div v-for="g in genreCounts" :key="g.genre" :style="{ width: g.percent + '%' }" :class="g.class" :title="g.title"><Icon class="inline" :icon="g.genre" /></div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon } from "@iconify/vue";

export default {
  name: "HGenreCounts",
  inject: ["i18n"],
  components: { Icon },

  props: {
    counts: {
      type: Object,
      required: true,
    },
  },

  computed: {
    genreTotal() {
      if (!this.counts) {
        return 0;
      }

      return this.counts.reduce((acc, cur) => acc + cur);
    },

    genreCounts() {
      if (this.genreTotal == 0) {
        return [];
      }

      return [
        {
          genre: "adventure",
          class: "bg-yellow-400 text-yellow-900",
          count: this.counts[Constants.ADVENTURE],
        },
        {
          genre: "horror",
          class: "bg-green-700 text-green-100",
          count: this.counts[Constants.HORROR],
        },
        {
          genre: "mystery",
          class: "bg-blue-600 text-blue-100",
          count: this.counts[Constants.MYSTERY],
        },
        {
          genre: "romance",
          class: "bg-red-700 text-red-100",
          count: this.counts[Constants.ROMANCE],
        },
        {
          genre: "starter",
          class: "bg-gray-600 text-gray-100",
          count: this.counts[Constants.STARTER],
        },
      ].map((x: any) => {
        x.percent = (x.count / this.genreTotal) * 100;
        x.title = `${this.i18n(x.genre)}: ${x.count}/${this.genreTotal} (${x.percent.toFixed(0)}%)`;
        return x;
      });
    },
  },
};
</script>
