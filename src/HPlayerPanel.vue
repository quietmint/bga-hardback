<template>
  <teleport :to="teleportTo">
    <div class="tailwind panelbg">
      <div class="panel-ink flex items-center text-20 text-center font-bold">
        <div class="text-white flex-1" :title="i18n('ink') + ': ' + player.ink">{{ player.ink }}</div>
        <div class="text-black flex-1" :title="i18n('remover') + ': ' + player.remover">{{ player.remover }}</div>
      </div>

      <div class="panel-cards flex flex-col items-center justify-evenly ml-1 mt-1 mb-2 ring-2 ring-gray-600 shadow">
        <div class="p-1 text-white bg-black bg-opacity-25 rounded-lg leading-17">
          <span v-text="i18n('discardButton')"></span>: <span class="font-bold text-17">{{ player.discardCount }}</span
          ><br />
          <span v-text="i18n('deck')"></span>: <span class="font-bold text-17">{{ player.deckCount || 0 }}</span>
        </div>
      </div>

      <div class="panel-options flex justify-around">
        <HTooltip v-if="options.value.awards" :table="refs.value.awards" :header="i18n('award')">
          <div :class="'panel-award ' + (player.award ? 'length' + player.award.length : 'empty')" :title="i18n('award') + ': ' + (player.award ? player.award.points : 0)"></div>
        </HTooltip>
        <HTooltip v-if="options.value.adverts" :table="refs.value.adverts" :header="i18n('adverts')" suffix="¢">
          <div class="panel-dr" :title="i18n('adverts') + ': ' + player.advert">{{ player.advert ? player.advert : "" }}<Icon v-if="player.advert" class="inline" icon="star" /></div>
        </HTooltip>
      </div>

      <div v-if="player.order == 1" class="text-13 font-bold text-center mt-2" v-text="i18n('first')"></div>

      <HGenreCounts :counts="player.genreCounts" />
    </div>
  </teleport>
</template>

<script lang="ts">
import HGenreCounts from "./HGenreCounts.vue";
import HTooltip from "./HTooltip.vue";
import { Icon } from "@iconify/vue";

export default {
  name: "HPlayerPanel",
  inject: ["i18n", "options", "refs"],
  components: { Icon, HGenreCounts, HTooltip },

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
  },
};
</script>
