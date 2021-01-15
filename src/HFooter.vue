<template>
  <div v-if="action" :class="footerClass" class="mx-auto w-32 pt-1 h-5 shadow rounded-b-lg text-center text-xs" @click="click">
    {{ text }}
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
    action() {
      console.log("compute action", this.card.id, this.gamestate.name, this.gamestate.active);
      if (this.card.location == "tableau") {
        if (this.gamestate.active && this.gamestate.name == "playerTurn") {
          return this.card.ink ? "ink" : this.card.wild ? "reset" : "wild";
        }
        if (this.gamestate.active && this.gamestate.name == "uncover" && this.gamestate.args.possible.hasOwnProperty(this.card.id)) {
          return "uncover";
        }
      }

      if (this.card.origin == "timeless") {
        return "timeless";
      }

      if (this.card.location.startsWith("hand")) {
        return this.card.ink ? "ink" : this.card.wild ? "reset" : "wild";
      }
    },
    footerClass() {
      switch (this.action) {
        case "uncover":
          return "cursor-pointer bg-blue-500 text-white";
        case "ink":
          return "cursor-pointer bg-black text-white";
        case "wild":
        case "reset":
          return "cursor-pointer bg-white text-black";
        case "timeless":
          return "bg-red-700";
      }
    },
    text() {
      switch (this.action) {
        case "uncover":
          return "UNCOVER";
        case "ink":
          return "REMOVE INK";
        case "wild":
          return " WILD";
        case "reset":
          return "RESET";
        case "timeless":
          return this.card.origin;
      }
    },
  },
  methods: {
    click(evt): void {
      let action: String = this.action;
      let card: any = this.card;
      this.emitter.emit("clickFooter", { action, card });
    },
  },
};
</script>