<template>
  <div :id="'cardholder_' + this.card.id"
       @pointerdown="pointStart"
       @pointerup="pointStop"
       @pointercancel="pointStop"
       class="cardholder relative"
       :class="holderClass"
       ref="cardholder">
    <!-- Card -->
    <div @click="clickCard"
         :class="cardClass"
         class="card shadow relative rounded-lg">
      <div class="cardface front rounded-lg">
        <!-- Bookmark -->
        <div :class="bookmarkClass"
             class="bookmark absolute flex items-center text-center font-bold leading-none whitespace-nowrap">
          <Icon v-if="card.genreName != 'starter'"
                :icon="card.genreName"
                class="icon" />
          <div v-if="card.cost">{{ card.cost }}¢</div>
          <div v-if="card.points">{{ card.points }}-NO-BREAK-
            <Icon icon="star"
                  class="inline star" />
          </div>
        </div>

        <!-- Letter -->
        <div :class="letterClass"
             class="absolute letter text-center leading-none">
          {{ card.letter }}
        </div>

        <!-- Benefits -->
        <div @pointerenter="tooltipEnter"
             @pointerleave="tooltipLeave"
             class="benefits absolute text-115 leading-120 tracking-tight font-bold text-center flex flex-col grow whitespace-nowrap"
             ref="benefits">
          <!-- Basic Benefits -->
          <div :class="basicSectionClass"
               class="grow flex items-center justify-evenly">
            <div v-for="benefit in basicBenefitsList"
                 :key="benefit.id"
                 class="rounded-lg px-1 border border-black/25 bg-white/50 border-black"
                 v-html="benefit.html"></div>
          </div>

          <!-- Genre Benefits -->
          <div v-if="genreBenefitsList.length"
               :class="genreSectionClass"
               class="grow flex items-center justify-evenly border-t-2">
            <div v-for="benefit in genreBenefitsList"
                 :key="benefit.id"
                 class="rounded-lg px-1 bg-white/50"
                 v-html="benefit.html"></div>
          </div>
        </div>

        <!-- Tooltip -->
        <teleport to="#HGame"
                  v-if="tooltip.visible">
          <div class="absolute z-top shadow bg-white text-black ring-2 ring-black rounded-lg overflow-hidden"
               :style="{ top: tooltip.top, left: tooltip.left, maxWidth: '240px' }"
               ref="tooltip">
            <div :class="titleClass"
                 class="px-2 py-1 text-110 font-bold border-b-2 border-black">
              <Icon :icon="card.genreName"
                    class="inline text-125" /> {{ i18n(card.genreName) }} {{ card.letter }}
            </div>
            <div v-for="benefit in basicBenefitsList"
                 :key="benefit.id"
                 class="px-2 py-1"
                 v-html="benefit.htmlLong"></div>
            <div v-if="genreBenefitsList.length > 0"
                 :class="genreTooltipClass"
                 class="px-1 pt-1 text-90 italic border-t-2 border-black"
                 v-text="i18n('genreTip', { x: i18n(card.genreName) })"></div>
            <div v-for="benefit in genreBenefitsList"
                 :key="benefit.id"
                 :class="genreTooltipClass"
                 class="px-2 py-1"
                 v-html="benefit.htmlLong"></div>
          </div>
        </teleport>
      </div>

      <!-- Wild -->
      <div class="cardface back rounded-lg">
        <div v-if="card.wild"
             class="absolute wildletter text-center leading-none w-full">
          {{ card.wild }}
        </div>
        <div v-if="card.wild"
             class="absolute bottom-1 w-full flex grow justify-evenly text-16 bold">
          <div :class="titleClass"
               class="rounded-lg px-2">
            <Icon :icon="card.genreName"
                  class="icon inline text-105" />{{ card.letter }}
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="h-8 leading-8 flex items-start justify-evenly text-center text-14 whitespace-nowrap">
      <div :id="'tut_a' + index + '_c' + this.card.id"
           v-for="(action, index) in footerActions"
           :key="action"
           @click="clickFooter(action)"
           :title="action.title"
           :class="action.class"
           class="rounded-b-lg z-10">{{ action.text }}
        <Icon v-if="action.icon"
              :icon="action.icon"
              class="inline text-15" />
      </div>
    </div>
  </div>
