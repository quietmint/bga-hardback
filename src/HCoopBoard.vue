<template>
  <teleport to="#player_boards">
    <div id="overall_player_board_0" class="player-board" style="border-color: black; width: 234px; height: auto">
      <div class="player_board_inner" id="player_board_inner_000000">
        <div class="emblemwrap is_premium" id="avatarwrap_0">
          <img src="" alt="" class="avatar emblem" id="avatar_0" />
        </div>
        <div class="player-name" id="player_name_0">
          <div style="color: #000000">Penny Dreadful</div>
          <i class="fa fa-circle status_online player_status"></i>
          <div class="flag" style="background-position: -384px -99px" title="Unknown"></div>
        </div>
        <div id="player_board_0" class="player_board_content">
          <div class="player_score">
            <span id="player_score_0" class="player_score_value">0</span> <i class="fa fa-star" id="icon_point_0"></i>
            <span class="player_elo_wrap"
              >•
              <div class="gamerank gamerank_expert"><span class="icon20 icon20_rankw"></span> <span class="gamerank_value">666</span></div></span
            >
          </div>
          <div class="player_table_status" id="player_table_status_0" style="display: none"></div>
        </div>
        <div id="player_panel_content_000000" class="player_panel_content"></div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
export default {
  name: "HCoopBoard",
  inject: ["options"],
};
</script>
