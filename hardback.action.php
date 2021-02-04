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
  public function preview()
  {
    self::setAjaxMode();
    $this->game->checkAction('confirmWord');
    $cardId = self::getArg('cardId', AT_posint, true);
    $location = self::getArg('location', AT_alphanum, true);
    $order = self::getArg('cardId', AT_posint, true);
    hardback::$instance->notifyAllPlayers('preview', "card $cardId, location $location, order $order", []);
    self::ajaxResponse();
  }

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

  public function double()
  {
    self::setAjaxMode();
    $this->game->checkAction('double');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->double($cardId);
    self::ajaxResponse();
  }

  public function discardInk()
  {
    self::setAjaxMode();
    $this->game->checkAction('discardInk');
    $this->game->discardInk();
    self::ajaxResponse();
  }

  public function discardRemover()
  {
    self::setAjaxMode();
    $this->game->checkAction('discardRemover');
    $this->game->discardRemover();
    self::ajaxResponse();
  }

  public function previewReturn()
  {
    self::setAjaxMode();
    $this->game->checkAction('previewReturn');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->previewReturn($cardId);
    self::ajaxResponse();
  }

  public function previewDiscard()
  {
    self::setAjaxMode();
    $this->game->checkAction('previewDiscard');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->previewDiscard($cardId);
    self::ajaxResponse();
  }

  public function trash()
  {
    self::setAjaxMode();
    $this->game->checkAction('trash');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->trash($cardId);
    self::ajaxResponse();
  }

  public function trashDiscard()
  {
    self::setAjaxMode();
    $this->game->checkAction('trashDiscard');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->trashDiscard($cardId);
    self::ajaxResponse();
  }

  public function either()
  {
    self::setAjaxMode();
    $this->game->checkAction('either');
    $cardId = self::getArg('cardId', AT_posint, true);
    $benefitId = self::getArg('benefitId', AT_posint, true);
    $choice = self::getArg('choice', AT_alphanum, true);
    $this->game->either($cardId, $benefitId, $choice);
    self::ajaxResponse();
  }

  public function jail()
  {
    self::setAjaxMode();
    $this->game->checkAction('jail');
    $cardId = self::getArg('cardId', AT_posint, true);
    $choice = self::getArg('choice', AT_alphanum, true);
    $this->game->jail($cardId, $choice);
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

  public function convert()
  {
    self::setAjaxMode();
    $this->game->checkAction('convert');
    $this->game->convert();
    self::ajaxResponse();
  }

  // "advert.html" is blocked by browser Adblock plugin
  public function doctor()
  {
    self::setAjaxMode();
    $this->game->checkAction('doctor');
    $this->game->advert();
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
    // Custom access check, non-active players can always do this
    if ($this->game->isCurrentPlayerActive() && $this->game->gamestate->state()['name'] != 'playerTurn') {
      throw new BgaUserException('This game action is impossible right now');
    }
    $this->game->useInk();
    self::ajaxResponse();
  }

  public function useRemover()
  {
    self::setAjaxMode();
    // Custom access check, non-active players can always do this
    if ($this->game->isCurrentPlayerActive() && $this->game->gamestate->state()['name'] != 'playerTurn') {
      throw new BgaUserException('This game action is impossible right now');
    }
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->useRemover($cardId);
    self::ajaxResponse();
  }
}
