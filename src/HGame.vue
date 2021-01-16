<template>
  <div>
    <!-- Player panels (moved using teleport) -->
    <HPlayerPanel v-for="(player, id) in players" :key="id" :player="player" />

    <!-- Log message icons-->
    <div class="hidden">
      <Icon v-for="icon in logIcons" :key="icon" :id="'logIcon_' + icon" :icon="icon" style="font-size: 20px; vertical-align: middle" />
    </div>

    <div><b>Gamestate:</b> Active: {{ gamestate.active }} &mdash; Name: {{ gamestate.name }}</div>

    <div class="flex">
      <div class="flex-grow">
        <!-- Hand -->
        <div class="container-hand bg-opacity-50 rounded-lg my-2 p-2" :class="currentPlayer.colorBg">
          <div class="flex flex-wrap justify-end mb-1">
            <b class="flex-grow">My Hand ({{ handCards.length }})</b>

            <div v-if="currentPlayer.ink && (currentPlayer.deckCount || currentPlayer.discardCount)" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
              <div class="rounded-lg" :class="buttonClass" @click="takeAction('useInk')"><Icon icon="ink" class="inline text-lg" /> Draw with Ink</div>
            </div>

            <div v-if="gamestate.active && handCards.length > 1" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
              <div class="rounded-lg" :class="buttonClass" @click="clickAll(handCards)"><Icon icon="clickAll" class="inline text-lg" /> Play All</div>
            </div>

            <div v-if="handCards.length > 1" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
              <div class="rounded-l-lg" :class="buttonHeaderClass">Order:</div>
              <div :class="buttonClass" @click="sort(handLocation, 'letter')" title="By Letter"><Icon icon="sortAZ" class="text-lg" /></div>
              <div :class="buttonClass" @click="sort(handLocation, 'cost')" title="By Cost">¢</div>
              <div :class="buttonClass" @click="sort(handLocation, 'genre')" title="By Genre"><Icon icon="genre" class="text-lg" /></div>
              <div class="rounded-r-lg" :class="buttonClass" @click="sort(handLocation, 'shuffle')" title="Shuffle"><Icon icon="shuffle" class="text-lg" /></div>
            </div>
          </div>

          <HCardList :cards="handCards" :location="handLocation" :checkDrag="checkDragHand" @click="clickCard" @clickFooter="clickFooter" />
        </div>

        <!-- Tableau -->
        <div class="container-tableau rounded-lg my-2 p-2">
          <div class="flex flex-wrap justify-end mb-1">
            <b class="flex-grow">Word ({{ tableauCards.length }})</b>

            <div v-if="gamestate.active && tableauCards.length" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
              <div class="rounded-lg" :class="buttonClass" @click="clickAll(tableauCards)"><Icon icon="close" class="inline text-lg" /> Reset</div>
            </div>
          </div>

          <HCardList :cards="tableauCards" location="tableau" :checkDrag="checkDragTableau" @click="clickCard" @clickFooter="clickFooter" />
        </div>

        <!-- Offer -->
        <div class="container-offer bg-gray-700 bg-opacity-30 rounded-lg my-2 p-2">
          <div class="flex flex-wrap justify-end mb-1">
            <b class="flex-grow">Offer Row ({{ offerCards.length }})</b>

            <div class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap leading-6" :class="buttonGroupClass">
              <div class="rounded-l-lg font-bold" :class="buttonHeaderClass">Order:</div>
              <div :class="buttonClass" @click="sort('offer', 'letter')" title="By Letter"><Icon icon="sortAZ" class="text-lg" /></div>
              <div :class="buttonClass" @click="sort('offer', 'cost')" title="By Cost"><Icon icon="sort09" class="inline text-lg" /></div>
              <div :class="buttonClass" @click="sort('offer', 'genre')" title="By Genre"><Icon icon="genre" class="text-lg" /></div>
              <div class="rounded-r-lg" :class="buttonClass" @click="sort('offer', 'shuffle')" title="Shuffle"><Icon icon="shuffle" class="text-lg" /></div>
            </div>
          </div>

          <HCardList :cards="offerCards" location="offer" :checkDrag="checkDragTimeless" @click="clickCard" />
        </div>
      </div>

      <!-- Timeless -->
      <div class="flex-none w-64 bg-gray-700 bg-opacity-30 rounded-lg ml-2 my-2 p-2">
        <b>Timeless Classics ({{ timelessCards.length }})</b>
        <HCardList :cards="timelessCards" location="timeless" :checkDrag="checkDragTimeless" @click="clickCard" />
      </div>
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

