<template>
  <teleport :to="teleportTo">
    <div class="tailwind unmargin">
      <!-- Genre counts -->
      <HGenreCounts :counts="player.genreCounts" />

      <div class="m-2">
        <div>Ink: {{ player.ink || 0 }} &bull; Remover: {{ player.remover || 0 }}</div>
        <div>Deck: {{ player.deckCount || 0 }} cards</div>
        <div>Discard: {{ player.discardCount || 0 }} cards</div>

        <!-- Awards -->
        <div v-if="options.value.awards">Literary Award: {{ player.award }}<Icon class="inline text-18" icon="star" /></div>

        <!-- Adverts -->
        <div v-if="options.value.adverts" class="mt-1">Advet Level: {{ player.advert }}<Icon class="inline text-18" icon="star" /></div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
import HGenreCounts from "./HGenreCounts.vue";
import { Icon } from "@iconify/vue";

export default {
  name: "HPlayerPanel",
  inject: ["i18n", "options"],
  components: { Icon, HGenreCounts },

  props: {
    player: {
      type: Object,
      required: true,
    },
  },

  mounted() {
    if (this.player.order == 1) {
      const el = document.getElementById("icon_point_" + this.player.id);
      if (el) {
        el.after(document.createTextNode(" • 1st "));
      }
    }
  },

  computed: {
    teleportTo() {
      return "#player_board_" + this.player.id;
    },
  },
};
</script>
