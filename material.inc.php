<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Hardback implementation : © quietmint
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * hardback game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

require_once('modules/constants.inc.php');

$this->genres = [STARTER, ADVENTURE, HORROR, ROMANCE, MYSTERY];

$this->awards = [
    7 => 5,
    8 => 6,
    9 => 7,
    10 => 9,
    11 => 12,
    12 => 15,
];

$this->adverts = [
    3 => 6,
    6 => 9,
    10 => 12,
    15 => 15,
    20 => 18,
];

$this->benefits = [
    COINS => clienttranslate('${value}¢'),
    DOUBLE_ADJ => clienttranslate('Double adjacent card'),
    EITHER_BASIC => clienttranslate('${value}¢ or ${value}${star}'),
    EITHER_GENRE => clienttranslate('${value}¢ or ${value}${star}'),
    EITHER_INK => clienttranslate('1 ink or remover'),
    JAIL => clienttranslate('Jail offer card'),
    POINTS => clienttranslate('${value}${star}'),
    SPECIAL_ADVENTURE => clienttranslate('2¢ for each ${adventure}'),
    SPECIAL_HORROR => clienttranslate('Opponents return 1 ink or remover'),
    SPECIAL_MYSTERY => clienttranslate('1${star} for each wild'),
    SPECIAL_ROMANCE => clienttranslate('Preview your next 3 cards'),
    TRASH_COINS => clienttranslate('Trash this for ${value}¢'),
    TRASH_DISCARD => clienttranslate('Trash discard for ${value}¢'),
    TRASH_POINTS => clienttranslate('Trash this for ${value}${star}'),
    UNCOVER_ADJ => clienttranslate('Uncover adjacent wild'),
];

$this->i18n = [
    'starter' => clienttranslate('Starter'),
    'adventure' => clienttranslate('Adventure'),
    'horror' => clienttranslate('Horror'),
    'mystery' => clienttranslate('Mystery'),
    'romance' => clienttranslate('Romance'),
    'basicTip' => clienttranslate('Basic benefits always activate'),
    'confirmButton' => clienttranslate('Confirm Word'),
    'convertButton' => clienttranslate('Convert 3 Ink to 1¢'),
    'dictionary' => clienttranslate('Dictionary definitions:'),
    'discardButton' => clienttranslate('Discard'),
    'discardInkButton' => clienttranslate('Discard 1 Ink'),
    'discardRemoverButton' => clienttranslate('Discard 1 Remover'),
    'doctorButton' => clienttranslate('${points}${icon} for ${coins}¢'),
    'doubleButton' => clienttranslate('Double'),
    'draw' => clienttranslate('Draw With Ink (${count})'),
    'finalRound' => clienttranslate('This is the final round!'),
    'flushButton' => clienttranslate('Flush Offer Row'),
    'genreTip' => clienttranslate('Genre benefits activate if you play multiple ${x} cards'),
    'ink' => clienttranslate('ink'),
    'invalid' => clienttranslate('${invalid} is not a valid word'),
    'jailButton' => clienttranslate('Jail'),
    'jailButton' => clienttranslate('Jail'),
    'jailTip' => clienttranslate('Jailed: Only ${player_name} may purchase'),
    'keyboard' => clienttranslate('Click or type the wild letter'),
    'moveAll' => clienttranslate('Move All'),
    'myDiscard' => clienttranslate('My Discard Pile (${count})'),
    'myHand' => clienttranslate('My Hand (${count})'),
    'offer' => clienttranslate('Offer Row (${count})'),
    'penny' => clienttranslate('Penny Dreadful'),
    'purchaseButton' => clienttranslate('Purchase'),
    'remover' => clienttranslate('remover'),
    'resetAll' => clienttranslate('Reset All'),
    'resetButton' => clienttranslate('Reset (${x})'),
    'returnButton' => clienttranslate('Return'),
    'shuffleTip' => clienttranslate('Shuffle cards'),
    'skipButton' => clienttranslate('Skip'),
    'skipPurchaseButton' => clienttranslate('${coins} Ink (End Turn)'),
    'skipWordButton' => clienttranslate('Skip Turn'),
    'sortCostTip' => clienttranslate('Sort cards by cost'),
    'sortGenreTip' => clienttranslate('Sort cards by genre'),
    'sortLetterTip' => clienttranslate('Sort cards by letter'),
    'sortOrderTip' => clienttranslate('Sort cards by draw order'),
    'tableau' => clienttranslate('Current Word (${count})'),
    'timeless' => clienttranslate('Timeless Classics (${count})'),
    'timelessTip' => clienttranslate('Timeless Classic: ${player_name} receives benefits each turn'),
    'trashButton' => clienttranslate('Trash for ${coins}¢'),
    'trashButton' => clienttranslate('Trash'),
    'uncoverButton' => clienttranslate('Uncover'),
    'useRemoverButton' => clienttranslate('Remove Ink'),
    'wildButton' => clienttranslate('Wild'),
];

