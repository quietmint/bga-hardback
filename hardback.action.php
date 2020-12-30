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

  public function buildTableau()
  {
    self::setAjaxMode();
    $this->game->checkAction('buildTableau');
    $cardIds = explode(',', self::getArg('cardIds', AT_numberlist, true));
    $this->game->buildTableau($cardIds);
    self::ajaxResponse();
  }

  public function dragOrder()
  {
    self::setAjaxMode();
    $this->game->checkAction('dragOrder');
    $location = self::getArg('location', AT_alphanum, true);
    $cardId = self::getArg('cardId', AT_posint, true);
    $order = self::getArg('order', AT_posint, true);
    $this->game->dragOrder($cardId, $order, $location);
    self::ajaxResponse();
  }

  public function dragMove()
  {
    self::setAjaxMode();
    $this->game->checkAction('dragMove');
    $from = self::getArg('from', AT_alphanum, true);
    $to = self::getArg('to', AT_alphanum, true);
    $cardId = self::getArg('cardId', AT_posint, true);
    $order = self::getArg('order', AT_posint, true);
    $this->game->dragMove($cardId, $order, $from, $to);
    self::ajaxResponse();
  }
}
