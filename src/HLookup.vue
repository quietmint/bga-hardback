<template>
  <div class="fixed z-top inset-0 bg-white/75 dark:bg-black/75"
       @click="hide()">
    <div class="flex items-start justify-center min-h-screen">
      <div class="select-text hpopup bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow text-20 p-6 mt-18 w-120"
           @click.stop>
        <div v-text="i18n(options.dictionary.dict) + ' (' + options.dictionary.lang + ')'"
             class="text-center"></div>
        <form @submit.prevent="lookup">
          <input id="lookupInput"
                 v-model="input"
                 :placeholder="i18n('lookupPlaceholder')"
                 class="w-full text-24 text-center uppercase bg-blue-100 text-blue-600 dark:bg-gray-800 dark:text-gray-300 rounded-lg p-3 my-4"
                 autocomplete="off"
                 autofocus />
        </form>
        <div v-for="hist in lookupHistory"
             :key="hist"
             class="flex items-start mb-2">
          <Icon :icon="hist.icon"
                :class="{ 'text-red-600': hist.icon == 'no', 'text-green-600': hist.icon == 'yes', 'animate-spin': hist.icon == 'loading' }"
                class="inline text-24 mr-1" />
          <div class="grow">
            {{ hist.word }} &mdash; {{ hist.word.length }}
            <div v-if="hist.links"
                 class="hdefine text-15">
              <a v-for="link in hist.links"
                 :key="link.url"
                 :href="link.url"
                 target="hdefine"
                 class="hdictionarylink">{{ link.name }}
                <Icon icon="openInNew"
                      class="inline" />
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
