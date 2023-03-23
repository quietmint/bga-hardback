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

$this->genres = [H_STARTER, H_ADVENTURE, H_HORROR, H_ROMANCE, H_MYSTERY];

$this->awards = [
    7 => 5,
    8 => 6,
    9 => 7,
    10 => 9,
    11 => 12,
    12 => 15,
];

$this->adverts = [
    6 => 3,
    9 => 6,
    12 => 10,
    15 => 15,
    18 => 20,
];

$this->signatures = [
    H_ADVENTURE => [
        'title' => clienttranslate('Adventures in Peril'),
        'description' => clienttranslate('If there is an Adventure card in the offer row, Penny gains 1${star} for each card purchased by any player'),
    ],
    H_HORROR => [
        'title' => clienttranslate('The Horrors of Fear'),
        'description' => clienttranslate('If there is a Horror card in the offer row, Penny gains 1${star} for each ink used by any player'),
    ],
    H_MYSTERY => [
        'title' => clienttranslate('A Mysterious Mystery'),
        'description' => clienttranslate('Each time Penny claims a Mystery card, she also jails the cheapest card in the offer row'),
    ],
    H_ROMANCE => [
        'title' => clienttranslate('Romancing the Heart'),
        'description' => clienttranslate('Double the value of each Romance card Penny claims from the offer row'),
    ],
];

$this->i18n = [
    'starter' => clienttranslate('Starter'),
    'adventure' => clienttranslate('Adventure'),
    'horror' => clienttranslate('Horror'),
    'mystery' => clienttranslate('Mystery'),
    'romance' => clienttranslate('Romance'),

    'adverts' => clienttranslate('Adverts'),
    'award' => clienttranslate('Literary Awards'),
    'awardLetters' => clienttranslate('${length} letters'),
    'browserWarnDesc' => clienttranslate('Some game features are unavailable'),
    'browserWarnTitle' => clienttranslate('Update your browser for a better experience'),
    'confirmButton' => clienttranslate('Confirm Word'),
    'convertButton' => clienttranslate('Convert ${ink} Ink to ${coins}¢'),
    'dictionary' => clienttranslate('Dictionary definitions:'),
    'discardButton' => clienttranslate('Discard'),
    'discardLocation' => clienttranslate('Discard Pile'),
    'doctorButton' => clienttranslate('${points}${icon} Advert (${coins}¢)'),
    'doubleButton' => clienttranslate('Double'),
    'drawLocation' => clienttranslate('Draw Pile'),
    'empty' => clienttranslate('No cards in ${location}'),
    'endGameWarning' => clienttranslate('Penny Dreadful will score enough points to end the game immediately'),
    'finalRound' => clienttranslate('This is the final round!'),
    'first' => clienttranslate('First Player'),
    'flushButton' => clienttranslate('Flush Offer Row'),
    'genreCountsTip' => clienttranslate('${player_name}\'s cards (${count})'),
    'genreTip' => clienttranslate('With multiple ${x} cards'),
    'handLocation' => clienttranslate('Hand'),
    'ink' => clienttranslate('Ink'),
    'jailButton' => clienttranslate('Jail'),
    'jailTip' => clienttranslate('Jailed: Only ${player_name} may purchase'),
    'jailWarning' => clienttranslate('${genre}${letter} currently in jail will be trashed'),
    'keyboard' => clienttranslate('Click or type the wild letter for ${genre}${letter}'),
    'lengthHeader' => clienttranslate('Length'),
    'lookup' => clienttranslate('Lookup'),
    'lookupPlaceholder' => clienttranslate('Type a word to lookup'),
    'offerLocation' => clienttranslate('Offer Row'),
    'oldestTip' => clienttranslate('This is the oldest card'),
    'penny' => clienttranslate('Penny Dreadful'),
    'playAll' => clienttranslate('Play All'),
    'playerHeader' => clienttranslate('Player'),
    'previewButton' => clienttranslate('Preview'),
    'purchaseButton' => clienttranslate('Purchase (${coins}¢)'),
    'remover' => clienttranslate('Remover'),
    'returnAll' => clienttranslate('Return All'),
    'returnButton' => clienttranslate('Return'),
    'shuffleTip' => clienttranslate('Shuffle cards'),
    'skipButton' => clienttranslate('Skip'),
    'skipPurchaseButton' => clienttranslate('${coins} Ink (End Turn)'),
    'skipWordButton' => clienttranslate('Skip Turn'),
    'skipWordWarning' => clienttranslate('You are about to skip your turn'),
    'sortCostTip' => clienttranslate('Sort cards by cost'),
    'sortGenreTip' => clienttranslate('Sort cards by genre'),
    'sortLetterTip' => clienttranslate('Sort cards by letter'),
    'sortTimeTip' => clienttranslate('Sort cards newest to oldest'),
    'tableauLocation' => clienttranslate('Word'),
    'timelessLocation' => clienttranslate('Timeless Classics'),
    'timelessTip' => clienttranslate('Timeless Classic: ${player_name} receives benefits each turn'),
    'trashButton' => clienttranslate('Trash'),
    'trashCoinsButton' => clienttranslate('Trash (${coins}¢)'),
    'uncoverAll' => clienttranslate('Uncover All'),
    'uncoverButton' => clienttranslate('Uncover (${x})'),
    'undoRemoverButton' => clienttranslate('Re-Ink'),
    'useInk' => clienttranslate('Draw With Ink (${count})'),
    'useRemoverButton' => clienttranslate('Remove Ink'),
    'voteAcceptButton' => clienttranslate('Accept ${word}'),
    'voteRejectButton' => clienttranslate('Reject ${word}'),
    'wildButton' => clienttranslate('Wild'),
    'wordHeader' => clienttranslate('Word'),
];

