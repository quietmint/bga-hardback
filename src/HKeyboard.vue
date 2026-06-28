<template>
  <div class="hb-fixed hb-z-top hb-inset-0 hb-bg-white/75 dark:hb-bg-black/75"
       @click="click(null)">
    <div class="hb-flex hb-items-start hb-justify-center hb-min-h-screen">
      <div class="hb-popup hb-flex hb-flex-col hb-items-center hb-justify-center hb-bg-white hb-text-gray-800 dark:hb-bg-gray-900 dark:hb-text-gray-300 hb-rounded-lg hb-shadow hb-text-20 hb-p-6 hb-mt-18"
           @click.stop>
        <div class="hb-mb-4"
             v-html="i18n('keyboard', keyboardPopup)"></div>
        <div v-for="row in rows"
             :key="row"
             class="hb-flex hb-justify-center">
          <div :id="'tut_keyboard_' + letter"
               v-for="letter in row"
               :key="letter"
               @click.stop="click(letter)"
               class="hb-flex-none hb-cursor-pointer hb-text-center hb-m-1 hb-w-14 hb-h-14 hb-leading-14 hb-shadow hb-text-24 hb-font-bold hb-rounded-full hb-bg-gradient-to-b hb-from-gray-100 hb-via-gray-100 hb-to-gray-200 dark:hb-from-gray-700 dark:hb-via-gray-700 dark:hb-to-gray-800">{{ letter }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="js">
const escapes = ["ESCAPE", "BACKSPACE", "DELETE"];
const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
const rows = ["QWERTYUIOP".split(""), "ASDFGHJKL".split(""), "ZXCVBNM".split("")];

import { Icon } from "@iconify/vue";

export default {
  name: "HKeyboard",
  emits: ["clickKey"],
  inject: ["i18n"],
  components: { Icon },

  props: {
    keyboardPopup: {
      type: Object,
      required: true,
    },
  },

  mounted() {
    window.addEventListener("keydown", this.keydown, true);
  },

  beforeUnmount() {
    window.removeEventListener("keydown", this.keydown, true);
  },

  data() {
    return {
      rows: rows,
    };
  },

  methods: {
    keydown(evt) {
      let letter = evt.key.toUpperCase();
      if (escapes.includes(letter)) {
        evt.stopPropagation();
        this.click(null);
      } else if (letters.includes(letter)) {
        evt.stopPropagation();
        this.click(letter);
      }
    },

    click(letter) {
      this.emitter.emit("clickKey", letter);
    },
  },
};
</script>
