<template>
  <!-- Literary Award -->
  <div v-if="options.awards"
       :id="'tut_award_p' + id"
       @click="click('award')"
       :class="visible == 'award' ? 'rounded-t-lg bg-black/70' : 'rounded-lg bg-white/70 text-black'"
       class="flex mt-1 mx-1 p-1 text-15 text-center text-noshadow font-bold cursor-pointer">
    <div class="flex-1">{{ i18n('award') }}:</div>
    <div v-if="award"
         class="flex-1">
      {{ award || 0 }}-NO-BREAK-
      <Icon icon="star"
            class="inline text-16" /> ({{ i18n('awardTable', { length: awardLength }) }})
    </div>
    <div v-if="!award"
         class="flex-1">--</div>
  </div>
  <div v-if="visible == 'award'"
       class="mx-1 rounded-b-lg overflow-hidden text-14 text-center text-noshadow ">
    <div v-for="(points, length) in refs.awards"
         :key="letters"
         :class="{ 'font-bold': award == points }"
         class="flex h-6 leading-6 bg-white/70 text-black">
      <div class="flex-1">
        {{ points }}-NO-BREAK-
        <Icon icon="star"
              class="inline text-16" />
      </div>
      <div class="flex-1">{{ i18n('awardTable', { length }) }}</div>
    </div>
  </div>

  <!-- Adverts -->
  <div v-if="options.adverts"
       :id="'tut_doctor_p' + id"
       @click="click('advert')"
       :class="visible == 'advert' ? 'rounded-t-lg bg-black/70' : 'rounded-lg bg-white/70 text-black'"
       class="flex mt-1 mx-1 p-1 text-15 text-center text-noshadow font-bold cursor-pointer">
    <div class="flex-1">{{ i18n('advert') }}:</div>
    <div v-if="advert"
         class="flex-1">{{ advert }}-NO-BREAK-
      <Icon icon="star"
            class="inline text-16" /> ({{ advertCoins }}¢)
    </div>
    <div v-if="!advert"
         class="flex-1">--</div>
  </div>
  <div v-if="visible == 'advert'"
       class="mx-1 rounded-b-lg overflow-hidden text-14 text-center text-noshadow">
    <div v-for="(points, coins) in refs.adverts"
         :key="key"
         :class="{ 'font-bold': advert == points }"
         class="flex h-6 leading-6 bg-white/70 text-black">
      <div class="flex-1">
        {{ points }}-NO-BREAK-
        <Icon icon="star"
              class="inline text-16" />
      </div>
      <div class="flex-1">{{ coins }}¢</div>
    </div>
  </div>
</template>

<script lang="js">
import { nextTick } from "vue";
import { Icon } from "@iconify/vue";
import { findKey } from "lodash-es";

const repaint = () => new Promise((resolve) => requestAnimationFrame(resolve));

export default {
  name: "HAwardAdvert",
  inject: ["i18n", "options", "refs"],
  components: { Icon },

  props: {
    id: {
      type: Number,
      required: true,
    },
    award: {
      type: Number,
      required: true,
    },
    advert: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      visible: null,
    };
  },

  computed: {
    awardLength() {
      if (this.award) {
        return findKey(this.refs.awards, (v) => v == this.award);
      }
    },
    advertCoins() {
      if (this.advert) {
        return findKey(this.refs.adverts, (v) => v == this.advert);
      }
    },
  },

  methods: {
    async click(section) {
      this.visible = this.visible == section ? null : section;
      await repaint();
      await nextTick();
      this.game.adaptPlayersPanels();
    }
  }
};
</script>
