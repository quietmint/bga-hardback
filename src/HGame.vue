<template>
  <div class="select-none adjust-none">
    <!-- Player panels (moved using teleport) -->
    <HPlayerPanel v-for="(player, id) in players"
                  :key="id"
                  :player="player" />
    <HPenny v-if="gamedatas.penny"
            :penny="gamedatas.penny" />

    <!-- Icons -->
    <div class="hidden">
      <Icon :id="'icon_' + key"
            v-for="(icon, key) in icons"
            :key="key"
            :icon="icon" />
    </div>

    <!-- Keyboard popup -->
    <transition name="fade">
      <HKeyboard v-if="keyboardPopup"
                 :keyboardPopup="keyboardPopup" />
    </transition>

    <!-- Lookup popup -->
    <transition name="fade">
      <HLookup v-if="lookupPopup"
               :history="lookupHistory"
               :lookupPopup="lookupPopup"
               :myself="myself"
               :options="gamedatas.options" />
    </transition>

    <!-- Browser warning (Safari 12, etc.) -->
    <div v-if="warningVisible"
         class="m-4 p-3 text-17 font-bold text-center border-2 border-red-700 bg-white bg-opacity-50">
      <a href="https://browsehappy.com/"
         target="_blank"
         class="block text-blue-700"
         v-text="i18n('browserWarnTitle')"></a>
      <div class="text-15"
           v-text="i18n('browserWarnDesc')"></div>
    </div>

    <!-- Final round reminder -->
    <div v-if="gamedatas.finalRound && !gamedatas.options.coop"
         class="m-4 p-3 text-17 text-center font-bold border-2 border-red-700 bg-white bg-opacity-50"
         v-text="i18n('finalRound')"></div>

    <!-- Offer -->
    <div class="py-2 border-t-2 border-black">
      <div class="flex leading-7">
        <div id="tut_offer_title"
             class="title ml-2 flex-grow"
             v-text="i18n('offerLocation')"></div>

        <div class="buttongroup mr-2 grid grid-cols-4">
          <div id="tut_offer_sortLetter"
               @click="sort('offer', 'letter')"
               class="button blue"
               :class="{ active: locationOrder.offer == 'letter' }"
               :title="i18n('sortLetterTip')">A-Z</div>
          <div id="tut_offer_sortCost"
               @click="sort('offer', 'cost')"
               class="button blue text-15"
               :class="{ active: locationOrder.offer == 'cost' }"
               :title="i18n('sortCostTip')">¢</div>
          <div id="tut_offer_sortGenre"
               @click="sort('offer', 'genre')"
               class="button blue"
               :class="{ active: locationOrder.offer == 'genre' }"
               :title="i18n('sortGenreTip')">
            <Icon icon="starter"
                  class="inline text-17 h-7" />
          </div>
          <div id="tut_offer_sortTime"
               @click="sort('offer', 'order')"
               class="button blue"
               :class="{ active: locationOrder.offer == 'order' }"
               :title="i18n('sortTimeTip')">
            <Icon icon="clock"
                  class="inline text-17 h-7" />
          </div>
        </div>
      </div>

      <HCardList id="tut_offer_cards"
                 :cards="offerCards"
                 location="offer" />
    </div>

    <!-- Timeless Classics -->
    <div v-if="timelessVisible"
         class="py-2 border-t-2 border-black">
      <div class="flex leading-7">
        <div id="tut_timeless_title"
             class="title ml-2 flex-grow">{{ i18n('timelessLocation') }} ({{ timelessCards.length }})</div>
      </div>

      <HCardList id="tut_timeless_cards"
                 :cards="timelessCards"
                 location="timeless"
                 ref="timeless" />
    </div>

    <!-- Players -->
    <div v-for="player in playersSorted"
         :id="'tut_section_' + player.id"
         class="p-2 border-t-2 border-black">

      <div class="flex leading-7">
        <!-- Player name -->
        <div class="flex-grow">
          <span :class="player.colorText"
                class="title"
                v-text="player.name"></span>
          <span v-if="player.order == 1"
                class="text-14"> &mdash; {{ i18n('first') }}</span>
        </div>

        <!-- Myself only: Buttons for Cards -->
        <div v-if="player.myself && gamestate.name != 'gameEnd'"
             class="buttongroup flex">
          <div id="tut_moveAll"
               @click="(buttonEnabled['playAll'] && moveAll(player.handLocation, player.tableauLocation)) || (buttonEnabled['returnAll'] && moveAll(player.tableauLocation, 'origin'))"
               class="button"
               :class="buttonEnabled['playAll'] || buttonEnabled['returnAll'] ? 'blue' : 'disabled'">
            <Icon icon="move"
                  class="inline text-17" /> <span v-text="i18n(buttonEnabled['playAll'] ? 'playAll' : 'returnAll')"></span>
          </div>
          <div id="tut_resetAll"
               @click="buttonEnabled['resetAll'] && resetAll()"
               class="button"
               :class="buttonEnabled['resetAll'] ? 'blue' : 'disabled'">
            <Icon icon="reset"
                  class="inline text-17" /> <span v-text="i18n('resetAll')"></span>
          </div>
        </div>

        <div v-if="player.myself && gamestate.name != 'gameEnd' && buttonEnabled['lookup']"
             class="buttongroup flex">
          <div id="tut_lookup"
               @click="showLookup()"
               class="button blue">
            <Icon icon="dictionary"
                  class="inline text-17" /> <span v-text="i18n('lookup')"></span>
          </div>
        </div>

        <div v-if="player.myself && gamestate.name != 'gameEnd'"
             class="buttongroup flex">
          <div id="tut_useInk"
               @click="buttonEnabled['useInk'] && useInk()"
               class="button"
               :class="buttonEnabled['useInk'] ? 'blue' : 'disabled'">
            <Icon icon="ink"
                  class="inline text-17" /> <span v-text="i18n('draw', { count: myself.ink })"></span>
          </div>
        </div>

        <div v-if="player.myself && gamestate.name != 'gameEnd'"
             class="buttongroup grid grid-cols-3">
          <div id="tut_cards_sortLetter"
               @click="sort(visibleLocation, 'letter')"
               class="button blue"
               :class="{ active: locationOrder[tab] == 'letter' }"
               :title="i18n('sortLetterTip')">A-Z</div>
          <div id="tut_cards_sortGenre"
               @click="sort(visibleLocation, 'genre')"
               class="button blue"
               :class="{ active: locationOrder[tab] == 'genre' }"
               :title="i18n('sortGenreTip')">
            <Icon icon="starter"
                  class="inline text-17 h-7" />
          </div>
          <div id="tut_cards_shuffle"
               @click="sort(visibleLocation, 'shuffle')"
               class="button blue"
               :title="i18n('shuffleTip')">
            <Icon icon="shuffle"
                  class="inline text-17 h-7" />
          </div>
        </div>
      </div>

      <div v-if="player.myself && gamestate.name != 'gameEnd'"
           class="tabgroup"
           :class="player.colorBorder, player.colorTextDark">
        <!-- Myself: Tabs for Draw, Hand, Discard -->
        <div :id="'tab_draw_' + player.id"
             @click="this.gamedatas.options.deck && clickTab('draw')"
             class="tab"
             :class="[player.colorBgTab, { 'cursor-pointer': this.gamedatas.options.deck, 'cursor-not-allowed': !this.gamedatas.options.deck, 'active': tab == 'draw' }]">
          <Icon icon="deck"
                class="float-left text-20 mr-1" />
          <span>{{ i18n('drawLocation') }} ({{ cardsInLocation(player.drawLocation).length }})</span>
        </div>
        <div :id="'tab_hand_' + player.id"
             @click="clickTab('hand')"
             class="tab cursor-pointer"
             :class="[player.colorBgTab, { 'active': tab == 'hand' }]">
          <Icon icon="hand"
                class="float-left text-20 mr-1" />
          <span>{{ i18n('handLocation') }} ({{ cardsInLocation(player.handLocation).length }})</span>
        </div>
        <div :id="'tab_discard_' + player.id"
             @click="clickTab('discard')"
             class="tab cursor-pointer"
             :class="[player.colorBgTab, { 'active': tab == 'discard' }]">
          <Icon icon="shuffle"
                class="float-left text-20 mr-1" />
          <span>{{ i18n('discardLocation') }} ({{ cardsInLocation(player.discardLocation).length }})</span>
        </div>
      </div>

      <div v-if="player.myself && gamestate.name != 'gameEnd'"
           class="tabcontent"
           :class="player.colorBgTab">
        <HCardList :id="'tut_cards_' + player.id"
                   :cards="visibleCards"
                   :location="visibleLocation"
                   :ref="visibleLocation" />
      </div>

      <div v-if="gamestate.name != 'gameEnd'"
           class="tabgroup"
           :class="player.colorBorder, player.colorTextDark">
        <!-- Opponents: Tabs for Draw, Hand -->
        <div v-if="!player.myself"
             :id="'tab_draw_' + player.id"
             class="tab cursor-not-allowed"
             :class="player.colorBgTab">
          <Icon icon="deck"
                class="float-left text-20 mr-1" />
          <span>{{ i18n('drawLocation') }} ({{ cardsInLocation(player.drawLocation).length }})</span>
        </div>
        <div v-if="!player.myself"
             :id="'tab_hand_' + player.id"
             class="tab cursor-not-allowed"
             :class="player.colorBgTab">
          <Icon icon="hand"
                class="float-left text-20 mr-1" />
          <span>{{ i18n('handLocation') }} ({{ cardsInLocation(player.handLocation).length }})</span>
        </div>

        <!-- Everyone: Tab for Tableau -->
        <div :id="'tab_tableau_' + player.id"
             class="tab active"
             :class="player.colorBgTab">
          <Icon icon="starter"
                class="float-left text-20 mr-1" />
          <span>{{ i18n('tableauLocation') }} ({{ cardsInLocation(player.tableauLocation).length }})</span>
        </div>

        <!-- Opponents: Tab for Discard -->
        <div v-if="!player.myself"
             :id="'tab_discard_' + player.id"
             class="tab cursor-not-allowed"
             :class="player.colorBgTab">
          <Icon icon="shuffle"
                class="float-left text-20 mr-1" />
          <span>{{ i18n('discardLocation') }} ({{ cardsInLocation(player.discardLocation).length }})</span>
        </div>

        <!-- Myself: Buttons for Tableau -->
        <div v-if="player.myself"
             class="absolute right-0 leading-7 flex">
          <div class="buttongroup grid grid-cols-3">
            <div id="tut_tableau_sortLetter"
                 @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'letter')"
                 class="button"
                 :class="buttonEnabled['sortTableau'] ? 'blue' : 'disabled'"
                 :title="i18n('sortLetterTip')">A-Z</div>
            <div id="tut_tableau_sortGenre"
                 @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'genre')"
                 class="button"
                 :class="buttonEnabled['sortTableau'] ? 'blue' : 'disabled'"
                 :title="i18n('sortGenreTip')">
              <Icon icon="starter"
                    class="inline text-17 h-7" />
            </div>
            <div id="tut_tableau_shuffle"
                 @click="buttonEnabled['sortTableau'] && sort(player.tableauLocation, 'shuffle')"
                 class="button"
                 :class="buttonEnabled['sortTableau'] ? 'blue' : 'disabled'"
                 :title="i18n('shuffleTip')">
              <Icon icon="shuffle"
                    class="inline text-17 h-7" />
            </div>
          </div>
        </div>
      </div>

      <div class="tabcontent"
           :class="player.colorBgTab">
        <HCardList :id="'tut_tableau_' + player.id"
                   :cards="cardsInLocation(player.tableauLocation)"
                   :location="player.tableauLocation"
                   :ref="player.tableauLocation" />
      </div>
    </div>
  </div>
