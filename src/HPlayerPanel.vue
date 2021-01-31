<template>
  <teleport :to="teleportTo">
    <div class="tailwind">
      <!-- Genre counts -->
      <div v-if="genreCounts" class="genreCounts my-1 flex flex-grow rounded-lg overflow-hidden text-center">
        <div v-for="g in genreCounts" :key="g.genre" :style="{ width: g.percent + '%' }" :class="g.class" :title="g.title"><Icon class="inline h-5" :icon="g.genre" /></div>
      </div>

      <!-- Coop signature genre -->
      <div v-if="player.id == 0">
        <div v-if="genreNovel" class="genreNovel py-1 mt-2 border-t border-b border-black text-center text-20">{{ genreNovel.title }}</div>
        <div v-if="genreNovel" class="flex items-center mt-1 text-14">
          <Icon :icon="genreNovel.icon" :class="genreNovel.color" class="text-24 flex-none mr-2" />
          <div>{{ genreNovel.description }}<Icon v-if="genreNovel.description2" icon="star" class="inline text-17" />{{ genreNovel.description2 }}</div>
        </div>
      </div>

      <!-- Ink, remover, card counts -->
      <div v-if="player.id" :class="colorClass" class="grid grid-cols-4 gap-1 mt-1 text-14 text-center">
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
      <div v-if="player.id" class="flex justify-between mt-1">
        <div v-for="a in advertTracker" :key="a.level" :class="a.class" :title="a.title">{{ a.level }}<Icon class="inline text-18" icon="star" /></div>
      </div>

      <!-- First player marker -->
      <div v-if="player.order == 1">TODO: First player marker</div>
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

    genreNovel() {
      if (this.player.id == 0 && this.player.genre) {
        if (this.player.genre == Constants.ADVENTURE) {
          return {
            title: "Adventures in Peril",
            description: "If there is an Adventure card in the offer row, Penny gains 1",
            description2: " for each card purchased by any player",
            icon: "adventure",
            color: "text-yellow-900",
          };
        } else if (this.player.genre == Constants.HORROR) {
          return {
            title: "The Horrors of Fear",
            description: "If there is a Horror card in the offer row, Penny gains 1",
            description2: " for each ink used by any player",
            icon: "horror",
            color: "text-green-700",
          };
        } else if (this.player.genre == Constants.MYSTERY) {
          return {
            title: "A Mysterious Mystery",
            description: "Each time Penny claims a Mystery card, she also jails the cheapest card in the offer row",
            description2: "",
            icon: "mystery",
            color: "text-blue-700",
          };
        } else if (this.player.genre == Constants.ROMANCE) {
          return {
            title: "Romancing the Heart",
            description: "Double the value of each Romance card Penny claims from the offer row",
            description2: "",
            icon: "romance",
            color: "text-red-700",
          };
        }
      }
    },

    genreTotal() {
      const counts = this.player.genreCounts || [0];
      return counts.reduce((acc, cur) => acc + cur);
    },

    genreCounts() {
      if (this.genreTotal == 0) {
        return [];
      }

      const counts = this.player.genreCounts;
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
        x.title = `Deck contains ${x.count}/${this.genreTotal} ${x.genre} cards`;
        x.percent = (x.count / this.genreTotal) * 100;
        return x;
      });
    },
  },
};
</script>
