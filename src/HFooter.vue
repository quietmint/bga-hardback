<template>
  <div v-if="action" :class="footerClass" class="mx-auto w-32 py-1 rounded-b-lg text-center text-xs" @click="click">
    <Icon v-if="icon" :icon="icon" class="inline text-md" />
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
      } else if (this.location == "tableau") {
        return "flip";
      }
    },
    footerClass() {
      switch (this.action) {
        case "ink":
          return "cursor-pointer bg-black text-white";
        case "timeless":
          return "bg-red-700";
        case "flip":
          return "cursor-pointer bg-white text-black";
      }
    },
    icon() {
      switch (this.action) {
        case "ink":
          return "remover";
        case "timeless":
          return null;
        case "flip":
          return "flip";
      }
    },
    text() {
      switch (this.action) {
        case "ink":
          return "REMOVE INK";
        case "timeless":
          return this.card.origin;
        case "flip":
          return "FLIP";
      }
    },
  },
  methods: {
    click(evt): void {
      let action: String = this.action;
      let location: String = this.location;
      let card: any = this.card;
      console.log("emit event clickFooter");
      this.emitter.emit("clickFooter", { action, location, card });
    },
  },
};
</script>