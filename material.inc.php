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

require_once('constants.inc.php');

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

$this->names = [
    'define' => clienttranslate('Dictionary definitions'),
    'ink' => clienttranslate('ink'),
    'remover' => clienttranslate('remover'),
];

$this->msg = [
    'awardLose' => clienttranslate('... and ${player_name} loses the tarnished ${points}${iconPoints} literary award'),
    'awardWin' => clienttranslate('${player_name} earns the ${points}${iconPoints} literary award for spelling the first ${length}-letter word ${award}'),
    'confirmWord' => clienttranslate('${player_name} spells ${word}'),
    'convert' => clienttranslate('${player_name} converts 3 ink to ${amount}${icon}'),
    'coopDiscard' => clienttranslate('${player_name} discards timeless classic ${genre}${letter}'),
    'coopLose' => clienttranslate('Failure! The players are defeated and ${player_name} wins!'),
    'coopWin' => clienttranslate('Success! ${player_name} is defeated and the players win!'),
    'double' => clienttranslate('${player_name} doubles ${genre}${letter}'),
    'earn' => clienttranslate('${player_name} earns ${amount}${icon}'),
    'endTurn' => clienttranslate('${player_name} ends their turn'),
    'endTurnInk' => clienttranslate('${player_name} purchases ${amount} ink and ends their turn'),
    'errorInk' => clienttranslate('Your word must use all inked cards: %s'),
    'errorLength' => clienttranslate('Your word must use at least 2 letters'),
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
];
