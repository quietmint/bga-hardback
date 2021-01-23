<template>
  <div :id="'card' + this.card.id" :class="this.card.invisible ? 'invisible' : ''" class="cardholder relative">
    <!-- Card -->
    <div @click="clickCard()" :class="cardClass" class="card shadow relative rounded-lg select-none mt-1 mx-1">
      <div class="cardface front rounded-lg">
        <!-- Bookmark -->
        <div :class="bookmarkClass" class="bookmark absolute flex items-center text-center font-bold leading-none">
          <Icon v-if="card.genreName != 'starter'" :icon="card.genreName" class="icon" />
          <div v-if="card.cost" class="pt-1">{{ card.cost }}¢</div>
          <div v-if="card.points" class="pt-1">{{ card.points }}<Icon icon="star" class="inline star" /></div>
        </div>

        <!-- Letter -->
        <div :class="letterClass" class="absolute letter text-center leading-none" :title="card.letter + ' (' + card.genreName + ')'">
          {{ card.letter }}
        </div>

        <div :class="benefitClass" class="absolute">
          <!-- Basic Benefits -->
          <ul title="Basic benefits always activate">
            <li v-for="benefit in card.basicBenefitsList" :key="benefit.id" class="hanging"><span v-html="benefit.text"></span><Icon v-if="benefit.icon" :icon="benefit.icon" class="inline text-lg" /><span v-html="benefit.text2"></span><Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline text-lg" /></li>
          </ul>

          <!-- Genre Benefits -->
          <div v-if="card.genreBenefitsList.length" :class="textClass" class="flex items-center border-t border-gray-900" :title="'Genre benefits activate if you play multiple ' + card.genreName + ' cards'">
            <Icon :icon="card.genreName" class="text-2xl flex-none" />
            <ul class="border-l border-gray-900 ml-1 py-1 pl-1">
              <li v-for="benefit in card.genreBenefitsList" :key="benefit.id" class="hanging"><span v-html="benefit.text"></span><Icon v-if="benefit.icon" :icon="benefit.icon" class="inline text-lg" /><span v-html="benefit.text2"></span><Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline text-lg" /></li>
            </ul>
          </div>
        </div>

        <!-- ID -->
        <div class="absolute bottom-1 right-1 text-xs text-gray-400">{{ card.origin }} (#{{ card.id }}/{{ card.order }})</div>
      </div>

      <!-- Wild -->
      <div class="cardface back rounded-lg">
        <div v-if="card.wild" class="absolute wildletter text-center leading-none top-11 w-full">
          {{ card.wild }}
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div v-if="footerActions && footerActions.length > 0" class="flex items-start justify-evenly text-center text-sm">
      <div v-for="action in footerActions" :key="action" @click="clickFooter(action)" :class="action.class" class="px-3 rounded-b-lg transition-all z-0" :title="action.title">{{ action.text }}<Icon v-if="action.icon" :icon="action.icon" class="inline" /></div>
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon } from "@iconify/vue";

const actionButton = "hover:py-1 cursor-pointer font-bold shadow";
const actionBlack = actionButton + " bg-black text-white hover:bg-gray-100 hover:text-black";
const actionBlue = actionButton + " bg-blue-700 text-white hover:bg-blue-100 hover:text-blue-700";
const actionRed = actionButton + " bg-red-700 text-white hover:bg-red-100 hover:text-red-700";
const actionRef = {
  ink: {
    action: "useRemover",
    text: "REMOVE INK",
    class: actionBlack,
  },
  wild: {
    action: "wild",
    text: "WILD",
    class: actionBlue,
  },
  reset: {
    action: "reset",
    text: "RESET",
    class: actionBlue,
  },
  uncover: {
    action: "uncover",
    text: "UNCOVER",
    class: actionBlue,
  },
  double: {
    action: "double",
    text: "DOUBLE",
    class: actionBlue,
  },
  trash: {
    action: "trash",
    text: "TRASH",
    class: actionRed,
  },
  jailJail: {
    action: "jail",
    actionArgs: {
      choice: "jail",
    },
    text: "JAIL",
    class: actionBlue,
  },
  jailTrash: {
    action: "jail",
    actionArgs: {
      choice: "trash",
    },
    text: "TRASH",
    class: actionRed,
  },
  eitherCoins: {
    action: "either",
    actionArgs: {
      benefit: null,
      choice: "coins",
    },
    class: actionBlue,
  },
  eitherPoints: {
    action: "either",
    actionArgs: {
      benefit: null,
      choice: "points",
    },
    icon: "star",
    class: actionBlue,
  },
  eitherInk: {
    action: "either",
    actionArgs: {
      benefit: Constants.EITHER_INK,
      choice: "ink",
    },
    text: "Ink",
    class: actionBlue,
  },
  eitherRemover: {
    action: "either",
    actionArgs: {
      benefit: Constants.EITHER_INK,
      choice: "remover",
    },
    text: "Remover",
    class: actionBlue,
  },
  purchase: {
    action: "purchase",
    text: "PURCHASE",
    class: actionBlue,
  },
};

