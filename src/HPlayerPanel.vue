<template>
  <div class="my-1 text-lg">
    <div class="inline-block whitespace-nowrap px-1 mx-1 rounded-md bg-gray-50 bg-opacity-50" title="Coins"><Icon class="inline text-2xl pr-1" icon="coin" />{{ coins }}</div>
    <div class="inline-block whitespace-nowrap px-1 mx-1 rounded-md bg-gray-50 bg-opacity-50" title="Ink"><Icon class="inline text-2xl pr-1" icon="ink" />{{ ink }}</div>
    <div class="inline-block whitespace-nowrap px-1 mx-1 rounded-md bg-gray-50 bg-opacity-50" title="Remover"><Icon class="inline text-2xl pr-1" icon="remover" />{{ remover }}</div>
    <div class="inline-block whitespace-nowrap px-1 mx-1 rounded-md bg-gray-50 bg-opacity-50" title="Total Cards"><Icon class="inline text-2xl pr-1" icon="cards" />{{ totalCards }}</div>
  </div>
</template>

<script>
import Constants from "./constants.js";
import { Icon, addIcon } from "@iconify/vue";
import mdiCards from "@iconify-icons/mdi/cards";
import mdiCoin from "@iconify-icons/mdi/alpha-c-circle-outline";
import mdiFlaskEmptyPlus from "@iconify-icons/mdi/flask-empty-plus";
import mdiFlaskEmptyRemoveOutline from "@iconify-icons/mdi/flask-empty-remove-outline";
import mdiStar from "@iconify-icons/mdi/star-outline";

addIcon("cards", mdiCards);
addIcon("coin", mdiCoin);
addIcon("ink", mdiFlaskEmptyPlus);
addIcon("remover", mdiFlaskEmptyRemoveOutline);
addIcon("star", mdiStar);

export default {
  name: "HPlayerPanel",
  components: {
    Icon,
  },
  data() {
    return {
      player: {},
    };
  },
  computed: {
    coins() {
      return this.player.coins || 0;
    },
    ink() {
      return this.player.ink || 0;
    },
    remover() {
      return this.player.remover || 0;
    },
    totalCards() {
      return this.player.totalCards || 0;
    },
  },
};
</script>
