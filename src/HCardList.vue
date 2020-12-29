<template>
  <div class="my-2 p-1 rounded-lg bg-gray-900 bg-opacity-20">
    <b>{{ locationName }}</b>
    <draggable class="min-h-64 flex flex-wrap justify-center" group="cards" :list="cards" item-key="id" :animation="500" :move="checkMove" @change="dragChange" tag="transition-group" :component-data="{ tag: 'div' }">
      <template #item="{ element }">
        <HCard v-bind="element" />
      </template>
    </draggable>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import Constants from "./constants.js";
import HCard from "./HCard.vue";

export default {
  name: "HCardList",
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
  },
  computed: {
    locationName() {
      switch (this.location) {
        case "hand":
          return "My Hand";
        case "tableau":
          return "Tableau";
        case "timeless":
          return "Timeless Classics";
      }
    },
    checkMove() {
      let loc = this.location;
      switch (this.location) {
        case "hand":
          return function (evt) {
            let cardLocation = evt.draggedContext.element.location;
            let toLocation = evt.relatedContext.component.$parent.location;
            return toLocation == loc || toLocation == "tableau";
          };
        case "tableau":
          return function (evt) {
            let cardLocation = evt.draggedContext.element.location;
            let toLocation = evt.relatedContext.component.$parent.location;
            return toLocation == loc || toLocation == cardLocation;
          };
        case "timeless":
          return function (evt) {
            let cardLocation = evt.draggedContext.element.location;
            let toLocation = evt.relatedContext.component.$parent.location;
            return toLocation == loc || toLocation == "tableau";
          };
      }
    },
  },
  methods: {
    dragChange(evt) {
      console.log("change " + this.location, evt);
    },
  },
};
</script>
