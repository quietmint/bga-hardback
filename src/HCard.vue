<template>
  <div class="p-1">
    <div
      v-bind:class="bgColorClass"
      class="relative inline-grid grid-cols-1 grid-rows-2 rounded-lg w-48 h-64"
    >
      <!-- Letter -->
      <div v-bind:class="letterClass" class="self-center text-center text-8xl">
        {{ letter }}
      </div>

      <!-- Effect -->
      <div
        class="bg-gray-100 bg-opacity-80 rounded-tl-lg rounded-br-lg p-2 ml-10"
      >
        This is card ID # {{ id }}<br />
        Lorem ipsum do the thing
      </div>

      <!-- Icon -->
      <div class="absolute top-2 left-2 leading-none">
        <Icon v-bind:icon="icon" v-bind:class="iconClass" class="text-3xl" />
      </div>

      <!-- Cost -->
      <div
        v-if="cost"
        class="absolute bottom-2 left-0 w-8 bg-gray-100 bg-opacity-80 text-center font-bold rounded-tr-lg rounded-br-lg"
      >
        {{ cost }}¢
      </div>
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
    genre: Number,
    letter: String,
    id: Number,
    benefits: Object,
    genreBenefits: Object,
    order: Number,
  },
  computed: {
    bgColorClass() {
      switch (this.genre) {
        case Constants.ADVENTURE:
          return "bg-gradient-to-b from-yellow-800 to-yellow-500";
        case Constants.HORROR:
          return "bg-gradient-to-b from-green-900 to-green-600";
        case Constants.ROMANCE:
          return "bg-gradient-to-b from-red-800 to-red-600";
        case Constants.MYSTERY:
          return "bg-gradient-to-b from-blue-500 to-blue-800";
        default:
          return "bg-gradient-to-b from-gray-500 to-gray-700";
      }
    },
    letterClass() {
      switch (this.genre) {
        case Constants.ADVENTURE:
          return "font-adventure text-yellow-300";
        case Constants.HORROR:
          return "font-horror text-green-400";
        case Constants.ROMANCE:
          return "font-romance text-gray-50";
        case Constants.MYSTERY:
          return "font-mystery text-blue-800";
        default:
          return "";
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
