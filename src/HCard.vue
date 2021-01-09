<template>
  <div :class="cardClass" class="relative inline-flex justify-center auto-rows-auto rounded-lg card-item select-none cursor-move overflow-hidden">
    <!-- ID -->
    <div class="absolute bottom-1 left-1 font-bold text-sm">{{ origin }} (#{{ id }})</div>

    <!-- Icon -->
    <div class="absolute top-1 left-1 leading-none">
      <Icon :icon="icon" :class="iconClass" class="text-3xl" />
    </div>

    <!-- Letter -->
    <div :class="letterClass" class="text-center text-8xl">
      {{ wild || letter }}
    </div>

    <div class="flex flex-col flex-grow" :class="effectClass">
      <!-- Basic Benefits -->
      <div class="bg-gray-100 bg-opacity-60 p-1 flex-shrink rounded-l-lg">
        <ul>
          <li v-for="benefit in basicBenefitsList" :key="benefit.id" class="hanging">{{ benefit.text }}<Icon v-if="benefit.icon" :icon="benefit.icon" class="inline" />{{ benefit.text2 }}<Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline" />{{ benefit.text3 }}<Icon v-if="benefit.icon3" :icon="benefit.icon3" class="inline" /></li>
        </ul>
      </div>

      <!-- Genre Benefits -->
      <div v-if="genreBenefitsList.length" class="bg-gray-100 bg-opacity-60 p-1 mt-1 flex-shrink rounded-l-lg">
        <div class="italic uppercase text-xs text-gray-700 border-b border-gray-700">With other <Icon :icon="icon" class="inline text-base" /></div>
        <ul>
          <li v-for="benefit in genreBenefitsList" :key="benefit.id" class="hanging">{{ benefit.text }}<Icon v-if="benefit.icon" :icon="benefit.icon" class="inline" />{{ benefit.text2 }}<Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline" />{{ benefit.text3 }}<Icon v-if="benefit.icon3" :icon="benefit.icon3" class="inline" /></li>
        </ul>
      </div>
    </div>

    <!-- Price Tag -->
    <div v-if="cost" class="pricetag absolute bottom-1 right-1 font-bold text-sm leading-tight shadow-md">
      {{ cost }}¢
      <div v-if="points" class="border-t border-gray-500">{{ points }}<Icon icon="star" class="inline" /></div>
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon, addIcon } from "@iconify/vue";

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
    basicBenefitsList: Array,
    genreBenefits: Object,
    genreBenefitsList: Array,
    order: Number,
    origin: String,
    timeless: Boolean,
    ink: Boolean,
    wild: String,
  },
  computed: {
    cardClass() {
      let c = "";
      if (this.timeless) {
        c = "flex-row	w-60 h-44 items-center ";
      } else {
        c = "flex-col w-44 h-60 ";
      }

      if (this.ink) {
        c += "ring-4 ring-gray-900 ring-inset ";
      }

      if (this.wild) {
        return c + "bg-gradient-to-b from-gray-100 via-purple-200 to-gray-100";
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
      if (this.wild) {
        return "hidden";
      } else if (this.timeless) {
        return "my-2";
      } else {
        return "ml-2 mb-2";
      }
    },

    letterClass() {
      let c = "px-4 py-2 ";
      if (this.wild) {
        return c;
      }
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
      if (this.wild) {
        return "text-gray-400";
      }
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
      if (this.wild) {
        return "wild";
      }
      switch (this.genre) {
        case Constants.ADVENTURE:
          return "adventure";
        case Constants.HORROR:
          return "horror";
        case Constants.ROMANCE:
          return "romance";
        case Constants.MYSTERY:
          return "mystery";
      }
    },
  },
};
</script>
