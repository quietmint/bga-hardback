<template>
  <div>
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

    <!-- Final round reminder -->
    <div v-if="gamedatas.finalRound && !gamedatas.options.coop" class="text-20 text-center font-bold text-red-600 m-4" v-text="i18n('finalRound')"></div>

    <!-- Discard -->
    <div class="container-discard cardgrid bg-opacity-50 rounded-lg my-2 p-2" :class="(visibleLocations[discardLocation] ? '' : 'collapsed ') + myself.colorBg">
      <!-- Title -->
      <b class="title"><Icon @click="chevron(discardLocation)" icon="chevron" class="chevron inline text-24" /> <span v-text="i18n('myDiscard', { count: discardCards.length })"></span></b>

      <!-- Cards -->
      <HCardList v-if="visibleLocations[discardLocation]" :cards="discardCards" :location="discardLocation" />

      <!-- Sorter -->
      <div v-if="visibleLocations[discardLocation] && discardCards.length" class="sorter">
        <div @click="sort(discardLocation, 'letter')" class="button blue" :title="i18n('sortLetterTip')">A-Z</div>
        <div @click="sort(discardLocation, 'cost')" class="button blue" :title="i18n('sortCostTip')">¢</div>
        <div @click="sort(discardLocation, 'genre')" class="button blue" :title="i18n('sortGenreTip')"><Icon icon="starter" class="inline text-18 h-7" /></div>
        <div @click="sort(discardLocation, 'shuffle')" class="button blue" :title="i18n('shuffleTip')"><Icon icon="shuffle" class="inline text-18 h-7" /></div>
      </div>
    </div>

    <!-- Hand -->
    <div class="container-hand cardgrid bg-opacity-50 rounded-lg my-2 p-2" :class="myself.colorBg">
      <!-- Title -->
      <b class="title" v-text="i18n('myHand', { count: handCards.length })"></b>

      <!-- Cards -->
      <HCardList :cards="handCards" :location="handLocation" />

      <!-- Actions -->
      <div class="actions">
        <div @click="buttonEnabled['useInk'] && takeAction('useInk')" class="button" :class="buttonEnabled['useInk'] ? 'blue' : 'disabled'" v-text="i18n('draw', { count: myself.ink })"></div>
        <div @click="buttonEnabled['resetAll'] && resetAll(handLocation)" class="button" :class="buttonEnabled['resetAll'] ? 'blue' : 'disabled'" v-text="i18n('resetAll')"></div>
        <div @click="buttonEnabled['moveAll'] && moveAll(handLocation)" class="button" :class="buttonEnabled['moveAll'] ? 'blue' : 'disabled'" v-text="i18n('moveAll')"></div>
      </div>

      <!-- Sorter -->
      <div v-if="handCards.length" class="sorter">
        <div @click="sort(handLocation, 'letter')" class="button blue" :title="i18n('sortLetterTip')">A-Z</div>
        <div @click="sort(handLocation, 'genre')" class="button blue" :title="i18n('sortGenreTip')"><Icon icon="starter" class="inline text-18 h-7" /></div>
        <div @click="sort(handLocation, 'shuffle')" class="button blue" :title="i18n('shuffleTip')"><Icon icon="shuffle" class="inline text-18 h-7" /></div>
      </div>
    </div>

    <!-- Tableau -->
    <div class="container-tableau cardgrid my-2 p-2">
      <!-- Title -->
      <b class="title" v-text="i18n('tableau', { count: tableauCards.length })"></b>

      <!-- Cards -->
      <HCardList :cards="tableauCards" location="tableau" />

      <!-- Actions -->
      <div v-if="tableauCards.length" class="actions">
        <div @click="buttonEnabled['resetAllTableau'] && resetAll('tableau')" class="button" :class="buttonEnabled['resetAllTableau'] ? 'blue' : 'disabled'" v-text="i18n('resetAll')"></div>
        <div @click="buttonEnabled['moveAllTableau'] && moveAll('tableau')" class="button" :class="buttonEnabled['moveAllTableau'] ? 'blue' : 'disabled'" v-text="i18n('moveAll')"></div>
      </div>

      <!-- Sorter -->
      <div v-if="tableauCards.length" class="sorter">
        <div @click="sort('tableau', 'letter')" class="button blue" :title="i18n('sortLetterTip')">A-Z</div>
        <div @click="sort('tableau', 'genre')" class="button blue" :title="i18n('sortGenreTip')"><Icon icon="starter" class="inline text-18 h-7" /></div>
        <div @click="sort('tableau', 'shuffle')" class="button blue" :title="i18n('shuffleTip')"><Icon icon="shuffle" class="inline text-18 h-7" /></div>
      </div>
    </div>

    <!-- Timeless Classics -->
    <div v-if="timelessCards.length" class="container-timeless cardgrid no-sorter my-2 p-2 border-t border-dashed border-black">
      <!-- Title -->
      <b class="title" v-text="i18n('timeless', { count: timelessCards.length })"></b>

      <!-- Cards -->
      <HCardList :cards="timelessCards" location="timeless" />
    </div>

    <!-- Offer -->
    <div class="container-offer cardgrid bg-gray-700 bg-opacity-30 rounded-lg my-2 p-2">
      <!-- Title -->
      <b class="title" v-text="i18n('offer', { count: offerCards.length })"></b>

      <!-- Cards -->
      <HCardList :cards="offerCards" location="offer" />

      <!-- Sorter -->
      <div class="sorter">
        <div @click="sort('offer', 'letter')" class="button blue" :title="i18n('sortLetterTip')">A-Z</div>
        <div @click="sort('offer', 'cost')" class="button blue" :title="i18n('sortCostTip')">¢</div>
        <div @click="sort('offer', 'genre')" class="button blue" :title="i18n('sortGenreTip')"><Icon icon="starter" class="inline text-18 h-7" /></div>
        <div @click="sort('offer', 'order')" class="button blue" :title="i18n('sortOrderTip')"><Icon icon="clock" class="inline text-18 h-7" /></div>
      </div>
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

