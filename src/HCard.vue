<template>
  <div :id="'card' + this.card.id" :class="this.card.invisible ? 'invisible' : ''" class="cardholder relative">
    <!-- Card -->
    <div @click="clickCard()" :class="cardClass" class="card shadow relative rounded-lg select-none mt-2 mx-1">
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

        <div :class="benefitClass" class="absolute text-15">
          <!-- Basic Benefits -->
          <ul title="Basic benefits always activate">
            <li v-for="benefit in card.basicBenefitsList" :key="benefit.id" class="hanging"><span v-html="benefit.text"></span><Icon v-if="benefit.icon" :icon="benefit.icon" class="inline text-17" /><span v-html="benefit.text2"></span><Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline text-17" /></li>
          </ul>

          <!-- Genre Benefits -->
          <div v-if="card.genreBenefitsList.length" :class="textClass" class="flex items-center border-t border-black" :title="'Genre benefits activate if you play multiple ' + card.genreName + ' cards'">
            <Icon :icon="card.genreName" class="text-24 flex-none" />
            <ul class="border-l border-black ml-1 py-1 pl-1">
              <li v-for="benefit in card.genreBenefitsList" :key="benefit.id" class="hanging"><span v-html="benefit.text"></span><Icon v-if="benefit.icon" :icon="benefit.icon" class="inline text-17" /><span v-html="benefit.text2"></span><Icon v-if="benefit.icon2" :icon="benefit.icon2" class="inline text-17" /></li>
            </ul>
          </div>
        </div>

        <!-- ID -->
        <div class="absolute top-0 right-0 text-12 text-white" style="text-shadow: 1px 1px black">{{ card.origin }} (#{{ card.id }}/{{ card.order }})</div>
      </div>

      <!-- Wild -->
      <div class="cardface back rounded-lg">
        <div v-if="card.wild" class="absolute wildletter text-center leading-none top-10 w-full">
          {{ card.wild }}
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div v-if="footerActions && footerActions.length > 0" class="flex items-start justify-evenly text-center text-13 s mb-1">
      <div v-for="action in footerActions" :key="action" @click="clickFooter(action)" :class="action.class" class="px-3 rounded-b-lg transition-all z-10" :title="action.title">{{ action.text }}<Icon v-if="action.icon" :icon="action.icon" class="inline" /></div>
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon } from "@iconify/vue";

const actionBlack = "button black shadow";
const actionBlue = "button blue shadow";
const actionRed = "button red shadow";
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
  trashDiscard: {
    action: "trashDiscard",
    // dynamic text
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
      benefitId: null,
      choice: "coins",
    },
    // dynamic text
    class: actionBlue,
  },
  eitherPoints: {
    action: "either",
    actionArgs: {
      benefitId: null,
      choice: "points",
    },
    // dynamic text
    icon: "star",
    class: actionBlue,
  },
  eitherInk: {
    action: "either",
    actionArgs: {
      benefitId: Constants.EITHER_INK,
      choice: "ink",
    },
    text: "Ink",
    class: actionBlue,
  },
  eitherRemover: {
    action: "either",
    actionArgs: {
      benefitId: Constants.EITHER_INK,
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
  components: { Icon },

  props: {
    card: {
      type: Object,
      required: true,
    },
  },

  computed: {
    cardClass(): string {
      let c = "card-" + this.card.genreName + " ";
      c += this.card.draggable ? "cursor-ew-resize " : this.clickAction ? "cursor-pointer " : "cursor-not-allowed ";
      c += this.card.timeless ? "timeless " : "";
      if (this.card.ink) {
        c += "mx-2 ring ring-black ";
      } else if (this.card.location == "jail" || this.card.origin.startsWith("timeless")) {
        c += "mx-2 ring " + this.card.player.colorRing;
      } else if (this.card.wild) {
        c += "wild ";
      }
      return c;
    },

    bookmarkClass(): string {
      let c = this.card.timeless ? "flex-row w-20 h-7 " : "flex-col w-7 h-20 ";
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
      return "letter-" + this.card.letter + (this.card.timeless ? " top-0 w-28" : " top-7 w-full");
    },

    benefitClass(): string {
      return this.card.timeless ? "top-8 bottom-0 right-1 w-28 pl-2" : "bottom-0 left-0 right-0 h-24 px-2 pb-1";
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
      if (this.gamestate.active) {
        if (this.gamestate.name == "uncover" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.uncover];
        } else if (this.gamestate.name == "double" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.double];
        } else if (this.gamestate.name == "trash" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.trash];
        } else if (this.gamestate.name == "trashDiscard" && this.card.location.startsWith("discard")) {
          const trashDiscard = Object.assign({ text: `TRASH FOR ${this.gamestate.args.amount}¢` }, actionRef.trashDiscard);
          return [trashDiscard];
        } else if (this.gamestate.name.startsWith("either") && this.gamestate.args.possible.hasOwnProperty(this.card.id)) {
          const p = this.gamestate.args.possible[this.card.id];
          if (p.benefit == Constants.EITHER_INK) {
            return [actionRef.eitherInk, actionRef.eitherRemover];
          } else {
            let eitherCoins = Object.assign({ text: `${p.amount}¢` }, actionRef.eitherCoins);
            eitherCoins.actionArgs.benefitId = p.benefit;
            let eitherPoints = Object.assign({ text: p.amount }, actionRef.eitherPoints);
            eitherPoints.actionArgs.benefitId = p.benefit;
            return [eitherCoins, eitherPoints];
          }
        } else if (this.gamestate.name == "jail" && this.card.location == "offer") {
          return [actionRef.jailJail, actionRef.jailTrash];
        } else if (this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.purchase];
        }
      }

      if (this.card.origin.startsWith("timeless")) {
        return [
          {
            action: null,
            text: `${this.card.player.name} `,
            icon: "timeless",
            title: `Timeless Classic: ${this.card.player.name} receives benefits each turn`,
            class: `leading-6 ${this.card.player.colorBg} ${this.card.player.colorBgText}`,
          },
        ];
      }

      if (this.card.location == "jail") {
        if (this.gamestate.active && this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.purchase];
        } else {
          return [
            {
              action: null,
              text: `${this.card.player.name} `,
              icon: "jail",
              title: `Jailed: Only ${this.card.player.name} may purchase`,
              class: `leading-6 ${this.card.player.colorBg} ${this.card.player.colorBgText}`,
            },
          ];
        }
      }

      if (this.card.location.startsWith("hand") || (this.card.location == "tableau" && this.gamestate.active && this.gamestate.name == "playerTurn")) {
        return this.card.ink ? [actionRef.ink] : this.card.wild ? [actionRef.reset] : [actionRef.wild];
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
