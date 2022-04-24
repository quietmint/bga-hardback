<template>
  <div :id="'cardholder_' + this.card.id" @pointerdown="pointStart" @pointerup="pointStop" @pointercancel="pointStop" class="cardholder relative" :class="holderClass" ref="cardholder">
    <!-- Header -->
    <div v-if="header" class="flex items-start justify-evenly text-center text-14 whitespace-nowrap leading-5">
      <div class="px-2 rounded-t-lg z-10" :class="header.class" :title="header.title"><Icon v-if="header.icon" :icon="header.icon" class="inline text-15" /> {{ header.text }}</div>
    </div>

    <!-- Card -->
    <div @click="clickCard" :class="cardClass" class="card shadow relative rounded-lg">
      <div class="cardface front rounded-lg">
        <!-- Bookmark -->
        <div :class="bookmarkClass" class="bookmark absolute flex items-center text-center font-bold leading-none whitespace-nowrap">
          <Icon v-if="card.genreName != 'starter'" :icon="card.genreName" class="icon" />
          <div v-if="card.cost">{{ card.cost }}¢</div>
          <div v-if="card.points">{{ card.points }}<Icon icon="star" class="inline star" /></div>
        </div>

        <!-- Letter -->
        <div :class="letterClass" class="absolute letter text-center leading-none">
          {{ letterDisplay }}
        </div>

        <!-- Benefits -->
        <div @pointerenter="tooltipEnter" @pointerleave="tooltipLeave" class="benefits absolute text-115 leading-120 tracking-tight font-bold text-center flex flex-col flex-grow whitespace-nowrap" ref="benefits">
          <!-- Basic Benefits -->
          <div :class="basicSectionClass" class="flex-grow flex items-center justify-evenly">
            <div v-for="benefit in basicBenefitsList" :key="benefit.id" class="rounded-lg px-1 bg-opacity-50 border border-opacity-30 bg-white border-black" v-html="benefit.html"></div>
          </div>

          <!-- Genre Benefits -->
          <div v-if="genreBenefitsList.length" :class="genreSectionClass" class="flex-grow flex items-center justify-evenly border-t-2 border-black">
            <div v-for="benefit in genreBenefitsList" :key="benefit.id" :class="genreBubbleClass" class="rounded-lg px-1 bg-opacity-50 border border-opacity-30 bg-white" v-html="benefit.html"></div>
          </div>
        </div>

        <!-- Tooltip -->
        <teleport to="#HGame" v-if="tooltip.visible">
          <div class="absolute z-top shadow bg-white text-black ring-2 ring-black rounded-lg overflow-hidden" :style="{ top: tooltip.top, left: tooltip.left, maxWidth: '240px' }" ref="tooltip">
            <div :class="titleClass" class="px-2 py-1 text-110 font-bold border-b-2 border-black"><Icon :icon="card.genreName" class="inline text-125" /> {{ i18n(card.genreName) }} {{ card.letter }}</div>
            <table>
              <tr v-for="benefit in basicBenefitsList" :key="benefit.id">
                <td class="px-2 py-1 whitespace-nowrap text-center">
                  <div class="text-110 leading-120 tracking-tight font-bold rounded-lg px-1 bg-opacity-50 border border-opacity-30 bg-white border-black" v-html="benefit.html"></div>
                </td>
                <td class="pr-2 py-1" v-html="benefit.htmlLong"></td>
              </tr>
              <tr v-if="genreBenefitsList.length">
                <td class="px-2 py-1 italic border-t-2 border-black" colspan="2" :class="genreTooltipClass" v-text="i18n('genreTip', { x: i18n(card.genreName) })"></td>
              </tr>
              <tr v-for="benefit in genreBenefitsList" :key="benefit.id" :class="genreTooltipClass">
                <td class="px-2 py-1 whitespace-nowrap text-center">
                  <div class="text-110 leading-120 tracking-tight font-bold rounded-lg px-1 bg-opacity-50 border border-opacity-30 bg-white border-black" :class="genreBubbleClass" v-html="benefit.html"></div>
                </td>
                <td class="pr-2 py-1" v-html="benefit.htmlLong"></td>
              </tr>
            </table>
          </div>
        </teleport>
      </div>

      <!-- Wild -->
      <div class="cardface back rounded-lg">
        <div v-if="card.wild" class="absolute wildletter text-center leading-none w-full">
          {{ card.wild }}
        </div>
        <div v-if="card.wild" class="absolute bottom-1 w-full flex flex-grow justify-evenly text-16 bold">
          <div :class="titleClass" class="rounded-lg px-2 bg-opacity-80"><Icon :icon="card.genreName" class="icon inline text-105" />{{ card.letter }}</div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="h-7 leading-7 flex items-start justify-evenly text-center text-14 whitespace-nowrap">
      <div :id="'tut_a' + index + '_c' + this.card.id" v-for="(action, index) in footerActions" :key="action" @click="clickFooter(action)" :class="action.class" class="rounded-b-lg z-10">{{ action.text }}<Icon v-if="action.icon" :icon="action.icon" class="inline text-15" /></div>
    </div>
  </div>
