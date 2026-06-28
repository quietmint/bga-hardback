<template>
  <!-- Literary Award -->
  <div v-if="options.awards"
       :id="'tut_award_p' + id"
       @click="click('award')"
       :class="visible == 'award' ? 'hb-rounded-t-lg hb-bg-black/70' : 'hb-rounded-lg hb-bg-white/70 hb-text-black'"
       class="hb-flex hb-mt-1 hb-mx-1 hb-p-1 hb-text-15 hb-font-bold hb-cursor-pointer">
    <div class="hb-w-1/3">{{ i18n('award') }}:</div>
    <div v-if="award"
         class="hb-w-2/3">
      {{ award || 0 }}-NO-BREAK-
      <Icon icon="star"
            class="hb-inline hb-text-16" /> ({{ i18n('awardTable', { length: awardLength }) }})
    </div>
    <div v-if="!award"
         class="hb-w-2/3">--</div>
  </div>
  <div v-if="visible == 'award'"
       class="hb-mx-1 hb-rounded-b-lg hb-overflow-hidden hb-text-15">
    <div v-for="(points, length) in refs.awards"
         :key="letters"
         :class="{ 'hb-font-bold': award == points }"
         class="hb-pl-1/3 hb-h-6 hb-leading-6 hb-bg-white/70 hb-text-black">
      {{ points }}-NO-BREAK-
      <Icon icon="star"
            class="hb-inline hb-text-16" /> ({{ i18n('awardTable', { length }) }})
    </div>
  </div>

  <!-- Adverts -->
  <div v-if="options.adverts"
       :id="'tut_doctor_p' + id"
       @click="click('advert')"
       :class="visible == 'advert' ? 'hb-rounded-t-lg hb-bg-black/70' : 'hb-rounded-lg hb-bg-white/70 hb-text-black'"
       class="hb-flex hb-mt-1 hb-mx-1 hb-p-1 hb-text-15 hb-font-bold hb-cursor-pointer">
    <div class="hb-w-1/3">{{ i18n('advert') }}:</div>
    <div v-if="advert"
         class="hb-w-2/3">{{ advert }}-NO-BREAK-
      <Icon icon="star"
            class="hb-inline hb-text-16" /> ({{ advertCoins }}¢)
    </div>
    <div v-if="!advert"
         class="hb-w-2/3">--</div>
  </div>
  <div v-if="visible == 'advert'"
       class="hb-mx-1 hb-rounded-b-lg hb-overflow-hidden hb-text-15">
    <div v-for="(points, coins) in refs.adverts"
         :key="key"
         :class="{ 'hb-font-bold': advert == points }"
         class="hb-pl-1/3 hb-h-6 hb-leading-6 hb-bg-white/70 hb-text-black">
      {{ points }}-NO-BREAK-
      <Icon icon="star"
            class="hb-inline hb-text-16" /> ({{ coins }}¢)
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