$this->msg = [
    'acceptedWord' => clienttranslate('${word} is accepted by ${dict}'),
    'awardEnd' => clienttranslate('${player_name} earns ${amount}${icon} for the ${length}-letter literary award'),
    'awardLose' => clienttranslate('${player_name} loses the ${length}-letter literary award'),
    'awardWin' => clienttranslate('${player_name} wins the ${length}-letter literary award (worth ${amount}${icon} at game end)${award}'),
    'confirmWord' => clienttranslate('${player_name} spells ${word}${definitions}'),
    'convert' => clienttranslate('${player_name} converts ${ink} ink to ${amount}${icon}'),
    'coopLose' => clienttranslate('Failure! The players are defeated and ${player_name} wins!'),
    'coopWin' => clienttranslate('Success! ${player_name} is defeated and the players win!'),
    'cycled' => clienttranslate('${genre}${letter} is discarded from the offer row'),
    'dictionary' => clienttranslate('The dictionary selected for this game is ${dict} (${lang})'),
    'double' => clienttranslate('${player_name} doubles ${genre}${letter}'),
    'draw' => clienttranslate('${genre}${letter} is added to the offer row'),
    'drawDiscard' => clienttranslate('${genre}${letter} is added to the offer row and ${player_name} forces ${player_name2} to discard Timeless Classic ${genre2}${letter2}'),
    'earn' => clienttranslate('${player_name} earns ${amount}${icon}'),
    'endTurn' => clienttranslate('${player_name} ends their turn'),
    'endTurnInk' => clienttranslate('${player_name} purchases ${amount} ink and ends their turn'),
    'errorEmpty' => self::_('No more cards'),
    'errorInk' => self::_('Must use all inked cards: %s'),
    'errorLength' => self::_('Must use at least 2 letters'),
    'flush' => clienttranslate('${player_name} flushes the offer row'),
    'jailOffer' => clienttranslate('${player_name} jails ${genre}${letter} from the offer row'),
    'nextRound' => clienttranslate('Round ${round} begins'),
    'preview' => clienttranslate('${player_name} previews ${count} cards from their deck'),
    'purchase' => clienttranslate('${player_name} purchases ${genre}${letter} for ${coins}${iconCoins}'),
    'purchase2' => clienttranslate('${player_name} purchases ${genre}${letter} for ${coins}${iconCoins} and earns ${points}${iconPoints}'),
    'purchaseAdvert' => clienttranslate('${player_name} purchases the ${points}${iconPoints} advertisement for ${coins}${iconCoins}'),
    'purchaseCoop' => clienttranslate('${player_name} purchases ${genre}${letter} and earns ${points}${iconPoints}'),
    'rejectedWord' => clienttranslate('${word} is rejected by ${dict} (${lang})'),
    'skipWord' => clienttranslate('${player_name} is stymied by writer\'s block and skips their turn.'),
    'starterCards' => clienttranslate('Your ${amount}${icon} cards are ${genre}${letter} and ${genre2}${letter2}'),
    'summary0' => clienttranslate('${player_name} earns nothing this round'),
    'summary1' => clienttranslate('${player_name} earns ${amount1}${icon1} this round'),
    'summary2' => clienttranslate('${player_name} earns ${amount1}${icon1} and ${amount2}${icon2} this round'),
    'summary3' => clienttranslate('${player_name} earns ${amount1}${icon1}, ${amount2}${icon2}, and ${amount3}${icon3} this round'),
    'summary4' => clienttranslate('${player_name} earns ${amount1}${icon1}, ${amount2}${icon2}, ${amount3}${icon3}, and ${amount4}${icon4} this round'),
    'trash' => clienttranslate('${player_name} trashes ${genre}${letter} and earns ${amount}${icon}'),
    'trashOffer' => clienttranslate('${player_name} trashes ${genre}${letter} from the offer row'),
    'uncover' => clienttranslate('${player_name} uncovers ${genre}${letter}'),
    'undoRemover' => clienttranslate('${player_name} re-inks ${genre}${letter}'),
    'upgradeTableDb' => clienttranslate('${player_name}: An updated version of this game has just been deployed! Please refresh your browser window (F5)'),
    'useInk' => clienttranslate('${player_name} spends ink to draw ${genre}${letter}'),
    'useRemover' => clienttranslate('${player_name} spends remover to avoid ${genre}${letter}'),
    'votesReject' => clienttranslate('${player_name} rejects ${word}'),
];

