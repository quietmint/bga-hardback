<template>
  <teleport to="#player_boards">
    <div id="overall_player_board_0" class="player-board black" style="border-color: black; width: 234px; height: auto">
      <div id="player_board_inner_000000" class="player_board_inner">
        <div id="avatarwrap_0" v-if="icon" class="emblemwrap tailwind">
          <Icon id="avatar_0" :icon="icon.icon" :class="icon.color" class="avatar emblem" />
        </div>
        <div id="player_name_0" class="player-name">
          <a style="color: #000000" v-text="i18n('penny')"></a>
        </div>
        <div id="player_board_0" class="player_board_content">
          <div class="player_score">
            <span id="player_score_0" class="player_score_value">{{ penny.score }} / {{ penny.gameLength }}</span> <i id="icon_point_0" class="fa fa-star"></i>
            <span class="player_elo_wrap">
              •
              <div class="gamerank gamerank_expert"><span class="icon20 icon20_rankw"></span> <span class="gamerank_value">666</span></div></span
            >
          </div>
          <div class="tailwind">
            <div class="panel-signature flex flex-col items-center justify-center">
              <div id="tut_pennySignature" class="text-14 ml-1" v-html="description"></div>
            </div>
            <div id="tut_pennyTitle" v-if="title" class="panel-novel text-center text-20">{{ title }}</div>
          </div>
        </div>
        <div id="player_panel_content_000000" class="player_panel_content"></div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
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
        return this.i18n(this.refs.value.signatures[this.penny.genre].title);
      }
    },

    description() {
      if (this.penny.genre) {
        return this.i18n(this.refs.value.signatures[this.penny.genre].description);
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
