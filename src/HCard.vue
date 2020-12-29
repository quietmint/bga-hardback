<template>
  <div class="card-item p-1 select-none cursor-move">
    <div :class="cardClass" class="relative inline-flex auto-rows-auto rounded-lg">
      <!-- Letter -->
      <div :class="letterClass" class="self-center text-center text-8xl">
        {{ letter }}
      </div>

      <!-- Effect -->
      <div :class="effectClass" class="bg-gray-100 bg-opacity-80 rounded-tl-lg rounded-br-lg p-2 flex-grow">This is card ID # {{ id }}</div>

      <!-- Icon -->
      <div class="absolute top-2 left-2 leading-none">
        <Icon :icon="icon" :class="iconClass" class="text-3xl" />
      </div>

      <!-- Instant Points -->
      <div v-if="points" class="absolute bottom-7 left-0 w-8 bg-gray-100 bg-opacity-80 text-center font-bold rounded-tr-lg rounded-br-lg">{{ points }}<Icon icon="star" :inline="true" class="inline" /></div>

      <!-- Cost -->
      <div v-if="cost" class="absolute bottom-2 left-0 w-8 bg-gray-100 bg-opacity-80 text-center font-bold rounded-tr-lg rounded-br-lg">{{ cost }}¢</div>
    </div>
  </div>
</template>

<script>
import Constants from "./constants.js";
import { Icon, addIcon } from "@iconify/vue";
import mdiCompass from "@iconify-icons/mdi/compass";
import mdiHeart from "@iconify-icons/mdi/heart";
import mdiMagnify from "@iconify-icons/mdi/magnify";
import mdiSkull from "@iconify-icons/mdi/skull";

export default {
  name: "HCard",
  components: {
    Icon,
  },
  props: {
    cost: Number,
    points: Number,
    genre: Number,
    letter: String,
    id: Number,
    benefits: Object,
    genreBenefits: Object,
    order: Number,
    location: String,
    timeless: Boolean,
  },
  computed: {
    cardClass() {
      let c = "";
      if (this.timeless) {
        c = "flex-row	w-64 h-48 ";
      } else {
        c = "flex-col w-48 h-64 ";
      }
      switch (this.genre) {
        case Constants.ADVENTURE:
          return c + "bg-gradient-to-b from-yellow-800 to-yellow-500";
        case Constants.HORROR:
          return c + "bg-gradient-to-b from-green-900 to-green-600";
        case Constants.ROMANCE:
          return c + "bg-gradient-to-b from-red-800 to-red-600";
        case Constants.MYSTERY:
          return c + "bg-gradient-to-b from-blue-500 to-blue-800";
        default:
          return c + "bg-gradient-to-b from-gray-500 to-gray-700";
      }
    },

    effectClass() {
      if (this.timeless) {
        return "ml-0 rounded-lg";
      } else {
        return "ml-10";
      }
    },

    letterClass() {
      let c = "px-6 py-4 ";
      switch (this.genre) {
        case Constants.ADVENTURE:
          return c + "font-adventure text-yellow-300";
        case Constants.HORROR:
          return c + "font-horror text-green-400";
        case Constants.ROMANCE:
          return c + "font-romance text-gray-50";
        case Constants.MYSTERY:
          return c + "font-mystery text-blue-800";
        default:
          return c;
      }
    },

    iconClass() {
      switch (this.genre) {
        case Constants.ADVENTURE:
          return "text-yellow-300";
        case Constants.HORROR:
          return "text-green-400";
        case Constants.ROMANCE:
          return "text-red-400";
        case Constants.MYSTERY:
          return "text-blue-800";
      }
    },

    icon() {
      switch (this.genre) {
        case Constants.ADVENTURE:
          return mdiCompass;
        case Constants.HORROR:
          return mdiSkull;
        case Constants.ROMANCE:
          return mdiHeart;
        case Constants.MYSTERY:
          return mdiMagnify;
      }
    },
  },
};
</script>
