<template>
  <div class="select-none adjust-none"
       :class="{ 'no-animation': !this.prefs.animation }">
    <!-- Player panels (moved using teleport) -->
    <HPlayerPanel v-for="(player, id) in players"
                  :key="id"
                  :player="player" />
    <HPenny v-if="gamedatas.penny"
            :penny="gamedatas.penny" />

    <!-- Current word -->
    <teleport to="#pagemaintitle_wrap">
      <div v-if="gamestate.name != 'gameEnd'"
           id="hword">{{ current.word }}
        <span v-if="gamestate.name != 'vote' && gamestate.name != 'uncover' && gamestate.name != 'double'"> &mdash; </span>
        <span v-if="current.word && gamestate.name != 'vote' && gamestate.name != 'uncover' && gamestate.name != 'double'"
              v-html="i18n(current.complete ? 'current' : 'currentSoFar', { coins: current.coins, points: current.points, icon: 'star' })"></span>
      </div>
    </teleport>

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
               :lookupHistory="lookupHistory"
               :lookupPopup="lookupPopup"
               :myself="myself"
               :options="gamedatas.options" />
    </transition>

    <!-- Browser warning (Safari 12, etc.) -->
    <div v-if="warningVisible"
         class="m-4 p-3 text-17 font-bold text-center border-2 border-red-700 bg-white/50">
      <a href="https://browsehappy.com/"
         target="_blank"
         class="block text-blue-700"
         v-text="i18n('browserWarnTitle')"></a>
      <div class="text-15"
           v-text="i18n('browserWarnDesc')"></div>
    </div>

    <!-- Final round reminder -->
    <div v-if="gamedatas.finalRound && !gamedatas.options.coop"
         class="m-4 p-3 text-17 text-center font-bold border-2 border-red-700 bg-white/50"
         v-text="i18n('finalRound')"></div>

    <!-- Final word history -->
    <div v-if="wordHistory"
         class="select-text pagesection">
      <table class="statstable">
        <thead>
          <tr>
            <th @click="historySort = 'id'"
                class="cursor-pointer"
                width="5%">#</th>
            <th v-text="i18n('playerHeader')"
                @click="historySort = 'player.order'"
                class="cursor-pointer"></th>
            <th v-text="i18n('wordHeader')"
                @click="historySort = 'word'"
                class="cursor-pointer"></th>
            <th v-text="i18n('lengthHeader')"
                @click="historySort = '-length'"
                class="cursor-pointer"
                width="10%"></th>
            <th @click="historySort = '-coins'"
                class="cursor-pointer"
                width="10%">¢</th>
            <th @click="historySort = '-score'"
                class="cursor-pointer"
                width="10%">
              <Icon icon="star"
                    class="inline text-18" />
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="hist in wordHistory"
              :key="hist.id"
              :class="hist.player.colorBg20">
            <td>{{ hist.id }}</td>
            <td :class="hist.player.colorText"
                class="font-bold break-all"
                v-text="hist.player.name"></td>
            <td>{{ hist.word }}</td>
            <td>{{ hist.word.length }}</td>
            <td>{{ hist.coins }}¢</td>
            <td class="whitespace-nowrap">{{ hist.score }}-NO-BREAK-
              <Icon icon="star"
                    class="inline text-18" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Myself area -->
    <HPlayerArea v-if="myself"
                 :player="myself" />

    <!-- Timeless Classics -->
    <div v-if="timelessVisible"
         class="px-1 py-3 border-t-2 border-black">
      <div class="flex leading-8">
        <div id="tut_timeless_title"
             class="title text-17 font-bold grow">{{ i18n("timelessLocation") }} ({{ timelessCards.length }})</div>
      </div>
      <HCardList :cards="timelessCards"
                 location="timeless" />
    </div>

    <!-- Offer -->
    <div class="px-1 py-3 border-t-2 border-black">
      <div class="flex leading-8">
        <div id="tut_offer_title"
             class="title text-17 font-bold grow"
             v-text="i18n('offerLocation')"></div>

        <div class="buttongroup grid grid-cols-4">
          <div id="sort_offer_letter"
               @click="sort('offer', 'letter')"
               class="button blue"
               :class="{ active: locationOrder.offer == 'letter' }"
               :title="i18n('sortLetterTip')">A-Z</div>
          <div id="sort_offer_cost"
               @click="sort('offer', 'cost')"
               class="button blue text-15"
               :class="{ active: locationOrder.offer == 'cost' }"
               :title="i18n('sortCostTip')">¢</div>
          <div id="sort_offer_genre"
               @click="sort('offer', 'genre')"
               class="button blue"
               :class="{ active: locationOrder.offer == 'genre' }"
               :title="i18n('sortGenreTip')">
            <Icon icon="starter"
                  class="inline text-17 h-8" />
          </div>
          <div id="sort_offer_time"
               @click="sort('offer', 'order')"
               class="button blue"
               :class="{ active: locationOrder.offer == 'order' }"
               :title="i18n('sortTimeTip')">
            <Icon icon="clock"
                  class="inline text-17 h-8" />
          </div>
        </div>
      </div>
      <HCardList :cards="offerCards"
                 location="offer" />
    </div>

    <!-- Opponent areas -->
    <HPlayerArea v-for="player in opponents"
                 :player="player" />
  </div>
