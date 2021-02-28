<template>
  <div class="select-none">
    <!-- Player panels (moved using teleport) -->
    <HPlayerPanel v-for="(player, id) in players" :key="id" :player="player" />
    <HPenny v-if="gamedatas.penny" :penny="gamedatas.penny" />

    <!-- Log message icons -->
    <div class="hidden">
      <Icon v-for="icon in logIcons" :key="icon" :id="'hicon_' + icon" :icon="icon" class="hicon" />
      <Icon icon="star" id="benefit_star" class="inline text-17" />
      <Icon icon="adventure" id="benefit_adventure" class="inline text-17" />
    </div>

    <!-- Keyboard popup -->
    <transition name="fade">
      <HKeyboard v-if="keyboardId" />
    </transition>

    <!-- Browser warning (Safari 12, etc.) -->
    <div v-if="warningVisible" class="m-4 p-3 text-18 font-bold text-center border-2 border-red-700 bg-white bg-opacity-50">
      <a href="https://browsehappy.com/" target="_blank" class="block text-blue-700" v-text="i18n('browserWarnTitle')"></a>
      <div class="text-14" v-text="i18n('browserWarnDesc')"></div>
    </div>

    <!-- Final round reminder -->
    <div v-if="gamedatas.finalRound && !gamedatas.options.coop" class="m-4 p-3 text-18 text-center font-bold border-2 border-red-700 bg-white bg-opacity-50" v-text="i18n('finalRound')"></div>

    <!-- Discard -->
    <div v-if="!spectator" class="p-2 border-t-2 border-black bg-black bg-opacity-25">
      <div class="flex leading-7 font-bold">
        <div @click="discardVisible = !discardVisible" class="title flex-grow cursor-pointer"><Icon icon="chevron" class="chevron float-left h-7 text-24" :class="{ collapsed: !discardVisible }" /> <span v-text="i18n('myDiscard', { count: discardCards.length })"></span></div>

        <div v-if="discardVisible" class="buttongroup grid grid-cols-3">
          <div @click="sort(discardLocation, 'letter')" class="button" :class="discardCards.length ? 'blue' : 'disabled'" :title="i18n('sortLetterTip')">A-Z</div>
          <div @click="sort(discardLocation, 'cost')" class="button text-15" :class="discardCards.length ? 'blue' : 'disabled'" :title="i18n('sortCostTip')">¢</div>
          <div @click="sort(discardLocation, 'genre')" class="button" :class="discardCards.length ? 'blue' : 'disabled'" :title="i18n('sortGenreTip')"><Icon icon="starter" class="inline text-17 h-7" /></div>
        </div>
      </div>

      <HCardList v-if="discardVisible" :cards="discardCards" :location="discardLocation" />
    </div>

    <!-- Hand -->
    <div v-if="!spectator && gamestate.name != 'gameEnd'" class="p-2 border-t-2 border-black">
      <div class="flex leading-7 font-bold">
        <div class="title flex-grow" v-text="i18n('myHand', { count: handCards.length })"></div>

        <div class="buttongroup flex">
          <div @click="buttonEnabled['useInk'] && takeAction('useInk')" class="button" :class="buttonEnabled['useInk'] ? 'blue' : 'disabled'" v-text="i18n('draw', { count: myself.ink })"></div>
          <div @click="buttonEnabled['resetAll'] && resetAll(handLocation)" class="button" :class="buttonEnabled['resetAll'] ? 'blue' : 'disabled'" v-text="i18n('resetAll')"></div>
          <div @click="buttonEnabled['moveAll'] && moveAll(handLocation)" class="button" :class="buttonEnabled['moveAll'] ? 'blue' : 'disabled'" v-text="i18n('moveAll')"></div>
        </div>

        <div class="buttongroup grid grid-cols-3">
          <div @click="sortOnce(handLocation, 'letter')" class="button" :class="handCards.length ? 'blue' : 'disabled'" :title="i18n('sortLetterTip')">A-Z</div>
          <div @click="sortOnce(handLocation, 'genre')" class="button" :class="handCards.length ? 'blue' : 'disabled'" :title="i18n('sortGenreTip')"><Icon icon="starter" class="inline text-17 h-7" /></div>
          <div @click="sortOnce(handLocation, 'shuffle')" class="button" :class="handCards.length ? 'blue' : 'disabled'" :title="i18n('shuffleTip')"><Icon icon="shuffle" class="inline text-17 h-7" /></div>
        </div>
      </div>

      <HCardList :cards="handCards" :location="handLocation" :ref="handLocation" />
    </div>

    <!-- Tableau -->
    <div v-if="gamestate.name != 'gameEnd'" class="p-2 border-t-2 border-black bg-opacity-30 transition-colors duration-1000" :class="author.colorBg || 'bg-white'">
      <div class="flex leading-7 font-bold">
        <div class="title flex-grow" v-html="i18n('tableau', { player_name: '<span class=\'transition-colors duration-1000 ' + (author.colorText || 'text-black') + '\'>' + author.name + '</span>', count: tableauCards.length })"></div>

        <div v-if="buttonEnabled['moveAllTableau']" class="buttongroup flex">
          <div @click="buttonEnabled['resetAllTableau'] && resetAll('tableau')" class="button" :class="buttonEnabled['resetAllTableau'] ? 'blue' : 'disabled'" v-text="i18n('resetAll')"></div>
          <div @click="buttonEnabled['moveAllTableau'] && moveAll('tableau')" class="button" :class="buttonEnabled['moveAllTableau'] ? 'blue' : 'disabled'" v-text="i18n('moveAll')"></div>
        </div>

        <div v-if="buttonEnabled['moveAllTableau']" class="buttongroup grid grid-cols-3">
          <div @click="sortOnce('tableau', 'letter')" class="button" :class="buttonEnabled['moveAllTableau'] ? 'blue' : 'disabled'" :title="i18n('sortLetterTip')">A-Z</div>
          <div @click="sortOnce('tableau', 'genre')" class="button" :class="buttonEnabled['moveAllTableau'] ? 'blue' : 'disabled'" :title="i18n('sortGenreTip')"><Icon icon="starter" class="inline text-17 h-7" /></div>
          <div @click="sortOnce('tableau', 'shuffle')" class="button" :class="buttonEnabled['moveAllTableau'] ? 'blue' : 'disabled'" :title="i18n('shuffleTip')"><Icon icon="shuffle" class="inline text-17 h-7" /></div>
        </div>
      </div>

      <HCardList :cards="tableauCards" location="tableau" ref="tableau" />
    </div>

    <!-- Timeless Classics -->
    <div v-if="timelessVisible" class="p-2 border-t-2 border-black">
      <div class="flex leading-7 font-bold">
        <div class="title flex-grow" v-text="i18n('timeless', { count: timelessCards.length })"></div>
      </div>

      <HCardList :cards="timelessCards" location="timeless" ref="timeless" />
    </div>

    <!-- Offer -->
    <div class="p-2 border-t-2 border-black">
      <div class="flex leading-7 font-bold">
        <div class="title flex-grow" v-text="i18n('offer', { count: offerCards.length })"></div>

        <div class="buttongroup grid grid-cols-4">
          <div @click="sort('offer', 'letter')" class="button blue" :class="{ active: locationOrder.offer == 'letter' }" :title="i18n('sortLetterTip')">A-Z</div>
          <div @click="sort('offer', 'cost')" class="button blue text-15" :class="{ active: locationOrder.offer == 'cost' }" :title="i18n('sortCostTip')">¢</div>
          <div @click="sort('offer', 'genre')" class="button blue" :class="{ active: locationOrder.offer == 'genre' }" :title="i18n('sortGenreTip')"><Icon icon="starter" class="inline text-17 h-7" /></div>
          <div @click="sort('offer', 'order')" class="button blue" :class="{ active: locationOrder.offer == 'order' }" :title="i18n('sortTimeTip')"><Icon icon="clock" class="inline text-17 h-7" /></div>
        </div>
      </div>

      <HCardList :cards="offerCards" location="offer" />
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import HCardList from "./HCardList.vue";
import HKeyboard from "./HKeyboard.vue";
import HPenny from "./HPenny.vue";
import HPlayerPanel from "./HPlayerPanel.vue";
import { Icon, addIcon } from "@iconify/vue";
import { firstBy } from "thenby";
import { nextTick, computed } from "vue";
import { debounce } from "lodash-es";

