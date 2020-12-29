<template>
  <div>
    <div>{{ message }}</div>
    <div>
      <button @click="sortCards" class="bg-blue-500 hover:bg-blue-700 focus:ring text-white font-bold py-2 px-4 rounded mx-2 text-gray-50">Sort</button>
      <button @click="shuffleCards" class="bg-blue-500 hover:bg-blue-700 focus:ring ztext-white font-bold py-2 px-4 rounded mx-2 text-gray-50">Shuffle</button>
      <button @click="addCard" class="bg-blue-500 hover:bg-blue-700 focus:ring ztext-white font-bold py-2 px-4 rounded mx-2 text-gray-50">Add</button>
      <button @click="removeCard" class="bg-blue-500 hover:bg-blue-700 focus:ring ztext-white font-bold py-2 px-4 rounded mx-2 text-gray-50">Remove</button>
    </div>
    <HCardList :cards="cardsInHand" location="hand" />
    <HCardList :cards="tableau" location="tableau" />
    <HCardList :cards="timeless" location="timeless" />
  </div>
</template>

<script>
import Constants from "./constants.js";
import HCardList from "./HCardList.vue";

export default {
  name: "HGame",
  components: {
    HCardList,
  },
  data() {
    return {
      game: null,
      gamedatas: null,
      message: "Hello Vue!",
      hand: [],
      timeless: [
        {
          genre: 3,
          letter: "C",
          cost: 8,
          benefits: {
            1: 1,
          },
          genreBenefits: [],
          id: 403,
          order: 4,
          timeless: true,
          location: "timeless",
        },
        {
          genre: 4,
          letter: "D",
          cost: 7,
          benefits: {
            1: 1,
          },
          genreBenefits: [],
          id: 404,
          order: 4,
          timeless: true,
          location: "timeless",
        },
        {
          genre: 1,
          letter: "T",
          cost: 4,
          points: 1,
          benefits: {
            1: 1,
          },
          genreBenefits: [],
          id: 401,
          order: 4,
          timeless: true,
          location: "timeless",
        },
        {
          genre: 2,
          letter: "X",
          cost: 6,
          benefits: {
            1: 1,
          },
          genreBenefits: [],
          id: 402,
          order: 4,
          timeless: true,
          location: "timeless",
        },
      ],
      tableau: [
        {
          genre: 4,
          letter: "S",
          cost: 4,
          benefits: {
            1: 1,
          },
          genreBenefits: [],
          id: 101,
          order: 4,
          location: "tablau",
        },
        {
          genre: 1,
          letter: "W",
          cost: 8,
          points: 2,
          benefits: {
            1: 1,
          },
          genreBenefits: [],
          id: 102,
          order: 4,
          location: "tablau",
        },
        {
          genre: 2,
          letter: "B",
          cost: 8,
          benefits: {
            1: 1,
          },
          genreBenefits: [],
          id: 103,
          order: 5,
          location: "tablau",
        },
      ],
    };
  },

  computed: {
    state() {
      return this.game.gamedatas.gamestate;
    },

    isCurrentPlayerActive() {
      return this.game.isCurrentPlayerActive();
    },

    getActivePlayerId() {
      return this.game.getActivePlayerId();
    },

    cardsInHand() {
      return this.gamedatas ? this.gamedatas.hand : [];
    },
  },

  methods: {
    /*
     * Event callbacks from BGA framework
     */
    setup(game, gamedatas) {
      console.log("Vue setup", gamedatas);
      this.game = game;
      this.gamedatas = gamedatas;
    },

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

    /*
     * Other functions
     */

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

    randomIndex() {
      return Math.floor(Math.random() * this.gamedatas.hand.length);
    },

    addCard() {
      let r = this.randomIndex();
      let newCard = {
        genre: 2,
        letter: "Q",
        cost: 0,
        benefits: {
          1: 1,
        },
        genreBenefits: [],
        id: 200 + this.gamedatas.hand.length,
        order: 2,
      };
      this.gamedatas.hand.splice(r, 0, newCard);
    },

    removeCard() {
      this.gamedatas.hand.splice(this.randomIndex(), 1);
    },

    takeAction(action, data, callback) {
      data = data || {};
      data.lock = true;
      callback = callback || function (res) {};
      var gameName = this.game.name();
      this.game.ajaxcall("/" + gameName + "/" + gameName + "/" + action + ".html", data, this, callback);
    },
  },
};
</script>