</template>

<script lang="js">
import HCardList from "./HCardList.vue";
import HConstants from "./HConstants.js";
import HKeyboard from "./HKeyboard.vue";
import HLookup from "./HLookup.vue";
import HPenny from "./HPenny.vue";
import HPlayerArea from "./HPlayerArea.vue";
import HPlayerPanel from "./HPlayerPanel.vue";
import { Icon, addIcon } from "@iconify/vue";
import { firstBy } from "thenby";
import { nextTick, computed } from "vue";
import { groupBy, mapValues, remove, throttle } from "lodash-es";

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
const playerRegExp = new RegExp('">(.*)</span>');

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

// Button and tab icons
import backspaceIcon from "@iconify-icons/mdi/backspace";
import bookOpenPageVariant from "@iconify-icons/mdi/book-open-page-variant";
import cardsIcon from "@iconify-icons/mdi/cards";
import chevronDown from "@iconify-icons/mdi/chevron-down";
import clockOutline from "@iconify-icons/mdi/clock-outline";
import flipHorizontal from "@iconify-icons/mdi/flip-horizontal";
import handRight from "@iconify-icons/mdi/hand-right";
import leadPencil from "@iconify-icons/mdi/lead-pencil";
import plusBox from "@iconify-icons/mdi/plus-box";
import shuffleVariant from "@iconify-icons/mdi/shuffle-variant";
import swapVerticalBold from "@iconify-icons/mdi/swap-vertical-bold";

// Lookup word icons
import approximatelyEqualBox from "@iconify-icons/mdi/approximately-equal-box";
import checkboxMarked from "@iconify-icons/mdi/checkbox-marked";
import closeBox from "@iconify-icons/mdi/close-box";
import loadingIcon from "@iconify-icons/mdi/loading";

