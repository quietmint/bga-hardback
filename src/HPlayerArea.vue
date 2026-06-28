<template>
  <div :id="'area_' + player.id"
       class="hb-px-1 hb-py-3 hb-border-t-2 hb-border-black">
    <div class="hb-flex hb-leading-8">
      <div class="hb-title hb-grow hb-font-bold">
        <!-- Player name -->
        <span :class="'hb-' + player.colorName"
              class="hb-text-18 hb-playername"
              v-text="player.name"></span>
        <!-- Ink count -->
        <span :id="'inkCount_' + player.id">
          <span class="hb-px-2">&bull;</span>
          <Icon icon="inkCount"
                class="hb-inline hb-text-18 hb-text-black"
                :title="i18n('ink')" /> {{ player.ink }}
        </span>
        <!-- Remover count-->
        <span :id="'removerCount_' + player.id">
          <span class="hb-px-2">&bull;</span>
          <Icon icon="removerCount"
                class="hb-inline hb-text-18 hb-text-white"
                :title="i18n('remover')" /> {{ player.remover }}
        </span>
      </div>

      <!-- Myself only: Action buttons -->
      <div v-if="player.myself && gamestate.name != 'gameEnd'"
           class="hb-flex">
        <!-- Lookup-->
        <div v-if="buttonEnabled['lookup']"
             class="hb-buttongroup hb-mb-1 hb-ml-2 hb-flex">
          <div id="button_lookup"
               @click="showLookup()"
               class="hb-button hb-blue">
            <Icon icon="dictionary"
                  class="hb-inline hb-text-17" /> {{ i18n('lookup') }}
          </div>
        </div>

        <!-- Uncover -->
        <div class="hb-buttongroup hb-mb-1 hb-ml-2 hb-flex">
          <div id="button_uncoverAll"
               @click="buttonEnabled['uncoverAll'] && uncoverAll()"
               class="hb-button hb-blue"
               :class="{ 'hb-disabled': !buttonEnabled['uncoverAll'] }">
            <Icon icon="uncover"
                  class="hb-inline hb-text-17" /> {{ i18n('uncoverAll') }}
          </div>
        </div>

        <!-- Sort -->
        <div class="hb-buttongroup hb-mb-1 hb-ml-2 hb-grid hb-grid-cols-3 hb-leading-8">
          <div id="sort_tableau_letter"
               @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'letter')"
               class="hb-button hb-blue"
               :class="{ 'hb-disabled': !buttonEnabled['sortTableau'] }"
               :title="i18n('sortLetterTip')">A-Z</div>
          <div id="sort_tableau_genre"
               @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'genre')"
               class="hb-button hb-blue"
               :class="{ 'hb-disabled': !buttonEnabled['sortTableau'] }"
               :title="i18n('sortGenreTip')">
            <Icon icon="starter"
                  class="hb-inline hb-text-17 hb-h-8" />
          </div>
          <div id="sort_tableau_shuffle"
               @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'shuffle')"
               class="hb-button hb-blue"
               :class="{ 'hb-disabled': !buttonEnabled['sortTableau'] }"
               :title="i18n('shuffleTip')">
            <Icon icon="shuffle"
                  class="hb-inline hb-text-17 hb-h-8" />
          </div>
        </div>
      </div>
    </div>

    <!-- Draw/Hand/Deck minis -->
    <div v-if="gamestate.name != 'gameEnd'"
         class="hb-grid hb-grid-cols-3 hb-mt-1 hb-border-y-2 hb-divide-x-2 hb-font-bold"
         :class="[player.colorText, player.colorBorder, player.colorBg25]">

      <div :id="'tab_' + player.drawLocation"
           class="hb-border-inherit">
        <!-- Draw buttons -->
        <div v-if="player.myself"
             class="hb-buttongroup hb-flex hb-my-1 hb-mx-2 hb-leading-8">
          <div id="button_useInk"
               @click="buttonEnabled['useInk'] && useInk()"
               class="hb-button hb-black hb-flex-1"
               :class="{ 'hb-disabled': !buttonEnabled['useInk'] }">
            <Icon icon="inkCount"
                  class="hb-inline hb-text-17" /> {{ i18n('useInk', { count: player.ink }) }}
          </div>
          <div id="button_viewDraw"
               @click="buttonEnabled['viewDraw'] && clickView('draw', true)"
               class="hb-button hb-blue"
               :class="{ 'hb-disabled': !buttonEnabled['viewDraw'], 'active': visibleView == 'draw' }">
            <Icon :icon="visibleView == 'draw' ? 'eyeX' : 'eye'"
                  class="hb-h-8 hb-inline hb-text-17" />
          </div>
        </div>

        <!-- Draw label -->
        <div class="hb-text-center hb-border-inherit hb-p-1"
             :class="[player.colorBg50, player.colorTextLight, player.dark_colorText100, { 'border-t-2': player.myself, 'border-b-2': options.open || player.myself }]">
          <span class="hb-mr-2">
            <Icon icon="drawLocation"
                  class="hb-inline hb-align-text-top hb-text-20" />
            {{ drawCards.length }}
          </span>
          {{ i18n('drawLocation') }}
        </div>

        <!-- Draw minis -->
        <div v-if="options.open || player.myself"
             class="hb-flex hb-flex-wrap hb-justify-evenly hb-m-1">
          <HCardMini v-for="card in drawCards"
                     :key="card.id"
                     :card="card" />
        </div>
      </div>

      <div :id="'tab_' + player.handLocation"
           class="hb-border-inherit">
        <!-- Hand buttons -->
        <div v-if="player.myself"
             class="hb-buttongroup hb-flex hb-my-1 hb-mx-2 hb-leading-8">
          <div id="button_playAll"
               @click="buttonEnabled['playAll'] && playAll()"
               class="hb-button hb-blue hb-flex-1"
               :class="{ 'hb-disabled': !buttonEnabled['playAll'] }">
            <Icon icon="download"
                  class="hb-inline hb-text-17" /> {{ i18n('playAll') }}
          </div>
          <div id="button_playNone"
               @click="buttonEnabled['playNone'] && playNone()"
               class="hb-button hb-blue hb-flex-1"
               :class="{ 'hb-disabled': !buttonEnabled['playNone'] }">
            <Icon icon="upload"
                  class="hb-inline hb-text-17" /> {{ i18n('playNone') }}
          </div>
          <div id="button_viewHand"
               @click="buttonEnabled['viewHand'] && clickView('hand', true)"
               class="hb-button hb-blue"
               :class="{ 'hb-disabled': !buttonEnabled['viewHand'], 'active': visibleView == 'hand' }">
            <Icon :icon="visibleView == 'hand' ? 'eyeX' : 'eye'"
                  class="hb-h-8 hb-inline hb-text-17" />
          </div>
        </div>

        <!-- Hand label -->
        <div class="hb-text-center hb-border-inherit hb-p-1"
             :class="[player.colorBg50, player.colorTextLight, player.dark_colorText100, { 'border-t-2': player.myself, 'border-b-2': options.open || player.myself }]">
          <span class="hb-mr-2">
            <Icon icon="handLocation"
                  class="hb-inline hb-align-text-top hb-text-20" />
            {{ handCards.length }}
          </span>
          {{ i18n('handLocation') }}
        </div>

        <!-- Hand minis -->
        <div v-if="options.open || player.myself"
             class="hb-flex hb-flex-wrap hb-justify-evenly hb-m-1">
          <HCardMini v-for="card in handCards"
                     :key="card.id"
                     :card="card" />
        </div>
      </div>

      <div :id="'tab_' + player.discardLocation"
           class="hb-border-inherit">
        <!-- Discard buttons -->
        <div v-if="player.myself"
             class="hb-buttongroup hb-flex hb-my-1 hb-mx-2 hb-leading-8">
          <div id="button_viewDiscard"
               @click="buttonEnabled['viewDiscard'] && clickView('discard', true)"
               class="hb-flex-1 hb-button hb-blue"
               :class="{ 'hb-disabled': !buttonEnabled['viewDiscard'], 'active': visibleView == 'discard' }">
            <Icon :icon="visibleView == 'discard' ? 'eyeX' : 'eye'"
                  class="hb-h-8 hb-inline hb-text-17" />
          </div>
        </div>

        <!-- Discard label -->
        <div class="hb-text-center hb-border-inherit hb-p-1"
             :class="[player.colorBg50, player.colorTextLight, player.dark_colorText100, { 'border-t-2': player.myself, 'border-b-2': options.open || player.myself }]">
          <span class="hb-mr-2">
            <Icon icon="discardLocation"
                  class="hb-inline hb-align-text-top hb-text-20" />
            {{ discardCards.length }}
          </span>
          {{ i18n('discardLocation') }}
        </div>

        <!-- Discard minis -->
        <div v-if="options.open || player.myself"
             class="hb-flex hb-flex-wrap hb-justify-evenly hb-m-1">
          <HCardMini v-for="card in discardCards"
                     :key="card.id"
                     :card="card" />
        </div>
      </div>

    </div>

    <!-- Draw/Hand/Deck cards -->
    <div v-if="gamestate.name != 'gameEnd' && player.myself && visibleLocation != null"
         class="hb-w-4/5 hb-mx-auto hb-border-x-2 hb-border-b-2"
         :class="player.colorBorder, player.colorBg25">
      <div class="hb-py-1 hb-px-2">
        <div class="hb-buttongroup hb-flex hb-leading-8">
          <div id="button_viewTableau"
               @click="clickView(null)"
               class="hb-button hb-blue hb-active hb-flex-1">
            <Icon icon="eyeX"
                  class="hb-inline hb-text-17" /> {{ i18n('close', { location: visibleName }) }}
          </div>
        </div>
      </div>
      <HCardList :cards="visibleCards"
                 :location="visibleLocation" />
    </div>

    <!-- Tableau cards -->
    <div v-if="options.open || player.myself || player.id == gamestate.activeId || gamestate.name == 'gameEnd'">
      <div class="hb-text-center hb-font-bold hb-my-1 hb-leading-8"
           :class="player.colorTextDark, player.dark_colorText100">
        <span class="hb-mr-2">
          <Icon icon="tableauLocation"
                class="hb-inline hb-align-text-top hb-text-20" />
          {{ tableauCards.length }}
        </span>
        {{ i18n('tableauLocation') }}
      </div>
      <HCardList :cards="tableauCards"
                 :location="player.tableauLocation" />
    </div>
    <div v-else
         v-text="i18n('closedHands')"
         class="hb-text-center hb-italic hb-m-4">
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
      previousView: null,
      visibleView: null,
    };
  },

  mounted() {
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
      if (view == "previous") {
        view = this.previousView;
      } else if (allowToggle && this.visibleView == view) {
        view = null;
      }
      this.previousView = this.visibleView;
      this.visibleView = view;
      console.log(`👀 View ${view}`);
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
