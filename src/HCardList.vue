<template>
  <draggable class="flex flex-wrap justify-center" :class="heightClass" group="cards" :list="cards" item-key="id" :move="move" @change="change" :animation="500" tag="transition-group" :component-data="{ tag: 'div' }">
    <template #item="{ element }">
      <div class="m-1" :key="element.id">
        <HCard v-bind="element" @click="click(element)" />
        <HFooter :location="location" :card="element" />
      </div>
    </template>
  </draggable>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon, addIcon } from "@iconify/vue";
import draggable from "vuedraggable";
import HCard from "./HCard.vue";
import HFooter from "./HFooter.vue";

export default {
  name: "HCardList",
  emits: ["click", "drag"],
  components: { Icon, draggable, HCard, HFooter },
  props: {
    cards: {
      type: Array,
      required: true,
    },
    location: {
      type: String,
      required: true,
    },
    checkDrag: {
      type: Function,
      required: true,
    },
  },
  computed: {
    heightClass(): String {
      switch (this.location) {
        case "tableau":
          return "min-h-64";
        case "timeless":
          "min-w-60";
        default:
          return "min-h-60";
      }
    },
  },
  methods: {
    move(evt): Boolean {
      let card: any = evt.draggedContext.element;
      let fromLocation: String = this.location;
      let toLocation: String = evt.relatedContext.component.$parent.location;
      return this.checkDrag(card, fromLocation, toLocation);
    },
    change(evt): void {
      let location: String = this.location;
      let event = evt.moved ? "order" : evt.added ? "add" : "remove";
      let e = evt.moved || evt.added || evt.removed;
      let cardId = e.element.id;
      let order = e.newIndex != null ? e.newIndex : e.oldIndex;
      this.$emit("drag", { location, event, cardId, order });
    },
    click(card): void {
      let location: String = this.location;
      this.$emit("click", { location, card });
    },

    footerClass(card): String {
      if (card.ink) {
        return "bg-black text-white";
      } else if (this.location == "tableau") {
        return "bg-white";
      }
    },
    footerText(card): String {
      if (card.ink) {
        return "bg-black text-white";
      } else if (this.location == "tableau") {
        return "bg-white";
      }
    },
  },
};
</script>