export default {
  name: "HGame",
  emits: ["clickTab"],
  components: { Icon, HCardList, HKeyboard, HLookup, HPenny, HPlayerArea, HPlayerPanel },

  provide() {
    return {
      cardsInLocation: this.cardsInLocation,
      gamestate: this.gamestate,
      genreCounts: computed(() => this.genreCounts),
      getRect: getRect,
      i18n: this.i18n,
      locationVisible: computed(() => this.locationVisible),
      myself: computed(() => this.myself),
      options: computed(() => this.gamedatas.options),
      prefs: computed(() => this.prefs),
      refs: computed(() => this.gamedatas.refs),
    };
  },

  created() {
    this.previewWord = throttle(this.previewWord, 1000, { leading: false, trailing: true });
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
    this.emitter.on("clickSort", this.clickSort);
    this.emitter.on("dragStart", this.dragStart);
    this.emitter.on("showLookup", this.showLookup);
    this.emitter.on("useInk", this.useInk);
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
    this.emitter.off("clickSort", this.clickSort);
    this.emitter.off("dragStart", this.dragStart);
    this.emitter.off("showLookup", this.showLookup);
    this.emitter.off("useInk", this.useInk);
  },

  data() {
    return {
      gamedatas: {
        cards: {},
        finalRound: false,
        gameLength: 0,
        history: [],
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
      historySort: "-score",
      keyboardPopup: null,
      lookupHistory: [],
      lookupPopup: null,
      prefs: {
        animation: true,
        drag: true,
        tooltip: true,
      },
      locationOrder: {
        discard: "genre",
        draw: "genre",
        offer: "order",
        timeless: "location",
      },
      locationVisible: new Set(['offer']),
      icons: {
        adventure: mdiCompass,
        chevron: chevronDown,
        clock: clockOutline,
        dictionary: bookOpenPageVariant,
        discardLocation: shuffleVariant,
        drawLocation: cardsIcon,
        equal: approximatelyEqualBox,
        handLocation: handRight,
        horror: mdiSkull,
        inkCount: plusBox,
        jail: lockIcon,
        loading: loadingIcon,
        move: swapVerticalBold,
        mystery: magnifyingGlass,
        no: closeBox,
        removerCount: backspaceIcon,
        romance: mdiHeart,
        shuffle: shuffleVariant,
        star: starOutlined,
        starter: bookmarkIcon,
        tableauLocation: leadPencil,
        timeless: cachedIcon,
        uncover: flipHorizontal,
        yes: checkboxMarked,
      },
      icons105: {},
    };
  },

  computed: {
    current() {
      let current = null;
      if (this.gamestate.activeId) {
        current = this.players[this.gamestate.activeId].current;
      }
      return current || {};
    },

    handCards() {
      return this.cardsInLocation(this.myself.handLocation);
    },

    genreCounts() {
      const cards = this.populateCards(
        Object.values(this.gamedatas.cards)
      ).filter((card) => card.playerId != null && !card.origin.startsWith('jail'));
      const groupByOwner = groupBy(cards, (card) => card.playerId);
      return mapValues(groupByOwner, (ownerCards) => {
        const groupByGenre = groupBy(ownerCards, 'genreName');
        return mapValues(groupByGenre, (genreCards, genreName) => {
          const triggeringCards = genreCards.filter((card) => card.triggering);
          const count = genreCards.length;
          const total = ownerCards.length;
          const percent = (count / total) * 100;
          const genre = HConstants.GENRES[genreName];
          return {
            class: `${genre.bg} ${genre.textLight}`,
            count,
            genre: genre.icon,
            percent,
            percentDisplay: percent.toFixed(0),
            total,
            triggering: triggeringCards.length,
          };
        });
      });
    },

    myself() {
      return this.players[this.game.player_id];
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

    opponents() {
      return this.spectator ? this.playersSorted : this.playersSorted.slice(1);
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
      return this.timelessCards.length > 0 || Object.values(this.gamedatas.cards).some((card) => card.origin.startsWith("timeless"));
    },

    warningVisible() {
      return window.PointerEvent === undefined && this.prefs.drag;
    },

    wordHistory() {
      if (this.gamedatas.history && this.gamedatas.history.length > 0) {
        const history = this.gamedatas.history.map(this.populateWordHistory);
        let sorter = null;
        if (this.historySort == "-length") {
          sorter = firstBy((hist) => hist.word.length, "desc");
        } else if (this.historySort == "player.order") {
          sorter = firstBy((hist) => hist.player.order);
        } else if (this.historySort.startsWith("-")) {
          sorter = firstBy(this.historySort.substring(1), "desc");
        } else {
          sorter = firstBy(this.historySort);
        }
        if (this.historySort != "id") {
          sorter = sorter.thenBy("id");
        }
        history.sort(sorter);
        return history;
      }
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
      return document.getElementById("cardlist_" + location);
    },

    getCardEl(card) {
      if (typeof card == "number") {
        card = this.gamedatas.cards[card];
      }
      return document.getElementById("cardholder_" + card.id);
    },

    cardsInLocation(location) {
      if (location == null) {
        return [];
      }
      if (location.startsWith("timeless")) {
        location = "timeless";
      }
      let cards = this.populateCards(
        Object.values(this.gamedatas.cards).filter((card) => card.location.startsWith(location))
      );
      let prefix = location.split("_")[0];
      this.sortCards(this.locationOrder[prefix], cards);
      if (cards.length > 0 && prefix == "offer") {
        // Compute oldest card
        let cardsOrder = cards;
        if (this.locationOrder[prefix] != "order") {
          cardsOrder = cards.slice();
          cardsOrder.sort(firstBy("order"));
        }
        cardsOrder[cardsOrder.length - 1].oldest = true;
      }
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
      const max = cards.map((card) => card.order).reduce((acc, cur) => Math.max(acc, cur), 0);
      return max + 1;
    },

    populateCard(card) {
      let newCard = Object.assign({ factor: 1 }, this.gamedatas.refs.cards[card.refId], card);
      newCard.genreName = HConstants.GENRES[newCard.genre].icon;

      // Owner
      if (newCard.origin.includes("_")) {
        newCard.playerId = newCard.origin.split("_")[1];
        newCard.triggering = !newCard.wild
          && (newCard.location == "tableau_" + newCard.playerId
            || (newCard.location.startsWith("timeless") && newCard.location == newCard.origin));
        if (newCard.location.startsWith("jail") || newCard.origin.startsWith("timeless")) {
          newCard.player = this.players[newCard.playerId];
        }
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
          player.colorBg20 = "bg-red-700/20";
          player.colorBg50 = "bg-red-700/50";
          player.colorBorder = "border-red-700";
          player.colorRing = "ring-red-700";
          player.colorText = "text-red-700";
          player.colorTextDark = "text-red-900";
          player.colorTextLight = "text-red-100";
          break;
        case "green":
          player.colorBg = "bg-green-700";
          player.colorBg20 = "bg-green-700/20";
          player.colorBg50 = "bg-green-700/50";
          player.colorBorder = "border-green-700";
          player.colorRing = "ring-green-700";
          player.colorText = "text-green-700";
          player.colorTextDark = "text-green-900";
          player.colorTextLight = "text-green-100";
          break;
        case "blue":
          player.colorBg = "bg-blue-700";
          player.colorBg20 = "bg-blue-700/20";
          player.colorBg50 = "bg-blue-700/50";
          player.colorBorder = "border-blue-700";
          player.colorRing = "ring-blue-700";
          player.colorText = "text-blue-700";
          player.colorTextDark = "text-blue-900";
          player.colorTextLight = "text-blue-100";
          break;
        case "yellow":
          player.colorBg = "bg-yellow-500";
          player.colorBg20 = "bg-yellow-500/20";
          player.colorBg50 = "bg-yellow-600/50";
          player.colorBorder = "border-yellow-600";
          player.colorRing = "ring-yellow-500";
          player.colorText = "text-yellow-600";
          player.colorTextDark = "text-yellow-900";
          player.colorTextLight = "text-yellow-900";
          break;
        case "purple":
          player.colorBg = "bg-purple-700";
          player.colorBg20 = "bg-purple-700/20";
          player.colorBg50 = "bg-purple-700/50";
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

    populateWordHistory(hist) {
      hist.player = this.players[hist.player_id] || {};
      return hist;
    },

    /*
     * Animation
     */
    async animate(cards) {
      const prep = this.prepareAnimate(cards);
      await this.runAnimate(prep);
      const promises = prep.map((p) => p.promise);
      await Promise.allSettled(promises);
    },

    prepareAnimate(cards) {
      const prep = [];
      for (const card of cards) {
        if (!this.gamestate.instant && this.prefs.animation) {
          const oldCard = this.gamedatas.cards[card.id];
          let cardEl = this.getCardEl(oldCard);
          const jailed = oldCard != null && oldCard.location == "offer" && card.location.startsWith("jail");
          const sameLocation = jailed || (oldCard != null && oldCard.location == card.location);
          if (sameLocation) {
            if (jailed || oldCard.order != card.order) {
              // DELAY -- Vue will animate the card
              prep.push({ mode: 'delay', card });
            }

          } else {
            // Compute start position
            const visible = card.location.startsWith("jail") || card.location.startsWith("timeless") || this.locationVisible.has(card.location);
            let start = null;
            if (oldCard != null) {
              start = getRect(cardEl);
              if (start == null) {
                start = getRect(document.getElementById("tab_" + oldCard.location));
              }
            }

            if (visible && cardEl != null) {
              // MOVE -- card exists and is moving
              prep.push({ mode: 'move', card, start });
            } else if (visible && cardEl == null) {
              // ENTER -- card doesn't exist and is appearing
              prep.push({ mode: 'enter', card, start });
            } else if (!visible && cardEl != null) {
              // EXIT -- card exists and is disapparing
              let end = getRect(document.getElementById("tab_" + card.location));
              if (end == null) {
                end = { top: start.top, left: window.innerWidth }; // to right
              }

              // Vue will destroy the card, so we need a clone for the animation
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
              prep.push({ mode: 'exit', card, start, end, cardEl });
            }
          }
        }
        this.gamedatas.cards[card.id] = card;
      }
      return prep;
    },

    async runAnimate(prep) {
      await repaint();
      await nextTick();
      for (const p of prep) {
        if (p.mode == 'enter' || p.mode == 'move') {
          // Vue just created the card
          p.cardEl = this.getCardEl(p.card);
          p.end = getRect(p.cardEl);
          if (p.start == null) {
            p.start = { top: p.end.top, left: -p.end.width }; // from left
          }
        }
        if (p.mode == 'enter' || p.mode == 'move' || p.mode == 'exit' && (p.start != null && p.end != null && p.cardEl != null)) {
          // FLIP technique: https://aerotwist.com/blog/flip-your-animations/
          // Element is already at the end position
          // Immediately translate it back to the start position
          const diffX = p.start.left - p.end.left;
          const diffY = p.start.top - p.end.top;
          p.cardEl.style.transition = "none";
          p.cardEl.style.transform = "translate(" + diffX + "px, " + diffY + "px)";
        }
      }
      await repaint();
      for (const p of prep) {
        if (p.mode == 'delay') {
          p.promise = sleep(500);
        } else if (p.mode == 'enter' || p.mode == 'move' || p.mode == 'exit') {
          // Run CSS transition in "reverse"
          p.cardEl.style.transition = "";
          p.cardEl.style.transform = "";
          p.promise = sleep(500);
          if (p.mode == 'exit') {
            p.promise = p.promise.then(() => { p.cardEl.remove(); });
          }
        }
      }
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
        console.log(`👆 Take action ${action}`, data);
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
                `<a target="hdefine" href="https://www.collinsdictionary.com/dictionary/english/${q}">Collins</a>`,
                `<a target="hdefine" href="https://www.google.com/search?q=define+${q}">Google</a>`,
              ],
              [
                `<a target="hdefine" href="https://www.dictionary.com/browse/${q}">Dictionary.com</a>`,
                `<a target="hdefine" href="https://www.merriam-webster.com/dictionary/${q}">Merriam-Webster</a>`,
              ],
            ];
          } else if (this.gamedatas.options.dictionary.langId == HConstants.LANG_DE) {
            links = [
              [
                `<a target="hdefine" href="https://www.duden.de/suchen/dudenonline/${q}">Duden</a>`,
              ],
            ];
          } else if (this.gamedatas.options.dictionary.langId == HConstants.LANG_FR) {
            links = [
              [
                `<a target="hdefine" href="https://www.cnrtl.fr/definition/academie9/${q}">Académie Française</a>`,
                `<a target="hdefine" href="https://dictionnaire.lerobert.com/definition/${q}">Le Robert</a>`,
              ],
              [
                `<a target="hdefine" href="https://www.larousse.fr/dictionnaires/francais/${q}">Larousse</a>`,
                `<a target="hdefine" href="https://www.cnrtl.fr/definition/${q}">Trésor</a>`,
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
          } else if (k == "you") {
            const matches = playerRegExp.exec(v);
            if (matches) {
              v = matches[1];
              v = `<!--PNS--><span class="playername ${this.myself.colorName}">${v}</span><!--PNE-->`;
            }
          } else if (k == "actplayer" || k == "otherplayer" || k.startsWith("player_name")) {
            const matches = playerRegExp.exec(v);
            if (matches) {
              v = matches[1];
            }
            for (const id in this.players) {
              const player = this.players[id];
              if (v == player.name) {
                v = `<!--PNS--><span class="playername ${player.colorName}">${v}</span><!--PNE-->`;
                break;
              }
            }
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
      this.gamestate.safeToMove = stateName != "gameEnd" && (!this.gamestate.active || stateName == "playerTurn");
      let activeId = this.game.getActivePlayerId();
      if (stateName == "vote") {
        activeId = args.player_id;
        this.gamestate.safeToMove = activeId != this.game.player_id;
      }
      if (activeId) {
        this.gamestate.activeId = activeId;
      }
      console.log(`▶️ State ${stateName}`, args);

      if (this.gamestate.active) {
        const actionRef = {
          confirmWord: {
            text: "confirmButton",
            color: "blue",
            async function() {
              if (this.tableauCards.length == 0) {
                // Did you forget to play any cards?
                await Promise.allSettled(
                  this.handCards.map((card) => this.clickCard({ card, action: { action: "move", destination: this.myself.tableauLocation } }))
                );
              }
              let tableauIds = this.cardIds(this.tableauCards);
              let tableauMask = this.wildMask(this.tableauCards);
              this.takeAction("confirmWord", { tableauIds, tableauMask });
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

        let possible = this.gamestate.possibleactions;
        possible.forEach((p, index) => {
          const action = actionRef[p];
          const visible = action != null && (action.condition == null || action.condition.apply(this));
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
      }

      if (stateName == "playerTurn" && args && args._private && args._private.replayFrom && !window.g_archive_mode && !window.g_replayFrom) {
        this.game.addActionButton(
          "action_replayFrom",
          this.i18n("replayFrom"),
          () => {
            this.takeAction("deletePreviewNotifications").then(() => {
              location.href = "/" + this.game.gameserver + "/" + this.game.game_name
                + "?table=" + this.game.table_id
                + (this.game.forceTestUser ? "&testuser=" + this.game.forceTestUser : "")
                + "&replayFrom=" + args._private.replayFrom;
            });
          },
          null,
          false,
          "gray"
        );
      }
    },

    onEnteringState(stateName, args) {
      if (args && args.updateGameProgression) {
        this.gamedatas.finalRound = this.gamestate.name != "gameEnd" && args.updateGameProgression >= 100;
      }
      if (this.gamestate.active && this.gamestate.name == "trashDiscard" && this.gamestate.args && !this.gamestate.args.skip) {
        this.emitter.emit("clickTab", "discard");
      }
      if (this.gamestate.active && this.gamestate.name == "specialRomance") {
        this.emitter.emit("clickTab", "hand");
      }
      if (this.gamestate.name == "playerTurn" && !this.gamestate.instant && window.g_replayFrom) {
        // Scroll to active player during replay
        const areaEl = document.getElementById("area_" + this.gamestate.activeId);
        if (areaEl != null) {
          areaEl.scrollIntoView({ behavior: "smooth", block: "center" });
        }
      }
    },

    onLeavingState(stateName) {
      if (this.gamestate.active && this.gamestate.name == "trashDiscard" && this.gamestate.args && !this.gamestate.args.skip) {
        this.emitter.emit("clickTab", "hand");
      }
    },

    onNotify(notif) {
      console.log(`💬 Notify ${notif.type}`, notif.args);
      if (notif.type == "cards" || notif.type == "cardsPreview") {
        if (notif.args.ignorePlayerId && notif.args.ignorePlayerId == this.game.player_id && !window.g_replayFrom) {
          this.game.notifqueue.setSynchronousDuration(1);
          return;
        }
        let cards = Object.values(notif.args.cards);
        cards.sort(firstBy("location").thenBy("order").thenBy("id"));
        this.animate(cards).then(() => {
          this.game.notifqueue.setSynchronousDuration(1);
        });

      } else if (notif.type == "history") {
        this.gamedatas.history = notif.args.history;

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
            hist.icon = notif.args.valid ? "yes" : notif.args.history ? "equal" : "no";
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
        if (this.gamedatas.options.coop) {
          Object.keys(this.gamedatas.players).forEach((id) => this.game.scoreCtrl[id].setValue(notif.args.player.score));
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
      if (id == HConstants.PREF_ANIMATION) {
        this.prefs.animation = value == HConstants.ANIMATION_ENABLED;
      } else if (id == HConstants.PREF_DRAG) {
        this.prefs.drag = value == HConstants.DRAG_ENABLED;
      } else if (id == HConstants.PREF_TOOLTIP) {
        this.prefs.tooltip = value == HConstants.TOOLTIP_ENABLED;
      }
    },

    onSetup() {
      console.log('🐣 Setup');
      const threedEl = document.getElementById('ingame_menu_3d');
      if (threedEl != null) {
        threedEl.style.display = 'none';
      }

      // Final score in player panels
      Object.keys(this.gamedatas.players).forEach((id) => {
        const totalEl = document.createElement('span');
        totalEl.textContent = ' / ' + this.gamedatas.gameLength;
        const scoreEl = document.getElementById('player_score_' + id);
        scoreEl.insertAdjacentElement('afterend', totalEl);
      });

      // ELO in player panels
      document.querySelectorAll(".player_elo_wrap").forEach((node) => {
        if (node.firstChild.nodeType == 3) {
          node.firstChild.remove();
        }
      });
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

    showLookup(e) {
      let { word } = e;
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

    clickCard(e) {
      let { action, card } = e;
      if (action.action == "move") {
        const newCard = Object.assign({}, card, {
          location: action.destination,
          order: this.nextOrderInLocation(action.destination),
        });
        return this.animate([newCard]).then(this.previewWord);
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
        let ignore = word.length == 1;
        if (!ignore) {
          remove(this.lookupHistory, (w) => w.word == word);
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

    clickSort(e) {
      let { location, order } = e;
      if (location.startsWith("hand") || location.startsWith("tableau")) {
        // the sort order is temporary
        this.sortOnce(location, order);
      } else {
        // the sort order is persistent
        if (order == "shuffle") {
          order = "order";
          const cards = this.cardsInLocation(location);
          this.shuffleCards(cards);
          cards.forEach((card, index) => {
            this.gamedatas.cards[card.id].order = index;
          });
        }
        let prefix = location.split("_")[0];
        this.locationOrder[prefix] = order;
        try {
          localStorage.setItem("hardback.sort." + prefix, order);
        } catch (ignore) { }
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