$this->msg = [
    'awardLose' => clienttranslate('... and ${player_name} loses the tarnished ${points}${iconPoints} literary award'),
    'awardWin' => clienttranslate('${player_name} earns the ${points}${iconPoints} literary award for spelling the first ${length}-letter word ${award}'),
    'confirmWord' => clienttranslate('${player_name} spells ${word}'),
    'convert' => clienttranslate('${player_name} converts 3 ink to ${amount}${icon}'),
    'coopLose' => clienttranslate('Failure! The players are defeated and ${player_name} wins!'),
    'coopWin' => clienttranslate('Success! ${player_name} is defeated and the players win!'),
    'double' => clienttranslate('${player_name} doubles ${genre}${letter}'),
    'earn' => clienttranslate('${player_name} earns ${amount}${icon}'),
    'endTurn' => clienttranslate('${player_name} ends their turn'),
    'endTurnInk' => clienttranslate('${player_name} purchases ${amount} ink and ends their turn'),
    'errorInk' => clienttranslate('Your word must use all inked cards: %s'),
    'errorLength' => clienttranslate('Your word must use at least 2 letters'),
    'forceDiscard' => clienttranslate('${player_name} forces ${player_name2} to discard ${amount}${icon}'),
    'forceTimeless' => clienttranslate('${player_name} forces ${player_name2} to discard timeless classic ${genre}${letter}'),
    'invalidWord' => clienttranslate('${player_name} spells an invalid word, ${invalid}'),
    'jailOffer' => clienttranslate('${player_name} jails ${genre}${letter} from the offer row'),
    'purchase' => clienttranslate('${player_name} purchases ${genre}${letter} for ${coins}${iconCoins}'),
    'purchase2' => clienttranslate('${player_name} purchases ${genre}${letter} for ${coins}${iconCoins} and earns ${points}${iconPoints}'),
    'purchaseAdvert' => clienttranslate('${player_name} purchases the ${points}${iconPoints} advertisement for ${coins}${iconCoins}'),
    'purchaseCoop' => clienttranslate('${player_name} purchases ${genre}${letter} and earns ${points}${iconPoints}'),
    'skipWord' => clienttranslate('${player_name} is stymied by writer\'s block and skips their turn.'),
    'summary0' => clienttranslate('${player_name} earns nothing this round'),
    'summary1' => clienttranslate('${player_name} earns ${amount1}${icon1} this round'),
    'summary2' => clienttranslate('${player_name} earns ${amount1}${icon1} and ${amount2}${icon2} this round'),
    'summary3' => clienttranslate('${player_name} earns ${amount1}${icon1}, ${amount2}${icon2}, and ${amount3}${icon3} this round'),
    'summary4' => clienttranslate('${player_name} earns ${amount1}${icon1}, ${amount2}${icon2}, ${amount3}${icon3}, and ${amount4}${icon4} this round'),
    'trash' => clienttranslate('${player_name} trashes ${genre}${letter} and earns ${amount}${icon}'),
    'trashOffer' => clienttranslate('${player_name} trashes ${genre}${letter} from the offer row'),
    'uncover' => clienttranslate('${player_name} uncovers ${genre}${letter}'),
    'useInk' => clienttranslate('${player_name} spends ink to draw ${genre}${letter}'),
    'useRemover' => clienttranslate('${player_name} spends remover to avoid ${genre}${letter}'),
];