const sleep = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));
const repaint = () => new Promise((resolve) => requestAnimationFrame(resolve));
const getRect = (el: Element) => {
  if (!el) {
    return null;
  }
  const bounds = el.getBoundingClientRect();
  return {
    top: bounds.top + window.scrollX,
    left: bounds.left + window.scrollY,
    width: bounds.width,
    height: bounds.height,
  };
};
const getHtml = (id: string) => {
  const el = document.getElementById(id);
  if (el) {
    return el.outerHTML.replace(/ id=.*? /, " ");
  }
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

// Player panel icons
import mdiFlaskEmptyPlus from "@iconify-icons/mdi/flask-empty-plus";
addIcon("ink", mdiFlaskEmptyPlus);

import mdiFlaskEmptyRemoveOutline from "@iconify-icons/mdi/flask-empty-remove-outline";
addIcon("remover", mdiFlaskEmptyRemoveOutline);

import mdiCards from "@iconify-icons/mdi/cards";
addIcon("cards", mdiCards);

export default {
  name: "HGame",
  components: { Icon, HCardList, HKeyboard, HPenny, HPlayerPanel },

  provide() {
    return {
      gamestate: this.gamestate,
      options: computed(() => this.gamedatas.options),
      i18n: this.i18n,
    };
  },

  mounted() {
    this.emitter.on("clickCard", this.clickCard);
    this.emitter.on("clickFooter", this.clickFooter);
    this.emitter.on("clickKey", this.clickKey);
    this.emitter.on("drag", this.drag);
    this.visibleLocations[this.discardLocation] = false;
    this.visibleLocations[this.handLocation] = true;
  },

  beforeUnmount() {
    this.emitter.off("clickCard", this.clickCard);
    this.emitter.off("clickFooter", this.clickFooter);
    this.emitter.off("clickKey", this.clickKey);
    this.emitter.off("drag", this.drag);
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
          benefits: {},
          cards: {},
          i18n: {},
        },
      },
      gamestate: {},
      keyboardId: null,
      locationOrder: {
        timeless: "location",
        tableau: "order",
        offer: "order",
      },
      logIcons: ["starter", "adventure", "horror", "mystery", "romance", "star"],
      visibleLocations: {
        tableau: true,
        timeless: true,
        offer: true,
        jail: true,
      },
    };
  },

  computed: {
    spectator() {
      return this.game.isSpectator;
    },

    myself() {
      return this.players[this.game.player_id] || {};
    },

    players() {
      return this.populatePlayers(this.gamedatas.players) || {};
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

    i18n(msg: string, args: any): string {
      if (this.gamedatas.refs.i18n[msg]) {
        msg = this.gamedatas.refs.i18n[msg];
      }
      msg = window._(msg);
      if (args) {
        msg = this.game.format_string_recursive(msg, args);
      }
      return msg;
    },

    cardsInLocation(location: string): any[] {
      let cards = this.populateCards(
        Object.values(this.gamedatas.cards).filter((card: any) => {
          return card.location.startsWith(location);
        })
      );
      this.sortLocation(location, cards);
      return cards;
    },

    sortLocation(location: string, cards: any[]): void {
      let order = this.locationOrder[location] || "letter";
      let sorter = firstBy(order);
      if (order != "letter") {
        sorter = sorter.thenBy("letter");
      }
      sorter = sorter.thenBy("id");
      cards.sort(sorter);
    },

    reorderLocation(location: string): void {
      if (this.locationOrder[location] != "order") {
        this.cardsInLocation(location).forEach((card, i) => {
          this.gamedatas.cards[card.id].order = i;
        });
        this.locationOrder[location] = "order";
      }
    },

    nextOrderInLocation(location: string): number {
      const cards = this.cardsInLocation(location);
      const max: number = cards.map((card: any) => card.order).reduce((acc: number, cur: number) => Math.max(acc, cur), -1);
      return max + 1;
    },

    populateCard(card) {
      const star = getHtml("benefit_star");
      const adventure = getHtml("benefit_adventure");
      let newCard = Object.assign({}, this.gamedatas.refs.cards[card.refId], card);
      // Basic benefits
      newCard.basicBenefitsList = [];
      newCard.factor = newCard.factor || 1;
      if (newCard.basicBenefits) {
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
      }

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

      // Draggable
      newCard.draggable = newCard.location == this.handLocation || (newCard.location == "tableau" && this.gamestate.active && this.gamestate.name == "playerTurn");

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

    populatePlayer(player) {
      player.colorBgText = "text-white";
      switch (player.colorName) {
        case "red":
          player.colorRing = "ring-red-700";
          player.colorBg = "bg-red-700";
          player.colorBgText = "text-red-100";
          player.colorText = "text-red-600";
          break;
        case "green":
          player.colorRing = "ring-green-700";
          player.colorBg = "bg-green-700";
          player.colorText = "text-green-700";
          break;
        case "blue":
          player.colorRing = "ring-blue-700";
          player.colorBg = "bg-blue-700";
          player.colorText = "text-blue-600";
          break;
        case "yellow":
          player.colorBgText = "text-black";
          player.colorRing = "ring-yellow-500";
          player.colorBg = "bg-yellow-600";
          player.colorText = "text-yellow-600";
          break;
        case "purple":
          player.colorRing = "ring-purple-700";
          player.colorBg = "bg-purple-700";
          player.colorText = "text-purple-600";
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
    async animateCard(card: any, changes: any): Promise<number> {
      if (changes) {
        card = Object.assign({}, card, changes);
      }

      if (this.game.instantaneousMode) {
        // Instant replay, no animation
        this.gamedatas.cards[card.id] = card;
        return card.id;
      }

      const oldCard = this.gamedatas.cards[card.id];
      if (oldCard != null && (oldCard.location == card.location || (oldCard.location == "offer" && card.location == "jail"))) {
        // Same location, no animation
        this.gamedatas.cards[card.id] = card;
        // Delay for Vue to animate the reorder
        if (oldCard.order != card.order || (oldCard.location == "offer" && card.location == "jail")) {
          await sleep(600);
        }
        return card.id;
      }

      // Compute start position
      let cardEl = null;
      let gapEl = null;
      let start = null;
      let end = null;
      if (oldCard != null) {
        cardEl = document.getElementById("card" + oldCard.id);
        start = getRect(cardEl);
      }

      let visible = this.visibleLocations[card.location] || card.location.startsWith("timeless");
      console.log(`Animate card ${card.id} new location ${card.location} is visible? ${visible}`);
      if (!start && !visible) {
        // Invisible card movement, no animation
        this.gamedatas.cards[card.id] = card;
        return card.id;
      }

      let mode = null;
      if (start && !visible) {
        // Compute end position
        mode = "leave";
        if (card.location.startsWith("discard_") || card.location == "deck_" + this.myself.id) {
          let playerId = card.location.split("_")[1];
          let parentEl = document.getElementById("player_board_" + playerId);
          end = getRect(parentEl);
        }
        if (end == null) {
          // Exit below
          end = { top: window.innerHeight + start.height, left: start.left, width: start.width, height: start.height };
        }

        // Insert a gap
        let rootEl = document.getElementById("happ");
        gapEl = document.createElement("div");
        gapEl.id = "gap" + card.id;
        gapEl.className = "gap";
        gapEl.style.width = start.width + "px";
        gapEl.style.height = start.height + "px";
        cardEl.parentNode.insertBefore(gapEl, cardEl);

        // Move card to end
        const top = end.top + (end.height - start.height) / 2;
        const left = end.left + (end.width - start.width) / 2;
        rootEl.appendChild(cardEl);
        cardEl.style.position = "absolute";
        cardEl.style.top = top + "px";
        cardEl.style.left = left + "px";
        await repaint();

        // Compute reverse transform
        end = getRect(cardEl);
      } else {
        // Move card to end and compute end position
        mode = "enter";
        this.gamedatas.cards[card.id] = card;
        await nextTick();
        cardEl = document.getElementById("card" + card.id);
        if (!cardEl) {
          console.warn(`Animate card ${card.id} ${mode} element disappeared`);
          return card.id;
        }
        end = getRect(cardEl);
        if (start == null) {
          // Enter above
          start = { top: -end.height, left: end.left, width: end.width, height: end.height };
        }
      }

      // Apply reverse transform
      let diffX = start.left - end.left;
      let diffY = start.top - end.top;
      cardEl.style.transition = "none";
      cardEl.style.transform = "translate(" + diffX + "px, " + diffY + "px)";
      await repaint();

      // Resolve when the transition ends
      let promise: Promise<number> = new Promise((resolve) => {
        // Just in case transitionend never fires
        const timeout = setTimeout(() => {
          console.warn(`Animate card ${card.id} ${mode} missing transitionend`);
          if (gapEl) {
            gapEl.remove();
          }
          resolve(card.id);
        }, 2000);

        cardEl.style.transition = "";
        cardEl.style.transform = "";
        const t0 = performance.now();
        cardEl.addEventListener(
          "transitionend",
          () => {
            const t1 = performance.now();
            clearTimeout(timeout);
            console.log(`Animate card ${card.id} ${mode} took ${Math.round(t1 - t0)}ms`);
            resolve(card.id);
          },
          { once: true }
        );

        if (gapEl) {
          gapEl.style.width = "";
          gapEl.addEventListener(
            "transitionend",
            () => {
              const t1 = performance.now();
              clearTimeout(timeout);
              console.log(`Animate card ${card.id} ${mode} destroy gap`);
              gapEl.remove();
              gapEl = null;
            },
            { once: true }
          );
        }
      });

      if (mode == "leave") {
        promise = promise.finally(() => {
          this.gamedatas.cards[card.id] = card;
        });
      }
      return promise;
    },

    /*
     * BGA framework methods
     */
    takeAction(action: string, data: any): Promise<any> {
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
      let gameName = this.game.name();
      console.log(`Take action ${action}`, data);
      return new Promise((resolve, reject) => {
        this.game.ajaxcall("/" + gameName + "/" + gameName + "/" + action + ".html", data, this, resolve, (error) => {
          error ? reject(error) : resolve(error);
        });
      });
    },

    /*
     * BGA framework event callbacks
     */
    onFormatString(log: string, args: any): string {
      if (args) {
        if (args.award) {
          args.award = `<div class="haward length${args.award}"></div>`;
        }
        if (args.word) {
          let q = args.word.toLowerCase();
          let links = [
            `<a target="hdefine" href="https://dictionary.cambridge.org/dictionary/english/${q}">Cambridge</a>`, //
            `<a target="hdefine" href="https://www.collinsdictionary.com/dictionary/english/${q}">Collins</a>`, //
            `<a target="hdefine" href="https://www.dictionary.com/browse/${q}">Dictionary.com</a>`, //
            `<a target="hdefine" href="https://www.merriam-webster.com/dictionary/${q}">Merriam-Webster</a>`, //
            `<a target="hdefine" href="https://www.lexico.com/en/definition/${q}">Oxford Lexico</a>`, //
            `<a target="hdefine" href="https://en.wiktionary.org/wiki/${q}">Wiktionary</a>`, //
            `<a target="hdefine" href="http://wordnetweb.princeton.edu/perl/webwn?s=${q}">WordNet</a>`, //
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

    onUpdateActionButtons(stateName: string, args: any): void {
      console.log(`State ${stateName}`, args);
      Object.assign(this.gamestate, this.game.gamedatas.gamestate, { active: this.game.isCurrentPlayerActive() });

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
            let wildMask = this.tableauCards.map((card) => card.wild || "_").join("");
            this.takeAction("confirmWord", { cardIds, wildMask });
          },
        },
        skipWord: {
          text: "skipWordButton",
          color: "red",
          function() {
            this.takeAction("skipWord");
          },
        },
        skip: {
          text() {
            return this.gamestate.name == "purchase" ? "skipPurchaseButton" : "skipButton";
          },
          color: "gray",
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
          color: "gray",
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

    onEnteringState(stateName: string, args: any): void {
      console.log(`onEnteringState ${stateName}`, args);
      if (args && args.updateGameProgression) {
        this.gamedatas.finalRound = args.updateGameProgression >= 100;
        console.warn("game progression", args.updateGameProgression, this.gamedatas.finalRound);
      }
      if (stateName == "trashDiscard" && !args.skip) {
        //this.visibleLocations[this.discardLocation] = true;
      }
    },

    onLeavingState(stateName: string): void {
      if (stateName == "trashDiscard") {
        //this.visibleLocations[this.discardLocation] = false;
      }
    },

    onNotify(notif: any): void {
      console.log(`Notify ${notif.type}`, notif.args);
      if (notif.type == "cards") {
        let cards = Object.values(notif.args.cards);
        cards.sort(firstBy("location").thenBy("order").thenBy("id"));
        Promise.all(cards.map(this.animateCard)).then(() => {
          this.game.notifqueue.setSynchronousDuration(0);
        });
      } else if (notif.type == "invalid") {
        if (this.game.player_id == notif.args.player_id) {
          this.game.showMessage(this.i18n("invalid", notif.args), "error");
        }
      } else if (notif.type == "pause") {
        this.game.notifqueue.setSynchronousDuration(notif.args.duration);
      } else if (notif.type == "penny") {
        Object.assign(this.gamedatas.penny, notif.args.penny);
      } else if (notif.type == "player") {
        Object.assign(this.gamedatas.players[notif.args.player.id], notif.args.player);
        if (notif.args.allScore) {
          Object.keys(this.gamedatas.players).forEach((id) => this.game.scoreCtrl[id].toValue(notif.args.allScore));
        } else {
          this.game.scoreCtrl[notif.args.player.id].toValue(notif.args.player.score);
        }
      }
    },

    /*
     * Other functions
     */

    sort(location: string, order: string): void {
      this.locationOrder[location] = order;
      if (order == "shuffle") {
        let cards = this.cardsInLocation(location);
        for (let i = cards.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [cards[i], cards[j]] = [cards[j], cards[i]];
        }
        cards.forEach((card, index) => {
          this.gamedatas.cards[card.id].shuffle = index;
        });
      }
    },

    drag(e): void {
      let { location, cardIds } = e;
      cardIds.forEach((id, index) => {
        this.gamedatas.cards[id].order = index;
      });
      this.locationOrder[location] = "order";

      // Non-blocking server update
      // let lock = false;
      // this.takeAction("drag", { cardIds, lock });
    },

    chevron(location: string) {
      this.visibleLocations[location] = !this.visibleLocations[location];
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
        if (action.destination == "tableau") {
          this.reorderLocation("tableau");
        }
        return this.animateCard(card, {
          location: action.destination,
          order: this.nextOrderInLocation(action.destination),
        });
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
      }
    },
  },
};
</script>
