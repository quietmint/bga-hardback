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
        <div :class="letterClass" class="absolute letter text-center leading-none" :title="card.letter">
          {{ card.letter }}
        </div>

        <div :class="benefitClass" class="absolute text-15 overflow-y-auto">
          <!-- Basic Benefits -->
          <ul :title="i18n('basicTip')">
            <li v-for="benefit in card.basicBenefitsList" :key="benefit.id" class="hanging"><span v-html="benefit.html"></span></li>
          </ul>

          <!-- Genre Benefits -->
          <div v-if="card.genreBenefitsList.length" :class="textClass" class="flex items-center border-t border-black" :title="i18n('genreTip', { x: i18n(card.genreName) })">
            <Icon :icon="card.genreName" class="text-24 flex-none" />
            <ul class="border-l border-black ml-1 py-1 pl-1">
              <li v-for="benefit in card.genreBenefitsList" :key="benefit.id" class="hanging"><span v-html="benefit.html"></span></li>
            </ul>
          </div>
        </div>

        <!-- ID -->
        <!-- <div class="absolute top-0 right-0 text-12 text-white" style="text-shadow: 1px 1px black">{{ card.origin }} (#{{ card.id }}/{{ card.order }})</div> -->
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
      <div v-for="action in footerActions" :key="action" @click="clickFooter(action)" :class="action.class" class="px-2 rounded-b-lg transition-all z-10" :title="action.title">{{ action.text }}<Icon v-if="action.icon" :icon="action.icon" class="inline text-15" /></div>
    </div>
  </div>
</template>

<script lang="ts">
import Constants from "./constants.js";
import { Icon } from "@iconify/vue";

export default {
  name: "HCard",
  emits: ["clickCard", "clickFooter"],
  inject: ["gamestate", "i18n"],
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
      return this.card.timeless ? "top-8 bottom-0 right-1 w-28 pl-2" : "bottom-0 left-0 right-0 h-24 px-1 pb-1";
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
          return { action: "purchase" };
        }
      }
    },

    footerActions(): any[] {
      const actionBlack = "button black shadow";
      const actionBlue = "button blue shadow";
      const actionRed = "button red shadow";
      const actionRef = {
        ink: {
          action: "useRemover",
          text: this.i18n("useRemoverButton"),
          class: actionBlack,
        },
        wild: {
          action: "wild",
          text: this.i18n("wildButton"),
          class: actionBlue,
        },
        uncover: {
          action: "uncover",
          text: this.i18n("uncoverButton"),
          class: actionBlue,
        },
        double: {
          action: "double",
          text: this.i18n("doubleButton"),
          class: actionBlue,
        },
        previewReturn: {
          action: "previewReturn",
          text: this.i18n("returnButton"),
          class: actionBlue,
        },
        previewDiscard: {
          action: "previewDiscard",
          text: this.i18n("discardButton"),
          class: actionBlue,
        },
        trash: {
          action: "trash",
          text: this.i18n("trashButton"),
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
          text: this.i18n("jailButton"),
          class: actionBlue,
        },
        jailTrash: {
          action: "jail",
          actionArgs: {
            choice: "trash",
          },
          text: this.i18n("trashButton"),
          class: actionRed,
        },
        eitherInk: {
          action: "either",
          actionArgs: {
            benefitId: Constants.EITHER_INK,
            choice: "ink",
          },
          text: this.i18n("ink"),
          class: actionBlue,
        },
        eitherRemover: {
          action: "either",
          actionArgs: {
            benefitId: Constants.EITHER_INK,
            choice: "remover",
          },
          text: this.i18n("remover"),
          class: actionBlue,
        },
        purchase: {
          action: "purchase",
          text: this.i18n("purchaseButton"),
          class: actionBlue,
        },
      };

      if (this.gamestate.active) {
        if (this.gamestate.name == "uncover" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.uncover];
        } else if (this.gamestate.name == "double" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.double];
        } else if (this.gamestate.name == "trash" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.trash];
        } else if (this.gamestate.name == "specialRomance" && this.card.location.startsWith("hand")) {
          return [actionRef.previewReturn, actionRef.previewDiscard];
        } else if (this.gamestate.name == "trashDiscard" && this.card.location.startsWith("discard")) {
          const text = this.i18n("trashCoinsButton", { coins: this.gamestate.args.amount });
          const trashDiscard = Object.assign({ text }, actionRef.trashDiscard);
          return [trashDiscard];
        } else if (this.gamestate.name.startsWith("either") && this.gamestate.args.possible.hasOwnProperty(this.card.id)) {
          const p = this.gamestate.args.possible[this.card.id];
          if (p.benefit == Constants.EITHER_INK) {
            return [actionRef.eitherInk, actionRef.eitherRemover];
          } else {
            const eitherCoins = {
              action: "either",
              actionArgs: {
                benefitId: p.benefit,
                choice: "coins",
              },
              text: `${p.amount}¢`,
              class: actionBlue,
            };
            const eitherPoints = {
              action: "either",
              actionArgs: {
                benefitId: p.benefit,
                choice: "points",
              },
              text: p.amount,
              icon: "star",
              class: actionBlue,
            };
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
            title: this.i18n("timelessTip", { player_name: this.card.player.name }),
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
              title: this.i18n("jailTip", { player_name: this.card.player.name }),
              class: `leading-6 ${this.card.player.colorBg} ${this.card.player.colorBgText}`,
            },
          ];
        }
      }

      if (this.card.location.startsWith("hand") || (this.card.location == "tableau" && this.gamestate.active && this.gamestate.name == "playerTurn")) {
        let reset = {
          action: "reset",
          text: this.i18n("resetButton", { x: this.card.letter }),
          class: actionBlue,
        };
        return this.card.ink ? [actionRef.ink] : this.card.wild ? [reset] : [actionRef.wild];
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