export default {
  name: "HCard",
  emits: ["clickCard", "clickFooter"],
  inject: ["gamestate"],
  components: {
    Icon,
  },
  props: {
    card: {
      type: Object,
      required: true,
    },
  },
  computed: {
    cardClass(): string {
      let c = "card-" + this.card.genreName + " ";
      c += this.clickAction ? "cursor-pointer " : "cursor-not-allowed ";
      c += this.card.timeless ? "timeless w-60 h-44 " : "w-44 h-60 ";
      if (this.card.ink) {
        c += "ring ring-gray-900 ";
      } else if (this.card.location == "jail" || this.card.origin.startsWith("timeless")) {
        c += "ring " + this.card.player.colorRing;
      } else if (this.card.wild) {
        c += "wild ";
      }
      return c;
    },

    bookmarkClass(): string {
      let c = this.card.timeless ? "flex-row bottom-2 left-0 w-20 h-7 " : "flex-col top-1 left-2 w-7 h-20 ";
      switch (this.card.genre) {
        case Constants.ADVENTURE:
          return c + "text-yellow-900";
        case Constants.HORROR:
          return c + "text-green-100";
        case Constants.ROMANCE:
          return c + "text-red-100";
        case Constants.MYSTERY:
          return c + "text-blue-100";
        default:
          return c + "text-white";
      }
    },

    letterClass(): string {
      return "letter-" + this.card.letter + (this.card.timeless ? " top-0 w-28" : " top-8 w-full");
    },

    benefitClass(): string {
      return this.card.timeless ? "top-8 bottom-0 right-1 w-28 pl-1" : "bottom-0 left-0 right-0 h-24 px-2 pb-1";
    },

    textClass(): string {
      switch (this.card.genre) {
        case Constants.ADVENTURE:
          return "text-yellow-900";
        case Constants.HORROR:
          return "text-green-700";
        case Constants.ROMANCE:
          return "text-red-700";
        case Constants.MYSTERY:
          return "text-blue-700";
      }
    },

    clickAction(): any {
      //this.takeAction("preview", { lock: false, cardId: card.id, location: card.location, order: card.order });
      if (this.gamestate.active) {
        if (this.gamestate.name == "playerTurn") {
          let destination = null;
          if (this.card.location == "tableau") {
            destination = this.card.origin;
          } else if (this.card.location.startsWith("hand") || this.card.location.startsWith("timeless")) {
            destination = "tableau";
          }
          if (destination) {
            return { action: "move", destination: destination };
          }
        } else if (this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return actionRef.purchase;
        }
      }
    },

    footerActions(): any[] {
      const handActions = this.card.ink ? [actionRef.ink] : this.card.wild ? [actionRef.reset] : [actionRef.wild];

      if (this.card.location.startsWith("hand")) {
        return handActions;
      }

      if (this.card.location == "tableau" && this.gamestate.active) {
        if (this.gamestate.name == "playerTurn" && !this.card.origin.startsWith("timeless")) {
          return handActions;
        } else if (this.gamestate.name == "uncover" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.uncover];
        } else if (this.gamestate.name == "double" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.double];
        } else if (this.gamestate.name == "trash" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.trash];
        } else if (this.gamestate.name.startsWith("either") && this.gamestate.args.possible.hasOwnProperty(this.card.id)) {
          const p = this.gamestate.args.possible[this.card.id];
          if (p.benefit == Constants.EITHER_INK) {
            return [actionRef.eitherInk, actionRef.eitherRemover];
          } else {
            let eitherCoins = Object.assign({ text: p.amount + "¢" }, actionRef.eitherCoins);
            eitherCoins.actionArgs.benefit = p.benefit;
            let eitherPoints = Object.assign({ text: p.amount }, actionRef.eitherPoints);
            eitherPoints.actionArgs.benefit = p.benefit;
            return [eitherCoins, eitherPoints];
          }
        }
      }

      if (this.card.origin.startsWith("timeless")) {
        return [
          {
            action: null,
            text: this.card.player.name + " ",
            icon: "timeless",
            title: "This timeless classic card provides benefits for " + this.card.player.name + " each turn",
            class: this.card.player.colorBg + " " + this.card.player.colorBgText,
          },
        ];
      }

      if (this.card.location == "offer" && this.gamestate.active) {
        if (this.gamestate.name == "jail") {
          return [actionRef.jailJail, actionRef.jailTrash];
        } else if (this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.purchase];
        }
      }

      if (this.card.location == "jail") {
        if (this.gamestate.active && this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.purchase];
        } else {
          return [
            {
              action: null,
              text: this.card.player.name + " ",
              icon: "jail",
              title: "This jailed card may only be purchased by " + this.card.player.name,
              class: this.card.player.colorBg + " " + this.card.player.colorBgText,
            },
          ];
        }
      }
    },
  },
  methods: {
    clickCard(): void {
      let action: any = this.clickAction;
      if (action) {
        let card: any = this.card;
        this.emitter.emit("clickCard", { action, card });
      }
    },
    clickFooter(action): void {
      if (action.action) {
        let card: any = this.card;
        this.emitter.emit("clickFooter", { action, card });
      }
    },
  },
};
</script>
