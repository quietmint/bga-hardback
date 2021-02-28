<template>
  <div class="cardlist flex flex-wrap items-center justify-center" ref="cardlist">
    <transition-group :css="false">
      <HCard v-for="card in cards" :key="card.id" :card="card" />
    </transition-group>
  </div>
</template>

<script lang="ts">
import HCard from "./HCard.vue";


export default {
  name: "HCardList",
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
};
</script>
