<template>
  <div :class="cardClass" class="card shadow relative rounded-lg select-none cursor-pointer">
    <div class="cardface front rounded-lg">
      <!-- Bookmark -->
      <div :class="bookmarkClass" class="bookmark absolute flex items-center text-center font-bold leading-none">
        <Icon v-if="card.genreName != 'starter'" :icon="card.genreName" class="icon" />
        <div v-if="card.cost" class="pt-1">{{ card.cost }}¢</div>
        <div v-if="card.points" class="pt-1">{{ card.points }}<Icon icon="star" class="inline star" /></div>
      </div>

      <!-- Letter -->
      <div :class="letterClass" class="absolute letter text-center leading-none" :title="'Letter: ' + card.letter + ' (' + card.genreName + ')'">
        {{ card.letter }}
      </div>

      <div :class="benefitClass" class="absolute">
        <!-- Basic Benefits -->
        <ul title="Basic benefits always activate">
          <li v-for="benefit in card.basicBenefitsList" :key="benefit.id" class="hanging">{{ benefit.text }}<Icon v-if="benefit.icon" :icon="benefit.icon" class="inline" />{{ benefit.text2 }}<Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline" />{{ benefit.text3 }}<Icon v-if="benefit.icon3" :icon="benefit.icon3" class="inline" /></li>
        </ul>

        <!-- Genre Benefits -->
        <div v-if="card.genreBenefitsList.length" :class="textClass" class="flex items-center border-t border-gray-900" :title="'Genre benefits activate if you play multiple ' + card.genreName + ' cards'">
          <Icon :icon="card.genreName" class="text-2xl flex-none" />
          <ul class="border-l border-gray-900 ml-1 py-1 pl-1">
            <li v-for="benefit in card.genreBenefitsList" :key="benefit.id" class="hanging">{{ benefit.text }}<Icon v-if="benefit.icon" :icon="benefit.icon" class="inline" />{{ benefit.text2 }}<Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline" />{{ benefit.text3 }}<Icon v-if="benefit.icon3" :icon="benefit.icon3" class="inline" /></li>
          </ul>
        </div>
      </div>

      <!-- ID -->
      <div class="absolute bottom-1 right-1 text-xs text-gray-400">{{ card.origin }} (#{{ card.id }}/{{ card.order }})</div>
    </div>

    <div class="cardface back rounded-lg">
      <!-- Wild Letter -->
      <div v-if="card.wild" class="absolute wildletter text-center leading-none top-16 w-full">
        {{ card.wild }}
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
    card: {
      type: Object,
      required: true,
    },
  },
  computed: {
    cardClass(): string {
      let c = "card-" + this.card.genreName + " ";
      c += this.card.timeless ? "timeless w-60 h-44 " : "w-44 h-60 ";
      if (this.card.ink) {
        c += "ring-8 ring-gray-900 ";
      } else if (this.card.wild) {
        c += "wild ";
      }
      return c;
    },

    bookmarkClass(): string {
      let c = this.card.timeless ? "flex-row bottom-2 left-0 w-20 h-7 " : "flex-col top-1 left-2 w-7 h-20 ";
      switch (this.card.genre) {
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

    letterClass(): string {
      return "letter-" + this.card.letter + (this.card.timeless ? " top-0 w-28" : " top-8 w-full");
    },

    benefitClass(): string {
      return this.card.timeless ? "top-8 bottom-0 right-1 w-28 pl-1" : "bottom-0 left-0 right-0 h-24 px-2 pb-1";
    },

    textClass(): string {
      switch (this.card.genre) {
        case Constants.ADVENTURE:
          return "text-yellow-900";
        case Constants.HORROR:
          return "text-green-700";
        case Constants.ROMANCE:
          return "text-red-700";
        case Constants.MYSTERY:
          return "text-blue-700";
      }
    },
  },
};
</script>
