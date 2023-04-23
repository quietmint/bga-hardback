<template>
  <div :id="'area_' + player.id"
       class="px-1 py-3 border-t-2 border-black">
    <div class="flex leading-8">
      <div class="title grow leading-none">
        <!-- Player name -->
        <span :class="player.colorName"
              class="text-17 font-bold playername"
              v-text="player.name"></span>

        <!-- Ink, remover counts -->
        <div class="text-15 font-bold">
          <span class="whitespace-nowrap"
                :id="'counts_' + player.id">
            <Icon icon="inkCount"
                  class="inline text-18 text-black"
                  :title="i18n('ink')" /> {{ player.ink }}
            <span class="px-2">&bull;</span>
            <Icon icon="removerCount"
                  class="inline text-18 text-white"
                  :title="i18n('remover')" /> {{ player.remover }}
          </span>
          <!-- First player marker -->
          <span v-if="player.order == 1"
                class="px-2">&bull;</span>
          <span v-if="player.order == 1">{{ i18n('first') }}</span>
        </div>
      </div>

      <!-- Myself only: Action buttons (lookup, play all, ink) -->
      <div v-if="player.myself && gamestate.name != 'gameEnd'"
           class="flex">
        <div v-if="buttonEnabled['lookup']"
             class="buttongroup flex">
          <div id="button_lookup"
               @click="showLookup()"
               class="button blue">
            <Icon icon="dictionary"
                  class="inline text-17" /> <span v-text="i18n('lookup')"></span>
          </div>
        </div>

        <div class="buttongroup flex">
          <div v-if="!(buttonEnabled['returnAll'] && !buttonEnabled['playAll'])"
               id="button_playAll"
               @click="buttonEnabled['playAll'] && playAll()"
               class="button"
               :class="buttonEnabled['playAll'] ? 'blue' : 'disabled'">
            <Icon icon="move"
                  class="inline text-17" /> <span v-text="i18n('playAll')"></span>
          </div>
          <div v-if="buttonEnabled['returnAll'] && !buttonEnabled['playAll']"
               id="button_returnAll"
               @click="buttonEnabled['returnAll'] && returnAll()"
               class="button"
               :class="buttonEnabled['returnAll'] ? 'blue' : 'disabled'">
            <Icon icon="move"
                  class="inline text-17" /> <span v-text="i18n('returnAll')"></span>
          </div>
          <div id="button_uncoverAll"
               @click="buttonEnabled['uncoverAll'] && uncoverAll()"
               class="button"
               :class="buttonEnabled['uncoverAll'] ? 'blue' : 'disabled'">
            <Icon icon="uncover"
                  class="inline text-17" /> <span v-text="i18n('uncoverAll')"></span>
          </div>
        </div>

        <div class="buttongroup flex">
          <div id="button_useInk"
               @click="buttonEnabled['useInk'] && useInk()"
               class="button"
               :class="buttonEnabled['useInk'] ? 'black' : 'disabled'">
            <Icon icon="inkCount"
                  class="inline text-17" /> <span v-text="i18n('useInk', { count: player.ink })"></span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="player.myself && gamestate.name != 'gameEnd'"
         class="flex pt-3">
      <!-- Myself: Tabs for Draw, Hand, Discard -->
      <div class="tabgroup dark:text-gray-400"
           :class="player.colorBorder, player.colorTextDark">
        <div :id="'tab_' + player.drawLocation"
             @click="options.deck && clickTab('draw')"
             class="tab"
             :class="[tab == 'draw' ? player.colorBg50 + ' text-black dark:text-white' : player.colorBg20, options.deck ? 'cursor-pointer hover:text-black dark:hover:text-white' : 'cursor-not-allowed']">
          <span class="float-left flex items-center mr-2">
            <Icon icon="drawLocation"
                  class="text-20 mr-1" />
            {{ drawCards.length }}
          </span>
          {{ i18n('drawLocation') }}
        </div>
        <div :id="'tab_' + player.handLocation"
             @click="clickTab('hand')"
             class="tab cursor-pointer hover:text-black dark:hover:text-white"
             :class="[tab == 'hand' ? player.colorBg50 + ' text-black dark:text-white' : player.colorBg20]">
          <span class="float-left flex items-center mr-2">
            <Icon icon="handLocation"
                  class="text-20 mr-1" />
            {{ handCards.length }}
          </span>
          {{ i18n('handLocation') }}
        </div>
        <div :id="'tab_' + player.discardLocation"
             @click="clickTab('discard')"
             class="tab cursor-pointer hover:text-black dark:hover:text-white"
             :class="[tab == 'discard' ? player.colorBg50 + ' text-black dark:text-white' : player.colorBg20]">
          <span class="float-left flex items-center mr-2">
            <Icon icon="discardLocation"
                  class="text-20 mr-1" />
            {{ discardCards.length }}
          </span>
          {{ i18n('discardLocation') }}
        </div>
      </div>

      <!-- Myself: Sort for visible location -->
      <div class="buttongroup grid grid-cols-3 leading-8">
        <div id="sort_visible_letter"
             @click="sort(visibleLocation, 'letter')"
             class="button blue"
             :title="i18n('sortLetterTip')">A-Z</div>
        <div id="sort_visible_genre"
             @click="sort(visibleLocation, 'genre')"
             class="button blue"
             :title="i18n('sortGenreTip')">
          <Icon icon="starter"
                class="inline text-17 h-8" />
        </div>
        <div id="sort_visible_shuffle"
             @click="sort(visibleLocation, 'shuffle')"
             class="button blue"
             :title="i18n('shuffleTip')">
          <Icon icon="shuffle"
                class="inline text-17 h-8" />
        </div>
      </div>
    </div>

    <!-- Draw/Hand/Discard cards -->
    <div v-if="player.myself && gamestate.name != 'gameEnd'"
         class="rounded-lg p-1"
         :class="player.colorBg50">
      <HCardList :cards="visibleCards"
                 :location="visibleLocation" />
    </div>

    <div v-if="gamestate.name != 'gameEnd'"
         class="flex pt-3">
      <!-- Opponents: Collapse -->
      <Icon v-if="!player.myself"
            :id="'chevron_' + player.id"
            icon="chevron"
            :class="{ collapsed }"
            class="chevron text-24"
            @click="clickChevron()" />

      <!-- Tabs -->
      <div class="tabgroup dark:text-gray-400"
           :class="player.colorBorder, player.colorTextDark">
        <!-- Opponents: Tabs for Draw, Hand -->
        <div v-if="!player.myself"
             :id="'tab_' + player.drawLocation"
             class="tab cursor-not-allowed"
             :class="[player.colorBg20, { 'border-b-2': collapsed }]">
          <span class="float-left flex items-center mr-2">
            <Icon icon="drawLocation"
                  class="text-20 mr-1" />
            {{ drawCards.length }}
          </span>
          {{ i18n('drawLocation') }}
        </div>
        <div v-if="!player.myself"
             :id="'tab_' + player.handLocation"
             class="tab cursor-not-allowed"
             :class="[player.colorBg20, { 'border-b-2': collapsed }]">
          <span class="float-left flex items-center mr-2">
            <Icon icon="handLocation"
                  class="text-20 mr-1" />
            {{ handCards.length }}
          </span>
          {{ i18n('handLocation') }}
        </div>

        <!-- Everyone: Tab for Tableau -->
        <div :id="'tab_' + player.tableauLocation"
             class="tab text-black dark:text-white"
             :class="[player.colorBg50, { 'border-b-2': collapsed }]">
          <span class="float-left flex items-center mr-2">
            <Icon icon="tableauLocation"
                  class="text-20 mr-1" />
            {{ tableauCards.length }}
          </span>
          {{ i18n('tableauLocation') }}
        </div>

        <!-- Opponents: Tab for Discard -->
        <div v-if="!player.myself"
             :id="'tab_' + player.discardLocation"
             class="tab cursor-not-allowed"
             :class="[player.colorBg20, { 'border-b-2': collapsed }]">
          <span class="float-left flex items-center mr-2">
            <Icon icon="discardLocation"
                  class="text-20 mr-1" />
            {{ discardCards.length }}
          </span>
          {{ i18n('discardLocation') }}
        </div>
      </div>

      <!-- Myself: Sort for Tableau -->
      <div v-if="player.myself"
           class="buttongroup grid grid-cols-3 leading-8">
        <div id="sort_tableau_letter"
             @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'letter')"
             class="button"
             :class="buttonEnabled['sortTableau'] ? 'blue' : 'disabled'"
             :title="i18n('sortLetterTip')">A-Z</div>
        <div id="sort_tableau_genre"
             @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'genre')"
             class="button"
             :class="buttonEnabled['sortTableau'] ? 'blue' : 'disabled'"
             :title="i18n('sortGenreTip')">
          <Icon icon="starter"
                class="inline text-17 h-8" />
        </div>
        <div id="sort_tableau_shuffle"
             @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'shuffle')"
             class="button"
             :class="buttonEnabled['sortTableau'] ? 'blue' : 'disabled'"
             :title="i18n('shuffleTip')">
          <Icon icon="shuffle"
                class="inline text-17 h-8" />
        </div>
      </div>
    </div>

    <!-- Tableau cards -->
    <div v-if="!collapsed"
         class="rounded-lg p-1"
         :class="player.colorBg50">
      <HCardList :cards="tableauCards"
                 :location="player.tableauLocation" />
    </div>
  </div>
