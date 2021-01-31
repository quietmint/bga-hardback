<template>
  <div>
    <!-- Coop player board -->
    <teleport to="#player_boards">
      <div id="overall_player_board_0" class="player-board" style="border-color: rgb(0, 0, 0); width: 234px; height: auto">
        <div class="player_board_inner" id="player_board_inner_000000">
          <div class="emblemwrap is_premium" id="avatarwrap_0">
            <img src="https://en.1.studio.boardgamearena.com:8083/data/themereleases/210121-0943/../../avatar/0/2/2305/2305326_32.jpg?h=a1c15cbf2a" alt="" class="avatar emblem" id="avatar_0" />
            <div class="emblempremium"></div>
          </div>
          <div class="player-name" id="player_name_0">
            <div style="color: #000000">Penny Dreadful</div>
            <i class="fa fa-circle status_online player_status"></i>
            <div class="flag" style="background-position: -384px -99px" title="Unknown"></div>
          </div>
          <div id="player_board_0" class="player_board_content">
            <div class="player_score">
              <span id="player_score_0" class="player_score_value">0</span> <i class="fa fa-star" id="icon_point_0"></i>
              <span class="player_elo_wrap"
                >•
                <div class="gamerank gamerank_expert"><span class="icon20 icon20_rankw"></span> <span class="gamerank_value">666</span></div></span
              >
            </div>
            <div class="player_table_status" id="player_table_status_0" style="display: none"></div>
          </div>
          <div id="player_panel_content_000000" class="player_panel_content"></div>
        </div>
      </div>
    </teleport>

    <!-- Player panels (moved using teleport) -->
    <HPlayerPanel v-for="(player, id) in players" :key="id" :player="player" />

    <!-- Log message icons-->
    <div class="hidden">
      <Icon v-for="icon in logIcons" :key="icon" :id="'hicon_' + icon" :icon="icon" class="hicon" />
    </div>

    <div><b>Gamestate:</b> Active: {{ gamestate.active }} &mdash; Name: {{ gamestate.name }}</div>

    <!-- Discard -->
    <div class="container-discard cardgrid bg-opacity-50 rounded-lg my-2 p-2" :class="(visibleLocations[discardLocation] ? '' : 'collapsed ') + myself.colorBg">
      <!-- Title -->
      <b class="title"><Icon @click="chevron(discardLocation)" icon="chevron" class="chevron inline text-24" /> My Discard Pile ({{ discardCards.length }})</b>

      <!-- Cards -->
      <HCardList v-if="visibleLocations[discardLocation]" :cards="discardCards" :location="discardLocation" />

      <!-- Sorter -->
      <div v-if="visibleLocations[discardLocation]" class="sorter">
        <div @click="sort(discardLocation, 'letter')" class="button blue" title="Sort cards by letter">A-Z</div>
        <div @click="sort(discardLocation, 'cost')" class="button blue" title="Sort cards by cost">¢</div>
        <div @click="sort(discardLocation, 'genre')" class="button blue" title="Sort cards by genre"><Icon icon="starter" class="inline text-18 h-7" /></div>
        <div @click="sort(discardLocation, 'shuffle')" class="button blue" title="Shuffle cards"><Icon icon="shuffle" class="inline text-18 h-7" /></div>
      </div>
    </div>

    <!-- Hand -->
    <div class="container-hand cardgrid bg-opacity-50 rounded-lg my-2 p-2" :class="(visibleLocations[handLocation] ? '' : 'collapsed ') + myself.colorBg">
      <!-- Title -->
      <b class="title"><Icon @click="chevron(handLocation)" icon="chevron" class="chevron inline text-24" /> My Hand ({{ handCards.length }})</b>

      <!-- Cards -->
      <HCardList v-if="visibleLocations[handLocation]" :cards="handCards" :location="handLocation" />

      <!-- Actions -->
      <div v-if="visibleLocations[handLocation]" class="actions">
        <div v-if="myself.ink && (myself.deckCount || myself.discardCount) && (!gamestate.active || gamestate.name == 'playerTurn')" @click="takeAction('useInk')" class="button blue">DRAW WITH INK</div>
        <div v-if="handWildCards.length" @click="resetAll()" class="button blue">RESET ALL</div>
        <div v-if="gamestate.active && handCards.length > 1" @click="clickAll(handLocation)" class="button blue">PLAY ALL</div>
      </div>

      <!-- Sorter -->
      <div v-if="visibleLocations[handLocation]" class="sorter">
        <div @click="sort(handLocation, 'letter')" class="button blue" title="Sort cards by letter">A-Z</div>
        <div @click="sort(handLocation, 'genre')" class="button blue" title="Sort cards by genre"><Icon icon="starter" class="inline text-18 h-7" /></div>
        <div @click="sort(handLocation, 'shuffle')" class="button blue" title="Shuffle cards"><Icon icon="shuffle" class="inline text-18 h-7" /></div>
      </div>
    </div>

    <!-- Tableau -->
    <div class="container-tableau cardgrid no-sorter my-2 p-2">
      <!-- Title -->
      <b class="title">Current Word ({{ tableauCards.length }})</b>

      <!-- Cards -->
      <HCardList :cards="tableauCards" location="tableau" />

      <!-- Actions -->
      <div v-if="gamestate.active && gamestate.name == 'playerTurn' && tableauCards.length" class="actions">
        <div @click="clickAll('tableau')" class="button blue">CLEAR ALL</div>
      </div>
    </div>

    <!-- Timeless Classics -->
    <div v-if="timelessCards.length" class="container-timeless cardgrid no-sorter my-2 p-2 border-t border-dashed border-black">
      <!-- Title -->
      <b class="title">Timeless Classics ({{ timelessCards.length }})</b>

      <!-- Cards -->
      <HCardList :cards="timelessCards" location="timeless" />
    </div>

    <!-- Offer -->
    <div class="container-offer cardgrid bg-gray-700 bg-opacity-30 rounded-lg my-2 p-2">
      <!-- Title -->
      <b class="title">Offer Row ({{ offerCards.length }})</b>

      <!-- Cards -->
      <HCardList :cards="offerCards" location="offer" />

      <!-- Sorter -->
      <div class="sorter">
        <div @click="sort('offer', 'letter')" class="button blue" title="Sort cards by letter">A-Z</div>
        <div @click="sort('offer', 'cost')" class="button blue" title="Sort cards by cost">¢</div>
        <div @click="sort('offer', 'genre')" class="button blue" title="Sort cards by genre"><Icon icon="starter" class="inline text-18 h-7" /></div>
        <div @click="sort('offer', 'order')" class="button blue" title="Sort cards by draw order"><Icon icon="clock" class="inline text-18 h-7" /></div>
        <div @click="sort('offer', 'shuffle')" class="button blue" title="Shuffle cards"><Icon icon="shuffle" class="inline text-18 h-7" /></div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import HCardList from "./HCardList.vue";
