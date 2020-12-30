<template>
  <div>
    <div>{{ message }}</div>
    <div>
      <button @click="sortCards" class="bg-blue-500 hover:bg-blue-700 focus:ring text-white font-bold py-2 px-4 rounded mx-2 text-gray-50">Sort</button>
      <button @click="shuffleCards" class="bg-blue-500 hover:bg-blue-700 focus:ring ztext-white font-bold py-2 px-4 rounded mx-2 text-gray-50">Shuffle</button>
    </div>
    <HCardList :cards="handCards" :location="handLocation" :clickCard="clickCard" :checkDrag="checkDragHand" @drag="drag" />
    <HCardList :cards="tableauCards" location="tableau" :clickCard="clickCard" :checkDrag="checkDragTableau" @drag="drag" />
    <HCardList :cards="timelessCards" location="timeless" :clickCard="clickCard" :checkDrag="checkDragTimeless" @drag="drag" />
    <HCardList :cards="offerCards" location="offer" :clickCard="clickCard" :checkDrag="checkDragTimeless" @drag="drag" />
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import HCardList from "./HCardList.vue";

export default {
  name: "HGame",
  components: {
    HCardList,
  },
  data() {
    return {
      message: "Hello Vue!",
      gamedatas: {
        players: {},
        refs: {
          cards: {},
          benefits: {},
        },
        locations: {},
      },
      dragMoveEvt: null,
    };
  },

  computed: {
    state() {
      return this.gamedatas.gamestate;
    },

    handLocation() {
      return "hand" + this.game.player_id;
    },

    handCards() {
      return this.populateCards(this.gamedatas.locations[this.handLocation]);
    },

    tableauCards() {
      return this.populateCards(this.gamedatas.locations.tableau);
    },

    timelessCards() {
      return this.populateCards(this.gamedatas.locations.timeless);
    },

    offerCards() {
      return this.populateCards(this.gamedatas.locations.offer);
    },
  },

  methods: {
    /*
     * BGA framework methods
     */
    takeAction(action, data, callback) {
      data = data || {};
      for (const key in data) {
        let val = data[key];
        if (Array.isArray(val)) {
          data[key] = val.join(",");
        }
      }
      data.lock = true;
      callback = callback || function (res) {};
      var gameName = this.game.name();
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
    onEnteringState(stateName, args) {
      console.log("Vue onEnteringState", stateName, args);
    },

    onLeavingState(stateName) {
      console.log("Vue onLeavingState", stateName);
    },

    onUpdateActionButtons(stateName, args) {
      console.log("Vue onUpdateActionButtons", stateName, args);
      if (stateName == "playerTurn") {
      }
    },

    onNotify(notif) {
      console.log("Vue onNotify", notif);
      if (notif.type == "cards") {
        //this.game.notifqueue.setSynchronousDuration(500);
        for (const location in notif.args.locations) {
          this.gamedatas.locations[location] = notif.args.locations[location];
        }
      } else {
        console.warn("Vue unknown notification type", notif.type);
      }
    },

    /*
     * Other functions
     */

    populateCard(card) {
      console.log("try to populate card", card);
      let newCard = Object.assign({}, this.gamedatas.refs.cards[card.refId], card);
      newCard.benefitsList = [];
      if (newCard.benefits) {
        for (const id in newCard.benefits) {
          let newBenefit = Object.assign({}, this.gamedatas.refs.benefits[id]);
          newBenefit.id = id;
          newBenefit.text = newBenefit.text.replaceAll("%", newCard.benefits[id]);
          //newBenefit.text = newBenefit.text.replaceAll("*", '<Icon icon="star" class="inline" />');
          newCard.benefitsList.push(newBenefit);
        }
      }
      newCard.genreBenefitsList = [];
      if (newCard.genreBenefits) {
        for (const id in newCard.genreBenefits) {
          let newBenefit = Object.assign({}, this.gamedatas.refs.benefits[id]);
          newBenefit.id = id;
          newBenefit.text = newBenefit.text.replaceAll("%", newCard.genreBenefits[id]);
          //newBenefit.text = newBenefit.text.replaceAll("*", '<Icon icon="star" class="inline" />');
          newCard.genreBenefitsList.push(newBenefit);
        }
      }
      return newCard;
    },

    populateCards(cards) {
      if (!Array.isArray(cards)) {
        return cards;
      }
      return cards.map(this.populateCard);
    },

    sortCards() {
      console.log("sortCards");
      let array = this.gamedatas.hand;
      array.sort(function (a, b) {
        if (a.letter < b.letter) {
          return -1;
        } else if (a.letter > b.letter) {
          return 1;
        }
        return 0;
      });
    },

    shuffleCards() {
      console.log("shuffleCards");
      let array = this.gamedatas.hand;
      for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
      }
    },

    checkDragHand(card: any, fromLocation: String, toLocation: String) {
      // Players can always reorder their hand at any time
      // Active player can move cards to the tableau
      return toLocation == fromLocation || (this.isCurrentPlayerActive() && toLocation == "tableau");
    },

    checkDragTimeless(card: any, fromLocation: String, toLocation: String) {
      // Active player can move cards to the tableau
      return this.isCurrentPlayerActive() && toLocation == "tableau";
    },

    checkDragTableau(card: any, fromLocation: String, toLocation: String) {
      // Active player can reorder the tableau and return cards to their origin
      return this.isCurrentPlayerActive() && (toLocation == fromLocation || toLocation == card.origin);
    },

    drag(evt) {
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
          console.log("dragMove", this.dragMoveEvt);
          this.takeAction("dragMove", this.dragMoveEvt);
          this.dragMoveEvt = null;
        }
      } else if (evt.event == "order") {
        console.log("dragOrder", evt);
        this.takeAction("dragOrder", evt);
      }
    },
  },
};
</script>
