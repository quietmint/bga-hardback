<template>
  <div>
    <HPlayerPanel v-for="(player, id) in players" :key="id" :player="player" />
    <div class="hidden">
      <Icon id="iconStarter" icon="genre" style="font-size: 20px; vertical-align: middle" />
      <Icon id="iconAdventure" icon="adventure" style="font-size: 20px; vertical-align: middle" />
      <Icon id="iconHorror" icon="horror" style="font-size: 20px; vertical-align: middle" />
      <Icon id="iconMystery" icon="mystery" style="font-size: 20px; vertical-align: middle" />
      <Icon id="iconRomance" icon="romance" style="font-size: 20px; vertical-align: middle" />
    </div>
    <div class="flex">
      <div class="flex-grow">
        <div class="container-hand bg-opacity-50 rounded-lg my-2 p-2" :class="currentPlayer.colorBg">
          <div class="mb-1">
            <b>My Hand</b> &mdash; Sort:
            <div class="inline-grid grid-cols-4 rounded-lg bg-gray-50 bg-opacity-50 divide-x border text-sm text-center cursor-pointer" :class="sorterClass">
              <div class="whitespace-nowrap px-1 mx-1" @click="sort(handLocation, 'shuffle')"><Icon icon="shuffle" class="inline text-lg" /> Shuffle</div>
              <div class="whitespace-nowrap px-1 mx-1" @click="sort(handLocation, 'letter')"><Icon icon="sortAZ" class="inline text-lg" /> Letter</div>
              <div class="whitespace-nowrap px-1 mx-1" @click="sort(handLocation, 'cost')"><Icon icon="sort09" class="inline text-lg" /> Cost</div>
              <div class="whitespace-nowrap px-1 mx-1" @click="sort(handLocation, 'genre')"><Icon icon="genre" class="inline text-lg" /> Genre</div>
            </div>
          </div>
          <HCardList :cards="handCards" :location="handLocation" :checkDrag="checkDragHand" @click="clickCard" @clickFooter="clickFooter" />
        </div>

        <div class="container-tableau rounded-lg my-2 p-2">
          <HCardList :cards="tableauCards" location="tableau" :checkDrag="checkDragTableau" @click="clickCard" @clickFooter="clickFooter" />
        </div>

        <div class="container-offer bg-gray-700 bg-opacity-30 rounded-lg my-2 p-2">
          <div class="mb-1">
            <b>Offer Row</b> &mdash; Sort:
            <div class="inline-grid grid-cols-4 rounded-lg bg-gray-50 bg-opacity-50 divide-x divide-gray-800 border border-gray-800 text-sm text-center cursor-pointer">
              <div class="whitespace-nowrap px-1 mx-1" @click="sort('offer', 'shuffle')"><Icon icon="shuffle" class="inline text-lg" /> Shuffle</div>
              <div class="whitespace-nowrap px-1 mx-1" @click="sort('offer', 'letter')"><Icon icon="sortAZ" class="inline text-lg" /> Letter</div>
              <div class="whitespace-nowrap px-1 mx-1" @click="sort('offer', 'cost')"><Icon icon="sort09" class="inline text-lg" /> Cost</div>
              <div class="whitespace-nowrap px-1 mx-1" @click="sort('offer', 'genre')"><Icon icon="genre" class="inline text-lg" /> Genre</div>
            </div>
          </div>
          <HCardList :cards="offerCards" location="offer" :checkDrag="checkDragTimeless" @click="clickCard" />
        </div>
      </div>

      <div class="flex-none w-64 bg-gray-700 bg-opacity-30 rounded-lg ml-2 my-2 p-2">
        <b>Timeless Classics</b>
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

import alphabeticalVariant from "@iconify-icons/mdi/alphabetical-variant";
addIcon("sortAZ", alphabeticalVariant);

import numericIcon from "@iconify-icons/mdi/numeric";
addIcon("sort09", numericIcon);

import bookmarkIcon from "@iconify-icons/mdi/bookmark";
addIcon("genre", bookmarkIcon);

import helpRhombus from "@iconify-icons/mdi/help-rhombus";
import helpRhombusOutline from "@iconify-icons/mdi/help-rhombus-outline";
addIcon("wild", helpRhombusOutline);

export default {
  name: "HGame",
  components: { Icon, HCardList, HPlayerPanel },

  data() {
    return {
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
      return this.gamedatas.gamestate;
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

    sorterClass() {
      return this.currentPlayer.colorDivide + " " + this.currentPlayer.colorBorder;
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
      switch (player.color) {
        case "ff0000": // red
          player.colorName = "red";
          player.colorBg = "bg-red-700";
          player.colorBorder = "border-red-800";
          player.colorDivide = "divide-red-800";
          player.colorText = "text-red-700";
          break;
        case "008000": // green
          player.colorName = "green";
          player.colorBg = "bg-green-700";
          player.colorBorder = "border-green-800";
          player.colorDivide = "divide-green-800";
          player.colorText = "text-green-700";
          break;
        case "0000ff": // blue
          player.colorName = "blue";
          player.colorBg = "bg-blue-700";
          player.colorBorder = "border-blue-800";
          player.colorDivide = "divide-blue-800";
          player.colorText = "text-blue-700";
          break;
        case "ffa500": // yellow
          player.colorName = "yellow";
          player.colorBg = "bg-yellow-600";
          player.colorBorder = "border-yellow-700";
          player.colorDivide = "divide-yellow-700";
          player.colorText = "text-yellow-600";
          break;
        case "982fff": // purple
          player.colorName = "purple";
          player.colorBg = "bg-purple-700";
          player.colorBorder = "border-purple-800";
          player.colorDivide = "divide-purple-800";
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
      if (args.genreName) {
        args.genreName = this.genreIcon(args.genreName);
      }
      return log;
    },

    genreIcon(log: String) {
      log = log.replaceAll("Starter", document.getElementById("iconStarter").outerHTML);
      log = log.replaceAll("Adventure", document.getElementById("iconAdventure").outerHTML);
      log = log.replaceAll("Horror", document.getElementById("iconHorror").outerHTML);
      log = log.replaceAll("Mystery", document.getElementById("iconMystery").outerHTML);
      log = log.replaceAll("Romance", document.getElementById("iconRomance").outerHTML);
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
      if (stateName == "playerTurn" || stateName == "cleanup") {
        this.game.addActionButton(
          "ink_button",
          "Use Ink to Draw",
          () => {
            this.takeAction("useInk");
          },
          null,
          false,
          "gray"
        );

        if (this.isCurrentPlayerActive()) {
          this.game.addActionButton("confirmWord_button", "Confirm Word", () => {
            let cardIds = this.cardIds(this.tableauCards);
            let wildMask = this.tableauCards.map((card) => card.wild || "_").join("");
            this.takeAction("confirmWord", { cardIds, wildMask });
          });

          this.game.addActionButton(
            "skipTurn_button",
            "Skip Turn",
            () => {
              this.takeAction("skipTurn");
            },
            null,
            false,
            "red"
          );
        }
      }
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
