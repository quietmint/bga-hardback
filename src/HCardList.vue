<template>
  <div class="cardlist flex flex-wrap justify-center min-h-60" :class="dragInProgress ? 'dragInProgress' : ''">
    <transition-group :css="false">
      <HCard v-for="card in cards" :key="card.id" :card="card" :draggable="card.draggable" @dragstart="dragStart($event, card)" @dragend="dragEnd($event, card)" @dragenter="dragEnter($event, card)" @dragover="dragOver($event, card)" :class="dragInProgress && dragInProgress.id == card.id ? 'opacity-5' : ''" />
    </transition-group>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import HCard from "./HCard.vue";

export default {
  name: "HCardList",
  emits: ["drag"],
  components: { HCard },

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
      evt.dataTransfer.dropEffect = "move";
      evt.dataTransfer.effectAllowed = "move";
      this.dragInProgress = card;
      console.log(`dragStart ${this.dragInProgress.id} (${this.dragInProgress.letter})`);
    },

    dragEnd(evt: DragEvent, card: any): void {
      this.dragInProgress = null;
    },

    dragEnter(evt: DragEvent, dst: any): void {
      if (this.dragInProgress == null || this.dragInProgress.location != dst.location) {
        // Do nothing, show "no" cursor
        return;
      }
      evt.preventDefault();
      if (this.dragWait || this.dragInProgress.id == dst.id) {
        // Do nothing, show "move" cursor
        return;
      }
    },

    dragOver(evt: DragEvent, dst: any): void {
      function arraysEqual(a: object[], b: object[]): boolean {
        if (a === b) return true;
        if (a == null || b == null) return false;
        if (a.length !== b.length) return false;

        // If you don't care about the order of the elements inside
        // the array, you should sort both arrays here.
        // Please note that calling sort on an array will modify that array.
        // you might want to clone your array first.

        for (var i = 0; i < a.length; ++i) {
          if (a[i] !== b[i]) return false;
        }
        return true;
      }

      if (this.dragInProgress == null || this.dragInProgress.location != dst.location) {
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

      // Determine before/after
      //const width = (evt.target as HTMLElement).offsetWidth;
      //const before = true; //evt.offsetX / width <= 0.5;

      // Calculate the new order
      let newIndex = dstIndex + (before ? -1 : 0) + (dstIndex < myIndex ? 1 : 0);
      let newOrder = this.cards.filter((card) => card.id != this.dragInProgress.id).map((card) => card.id);
      newOrder.splice(newIndex, 0, this.dragInProgress.id);
      if (!arraysEqual(oldOrder, newOrder)) {
        console.log(`drag ${this.dragInProgress.letter} ${before ? "before" : "after"} ${dst.letter}\nold: ${oldOrder.join(" ")}\nnew: ${newOrder.join(" ")}`);

        // Apply it
        let location: String = this.location;
        this.emitter.emit("drag", { location, newOrder });
        this.dragWait = true;
        setTimeout(() => (this.dragWait = false), 300);
      }
    },
  },
};
</script>
