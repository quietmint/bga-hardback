<template>
  <div :id="'cardmini_' + this.card.id"
       @click="clickCard"
       :class="[bgClass, { 'cursor-pointer': this.clickAction }]"
       class="rounded-lg w-11 p-1 m-1 bold flex items-center justify-evenly whitespace-nowrap">
    <Icon :icon="card.genreName"
          class="icon text-105" />{{ card.letter }}
  </div>
</template>

<script lang="js">
import HConstants from "./HConstants.js";
import { Icon } from "@iconify/vue";

export default {
  name: "HCardMini",
  emits: ["clickCard"],
  inject: ["gamestate", "myself"],
  components: { Icon },

  props: {
    card: {
      type: Object,
      required: true,
    },
  },

  computed: {
    bgClass() {
      if (this.card.ink) {
        return 'bg-black text-white';
      } else if (this.card.remover) {
        return 'bg-white text-black';
      } else {
        return `${HConstants.GENRES[this.card.genre].bg} ${HConstants.GENRES[this.card.genre].textLight}`;
      }
    },

    clickAction() {
      if (this.myself != null && this.gamestate.safeToMove && this.card.location == this.myself.handLocation) {
        return { action: "move", destination: this.myself.tableauLocation };
      }
    },
  },

  methods: {
    clickCard(ev) {
      let action = this.clickAction;
      if (action) {
        let card = this.card;
        this.emitter.emit("clickCard", { action, card });
      }
    },
  },
};
</script>
