<template>
  <div>
    <HPlayerPanel v-for="(player, id) in players" :key="id" :player="player" />

    <!-- Icons for log message replacement -->
    <div class="hidden">
      <Icon v-for="icon in logIcons" :key="icon" :id="'logIcon_' + icon" :icon="icon" style="font-size: 20px; vertical-align: middle" />
    </div>

    <div class="flex">
      <div class="flex-grow">
        <div class="container-hand bg-opacity-50 rounded-lg my-2 p-2" :class="currentPlayer.colorBg">
          <div class="flex flex-wrap justify-end mb-1">
            <b class="flex-grow">My Hand ({{ handCards.length }})</b>

            <div v-if="currentPlayer.ink && currentPlayer.deckCount" class="flex ml-1 rounded-lg divide-x border text-sm text-center cursor-pointer" :class="buttonGroupClass">
              <div class="rounded-lg" :class="buttonClass" @click="takeAction('useInk')"><Icon icon="ink" class="inline text-lg" /> Spend Ink to Draw</div>
            </div>

            <div v-if="handCards.length > 1 && isCurrentPlayerActive()" class="flex ml-1 rounded-lg divide-x border text-sm text-center cursor-pointer" :class="buttonGroupClass">
              <div class="rounded-lg" :class="buttonClass" @click="clickAll(handLocation, handCards)"><Icon icon="clickAll" class="inline text-lg" /> Play All</div>
            </div>

            <div v-if="handCards.length > 1" class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap" :class="buttonGroupClass">
              <div class="rounded-l-lg" :class="buttonHeaderClass">Order:</div>
              <div :class="buttonClass" @click="sort(handLocation, 'letter')" title="By Letter"><Icon icon="sortAZ" class="text-lg" /></div>
              <div :class="buttonClass" @click="sort(handLocation, 'cost')" title="By Cost"><Icon icon="sort09" class="text-lg" /></div>
              <div :class="buttonClass" @click="sort(handLocation, 'genre')" title="By Genre"><Icon icon="genre" class="text-lg" /></div>
              <div class="rounded-r-lg" :class="buttonClass" @click="sort(handLocation, 'shuffle')" title="Shuffle"><Icon icon="shuffle" class="text-lg" /></div>
            </div>
          </div>

          <HCardList :cards="handCards" :location="handLocation" :checkDrag="checkDragHand" @click="clickCard" @clickFooter="clickFooter" />
        </div>

        <div class="container-tableau rounded-lg my-2 p-2">
          <div class="flex flex-wrap justify-end mb-1">
            <b class="flex-grow">Word ({{ tableauCards.length }})</b>

            <div v-if="tableauCards.length" class="flex ml-1 rounded-lg divide-x border text-sm text-center cursor-pointer" :class="buttonGroupClass">
              <div class="rounded-lg" :class="buttonClass" @click="clickAll('tableau', tableauCards)"><Icon icon="close" class="inline text-lg" /> Reset</div>
            </div>
          </div>

          <HCardList :cards="tableauCards" location="tableau" :checkDrag="checkDragTableau" @click="clickCard" @clickFooter="clickFooter" />
        </div>

        <div class="container-offer bg-gray-700 bg-opacity-30 rounded-lg my-2 p-2">
          <div class="flex flex-wrap justify-end mb-1">
            <b class="flex-grow">Offer Row ({{ offerCards.length }})</b>

            <div class="flex ml-1 rounded-lg divide-x border text-sm text-center whitespace-nowrap" :class="buttonGroupClass">
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

      <div class="flex-none w-64 bg-gray-700 bg-opacity-30 rounded-lg ml-2 my-2 p-2">
        <b>Timeless Classics ({{ timelessCards.length }})</b>
        <HCardList :cards="timelessCards" location="timeless" :checkDrag="checkDragTimeless" @click="clickCard" />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
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

import mdiMagnify from "@iconify-icons/mdi/magnify";
addIcon("mystery", mdiMagnify);

import mdiRotate3dVariant from "@iconify-icons/mdi/rotate-3d-variant";
addIcon("flip", mdiRotate3dVariant);

import mdiSkull from "@iconify-icons/mdi/skull";
addIcon("horror", mdiSkull);

import mdiStar from "@iconify-icons/mdi/star-outline";
addIcon("star", mdiStar);

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
addIcon("wild", helpRhombusOutline);

