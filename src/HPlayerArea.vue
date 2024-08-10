<template>
  <div :id="'area_' + player.id"
       class="px-1 py-3 border-t-2 border-black">
    <div class="flex leading-8">
      <div class="title grow font-bold">
        <!-- Player name -->
        <span :class="player.colorName"
              class="text-18 playername"
              v-text="player.name"></span>
        <!-- Ink count -->
        <span :id="'inkCount_' + player.id">
          <span class="px-2">&bull;</span>
          <Icon icon="inkCount"
                class="inline text-18 text-black"
                :title="i18n('ink')" /> {{ player.ink }}
        </span>
        <!-- Remover count-->
        <span :id="'removerCount_' + player.id">
          <span class="px-2">&bull;</span>
          <Icon icon="removerCount"
                class="inline text-18 text-white"
                :title="i18n('remover')" /> {{ player.remover }}
        </span>
      </div>

      <!-- Myself only: Action buttons -->
      <div v-if="player.myself && gamestate.name != 'gameEnd'"
           class="flex">
        <!-- Lookup-->
        <div v-if="buttonEnabled['lookup']"
             class="buttongroup mb-1 ml-2 flex">
          <div id="button_lookup"
               @click="showLookup()"
               class="button blue">
            <Icon icon="dictionary"
                  class="inline text-17" /> {{ i18n('lookup') }}
          </div>
        </div>

        <!-- Uncover -->
        <div class="buttongroup mb-1 ml-2 flex">
          <div id="button_uncoverAll"
               @click="buttonEnabled['uncoverAll'] && uncoverAll()"
               class="button blue"
               :class="{ 'disabled': !buttonEnabled['uncoverAll'] }">
            <Icon icon="uncover"
                  class="inline text-17" /> {{ i18n('uncoverAll') }}
          </div>
        </div>

        <!-- Sort -->
        <div class="buttongroup mb-1 ml-2 grid grid-cols-3 leading-8">
          <div id="sort_tableau_letter"
               @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'letter')"
               class="button blue"
               :class="{ 'disabled': !buttonEnabled['sortTableau'] }"
               :title="i18n('sortLetterTip')">A-Z</div>
          <div id="sort_tableau_genre"
               @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'genre')"
               class="button blue"
               :class="{ 'disabled': !buttonEnabled['sortTableau'] }"
               :title="i18n('sortGenreTip')">
            <Icon icon="starter"
                  class="inline text-17 h-8" />
          </div>
          <div id="sort_tableau_shuffle"
               @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'shuffle')"
               class="button blue"
               :class="{ 'disabled': !buttonEnabled['sortTableau'] }"
               :title="i18n('shuffleTip')">
            <Icon icon="shuffle"
                  class="inline text-17 h-8" />
          </div>
        </div>
      </div>
    </div>

    <!-- Minis -->
    <div v-if="gamestate.name != 'gameEnd'"
         class="grid grid-cols-3 my-1 border-y-2 divide-x-2 font-bold"
         :class="[player.colorText, player.colorBorder, player.colorBg25]">

      <div :id="'tab_' + player.drawLocation"
           class="border-inherit">
        <!-- Draw buttons -->
        <div v-if="player.myself"
             class="buttongroup flex my-1 mx-2 leading-8">
          <div id="button_useInk"
               @click="buttonEnabled['useInk'] && useInk()"
               class="button black flex-1"
               :class="{ 'disabled': !buttonEnabled['useInk'] }">
            <Icon icon="inkCount"
                  class="inline text-17" /> {{ i18n('useInk', { count: player.ink }) }}
          </div>
          <div id="button_viewDraw"
               @click="buttonEnabled['viewDraw'] && clickView('draw', true)"
               class="button blue"
               :class="{ 'disabled': !buttonEnabled['viewDraw'], 'active': visibleView == 'draw' }">
            <Icon :icon="visibleView == 'draw' ? 'eyeX' : 'eye'"
                  class="h-8 inline text-17" />
          </div>
        </div>

        <!-- Draw label -->
        <div class="text-center border-inherit p-1"
             :class="[player.colorBg50, player.colorTextLight, { 'border-t-2': player.myself, 'border-b-2': options.open || player.myself }]">
          <span class="mr-2">
            <Icon icon="drawLocation"
                  class="inline align-text-top text-20" />
            {{ drawCards.length }}
          </span>
          {{ i18n('drawLocation') }}
        </div>

        <!-- Draw minis -->
        <div v-if="options.open || player.myself"
             class="flex flex-wrap justify-evenly m-1">
          <HCardMini v-for="card in drawCards"
                     :key="card.id"
                     :card="card" />
        </div>
      </div>

      <div :id="'tab_' + player.handLocation"
           class="border-inherit">
        <!-- Hand buttons -->
        <div v-if="player.myself"
             class="buttongroup flex my-1 mx-2 leading-8">
          <div id="button_playAll"
               @click="buttonEnabled['playAll'] && playAll()"
               class="button blue flex-1"
               :class="{ 'disabled': !buttonEnabled['playAll'] }">
            <Icon icon="download"
                  class="inline text-17" /> {{ i18n('playAll') }}
          </div>
          <div id="button_playNone"
               @click="buttonEnabled['playNone'] && playNone()"
               class="button blue flex-1"
               :class="{ 'disabled': !buttonEnabled['playNone'] }">
            <Icon icon="upload"
                  class="inline text-17" /> {{ i18n('playNone') }}
          </div>
          <div id="button_viewHand"
               @click="buttonEnabled['viewHand'] && clickView('hand', true)"
               class="button blue"
               :class="{ 'disabled': !buttonEnabled['viewHand'], 'active': visibleView == 'hand' }">
            <Icon :icon="visibleView == 'hand' ? 'eyeX' : 'eye'"
                  class="h-8 inline text-17" />
          </div>
        </div>

        <!-- Hand label -->
        <div class="text-center border-inherit p-1"
             :class="[player.colorBg50, player.colorTextLight, { 'border-t-2': player.myself, 'border-b-2': options.open || player.myself }]">
          <span class="mr-2">
            <Icon icon="handLocation"
                  class="inline align-text-top text-20" />
            {{ handCards.length }}
          </span>
          {{ i18n('handLocation') }}
        </div>

        <!-- Hand minis -->
        <div v-if="options.open || player.myself"
             class="flex flex-wrap justify-evenly m-1">
          <HCardMini v-for="card in handCards"
                     :key="card.id"
                     :card="card" />
        </div>
      </div>

      <div :id="'tab_' + player.discardLocation"
           class="border-inherit">
        <!-- Discard buttons -->
        <div v-if="player.myself"
             class="buttongroup flex my-1 mx-2 leading-8">
          <div id="button_viewDiscard"
               @click="buttonEnabled['viewDiscard'] && clickView('discard', true)"
               class="flex-1 button blue"
               :class="{ 'disabled': !buttonEnabled['viewDiscard'], 'active': visibleView == 'discard' }">
            <Icon :icon="visibleView == 'discard' ? 'eyeX' : 'eye'"
                  class="h-8 inline text-17" />
          </div>
        </div>

        <!-- Discard label -->
        <div class="text-center border-inherit p-1"
             :class="[player.colorBg50, player.colorTextLight, { 'border-t-2': player.myself, 'border-b-2': options.open || player.myself }]">
          <span class="mr-2">
            <Icon icon="discardLocation"
                  class="inline align-text-top text-20" />
            {{ discardCards.length }}
          </span>
          {{ i18n('discardLocation') }}
        </div>

        <!-- Discard minis -->
        <div v-if="options.open || player.myself"
             class="flex flex-wrap justify-evenly m-1">
          <HCardMini v-for="card in discardCards"
                     :key="card.id"
                     :card="card" />
        </div>
      </div>

    </div>

    <!-- Visible cards -->
    <div v-if="options.open || player.myself || player.id == gamestate.activeId">
      <div v-if="player.myself && visibleView != 'tableau'"
           class="buttongroup flex my-1 mx-auto w-3/4 leading-8">
        <div id="button_viewTableau"
             @click="clickView('tableau')"
             class="button blue active flex-1">
          <Icon icon="eyeX"
                class="inline text-17" /> {{ i18n('close', { location: visibleName }) }}
        </div>
      </div>
      <HCardList :cards="visibleCards"
                 :location="visibleLocation" />
    </div>
    <div v-else
         v-text="i18n('closedHands')"
         class="text-center italic m-4">
    </div>

  </div>