</template>

<script lang="js">
import HConstants from "./HConstants.js";
import { Icon } from "@iconify/vue";
import { firstBy } from "thenby";
import { nextTick } from "vue";

export default {
  name: "HCard",
  emits: ["clickCard", "clickFooter", "dragStart"],
  inject: ["gamestate", "genreCounts", "getRect", "i18n", "myself", "prefs", "refs"],
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
    const holder = this.$refs.benefits;
    if (!window.PointerEvent) {
      // Old Safari?
      holder.addEventListener("mouseenter", this.tooltipEnter, false);
      holder.addEventListener("mouseleave", this.tooltipLeave, false);
    }
  },

  beforeUnmount() {
    const holder = this.$refs.benefits;
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
          html: this.i18n(this.refs.benefits[id].short, { value }),
          htmlLong: this.i18n(this.refs.benefits[id].long, { value }),
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
          html: this.i18n(this.refs.benefits[id].short, { value }),
          htmlLong: this.i18n(this.refs.benefits[id].long, { value }),
        });
      }
      list.sort(firstBy("id"));
      return list;
    },

    holderClass() {
      let c = this.card.dragging ? "invisible " : "";
      c += this.card.ink || this.card.location.startsWith("jail") || this.card.origin.startsWith("timeless") ? "mx-2 mb-1 mt-2" : "m-1 mt-2";
      return c;
    },

    cardClass() {
      let c = "card-" + this.card.genreName + " ";
      c += this.dragLocations ? "touch-none cursor-move " : this.clickAction ? "cursor-pointer " : "cursor-not-allowed ";
      c += this.card.timeless ? "timeless " : "";
      if (this.card.ink) {
        c += "ring ring-black ";
      } else if (this.card.location.startsWith("jail") || this.card.origin.startsWith("timeless")) {
        c += `ring ${this.card.player.colorRing} `;
      } else if (this.card.wild) {
        c += "wild ";
      }
      return c;
    },

    titleClass() {
      return `${HConstants.GENRES[this.card.genre].bg80} ${HConstants.GENRES[this.card.genre].textLight}`;
    },

    bookmarkClass() {
      let c = this.card.timeless ? "flex-row " : "flex-col ";
      return c + HConstants.GENRES[this.card.genre].textLight;
    },

    letterClass() {
      return "letter-" + this.card.letter;
    },

    basicInactive() {
      return this.card.location.startsWith("tableau") && !this.card.triggering;
    },

    basicSectionClass() {
      let c = this.card.timeless ? "flex-col " : "flex ";
      if (this.basicInactive) {
        c += "hatch-transparent text-gray-500";
      }
      return c;
    },

    genreInactive() {
      if (this.basicInactive) {
        return true;
      }
      if (this.genreCounts && this.card.playerId) {
        const ownerGenreCounts = this.genreCounts[this.card.playerId];
        const ownerTriggering = ownerGenreCounts != null && ownerGenreCounts[this.card.genreName] && ownerGenreCounts[this.card.genreName].triggering >= 2;
        return this.card.triggering && !ownerTriggering;
      }
    },

    genreSectionClass() {
      let c = this.card.timeless ? "flex-col " : "flex ";
      if (this.genreInactive) {
        c += HConstants.GENRES[this.card.genre].hatch + " border-gray-500 text-gray-500";
      } else {
        c += HConstants.GENRES[this.card.genre].border + " " + HConstants.GENRES[this.card.genre].bg25 + " " + HConstants.GENRES[this.card.genre].text;
      }
      return c;
    },

    genreTooltipClass() {
      return `${HConstants.GENRES[this.card.genre].text} ${HConstants.GENRES[this.card.genre].bg25}`;
    },

    clickAction() {
      if (this.myself != null) { // not spectator
        if (this.gamestate.active && this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
          return { action: "purchase" };
        } else if (this.gamestate.safeToMove) {
          if (this.card.location == this.myself.tableauLocation) {
            return { action: "move", destination: this.card.origin };
          } else if (this.card.location == this.myself.handLocation || (this.gamestate.active && this.gamestate.name == "playerTurn" && this.card.location.startsWith("timeless"))) {
            return { action: "move", destination: this.myself.tableauLocation };
          }
        }
      }
    },

    footerActions() {
      if (this.myself != null) { // not spectator
        const actionBlack = "button mx-1 black shadow";
        const actionBlue = "button mx-1 blue shadow";
        const actionRed = "button mx-1 red shadow";
        if (this.gamestate.active) {
          if (this.gamestate.name == "uncover" && this.gamestate.args.cardIds.includes(this.card.id)) {
            return [{
              action: "uncover",
              class: actionBlue,
              text: this.i18n("uncoverButton", { x: this.card.letter })
            }];

          } else if (this.gamestate.name == "double" && this.gamestate.args.cardIds.includes(this.card.id)) {
            return [{
              action: "double",
              class: actionBlue,
              text: this.i18n("doubleButton"),
            }];

          } else if (this.gamestate.name == "trash" && this.gamestate.args.cardIds.includes(this.card.id)) {
            return [{
              action: "trash",
              class: actionRed,
              text: this.i18n("trashButton"),
            }];

          } else if (this.gamestate.name == "trashDiscard" && this.card.location.startsWith("discard")) {
            return [{
              action: "trashDiscard",
              class: actionRed,
              text: this.i18n("trashCoinsButton", { coins: this.gamestate.args.amount }),
            }];

          } else if ((this.gamestate.name == "trash" || this.gamestate.name == "trashDiscard" || this.gamestate.name == "specialRomancePrompt") && this.gamestate.args.previewDraw == this.card.id) {
            return [{
              action: "previewDraw",
              class: actionBlue,
              text: this.i18n("previewButton"),
            }];

          } else if (this.gamestate.name == "specialRomance" && this.card.location.startsWith("hand")) {
            return [{
              action: "previewReturn",
              class: actionBlue,
              text: this.i18n("returnButton"),
            }, {
              action: "previewDiscard",
              class: actionBlue,
              text: this.i18n("discardButton"),
            }];

          } else if (this.gamestate.name.startsWith("either") && this.gamestate.args.sourceId == this.card.id) {
            if (this.gamestate.args.benefit == HConstants.EITHER_INK) {
              return [{
                action: "either",
                actionArgs: {
                  benefitId: this.gamestate.args.benefit,
                  choice: "ink",
                },
                class: actionBlue,
                text: this.i18n("ink"),
              }, {
                action: "either",
                actionArgs: {
                  benefitId: this.gamestate.args.benefit,
                  choice: "remover",
                },
                class: actionBlue,
                text: this.i18n("remover"),
              }];
            } else {
              return [{
                action: "either",
                actionArgs: {
                  benefitId: this.gamestate.args.benefit,
                  choice: "coins",
                },
                text: `${this.gamestate.args.amount}¢`,
                class: actionBlue,
              }, {
                action: "either",
                actionArgs: {
                  benefitId: this.gamestate.args.benefit,
                  choice: "points",
                },
                text: this.gamestate.args.amount,
                icon: "star",
                class: actionBlue,
              }];
            }

          } else if (this.gamestate.name == "jail" && this.card.location == "offer") {
            let jailJail = {
              action: "jail",
              actionArgs: {
                choice: "jail",
              },
              class: actionBlue,
              text: this.i18n("jailButton"),
            };
            if (this.gamestate.args.jail) {
              const confirmation = this.i18n("jailWarning", this.gamestate.args.jail);
              jailJail = Object.assign({ confirmation }, jailJail);
            }
            return [jailJail, {
              action: "jail",
              actionArgs: {
                choice: "trash",
              },
              class: actionRed,
              text: this.i18n("trashButton"),
            }];

          } else if (this.gamestate.name == "purchase" && this.gamestate.args.cardIds.includes(this.card.id)) {
            return [{
              action: "purchase",
              class: actionBlue,
              text: this.i18n("purchaseButton", { coins: this.card.cost })
            }];
          }
        }

        if (this.gamestate.safeToMove && this.card.location == this.myself.tableauLocation) {
          if (this.card.ink) {
            if (this.myself.remover > 0) {
              return [{
                action: "useRemover",
                class: actionBlack,
                text: this.i18n("useRemoverButton"),
              }];
            }

          } else if (!this.card.origin.startsWith("timeless")) {
            if (this.card.wild) {
              return [{
                action: "reset",
                class: actionBlue,
                text: this.i18n("uncoverButton", { x: this.card.letter }),
              }];

            } else {
              let wild = {
                action: "wild",
                class: actionBlue,
                text: this.i18n("wildButton"),
              };
              if (this.card.remover) {
                return [wild, {
                  action: "undoRemover",
                  class: actionBlack,
                  text: this.i18n("undoRemoverButton"),
                }];
              } else {
                return [wild];
              }
            }
          }
        }
      }

      // Even spectators see these
      if (this.card.ink) {
        return [{
          class: "px-2 leading-6 uppercase font-bold bg-black text-white",
          text: this.i18n("ink"),
        }];

      } else if (this.card.origin.startsWith("timeless")) {
        return [{
          class: `px-2 leading-6 ${this.card.player.colorBg} ${this.card.player.colorTextLight}`,
          text: this.card.player.name,
          icon: "timeless",
          title: this.i18n("timelessTip", { player_name: this.card.player.name }),
        }];

      } else if (this.card.location.startsWith("jail")) {
        return [{
          class: `px-2 leading-6 ${this.card.player.colorBg} ${this.card.player.colorTextLight}`,
          text: this.card.player.name,
          icon: "jail",
          title: this.i18n("jailTip", { player_name: this.card.player.name }),
        }];

      } else if (this.card.oldest) {
        return [{
          class: "mt-2 h-6",
          icon: "clock",
          title: this.i18n("oldestTip"),
        }];
      }
    },

    dragLocations() {
      if (this.myself != null && this.gamestate.safeToMove && this.prefs.drag) {
        if (this.card.location == this.myself.tableauLocation
          || (this.gamestate.active && this.gamestate.name == "playerTurn" && this.card.location.startsWith("timeless"))) {
          let locations = [this.myself.tableauLocation, this.card.location, this.card.origin];
          let unique = [...new Set(locations)];
          return unique;
        }
      }
    },
  },

  methods: {
    clickCard(ev) {
      let action = this.clickAction;
      if (action) {
        let card = this.card;
        this.emitter.emit("clickCard", { action, card });
      }
    },

    clickFooter(action) {
      if (action.action) {
        let card = this.card;
        this.emitter.emit("clickFooter", { action, card });
      }
    },

    /*
     * Drag and drop
     */

    pointStart(ev) {
      // Start the timer (which prevents click event and starts dragging)
      if (this.dragLocations) {
        console.log(`Pre-drag card ${this.card.id} start`);
        this.dragTimeout = setTimeout(() => this.dragStart(ev), 250);
      }
    },

    pointStop(ev) {
      // Stop the timer
      if (this.dragTimeout) {
        console.log(`Pre-drag card ${this.card.id} stop (via ${ev.type})`);
        clearTimeout(this.dragTimeout);
        this.dragTimeout = null;
      }
    },

    dragStart(ev) {
      if (this.dragTimeout) {
        // Stop the timer
        clearTimeout(this.dragTimeout);
        this.dragTimeout = null;

        // Start dragging
        console.log(`Pre-drag card ${this.card.id}`, this.dragLocations);
        if (this.dragLocations) {
          const el = this.$refs.cardholder;
          this.emitter.emit("dragStart", { ev, el, cardId: this.card.id, locations: this.dragLocations });
        }
      }
    },

    /*
     * Tooltip
     */
    tooltipEnter(ev) {
      if (this.prefs.tooltip) {
        this.tooltip.timeout = setTimeout(() => this.tooltipShow(), HConstants.TOOLTIP_TIMEOUT);
      }
    },

    tooltipLeave(ev) {
      clearTimeout(this.tooltip.timeout);
      this.tooltip.timeout = null;
      this.tooltip.visible = false;
    },

    async tooltipShow() {
      this.tooltip.visible = true;
      this.tooltip.top = "0px";
      this.tooltip.left = "-999px";
      await nextTick();
      const tip = this.$refs.tooltip;
      const holder = this.$refs.benefits;
      if (tip == null || holder == null) {
        this.tooltipLeave();
        return;
      }
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