</template>

<script lang="js">
import HCardList from "./HCardList.vue";
import { Icon } from "@iconify/vue";

export default {
  name: "HPlayerArea",
  emits: ["clickSort", "clickTab"],
  inject: ["cardsInLocation", "gamestate", "i18n", "locationVisible", "options"],
  components: { Icon, HCardList },

  props: {
    player: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      collapsed: false,
      tab: "",
    };
  },

  mounted() {
    this.locationVisible.add(this.player.tableauLocation);
    if (this.player.myself) {
      this.emitter.on("clickTab", this.clickTab);
      this.clickTab("hand");
    }
  },

  beforeUnmount() {
    if (this.player.myself) {
      this.emitter.off("clickTab", this.clickTab);
    }
  },

  computed: {
    buttonEnabled() {
      return {
        lookup: this.options.lookup && this.options.dictionary && !this.options.dictionary.voting,
        playAll: this.gamestate.safeToMove && this.handCards.length > 0,
        returnAll: this.gamestate.safeToMove && this.tableauCards.length > 0,
        sortTableau: this.gamestate.safeToMove,
        uncoverAll: this.gamestate.safeToMove && this.wildCards.length > 0,
        useInk: this.gamestate.safeToMove && this.player.ink && (this.drawCards.length > 0 || this.discardCards.length > 0),
      };
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

    visibleCards() {
      return this.cardsInLocation(this.visibleLocation);
    },

    visibleLocation() {
      return this.tab + "_" + this.player.id;
    },

    wildCards() {
      const hand = this.handCards;
      const tableau = this.tableauCards;
      const wilds = hand.concat(tableau);
      return wilds.filter((card) => card.wild);
    },
  },

  methods: {
    clickChevron() {
      this.collapsed = !this.collapsed;
      if (this.collapsed) {
        this.locationVisible.delete(this.player.tableauLocation);
      } else {
        this.locationVisible.add(this.player.tableauLocation);
      }
      console.log('👀 ' + (this.collapsed ? 'Collapse' : 'Expand'), this.player.id);
    },

    clickTab(tab) {
      this.tab = tab;
      this.locationVisible.delete(this.player.drawLocation);
      this.locationVisible.delete(this.player.discardLocation);
      this.locationVisible.delete(this.player.handLocation);
      this.locationVisible.add(this.visibleLocation);
      console.log('👀 Tab', this.visibleLocation);
    },

    playAll() {
      this.handCards.forEach((card) => {
        this.emitter.emit("clickCard", { action: { action: "move", destination: this.player.tableauLocation }, card });
      });
    },

    returnAll() {
      this.tableauCards.forEach((card) => {
        this.emitter.emit("clickCard", { action: { action: "move", destination: card.origin }, card });
      });
    },

    showLookup() {
      let word = '';
      if (this.tableauCards.length > 0) {
        word = this.word(this.tableauCards);
      } else if (this.handCards.length > 0) {
        word = this.word(this.handCards);
      }
      this.emitter.emit("showLookup", { word });
    },

    sort(location, order) {
      this.emitter.emit("clickSort", { location, order });
    },

    uncoverAll() {
      this.wildCards.forEach((card) => {
        this.emitter.emit("clickFooter", { action: { action: "reset" }, card });
      });
    },

    useInk() {
      this.emitter.emit("useInk");
    },

    word(cards) {
      return cards.map((card) => card.wild || card.letter).join("");
    },
  },
};
</script>
