<template>
  <div :id="'cardmini_' + this.card.id"
       @click="clickCard"
       :class="[bgClass, { 'hb-cursor-pointer': this.clickAction }]"
       class="hb-rounded-lg hb-w-11 hb-p-1 hb-m-1 hb-bold hb-flex hb-items-center hb-justify-evenly hb-whitespace-nowrap">
    <Icon :icon="card.genreName"
          class="hb-icon hb-text-105" />{{ card.letter }}
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
        return 'hb-bg-black hb-text-white';
      } else if (this.card.remover) {
        return 'hb-bg-white hb-text-black';
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