</template>

<script lang="js">
import HCardList from "./HCardList.vue";
import HCardMini from "./HCardMini.vue";
import { Icon } from "@iconify/vue";

export default {
  name: "HPlayerArea",
  emits: ["clickSort"],
  inject: ["cardsInLocation", "gamestate", "i18n", "options"],
  components: { Icon, HCardList, HCardMini },

  props: {
    player: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      visibleView: "",
    };
  },

  mounted() {
    this.clickView("tableau");
    if (this.player.myself) {
      this.emitter.on("clickView", this.clickView);
    }
  },

  computed: {
    buttonEnabled() {
      return {
        lookup: this.options.dictionary.dictId,
        playAll: this.gamestate.safeToMove && this.handCards.length > 0,
        playNone: this.gamestate.safeToMove && this.tableauCards.length > 0,
        sortTableau: this.gamestate.safeToMove,
        uncoverAll: this.gamestate.safeToMove && this.wildCards.length > 0,
        useInk: this.gamestate.safeToMove && this.player.ink && (this.drawCards.length > 0 || this.discardCards.length > 0),
        viewDiscard: this.visibleView == "discard" || this.discardCards.length > 0,
        viewDraw: this.visibleView == "draw" || this.drawCards.length > 0,
        viewHand: this.visibleView == "hand" || this.handCards.length > 0,
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
      if (this.visibleView) {
        return this.visibleView + "_" + this.player.id;
      }
    },

    visibleName() {
      return this.i18n(this.visibleView + "Location");
    },

    wildCards() {
      return this.tableauCards.filter((card) => card.wild);
    },
  },

  methods: {
    clickView(view, allowToggle) {
      if (allowToggle && this.visibleView == view) {
        view = "tableau";
      }
      this.visibleView = view;
    },

    playAll() {
      this.handCards.forEach((card) => {
        this.emitter.emit("clickCard", { action: { action: "move", destination: this.player.tableauLocation }, card });
      });
    },

    playNone() {
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
