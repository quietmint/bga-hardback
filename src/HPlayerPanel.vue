<template>
  <teleport :to="teleportTo">
    <div class="hb-tailwind hb-panelbg">
      <div class="hb-panel-left hb-text-center">
        <div id="tut_firstPlayer"
             v-if="player.order == 1"
             class="hb-text-14 hb-text-left hb-text-shadow hb-pl-1 hb-pt-1"
             v-text="i18n('first')"></div>
        <div class="hb-panel-ink hb-flex hb-items-center hb-text-18 hb-font-bold">
          <div :id="'tut_ink_p' + player.id"
               class="hb-flex-1"
               :title="i18n('ink') + ': ' + player.ink">{{ player.ink }}</div>
          <div :id="'tut_remover_p' + player.id"
               class="hb-flex-1 hb-text-black"
               :title="i18n('remover') + ': ' + player.remover">{{ player.remover }}</div>
        </div>
      </div>

      <div class="hb-panel-right hb-flex hb-flex-wrap hb-gap-1 hb-content-end hb-justify-center hb-text-18 hb-font-bold hb-text-center">
        <!-- Location Counts -->
        <div :id="'count_' + player.drawLocation"
             :class="{ 'hb-cursor-pointer': player.myself && drawCards.length > 0 }"
             class="hb-rounded-lg hb-flex-2 hb-whitespace-nowrap hb-overflow-hidden hb-bg-black/70 hb-p-0.5"
             @click="player.myself && drawCards.length > 0 && clickView('draw')"
             :title="i18n('drawLocation') + ': ' + drawCards.length">
          <Icon icon="drawLocation"
                class="hb-inline hb-text-20" /> {{ drawCards.length }}
        </div>
        <div :id="'count_' + player.handLocation"
             class="hb-rounded-lg hb-flex-2 hb-whitespace-nowrap hb-overflow-hidden hb-bg-black/70 hb-p-0.5"
             :class="{ 'hb-cursor-pointer': player.myself && handCards.length > 0 }"
             @click="player.myself && handCards.length > 0 && clickView('hand')"
             :title="i18n('handLocation') + ': ' + handCards.length">
          <Icon icon="handLocation"
                class="hb-inline hb-text-20" /> {{ handCards.length }}
        </div>
        <div :id="'count_' + player.tableauLocation"
             class="hb-rounded-lg hb-flex-2 hb-whitespace-nowrap hb-overflow-hidden hb-bg-black/70 hb-p-0.5"
             :title="i18n('tableauLocation') + ': ' + tableauCards.length">
          <Icon icon="tableauLocation"
                class="hb-inline hb-text-20" /> {{ tableauCards.length }}
        </div>
        <div :id="'count_' + player.discardLocation"
             :class="{ 'hb-cursor-pointer': player.myself && discardCards.length > 0 }"
             class="hb-rounded-lg hb-flex-2 hb-whitespace-nowrap hb-overflow-hidden hb-bg-black/70 hb-p-0.5"
             @click="player.myself && discardCards.length > 0 && clickView('discard')"
             :title="i18n('discardLocation') + ': ' + discardCards.length">
          <Icon icon="discardLocation"
                class="hb-inline hb-text-20" /> {{ discardCards.length }}
        </div>
      </div>

      <div class="hb-panel-bottom">
        <HAwardAdvert :id="player.id"
                      :award="player.award"
                      :advert="player.advert" />
        <HGenreCounts :id="player.id" />
      </div>
    </div>
  </teleport>
</template>

<script lang="js">
import HAwardAdvert from "./HAwardAdvert.vue";
import HGenreCounts from "./HGenreCounts.vue";
import { Icon } from "@iconify/vue";

export default {
  name: "HPlayerPanel",
  emits: ["clickView"],
  inject: ["cardsInLocation", "i18n", "options"],
  components: { Icon, HAwardAdvert, HGenreCounts },

  props: {
    player: {
      type: Object,
      required: true,
    },
  },

  mounted() {
    document.getElementById("overall_player_board_" + this.player.id).classList.add("hb-" + this.player.colorName);
  },

  computed: {
    teleportTo() {
      return "#player_board_" + this.player.id;
    },

    drawCards() {
      return this.cardsInLocation(this.player.drawLocation);
    },

    discardCards() {
      return this.cardsInLocation(this.player.discardLocation);
    },

    handCards() {
      return this.cardsInLocation(this.player.handLocation);
    },

    tableauCards() {
      return this.cardsInLocation(this.player.tableauLocation);
    },
  },

  methods: {
    clickView(tab) {
      this.emitter.emit("clickView", tab);
    }
  },
};
</script>
