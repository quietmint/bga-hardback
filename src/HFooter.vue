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
  props: {
    location: {
      type: String,
      required: true,
    },
    card: {
      type: Object,
      required: true,
    },
  },
  computed: {
    action() {
      if (this.card.ink) {
        return "ink";
      } else if (this.card.origin == "timeless") {
        return "timeless";
      } else if ((this.location == "tableau" && this.game.isCurrentPlayerActive()) || this.location.startsWith("hand")) {
        return this.card.wild ? "reset" : "wild";
      }
    },
    footerClass() {
      switch (this.action) {
        case "ink":
          return "cursor-pointer bg-black text-white";
        case "timeless":
          return "bg-red-700";
        case "wild":
        case "reset":
          return "cursor-pointer bg-white text-black";
      }
    },
    icon() {
      switch (this.action) {
        case "ink":
          return "remover";
        case "timeless":
          return null;
        case "wild":
          return "wild";
        case "reset":
          return "reset";
      }
    },
    text() {
      switch (this.action) {
        case "ink":
          return "REMOVE INK";
        case "timeless":
          return this.card.origin;
        case "wild":
          return " WILD";
        case "reset":
          return "RESET";
      }
    },
  },
  methods: {
    click(evt): void {
      let action: String = this.action;
      let location: String = this.location;
      let card: any = this.card;
      this.emitter.emit("clickFooter", { action, location, card });
    },
  },
};
</script>