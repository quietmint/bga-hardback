<template>
  <teleport :to="teleportTo">
    <div class="tailwind">
      <!-- Ink, remover, card counts -->
      <div :class="colorClass" class="grid grid-cols-4 gap-1 mt-1 text-sm text-center">
        <div class="rounded-lg bg-gray-50 bg-opacity-50" title="Ink">
          <Icon class="text-4xl mx-auto" icon="ink" />
          {{ ink }}
        </div>
        <div class="rounded-lg bg-gray-50 bg-opacity-50" title="Remover">
          <Icon class="text-4xl mx-auto" icon="remover" />
          {{ remover }}
        </div>
        <div class="rounded-lg bg-gray-50 bg-opacity-50" title="Cards in deck">
          <Icon class="text-4xl mx-auto" icon="cards" />
          {{ deckCount }}
        </div>
        <div class="rounded-lg bg-gray-50 bg-opacity-50" title="Cards in discard">
          <Icon class="text-4xl mx-auto" icon="shuffle" />
          {{ discardCount }}
        </div>
      </div>

      <!-- Genre counts -->
      <div class="genreCounts flex rounded-lg overflow-hidden a mt-1 text-center text-md">
        <div v-for="g in genreCounts" :key="g.genre" :title="g.count + ' ' + g.genre + ' cards'" :style="{ width: g.percent + '%' }" :class="g.color"><Icon class="inline" :icon="g.genre" /></div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon } from "@iconify/vue";

export default {
  name: "HPlayerPanel",
  components: {
    Icon,
  },
  props: {
    player: {
      type: Object,
      required: true,
    },
  },
  data() {
    return { Constants };
  },
  computed: {
    teleportTo() {
      return "#player_board_" + this.player.id;
    },
    colorClass() {
      return this.player.colorText;
    },
    coins() {
      return this.player.coins || 0;
    },
    ink() {
      return this.player.ink || 0;
    },
    remover() {
      return this.player.remover || 0;
    },
    deckCount() {
      return this.player.deckCount || 0;
    },
    discardCount() {
      return this.player.discardCount || 0;
    },
    genreCounts() {
      const counts = this.player.genreCounts || [7, 0, 0, 0, 0];
      const total = counts.reduce((acc, cur) => acc + cur);
      return [
        {
          genre: "starter",
          color: "bg-gray-600 text-gray-100",
          count: counts[Constants.STARTER],
          percent: (counts[Constants.STARTER] / total) * 100,
        },
        {
          genre: "adventure",
          color: "bg-yellow-400 text-yellow-900",
          count: counts[Constants.ADVENTURE],
          percent: (counts[Constants.ADVENTURE] / total) * 100,
        },
        {
          genre: "horror",
          color: "bg-green-700 text-green-100",
          count: counts[Constants.HORROR],
          percent: (counts[Constants.HORROR] / total) * 100,
        },
        {
          genre: "mystery",
          color: "bg-blue-600 text-blue-100",
          count: counts[Constants.MYSTERY],
          percent: (counts[Constants.MYSTERY] / total) * 100,
        },
        {
          genre: "romance",
          color: "bg-red-700 text-red-100",
          count: counts[Constants.ROMANCE],
          percent: (counts[Constants.ROMANCE] / total) * 100,
        },
      ];
    },
  },
};
</script>