$this->benefits = [
    H_COINS => [
        'short' => '${value}¢',
        'long' => clienttranslate('Gain ${value}¢'),
    ],
    H_DOUBLE_ADJ => [
        'short' => clienttranslate('Double'),
        'long' => clienttranslate('Double the ¢ and ${star} values of an adjacent card'),
    ],
    H_EITHER_BASIC => [
        'short' => clienttranslate('${value}¢/${value}${star}'),
        'long' => clienttranslate('Gain ${value}¢ or ${value}${star}'),
    ],
    H_EITHER_GENRE => [
        'short' => clienttranslate('${value}¢/${value}${star}'),
        'long' => clienttranslate('Gain ${value}¢ or ${value}${star}'),
    ],
    H_EITHER_INK => [
        'short' => clienttranslate('Remover'),
        'long' => clienttranslate('Gain an ink or remover'),
    ],
    H_JAIL => [
        'short' => clienttranslate('Jail'),
        'long' => clienttranslate('Reserve or remove an offer row card so others cannot purchase it'),
    ],
    H_POINTS => [
        'short' => '${value}${star}',
        'long' => clienttranslate('Gain ${value}${star}'),
    ],
    H_SPECIAL_ADVENTURE => [
        'short' => '${value}¢ per ${adventure}',
        'long' => clienttranslate('Gain ${value}¢ for each ${adventure} card in play'),
    ],
    H_SPECIAL_HORROR => [
        'short' => clienttranslate('${value}${star} per Ink'),
        'long' => clienttranslate('Gain ${value}${star} for each inked card in play'),
    ],
    H_SPECIAL_MYSTERY => [
        'short' => clienttranslate('${value}${star} per Wild'),
        'long' => clienttranslate('Gain ${value}${star} for each wild card in play'),
    ],
    H_SPECIAL_ROMANCE => [
        'short' => clienttranslate('Preview'),
        'long' => clienttranslate('Preview 3 cards from your deck. Return or discard each.'),
    ],
    H_TRASH_COINS => [
        'short' => clienttranslate('Trash: ${value}¢'),
        'long' => clienttranslate('Remove this card to gain ${value}¢'),
    ],
    H_TRASH_DISCARD => [
        'short' => clienttranslate('Dump: ${value}¢'),
        'long' => clienttranslate('Remove a discarded card to gain ${value}¢'),
    ],
    H_TRASH_POINTS => [
        'short' => clienttranslate('Trash: ${value}${star}'),
        'long' => clienttranslate('Remove this card to gain ${value}${star}'),
    ],
    H_UNCOVER_ADJ => [
        'short' => clienttranslate('Uncover'),
        'long' => clienttranslate('Uncover an adjacent wild card'),
    ],
];

// UPDATE card SET `location` = 'hand_2305326', `origin` = 'hand_2305326' WHERE `refId` IN (5, 12, 23, 31, 40, 42, 53, 64, 73, 86, 92, 96, 107, 113, 123, 134) OR `refId` IN (1, 7, 21, 26, 36, 43, 46, 71, 76, 95, 106, 111, 124);

