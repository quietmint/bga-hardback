<template>
  <div @mouseover="show" @mouseout="hover = false" @pointerdown="toggle" ref="holder">
    <table v-if="hover" class="table-fixed fixed z-top bg-white text-black ring-2 ring-black rounded-lg overflow-hidden text-16 text-center" :style="{ top: top, left: left }" ref="table">
      <tr>
        <th colspan="2" class="p-2 bg-gray-200 font-bold text-center" v-html="header"></th>
      </tr>
      <tr v-for="(value, key) in table" :key="key">
        <td class="px-2 py-1 border-t border-b border-gray-800">{{ key }}{{ keySuffix }}</td>
        <td class="px-2 py-1 border-t border-b border-gray-800">{{ value }}<Icon v-if="valueIcon" :icon="valueIcon" class="inline text-18" /></td>
      </tr>
    </table>
    <slot />
  </div>
</template>

<script lang="ts">
import { Icon } from "@iconify/vue";
import { nextTick } from "vue";

export default {
  name: "HTooltip",
  inject: ["i18n"],
  components: { Icon },

  props: {
    header: {
      type: String,
      required: true,
    },
    table: {
      type: Object,
      required: true,
    },
    keySuffix: {
      type: String,
      required: false,
      default: "",
    },
    valueIcon: {
      type: String,
      required: false,
      default: null,
    },
  },

  data() {
    return {
      hover: false,
      top: 0,
      left: -999,
    };
  },

  methods: {
    async show() {
      this.hover = true;
      this.top = 0;
      this.left = -999;
      await nextTick();
      const table: HTMLElement = this.$refs.table;
      const holder: HTMLElement = this.$refs.holder;
      const h = holder.getBoundingClientRect();
      this.top = Math.max(h.top - table.clientHeight / 2, 0) + "px";
      this.left = Math.max(h.left - table.clientWidth - 10, 0) + "px";
    },

    async toggle() {
      if (this.hover) {
        this.hover = false;
      } else {
        this.show();
      }
    },
  },
};
</script>