export default {
  name: "HGame",
  components: { Icon, HCardList, HPlayerPanel },

  data() {
    return {
      logIcons: ["starter", "adventure", "horror", "mystery", "romance", "star"],
      gamedatas: {
        players: {},
        refs: {
          cards: {},
          benefits: {},
        },
        locations: {},
        gamestate: {},
      },
      dragMoveEvt: null,
    };
  },

  mounted() {
    this.emitter.on("clickFooter", this.clickFooter);
  },

  computed: {
    state() {
      console.log("computed state", this.gamedatas.gamestate);
      return this.gamedatas.gamestate;
    },

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
      return this.populateCards(this.gamedatas.locations[this.handLocation]) || [];
    },

    tableauCards() {
      return this.populateCards(this.gamedatas.locations.tableau) || [];
    },

    timelessCards() {
      return this.populateCards(this.gamedatas.locations.timeless) || [];
    },

    offerCards() {
      return this.populateCards(this.gamedatas.locations.offer) || [];
    },

    buttonGroupClass() {
      return this.currentPlayer.colorDivide + " " + this.currentPlayer.colorBorder;
    },

    buttonHeaderClass() {
      return "pl-2 pr-1 py-1 font-bold " + this.currentPlayer.colorHeader;
    },

    buttonClass() {
      return "px-2 py-1 cursor-pointer " + this.currentPlayer.colorButton;
    },
  },

  methods: {
    /*
     * Utility functions
     */
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

    isCurrentPlayerActive() {
      return this.game.isCurrentPlayerActive();
    },

    getActivePlayerId() {
      return this.game.getActivePlayerId();
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
        const el = document.getElementById("logIcon_" + args.icon.toLowerCase());
        console.log("look for icon element", "logIcon_" + args.icon.toLowerCase(), el);
        if (el) {
          args.icon = el.outerHTML;
        }
      }
      return log;
    },

    onEnteringState(stateName, args) {
      console.log("Vue onEnteringState", stateName, args);
    },

    onLeavingState(stateName) {
      console.log("Vue onLeavingState", stateName);
    },

    onUpdateActionButtons(stateName, args) {
      console.log("Vue onUpdateActionButtons", stateName, args);

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
      if (!this.isCurrentPlayerActive()) {
        return;
      }

      let possible = this.state.possibleactions;
      console.log("possible", possible, stateName, this.state);

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

    onNotify(notif) {
      console.log("Vue onNotify", notif);
      if (notif.type == "cards") {
        for (const location in notif.args.locations) {
          this.gamedatas.locations[location] = notif.args.locations[location];
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
      if (this.isCurrentPlayerActive() && toLocation == "tableau") {
        return;
      }
      return false;
    },

    checkDragTimeless(card: any, fromLocation: String, toLocation: String) {
      // Active player can move cards to the tableau
      return this.isCurrentPlayerActive() && toLocation == "tableau";
    },

    checkDragTableau(card: any, fromLocation: String, toLocation: String) {
      // Active player can reorder the tableau and return cards to their origin
      return this.isCurrentPlayerActive() && (toLocation == fromLocation || toLocation == card.origin);
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
      let cards = this.gamedatas.locations[location];
      if (order == "shuffle") {
        for (let i = cards.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [cards[i], cards[j]] = [cards[j], cards[i]];
        }
      } else {
        let sortLetter = (a, b) => {
          let cardA = this.populateCard(a);
          let cardB = this.populateCard(b);
          return cardA.letter.localeCompare(cardB.letter);
        };
        let sortCost = (a, b) => {
          let cardA = this.populateCard(a);
          let cardB = this.populateCard(b);
          return cardA.cost - cardB.cost;
        };
        let sortGenre = (a, b) => {
          let cardA = this.populateCard(a);
          let cardB = this.populateCard(b);
          return cardA.genre - cardB.genre;
        };
        if (order == "cost") {
          cards.sort(firstBy(sortCost).thenBy(sortLetter).thenBy("id"));
        } else if (order == "genre") {
          cards.sort(firstBy(sortGenre).thenBy(sortLetter).thenBy("id"));
        } else {
          cards.sort(firstBy(sortLetter).thenBy("id"));
        }
      }
    },

    clickCard(evt) {
      let { location, card } = evt;
      console.log("clickCard event in parent", location, card.id);
      if (this.isCurrentPlayerActive()) {
        let destination = null;
        if (location == this.handLocation || location.startsWith("timeless")) {
          destination = "tableau";
        } else if (location == "tableau") {
          destination = card.origin;
        }
        if (destination) {
          this.gamedatas.locations[location] = this.gamedatas.locations[location].filter((c) => c.id != card.id);
          this.gamedatas.locations[destination].push(card);
        }
      }
    },

    clickAll(location, cards) {
      console.log("clickAll", cards);
      if (this.isCurrentPlayerActive()) {
        cards.forEach((card) => {
          this.clickCard({ location, card });
        });
      }
    },

    clickFooter(evt) {
      let { action, location, card } = evt;
      console.log("clickFooter event in parent", action, location, card.id);
      if (action == "ink") {
        let cardId = card.id;
        this.takeAction("useRemover", { cardId });
      } else if (action == "flipWild") {
        let wild = (prompt("What letter does this wild card represent?") || "").trim().toUpperCase();
        const regex = RegExp("^[A-Z]$");
        if (regex.test(wild)) {
          let cardId = card.id;
          let cards = this.gamedatas.locations[location] || [];
          cards.forEach((c) => {
            if (c.id == cardId) {
              c.wild = wild;
            }
          });
        }
      } else if (action == "flipUnwild") {
        let cardId = card.id;
        let cards = this.gamedatas.locations[location] || [];
        cards.forEach((c) => {
          if (c.id == cardId) {
            c.wild = false;
          }
        });
      }
    },
  },
};
</script>
