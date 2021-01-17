<template>
  <div v-if="footer" class="flex justify-evenly text-center text-sm font-bold">
    <div :class="footer.class" class="px-3 pb-1 hover:pt-1 shadow rounded-b-lg transition-all z-0" @click="click">
      {{ footer.text }}
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon, addIcon } from "@iconify/vue";

export default {
  name: "HFooter",
  emits: ["clickFooter"],
  components: { Icon },
  inject: ["gamestate"],
  props: {
    card: {
      type: Object,
      required: true,
    },
  },
  computed: {
    footer() {
      const footerRef = {
        ink: {
          action: "useRemover",
          text: "REMOVE INK",
          class: "cursor-pointer bg-black text-white hover:bg-gray-100 hover:text-black",
        },
        wild: {
          action: "wild",
          text: "WILD",
          class: "cursor-pointer bg-blue-700 text-white hover:bg-blue-100 hover:text-blue-700",
        },
        reset: {
          action: "reset",
          text: "RESET",
          class: "cursor-pointer bg-blue-700 text-white hover:bg-blue-100 hover:text-blue-700",
        },
        uncover: {
          action: "uncover",
          text: "UNCOVER",
          class: "cursor-pointer bg-blue-700 text-white hover:bg-blue-100 hover:text-blue-700",
        },
        double: {
          action: "double",
          text: "DOUBLE",
          class: "cursor-pointer bg-blue-700 text-white hover:bg-blue-100 hover:text-blue-700",
        },
        trash: {
          action: "trash",
          text: "TRASH FOREVER",
          class: "cursor-pointer bg-red-700 text-white hover:bg-red-100 hover:text-red-700",
        },
        trash: {
          action: "trash",
          text: "TRASH FOREVER",
          class: "cursor-pointer bg-red-700 text-white hover:bg-red-100 hover:text-red-700",
        },
      };

      if (this.card.location == "tableau" && this.gamestate.active) {
        if (this.gamestate.name == "playerTurn") {
          return this.card.ink ? footerRef.ink : this.card.wild ? footerRef.reset : footerRef.wild;
        } else if (this.gamestate.name == "uncover" && this.gamestate.args.possible.hasOwnProperty(this.card.id)) {
          return footerRef.uncover;
        } else if (this.gamestate.name == "double" && this.gamestate.args.possible.hasOwnProperty(this.card.id)) {
          return footerRef.double;
        } else if (this.gamestate.name == "trash" && this.gamestate.args.possible.includes(this.card.id)) {
          return footerRef.trash;
        }
      }

      if (this.card.origin == "timeless") {
        return {
          text: this.card.origin,
          class: "bg-grey-600 text-white",
        };
      }

      if (this.card.location.startsWith("hand")) {
        return this.card.ink ? footerRef.ink : this.card.wild ? footerRef.reset : footerRef.wild;
      }
    },
  },
  methods: {
    click(evt): void {
      if (this.footer && this.footer.action) {
        let action: String = this.footer.action;
        let card: any = this.card;
        this.emitter.emit("clickFooter", { action, card });
      }
    },
  },
};
</script>