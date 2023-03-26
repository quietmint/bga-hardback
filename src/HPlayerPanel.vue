<template>
  <teleport :to="teleportTo">
    <div class="tailwind panelbg">
      <div class="panel-left text-center">
        <div id="tut_firstPlayer"
             v-if="player.order == 1"
             class="text-14 text-left pl-1 pt-1"
             v-text="i18n('first')"></div>
        <div class="panel-ink flex items-center text-18 font-bold text-noshadow">
          <div :id="'tut_ink_p' + player.id"
               class="flex-1"
               :title="i18n('ink') + ': ' + player.ink">{{ player.ink }}</div>
          <div :id="'tut_remover_p' + player.id"
               class="flex-1 text-black"
               :title="i18n('remover') + ': ' + player.remover">{{ player.remover }}</div>
        </div>
      </div>

      <div class="panel-right flex flex-wrap gap-1 content-end justify-center text-18 font-bold text-center text-noshadow">
        <!-- Location Counts -->
        <div :id="'count_' + player.drawLocation"
             :class="{ 'cursor-pointer': player.myself && options.deck }"
             class="rounded-lg flex-2 whitespace-nowrap overflow-hidden bg-black/70 p-0.5"
             @click="player.myself && options.deck && clickTab('draw')"
             :title="i18n('drawLocation') + ': ' + drawCards.length">
          <Icon icon="drawLocation"
                class="inline text-20" /> {{ drawCards.length }}
        </div>
        <div :id="'count_' + player.handLocation"
             class="rounded-lg flex-2 whitespace-nowrap overflow-hidden bg-black/70 p-0.5"
             :class="{ 'cursor-pointer': player.myself }"
             @click="player.myself && clickTab('hand')"
             :title="i18n('handLocation') + ': ' + handCards.length">
          <Icon icon="handLocation"
                class="inline text-20" /> {{ handCards.length }}
        </div>
        <div :id="'count_' + player.tableauLocation"
             class="rounded-lg flex-2 whitespace-nowrap overflow-hidden bg-black/70 p-0.5"
             :title="i18n('tableauLocation') + ': ' + tableauCards.length">
          <Icon icon="tableauLocation"
                class="inline text-20" /> {{ tableauCards.length }}
        </div>
        <div :id="'count_' + player.discardLocation"
             :class="{ 'cursor-pointer': player.myself }"
             class="rounded-lg flex-2 whitespace-nowrap overflow-hidden bg-black/70 p-0.5"
             @click="player.myself && clickTab('discard')"
             :title="i18n('discardLocation') + ': ' + discardCards.length">
          <Icon icon="discardLocation"
                class="inline text-20" /> {{ discardCards.length }}
        </div>
      </div>

      <div class="panel-bottom">
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
  emits: ["clickTab"],
  inject: ["cardsInLocation", "i18n", "options"],
  components: { Icon, HAwardAdvert, HGenreCounts },

  props: {
    player: {
      type: Object,
      required: true,
    },
  },

  mounted() {
    document.getElementById("overall_player_board_" + this.player.id).classList.add(this.player.colorName);
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
    clickTab(tab) {
      this.emitter.emit("clickTab", tab);
    }
  },
};
</script>
