<template>
  <div class="fixed z-top inset-0 bg-white bg-opacity-75 dark:bg-black dark:bg-opacity-75" @click="hide()">
    <div class="flex items-center justify-center min-h-screen">
      <div class="bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow text-20 p-6 w-96" @click.stop>
        <div v-text="i18n(options.dictionary.dict) + ' (' + options.dictionary.lang + ')'" class="text-center"></div>
        <div class="mb-4 text-16 text-center">{{ attemptText }}</div>
        <input v-if="!disabled" id="lookupInput" @input="input" :placeholder="i18n('lookupPlaceholder')" class="w-full text-24 text-center bg-blue-100 text-blue-600 dark:bg-blue-900 dark:bg-opacity-75 dark:text-blue-300 rounded-lg p-3 mb-4" autocomplete="off" autofocus />
        <div v-for="hist in history" :key="hist" class="mb-2"><Icon :icon="hist.icon" :class="{ 'text-red-600': hist.icon == 'no', 'text-green-600': hist.icon == 'yes', 'animate-spin': hist.icon == 'loading' }" class="inline" /> {{ hist.word }}</div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
const submits = ["ENTER"];
const escapes = ["ESCAPE"];
const pattern = /[^A-Z]/g;

import { Icon } from "@iconify/vue";

export default {
  name: "HLookup",
  emits: ["clickLookup"],
  inject: ["i18n"],
  components: { Icon },

  props: {
    options: {
      type: Object,
      required: true,
    },
    history: {
      type: Array,
      required: true,
    },
    myself: {
      type: Object,
      required: true,
    },
  },

  mounted() {
    window.addEventListener("keydown", this.keydown, true);
    let inputEl = document.getElementById("lookupInput") as HTMLInputElement;
    inputEl.value = "";
    inputEl.focus();
  },

  computed: {
    attemptText() {
      if (this.options.attempts > 0) {
        return this.i18n("lookupAttempts", { remaining: this.options.attempts - this.myself.attempts });
      }
    },

    disabled() {
      return this.options.attempts > 0 && this.myself.attempts >= this.options.attempts;
    },
  },

  beforeUnmount() {
    window.removeEventListener("keydown", this.keydown, true);
  },

  methods: {
    keydown(evt: KeyboardEvent) {
      let letter = evt.key.toUpperCase();
      if (escapes.includes(letter)) {
        evt.stopPropagation();
        this.hide();
      } else if (submits.includes(letter)) {
        evt.stopPropagation();
        this.lookup();
      }
    },

    input(evt: Event) {
      evt.target.value = evt.target.value.toUpperCase().replace(pattern, "");
    },

    hide() {
      this.emitter.emit("clickLookup");
    },

    lookup() {
      let inputEl = document.getElementById("lookupInput") as HTMLInputElement;
      if (inputEl != null) {
        let word = inputEl.value.toUpperCase().replace(pattern, "");
        inputEl.value = "";
        inputEl.focus();
        this.emitter.emit("clickLookup", word);
      } else {
        // pressing Enter when the box is gone
        this.hide();
      }
    },
  },
};
</script>
