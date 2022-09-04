<template>
  <div class="fixed z-top inset-0 bg-white bg-opacity-75 dark:bg-black dark:bg-opacity-75"
       @click="click(null)">
    <div class="flex items-center justify-center min-h-screen">
      <div class="hpopup flex flex-col items-center justify-center bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow text-20 p-6"
           @click.stop>
        <div class="mb-4"
             v-html="i18n('keyboard', keyboardPopup)"></div>
        <div v-for="row in rows"
             :key="row"
             class="flex justify-center">
          <div :id="'tut_keyboard_' + letter"
               v-for="letter in row"
               :key="letter"
               @click.stop="click(letter)"
               class="flex-none cursor-pointer text-center m-2 w-14 h-14 leading-14 shadow text-24 font-bold rounded-full bg-gradient-to-b from-gray-100 via-gray-100 to-gray-200 dark:from-gray-700 dark:via-gray-700 dark:to-gray-800 hover:from-blue-600 hover:to-blue-700 hover:text-white">{{ letter }}</div>
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
