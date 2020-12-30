<template>
  <div class="my-2 p-1 rounded-lg bg-gray-900 bg-opacity-20">
    <b>{{ locationName }}</b>
    <draggable class="min-h-64 flex flex-wrap justify-center" group="cards" :list="cards" item-key="id" :animation="500" :move="move" @change="change" tag="transition-group" :component-data="{ tag: 'div' }">
      <template #item="{ element }">
        <HCard v-bind="element" @click="clickCard" />
      </template>
    </draggable>
  </div>
</template>

<script lang="ts">
import draggable from "vuedraggable";
import Constants from "./constants.js";
import HCard from "./HCard.vue";

export default {
  name: "HCardList",
  emits: ["drag"],
  components: {
    draggable,
    HCard,
  },
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
    clickCard: {
      type: Function,
      required: false,
    },
  },
  computed: {
    locationName(): String {
      switch (this.location) {
        case "tableau":
          return "Tableau";
        case "timeless":
          return "Timeless Classics";
        case "offer":
          return "Offer Row";
        default:
          return "My Hand";
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
    change(evt) {
      let location: String = this.location;
      let event = evt.moved ? "order" : evt.added ? "add" : "remove";
      let e = evt.moved || evt.added || evt.removed;
      let cardId = e.element.id;
      let order = e.newIndex != null ? e.newIndex : e.oldIndex;
      // let cardIds: Array<Number> = this.cards.map((card) => card.id);
      this.$emit("drag", { location, event, cardId, order });
    },
  },
};
</script>
