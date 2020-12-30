<template>
  <div class="card-item p-1 select-none cursor-move">
    <div :class="cardClass" class="relative inline-flex auto-rows-auto rounded-lg">
      <!-- Letter -->
      <div :class="letterClass" class="self-center text-center text-8xl">
        {{ letter }}
      </div>

      <!-- Effect -->
      <div :class="effectClass" class="bg-gray-100 bg-opacity-60 p-2 flex-grow rounded-l-lg">
        <p>Origin: {{ origin }}</p>

        <ul>
          <li v-for="benefit in benefitsList" :key="benefit.id" v-html="benefit.text"></li>
        </ul>
        <hr>
        <ul>
          <li v-for="benefit in genreBenefitsList" :key="benefit.id" v-html="benefit.text"></li>
        </ul>
      </div>

      <!-- Icon -->
      <div class="absolute top-1 left-1 leading-none">
        <Icon :icon="icon" :class="iconClass" class="text-3xl" />
      </div>

      <!-- Instant Points -->
      <div v-if="points" class="absolute bottom-0 right-10 bg-gray-50 border border-r-2 border-b-0 border-gray-900 p-1 font-bold rounded-t-md">{{ points }}<Icon icon="star" class="inline" /></div>

      <!-- Cost -->
      <div v-if="cost" class="absolute bottom-0 right-2 bg-gray-50 border border-r-2 border-b-0 border-gray-900 p-1 font-bold rounded-t-md">{{ cost }}¢</div>

      <!-- ID -->
      <div class="absolute top-1 right-2 font-bold text-sm">{{ id }}</div>
    </div>
  </div>
</template>

<script lang="ts">
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
    benefitsList: Array,
    genreBenefits: Object,
    genreBenefitsList: Array,
    order: Number,
    origin: String,
    timeless: Boolean,
  },
  computed: {
    cardClass() {
      let c = "";
      if (this.timeless) {
        c = "flex-row	w-60 h-44 ";
      } else {
        c = "flex-col w-44 h-60 ";
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
        return "my-2";
      } else {
        return "ml-2 mb-2";
      }
    },

    letterClass() {
      let c = "px-4 py-2 ";
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