$this->cards = [
    1 => ['genre' => H_ADVENTURE, 'letter' => 'A', 'cost' => 5, 'points' => 1, 'basicBenefits' => [H_POINTS => 2, H_TRASH_COINS => 3], 'genreBenefits' => [H_POINTS => 1]],
    2 => ['genre' => H_ADVENTURE, 'letter' => 'A', 'cost' => 7, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 2]],
    3 => ['genre' => H_ADVENTURE, 'letter' => 'B', 'cost' => 4, 'points' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 2]],
    4 => ['genre' => H_ADVENTURE, 'letter' => 'C', 'cost' => 3, 'basicBenefits' => [H_COINS => 1, H_TRASH_COINS => 2], 'genreBenefits' => [H_COINS => 1]],
    5 => ['genre' => H_ADVENTURE, 'letter' => 'C', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 1]],
    6 => ['genre' => H_ADVENTURE, 'letter' => 'D', 'cost' => 4, 'points' => 1, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_POINTS => 3]],
    7 => ['genre' => H_ADVENTURE, 'letter' => 'E', 'cost' => 3, 'basicBenefits' => [H_POINTS => 1, H_TRASH_COINS => 2], 'genreBenefits' => [H_POINTS => 1]],
    8 => ['genre' => H_ADVENTURE, 'letter' => 'F', 'cost' => 8, 'points' => 1, 'basicBenefits' => [H_POINTS => 5], 'genreBenefits' => [H_POINTS => 2]],
    9 => ['genre' => H_ADVENTURE, 'letter' => 'G', 'cost' => 2, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_POINTS => 2]],
    10 => ['genre' => H_ADVENTURE, 'letter' => 'G', 'cost' => 6, 'basicBenefits' => [H_POINTS => 4, H_TRASH_COINS => 4], 'genreBenefits' => [H_POINTS => 1]],
    11 => ['genre' => H_ADVENTURE, 'letter' => 'H', 'cost' => 3, 'points' => 3, 'basicBenefits' => [H_POINTS => 1, H_TRASH_POINTS => 1], 'genreBenefits' => [H_POINTS => 1]],
    12 => ['genre' => H_ADVENTURE, 'letter' => 'I', 'cost' => 3, 'timeless' => true, 'basicBenefits' => [H_POINTS => 2]],
    13 => ['genre' => H_ADVENTURE, 'letter' => 'I', 'cost' => 6, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 1]],
    14 => ['genre' => H_ADVENTURE, 'letter' => 'J', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2, H_TRASH_COINS => 2], 'genreBenefits' => [H_POINTS => 1]],
    15 => ['genre' => H_ADVENTURE, 'letter' => 'J', 'cost' => 5, 'basicBenefits' => [H_POINTS => 3, H_TRASH_POINTS => 2], 'genreBenefits' => [H_POINTS => 2]],
    16 => ['genre' => H_ADVENTURE, 'letter' => 'K', 'cost' => 9, 'points' => 2, 'basicBenefits' => [H_POINTS => 5], 'genreBenefits' => [H_POINTS => 3]],
    17 => ['genre' => H_ADVENTURE, 'letter' => 'L', 'cost' => 2, 'points' => 1, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_POINTS => 1]],
    18 => ['genre' => H_ADVENTURE, 'letter' => 'L', 'cost' => 4, 'points' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 1]],
    19 => ['genre' => H_ADVENTURE, 'letter' => 'M', 'cost' => 6, 'points' => 3, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 2]],
    20 => ['genre' => H_ADVENTURE, 'letter' => 'N', 'cost' => 4, 'points' => 1, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_COINS => 1, H_POINTS => 1]],
    21 => ['genre' => H_ADVENTURE, 'letter' => 'O', 'cost' => 6, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_SPECIAL_ADVENTURE => 2]],
    22 => ['genre' => H_ADVENTURE, 'letter' => 'P', 'cost' => 4, 'points' => 1, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 2]],
    23 => ['genre' => H_ADVENTURE, 'letter' => 'P', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 2]],
    24 => ['genre' => H_ADVENTURE, 'letter' => 'Q', 'cost' => 7, 'basicBenefits' => [H_POINTS => 3, H_TRASH_POINTS => 3], 'genreBenefits' => [H_POINTS => 4]],
    25 => ['genre' => H_ADVENTURE, 'letter' => 'R', 'cost' => 3, 'points' => 1, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_POINTS => 2]],
    26 => ['genre' => H_ADVENTURE, 'letter' => 'S', 'cost' => 5, 'points' => 1, 'basicBenefits' => [H_POINTS => 2, H_TRASH_POINTS => 2], 'genreBenefits' => [H_POINTS => 1]],
    27 => ['genre' => H_ADVENTURE, 'letter' => 'T', 'cost' => 4, 'points' => 2, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 1]],
    28 => ['genre' => H_ADVENTURE, 'letter' => 'U', 'cost' => 4, 'basicBenefits' => [H_POINTS => 1, H_TRASH_POINTS => 2], 'genreBenefits' => [H_POINTS => 3]],
    29 => ['genre' => H_ADVENTURE, 'letter' => 'V', 'cost' => 2, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_POINTS => 2]],
    30 => ['genre' => H_ADVENTURE, 'letter' => 'W', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2, H_TRASH_COINS => 2], 'genreBenefits' => [H_POINTS => 1]],
    31 => ['genre' => H_ADVENTURE, 'letter' => 'W', 'cost' => 5, 'points' => 2, 'timeless' => true, 'basicBenefits' => [H_POINTS => 2]],
    32 => ['genre' => H_ADVENTURE, 'letter' => 'X', 'cost' => 4, 'basicBenefits' => [H_COINS => 2, H_TRASH_POINTS => 2], 'genreBenefits' => [H_COINS => 2]],
    33 => ['genre' => H_ADVENTURE, 'letter' => 'Y', 'cost' => 2, 'basicBenefits' => [H_POINTS => 1, H_TRASH_COINS => 1], 'genreBenefits' => [H_POINTS => 1]],
    34 => ['genre' => H_ADVENTURE, 'letter' => 'Y', 'cost' => 4, 'points' => 3, 'basicBenefits' => [H_COINS => 2, H_TRASH_COINS => 2], 'genreBenefits' => [H_COINS => 1]],
    35 => ['genre' => H_ADVENTURE, 'letter' => 'Z', 'cost' => 5, 'points' => 3, 'basicBenefits' => [H_POINTS => 4], 'genreBenefits' => [H_POINTS => 1]],

    36 => ['genre' => H_HORROR, 'letter' => 'A', 'cost' => 3, 'basicBenefits' => [H_EITHER_BASIC => 2]],
    37 => ['genre' => H_HORROR, 'letter' => 'B', 'cost' => 6, 'basicBenefits' => [H_COINS => 3], 'genreBenefits' => [H_COINS => 2, H_EITHER_INK => true]],
    38 => ['genre' => H_HORROR, 'letter' => 'C', 'cost' => 5, 'basicBenefits' => [H_POINTS => 2, H_EITHER_INK => true], 'genreBenefits' => [H_POINTS => 1]],
    39 => ['genre' => H_HORROR, 'letter' => 'C', 'cost' => 8, 'basicBenefits' => [H_COINS => 2, H_EITHER_INK => true], 'genreBenefits' => [H_COINS => 3]],
    40 => ['genre' => H_HORROR, 'letter' => 'D', 'cost' => 4, 'timeless' => true, 'basicBenefits' => [H_EITHER_BASIC => 1], 'genreBenefits' => [H_COINS => 1, H_POINTS => 1]],
    41 => ['genre' => H_HORROR, 'letter' => 'D', 'cost' => 9, 'basicBenefits' => [H_POINTS => 3, H_EITHER_INK => true], 'genreBenefits' => [H_POINTS => 3]],
    42 => ['genre' => H_HORROR, 'letter' => 'E', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [H_EITHER_BASIC => 2], 'genreBenefits' => [H_POINTS => 1]],
    43 => ['genre' => H_HORROR, 'letter' => 'E', 'cost' => 8, 'basicBenefits' => [H_COINS => 2, H_EITHER_INK => true], 'genreBenefits' => [H_EITHER_GENRE => 2]],
    44 => ['genre' => H_HORROR, 'letter' => 'F', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_EITHER_GENRE => 2, H_EITHER_INK => true]],
    45 => ['genre' => H_HORROR, 'letter' => 'G', 'cost' => 4, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 2, H_EITHER_INK => true]],
    46 => ['genre' => H_HORROR, 'letter' => 'H', 'cost' => 7, 'basicBenefits' => [H_COINS => 1, H_POINTS => 2], 'genreBenefits' => [H_SPECIAL_HORROR => 1]],
    47 => ['genre' => H_HORROR, 'letter' => 'I', 'cost' => 4, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_EITHER_GENRE => 2]],
    48 => ['genre' => H_HORROR, 'letter' => 'J', 'cost' => 5, 'basicBenefits' => [H_POINTS => 3, H_EITHER_INK => true], 'genreBenefits' => [H_POINTS => 2]],
    49 => ['genre' => H_HORROR, 'letter' => 'K', 'cost' => 2, 'basicBenefits' => [H_EITHER_BASIC => 1], 'genreBenefits' => [H_COINS => 2]],
    50 => ['genre' => H_HORROR, 'letter' => 'L', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_EITHER_INK => true]],
    51 => ['genre' => H_HORROR, 'letter' => 'M', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 1]],
    52 => ['genre' => H_HORROR, 'letter' => 'N', 'cost' => 5, 'basicBenefits' => [H_COINS => 2, H_EITHER_INK => true], 'genreBenefits' => [H_COINS => 1]],
    53 => ['genre' => H_HORROR, 'letter' => 'N', 'cost' => 6, 'timeless' => true, 'basicBenefits' => [H_EITHER_BASIC => 1], 'genreBenefits' => [H_POINTS => 2, H_EITHER_INK => true]],
    54 => ['genre' => H_HORROR, 'letter' => 'O', 'cost' => 4, 'basicBenefits' => [H_EITHER_BASIC => 2], 'genreBenefits' => [H_EITHER_GENRE => 1]],
    55 => ['genre' => H_HORROR, 'letter' => 'P', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2, H_EITHER_INK => true]],
    56 => ['genre' => H_HORROR, 'letter' => 'Q', 'cost' => 4, 'basicBenefits' => [H_COINS => 3], 'genreBenefits' => [H_COINS => 1, H_EITHER_INK => true]],
    57 => ['genre' => H_HORROR, 'letter' => 'R', 'cost' => 4, 'basicBenefits' => [H_EITHER_BASIC => 1], 'genreBenefits' => [H_COINS => 2, H_EITHER_INK => true]],
    58 => ['genre' => H_HORROR, 'letter' => 'S', 'cost' => 2, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_POINTS => 1]],
    59 => ['genre' => H_HORROR, 'letter' => 'S', 'cost' => 7, 'basicBenefits' => [H_POINTS => 3, H_EITHER_INK => true], 'genreBenefits' => [H_POINTS => 1]],
    60 => ['genre' => H_HORROR, 'letter' => 'T', 'cost' => 4, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 1, H_EITHER_INK => true]],
    61 => ['genre' => H_HORROR, 'letter' => 'U', 'cost' => 2, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_POINTS => 2]],
    62 => ['genre' => H_HORROR, 'letter' => 'U', 'cost' => 6, 'basicBenefits' => [H_POINTS => 4], 'genreBenefits' => [H_POINTS => 1]],
    63 => ['genre' => H_HORROR, 'letter' => 'V', 'cost' => 4, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_COINS => 2, H_EITHER_INK => true]],
    64 => ['genre' => H_HORROR, 'letter' => 'V', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1, H_EITHER_INK => true]],
    65 => ['genre' => H_HORROR, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [H_POINTS => 2, H_EITHER_INK => true], 'genreBenefits' => [H_POINTS => 2]],
    66 => ['genre' => H_HORROR, 'letter' => 'W', 'cost' => 5, 'basicBenefits' => [H_COINS => 2, H_EITHER_INK => true], 'genreBenefits' => [H_COINS => 3]],
    67 => ['genre' => H_HORROR, 'letter' => 'X', 'cost' => 2, 'basicBenefits' => [H_EITHER_BASIC => 1], 'genreBenefits' => [H_POINTS => 2]],
    68 => ['genre' => H_HORROR, 'letter' => 'X', 'cost' => 6, 'basicBenefits' => [H_POINTS => 3, H_EITHER_INK => true], 'genreBenefits' => [H_POINTS => 3]],
    69 => ['genre' => H_HORROR, 'letter' => 'Y', 'cost' => 3, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_COINS => 1, H_EITHER_INK => true]],
    70 => ['genre' => H_HORROR, 'letter' => 'Z', 'cost' => 3, 'basicBenefits' => [H_EITHER_BASIC => 2], 'genreBenefits' => [H_EITHER_GENRE => 1, H_EITHER_INK => true]],

    71 => ['genre' => H_ROMANCE, 'letter' => 'A', 'cost' => 4, 'basicBenefits' => [H_COINS => 1, H_TRASH_DISCARD => 1], 'genreBenefits' => [H_COINS => 1]],
    72 => ['genre' => H_ROMANCE, 'letter' => 'B', 'cost' => 3, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_DOUBLE_ADJ => true]],
    73 => ['genre' => H_ROMANCE, 'letter' => 'B', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1, H_TRASH_DISCARD => 1]],
    74 => ['genre' => H_ROMANCE, 'letter' => 'C', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_TRASH_DISCARD => 1]],
    75 => ['genre' => H_ROMANCE, 'letter' => 'D', 'cost' => 4, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_DOUBLE_ADJ => true]],
    76 => ['genre' => H_ROMANCE, 'letter' => 'E', 'cost' => 2, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1]],
    77 => ['genre' => H_ROMANCE, 'letter' => 'E', 'cost' => 6, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_TRASH_DISCARD => 1]],
    78 => ['genre' => H_ROMANCE, 'letter' => 'F', 'cost' => 4, 'basicBenefits' => [H_POINTS => 2, H_TRASH_DISCARD => 1], 'genreBenefits' => [H_POINTS => 1]],
    79 => ['genre' => H_ROMANCE, 'letter' => 'F', 'cost' => 6, 'basicBenefits' => [H_COINS => 2, H_DOUBLE_ADJ => true], 'genreBenefits' => [H_COINS => 1, H_TRASH_DISCARD => 1]],
    80 => ['genre' => H_ROMANCE, 'letter' => 'G', 'cost' => 3, 'basicBenefits' => [H_POINTS => 1, H_TRASH_DISCARD => 1], 'genreBenefits' => [H_POINTS => 1]],
    81 => ['genre' => H_ROMANCE, 'letter' => 'H', 'cost' => 3, 'basicBenefits' => [H_COINS => 1, H_TRASH_DISCARD => 1], 'genreBenefits' => [H_COINS => 1]],
    82 => ['genre' => H_ROMANCE, 'letter' => 'H', 'cost' => 7, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 2, H_DOUBLE_ADJ => true]],
    83 => ['genre' => H_ROMANCE, 'letter' => 'I', 'cost' => 2, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1]],
    84 => ['genre' => H_ROMANCE, 'letter' => 'J', 'cost' => 6, 'basicBenefits' => [H_COINS => 2, H_DOUBLE_ADJ => true], 'genreBenefits' => [H_COINS => 2, H_TRASH_DISCARD => 1]],
    85 => ['genre' => H_ROMANCE, 'letter' => 'K', 'cost' => 3, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_COINS => 1, H_TRASH_DISCARD => 1]],
    86 => ['genre' => H_ROMANCE, 'letter' => 'K', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1, H_TRASH_DISCARD => 1]],
    87 => ['genre' => H_ROMANCE, 'letter' => 'L', 'cost' => 8, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 2, H_DOUBLE_ADJ => true]],
    88 => ['genre' => H_ROMANCE, 'letter' => 'M', 'cost' => 2, 'basicBenefits' => [H_COINS => 1, H_TRASH_DISCARD => 1]],
    89 => ['genre' => H_ROMANCE, 'letter' => 'N', 'cost' => 2, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_TRASH_DISCARD => 1]],
    90 => ['genre' => H_ROMANCE, 'letter' => 'N', 'cost' => 5, 'basicBenefits' => [H_COINS => 2, H_TRASH_DISCARD => 1], 'genreBenefits' => [H_COINS => 1]],
    91 => ['genre' => H_ROMANCE, 'letter' => 'O', 'cost' => 4, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_DOUBLE_ADJ => true]],
    92 => ['genre' => H_ROMANCE, 'letter' => 'O', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [H_COINS => 2, H_POINTS => 1], 'genreBenefits' => [H_COINS => 1, H_POINTS => 1]],
    93 => ['genre' => H_ROMANCE, 'letter' => 'P', 'cost' => 6, 'basicBenefits' => [H_POINTS => 2, H_DOUBLE_ADJ => true], 'genreBenefits' => [H_POINTS => 1]],
    94 => ['genre' => H_ROMANCE, 'letter' => 'Q', 'cost' => 4, 'basicBenefits' => [H_POINTS => 2, H_TRASH_DISCARD => 1], 'genreBenefits' => [H_POINTS => 2]],
    95 => ['genre' => H_ROMANCE, 'letter' => 'R', 'cost' => 5, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 1, H_SPECIAL_ROMANCE => true]],
    96 => ['genre' => H_ROMANCE, 'letter' => 'R', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_TRASH_DISCARD => 1]],
    97 => ['genre' => H_ROMANCE, 'letter' => 'S', 'cost' => 4, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_POINTS => 1, H_DOUBLE_ADJ => true]],
    98 => ['genre' => H_ROMANCE, 'letter' => 'T', 'cost' => 3, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1, H_TRASH_DISCARD => 1]],
    99 => ['genre' => H_ROMANCE, 'letter' => 'U', 'cost' => 9, 'basicBenefits' => [H_POINTS => 5], 'genreBenefits' => [H_POINTS => 1, H_DOUBLE_ADJ => true]],
    100 => ['genre' => H_ROMANCE, 'letter' => 'V', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 1, H_TRASH_DISCARD => 1]],
    101 => ['genre' => H_ROMANCE, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [H_POINTS => 1, H_DOUBLE_ADJ => true], 'genreBenefits' => [H_POINTS => 1]],
    102 => ['genre' => H_ROMANCE, 'letter' => 'X', 'cost' => 7, 'basicBenefits' => [H_POINTS => 4, H_TRASH_DISCARD => 1], 'genreBenefits' => [H_POINTS => 2]],
    103 => ['genre' => H_ROMANCE, 'letter' => 'Y', 'cost' => 4, 'basicBenefits' => [H_POINTS => 1, H_DOUBLE_ADJ => true], 'genreBenefits' => [H_TRASH_DISCARD => 1]],
    104 => ['genre' => H_ROMANCE, 'letter' => 'Z', 'cost' => 4, 'basicBenefits' => [H_POINTS => 2, H_TRASH_DISCARD => 1], 'genreBenefits' => [H_POINTS => 2]],
    105 => ['genre' => H_ROMANCE, 'letter' => 'Z', 'cost' => 5, 'basicBenefits' => [H_POINTS => 2, H_DOUBLE_ADJ => true], 'genreBenefits' => [H_COINS => 2]],

    106 => ['genre' => H_MYSTERY, 'letter' => 'A', 'cost' => 3, 'basicBenefits' => [H_POINTS => 1, H_UNCOVER_ADJ => true]],
    107 => ['genre' => H_MYSTERY, 'letter' => 'A', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_COINS => 1]],
    108 => ['genre' => H_MYSTERY, 'letter' => 'B', 'cost' => 4, 'basicBenefits' => [H_COINS => 2, H_JAIL => true], 'genreBenefits' => [H_COINS => 2]],
    109 => ['genre' => H_MYSTERY, 'letter' => 'C', 'cost' => 5, 'basicBenefits' => [H_POINTS => 2, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_JAIL => true]],
    110 => ['genre' => H_MYSTERY, 'letter' => 'D', 'cost' => 4, 'basicBenefits' => [H_POINTS => 1, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_POINTS => 2]],
    111 => ['genre' => H_MYSTERY, 'letter' => 'E', 'cost' => 4, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_UNCOVER_ADJ => true]],
    112 => ['genre' => H_MYSTERY, 'letter' => 'F', 'cost' => 2, 'basicBenefits' => [H_POINTS => 1, H_JAIL => true], 'genreBenefits' => [H_POINTS => 1]],
    113 => ['genre' => H_MYSTERY, 'letter' => 'F', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [H_POINTS => 1, H_JAIL => true], 'genreBenefits' => [H_POINTS => 1]],
    114 => ['genre' => H_MYSTERY, 'letter' => 'G', 'cost' => 6, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 2, H_UNCOVER_ADJ => true]],
    115 => ['genre' => H_MYSTERY, 'letter' => 'H', 'cost' => 3, 'basicBenefits' => [H_POINTS => 1, H_JAIL => true], 'genreBenefits' => [H_POINTS => 2]],
    116 => ['genre' => H_MYSTERY, 'letter' => 'I', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_JAIL => true]],
    117 => ['genre' => H_MYSTERY, 'letter' => 'I', 'cost' => 5, 'basicBenefits' => [H_COINS => 2, H_UNCOVER_ADJ => true]],
    118 => ['genre' => H_MYSTERY, 'letter' => 'J', 'cost' => 8, 'basicBenefits' => [H_POINTS => 5, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_POINTS => 2]],
    119 => ['genre' => H_MYSTERY, 'letter' => 'K', 'cost' => 2, 'basicBenefits' => [H_POINTS => 1], 'genreBenefits' => [H_POINTS => 1, H_UNCOVER_ADJ => true]],
    120 => ['genre' => H_MYSTERY, 'letter' => 'K', 'cost' => 4, 'basicBenefits' => [H_COINS => 2, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_COINS => 2]],
    121 => ['genre' => H_MYSTERY, 'letter' => 'L', 'cost' => 6, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 1, H_UNCOVER_ADJ => true]],
    122 => ['genre' => H_MYSTERY, 'letter' => 'M', 'cost' => 3, 'basicBenefits' => [H_COINS => 1, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_COINS => 1]],
    123 => ['genre' => H_MYSTERY, 'letter' => 'M', 'cost' => 4, 'timeless' => true, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1, H_JAIL => true]],
    124 => ['genre' => H_MYSTERY, 'letter' => 'N', 'cost' => 7, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 1, H_SPECIAL_MYSTERY => 1]],
    125 => ['genre' => H_MYSTERY, 'letter' => 'O', 'cost' => 3, 'basicBenefits' => [H_POINTS => 1, H_JAIL => true], 'genreBenefits' => [H_UNCOVER_ADJ => true]],
    126 => ['genre' => H_MYSTERY, 'letter' => 'P', 'cost' => 2, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1, H_JAIL => true]],
    127 => ['genre' => H_MYSTERY, 'letter' => 'P', 'cost' => 4, 'basicBenefits' => [H_POINTS => 1, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_POINTS => 2]],
    128 => ['genre' => H_MYSTERY, 'letter' => 'Q', 'cost' => 3, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 2, H_UNCOVER_ADJ => true]],
    129 => ['genre' => H_MYSTERY, 'letter' => 'Q', 'cost' => 5, 'basicBenefits' => [H_POINTS => 2, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_POINTS => 3]],
    130 => ['genre' => H_MYSTERY, 'letter' => 'R', 'cost' => 4, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 2, H_UNCOVER_ADJ => true]],
    131 => ['genre' => H_MYSTERY, 'letter' => 'R', 'cost' => 6, 'basicBenefits' => [H_POINTS => 2, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_POINTS => 1, H_JAIL => true]],
    132 => ['genre' => H_MYSTERY, 'letter' => 'S', 'cost' => 4, 'basicBenefits' => [H_COINS => 1, H_JAIL => true], 'genreBenefits' => [H_COINS => 2]],
    133 => ['genre' => H_MYSTERY, 'letter' => 'T', 'cost' => 6, 'basicBenefits' => [H_POINTS => 2, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_POINTS => 2]],
    134 => ['genre' => H_MYSTERY, 'letter' => 'T', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [H_POINTS => 2], 'genreBenefits' => [H_POINTS => 2, H_JAIL => true]],
    135 => ['genre' => H_MYSTERY, 'letter' => 'U', 'cost' => 2, 'basicBenefits' => [H_COINS => 1], 'genreBenefits' => [H_COINS => 1, H_UNCOVER_ADJ => true]],
    136 => ['genre' => H_MYSTERY, 'letter' => 'V', 'cost' => 9, 'basicBenefits' => [H_POINTS => 4, H_UNCOVER_ADJ => true], 'genreBenefits' => [H_POINTS => 4]],
    137 => ['genre' => H_MYSTERY, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [H_COINS => 2], 'genreBenefits' => [H_COINS => 2, H_UNCOVER_ADJ => true]],
    138 => ['genre' => H_MYSTERY, 'letter' => 'X', 'cost' => 3, 'basicBenefits' => [H_POINTS => 3, H_JAIL => true]],
    139 => ['genre' => H_MYSTERY, 'letter' => 'Y', 'cost' => 7, 'basicBenefits' => [H_POINTS => 4], 'genreBenefits' => [H_POINTS => 2, H_UNCOVER_ADJ => true]],
    140 => ['genre' => H_MYSTERY, 'letter' => 'Z', 'cost' => 5, 'basicBenefits' => [H_POINTS => 3], 'genreBenefits' => [H_POINTS => 2, H_UNCOVER_ADJ => true]],

    141 => ['genre' => H_STARTER, 'letter' => 'A', 'cost' => 0, 'basicBenefits' => [H_COINS => 1]],
    142 => ['genre' => H_STARTER, 'letter' => 'B', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    143 => ['genre' => H_STARTER, 'letter' => 'C', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    144 => ['genre' => H_STARTER, 'letter' => 'D', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    145 => ['genre' => H_STARTER, 'letter' => 'E', 'cost' => 0, 'basicBenefits' => [H_COINS => 1]],
    146 => ['genre' => H_STARTER, 'letter' => 'G', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    147 => ['genre' => H_STARTER, 'letter' => 'H', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    148 => ['genre' => H_STARTER, 'letter' => 'I', 'cost' => 0, 'basicBenefits' => [H_COINS => 1]],
    149 => ['genre' => H_STARTER, 'letter' => 'L', 'cost' => 0, 'basicBenefits' => [H_COINS => 1]],
    150 => ['genre' => H_STARTER, 'letter' => 'M', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    151 => ['genre' => H_STARTER, 'letter' => 'N', 'cost' => 0, 'basicBenefits' => [H_COINS => 1]],
    152 => ['genre' => H_STARTER, 'letter' => 'O', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    153 => ['genre' => H_STARTER, 'letter' => 'P', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    154 => ['genre' => H_STARTER, 'letter' => 'R', 'cost' => 0, 'basicBenefits' => [H_COINS => 1]],
    155 => ['genre' => H_STARTER, 'letter' => 'S', 'cost' => 0, 'basicBenefits' => [H_COINS => 1]],
    156 => ['genre' => H_STARTER, 'letter' => 'T', 'cost' => 0, 'basicBenefits' => [H_COINS => 1]],
    157 => ['genre' => H_STARTER, 'letter' => 'U', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
    158 => ['genre' => H_STARTER, 'letter' => 'Y', 'cost' => 0, 'basicBenefits' => [H_POINTS => 1]],
];
