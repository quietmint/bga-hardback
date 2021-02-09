<template>
  <teleport to="#player_boards">
    <div id="overall_player_board_0" class="player-board" style="border-color: black; width: 234px; height: auto">
      <div class="player_board_inner" id="player_board_inner_000000">
        <div class="emblemwrap is_premium" id="avatarwrap_0">
          <img src="" alt="" class="avatar emblem" id="avatar_0" />
        </div>
        <div class="player-name" id="player_name_0">
          <div style="color: #000000" v-text="i18n('penny')"></div>
          <i class="fa fa-circle status_online player_status"></i>
          <div class="flag" style="background-position: -352px -99px;" title="United States"></div>
        </div>
        <div id="player_board_0" class="player_board_content">
          <div class="player_score">
            <span id="player_score_0" class="player_score_value">{{ penny.score }}</span> <i class="fa fa-star" id="icon_point_0"></i>
            <span class="player_elo_wrap">
              •
              <div class="gamerank gamerank_expert"><span class="icon20 icon20_rankw"></span> <span class="gamerank_value">666</span></div></span>
          </div>
          <div class="player_table_status" id="player_table_status_0" style="display: none"></div>
          <div class="tailwind unmargin">
            <!-- Genre counts -->
            <HGenreCounts :counts="penny.genreCounts" />

            <!-- Signature genre -->
            <div v-if="genreNovel" class="genreNovel py-1 mt-2 border-t border-b border-black text-center text-20">{{ genreNovel.title }}</div>
            <div v-if="genreNovel" class="flex items-center m-2 text-14">
              <Icon :icon="genreNovel.icon" :class="genreNovel.color" class="text-24 flex-none mr-2" />
              <div>{{ genreNovel.description }}<Icon v-if="genreNovel.description2" icon="star" class="inline text-17" />{{ genreNovel.description2 }}</div>
            </div>
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
  inject: ["i18n"],
  components: { Icon, HGenreCounts },

  props: {
    penny: {
      type: Object,
      required: true,
    },
  },

  computed: {
    genreNovel() {
      switch (this.penny.genre) {
        case Constants.ADVENTURE:
          return {
            title: "Adventures in Peril",
            description: "If there is an Adventure card in the offer row, Penny gains 1",
            description2: " for each card purchased by any player",
            icon: "adventure",
            color: "text-yellow-900",
          };
        case Constants.HORROR:
          return {
            title: "The Horrors of Fear",
            description: "If there is a Horror card in the offer row, Penny gains 1",
            description2: " for each ink used by any player",
            icon: "horror",
            color: "text-green-700",
          };
        case Constants.MYSTERY:
          return {
            title: "A Mysterious Mystery",
            description: "Each time Penny claims a Mystery card, she also jails the cheapest card in the offer row",
            description2: "",
            icon: "mystery",
            color: "text-blue-700",
          };
        case Constants.ROMANCE:
          return {
            title: "Romancing the Heart",
            description: "Double the value of each Romance card Penny claims from the offer row",
            description2: "",
            icon: "romance",
            color: "text-red-700",
          };
      }
    },
  },
};
</script>
