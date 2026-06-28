<template>
  <div class="hb-fixed hb-z-top hb-inset-0 hb-bg-white/75 dark:hb-bg-black/75"
       @click="hide()">
    <div class="hb-flex hb-items-start hb-justify-center hb-min-h-screen">
      <div class="hb-select-text hb-popup hb-bg-white hb-text-gray-800 dark:hb-bg-gray-900 dark:hb-text-gray-300 hb-rounded-lg hb-shadow hb-text-20 hb-p-6 hb-mt-18 hb-w-120"
           @click.stop>
        <div v-text="i18n(options.dictionary.dict) + ' (' + options.dictionary.lang + ')'"
             class="hb-text-center"></div>
        <form @submit.prevent="lookup">
          <input id="lookupInput"
                 v-model="input"
                 :placeholder="i18n('lookupPlaceholder')"
                 class="hb-w-full hb-text-24 hb-text-center hb-uppercase hb-bg-blue-100 hb-text-blue-600 dark:hb-bg-gray-800 dark:hb-text-gray-300 hb-rounded-lg hb-p-3 hb-my-4"
                 autocomplete="off"
                 autofocus />
        </form>
        <div v-for="hist in lookupHistory"
             :key="hist"
             class="hb-flex hb-items-start hb-mb-2">
          <Icon :icon="hist.icon"
                :class="{ 'hb-text-red-600': hist.icon == 'no', 'hb-text-green-600': hist.icon == 'yes', 'hb-animate-spin': hist.icon == 'loading' }"
                class="hb-inline hb-text-24 hb-mr-1" />
          <div class="hb-grow">
            {{ hist.word }} &mdash; {{ hist.word.length }}
            <div v-if="hist.links"
                 class="hb-define hb-text-15">
              <a v-for="link in hist.links"
                 :key="link.url"
                 :href="link.url"
                 target="hb-define"
                 class="hb-dictionarylink">{{ link.name }}
                <Icon icon="openInNew"
                      class="hb-inline" />
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
const letterPattern = /[^A-Z]/g;

import { nextTick } from "vue";
import { Icon } from "@iconify/vue";

export default {
  name: "HLookup",
  emits: ["clickLookup"],
  inject: ["i18n"],
  components: { Icon },

  props: {
    lookupHistory: {
      type: Array,
      required: true,
    },
    lookupPopup: {
      type: Object,
      required: true,
    },
    options: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      input: this.lookupPopup.word,
    };
  },

  mounted() {
    window.addEventListener("keydown", this.keydown, true);
    this.lookup();
  },

  beforeUnmount() {
    window.removeEventListener("keydown", this.keydown, true);
  },

  methods: {
    keydown(evt) {
      let letter = evt.key.toUpperCase();
      if (letter == "ESCAPE") {
        evt.stopPropagation();
        this.hide();
      }
    },

    hide() {
      this.emitter.emit("clickLookup");
    },

    async lookup() {
      const word = this.input.toUpperCase().replace(letterPattern, "");
      if (this.input != word) {
        this.input = word;
        await nextTick();
      }
      if (word) {
        this.emitter.emit("clickLookup", word);
        this.select();
      } else {
        // pressing Enter when the box is gone
        this.hide();
      }
    },

    select() {
      const inputEl = document.getElementById("lookupInput");
      inputEl.focus();
      inputEl.select();
    },
  },
};
</script>
