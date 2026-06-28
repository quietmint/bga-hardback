<template>
  <teleport to="#player_boards">
    <div id="overall_player_board_0"
         class="player-board hb-black"
         style="border-color: black; width: 234px; height: auto">
      <div id="player_board_inner_000000"
           class="player_board_inner">
        <div id="avatarwrap_0"
             v-if="icon"
             class="emblemwrap hb-tailwind">
          <Icon id="avatar_0"
                :icon="icon.icon"
                :class="icon.color"
                class="avatar emblem" />
        </div>
        <div id="player_name_0"
             class="player-name">
          <a style="color: #000000"
             v-text="i18n('penny')"></a>
        </div>
        <div id="player_board_0"
             class="player_board_content">
          <div class="player_score">
            <span id="player_score_0"
                  class="player_score_value">{{ penny.score }} / {{ penny.gameLength }}</span> <i id="icon_point_0"
               class="fa fa-star"></i>
          </div>
          <div class="hb-tailwind">
            <div class="hb-panel-signature hb-flex hb-flex-col hb-justify-center hb-ml-1">
              <div id="tut_pennyTitle"
                   v-if="title"
                   v-text="title"
                   class="hb-panel-novel hb-text-20 hb-leading-none hb-my-1 hb-pb-1 hb-border-b hb-border-white"></div>
              <div id="tut_pennySignature"
                   class="hb-text-15"
                   v-html="description"></div>
            </div>
          </div>
        </div>
        <div id="player_panel_content_000000"
             class="player_panel_content"></div>
      </div>
    </div>
  </teleport>
</template>

<script lang="js">
import HConstants from "./HConstants.js";
import { Icon } from "@iconify/vue";

export default {
  name: "HPenny",
  inject: ["i18n", "refs"],
  components: { Icon },

  props: {
    penny: {
      type: Object,
      required: true,
    },
  },

  computed: {
    title() {
      if (this.penny.genre) {
        return this.i18n(this.refs.signatures[this.penny.genre].title);
      }
    },

    description() {
      if (this.penny.genre) {
        return this.i18n(this.refs.signatures[this.penny.genre].description);
      }
    },

    icon() {
      if (this.penny.genre) {
        return {
          icon: HConstants.GENRES[this.penny.genre].icon,
          color: HConstants.GENRES[this.penny.genre].text,
        };
      }
    },
  },
};
</script>