</template>

<script lang="js">
import HCardList from "./HCardList.vue";
import HConstants from "./HConstants.js";
import HKeyboard from "./HKeyboard.vue";
import HLookup from "./HLookup.vue";
import HPenny from "./HPenny.vue";
import HPlayerPanel from "./HPlayerPanel.vue";
import { Icon, addIcon } from "@iconify/vue";
import { firstBy } from "thenby";
import { nextTick, computed } from "vue";
import { throttle } from "lodash-es";

let lastErrorCode = null;

const queue = (callable) => {
  let busy = false;
  const q = [];
  const execute = function () {
    if (busy) {
      return; // queue is busy
    }
    busy = true;
    const item = q.shift();
    if (!item) {
      busy = false;
      return; // queue is empty
    }
    try {
      callable
        .apply(this, item.args)
        .then((value) => {
          item.resolve(value);
          busy = false;
          execute();
        })
        .catch((err) => {
          item.reject(err);
          busy = false;
          execute();
        });
    } catch (err) {
      item.reject(err);
      busy = false;
      execute();
    }
  };

  return (...args) => {
    return new Promise((resolve, reject) => {
      q.push({ args, resolve, reject });
      execute();
    });
  };
};

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const repaint = () => new Promise((resolve) => requestAnimationFrame(resolve));
const getRect = (el, calculateMargin = false) => {
  if (!el) {
    return null;
  }
  const bounds = el.getBoundingClientRect();
  let output = {
    top: bounds.top + window.scrollY,
    left: bounds.left + window.scrollX,
    width: bounds.width,
    height: bounds.height,
    offsetTop: el.offsetTop,
    offsetLeft: el.offsetLeft,
    marginTop: 0,
    marginBottom: 0,
    marginLeft: 0,
    marginRight: 0,
  };
  if (calculateMargin) {
    const style = getComputedStyle(el);
    output.marginTop = parseFloat(style.getPropertyValue("margin-top"));
    output.marginBottom = parseFloat(style.getPropertyValue("margin-bottom"));
    output.marginLeft = parseFloat(style.getPropertyValue("margin-left"));
    output.marginRight = parseFloat(style.getPropertyValue("margin-right"));
  }
  return output;
};
const getIcon = (key, cssClass) => {
  const el = document.getElementById("icon_" + key);
  if (el) {
    return el.outerHTML.replace(/ id=.*? /, ` class="icon-${key} ${cssClass}" `);
  }
};
const transitionEnd = (el) => {
  return new Promise((resolve) => {
    function onTransition(ev) {
      if (ev.target == el) {
        el.removeEventListener("transitionend", onTransition);
        el.removeEventListener("transitioncancel", onTransition);
        clearTimeout(timeout);
        resolve();
      }
    }
    el.addEventListener("transitionend", onTransition);
    el.addEventListener("transitioncancel", onTransition);
    const timeout = setTimeout(() => onTransition({ target: el }), 2400);
  });
};

