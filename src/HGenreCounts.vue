<template>
  <div :id="'tut_genreCounts_p' + id"
       class="hb-mt-1 hb-text-15 hb-cursor-pointer"
       @click="click">

    <!-- Collapsed -->
    <div v-if="collapsed"
         class="hb-genreCounts hb-flex hb-grow hb-whitespace-nowrap hb-text-center hb-h-6 hb-leading-6">
      <div v-for="gc in counts"
           :key="gc.genre"
           :style="{ width: gc.percent + '%' }"
           :class="gc.class">
        <Icon class="hb-inline hb-text-16"
              :icon="gc.genre" />{{ gc.count }}
      </div>
    </div>

    <!-- Expanded -->
    <div v-if="!collapsed"
         v-for="gc in counts"
         :key="gc.genre"
         class="hb-px-2 hb-py-1 hb-flex hb-h-6"
         :class="gc.class">
      <Icon class="hb-text-16 hb-mr-1"
            :icon="gc.genre" /> {{ i18n(gc.genre) }}
      <div class="hb-grow hb-text-right">{{ gc.count }} / {{ gc.total }} ({{ gc.percentDisplay }}%)</div>
    </div>

  </div>
</template>

<script lang="js">
import { nextTick } from "vue";
import { Icon } from "@iconify/vue";

const repaint = () => new Promise((resolve) => requestAnimationFrame(resolve));

export default {
  name: "HGenreCounts",
  inject: ["genreCounts", "i18n"],
  components: { Icon },

  props: {
    id: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      collapsed: true,
    };
  },

  computed: {
    counts() {
      return this.genreCounts[this.id];
    },
  },

  methods: {
    async click() {
      this.collapsed = !this.collapsed;
      await repaint();
      await nextTick();
      this.game.adaptPlayersPanels();
    }
  }
};
</script>
