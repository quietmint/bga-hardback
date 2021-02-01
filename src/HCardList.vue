<template>
  <div class="cardlist relative flex flex-wrap justify-center" :class="dragInProgress ? 'dragInProgress' : ''" @dragover.stop="dragOverList($event)">
    <transition-group :css="false">
      <HCard v-for="card in cards" :key="card.id" :card="card" :draggable="card.draggable" @dragstart="dragStart($event, card)" @dragend="dragEnd($event, card)" @dragover.stop="dragOverCard($event, card)" :class="dragInProgress && dragInProgress.id == card.id ? 'opacity-5' : ''" />
    </transition-group>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import HCard from "./HCard.vue";
import { debounce } from "lodash-es";

function arraysEqual(a: any[], b: any[]): boolean {
  if (a === b) return true;
  if (a == null || b == null) return false;
  if (a.length !== b.length) return false;
  for (var i = 0; i < a.length; ++i) {
    if (a[i] !== b[i]) return false;
  }
  return true;
}

export default {
  name: "HCardList",
  emits: ["drag"],
  components: { HCard },

  created() {
    this.dragOverCard = debounce(this.dragOverCard, 200, { maxWait: 200 });
    this.dragOverList = debounce(this.dragOverList, 200, { maxWait: 200 });
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

  data() {
    return {
      dragInProgress: null,
      dragWait: false,
    };
  },

  methods: {
    dragStart(evt: DragEvent, card: any): void {
      this.dragInProgress = card;
      evt.dataTransfer.dropEffect = "move";
      evt.dataTransfer.effectAllowed = "move";
    },

    dragEnd(evt: DragEvent, card: any): void {
      this.dragInProgress = null;
    },

    // When dragging on top of another card
    dragOverCard(evt: DragEvent, dst: any): void {
      if (this.dragInProgress == null) {
        // Do nothing, browser shows "not allowed" cursor
        return;
      }
      evt.preventDefault();
      if (this.dragWait || this.dragInProgress.id == dst.id) {
        // Do nothing, browser shows "move" cursor
        return;
      }

      // Calculate direction
      const oldOrder = this.cards.map((card) => card.id);
      const myIndex = oldOrder.indexOf(this.dragInProgress.id);
      const dstIndex = oldOrder.indexOf(dst.id);
      const before = myIndex > dstIndex;

      // Calculate the new order
      let newIndex = dstIndex + (before ? -1 : 0) + (dstIndex < myIndex ? 1 : 0);
      let newOrder = this.cards.filter((card) => card.id != this.dragInProgress.id).map((card) => card.id);
      newOrder.splice(newIndex, 0, this.dragInProgress.id);

      this.dragOver(oldOrder, newOrder);
    },

    // When dragging to the either end of the list
    dragOverList(evt: DragEvent): void {
      if (this.dragInProgress == null) {
        // Do nothing, browser shows "not allowed" cursor
        return;
      }
      evt.preventDefault();
      if (this.dragWait) {
        // Do nothing, browser shows "move" cursor
        return;
      }

      const listEl: HTMLElement = evt.target as HTMLElement;
      const firstEl: HTMLElement = listEl.firstElementChild as HTMLElement;
      const lastEl: HTMLElement = listEl.lastElementChild as HTMLElement;
      let direction = null;
      if (evt.offsetX <= firstEl.offsetLeft && evt.offsetY >= firstEl.offsetTop && evt.offsetY <= firstEl.offsetTop + firstEl.offsetHeight) {
        direction = "before";
      } else if (evt.offsetX >= lastEl.offsetLeft && evt.offsetY >= lastEl.offsetTop && evt.offsetY <= lastEl.offsetTop + lastEl.offsetHeight) {
        direction = "after";
      }
      if (direction) {
        const oldOrder = this.cards.map((card) => card.id);
        const newIndex = direction == "before" ? 0 : oldOrder.length;
        let newOrder = this.cards.filter((card) => card.id != this.dragInProgress.id).map((card) => card.id);
        newOrder.splice(newIndex, 0, this.dragInProgress.id);
        this.dragOver(oldOrder, newOrder);
      }
    },

    dragOver(oldOrder: number[], newOrder: number[]) {
      if (!arraysEqual(oldOrder, newOrder)) {
        console.log(`Drag ${this.dragInProgress.letter} (${this.dragInProgress.id})\nold: ${oldOrder.join(" ")}\nnew: ${newOrder.join(" ")}`);

        // Apply it
        this.emitter.emit("drag", { location: this.location, cardIds: newOrder });
        this.dragWait = true;
        setTimeout(() => (this.dragWait = false), 300);
      }
    },
  },
};
</script>