// Genre icons
import bookmarkIcon from "@iconify-icons/mdi/bookmark";
import magnifyingGlass from "@iconify-icons/foundation/magnifying-glass";
import mdiCompass from "@iconify-icons/mdi/compass";
import mdiHeart from "@iconify-icons/mdi/heart";
import mdiSkull from "@iconify-icons/mdi/skull";

// Card icons
import cachedIcon from "@iconify-icons/mdi/cached";
import lockIcon from "@iconify-icons/mdi/lock";
import starOutlined from "@iconify-icons/ant-design/star-outlined";

// Button icons
import bookOpenPageVariant from "@iconify-icons/mdi/book-open-page-variant";
import chevronDown from "@iconify-icons/mdi/chevron-down";
import clockOutline from "@iconify-icons/mdi/clock-outline";
import flipHorizontal from "@iconify-icons/mdi/flip-horizontal";
import plusBox from "@iconify-icons/mdi/plus-box";
import shuffleVariant from "@iconify-icons/mdi/shuffle-variant";
import swapVerticalBold from "@iconify-icons/mdi/swap-vertical-bold";

// Player panel icons
import cardsIcon from "@iconify-icons/mdi/cards";
import handRight from "@iconify-icons/mdi/hand-right";

// Lookup word icons
import checkCircle from "@iconify-icons/mdi/check-circle";
import closeCircle from "@iconify-icons/mdi/close-circle";
import loadingIcon from "@iconify-icons/mdi/loading";

