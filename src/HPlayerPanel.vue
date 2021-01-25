<template>
  <teleport :to="teleportTo">
    <div class="tailwind">
      <!-- Ink, remover, card counts -->
      <div :class="colorClass" class="grid grid-cols-4 gap-1 mt-1 text-14 text-center">
        <div class="rounded-lg bg-white bg-opacity-50" title="Ink">
          <Icon class="text-32 mx-auto" icon="ink" />
          {{ ink }}
        </div>
        <div class="rounded-lg bg-white bg-opacity-50" title="Remover">
          <Icon class="text-32 mx-auto" icon="remover" />
          {{ remover }}
        </div>
        <div class="rounded-lg bg-white bg-opacity-50" title="Cards in deck">
          <Icon class="text-32 mx-auto" icon="cards" />
          {{ deckCount }}
        </div>
        <div class="rounded-lg bg-white bg-opacity-50" title="Cards in discard">
          <Icon class="text-32 mx-auto" icon="shuffle" />
          {{ discardCount }}
        </div>
      </div>

      <!-- Advert tracker -->
      <div class="flex justify-between mt-1">
        <div v-for="a in advertTracker" :key="a.level" :class="a.class" :title="a.title">{{ a.level }}<Icon class="inline text-18" icon="star" /></div>
      </div>

      <!-- Genre counts -->
      <div class="genreCounts mt-1 flex flex-grow rounded-lg overflow-hidden text-center">
        <div v-for="g in genreCounts" :key="g.genre" :style="{ width: g.percent + '%' }" :class="g.class" :title="g.title"><Icon class="inline h-5" :icon="g.genre" /></div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon } from "@iconify/vue";

export default {
  name: "HPlayerPanel",
  components: { Icon },

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

    advertTracker() {
      const level = this.player.advert || 0;
      return [3, 6, 10, 15, 20].map((x: number) => {
        const purchased = level >= x;
        return {
          level: x,
          class: `p-1 rounded-lg whitespace-nowrap ${purchased ? "bg-white bg-opacity-50 font-bold" : "bg-white opacity-25"}`,
          title: `${x}-point advert ${purchased ? "purchased" : "not purchased"}`,
        };
      });
    },

    genreCounts() {
      const counts = this.player.genreCounts || [7, 0, 0, 0, 0];
      const total = counts.reduce((acc, cur) => acc + cur);
      return [
        {
          genre: "starter",
          class: "bg-gray-600 text-gray-100",
          count: counts[Constants.STARTER],
        },
        {
          genre: "adventure",
          class: "bg-yellow-400 text-yellow-900",
          count: counts[Constants.ADVENTURE],
        },
        {
          genre: "horror",
          class: "bg-green-700 text-green-100",
          count: counts[Constants.HORROR],
        },
        {
          genre: "mystery",
          class: "bg-blue-600 text-blue-100",
          count: counts[Constants.MYSTERY],
        },
        {
          genre: "romance",
          class: "bg-red-700 text-red-100",
          count: counts[Constants.ROMANCE],
        },
      ].map((x: any) => {
        x.title = `Deck contains ${x.count}/${total} ${x.genre} cards`;
        x.percent = (x.count / total) * 100;
        return x;
      });
    },
  },
};
</script>
