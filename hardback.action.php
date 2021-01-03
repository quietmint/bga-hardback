<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Hardback implementation : © quietmint
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * hardback.action.php
 *
 * hardback main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/hardback/hardback/myAction.html", ...)
 *
 */


class action_hardback extends APP_GameAction
{
  // Constructor: please do not modify
  public function __default()
  {
    if (self::isArg('notifwindow')) {
      $this->view = "common_notifwindow";
      $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
    } else {
      $this->view = "hardback_hardback";
      self::trace("Complete reinitialization of board game");
    }
  }

  public function useInk()
  {
    self::setAjaxMode();
    // Don't check, since using ink is allowed out-of-turn
    $this->game->useInk();
    self::ajaxResponse();
  }

  public function useRemover()
  {
    self::setAjaxMode();
    // Don't check, since using remover is allowed out-of-turn
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->useRemover($cardId);
    self::ajaxResponse();
  }

  public function confirmWord()
  {
    self::setAjaxMode();
    $this->game->checkAction('confirmWord');
    $cardIds = explode(',', self::getArg('cardIds', AT_numberlist, true));
    $this->game->confirmWord($cardIds);
    self::ajaxResponse();
  }

  public function skipTurn()
  {
    self::setAjaxMode();
    $this->game->checkAction('skipTurn');
    $this->game->skipTurn();
    self::ajaxResponse();
  }
}
