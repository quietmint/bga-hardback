<template>
  <!--<draggable class="flex flex-wrap justify-center" :class="heightClass" group="cards" :list="cards" item-key="id" :move="move" @change="change" :animation="500" :delay="100" tag="transition-group" :component-data="{ tag: 'div' }">
    <template #item="{ element }">
      <div class="m-1 relative" :key="element.id" :id="location + '_card' + element.id" :class="element.invisible ? 'invisible' : ''">
        <HCard v-bind="element" @click="click(element)" />
        <HFooter :location="location" :card="element" />
      </div>
    </template>
  </draggable>-->

  <div class="cardlist flex flex-wrap justify-center" :class="heightClass">
    <transition-group>
      <div class="cardholder m-1 relative" v-for="card in cards" :key="card.id" :id="location + '_card' + card.id" :class="card.invisible ? 'invisible' : ''">
        <HCard v-bind="card" @click="click(card)" />
        <HFooter :card="card" />
      </div>
    </transition-group>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon } from "@iconify/vue";
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
      if (this.location == "timeless") {
        return "min-w-60";
      } else if (this.location == "offer") {
        return "min-h-60";
      }
      return "min-h-66";
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
      let card = e.element;
      let cardId = e.element.id;
      let order = e.newIndex != null ? e.newIndex : e.oldIndex;
      this.$emit("drag", { card, location, event, cardId, order });
    },
    click(card): void {
      this.$emit("click", { card });
    },
  },
};
</script>
