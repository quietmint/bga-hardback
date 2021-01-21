<template>
  <div>
    <!-- Player panels (moved using teleport) -->
    <HPlayerPanel v-for="(player, id) in players" :key="id" :player="player" />

    <!-- Log message icons-->
    <div class="hidden">
      <Icon v-for="icon in logIcons" :key="icon" :id="'hicon_' + icon" :icon="icon" class="hicon" />
    </div>

    <div><b>Gamestate:</b> Active: {{ gamestate.active }} &mdash; Name: {{ gamestate.name }}</div>

    <!-- Hand -->
    <div class="container-hand bg-opacity-50 rounded-lg my-2 p-2" :class="myself.colorBg">
      <div class="flex flex-wrap justify-end mb-1">
        <b class="flex-grow">My Hand ({{ handCards.length }})</b>
        <div v-if="myself.ink && (myself.deckCount || myself.discardCount)" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
          <div class="rounded-lg" :class="buttonClass" @click="takeAction('useInk')"><Icon icon="ink" class="inline text-lg" /> Draw with Ink</div>
        </div>
        <div v-if="gamestate.active && handCards.length > 1" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
          <div class="rounded-lg" :class="buttonClass" @click="clickAll(handCards)"><Icon icon="clickAll" class="inline text-lg" /> Play All</div>
        </div>
        <div v-if="handCards.length > 1" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
          <div class="rounded-l-lg" :class="buttonHeaderClass">Order:</div>
          <div :class="buttonClass" @click="sort(handLocation, 'letter')" title="By Letter"><Icon icon="sortAZ" class="text-lg" /></div>
          <div :class="buttonClass" @click="sort(handLocation, 'genre')" title="By Genre"><Icon icon="starter" class="text-lg" /></div>
          <div class="rounded-r-lg" :class="buttonClass" @click="sort(handLocation, 'shuffle')" title="Shuffle"><Icon icon="shuffle" class="text-lg" /></div>
        </div>
      </div>
      <HCardList :cards="handCards" :location="handLocation" :checkDrag="checkDragHand" />
    </div>

    <!-- Tableau -->
    <div class="container-tableau rounded-lg my-2 p-2">
      <div class="flex flex-wrap justify-end mb-1">
        <b class="flex-grow">Word ({{ tableauCards.length }})</b>
        <div v-if="gamestate.active && tableauCards.length" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
          <div class="rounded-lg" :class="buttonClass" @click="clickAll(tableauCards)"><Icon icon="close" class="inline text-lg" /> Reset</div>
        </div>
      </div>
      <HCardList :cards="tableauCards" location="tableau" :checkDrag="checkDragTableau" />
    </div>

    <!-- Timeless Classics -->
    <div class="container-timeless rounded-lg my-2 p-2">
      <div class="flex flex-wrap justify-end mb-1">
        <b class="flex-grow">Timeless Classics ({{ timelessCards.length }})</b>
      </div>
      <HCardList :cards="timelessCards" location="timeless" :checkDrag="checkDragTimeless" />
    </div>

    <!-- Offer -->
    <div class="container-offer bg-gray-700 bg-opacity-30 rounded-lg my-2 p-2">
      <div class="flex flex-wrap justify-end mb-1">
        <b class="flex-grow">Offer Row ({{ offerCards.length }})</b>
        <div class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
          <div class="rounded-l-lg font-bold" :class="buttonHeaderClass">Order:</div>
          <div :class="buttonClass" @click="sort('offer', 'order')" title="By Recency"><Icon icon="clock" class="text-lg" /></div>
          <div :class="buttonClass" @click="sort('offer', 'letter')" title="By Letter"><Icon icon="sortAZ" class="text-lg" /></div>
          <div :class="buttonClass" @click="sort('offer', 'cost')" title="By Cost"><Icon icon="sort09" class="inline text-lg" /></div>
          <div class="rounded-r-lg" :class="buttonClass" @click="sort('offer', 'genre')" title="By Genre"><Icon icon="starter" class="text-lg" /></div>
        </div>
      </div>
      <HCardList :cards="offerCards" location="offer" :checkDrag="checkDragTimeless" />
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { computed, nextTick } from "vue";
import { firstBy } from "thenby";
import { Icon, addIcon } from "@iconify/vue";
import HCardList from "./HCardList.vue";
import HPlayerPanel from "./HPlayerPanel.vue";

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const repaint = () => new Promise((resolve) => requestAnimationFrame(resolve));

