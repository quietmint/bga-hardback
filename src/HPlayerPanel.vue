<template>
  <teleport :to="teleportTo">
    <div class="tailwind panelbg">
      <div class="panel-left text-center">
        <div id="tut_firstPlayer" v-if="player.order == 1" class="text-14 text-left pl-1 pt-1" v-text="i18n('first')"></div>

        <div class="panel-ink flex items-center text-20 font-bold text-noshadow">
          <div :id="'tut_ink_p' + player.id" class="flex-1" :title="i18n('ink') + ': ' + player.ink">{{ player.ink }}</div>
          <div :id="'tut_remover_p' + player.id" class="flex-1 text-black" :title="i18n('remover') + ': ' + player.remover">{{ player.remover }}</div>
        </div>
      </div>

      <div class="panel-right flex items-center justify-around text-noshadow">
        <!-- Literary Award -->
        <HTooltip v-if="options.value.awards" position="left">
          <template v-slot:tip>
            <div class="shadow bg-white text-black ring-2 ring-black rounded-lg overflow-hidden text-16 text-center whitespace-nowrap">
              <div class="p-2 bg-gray-200 font-bold">{{ i18n("award") }}</div>
              <div v-for="(value, key) in refs.value.awards" :key="key" class="px-2 py-1 border-t border-black border-opacity-30">
                {{ key }}
                <span class="pl-4">{{ value }}<Icon icon="star" class="inline text-18" /></span>
              </div>
            </div>
          </template>
          <template v-slot:default>
            <div :id="'tut_award_p' + player.id" class="panel-opt">{{ player.award || "" }}<Icon v-if="player.award" class="inline text-20" icon="star" /></div>
          </template>
        </HTooltip>

        <!-- Adverts -->
        <HTooltip v-if="options.value.adverts" position="left">
          <template v-slot:tip>
            <div class="shadow bg-white text-black ring-2 ring-black rounded-lg overflow-hidden text-16 text-center whitespace-nowrap">
              <div class="p-2 bg-gray-200 font-bold">{{ i18n("adverts") }}</div>
              <div v-for="(value, key) in refs.value.adverts" :key="key" class="px-2 py-1 border-t border-black border-opacity-30">
                {{ key }}¢
                <span class="pl-4">{{ value }}<Icon icon="star" class="inline text-18" /></span>
              </div>
            </div>
          </template>
          <template v-slot:default>
            <div :id="'tut_advert_p' + player.id" class="panel-opt">{{ player.advert || "" }}<Icon v-if="player.advert" class="inline text-20" icon="star" /></div>
          </template>
        </HTooltip>
      </div>

      <div class="panel-bottom mt-1 text-white text-17 font-bold flex text-noshadow">
        <div :id="'tut_hand_p' + player.id" class="rounded-lg bg-black bg-opacity-50 px-2 ml-1" :title="i18n('hand') + ': ' + player.activeCount"><Icon icon="hand" class="inline" /> {{ player.activeCount }}</div>
        <div :id="'tut_deck_p' + player.id" class="rounded-lg bg-black bg-opacity-50 px-2 ml-1" :class="{ 'cursor-pointer': player.id == game.player_id }" @click="player.id == game.player_id ? clickDeck() : null" :title="i18n('deck') + ': ' + player.deckCount"><Icon icon="deck" class="inline" /> {{ player.deckCount }}</div>
        <div :id="'tut_discard_p' + player.id" class="rounded-lg bg-black bg-opacity-50 px-2 ml-1" :class="{ 'cursor-pointer': player.id == game.player_id }" @click="player.id == game.player_id ? clickDiscard() : null" :title="i18n('discardButton') + ': ' + player.discardCount"><Icon icon="shuffle" class="inline" /> {{ player.discardCount }}</div>
      </div>

      <HTooltip :id="'tut_genreCounts_p' + player.id" class="genreCounts text-noshadow" position="left">
        <template v-slot:tip>
          <div class="shadow bg-white text-black ring-2 ring-black rounded-lg overflow-hidden text-16 text-left whitespace-nowrap">
            <div class="p-2 bg-gray-200 font-bold text-center" v-html="i18n('genreCountsTip', { player_name: `<span class='${player.colorText}'>${player.name}</span>`, count: genreTotal })"></div>
            <div v-for="gc in genreCountsNonEmpty" :key="gc.genre" class="px-2 py-1 border-t border-black border-opacity-30" :class="gc.class">
              <Icon class="inline text-20" :icon="gc.genre" /> {{ i18n(gc.genre) }}
              <div class="float-right pt-1 pl-4">{{ gc.display }}</div>
            </div>
          </div>
        </template>
        <template v-slot:default>
          <div class="rounded-bl-lg flex flex-grow overflow-hidden text-center">
            <div v-for="gc in genreCounts" :key="gc.genre" :style="{ width: gc.percent + '%' }" :class="gc.class"><Icon class="inline" :icon="gc.genre" /></div>
          </div>
        </template>
      </HTooltip>
    </div>
  </teleport>
</template>

<script lang="ts">
import HConstants from "./HConstants.js";
import HTooltip from "./HTooltip.vue";
import { Icon } from "@iconify/vue";

export default {
  name: "HPlayerPanel",
  inject: ["i18n", "options", "refs"],
  components: { Icon, HTooltip },

  props: {
    player: {
      type: Object,
      required: true,
    },
  },

  mounted() {
    document.getElementById("overall_player_board_" + this.player.id).classList.add(this.player.colorName);
  },

  computed: {
    teleportTo() {
      return "#player_board_" + this.player.id;
    },

    genreTotal() {
      if (!this.player.genreCounts) {
        return 0;
      }
      return this.player.genreCounts.reduce((acc, cur) => acc + cur);
    },

    genreCounts() {
      if (this.genreTotal == 0) {
        return [];
      }
      return [HConstants.ADVENTURE, HConstants.HORROR, HConstants.MYSTERY, HConstants.ROMANCE, HConstants.STARTER].map((id: number) => {
        const count = this.player.genreCounts[id];
        const genre = HConstants.GENRES[id];
        const percent = (count / this.genreTotal) * 100;
        return {
          class: `${genre.bg} ${genre.textLight}`,
          count: count,
          display: `${count} (${percent.toFixed(0)}%)`,
          genre: genre.icon,
          percent: percent,
        };
      });
    },

    genreCountsNonEmpty() {
      return this.genreCounts.filter((gc) => gc.count > 0);
    },
  },

  methods: {
    clickDiscard() {
      this.emitter.emit("clickDiscard");
    },

    clickDeck() {
      this.emitter.emit("clickDeck");
    },
  },
};
</script>
