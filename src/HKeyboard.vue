<template>
  <div class="fixed z-top inset-0 bg-white bg-opacity-75 dark:bg-black dark:bg-opacity-75" @click="click(null)">
    <div class="flex items-center justify-center min-h-screen">
      <div class="flex flex-col items-center justify-center bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow p-4" @click.stop>
        <div class="text-20 mb-4" v-text="i18n('keyboard')"></div>
        <div v-for="row in rows" :key="row" class="flex justify-center">
          <div :id="'tut_keyboard_' + letter" v-for="letter in row" :key="letter" @click.stop="click(letter)" class="flex-none cursor-pointer text-center m-2 w-14 h-14 leading-14 shadow text-24 font-bold rounded-full bg-gradient-to-b from-gray-100 via-gray-100 to-gray-200 dark:from-gray-700 dark:via-gray-700 dark:to-gray-800 hover:from-blue-600 hover:to-blue-700 hover:text-white">{{ letter }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
const escapes = ["ESCAPE", "BACKSPACE", "DELETE"];
const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split("");
const rows = ["QWERTYUIOP".split(""), "ASDFGHJKL".split(""), "ZXCVBNM".split("")];

export default {
  name: "HKeyboard",
  emits: ["clickKey"],
  inject: ["i18n"],

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
    keydown(evt: KeyboardEvent) {
      evt.stopPropagation();
      let letter = evt.key.toUpperCase();
      if (escapes.includes(letter)) {
        this.click(null);
      } else if (letters.includes(letter)) {
        this.click(letter);
      }
    },

    click(letter: string) {
      this.emitter.emit("clickKey", letter);
    },
  },
};
</script>
