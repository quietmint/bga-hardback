<template>
  <div class="border-dashed border-2 border-gray-600 min-h-64 flex flex-wrap justify-center">
    <HCard v-for="card in cards" :key="card.id" v-bind="card" />
  </div>
</template>

<script>
import draggable from 'vuedraggable';
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
  },
  methods: {
    sort() {
      console.log("HCardList sort", this.cards);
      this.cards.sort(function (a, b) {
        if (a.letter < b.letter) {
          return -1;
        } else if (a.letter > b.letter) {
          return 1;
        }
        return 0;
      });
    },

    shuffle() {
      console.log("HCardList shuffle", this.cards);
      for (let i = this.cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [this.cards[i], this.cards[j]] = [this.cards[j], this.cards[i]];
      }
    },
  },
};
</script>