const sleep = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));
const repaint = () => new Promise((resolve) => requestAnimationFrame(resolve));
const getRect = (el: HTMLElement, calculateMargin = false) => {
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
const getHtml = (id: string) => {
  const el = document.getElementById(id);
  if (el) {
    return el.outerHTML.replace(/ id=.*? /, " ");
  }
};
const transitionEnd = (el: HTMLElement): Promise<void> => {
  return new Promise((resolve, reject) => {
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
addIcon("starter", bookmarkIcon);

import mdiCompass from "@iconify-icons/mdi/compass";
addIcon("adventure", mdiCompass);

import mdiSkull from "@iconify-icons/mdi/skull";
addIcon("horror", mdiSkull);

import magnifyingGlass from "@iconify-icons/foundation/magnifying-glass";
addIcon("mystery", magnifyingGlass);

import mdiHeart from "@iconify-icons/mdi/heart";
addIcon("romance", mdiHeart);

// Card icons
import starOutlined from "@iconify-icons/ant-design/star-outlined";
addIcon("star", starOutlined);

import lockIcon from "@iconify-icons/mdi/lock";
addIcon("jail", lockIcon);

import cachedIcon from "@iconify-icons/mdi/cached";
addIcon("timeless", cachedIcon);

// Sorter icons
import shuffleVariant from "@iconify-icons/mdi/shuffle-variant";
addIcon("shuffle", shuffleVariant);

import clockOutline from "@iconify-icons/mdi/clock-outline";
addIcon("clock", clockOutline);

import chevronDown from "@iconify-icons/mdi/chevron-down";
addIcon("chevron", chevronDown);

export default {
  name: "HGame",
  components: { Icon, HCardList, HKeyboard, HPenny, HPlayerPanel },

  provide() {
    return {
      gamestate: this.gamestate,
      getHtml: getHtml,
      i18n: this.i18n,
      options: computed(() => this.gamedatas.options),
      refs: computed(() => this.gamedatas.refs),
    };
  },

  created() {
    this.previewWord = debounce(this.previewWord, 1000, { maxWait: 1000 });
  },

  mounted() {
    this.emitter.on("clickCard", this.clickCard);
    this.emitter.on("clickFooter", this.clickFooter);
    this.emitter.on("clickKey", this.clickKey);
    this.emitter.on("dragStart", this.dragStart);
    this.discardVisible = this.gamestate.name == "gameEnd";
    this.locationOrder[this.discardLocation] = "genre";
    this.locationOrder.offer = "order";
    try {
      var x = localStorage.getItem("hardback.sort.offer");
      if (x) {
        this.locationOrder.offer = x;
      }
    } catch (ignore) {}
  },

  beforeUnmount() {
    this.emitter.off("clickCard", this.clickCard);
    this.emitter.off("clickFooter", this.clickFooter);
    this.emitter.off("clickKey", this.clickKey);
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
      },
      gamestate: {},
      drag: null,
      discardVisible: false,
      warningVisible: window.PointerEvent === undefined,
      keyboardId: null,
      locationOrder: {
        timeless: "location",
      },
      logIcons: ["starter", "adventure", "horror", "mystery", "romance", "star"],
    };
  },

  computed: {
    spectator() {
      return this.game.isSpectator;
    },

    players() {
      return this.populatePlayers(this.gamedatas.players) || {};
    },

    myself() {
      return this.players[this.game.player_id] || {};
    },

    author() {
      return this.players[this.gamestate.activeId] || {};
    },

    handLocation() {
      return "hand_" + this.game.player_id;
    },

    handCards() {
      return this.cardsInLocation(this.handLocation);
    },

    handWildCards() {
      return this.handCards.filter((card) => card.wild);
    },

    discardLocation() {
      return "discard_" + this.game.player_id;
    },

    discardCards() {
      return this.cardsInLocation(this.discardLocation);
    },

    tableauCards() {
      return this.cardsInLocation("tableau");
    },

    tableauWildCards() {
      return this.tableauCards.filter((card) => card.wild);
    },

    timelessCards() {
      return this.cardsInLocation("timeless");
    },

    timelessVisible() {
      return this.timelessCards.length > 0 || this.tableauCards.some((card) => card.origin.startsWith("timeless"));
    },

    offerCards() {
      let cards = this.cardsInLocation("offer");
      let jail = this.cardsInLocation("jail");
      Array.prototype.push.apply(cards, jail);
      return cards;
    },

    buttonEnabled() {
      return {
        useInk: this.myself.ink && (this.myself.deckCount || this.myself.discardCount) && (!this.gamestate.active || this.gamestate.name == "playerTurn"),
        moveAll: this.gamestate.active && this.gamestate.name == "playerTurn" && this.handCards.length > 1,
        resetAll: this.handWildCards.length,
        moveAllTableau: this.gamestate.active && this.gamestate.name == "playerTurn" && this.tableauCards.length,
        resetAllTableau: this.gamestate.active && this.gamestate.name == "playerTurn" && this.tableauWildCards.length,
      };
    },
  },

  methods: {
    /*
     * Utility functions
     */

    i18n(msg: string, args): string {
      if (!msg) {
        return "";
      }
      if (this.gamedatas.refs.i18n[msg]) {
        msg = this.gamedatas.refs.i18n[msg];
      }
      // @ts-ignore
      msg = window._(msg);
      if (args) {
        msg = this.game.format_string_recursive(msg, args);
      }
      return msg;
    },

    getLocationEl(location: string): HTMLElement {
      if (location.startsWith("timeless")) {
        location = "timeless";
      }
      const component = this.$refs[location];
      if (component) {
        return component.$refs.cardlist;
      }
    },

    getCardEl(card): HTMLElement {
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

    cardsInLocation(location: string): any[] {
      if (location.startsWith("timeless")) {
        location = "timeless";
      }
      let cards = this.populateCards(
        Object.values(this.gamedatas.cards).filter((card: any) => {
          return card.location.startsWith(location);
        })
      );
      this.sortCards(this.locationOrder[location], cards);
      return cards;
    },

    sortCards(order: string, cards: any[]): void {
      order = order || "order";
      let sorter = firstBy(order);
      if (order != "letter") {
        // @ts-ignore
        sorter = sorter.thenBy("letter");
      }
      // @ts-ignore
      sorter = sorter.thenBy("id");
      cards.sort(sorter);
    },

    shuffleCards(cards: any[]): void {
      for (let i = cards.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [cards[i], cards[j]] = [cards[j], cards[i]];
      }
    },

    nextOrderInLocation(location: string): number {
      const cards = this.cardsInLocation(location);
      const max: number = cards.map((card) => card.order).reduce((acc: number, cur: number) => Math.max(acc, cur), -1);
      return max + 1;
    },

    populateCard(card) {
      const star = getHtml("benefit_star");
      const adventure = getHtml("benefit_adventure");
      let newCard = Object.assign({ factor: 1 }, this.gamedatas.refs.cards[card.refId], card);

      // Basic benefits
      newCard.basicBenefitsList = [];
      for (const id in newCard.basicBenefits) {
        let value = newCard.basicBenefits[id];
        if (newCard.factor > 1) {
          value = `<span class="font-bold px-1 bg-yellow-400">${value * newCard.factor}</span>`;
        }
        let newBenefit = {
          id: parseInt(id),
          html: this.i18n(this.gamedatas.refs.benefits[id], { value, star, adventure }),
        };
        newCard.basicBenefitsList.push(newBenefit);
      }
      newCard.basicBenefitsList.sort(firstBy("id"));

      // Genre benefits
      newCard.genreBenefitsList = [];
      if (newCard.genreBenefits) {
        for (const id in newCard.genreBenefits) {
          let value = newCard.genreBenefits[id];
          if (newCard.factor > 1) {
            value = `<span class="font-bold px-1 bg-yellow-400">${value * newCard.factor}</span>`;
          }
          let newBenefit = {
            id: parseInt(id),
            html: this.i18n(this.gamedatas.refs.benefits[id], { value, star, adventure }),
          };
          newCard.genreBenefitsList.push(newBenefit);
        }
        newCard.genreBenefitsList.sort(firstBy("id"));
      }

      // Genre name
      if (newCard.genre == Constants.ADVENTURE) {
        newCard.genreName = "adventure";
      } else if (newCard.genre == Constants.HORROR) {
        newCard.genreName = "horror";
      } else if (newCard.genre == Constants.ROMANCE) {
        newCard.genreName = "romance";
      } else if (newCard.genre == Constants.MYSTERY) {
        newCard.genreName = "mystery";
      } else if (newCard.genre == Constants.STARTER) {
        newCard.genreName = "starter";
      }

      // Owning player
      if (newCard.location == "jail") {
        newCard.player = this.players[newCard.order];
      } else if (newCard.origin.startsWith("timeless")) {
        const playerId = newCard.origin.split("_")[1];
        newCard.player = this.players[playerId];
      }

      return newCard;
    },

    populateCards(cards): any[] {
      if (!Array.isArray(cards)) {
        return null;
      }
      return cards.map(this.populateCard);
    },

    cardIds(cards): number[] {
      if (!Array.isArray(cards)) {
        return null;
      }
      return cards.map((card) => card.id);
    },

    wildMask(cards): string {
      return cards.map((card) => card.wild || "_").join("");
    },

    populatePlayer(player) {
      switch (player.colorName) {
        case "red":
          player.colorRing = "ring-red-700";
          player.colorBg = "bg-red-700";
          player.colorTextLight = "text-red-100";
          player.colorText = "text-red-700";
          break;
        case "green":
          player.colorRing = "ring-green-700";
          player.colorBg = "bg-green-700";
          player.colorTextLight = "text-green-100";
          player.colorText = "text-green-700";
          break;
        case "blue":
          player.colorRing = "ring-blue-700";
          player.colorBg = "bg-blue-700";
          player.colorTextLight = "text-blue-100";
          player.colorText = "text-blue-700";
          break;
        case "yellow":
          player.colorRing = "ring-yellow-500";
          player.colorBg = "bg-yellow-500";
          player.colorTextLight = "text-yellow-900";
          player.colorText = "text-yellow-700";
          break;
        case "purple":
          player.colorRing = "ring-purple-700";
          player.colorBg = "bg-purple-700";
          player.colorTextLight = "text-purple-100";
          player.colorText = "text-purple-700";
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
    async animateCard(card, changes): Promise<void> {
      if (changes) {
        card = Object.assign({}, card, changes);
      }

      if (this.gamestate.instant) {
        // Instant replay, no animation
        this.gamedatas.cards[card.id] = card;
        return;
      }

      const oldCard = this.gamedatas.cards[card.id];
      if (oldCard != null && (oldCard.location == card.location || (oldCard.location == "offer" && card.location == "jail"))) {
        // Same location, no animation
        this.gamedatas.cards[card.id] = card;
        // Delay for Vue to animate the reorder
        if (oldCard.order != card.order || (oldCard.location == "offer" && card.location == "jail")) {
          await sleep(600);
        }
        return;
      }

      // Compute start position
      let cardEl: HTMLElement = null;
      let start = null;
      let end = null;
      if (oldCard != null) {
        cardEl = this.getCardEl(oldCard);
        start = getRect(cardEl);
      }

      let visible = card.location == this.handLocation || card.location == "tableau" || card.location == "offer" || card.location == "jail" || (this.discardVisible && card.location == this.discardLocation) || card.location.startsWith("timeless");
      console.log(`Animate card ${card.id} new location ${card.location} is visible? ${visible}`);
      if (!start && !visible) {
        // Invisible card movement, no animation
        this.gamedatas.cards[card.id] = card;
        return;
      }

      let mode = null;
      if (start && !visible) {
        mode = "leave";
        if (card.location.startsWith("discard_") || card.location == "deck_" + this.myself.id) {
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
        cardEl = cardEl.cloneNode(true) as HTMLElement;
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

    async animateFlip(el: HTMLElement, start, end): Promise<void> {
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
    takeAction(action: string, data): Promise<any> {
      data = data || {};
      if (data.lock === false) {
        delete data.lock;
      } else {
        data.lock = true;
      }
      for (const key in data) {
        if (Array.isArray(data[key])) {
          data[key] = data[key].join(",");
        }
      }
      console.log(`Take action ${action}`, data);
      return new Promise((resolve, reject) => {
        this.game.ajaxcall("/hardback/hardback/" + action + ".html", data, this, resolve, (error) => {
          error ? reject(error) : resolve(error);
        });
      });
    },

    /*
     * BGA framework event callbacks
     */
    onFormatString(log: string, args): string {
      if (args) {
        if (args.award) {
          args.award = `<div class="haward length${args.award}"></div>`;
        }
        if (args.word) {
          const q = args.word.toLowerCase();
          const links = [
            `<a target="hdefine" href="https://dictionary.cambridge.org/search/english/direct/?q=${q}">Cambridge</a>`, //
            `<a target="hdefine" href="https://www.collinsdictionary.com/dictionary/english/${q}">Collins</a>`, //
            `<a target="hdefine" href="https://www.dictionary.com/browse/${q}">Dictionary.com</a>`, //
            `<a target="hdefine" href="https://www.merriam-webster.com/dictionary/${q}">Merriam-Webster</a>`, //
            `<a target="hdefine" href="https://www.lexico.com/en/definition/${q}">Oxford Lexico</a>`, //
            `<a target="hdefine" href="https://en.wiktionary.org/wiki/${q}">Wiktionary</a>`, //
          ];
          args.word = `<b>${args.word}</b><div class="hdefine">${this.i18n("dictionary")}<ul><li>${links.join("</li><li>")}</li></ul></div>`;
        }
        if (args.invalid) {
          args.invalid = `<b>${args.invalid}</b>`;
        }
        if (args.genre) {
          const html = getHtml("hicon_" + args.genre.toLowerCase().trim());
          args.genre = `<span class="hgenre ${args.genre}">${html ? html : args.genre}`;
        }
        if (args.letter) {
          args.letter = `${args.letter}</span>`;
        }
        for (const k in args) {
          if (k.startsWith("icon")) {
            let v = args[k];
            const html = getHtml("hicon_" + v.toLowerCase().trim());
            if (html) {
              v = html;
            } else if (v != "¢") {
              v = ` ${this.i18n(v)}`;
            }
            args[k] = v;
          }
        }
      }
      return log;
    },

    onUpdateActionButtons(stateName: string, args): void {
      Object.assign(this.gamestate, this.game.gamedatas.gamestate, {
        active: this.game.isCurrentPlayerActive(),
        instant: this.game.instantaneousMode,
      });
      const activeId = this.game.getActivePlayerId();
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
              await this.moveAll(this.handLocation);
            }
            let cardIds = this.cardIds(this.tableauCards);
            let wildMask = this.wildMask(this.tableauCards);
            this.takeAction("confirmWord", { cardIds, wildMask });
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
        skip: {
          text() {
            return this.gamestate.name == "purchase" ? "skipPurchaseButton" : "skipButton";
          },
          color: this.gamestate.name == "purchase" ? "blue" : "gray",
          function() {
            this.takeAction("skip");
          },
        },
        discardInk: {
          text: "discardInkButton",
          color: "blue",
          function() {
            this.takeAction("discardInk");
          },
        },
        discardRemover: {
          text: "discardRemoverButton",
          color: "blue",
          function() {
            this.takeAction("discardRemover");
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
            return this.gamestate.args && this.gamestate.args.advert && this.myself.coins >= this.gamestate.args.advert.coins;
          },
        },
        convert: {
          text: "convertButton",
          color: "red",
          function() {
            this.takeAction("convert");
          },
          condition() {
            return this.myself.ink >= 3;
          },
        },
      };

      // No actions for inactive players
      if (!this.gamestate.active) {
        return;
      }

      let possible: string[] = this.gamestate.possibleactions;
      possible.forEach((p, index) => {
        const action = actionRef[p];
        let visible: boolean = action != null && (action.condition == null || action.condition.apply(this));
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

    onEnteringState(stateName: string, args): void {
      if (args && args.updateGameProgression) {
        this.gamedatas.finalRound = args.updateGameProgression >= 100;
      }
      if (this.gamestate.name == "gameEnd" || (this.gamestate.active && this.gamestate.name == "trashDiscard" && this.gamestate.args && !this.gamestate.args.skip)) {
        this.discardVisible = true;
      }
    },

    onLeavingState(stateName: string): void {
      if (this.gamestate.active && this.gamestate.name == "trashDiscard" && this.gamestate.args && !this.gamestate.args.skip) {
        this.discardVisible = false;
      }
    },

    onNotify(notif): void {
      console.log(`Notify ${notif.type}`, notif.args);
      if (notif.type == "cards" || notif.type == "cardsPreview") {
        if (notif.args.ignorePlayerId && notif.args.ignorePlayerId == this.game.player_id) {
          this.game.notifqueue.setSynchronousDuration(1);
          return;
        }
        let cards = Object.values(notif.args.cards);
        // @ts-ignore
        cards.sort(firstBy("location").thenBy("order").thenBy("id"));
        Promise.all(cards.map(this.animateCard)).then(() => {
          this.game.notifqueue.setSynchronousDuration(1);
        });
      } else if (notif.type == "invalid") {
        if (!this.gamestate.instant && this.game.player_id == notif.args.player_id) {
          this.game.showMessage(this.i18n("invalid", notif.args), "error");
        }
      } else if (notif.type == "pause") {
        const duration = this.gamestate.instant ? 1 : notif.args.duration;
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

    /*
     * Other functions
     */

    sortOnce(location: string, order: string): void {
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

    sort(location: string, order: string): void {
      if (order == "shuffle") {
        order = "order";
        const cards = this.cardsInLocation(location);
        this.shuffleCards(cards);
        cards.forEach((card, index) => {
          this.gamedatas.cards[card.id].order = index;
        });
      }
      this.locationOrder[location] = order;
      try {
        localStorage.setItem("hardback.sort." + location, order);
      } catch (ignore) {}
    },

    previewWord(): void {
      let lock = false;
      let handIds = this.cardIds(this.handCards);
      let handMask = this.wildMask(this.handCards);
      let tableauIds = null;
      let tableauMask = null;
      if (this.gamestate.active) {
        tableauIds = this.cardIds(this.tableauCards);
        tableauMask = this.wildMask(this.tableauCards);
      }
      this.takeAction("previewWord", { lock, handIds, handMask, tableauIds, tableauMask });
    },

    moveAll(location: string): Promise<any> {
      if (this.gamestate.active && this.gamestate.name == "playerTurn") {
        return Promise.all(
          this.cardsInLocation(location).map((card) =>
            this.clickCard({
              card: card,
              action: {
                action: "move",
                destination: location == "tableau" ? card.origin : "tableau",
              },
            })
          )
        );
      } else {
        return Promise.resolve([]);
      }
    },

    resetAll(location: string): void {
      const wilds = location == "tableau" ? this.tableauWildCards : this.handWildCards;
      wilds.forEach((card) => {
        this.clickFooter({ card: card, action: { action: "reset" } });
      });
    },

    clickCard(e): Promise<any> {
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

    clickFooter(e): void {
      let { action, card } = e;
      if (action.action == "wild") {
        // Show keyboard
        this.keyboardId = card.id;
      } else if (action.action == "reset") {
        this.gamedatas.cards[card.id].wild = false;
        this.previewWord();
      } else {
        let args = { cardId: card.id };
        if (action.actionArgs) {
          Object.assign(args, action.actionArgs);
        }
        this.takeAction(action.action, args);
      }
    },

    clickKey(letter: string): void {
      if (this.keyboardId != null) {
        this.gamedatas.cards[this.keyboardId].wild = letter;
        this.keyboardId = null;
        this.previewWord();
      }
    },

    /*
     * Drag and drop
     */

    dragStart(e) {
      if (this.drag) {
        console.warn("dragStart: Another drag is already in progress");
        this.dragStop();
      }

      const ev: PointerEvent = e.ev;
      const el: HTMLElement = e.el;
      const cardId: number = e.cardId;
      const card = this.gamedatas.cards[cardId];
      const locations: string[] = e.locations;
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
      const cloneEl = el.cloneNode(true) as HTMLElement;
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
    debugDrops(drops: Map<any, any>) {
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

    dragMove(ev: PointerEvent) {
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

    dragStop(ev: PointerEvent) {
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