import mdiCards from "@iconify-icons/mdi/cards";
addIcon("cards", mdiCards);

import bookmarkIcon from "@iconify-icons/mdi/bookmark";
addIcon("starter", bookmarkIcon);

import mdiCompass from "@iconify-icons/mdi/compass";
addIcon("adventure", mdiCompass);

import mdiFlaskEmptyPlus from "@iconify-icons/mdi/flask-empty-plus";
addIcon("ink", mdiFlaskEmptyPlus);

import mdiFlaskEmptyRemoveOutline from "@iconify-icons/mdi/flask-empty-remove-outline";
addIcon("remover", mdiFlaskEmptyRemoveOutline);

import mdiHeart from "@iconify-icons/mdi/heart";
addIcon("romance", mdiHeart);

import magnifyingGlass from "@iconify-icons/foundation/magnifying-glass";
addIcon("mystery", magnifyingGlass);

import mdiSkull from "@iconify-icons/mdi/skull";
addIcon("horror", mdiSkull);

import starOutlined from "@iconify-icons/ant-design/star-outlined";
addIcon("star", starOutlined);

import shuffleVariant from "@iconify-icons/mdi/shuffle-variant";
addIcon("shuffle", shuffleVariant);

import sortAlphabeticalVariant from "@iconify-icons/mdi/sort-alphabetical-variant";
addIcon("sortAZ", sortAlphabeticalVariant);

//import numericIcon from "@iconify-icons/mdi/numeric";
import sortNumericVariant from "@iconify-icons/mdi/sort-numeric-variant";
addIcon("sort09", sortNumericVariant);

import clockOutline from "@iconify-icons/mdi/clock-outline";
addIcon("clock", clockOutline);

import closeIcon from "@iconify-icons/mdi/close";
addIcon("close", closeIcon);

import cursorDefaultClick from "@iconify-icons/mdi/cursor-default-click";
import expandAllOutline from "@iconify-icons/mdi/expand-all-outline";
//import plusBoxMultipleOutline from '@iconify-icons/mdi/plus-box-multiple-outline';
addIcon("clickAll", expandAllOutline);

import lockIcon from "@iconify-icons/mdi/lock";
addIcon("jail", lockIcon);

import cachedIcon from "@iconify-icons/mdi/cached";
addIcon("timeless", cachedIcon);

