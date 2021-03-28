<template>
  <div @mouseover="show" @mouseout="hover = false" @pointerdown="toggle" ref="holder">
    <table v-if="hover" class="table-fixed fixed z-top bg-white text-black ring-2 ring-black rounded-lg overflow-hidden text-16 text-center" :style="{ top: top, left: left }" ref="table">
      <tr>
        <th colspan="2" class="p-2 bg-gray-200 font-bold text-center">{{ header }}</th>
      </tr>
      <tr v-for="(points, key) in table" :key="key">
        <td class="px-2 py-1 border-t border-b border-gray-800">{{ key }}{{ suffix }}</td>
        <td class="px-2 py-1 border-t border-b border-gray-800">{{ points }}<Icon icon="star" class="inline text-18" /></td>
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
    table: {
      type: Object,
      required: true,
    },
    header: {
      type: String,
      required: true,
    },
    suffix: {
      type: String,
      required: false,
      default: "",
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
