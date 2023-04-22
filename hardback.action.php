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

  private function checkVersion()
  {
    $clientVersion = (int) self::getArg('version', AT_int, false);
    $this->game->checkVersion($clientVersion);
  }

  private function getArgIdList(string $name, bool $required): ?array
  {
    $value = self::getArg($name, AT_numberlist, $required);
    return ($value === null) ? null : explode(',', $value);
  }

  // Production bug report handler
  public function loadBugSQL()
  {
    self::setAjaxMode();
    $reportId = (int) self::getArg('report_id', AT_int, true);
    $this->game->loadBugSQL($reportId);
    self::ajaxResponse();
  }

  /*
   * PHASE 1: SPELL A WORD
   * PHASE 2: DISCARD UNUSED CARDS
   */
  public function deletePreviewNotifications()
  {
    self::setAjaxMode();
    self::checkVersion();
    CardMgr::deletePreviewNotifications();
    self::ajaxResponse();
  }

  public function previewWord()
  {
    self::setAjaxMode();
    self::checkVersion();
    if ($this->game->checkActionCustom('previewWord', false)) {
      $handIds = $this->getArgIdList('handIds', true);
      $handMask = self::getArg('handMask', AT_alphanum, true);
      $tableauIds = $this->getArgIdList('tableauIds', true);
      $tableauMask = self::getArg('tableauMask', AT_alphanum, true);
      $this->game->previewWord($handIds, $handMask, $tableauIds, $tableauMask);
    }
    self::ajaxResponse();
  }

  public function confirmWord()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('confirmWord');
    $tableauIds = $this->getArgIdList('tableauIds', true);
    $tableauMask = self::getArg('tableauMask', AT_alphanum, true);
    $this->game->confirmWord($tableauIds, $tableauMask);
    self::ajaxResponse();
  }

  public function voteWord()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('voteAccept');
    $vote = self::getArg('vote', AT_bool, true);
    $this->game->voteWord($vote);
    self::ajaxResponse();
  }

  public function lookup()
  {
    self::setAjaxMode();
    self::checkVersion();
    $word = self::getArg('word', AT_alphanum, true);
    $this->game->lookup($word);
    self::ajaxResponse();
  }

  /*
   * PHASE 3: RESOLVE CARD BENEFITS
   */

  public function skip()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('skip');
    $this->game->skip();
    self::ajaxResponse();
  }

  public function uncover()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('uncover');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->uncover($cardId);
    self::ajaxResponse();
  }

  public function double()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('double');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->double($cardId);
    self::ajaxResponse();
  }

  public function previewDraw()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('previewDraw');
    $this->game->previewDraw();
    self::ajaxResponse();
  }

  public function previewReturn()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('previewReturn');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->previewReturn($cardId);
    self::ajaxResponse();
  }

  public function previewDiscard()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('previewDiscard');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->previewDiscard($cardId);
    self::ajaxResponse();
  }

  public function trash()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('trash');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->trash($cardId);
    self::ajaxResponse();
  }

  public function trashDiscard()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('trashDiscard');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->trashDiscard($cardId);
    self::ajaxResponse();
  }

  public function either()
  {
    self::setAjaxMode();
    self::checkVersion();
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
    self::checkVersion();
    $this->game->checkAction('jail');
    $cardId = self::getArg('cardId', AT_posint, true);
    $choice = self::getArg('choice', AT_alphanum, true);
    $this->game->jail($cardId, $choice);
    self::ajaxResponse();
  }

  /*
   * PHASE 4: PURCHASE NEW CARDS AND INK
   */

  public function skipPurchase()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('skipPurchase');
    $this->game->skip();
    self::ajaxResponse();
  }

  public function flush()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('flush');
    $this->game->flush();
    self::ajaxResponse();
  }

  public function purchase()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('purchase');
    $cardId = self::getArg('cardId', AT_posint, true);
    $endGameConfirm = self::getArg('endGameConfirm', AT_bool, false, false);
    $this->game->purchase($cardId, $endGameConfirm);
    self::ajaxResponse();
  }

  public function convert()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('convert');
    $endGameConfirm = self::getArg('endGameConfirm', AT_bool, false, false);
    $this->game->convert($endGameConfirm);
    self::ajaxResponse();
  }

  // "advert.html" is blocked by browser Adblock plugin
  public function doctor()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkAction('doctor');
    $this->game->advert();
    self::ajaxResponse();
  }

  /*
   * PHASE 5: DISCARD USED CARDS AND INK
   * PHASE 6: DISCARD USED TIMELESS CLASSIC CARDS
   * PHASE 7: DRAW YOUR NEXT HAND
   */

  public function skipWord()
  {
    self::setAjaxMode();
    self::checkVersion();
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
    self::checkVersion();
    $this->game->checkActionCustom('useInk');
    $endGameConfirm = self::getArg('endGameConfirm', AT_bool, false, false);
    $this->game->useInk($endGameConfirm);
    self::ajaxResponse();
  }

  public function useRemover()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkActionCustom('useInk');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->useRemover($cardId);
    self::ajaxResponse();
  }

  public function undoRemover()
  {
    self::setAjaxMode();
    self::checkVersion();
    $this->game->checkActionCustom('useInk');
    $cardId = self::getArg('cardId', AT_posint, true);
    $this->game->undoRemover($cardId);
    self::ajaxResponse();
  }
}
