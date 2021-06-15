<template>
  <div @pointerenter="enter" @pointerleave="hide" ref="holder">
    <div v-if="visible" class="absolute z-top" :style="{ marginTop: top, marginLeft: left }" ref="tip">
      <slot name="tip" />
    </div>
    <slot />
  </div>
</template>

<script lang="ts">
import { nextTick } from "vue";
import HConstants from "./HConstants";

export default {
  name: "HTooltip",
  inject: ["getRect", "i18n", "prefs"],

  props: {
    position: {
      type: String,
      required: false,
    },
  },

  data() {
    return {
      top: "0px",
      left: "-9999px",
      timeout: null,
      visible: false,
    };
  },

  mounted() {
    const holder: HTMLElement = this.$refs.holder;
    if (!window.PointerEvent) {
      // Old Safari?
      holder.addEventListener("mouseenter", this.enter, false);
      holder.addEventListener("mouseleave", this.hide, false);
    }
  },

  beforeUnmount() {
    const holder: HTMLElement = this.$refs.holder;
    if (!window.PointerEvent) {
      // Old Safari?
      holder.removeEventListener("mouseenter", this.enter, false);
      holder.removeEventListener("mouseleave", this.hide, false);
    }
  },

  methods: {
    enter(ev: MouseEvent) {
      if (this.prefs.value[HConstants.PREF_TOOLTIPS] == HConstants.TOOLTIPS_ENABLED) {
        this.timeout = setTimeout(() => this.show(), HConstants.TOOLTIP_TIMEOUT);
      }
    },

    hide(ev: MouseEvent) {
      clearTimeout(this.timeout);
      this.timeout = null;
      this.visible = false;
    },

    async show() {
      this.visible = true;
      this.top = "0px";
      this.left = "-9999px";
      await nextTick();
      const tip: HTMLElement = this.$refs.tip;
      const holder: HTMLElement = this.$refs.holder;
      const holderRect = this.getRect(holder);
      const tipRect = this.getRect(tip);
      const padding = 8;
      if (this.position == "left") {
        // left
        this.top = (holderRect.height - tipRect.height) / 2;
        this.left = -1 * tipRect.width - padding;
      } else if (this.position == "bottom") {
        // bottom
        this.top = holderRect.height + 24;
        this.left = (holderRect.width - tipRect.width) / 2;
      } else {
        // top
        this.top = -1 * tipRect.height - 24;
        this.left = (holderRect.width - tipRect.width) / 2;
      }
      const overflowLeft = holderRect.left + this.left - padding;
      const overflowRight = document.documentElement.clientWidth - (holderRect.left + this.left + tipRect.width + padding);
      if (overflowLeft < 0) {
        this.left = this.left - overflowLeft;
      } else if (overflowRight < 0) {
        this.left = this.left + overflowRight;
      }
      this.top = this.top + "px";
      this.left = this.left + "px";
    },
  },
};
</script>
