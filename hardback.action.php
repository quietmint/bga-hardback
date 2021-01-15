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

  /*
   * PHASE 1: SPELL A WORD
   * PHASE 2: DISCARD UNUSED CARDS
   */

  public function confirmWord()
  {
    self::setAjaxMode();
    $this->game->checkAction('confirmWord');
    $cardIds = explode(',', self::getArg('cardIds', AT_numberlist, true));
    $wildMask = self::getArg('wildMask', AT_alphanum, true);
    $this->game->confirmWord($cardIds, $wildMask);
    self::ajaxResponse();
  }

  /*
   * PHASE 3: RESOLVE CARD BENEFITS
   */

  public function skip()
  {
    self::setAjaxMode();
    $this->game->checkAction('skip');
    $this->game->skip();
    self::ajaxResponse();
  }

  public function uncover()
  {
    self::setAjaxMode();
    $this->game->checkAction('uncover');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->uncover($cardId);
    self::ajaxResponse();
  }

  public function either()
  {
    self::setAjaxMode();
    $this->game->checkAction('either');
    $points = self::getArg('points', AT_bool, true);
    $this->game->either($points);
    self::ajaxResponse();
  }

  /*
   * PHASE 4: PURCHASE NEW CARDS AND INK
   */

  public function flush()
  {
    self::setAjaxMode();
    $this->game->checkAction('flush');
    $this->game->flush();
    self::ajaxResponse();
  }

  public function purchase()
  {
    self::setAjaxMode();
    $this->game->checkAction('purchase');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->purchase($cardId);
    self::ajaxResponse();
  }

  /*
   * PHASE 5: DISCARD USED CARDS AND INK
   * PHASE 6: DISCARD USED TIMELESS CLASSIC CARDS
   */

  /*
   * PHASE 7: DRAW YOUR NEXT HAND
   */

  public function skipWord()
  {
    self::setAjaxMode();
    $this->game->checkAction('skipWord');
    $this->game->skipWord();
    self::ajaxResponse();
  }

  /*
   * PHASE 8: USE INK AND REMOVER
   */

  public function useInk()
  {
    self::setAjaxMode();
    // Don't check action, since ink is allowed out-of-turn
    $this->game->useInk();
    self::ajaxResponse();
  }

  public function useRemover()
  {
    self::setAjaxMode();
    // Don't check action, since remover is allowed out-of-turn
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->useRemover($cardId);
    self::ajaxResponse();
  }
}
