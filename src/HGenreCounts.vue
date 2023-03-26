<template>
  <div :id="'tut_genreCounts_p' + id"
       class="mt-1 text-noshadow text-14 cursor-pointer"
       @click="click">

    <!-- Collapsed -->
    <div v-if="collapsed"
         class="genreCounts flex grow whitespace-nowrap text-center h-6 leading-6">
      <div v-for="gc in counts"
           :key="gc.genre"
           :style="{ width: gc.percent + '%' }"
           :class="gc.class">
        <Icon class="inline text-16"
              :icon="gc.genre" />{{ gc.count }}
      </div>
    </div>

    <!-- Expanded -->
    <div v-if="!collapsed"
         v-for="gc in counts"
         :key="gc.genre"
         class="px-2 py-1 flex h-6"
         :class="gc.class">
      <Icon class="text-16 mr-1"
            :icon="gc.genre" /> {{ i18n(gc.genre) }}
      <div class="grow text-right">{{ gc.count }} / {{ gc.total }} ({{ gc.percentDisplay }}%)</div>
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
