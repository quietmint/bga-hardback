<template>
  <teleport :to="teleportTo">
    <div class="tailwind panelbg">
      <div class="panel-left text-center">
        <div v-if="player.order == 1" class="text-13 text-left pl-1 pt-1" v-text="i18n('first')"></div>

        <div class="panel-ink flex items-center text-20 font-bold text-noshadow">
          <div class="flex-1" :title="i18n('ink') + ': ' + player.ink">{{ player.ink }}</div>
          <div class="flex-1 text-black" :title="i18n('remover') + ': ' + player.remover">{{ player.remover }}</div>
        </div>

        <div v-if="player.id == game.player_id" class="panel-cards flex items-center text-24 font-bold text-noshadow">
          <div class="flex-1" :title="i18n('deck') + ': ' + player.deckCount">{{ player.deckCount }}</div>
          <div class="flex-1 cursor-pointer" @click="clickDiscard" :title="i18n('discardButton') + ': ' + player.discardCount">{{ player.discardCount }}</div>
        </div>
        <div v-if="player.id == game.player_id" class="flex items-center text-12">
          <div class="flex-1" v-text="i18n('deck')"></div>
          <div class="flex-1 cursor-pointer" @click="clickDiscard" v-text="i18n('discardButton')"></div>
        </div>
      </div>

      <div class="panel-right flex items-end justify-around text-noshadow">
        <HTooltip v-if="options.value.awards" :table="refs.value.awards" :header="i18n('award')">
          <div class="panel-opt" :title="i18n('award') + ': ' + player.award">{{ player.award || "" }}<Icon v-if="player.award" class="inline text-20" icon="star" /></div>
        </HTooltip>
        <HTooltip v-if="options.value.adverts" :table="refs.value.adverts" :header="i18n('adverts')" suffix="¢">
          <div class="panel-opt" :title="i18n('adverts') + ': ' + player.advert">{{ player.advert || "" }}<Icon v-if="player.advert" class="inline text-20" icon="star" /></div>
        </HTooltip>
      </div>

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

  methods: {
    clickDiscard() {
      this.emitter.emit("clickDiscard");
    },
  },
};
</script>