export default {
  name: "HGame",
  components: { Icon, HCardList, HKeyboard, HLookup, HPenny, HPlayerPanel },

  provide() {
    return {
      gamestate: this.gamestate,
      getRect: getRect,
      i18n: this.i18n,
      myself: computed(() => this.myself),
      options: computed(() => this.gamedatas.options),
      prefs: computed(() => this.prefs),
      refs: computed(() => this.gamedatas.refs),
    };
  },

  created() {
    this.previewWord = throttle(this.previewWord, 1500);
    this.takeActionAjax = queue(this.takeActionAjax);
    for (const key in this.icons) {
      addIcon(key, this.icons[key]);
    }
  },

  mounted() {
    this.emitter.on("clickCard", this.clickCard);
    this.emitter.on("clickFooter", this.clickFooter);
    this.emitter.on("clickKey", this.clickKey);
    this.emitter.on("clickLookup", this.clickLookup);
    this.emitter.on("dragStart", this.dragStart);
    Object.keys(this.locationOrder).forEach((location) => {
      try {
        let saved = localStorage.getItem("hardback.sort." + location);
        if (saved) {
          this.locationOrder[location] = saved;
        }
      } catch (ignore) { }
    });

    for (const key in this.icons) {
      this.icons105[key] = getIcon(key, "inline text-105");
    }
  },

  beforeUnmount() {
    this.emitter.off("clickCard", this.clickCard);
    this.emitter.off("clickFooter", this.clickFooter);
    this.emitter.off("clickKey", this.clickKey);
    this.emitter.off("clickLookup", this.clickLookup);
    this.emitter.off("dragStart", this.dragStart);
  },

  data() {
    return {
      gamedatas: {
        cards: {},
        finalRound: false,
        options: {},
        penny: {},
        players: {},
        refs: {
          adverts: {},
          awards: {},
          benefits: {},
          cards: {},
          i18n: {},
          signatures: {},
        },
        version: null,
      },
      gamestate: {},

      drag: null,
      keyboardPopup: null,
      lookupHistory: [],
      lookupPopup: null,
      prefs: {},
      locationOrder: {
        discard: "genre",
        draw: "genre",
        offer: "order",
        timeless: "location",
      },
      tab: "hand",
      icons: {
        adventure: mdiCompass,
        chevron: chevronDown,
        clock: clockOutline,
        deck: cardsIcon,
        dictionary: bookOpenPageVariant,
        hand: handRight,
        horror: mdiSkull,
        ink: plusBox,
        jail: lockIcon,
        loading: loadingIcon,
        move: swapVerticalBold,
        mystery: magnifyingGlass,
        no: closeCircle,
        reset: flipHorizontal,
        romance: mdiHeart,
        shuffle: shuffleVariant,
        star: starOutlined,
        starter: bookmarkIcon,
        timeless: cachedIcon,
        yes: checkCircle,
      },
      icons105: {},
    };
  },

  computed: {
    buttonEnabled() {
      return {
        lookup: this.gamedatas.options.lookup && this.gamedatas.options.dictionary && !this.gamedatas.options.dictionary.voting,
        playAll: this.gamestate.safeToMove && this.handCards.length > 0,
        resetAll: this.gamestate.safeToMove && this.wildCards.length > 0,
        returnAll: this.gamestate.safeToMove && this.tableauCards.length > 0,
        sortTableau: this.gamestate.safeToMove,
        useInk: this.gamestate.safeToMove && this.myself.ink && (this.cardsInLocation(this.myself.drawLocation).length > 0 || this.cardsInLocation(this.myself.discardLocation).length > 0),
      };
    },

    handCards() {
      return this.cardsInLocation(this.myself.handLocation);
    },

    myself() {
      return this.players[this.game.player_id] || {};
    },

    offerCards() {
      let jail = this.cardsInLocation("jail");
      let offer = this.cardsInLocation("offer");
      Array.prototype.push.apply(jail, offer);
      return jail;
    },

    players() {
      return this.populatePlayers(this.gamedatas.players) || {};
    },

    playersSorted() {
      return Object.values(this.players).sort((a, b) => a.myorder - b.myorder);
    },

    spectator() {
      return this.game.isSpectator;
    },

    tableauCards() {
      return this.cardsInLocation(this.myself.tableauLocation);
    },

    timelessCards() {
      return this.cardsInLocation("timeless");
    },

    timelessVisible() {
      return this.timelessCards.length > 0 || Object.values(this.gamedatas.cards).some((card) => {
        return card.origin.startsWith("timeless");
      });
    },

    visibleCards() {
      return this.cardsInLocation(this.visibleLocation);
    },

    visibleLocation() {
      return this.tab + "_" + this.game.player_id;
    },

    warningVisible() {
      return window.PointerEvent === undefined && this.prefs[HConstants.PREF_DRAG_DROP] !== HConstants.DRAG_DROP_DISABLED;
    },

    wildCards() {
      let hand = this.cardsInLocation(this.myself.handLocation);
      let tableau = this.cardsInLocation(this.myself.tableauLocation);
      Array.prototype.push.apply(hand, tableau);
      return hand.filter((card) => card.wild);
    },
  },

  methods: {
    /*
     * Utility functions
     */

    i18n(msg, args) {
      if (!msg) {
        return "";
      }
      if (this.gamedatas.refs.i18n[msg]) {
        msg = this.gamedatas.refs.i18n[msg];
      }
      const argsWithIcons = args ? { ...this.icons105, ...args } : { ...this.icons105 };
      msg = this.game.format_string_recursive(window._(msg), argsWithIcons);
      return msg;
    },

    getLocationEl(location) {
      if (location.startsWith("timeless")) {
        location = "timeless";
      }
      let component = this.$refs[location];
      if (Array.isArray(component)) {
        component = component[0];
      }
      if (component) {
        return component.$refs.cardlist;
      }
    },

    getCardEl(card) {
      if (typeof card == "number") {
        card = this.gamedatas.cards[card];
      }
      return document.getElementById("cardholder_" + card.id);
      /*
      // Vue can't nest refs? ("hoisted")
      const locationComponent = this.$refs[card.location];
      if (locationComponent) {
        const cardComponent = locationComponent.$refs[card.id];
        if (cardComponent) {
          return cardComponent.$refs.cardholder;
        }
      }
      */
    },

    cardsInLocation(location) {
      if (location == null) {
        return [];
      }
      if (location.startsWith("timeless")) {
        location = "timeless";
      }
      let cards = this.populateCards(
        Object.values(this.gamedatas.cards).filter((card) => {
          return card.location.startsWith(location);
        })
      );
      let prefix = location.split("_")[0];
      this.sortCards(this.locationOrder[prefix], cards);
      return cards;
    },

    sortCards(order, cards) {
      order = order || "order";
      let sorter = firstBy(order);
      if (order != "genre") {
        sorter = sorter.thenBy("genre");
      }
      if (order != "letter") {
        sorter = sorter.thenBy("letter");
      }
      sorter = sorter.thenBy("id");
      cards.sort(sorter);
    },

    shuffleCards(cards) {
      for (let i = cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [cards[i], cards[j]] = [cards[j], cards[i]];
      }
    },

    nextOrderInLocation(location) {
      const cards = this.cardsInLocation(location);
      const max = cards.map((card) => card.order).reduce((acc, cur) => Math.max(acc, cur), -1);
      return max + 1;
    },

    populateCard(card) {
      let newCard = Object.assign({ factor: 1 }, this.gamedatas.refs.cards[card.refId], card);
      newCard.genreName = HConstants.GENRES[newCard.genre].icon;

      // Owning player
      if (newCard.location.startsWith("jail")) {
        const playerId = newCard.origin.split("_")[1];
        newCard.player = this.players[playerId];
      } else if (newCard.origin.startsWith("timeless")) {
        const playerId = newCard.origin.split("_")[1];
        newCard.player = this.players[playerId];
      }

      return newCard;
    },

    populateCards(cards) {
      if (!Array.isArray(cards)) {
        return null;
      }
      return cards.map(this.populateCard);
    },

    cardIds(cards) {
      if (!Array.isArray(cards)) {
        return null;
      }
      return cards.map((card) => card.id);
    },

    wildMask(cards) {
      return cards.map((card) => card.wild || "_").join("");
    },

    word(cards) {
      return cards.map((card) => card.wild || card.letter).join("");
    },

    populatePlayer(player) {
      player.myself = player.id == this.game.player_id;
      if (this.spectator) {
        player.myorder = player.order;
      } else {
        player.myorder = this.gamedatas.playerorder.indexOf(player.id);
        if (player.myorder == -1) {
          player.myorder = this.gamedatas.playerorder.indexOf(player.id + '');
        }
      }
      switch (player.colorName) {
        case "red":
          player.colorBg = "bg-red-700";
          player.colorBgTab = player.colorBg;
          player.colorBorder = "border-red-700";
          player.colorRing = "ring-red-700";
          player.colorText = "text-red-700";
          player.colorTextDark = "text-red-900";
          player.colorTextLight = "text-red-100";
          break;
        case "green":
          player.colorBg = "bg-green-700";
          player.colorBgTab = player.colorBg;
          player.colorBorder = "border-green-700";
          player.colorRing = "ring-green-700";
          player.colorText = "text-green-700";
          player.colorTextDark = "text-green-900";
          player.colorTextLight = "text-green-100";
          break;
        case "blue":
          player.colorBg = "bg-blue-700";
          player.colorBgTab = player.colorBg;
          player.colorBorder = "border-blue-700";
          player.colorRing = "ring-blue-700";
          player.colorText = "text-blue-700";
          player.colorTextDark = "text-blue-900";
          player.colorTextLight = "text-blue-100";
          break;
        case "yellow":
          player.colorBg = "bg-yellow-500";
          player.colorBgTab = "bg-yellow-600";
          player.colorBorder = "border-yellow-600";
          player.colorRing = "ring-yellow-500";
          player.colorText = "text-yellow-600";
          player.colorTextDark = "text-yellow-900";
          player.colorTextLight = "text-yellow-900";
          break;
        case "purple":
          player.colorBg = "bg-purple-700";
          player.colorBgTab = player.colorBg;
          player.colorBorder = "border-purple-700";
          player.colorRing = "ring-purple-700";
          player.colorText = "text-purple-700";
          player.colorTextDark = "text-purple-900";
          player.colorTextLight = "text-purple-100";
          break;
      }
      return player;
    },

    populatePlayers(players) {
      if (players != null) {
        for (const playerId in players) {
          players[playerId] = this.populatePlayer(players[playerId]);
        }
      }
      return players;
    },

    /*
     * Animation
     */
    async animateCard(card, changes) {
      if (changes) {
        card = Object.assign({}, card, changes);
      }

      if (this.gamestate.instant) {
        // Instant replay, no animation
        this.gamedatas.cards[card.id] = card;
        return;
      }

      const oldCard = this.gamedatas.cards[card.id];
      if (oldCard != null && (oldCard.location == card.location || (oldCard.location == "offer" && card.location.startsWith("jail")))) {
        // Same location, no animation
        this.gamedatas.cards[card.id] = card;
        // Delay for Vue to animate the reorder
        if (oldCard.order != card.order || (oldCard.location == "offer" && card.location.startsWith("jail"))) {
          await sleep(600);
        }
        return;
      }

      // Compute start position
      let cardEl = null;
      let start = null;
      let end = null;
      if (oldCard != null) {
        cardEl = this.getCardEl(oldCard);
        start = getRect(cardEl);
      }

      let visible = card.location == "offer" || card.location.startsWith("jail") || card.location.startsWith("timeless") || card.location.startsWith("tableau") || card.location == this.visibleLocation;
      if (!start && !visible) {
        // Invisible card movement, no animation
        this.gamedatas.cards[card.id] = card;
        return;
      }

      let mode = null;
      if (start && !visible) {
        mode = "leave";
        if (card.location.startsWith("discard_") || card.location == this.myself.drawLocation) {
          let playerId = card.location.split("_")[1];
          let parentEl = document.getElementById("overall_player_board_" + playerId);
          end = getRect(parentEl);
        }
        if (end == null) {
          // Exit below
          end = { top: window.innerHeight + start.height, left: start.left };
        }

        // Clone, which will be animated
        const rootEl = document.getElementById("HGame");
        const root = getRect(rootEl);
        const top = end.top - root.top;
        const left = end.left - root.left;
        cardEl = cardEl.cloneNode(true);
        cardEl.id = "";
        cardEl.style.position = "absolute";
        cardEl.style.top = top + "px";
        cardEl.style.left = left + "px";
        rootEl.appendChild(cardEl);
        await repaint();
        end = getRect(cardEl);

        // Vue will delete the original
        this.gamedatas.cards[card.id] = card;
        await nextTick();
      } else {
        mode = "enter";
        // Vue will create the card
        this.gamedatas.cards[card.id] = card;
        await nextTick();
        cardEl = this.getCardEl(card);
        if (!cardEl) {
          console.warn(`Animate card ${card.id} ${mode} element not found`);
          return;
        }
        end = getRect(cardEl);
        if (start == null) {
          // Enter from left
          start = { top: end.top, left: -end.width };
        }
      }

      // Animate
      await this.animateFlip(cardEl, start, end);
      if (mode == "leave") {
        // Destroy the clone
        cardEl.remove();
      }
    },

    async animateFlip(el, start, end) {
      // FLIP technique: https://aerotwist.com/blog/flip-your-animations/
      // Element is already at the end position
      // Immediately translate it back to the start position
      // Then run a normal CSS transition in "reverse"
      const diffX = start.left - end.left;
      const diffY = start.top - end.top;
      el.style.transition = "none";
      el.style.transform = "translate(" + diffX + "px, " + diffY + "px)";
      await repaint();
      el.style.transition = "";
      el.style.transform = "";
      await repaint();
      return transitionEnd(el);
    },

    /*
     * BGA framework methods
     */
    takeAction(action, data) {
      data = data || {};
      data.version = this.gamedatas.version;
      if (data.lock === false) {
        delete data.lock;
      } else {
        data.lock = true;
        if (this.game.isInterfaceLocked()) {
          throw `Take action ${action} ignored by interface lock`;
        }
      }
      for (const key in data) {
        if (Array.isArray(data[key])) {
          data[key] = data[key].join(",");
        }
      }
      if (action != "previewWord") {
        // Cancel any pending preview
        this.previewWord.cancel();
      }
      return this.takeActionAjax(action, data).catch((errorMsg) => {
        if (errorMsg == "endGameWarning") {
          this.game.confirmationDialog(this.i18n("endGameWarning"), () => {
            data.lock = true;
            data.endGameConfirm = true;
            this.takeActionAjax(action, data);
          });
        } else {
          throw errorMsg;
        }
      });
    },

    takeActionAjax(action, data) {
      return new Promise((resolve, reject) => {
        const start = Date.now();
        console.log(`Take action ${action}`, data);
        this.game.ajaxcall(
          "/hardback/hardback/" + action + ".html",
          data,
          this,
          () => { },
          (error) => {
            const duration = Date.now() - start;
            if (error) {
              console.error(`Take action ${action} error in ${duration}ms`, lastErrorCode);
              reject(lastErrorCode);
            } else {
              console.log(`Take action ${action} done in ${duration}ms`);
              resolve();
            }
          }
        );
      });
    },

    /*
     * BGA framework event callbacks
     */
    onFormatString(log, args) {
      if (args) {
        if (args.award) {
          args.award = `<div class="haward length${args.award}"></div>`;
        }
        if (args.dict && args.link) {
          args.dict = `<a target="hdefine" href="${args.link}">${args.dict}</a>`;
        }
        if (args.word) {
          const q = args.word.toLowerCase();
          let links = [];
          if (this.gamedatas.options.dictionary.langId == HConstants.LANG_EN) {
            links = [
              [
                `<a target="hdefine" href="https://dictionary.cambridge.org/search/english/direct/?q=${q}">Cambridge</a>`, //
                `<a target="hdefine" href="https://www.merriam-webster.com/dictionary/${q}">Merriam-Webster</a>`, //
              ],
              [
                `<a target="hdefine" href="https://www.collinsdictionary.com/dictionary/english/${q}">Collins</a>`, //
                `<a target="hdefine" href="https://www.lexico.com/en/definition/${q}">Oxford Lexico</a>`, //
              ],
              [
                `<a target="hdefine" href="https://www.dictionary.com/browse/${q}">Dictionary.com</a>`, //
                `<a target="hdefine" href="https://en.wiktionary.org/wiki/${q}">Wiktionary</a>`, //
              ],
            ];
          } else if (this.gamedatas.options.dictionary.langId == HConstants.LANG_DE) {
            links = [
              [
                `<a target="hdefine" href="https://www.duden.de/suchen/dudenonline/${q}">Duden</a>`, //
              ],
            ];
          } else if (this.gamedatas.options.dictionary.langId == HConstants.LANG_FR) {
            links = [
              [
                `<a target="hdefine" href="https://www.cnrtl.fr/definition/academie9/${q}">Académie Française</a>`, //
                `<a target="hdefine" href="https://dictionnaire.lerobert.com/definition/${q}">Le Robert</a>`, //
              ],
              [
                `<a target="hdefine" href="https://www.larousse.fr/dictionnaires/francais/${q}">Larousse</a>`, //
                `<a target="hdefine" href="https://www.cnrtl.fr/definition/${q}">Trésor</a>`, //
              ],
            ];
          }
          if (links.length > 0) {
            const table = "<table><tr>" + links.map((o) => `<td>• ${o.join("</td><td>• ")}</td>`).join("</tr><tr>") + "</tr></table>";
            args.definitions = `<div class="hdefine">${this.i18n("dictionary")}${table}</div>`;
          } else {
            args.definitions = "";
          }
          args.word = `<b>${args.word}</b>`;
        }
        for (const k in args) {
          let v = args[k];
          if (k.startsWith("icon")) {
            const html = getIcon(v.toLowerCase().trim(), "hicon");
            if (html) {
              v = html;
            } else if (v != "¢") {
              v = ` ${this.i18n(v)}`;
            }
          } else if (k.startsWith("genre")) {
            const html = getIcon(v.toLowerCase().trim(), "hicon");
            v = `<span class="hgenre ${v}">${html ? html : v}`;
          } else if (k.startsWith("letter")) {
            v = `${v}</span>`;
          }
          args[k] = v;
        }
      }
      return log;
    },

    onUpdateActionButtons(stateName, args) {
      Object.assign(this.gamestate, this.game.gamedatas.gamestate, {
        active: this.game.isCurrentPlayerActive(),
        instant: this.game.instantaneousMode,
      });
      this.gamestate.safeToMove = this.gamestate.name != "gameEnd" && (!this.gamestate.active || this.gamestate.name == "playerTurn");
      let activeId = this.game.getActivePlayerId();
      if (stateName == "vote") {
        activeId = args.player_id;
      }
      if (activeId) {
        this.gamestate.activeId = activeId;
      }
      console.log(`State ${stateName}`, args);

      const actionRef = {
        confirmWord: {
          text: "confirmButton",
          color: "blue",
          async function() {
            if (this.tableauCards.length == 0) {
              // Did you forget to play any cards?
              await this.moveAll(this.myself.handLocation, this.myself.tableauLocation);
            }
            let cardIds = this.cardIds(this.tableauCards);
            let wildMask = this.wildMask(this.tableauCards);
            this.takeAction("confirmWord", { cardIds, wildMask });
          },
        },
        voteAccept: {
          text: "voteAcceptButton",
          color: "blue",
          function() {
            this.takeAction("voteWord", { vote: true });
          },
        },
        voteReject: {
          text: "voteRejectButton",
          color: "red",
          function() {
            this.takeAction("voteWord", { vote: false });
          },
        },
        skipWord: {
          text: "skipWordButton",
          color: "red",
          function() {
            this.game.confirmationDialog(this.i18n("skipWordWarning"), () => {
              this.takeAction("skipWord");
            });
          },
        },
        skipPurchase: {
          text: "skipPurchaseButton",
          color: "blue",
          function() {
            this.takeAction("skipPurchase");
          },
        },
        skip: {
          text: "skipButton",
          color: "gray",
          function() {
            this.takeAction("skip");
          },
        },
        flush: {
          text: "flushButton",
          color: "blue",
          function() {
            this.takeAction("flush");
          },
        },
        doctor: {
          text() {
            return this.i18n("doctorButton", this.gamestate.args.advert);
          },
          color: "blue",
          function() {
            this.takeAction("doctor");
          },
          condition() {
            return this.gamestate.args && this.gamestate.args.advert;
          },
        },
        convert: {
          text() {
            return this.i18n("convertButton", this.gamestate.args.convert);
          },
          color: "gray",
          function() {
            this.takeAction("convert");
          },
          condition() {
            return this.gamestate.args && this.gamestate.args.convert;
          },
        },
      };

      // No actions for inactive players
      if (!this.gamestate.active) {
        return;
      }

      let possible = this.gamestate.possibleactions;
      possible.forEach((p, index) => {
        const action = actionRef[p];
        let visible = action != null && (action.condition == null || action.condition.apply(this));
        if (visible) {
          let text = action.text;
          if (typeof text == "function") {
            text = text.apply(this);
          }
          text = this.i18n(text, this.gamestate.args);
          this.game.addActionButton(
            "action_" + index,
            text,
            () => {
              action.function.apply(this);
            },
            null,
            false,
            action.color
          );
        }
      });
    },

    onEnteringState(stateName, args) {
      if (args && args.updateGameProgression) {
        this.gamedatas.finalRound = this.gamestate.name != "gameEnd" && args.updateGameProgression >= 100;
      }
      if (this.gamestate.active && this.gamestate.name == "trashDiscard" && this.gamestate.args && !this.gamestate.args.skip) {
        this.tab = "discard";
      }
    },

    onLeavingState(stateName) {
      if (this.gamestate.active && this.gamestate.name == "trashDiscard" && this.gamestate.args && !this.gamestate.args.skip) {
        this.tab = "hand";
      }
    },

    onNotify(notif) {
      console.log(`Notify ${notif.type}`, notif.args);
      if (notif.type == "cards" || notif.type == "cardsPreview") {
        if (notif.args.ignorePlayerId && notif.args.ignorePlayerId == this.game.player_id) {
          this.game.notifqueue.setSynchronousDuration(1);
          return;
        }
        let cards = Object.values(notif.args.cards);
        cards.sort(firstBy("location").thenBy("order").thenBy("id"));
        Promise.all(cards.map(this.animateCard)).then(() => {
          this.game.notifqueue.setSynchronousDuration(1);
        });
      } else if (notif.type == "ink") {
        if (this.game.player_id != notif.args.player_id) {
          this.game.disableNextMoveSound();
        }
      } else if (notif.type == "invalid") {
        if (!this.gamestate.instant && this.game.player_id == notif.args.player_id) {
          this.game.showMessage(this.i18n(notif.log, notif.args), "error no_log");
        }
      } else if (notif.type == "lookup") {
        for (let i = 0; i < this.lookupHistory.length; i++) {
          let hist = this.lookupHistory[i];
          // onFormatString may add bold tag
          if (notif.args.word == hist.word || notif.args.word == `<b>${hist.word}</b>`) {
            hist.icon = notif.args.valid ? "yes" : "no";
            break;
          }
        }
      } else if (notif.type == "pause") {
        const duration = this.gamestate.instant || window.g_archive_mode ? 1 : notif.args.duration;
        this.game.notifqueue.setSynchronousDuration(duration);
      } else if (notif.type == "penny") {
        Object.assign(this.gamedatas.penny, notif.args.penny);
      } else if (notif.type == "player") {
        Object.assign(this.gamedatas.players[notif.args.player.id], notif.args.player);
        if (notif.args.allScore) {
          Object.keys(this.gamedatas.players).forEach((id) => this.game.scoreCtrl[id].setValue(notif.args.allScore));
        } else {
          this.game.scoreCtrl[notif.args.player.id].setValue(notif.args.player.score);
        }
      }
    },

    onErrorCode(code) {
      lastErrorCode = code;
    },

    onChatKeyDown(ev) {
      if (this.keyboardPopup || this.lookupPopup) {
        return true; // suppress BGA chat popup
      }
    },

    onPrefChange(id, value) {
      this.prefs[id] = value;
    },

    /*
     * Other functions
     */

    sortOnce(location, order) {
      const cards = this.cardsInLocation(location);
      if (order == "shuffle") {
        this.shuffleCards(cards);
      } else if (order != null) {
        this.sortCards(order, cards);
      }
      cards.forEach((card, index) => {
        this.gamedatas.cards[card.id].order = index;
      });
      this.previewWord();
    },

    sort(location, order) {
      if (location.startsWith("hand") || location.startsWith("tableau")) {
        this.sortOnce(location, order);
      } else {
        let prefix = location.split("_")[0];
        if (order == "shuffle") {
          order = "order";
          const cards = this.cardsInLocation(location);
          this.shuffleCards(cards);
          cards.forEach((card, index) => {
            this.gamedatas.cards[card.id].order = index;
          });
        }
        this.locationOrder[prefix] = order;
        try {
          localStorage.setItem("hardback.sort." + prefix, order);
        } catch (ignore) { }
      }
    },

    showLookup() {
      let word = '';
      if (this.tableauCards.length > 0) {
        word = this.word(this.tableauCards);
      } else {
        word = this.word(this.handCards);
      }
      this.lookupPopup = { word };
    },

    useInk() {
      this.takeAction("useInk");
    },

    previewWord() {
      if (window.g_tutorialwritten) {
        // skip during tutorials
        return;
      }
      let lock = false;
      let handIds = this.cardIds(this.handCards);
      let handMask = this.wildMask(this.handCards);
      let tableauIds = this.cardIds(this.tableauCards);
      let tableauMask = this.wildMask(this.tableauCards);
      this.takeAction("previewWord", {
        lock,
        handIds,
        handMask,
        tableauIds,
        tableauMask,
      });
    },

    moveAll(fromLocation, toLocation) {
      if (this.gamestate.safeToMove) {
        return Promise.all(
          this.cardsInLocation(fromLocation).map((card) =>
            this.clickCard({
              card: card,
              action: {
                action: "move",
                destination: toLocation == "origin" ? card.origin : toLocation,
              },
            })
          )
        );
      } else {
        return Promise.resolve([]);
      }
    },

    resetAll() {
      this.wildCards.forEach((card) => {
        this.clickFooter({ card: card, action: { action: "reset" } });
      });
    },

    clickTab(location) {
      this.tab = location;
    },

    clickCard(e) {
      let { action, card } = e;
      if (action.action == "move") {
        return this.animateCard(card, {
          location: action.destination,
          order: this.nextOrderInLocation(action.destination),
        }).then(this.previewWord, this.previewWord);
      } else {
        let args = { cardId: card.id };
        if (action.actionArgs) {
          Object.assign(args, action.actionArgs);
        }
        return this.takeAction(action.action, args);
      }
    },

    clickFooter(e) {
      let { action, card } = e;
      if (action.action == "wild") {
        // Show keyboard
        this.keyboardPopup = {
          id: card.id,
          genre: card.genreName,
          letter: card.letter,
        };
      } else if (action.action == "reset") {
        this.gamedatas.cards[card.id].wild = false;
        this.previewWord();
      } else {
        let args = { cardId: card.id };
        if (action.actionArgs) {
          Object.assign(args, action.actionArgs);
        }
        if (action.confirmation) {
          this.game.confirmationDialog(action.confirmation, () => {
            this.takeAction(action.action, args);
          });
        } else {
          this.takeAction(action.action, args);
        }
      }
    },

    clickKey(letter) {
      if (this.keyboardPopup != null) {
        this.gamedatas.cards[this.keyboardPopup.id].wild = letter;
        this.keyboardPopup = null;
        this.previewWord();
      }
    },

    clickLookup(word) {
      if (word) {
        let ignore = word.length == 1 || this.lookupHistory.some((hist) => hist.word == word);
        if (!ignore) {
          this.lookupHistory.unshift({
            icon: "loading",
            word: word,
          });
          if (this.lookupHistory.length > 10) {
            this.lookupHistory.splice(10, this.lookupHistory.length - 10);
          }
          this.takeAction("lookup", { lock: false, word }).catch((error) => {
            for (let i = 0; i < this.lookupHistory.length; i++) {
              let hist = this.lookupHistory[i];
              if (hist.word == word) {
                this.lookupHistory.splice(i, 1);
                break;
              }
            }
          });
        } else {
          // Move the duplicate word to the top
          this.lookupHistory.sort((a, b) => a.word == word ? -1 : b.word == word ? 1 : 0);
        }
      } else {
        this.lookupPopup = null;
      }
    },

    /*
     * Drag and drop
     */

    dragStart(e) {
      if (this.drag) {
        console.warn("dragStart: Another drag is already in progress");
        this.dragStop(e.ev);
      }

      const ev = e.ev;
      const el = e.el;
      const cardId = e.cardId;
      const card = this.gamedatas.cards[cardId];
      const locations = e.locations;
      ev.preventDefault();

      // Compute drop zones
      const drops = new Map();
      for (let location of locations) {
        const locationEl = this.getLocationEl(location);
        const locationRect = getRect(locationEl, true);
        if (!locationRect) {
          console.warn(`dragStart: No rect for location ${location}`);
          continue;
        }

        // Lock the location height
        locationEl.style.minHeight = locationRect.height + "px";

        // If empty, create a drop zone over the parent
        const zoneCards = this.cardsInLocation(location);
        if (zoneCards.length == 0 || location.startsWith("timeless")) {
          const parentRect = getRect(locationEl.parentElement);
          if (parentRect) {
            drops.set(parentRect, { location, order: 0 });
          }
        } else {
          for (const zoneCard of zoneCards) {
            let zoneEl = this.getCardEl(zoneCard);
            let rect = getRect(zoneEl, true);
            if (rect) {
              // Create a drop zone over the card
              let order = zoneCard.order + 0.5;
              if (card.location != zoneCard.location || card.order > zoneCard.order) {
                order -= 1;
              }
              rect.top -= rect.marginTop;
              rect.left -= rect.marginLeft;
              rect.height += rect.marginTop + rect.marginBottom;
              rect.width += rect.marginLeft + rect.marginRight;
              drops.set(rect, { location, order });

              // If first, create a drop zone over the empty space before
              if (zoneEl == zoneEl.parentElement.firstElementChild) {
                const rectFirst = {
                  top: rect.top,
                  left: 0,
                  height: rect.height,
                  width: rect.left,
                };
                drops.set(rectFirst, { location, order: -999 });
              }

              // If last, create a drop zone over the empty space after
              if (zoneEl == zoneEl.parentElement.lastElementChild) {
                const rectLast = {
                  top: rect.top,
                  left: rect.left + rect.width,
                  height: rect.height,
                  width: window.innerWidth - rect.left - rect.width - (rect.left - rect.offsetLeft),
                };
                drops.set(rectLast, { location, order: 999 });
              }
            }
          }
        }
      }
      console.log(`Drag card ${card.id} start location ${card.location}, order ${card.order} (${drops.size} drops)`);
      //this.debugDrops(drops);

      // Get current postion
      const elRect = getRect(el, true);
      const start = { x: ev.pageX, y: ev.pageY };
      const offset = {
        x: start.x - elRect.left + elRect.marginLeft,
        y: start.y - elRect.top + elRect.marginTop,
      };

      // Clone, which will follow the mouse
      const cloneEl = el.cloneNode(true);
      cloneEl.id = "clone";
      cloneEl.style.position = "absolute";
      cloneEl.style.top = elRect.offsetTop - elRect.marginTop + "px";
      cloneEl.style.left = elRect.offsetLeft - elRect.marginLeft + "px";
      cloneEl.style.transition = "none";
      cloneEl.style.zIndex = "999";
      el.parentNode.parentNode.appendChild(cloneEl);

      // Hide the original card
      card.dragging = true;
      el.classList.add("invisible");

      // Start listening
      this.drag = { cardId, cloneEl, drops, locations, offset, start };
      document.addEventListener("pointermove", this.dragMove);
      document.addEventListener("pointercancel", this.dragStop, { once: true });
      document.addEventListener("pointerup", this.dragStop, { once: true });
    },

    /*
    debugDrops(drops) {
      let boxes = document.getElementById("boxes");
      if (boxes) {
        boxes.remove();
      }
      boxes = document.createElement("div");
      boxes.id = "boxes";
      document.body.appendChild(boxes);
      for (const [rect, info] of drops) {
        const box = document.createElement("div");
        box.style.position = "absolute";
        box.style.top = rect.top + "px";
        box.style.left = rect.left + "px";
        box.style.height = rect.height + "px";
        box.style.width = rect.width + "px";
        box.style.border = "1px solid white";
        box.style.color = "white";
        box.style.fontWeight = "bold";
        box.style.wordBreak = "break-word";
        box.innerHTML = info.order + "<br>" + info.location;
        boxes.appendChild(box);
      }
    },
    */

    dragMove(ev) {
      if (!this.drag) {
        console.warn("dragMove: No drag is in progress");
        return;
      }
      ev.preventDefault();

      // Make the clone follow the mouse
      const x = ev.pageX;
      const y = ev.pageY;
      const diffX = x - this.drag.start.x;
      const diffY = y - this.drag.start.y;
      this.drag.cloneEl.style.transform = "translate(" + diffX + "px, " + diffY + "px)";

      // Check if we've hovering over any drop zone
      for (const [rect, info] of this.drag.drops) {
        if (x >= rect.left && x <= rect.left + rect.width && y >= rect.top && y <= rect.top + rect.height) {
          // Update the card if this zone represents a new location/order
          if (this.drag.over != info) {
            this.drag.over = info;
            const card = this.gamedatas.cards[this.drag.cardId];
            card.location = info.location;
            card.order = info.order;
            console.log(`Drag card ${card.id} set location ${card.location}, order ${card.order}`);
          }
          break;
        }
      }
    },

    dragStop(ev) {
      if (!this.drag) {
        console.warn("dragStop: No drag is in progress");
        return;
      }
      ev.preventDefault();
      console.log(`Drag card ${this.drag.cardId} stop (via ${ev.type})`);

      // Stop listening
      document.removeEventListener("pointermove", this.dragMove);
      document.removeEventListener("pointercancel", this.dragStop);
      document.removeEventListener("pointerup", this.dragStop);

      // Destroy the clone
      this.drag.cloneEl.remove();

      // Destroy debug
      /*
      let boxes = document.getElementById("boxes");
      if (boxes) {
        boxes.remove();
      }
      */

      // Unlock the location height
      for (const location of this.drag.locations) {
        const locationEl = this.getLocationEl(location);
        if (locationEl) {
          locationEl.style.minHeight = "";
          locationEl.classList.remove("dragging");
        }
      }

      // Make the card visible
      const card = this.gamedatas.cards[this.drag.cardId];
      card.dragging = false;

      // Reindex the location to fix special values -999, 0.5, 999
      this.sortOnce(card.location);

      // Clear drag state
      this.drag = null;
    },
  },
};
</script>