import HPlayerPanel from "./HPlayerPanel.vue";
import { Icon, addIcon } from "@iconify/vue";
import { firstBy } from "thenby";
import { nextTick } from "vue";

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
  emits: ["requestClickCard"],
  components: { Icon, HCardList, HPlayerPanel },

  provide() {
    return {
      gamestate: this.gamestate,
    };
  },

  mounted() {
    this.emitter.on("clickCard", this.clickCard);
    this.emitter.on("clickFooter", this.clickFooter);
    this.emitter.on("drag", this.drag);
    this.visibleLocations[this.discardLocation] = false;
    this.visibleLocations[this.handLocation] = true;
  },

  beforeUnmount() {
    this.emitter.off("clickCard", this.clickCard);
    this.emitter.off("clickFooter", this.clickFooter);
    this.emitter.off("drag", this.drag);
  },

  data() {
    return {
      gamedatas: {
        cards: {},
        players: {},
        refs: { benefits: {}, cards: {} },
      },
      gamestate: {},
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

    timelessCards() {
      return this.cardsInLocation("timeless");
    },

    offerCards() {
      let cards = this.cardsInLocation("offer");
      let jail = this.cardsInLocation("jail");
      Array.prototype.push.apply(cards, jail);
      return cards;
    },
  },

  methods: {
    /*
     * Utility functions
     */

    cardsInLocation(location: string): any[] {
      let cards = this.populateCards(
        Object.values(this.gamedatas.cards).filter((card: any) => {
          return card.location.startsWith(location);
        })
      );
      let order = this.locationOrder[location] || "letter";
      let sorter = firstBy(order);
      if (order != "letter") {
        sorter = sorter.thenBy("letter");
      }
      sorter = sorter.thenBy("id");
      cards.sort(sorter);
      return cards;
    },

    nextOrderInLocation(location: string): number {
      const cards = this.cardsInLocation(location);
      const max: number = cards.map((card: any) => card.order).reduce((acc: number, cur: number) => Math.max(acc, cur), -1);
      return max + 1;
    },

    populateCard(card) {
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

      // Genre benefits
      newCard.genreBenefitsList = [];
      if (newCard.genreBenefits) {
        for (const id in newCard.genreBenefits) {
          let value = newCard.genreBenefits[id];
          if (newCard.factor > 1) {
            value = `<span class="font-bold px-1 bg-yellow-400">${value * newCard.factor}</span>`;
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
          player.colorBg = "bg-yellow-500";
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
    async animateCard(card: any, delay: number, changes: any): Promise<number> {
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

      // Optional delay
      if (delay) {
        await sleep(delay);
      }

      let mode = null;
      if (start && !visible) {
        // Compute end position
        mode = "leave";
        if (card.location.startsWith("discard_")) {
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
      console.log(`Take action ${action}`, data);
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
          `<a target="hdefine" href="https://dictionary.cambridge.org/dictionary/english/${q}">Cambridge</a>`, //
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
      console.log(`State ${stateName}`, args);
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
          text() {
            if (this.gamestate.name == "purchase") {
              return `Purchase ${this.gamestate.args.coins} Ink (End Turn)`;
            }
            return "Skip";
          },
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
        doctor: {
          text() {
            return this.game.format_string_recursive("Purchase ${points}${icon} for ${coins}¢", this.gamestate.args.advert);
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
          let text = action.text;
          if (typeof text == "function") {
            text = text.apply(this);
          }
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
        let delay = 0;
        const promises = cards.map((card) => this.animateCard(card, (delay += 0)));
        Promise.allSettled(promises).then((results) => {
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

    clickAll(location): void {
      this.cardsInLocation(location).forEach((card) => {
        this.emitter.emit("requestClickCard", card.id);
      });
    },

    resetAll(): void {
      this.handWildCards.forEach((card) => {
        this.clickFooter({ card: card, action: { action: "reset" } });
      });
    },

    clickCard(e): void {
      let { action, card } = e;
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

    clickFooter(e): void {
      let { action, card } = e;
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
