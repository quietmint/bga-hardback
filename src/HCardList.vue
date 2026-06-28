<template>
  <div :id="'cardlist_' + location"
       class="hb-cardlist hb-flex hb-flex-wrap hb-items-center hb-justify-center">
    <transition-group :css="false">
      <HCard v-for="card in cards"
             :key="card.id"
             :card="card" />
    </transition-group>
    <div v-if="cards.length == 0"
         class="hb-py-4 hb-text-center"
         v-text="emptyMessage"></div>
  </div>
</template>

<script lang="js">
import HCard from "./HCard.vue";

export default {
  name: "HCardList",
  inject: ["i18n", "locationVisible"],
  components: { HCard },

  props: {
    cards: {
      type: Array,
      required: true,
    },
    location: {
      type: String,
      required: true,
    },
  },

  watch: {
    location: {
      handler(newLocation, oldLocation) {
        if (oldLocation) {
          this.locationVisible.delete(oldLocation);
        }
        if (newLocation) {
          this.locationVisible.add(newLocation);
        }
      },
      immediate: true,
    },
  },

  mounted() {
    if (!window.PointerEvent) {
      // Old Safari?
      const holder = this.$refs.benefits;
      holder.addEventListener("mouseenter", this.tooltipEnter, false);
      holder.addEventListener("mouseleave", this.tooltipLeave, false);
    }
  },

  beforeUnmount() {
    this.locationVisible.delete(this.location);
    if (!window.PointerEvent) {
      // Old Safari?
      const holder = this.$refs.benefits;
      holder.removeEventListener("mouseenter", this.tooltipEnter, false);
      holder.removeEventListener("mouseleave", this.tooltipLeave, false);
    }
  },

  computed: {
    emptyMessage() {
      if (this.location) {
        let prefix = this.location.split("_")[0];
        let location = this.i18n(prefix + "Location");
        return this.i18n("empty", { location });
      }
    }
  }
};
</script>
