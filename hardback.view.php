<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Hardback implementation : © quietmint
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 */

require_once(APP_BASE_PATH . "view/common/game.view.php");

class view_hardback_hardback extends game_view
{
  function getGameName()
  {
    return "hardback";
  }

  function build_page($viewArgs)
  {
  }
}
