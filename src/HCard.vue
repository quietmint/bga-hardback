<template>
  <div :class="cardClass" class="card shadow relative rounded-lg select-none cursor-pointer">
    <div class="cardface front rounded-lg">
      <!-- Icon -->
      <Icon v-if="icon" :icon="icon" :class="iconClass" class="absolute text-3xl" />

      <!-- Cost -->
      <div v-if="cost" :class="costClass" class="absolute w-6 text-center font-bold text-base leading-tight">
        {{ cost }}¢
        <div v-if="points">{{ points }}<Icon icon="star" class="inline" /></div>
      </div>

      <!-- Letter -->
      <div :class="letterClass" class="absolute letter text-center leading-none">
        {{ letter }}
      </div>

      <div :class="benefitClass" class="absolute">
        <!-- Basic Benefits -->
        <ul>
          <li v-for="benefit in basicBenefitsList" :key="benefit.id" class="hanging">{{ benefit.text }}<Icon v-if="benefit.icon" :icon="benefit.icon" class="inline" />{{ benefit.text2 }}<Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline" />{{ benefit.text3 }}<Icon v-if="benefit.icon3" :icon="benefit.icon3" class="inline" /></li>
        </ul>

        <!-- Genre Benefits -->
        <div v-if="genreBenefitsList.length" class="flex items-center border-t border-gray-900">
          <Icon :icon="icon" class="text-xl flex-none" />
          <ul class="border-l border-gray-900 ml-1 py-1 pl-1">
            <li v-for="benefit in genreBenefitsList" :key="benefit.id" class="hanging">{{ benefit.text }}<Icon v-if="benefit.icon" :icon="benefit.icon" class="inline" />{{ benefit.text2 }}<Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline" />{{ benefit.text3 }}<Icon v-if="benefit.icon3" :icon="benefit.icon3" class="inline" /></li>
          </ul>
        </div>
      </div>

      <!-- ID -->
      <div class="absolute bottom-1 right-1 text-xs text-red-700">{{ origin }} (#{{ id }}/{{ order }})</div>
    </div>
    <div class="cardface back rounded-lg">
      <!-- Wild Letter -->
      <div v-if="wild" class="absolute wildletter text-center leading-none top-16 w-full">
        {{ wild }}
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon } from "@iconify/vue";

export default {
  name: "HCard",
  components: {
    Icon,
  },
  props: {
    basicBenefits: Object,
    basicBenefitsList: Array,
    cost: Number,
    genre: Number,
    genreBenefits: Object,
    genreBenefitsList: Array,
    id: Number,
    ink: Boolean,
    invisible: Boolean,
    letter: String,
    location: String,
    order: Number,
    origin: String,
    points: Number,
    refId: Number,
    timeless: Boolean,
    wild: String,
  },
  computed: {
    cardClass() {
      let c = "";

      if (this.timeless) {
        c += "timeless w-60 h-44 ";
      } else {
        c += "w-44 h-60 ";
      }

      if (this.ink) {
        c += "ring-8 ring-gray-900 ";
      } else if (this.wild) {
        c += "wild ";
      }

      switch (this.genre) {
        case Constants.STARTER:
          return c + "card-starter";
        case Constants.ADVENTURE:
          return c + "card-adventure";
        case Constants.HORROR:
          return c + "card-horror";
        case Constants.ROMANCE:
          return c + "card-romance";
        case Constants.MYSTERY:
          return c + "card-mystery";
      }
    },

    icon() {
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

    iconClass() {
      let c = this.timeless ? "bottom-2 left-1 " : "top-1 left-2 ";
      switch (this.genre) {
        case Constants.ADVENTURE:
          return c + "text-yellow-900";
        case Constants.HORROR:
          return c + "text-green-100";
        case Constants.ROMANCE:
          return c + "text-red-100";
        case Constants.MYSTERY:
          return c + "text-blue-100";
        default:
          return c + "text-white";
      }
    },

    costClass() {
      return this.timeless ? "bottom-3 right-0" : "top-9 left-2";
    },

    letterClass() {
      if (this.timeless) {
        return "top-1 w-28";
      } else {
        return "top-8 w-full";
      }
    },

    benefitClass() {
      if (this.timeless) {
        return "top-7 right-1 w-28 h-24";
      } else {
        return "bottom-0 left-0 right-0 h-24 px-2 pb-1";
      }
    },
  },
};
</script>