export default {
  name: "HGame",
  components: { Icon, HCardList, HPlayerPanel },

  data() {
    return {
      gamedatas: {
        cards: {},
        players: {},
        refs: { benefits: {}, cards: {} },
      },
      gamestate: {},
      locationOrder: {},
      logIcons: ["starter", "adventure", "horror", "mystery", "romance", "star"],
    };
  },

  mounted() {
    this.emitter.on("clickCard", this.clickCard);
    this.emitter.on("clickFooter", this.clickFooter);
  },

  provide() {
    return {
      gamestate: this.gamestate,
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
      let location = this.handLocation;
      let cards = this.cardsInLocation(location);
      cards.sort(this.sorter(location));
      return cards;
    },

    tableauCards() {
      let location = "tableau";
      let cards = this.cardsInLocation(location);
      cards.sort(this.sorter(location));
      return cards;
    },

    timelessCards() {
      let location = "timeless";
      let cards = this.cardsInLocation(location);
      cards.sort(this.sorter(location));
      return cards;
    },

    offerCards() {
      let location = "offer";
      let cards = this.cardsInLocation(location);
      cards.sort(this.sorter(location));
      location = "jail";
      let jail = this.cardsInLocation(location);
      jail.sort(this.sorter(location));
      Array.prototype.push.apply(cards, jail);
      return cards;
    },

    buttonGroupClass() {
      return this.myself.colorDivide + " " + this.myself.colorBorder;
    },

    buttonHeaderClass() {
      return "pl-2 pr-1 font-bold " + this.myself.colorHeader;
    },

    buttonClass() {
      return "leading-6 px-2 cursor-pointer " + this.myself.colorButton;
    },
  },

  methods: {
    /*
     * Utility functions
     */

    cardsInLocation(location: string): any[] {
      return this.populateCards(
        Object.values(this.gamedatas.cards).filter((card: any) => {
          return card.location.startsWith(location);
        })
      );
    },

    nextOrderInLocation(location: string): number {
      const cards = this.cardsInLocation(location);
      const max: number = cards.map((card: any) => card.order).reduce((acc: number, cur: number) => Math.max(acc, cur), -1);
      return max + 1;
    },

    sorter(location: string) {
      let order = this.locationOrder[location] || "order";
      return firstBy(order).thenBy("letter").thenBy("id");
    },

    populateCard(card) {
      let newCard = Object.assign({}, this.gamedatas.refs.cards[card.refId], card);
      newCard.basicBenefitsList = [];
      newCard.factor = newCard.factor || 1;
      if (newCard.basicBenefits) {
        for (const id in newCard.basicBenefits) {
          let value = newCard.basicBenefits[id];
          if (newCard.factor > 1) {
            value = `<b class="text-red-500">${value * newCard.factor}</b>`;
          }
          let newBenefit = Object.assign({}, this.gamedatas.refs.benefits[id]);
          newBenefit.id = parseInt(id);
          if (newBenefit.text) {
            newBenefit.text = newBenefit.text.replaceAll("%", value);
          }
          if (newBenefit.text2) {
            newBenefit.text2 = newBenefit.text2.replaceAll("%", value);
          }
          newCard.basicBenefitsList.push(newBenefit);
        }
        newCard.basicBenefitsList.sort(firstBy("id"));
      }
      newCard.genreBenefitsList = [];
      if (newCard.genreBenefits) {
        for (const id in newCard.genreBenefits) {
          let value = newCard.genreBenefits[id];
          if (newCard.factor > 1) {
            value = `<b class="text-red-500">${value * newCard.factor}</b>`;
          }
          let newBenefit = Object.assign({}, this.gamedatas.refs.benefits[id]);
          newBenefit.id = parseInt(id);
          if (newBenefit.text) {
            newBenefit.text = newBenefit.text.replaceAll("%", value);
          }
          if (newBenefit.text2) {
            newBenefit.text2 = newBenefit.text2.replaceAll("%", value);
          }
          newCard.genreBenefitsList.push(newBenefit);
        }
        newCard.genreBenefitsList.sort(firstBy("id"));
      }
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

    populatePlayer(player) {
      player.colorBgText = "text-white";
      switch (player.colorName) {
        case "red":
          player.colorRing = "ring-red-700";
          player.colorBg = "bg-red-700";
          player.colorBgText = "text-red-100";
          player.colorBorder = "border-red-800";
          player.colorDivide = "divide-red-800";
          player.colorText = "text-red-700";
          player.colorButton = "bg-red-100 hover:bg-red-300 active:bg-red-500";
          player.colorHeader = "bg-red-50 " + player.colorText;
          break;
        case "green":
          player.colorRing = "ring-green-700";
          player.colorBg = "bg-green-700";
          player.colorBorder = "border-green-800";
          player.colorDivide = "divide-green-800";
          player.colorText = "text-green-700";
          player.colorButton = "bg-green-100 hover:bg-green-300 active:bg-green-500";
          player.colorHeader = "bg-green-50 " + player.colorText;
          break;
        case "blue":
          player.colorRing = "ring-blue-700";
          player.colorBg = "bg-blue-700";
          player.colorBorder = "border-blue-800";
          player.colorDivide = "divide-blue-800";
          player.colorText = "text-blue-700";
          player.buttonGroupClass = "bg-blue-100 hover:bg-blue-300 active:bg-blue-500";
          player.colorHeader = "bg-blue-50 " + player.colorText;
          break;
        case "yellow":
          player.colorBgText = "text-black";
          player.colorRing = "ring-yellow-500";
          player.colorBg = "bg-yellow-500";
          player.colorBorder = "border-yellow-700";
          player.colorDivide = "divide-yellow-700";
          player.colorText = "text-yellow-600";
          player.buttonGroupClass = "bg-yellow-100 hover:bg-yellow-300 active:bg-yellow-500";
          player.colorHeader = "bg-yellow-50 " + player.colorText;
          break;
        case "purple":
          player.colorRing = "ring-purple-700";
          player.colorBg = "bg-purple-700";
          player.colorBorder = "border-purple-800";
          player.colorDivide = "divide-purple-800";
          player.colorText = "text-purple-700";
          player.colorButton = "bg-purple-100 hover:bg-purple-300 active:bg-purple-500";
          player.colorHeader = "bg-purple-50 " + player.colorText;
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
    async animateCard(card: any, delay: number, changes: any): Promise<number> {
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

      if (changes) {
        card = Object.assign({}, card, changes);
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

      let visible = [this.handLocation, "tableau", "offer", "jail", "timeless"].includes(card.location) || card.location.startsWith("timeless");
      if (!start && !visible) {
        // Invisible card movement, no animation
        this.gamedatas.cards[card.id] = card;
        return card.id;
      }

      // Optional delay
      if (delay) {
        await sleep(delay);
      }

      let mode = null;
      if (start && !visible) {
        // Compute end position
        mode = "leave";
        if (card.location.includes("_")) {
          let playerId = card.location.split("_")[1];
          let parentEl = document.getElementById("player_board_" + playerId);
          end = getRect(parentEl);
        }
        if (end == null) {
          // Exit stage left
          end = { top: start.top, left: -start.width, width: start.width, height: start.height };
        }

        // Insert a gap
        let rootEl = document.getElementById("happ");
        console.log("gap start", start);
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
          // Enter from above
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
      let promise: Promise<number> = new Promise((resolve, reject) => {
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
    takeAction(action: string, data: any, callback): void {
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
      callback = callback || function (res) {};
      let gameName = this.game.name();
      console.log("Take action", action, data);
      this.game.ajaxcall("/" + gameName + "/" + gameName + "/" + action + ".html", data, this, callback);
    },

    /*
     * BGA framework event callbacks
     */
    onFormatString(log: string, args: any): string {
      if (args.award) {
        args.award = `<b>${args.award}</b>-letter word<div class="haward length${args.award}"></div>`;
      }
      if (args.word) {
        let q = args.word.toLowerCase();
        let links = [
          `<a target="hdefine" href="https://ahdictionary.com/word/search.html?q=${q}">American Heritage</a>`, //
          `<a target="hdefine" href="https://www.collinsdictionary.com/dictionary/english/${q}">Collins</a>`, //
          `<a target="hdefine" href="https://www.dictionary.com/browse/${q}">Dictionary.com</a>`, //
          `<a target="hdefine" href="https://www.merriam-webster.com/dictionary/${q}">Merriam-Webster</a>`, //
          `<a target="hdefine" href="https://www.lexico.com/en/definition/${q}">Oxford Lexico</a>`, //
          `<a target="hdefine" href="https://en.wiktionary.org/wiki/${q}">Wiktionary</a>`, //
          `<a target="hdefine" href="http://wordnetweb.princeton.edu/perl/webwn?s=${q}">WordNet</a>`, //
        ];
        args.word = `<b>${args.word}</b><div class="hdefine">Dictionary definitions:<ul><li>${links.join("</li><li>")}</li></ul></div>`;
      }
      if (args.invalid) {
        args.invalid = `<b>${args.invalid}</b>`;
      }
      if (args.genre) {
        const el = document.getElementById("hicon_" + args.genre.toLowerCase().trim());
        args.genre = `<span class="hgenre ${args.genre}">${el ? el.outerHTML : args.genre}`;
      }
      if (args.letter) {
        args.letter = `${args.letter}</span>`;
      }
      if (args.icon) {
        const el = document.getElementById("hicon_" + args.icon.toLowerCase().trim());
        if (el) {
          args.icon = el.outerHTML;
        }
      }
      return log;
    },

    onUpdateActionButtons(stateName: string, args: any): void {
      console.log("State", stateName, args);
      Object.assign(this.gamestate, this.game.gamedatas.gamestate, { active: this.game.isCurrentPlayerActive() });

      const actionRef = {
        confirmWord: {
          text: "Confirm Word",
          color: "blue",
          function() {
            let cardIds = this.cardIds(this.tableauCards);
            let wildMask = this.tableauCards.map((card) => card.wild || "_").join("");
            this.takeAction("confirmWord", { cardIds, wildMask });
          },
        },
        skipWord: {
          text: "Skip Turn",
          color: "red",
          function() {
            this.takeAction("skipWord");
          },
        },
        skip: {
          text: "Skip",
          color: "gray",
          function() {
            this.takeAction("skip");
          },
        },
        flush: {
          text: "Flush Offer Row",
          color: "blue",
          function() {
            this.takeAction("flush");
          },
        },
        convert: {
          text: "Spend 3 ink for 1¢",
          color: "gray",
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
          this.game.addActionButton(
            "action_" + index,
            action.text,
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
      Object.assign(this.gamestate, this.game.gamedatas.gamestate, { active: this.game.isCurrentPlayerActive() });
    },

    onLeavingState(stateName: string): void {},

    onNotify(notif: any): void {
      console.log("Notify", notif.type, notif.args);
      if (notif.type == "cards") {
        let cards = Object.values(notif.args.cards);
        cards.sort(firstBy("location").thenBy("order").thenBy("id"));
        console.log("packet of changed cards", cards);
        let delay = 0;
        const promises = cards.map((card) => this.animateCard(card, (delay += 0)));
        Promise.allSettled(promises).then((results) => {
          console.log("packet allSettled", results);
          this.game.notifqueue.setSynchronousDuration(0);
        });
      } else if (notif.type == "invalid") {
        if (this.game.player_id == notif.args.player_id) {
          this.game.showMessage(notif.args.invalid + " is not a valid word", "error");
        }
      } else if (notif.type == "pause") {
        this.game.notifqueue.setSynchronousDuration(notif.args.duration);
      } else if (notif.type == "panel") {
        this.gamedatas.players[notif.args.player.id] = notif.args.player;
        this.game.scoreCtrl[notif.args.player.id].toValue(notif.args.player.score);
      }
    },

    /*
     * Other functions
     */

    checkDragHand(card: any, fromLocation: String, toLocation: String): boolean {
      // Anyone can reorder their own hand
      if (toLocation == fromLocation) {
        return;
      }
      // Active player can also move cards to the tableau
      if (this.gamestate.active && toLocation == "tableau") {
        return;
      }
      return false;
    },

    checkDragTimeless(card: any, fromLocation: String, toLocation: String): boolean {
      // Active player can move cards to the tableau
      return this.gamestate.active && toLocation == "tableau";
    },

    checkDragTableau(card: any, fromLocation: String, toLocation: String): boolean {
      // Active player can reorder the tableau and return cards to their origin
      return this.gamestate.active && (toLocation == fromLocation || toLocation == card.origin);
    },

    /*
    dragCard(evt) {
      if (evt.event == "add" || evt.event == "remove") {
        if (this.dragMoveEvt == null || this.dragMoveEvt.cardId != evt.cardId) {
          this.dragMoveEvt = {
            event: "move",
            cardId: evt.cardId,
          };
        }
        if (evt.event == "add") {
          this.dragMoveEvt.to = evt.location;
          this.dragMoveEvt.order = evt.order;
        } else {
          this.dragMoveEvt.from = evt.location;
        }
        if (this.dragMoveEvt.from && this.dragMoveEvt.to) {
          this.takeAction("dragMove", this.dragMoveEvt);
          this.dragMoveEvt = null;
        }
      } else if (evt.event == "order") {
        this.takeAction("dragOrder", evt);
      }
    },
    */

    sort(location: string, order: string) {
      let cards = this.cardsInLocation(location);
      if (order == "shuffle") {
        for (let i = cards.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [cards[i], cards[j]] = [cards[j], cards[i]];
        }
        cards.forEach((card, index) => {
          this.gamedatas.cards[card.id].shuffle = index;
        });
      }
      this.locationOrder[location] = order;
    },

    clickAll(cards): void {
      console.log("clickAll", cards);
      if (this.gamestate.active) {
        cards.forEach((card) => {
          this.clickCard({ card });
        });
      }
    },

    clickCard(evt): void {
      let { action, card } = evt;
      console.log("clickCard event in parent", action, card.id);
      if (action.action == "move") {
        this.animateCard(card, 0, {
          location: action.destination,
          order: this.nextOrderInLocation(action.destination),
        });
      } else {
        let args = { cardId: card.id };
        if (action.actionArgs) {
          Object.assign(args, action.actionArgs);
        }
        this.takeAction(action.action, args);
      }
    },

    clickFooter(evt): void {
      let { action, card } = evt;
      console.log("clickFooter event in parent", action, card.id);
      if (action.action == "wild") {
        let wild = (prompt("What letter does this wild card represent?") || "").trim().toUpperCase();
        const regex = RegExp("^[A-Z]$");
        if (regex.test(wild)) {
          this.gamedatas.cards[card.id].wild = wild;
        }
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
  },
};
</script>