</template>

<script lang="ts">
import HConstants from "./HConstants.js";
import { Icon } from "@iconify/vue";
import { firstBy } from "thenby";
import { nextTick } from "vue";

export default {
  name: "HCard",
  emits: ["clickCard", "clickFooter", "dragStart"],
  inject: ["gamestate", "getRect", "i18n", "myself", "prefs", "refs"],
  components: { Icon },

  props: {
    card: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      dragTimer: null,
      tooltip: {
        timeout: null,
        visible: false,
        top: "0px",
        left: "-999px",
      },
    };
  },

  mounted() {
    const holder: HTMLElement = this.$refs.benefits;
    if (!window.PointerEvent) {
      // Old Safari?
      holder.addEventListener("mouseenter", this.tooltipEnter, false);
      holder.addEventListener("mouseleave", this.tooltipLeave, false);
    }
  },

  beforeUnmount() {
    const holder: HTMLElement = this.$refs.benefits;
    if (!window.PointerEvent) {
      // Old Safari?
      holder.removeEventListener("mouseenter", this.tooltipEnter, false);
      holder.removeEventListener("mouseleave", this.tooltipLeave, false);
    }
  },

  computed: {
    basicBenefitsList() {
      let list = [];
      for (const id in this.card.basicBenefits) {
        let value = this.card.basicBenefits[id];
        if (this.card.factor > 1) {
          value = `<span class="font-bold px-1 bg-yellow-400">${value * this.card.factor}</span>`;
        }
        list.push({
          id: parseInt(id),
          html: this.i18n(this.refs.value.benefits[id].short, { value }),
          htmlLong: this.i18n(this.refs.value.benefits[id].long, { value }),
        });
      }
      list.sort(firstBy("id"));
      return list;
    },

    genreBenefitsList() {
      let list = [];
      for (const id in this.card.genreBenefits) {
        let value = this.card.genreBenefits[id];
        if (this.card.factor > 1) {
          value = `<span class="font-bold px-1 bg-yellow-400">${value * this.card.factor}</span>`;
        }
        list.push({
          id: parseInt(id),
          html: this.i18n(this.refs.value.benefits[id].short, { value }),
          htmlLong: this.i18n(this.refs.value.benefits[id].long, { value }),
        });
      }
      list.sort(firstBy("id"));
      return list;
    },

    letterDisplay(): string {
      if (this.card.letter == "I" && this.card.genre == HConstants.ROMANCE) {
        return "|";
      }
      return this.card.letter;
    },

    holderClass(): string {
      let c = this.card.dragging ? "invisible " : "";
      c += this.card.ink || this.card.location == "jail" || this.card.origin.startsWith("timeless") ? "mx-2 mb-1 mt-2" : "m-1 mt-2";
      return c;
    },

    cardClass(): string {
      let c = "card-" + this.card.genreName + " ";
      c += this.dragLocations ? "touch-none cursor-move " : this.clickAction ? "cursor-pointer " : "cursor-not-allowed ";
      c += this.card.timeless ? "timeless " : "";
      if (this.card.ink) {
        c += "ring ring-black ";
      } else if (this.card.location == "jail" || this.card.origin.startsWith("timeless")) {
        c += `ring ${this.card.player.colorRing} `;
      } else if (this.card.wild) {
        c += "wild ";
      }
      return c;
    },

    titleClass(): string {
      return `${HConstants.GENRES[this.card.genre].bg} ${HConstants.GENRES[this.card.genre].textLight}`;
    },

    bookmarkClass(): string {
      let c = this.card.timeless ? "flex-row " : "flex-col ";
      return c + HConstants.GENRES[this.card.genre].textLight;
    },

    letterClass(): string {
      return "letter-" + this.card.letter;
    },

    basicSectionClass(): string {
      return this.card.timeless ? "flex-col" : "flex";
    },

    genreSectionClass(): string {
      return `${this.basicSectionClass} ${HConstants.GENRES[this.card.genre].text} ${HConstants.GENRES[this.card.genre].bg} bg-opacity-25`;
    },

    genreBubbleClass(): string {
      return `${HConstants.GENRES[this.card.genre].border}`;
    },

    genreTooltipClass(): string {
      return `${HConstants.GENRES[this.card.genre].text} ${HConstants.GENRES[this.card.genre].bg} bg-opacity-25`;
    },

    header() {
      if (this.card.origin.startsWith("timeless")) {
        return {
          text: this.card.player.name,
          icon: "timeless",
          title: this.i18n("timelessTip", { player_name: this.card.player.name }),
          class: `${this.card.player.colorBg} ${this.card.player.colorTextLight}`,
        };
      } else if (this.card.location == "jail") {
        return {
          text: this.card.player.name,
          icon: "jail",
          title: this.i18n("jailTip", { player_name: this.card.player.name }),
          class: `${this.card.player.colorBg} ${this.card.player.colorTextLight}`,
        };
      } else if (this.card.player && this.card.player.id === 0) {
        return {
          text: this.card.player.name,
          icon: "jail",
          title: this.i18n("jailTip", { player_name: this.card.player.name }),
          class: `${this.card.player.colorBg} ${this.card.player.colorTextLight}`,
        };
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
      const actionBlack = "button mx-1 black shadow";
      const actionBlue = "button mx-1 blue shadow";
      const actionRed = "button mx-1 red shadow";
      const actionRef = {
        inkText: {
          text: this.i18n("ink"),
          class: "uppercase font-bold px-2 rounded-t-lg z-10 bg-black text-white leading-5",
        },
        inkButton: {
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
          // dynamic text
          class: actionBlue,
        },
        double: {
          action: "double",
          text: this.i18n("doubleButton"),
          class: actionBlue,
        },
        previewDraw: {
          action: "previewDraw",
          text: this.i18n("previewButton"),
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
            benefitId: HConstants.EITHER_INK,
            choice: "ink",
          },
          text: this.i18n("ink"),
          class: actionBlue,
        },
        eitherRemover: {
          action: "either",
          actionArgs: {
            benefitId: HConstants.EITHER_INK,
            choice: "remover",
          },
          text: this.i18n("remover"),
          class: actionBlue,
        },
        purchase: {
          action: "purchase",
          // dynamic text
          class: actionBlue,
        },
      };

      if (this.gamestate.active) {
        if (this.gamestate.name == "uncover" && this.gamestate.args.cardIds.includes(this.card.id)) {
          const text = this.i18n("uncoverButton", { x: this.card.letter });
          const uncover = Object.assign({ text }, actionRef.uncover);
          return [uncover];
        } else if (this.gamestate.name == "double" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.double];
        } else if (this.gamestate.name == "trash" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return [actionRef.trash];
        } else if (this.gamestate.name == "trashDiscard" && this.card.location.startsWith("discard")) {
          const text = this.i18n("trashCoinsButton", { coins: this.gamestate.args.amount });
          const trashDiscard = Object.assign({ text }, actionRef.trashDiscard);
          return [trashDiscard];
        } else if ((this.gamestate.name == "trash" || this.gamestate.name == "trashDiscard" || this.gamestate.name == "specialRomancePrompt") && this.gamestate.args.previewDraw == this.card.id) {
          return [actionRef.previewDraw];
        } else if (this.gamestate.name == "specialRomance" && this.card.location.startsWith("hand")) {
          return [actionRef.previewReturn, actionRef.previewDiscard];
        } else if (this.gamestate.name.startsWith("either") && this.gamestate.args.sourceId == this.card.id) {
          if (this.gamestate.args.benefit == HConstants.EITHER_INK) {
            return [actionRef.eitherInk, actionRef.eitherRemover];
          } else {
            const eitherCoins = {
              action: "either",
              actionArgs: {
                benefitId: this.gamestate.args.benefit,
                choice: "coins",
              },
              text: `${this.gamestate.args.amount}¢`,
              class: actionBlue,
            };
            const eitherPoints = {
              action: "either",
              actionArgs: {
                benefitId: this.gamestate.args.benefit,
                choice: "points",
              },
              text: this.gamestate.args.amount,
              icon: "star",
              class: actionBlue,
            };
            return [eitherCoins, eitherPoints];
          }
        } else if (this.gamestate.name == "jail" && this.card.location == "offer") {
          let jailJail = actionRef.jailJail;
          if (this.gamestate.args.jail) {
            const confirmation = this.i18n("jailWarning", this.gamestate.args.jail);
            jailJail = Object.assign({ confirmation }, actionRef.jailJail);
          }
          return [jailJail, actionRef.jailTrash];
        } else if (this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
          const text = this.i18n("purchaseButton", { coins: this.card.cost });
          const purchase = Object.assign({ text }, actionRef.purchase);
          return [purchase];
        }
      }

      if (this.card.location == "jail" && this.gamestate.active && this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
        return [actionRef.purchase];
      }

      if (this.card.ink) {
        if (this.myself.value.remover > 0 && (this.card.location.startsWith("hand") || (this.card.location == "tableau" && this.gamestate.active && this.gamestate.name == "playerTurn"))) {
          return [actionRef.inkButton];
        } else {
          return [actionRef.inkText];
        }
      }

      if (!this.card.origin.startsWith("timeless") && (this.card.location.startsWith("hand") || (this.card.location == "tableau" && this.gamestate.active && this.gamestate.name == "playerTurn"))) {
        let reset = {
          action: "reset",
          text: this.i18n("resetButton", { x: this.card.letter }),
          class: actionBlue,
        };
        return this.card.wild ? [reset] : [actionRef.wild];
      }
    },

    dragLocations() {
      if (this.prefs.value[HConstants.PREF_DRAG_DROP] !== HConstants.DRAG_DROP_DISABLED) {
        if (this.gamestate.active && this.gamestate.name == "playerTurn") {
          if (this.card.location == "tableau") {
            return ["tableau", this.card.origin];
          } else if (this.card.location.startsWith("hand") || this.card.location.startsWith("timeless")) {
            return ["tableau", this.card.location];
          }
        } else if (!this.gamestate.active) {
          if (this.card.location.startsWith("hand")) {
            return [this.card.location];
          }
        }
      }
    },
  },

  methods: {
    clickCard(ev: MouseEvent): void {
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

    /*
     * Drag and drop
     */

    pointStart(ev: PointerEvent): void {
      // Start the timer (which prevents click event and starts dragging)
      if (this.dragLocations) {
        console.log(`Pre-drag card ${this.card.id} start`);
        this.dragTimeout = setTimeout(() => this.dragStart(ev), 250);
      }
    },

    pointStop(ev: PointerEvent): void {
      // Stop the timer
      if (this.dragTimeout) {
        console.log(`Pre-drag card ${this.card.id} stop (via ${ev.type})`);
        clearTimeout(this.dragTimeout);
        this.dragTimeout = null;
      }
    },

    dragStart(ev: PointerEvent): void {
      if (this.dragTimeout) {
        // Stop the timer
        clearTimeout(this.dragTimeout);
        this.dragTimeout = null;

        // Start dragging
        console.log(`Pre-drag card ${this.card.id}`, this.dragLocations);
        if (this.dragLocations) {
          const el: HTMLElement = this.$refs.cardholder;
          this.emitter.emit("dragStart", { ev, el, cardId: this.card.id, locations: this.dragLocations });
        }
      }
    },

    /*
     * Tooltip
     */
    tooltipEnter(ev: MouseEvent) {
      if (this.prefs.value[HConstants.PREF_TOOLTIPS] == HConstants.TOOLTIPS_ENABLED) {
        this.tooltip.timeout = setTimeout(() => this.tooltipShow(), HConstants.TOOLTIP_TIMEOUT);
      }
    },

    tooltipLeave(ev: MouseEvent) {
      clearTimeout(this.tooltip.timeout);
      this.tooltip.timeout = null;
      this.tooltip.visible = false;
    },

    async tooltipShow() {
      this.tooltip.visible = true;
      this.tooltip.top = "0px";
      this.tooltip.left = "-999px";
      await nextTick();
      const tip: HTMLElement = this.$refs.tooltip;
      const holder: HTMLElement = this.$refs.benefits;
      const holderRect = this.getRect(holder);
      const tipRect = this.getRect(tip);
      const parentRect = this.getRect(tip.offsetParent);
      const padding = 8;

      // Compute tooltip coords relative to document
      let top = holderRect.top - tipRect.height - padding * 3;
      let left = holderRect.left + (holderRect.width - tipRect.width) / 2;

      // Adjust coords relative to offset parent
      top -= parentRect.top;
      left -= parentRect.left;

      // Adjust coords to prevent overflow
      const overflowLeft = left - padding;
      const overflowRight = document.documentElement.clientWidth - (left + tipRect.width + padding);
      if (overflowLeft < 0) {
        left -= overflowLeft;
      } else if (overflowRight < 0) {
        left += overflowRight;
      }

      this.tooltip.top = top + "px";
      this.tooltip.left = left + "px";
    },
  },
};
</script>
