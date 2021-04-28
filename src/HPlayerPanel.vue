<template>
  <teleport :to="teleportTo">
    <div class="tailwind panelbg">
      <div class="panel-left text-center">
        <div v-if="player.order == 1" class="text-13 text-left pl-1 pt-1" v-text="i18n('first')"></div>

        <div class="panel-ink flex items-center text-20 font-bold text-noshadow">
          <div class="flex-1" :title="i18n('ink') + ': ' + player.ink">{{ player.ink }}</div>
          <div class="flex-1 text-black" :title="i18n('remover') + ': ' + player.remover">{{ player.remover }}</div>
        </div>
      </div>

      <div class="panel-right flex items-center justify-around text-noshadow">
        <HTooltip v-if="options.value.awards" :header="i18n('award')" :table="refs.value.awards" valueIcon="star">
          <div class="panel-opt" :title="i18n('award') + ': ' + player.award">{{ player.award || "" }}<Icon v-if="player.award" class="inline text-20" icon="star" /></div>
        </HTooltip>
        <HTooltip v-if="options.value.adverts" :header="i18n('adverts')" :table="refs.value.adverts" keySuffix="¢" valueIcon="star">
          <div class="panel-opt" :title="i18n('adverts') + ': ' + player.advert">{{ player.advert || "" }}<Icon v-if="player.advert" class="inline text-20" icon="star" /></div>
        </HTooltip>
      </div>

      <div class="panel-bottom mt-1 text-white flex text-noshadow">
        <div class="rounded-lg bg-black bg-opacity-40 px-2 ml-1" :title="i18n('hand') + ': ' + player.activeCount"><Icon icon="hand" class="inline" /> {{ player.activeCount }}</div>
        <div class="rounded-lg bg-black bg-opacity-40 px-2 ml-1" :title="i18n('deck') + ': ' + player.deckCount"><Icon icon="deck" class="inline" /> {{ player.deckCount }}</div>
        <div class="rounded-lg bg-black bg-opacity-40 px-2 ml-1" :class="{ 'cursor-pointer': player.id == game.player_id }" @click="player.id == game.player_id ? clickDiscard() : null" :title="i18n('discardButton') + ': ' + player.discardCount"><Icon icon="shuffle" class="inline" /> {{ player.discardCount }}</div>
      </div>

      <HTooltip class="genreCounts text-noshadow" :header="i18n('genreCountsTip', { player_name: `<span class='${player.colorText}'>${player.name}</span>` })" :table="genreTable">
        <div class="rounded-bl-lg flex flex-grow overflow-hidden text-center">
          <div v-for="gc in genreCounts" :key="gc.genre" :style="{ width: gc.percent + '%' }" :class="gc.class"><Icon class="inline" :icon="gc.genre" /></div>
        </div>
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
          genre: genre.icon,
          percent: percent,
        };
      });
    },

    genreTable() {
      return this.genreCounts.reduce((table, gc) => {
        if (gc.count > 0) {
          const key = this.i18n(gc.genre);
          const value = `${gc.count}/${this.genreTotal} (${gc.percent.toFixed(0)}%)`;
          table[key] = value;
        }
        return table;
      }, {});
    },
  },

  methods: {
    clickDiscard() {
      this.emitter.emit("clickDiscard");
    },
  },
};
</script>