import mdiCards from "@iconify-icons/mdi/cards";
addIcon("cards", mdiCards);

import mdiChevronDoubleRight from "@iconify-icons/mdi/chevron-double-right";
addIcon("chevron", mdiChevronDoubleRight);

import mdiCompass from "@iconify-icons/mdi/compass";
addIcon("adventure", mdiCompass);

import mdiDeleteEmptyOutline from "@iconify-icons/mdi/delete-empty-outline";
addIcon("trash", mdiDeleteEmptyOutline);

import mdiFlaskEmptyPlus from "@iconify-icons/mdi/flask-empty-plus";
addIcon("ink", mdiFlaskEmptyPlus);

import mdiFlaskEmptyRemoveOutline from "@iconify-icons/mdi/flask-empty-remove-outline";
addIcon("remover", mdiFlaskEmptyRemoveOutline);

import mdiHeart from "@iconify-icons/mdi/heart";
addIcon("romance", mdiHeart);

import magnifyingGlass from "@iconify-icons/ps/magnifying-glass";
addIcon("mystery", magnifyingGlass);

import mdiRotate3dVariant from "@iconify-icons/mdi/rotate-3d-variant";
addIcon("flip", mdiRotate3dVariant);

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

import bookmarkIcon from "@iconify-icons/mdi/bookmark";
addIcon("starter", bookmarkIcon);
addIcon("genre", bookmarkIcon);

import closeIcon from "@iconify-icons/mdi/close";
addIcon("close", closeIcon);

import cursorDefaultClick from "@iconify-icons/mdi/cursor-default-click";
import expandAllOutline from "@iconify-icons/mdi/expand-all-outline";
//import plusBoxMultipleOutline from '@iconify-icons/mdi/plus-box-multiple-outline';
addIcon("clickAll", expandAllOutline);

import helpRhombus from "@iconify-icons/mdi/help-rhombus";
import helpRhombusOutline from "@iconify-icons/mdi/help-rhombus-outline";
import helpCircleOutline from "@iconify-icons/mdi/help-circle-outline";
addIcon("wild", helpCircleOutline);

