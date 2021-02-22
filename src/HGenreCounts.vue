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

      return [Constants.ADVENTURE, Constants.HORROR, Constants.MYSTERY, Constants.ROMANCE, Constants.STARTER].map((id: number) => {
        const count = this.counts[id];
        const genre = Constants.GENRES[id];
        const percent = (count / this.genreTotal) * 100;
        return {
          class: `${genre.bg} ${genre.textLight}`,
          count: count,
          genre: genre.icon,
          percent: percent,
          title: `${this.i18n(genre.icon)}: ${count}/${this.genreTotal} (${percent.toFixed(0)}%)`,
        };
      });
    },
  },
};
</script>
