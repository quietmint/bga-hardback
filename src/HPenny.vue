<template>
  <teleport to="#player_boards">
    <div id="overall_player_board_0" class="player-board" style="border-color: black; width: 234px; height: auto">
      <div class="player_board_inner" id="player_board_inner_000000">
        <div class="player-name" id="player_name_0">
          <div style="color: #000000" v-text="i18n('penny')"></div>
        </div>
        <div id="player_board_0" class="player_board_content">
          <div class="player_score">
            <span id="player_score_0" class="player_score_value">{{ penny.score }} / {{ penny.gameLength }}</span> <i class="fa fa-star" id="icon_point_0"></i>
            <span class="player_elo_wrap">
              •
              <div class="gamerank gamerank_expert"><span class="icon20 icon20_rankw"></span> <span class="gamerank_value">666</span></div></span
            >
          </div>
          <div class="tailwind">
            <div class="panel-signature flex flex-col items-center justify-center">
              <div class="text-14 ml-1" v-html="description"></div>
              <Icon v-if="icon" :icon="icon.icon" :class="icon.color" class="text-24 mt-1" />
            </div>
            <div v-if="title" class="panel-novel border-t border-b border-black mt-1 py-1 text-center text-20">{{ title }}</div>
            <HGenreCounts :counts="penny.genreCounts" />
          </div>
        </div>
        <div id="player_panel_content_000000" class="player_panel_content"></div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
import Constants from "./constants.js";
import HGenreCounts from "./HGenreCounts.vue";
import { Icon } from "@iconify/vue";

export default {
  name: "HPenny",
  inject: ["getHtml", "i18n", "refs"],
  components: { Icon, HGenreCounts },

  props: {
    penny: {
      type: Object,
      required: true,
    },
  },

  computed: {
    title() {
      if (this.penny.genre) {
        return this.i18n(this.refs.value.signature[this.penny.genre].title);
      }
    },

    description() {
      const star = this.getHtml("benefit_star");
      if (this.penny.genre) {
        return this.i18n(this.refs.value.signature[this.penny.genre].description, { star });
      }
    },

    icon() {
      switch (this.penny.genre) {
        case Constants.ADVENTURE:
          return {
            icon: "adventure",
            color: "text-yellow-900",
          };
        case Constants.HORROR:
          return {
            icon: "horror",
            color: "text-green-700",
          };
        case Constants.MYSTERY:
          return {
            icon: "mystery",
            color: "text-blue-700",
          };
        case Constants.ROMANCE:
          return {
            icon: "romance",
            color: "text-red-700",
          };
      }
    },
  },
};
</script>
