<template>
  <div :id="'cardlist_' + location"
       class="cardlist flex flex-wrap items-center justify-center">
    <transition-group :css="false">
      <HCard v-for="card in cards"
             :key="card.id"
             :card="card" />
    </transition-group>
    <div v-if="cards.length == 0"
         class="py-4 text-center"
         v-text="emptyMessage"></div>
  </div>
</template>

<script lang="js">
import HCard from "./HCard.vue";

export default {
  name: "HCardList",
  inject: ["i18n"],
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

  computed: {
    emptyMessage() {
      if (this.location) {
        let prefix = this.location.split("_")[0];
        let location = this.i18n(prefix + "Location");
        return this.i18n("empty", { location });
      }
    }
  }
};
</script>
