<template>
  <div @mouseover="mouseover" @mouseout="hover = false" ref="holder">
    <table v-if="hover" class="table-fixed fixed z-10 bg-white ring-2 ring-black rounded-lg overflow-hidden text-13 text-center" :style="{ top: top, left: left }" ref="table">
      <tr>
        <th colspan="2" class="p-2 bg-gray-200 font-bold text-center">{{ header }}</th>
      </tr>
      <tr v-for="(points, key) in table" :key="key">
        <td class="px-2 py-1 border-t border-b border-gray-800">{{ key }}{{ suffix }}</td>
        <td class="px-2 py-1 border-t border-b border-gray-800">{{ points }}<Icon icon="star" class="inline text-17" /></td>
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
      left: 0,
    };
  },

  methods: {
    async mouseover(evt: MouseEvent) {
      this.hover = true;
      await nextTick();
      const table: HTMLElement = this.$refs.table;
      const holder: HTMLElement = this.$refs.holder;
      const h = holder.getBoundingClientRect();
      this.top = h.top - table.clientHeight / 2 + "px";
      this.left = h.left - table.clientWidth - 10 + "px";
    },
  },
};
</script>