import refreshIcon from "@iconify-icons/mdi/refresh";
addIcon("reset", refreshIcon);

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

    currentPlayer() {
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
      return cards;
    },

    buttonGroupClass() {
      return this.currentPlayer.colorDivide + " " + this.currentPlayer.colorBorder;
    },

    buttonHeaderClass() {
      return "pl-2 pr-1 font-bold " + this.currentPlayer.colorHeader;
    },

    buttonClass() {
      return "leading-6 px-2 cursor-pointer " + this.currentPlayer.colorButton;
    },
  },

  methods: {
    /*
     * Utility functions
     */

    cardsInLocation(location: string): Array<any> {
      return this.populateCards(
        Object.values(this.gamedatas.cards).filter((card: any) => {
          return card.location == location;
        })
      );
    },

    nextOrderInLocation(location: string): number {
      const cards = this.cardsInLocation(location);
      const max = cards.map((card) => card.order).reduce((acc, cur) => Math.max(acc, cur), -1);
      return max + 1;
    },

    sorter(location) {
      let order = this.locationOrder[location] || "order";
      return firstBy(order).thenBy("letter").thenBy("id");
    },

    populateCard(card) {
      let newCard = Object.assign({}, this.gamedatas.refs.cards[card.refId], card);
      newCard.basicBenefitsList = [];
      if (newCard.basicBenefits) {
        for (const id in newCard.basicBenefits) {
          let value = newCard.basicBenefits[id];
          let newBenefit = Object.assign({}, this.gamedatas.refs.benefits[id]);
          newBenefit.id = id;
          if (newBenefit.text) {
            newBenefit.text = newBenefit.text.replaceAll("%", value);
          }
          if (newBenefit.text2) {
            newBenefit.text2 = newBenefit.text2.replaceAll("%", value);
          }
          if (newBenefit.text3) {
            newBenefit.text3 = newBenefit.text3.replaceAll("%", value);
          }
          newCard.basicBenefitsList.push(newBenefit);
        }
      }
      newCard.genreBenefitsList = [];
      if (newCard.genreBenefits) {
        for (const id in newCard.genreBenefits) {
          let value = newCard.genreBenefits[id];
          let newBenefit = Object.assign({}, this.gamedatas.refs.benefits[id]);
          newBenefit.id = id;
          if (newBenefit.text) {
            newBenefit.text = newBenefit.text.replaceAll("%", value);
          }
          if (newBenefit.text2) {
            newBenefit.text2 = newBenefit.text2.replaceAll("%", value);
          }
          if (newBenefit.text3) {
            newBenefit.text3 = newBenefit.text3.replaceAll("%", value);
          }
          newCard.genreBenefitsList.push(newBenefit);
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

    cardIds(cards): Array<Number> {
      if (!Array.isArray(cards)) {
        return null;
      }
      return cards.map((card) => card.id);
    },

    populatePlayer(player) {
      switch (player.colorName) {
        case "red":
          player.colorName = "red";
          player.colorBg = "bg-red-700";
          player.colorBorder = "border-red-800";
          player.colorDivide = "divide-red-800";
          player.colorText = "text-red-700";
          player.colorButton = "bg-red-100 hover:bg-red-300 active:bg-red-500";
          player.colorHeader = "bg-red-50 " + player.colorText;
          break;
        case "green":
          player.colorBg = "bg-green-700";
          player.colorBorder = "border-green-800";
          player.colorDivide = "divide-green-800";
          player.colorText = "text-green-700";
          player.colorButton = "bg-green-100 hover:bg-green-300 active:bg-green-500";
          player.colorHeader = "bg-green-50 " + player.colorText;
          break;
        case "blue":
          player.colorBg = "bg-blue-700";
          player.colorBorder = "border-blue-800";
          player.colorDivide = "divide-blue-800";
          player.colorText = "text-blue-700";
          player.buttonGroupClass = "bg-blue-100 hover:bg-blue-300 active:bg-blue-500";
          player.colorHeader = "bg-blue-50 " + player.colorText;
          break;
        case "yellow":
          player.colorBg = "bg-yellow-600";
          player.colorBorder = "border-yellow-700";
          player.colorDivide = "divide-yellow-700";
          player.colorText = "text-yellow-600";
          player.buttonGroupClass = "bg-yellow-100 hover:bg-yellow-300 active:bg-yellow-500";
          player.colorHeader = "bg-yellow-50 " + player.colorText;
          break;
        case "purple":
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
    async animateCard(card, changes) {
      if (changes) {
        card = Object.assign({}, card, changes);
      }

      const oldCard = this.gamedatas.cards[card.id];
      if (oldCard != null && oldCard.location == card.location) {
        // Same location, no animation
        this.gamedatas.cards[card.id] = card;
        return;
      }

      let cardEl = null;
      let start = null;

      // Compute start position
      if (oldCard != null) {
        cardEl = document.getElementById(oldCard.location + "_card" + oldCard.id);
        if (cardEl) {
          start = cardEl.getBoundingClientRect();
        }
      }
      let visibleLocations = [this.handLocation, "tableau", "offer", "timeless"];

      if (start && !visibleLocations.includes(card.location)) {
        // Compute end position
        let end = null;
        if (card.location.includes("_")) {
          let playerId = card.location.split("_")[1];
          let boardEl = document.getElementById("player_board_" + playerId);
          if (boardEl) {
            end = boardEl.getBoundingClientRect();
          }
        }
        if (end == null) {
          // Leave top
          console.warn("destroy animation unknown end??", card.id, card.location);
          end = { x: start.x, y: -start.height, width: start.width, height: start.height };
        }

        // Compute forward transform
        const destX = end.x - start.x + (end.width - start.width) / 2;
        const destY = end.y - start.y + (end.height - start.width) / 2;
        const tr = "translate(" + destX + "px, " + destY + "px)";

        // Animate leave
        cardEl.style.transition = "none";
        cardEl.style.transform = "";
        requestAnimationFrame(() => {
          console.log("Animate card leave", cardEl.id, tr);
          cardEl.style.transition = "";
          cardEl.style.transform = tr;
          cardEl.addEventListener("transitionend", () => {
            console.log("Animate card leave destroy", cardEl.id);
            this.gamedatas.cards[card.id] = card;
          });
        });
      } else if (visibleLocations.includes(card.location)) {
        // Compute end position
        this.gamedatas.cards[card.id] = card;
        await nextTick();
        cardEl = document.getElementById(card.location + "_card" + card.id);
        if (cardEl) {
          let end = cardEl.getBoundingClientRect();
          if (start == null) {
            // Enter top
            start = { x: end.x, y: -end.height, width: end.width, height: end.height };
          }

          // Compute reverse transform
          const startX = start.x - end.x;
          const startY = start.y - end.y;
          const tr = "translate(" + startX + "px, " + startY + "px)"; // translate(50%)

          // Transform back to start
          cardEl.style.transition = "none";
          cardEl.style.transform = tr;
          console.log("Animate card enter/move", cardEl.id, tr);

          // Animate enter/move
          requestAnimationFrame(() => {
            cardEl.style.transition = "";
            cardEl.style.transform = "";
          });
        } else {
          console.warn("No enter animation because new card is missing???", card.location + "_card" + card.id);
        }
      } else {
        // Invisible card movement, no animation
        this.gamedatas.cards[card.id] = card;
      }
    },

    /*
     * BGA framework methods
     */
    takeAction(action, data, callback) {
      data = data || {};
      data.lock = true;
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
    onFormatString(log: String, args) {
      if (args.word) {
        args.word = "<b>" + args.word + "</b>";
      }
      if (args.letter) {
        args.letter = "<b>" + args.letter + "</b>";
      }
      if (args.icon) {
        const el = document.getElementById("logIcon_" + args.icon.toLowerCase().trim());
        if (el) {
          args.icon = el.outerHTML;
        }
      }
      if (args.icon2) {
        const el = document.getElementById("logIcon_" + args.icon2.toLowerCase().trim());
        if (el) {
          args.icon2 = el.outerHTML;
        }
      }
      return log;
    },

    onUpdateActionButtons(stateName, args) {
      console.log("Vue onUpdateActionButtons", stateName, args);
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
      };

      // No actions for inactive players
      if (!this.gamestate.active) {
        return;
      }

      let possible: string[] = this.gamestate.possibleactions;
      possible.forEach((p, index) => {
        const action = actionRef[p];
        if (action) {
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

    onEnteringState(stateName, args) {
      console.log("Vue onEnteringState", stateName, args);
      Object.assign(this.gamestate, this.game.gamedatas.gamestate, { active: this.game.isCurrentPlayerActive() });
    },

    onLeavingState(stateName) {
      console.log("Vue onLeavingState", stateName);
    },

    onNotify(notif) {
      console.log("Vue onNotify", notif);
      if (notif.type == "cards") {
        console.log("packet of changed cards", Object.keys(notif.args.cards));
        for (const cardId in notif.args.cards) {
          this.animateCard(notif.args.cards[cardId]);
        }
      } else if (notif.type == "invalid") {
        if (this.game.player_id == notif.args.player_id) {
          this.game.showMessage(notif.args.word + " is not a valid word", "error");
        }
      } else if (notif.type == "panel") {
        this.gamedatas.players[notif.args.player.id] = notif.args.player;
        this.game.scoreCtrl[notif.args.player.id].toValue(notif.args.player.score);
      } else {
        console.warn("Vue unknown notification type", notif.type);
      }
    },

    /*
     * Other functions
     */

    checkDragHand(card: any, fromLocation: String, toLocation: String) {
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

    checkDragTimeless(card: any, fromLocation: String, toLocation: String) {
      // Active player can move cards to the tableau
      return this.gamestate.active && toLocation == "tableau";
    },

    checkDragTableau(card: any, fromLocation: String, toLocation: String) {
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

    sort(location, order: String) {
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

    clickAll(cards) {
      console.log("clickAll", cards);
      if (this.gamestate.active) {
        cards.forEach((card) => {
          this.clickCard({ card });
        });
      }
    },

    clickCard(evt) {
      let { card } = evt;
      console.log("clickCard", card.id, this.gamestate);
      if (this.gamestate.active) {
        let destination = null;
        if (card.location == this.handLocation || card.location.startsWith("timeless")) {
          this.animateCard(card, {
            location: "tableau",
            order: this.nextOrderInLocation("tableau"),
          });
        } else if (card.location == "tableau") {
          this.animateCard(card, {
            location: card.origin,
            order: this.nextOrderInLocation(card.origin),
          });
        } else if (card.location == "offer" && this.gamestate.name == "purchase") {
          this.takeAction("purchase", { cardId: card.id });
        }
      }
    },

    clickFooter(evt) {
      let { action, card } = evt;
      console.log("clickFooter event in parent", action, card.id);
      if (action == "wild") {
        let wild = (prompt("What letter does this wild card represent?") || "").trim().toUpperCase();
        const regex = RegExp("^[A-Z]$");
        if (regex.test(wild)) {
          this.gamedatas.cards[card.id].wild = wild;
        }
      } else if (action == "reset") {
        this.gamedatas.cards[card.id].wild = false;
      } else {
        this.takeAction(action, { cardId: card.id });
      }
    },
  },
};
</script>
