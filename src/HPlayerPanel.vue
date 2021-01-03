<template>
  <teleport :to="teleportTo">
    <div class="tailwind">
      <div class="grid grid-cols-4 gap-2 mt-1 text-sm text-center" :class="colorClass">
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
          <Icon class="text-4xl mx-auto" icon="trash" />
          {{ discardCount }}
        </div>
      </div>
    </div>
  </teleport>
</template>

<script>
import Constants from "./constants.js";
import { Icon, addIcon } from "@iconify/vue";

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
  },
};
</script>
