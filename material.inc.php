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
    6 => 3,
    9 => 6,
    12 => 10,
    15 => 15,
    18 => 20,
];

$this->signatures = [
    ADVENTURE => [
        'title' => clienttranslate('Adventures in Peril'),
        'description' => clienttranslate('If there is an Adventure card in the offer row, Penny gains 1${star} for each card purchased by any player'),
    ],
    HORROR => [
        'title' => clienttranslate('The Horrors of Fear'),
        'description' => clienttranslate('If there is a Horror card in the offer row, Penny gains 1${star} for each ink used by any player'),
    ],
    MYSTERY => [
        'title' => clienttranslate('A Mysterious Mystery'),
        'description' => clienttranslate('Each time Penny claims a Mystery card, she also jails the cheapest card in the offer row'),
    ],
    ROMANCE => [
        'title' => clienttranslate('Romancing the Heart'),
        'description' => clienttranslate('Double the value of each Romance card Penny claims from the offer row'),
    ],
];

$this->dicts = [
    TWELVEDICTS => clienttranslate('12dicts'),
    US => clienttranslate('American Scrabble'),
    UK => clienttranslate('British Scrabble'),
    MORPHALOU => clienttranslate('Morphalou'),
    YANDEX => clienttranslate('Yandex.Dictionary'),
    VOTE_50 => clienttranslate('Majority Vote'),
    VOTE_100 => clienttranslate('Unanimous Vote'),
];

$this->langs = [
    LANG_EN => 'English',
    LANG_DE => 'Deutsch',
    LANG_FR => 'Français',
    LANG_ES => 'Español',
    LANG_IT => 'Italiano',
];

$this->i18n = [
    'starter' => clienttranslate('Starter'),
    'adventure' => clienttranslate('Adventure'),
    'horror' => clienttranslate('Horror'),
    'mystery' => clienttranslate('Mystery'),
    'romance' => clienttranslate('Romance'),

    'adverts' => clienttranslate('Adverts'),
    'award' => clienttranslate('Literary Awards'),
    'browserWarnDesc' => clienttranslate('Some game features are unavailable'),
    'browserWarnTitle' => clienttranslate('Update your browser for a better experience'),
    'confirmButton' => clienttranslate('Confirm Word'),
    'convertButton' => clienttranslate('Convert ${ink} Ink to ${coins}¢'),
    'deck' => clienttranslate('Deck'),
    'dictionary' => clienttranslate('Dictionary definitions:'),
    'discardButton' => clienttranslate('Discard'),
    'doctorButton' => clienttranslate('${points}${icon} Advert (${coins}¢)'),
    'doubleButton' => clienttranslate('Double'),
    'draw' => clienttranslate('Draw With Ink (${count})'),
    'finalRound' => clienttranslate('This is the final round!'),
    'first' => clienttranslate('First Player'),
    'flushButton' => clienttranslate('Flush Offer Row'),
    'genreCountsTip' => clienttranslate('${player_name}\'s deck'),
    'genreTip' => clienttranslate('With multiple ${x} cards'),
    'hand' => clienttranslate('Hand'),
    'handReminder' => clienttranslate('All your cards are already in play'),
    'ink' => clienttranslate('Ink'),
    'invalid' => clienttranslate('${word} is rejected by ${dict} (${lang})'),
    'jailButton' => clienttranslate('Jail'),
    'jailButton' => clienttranslate('Jail'),
    'jailTip' => clienttranslate('Jailed: Only ${player_name} may purchase'),
    'jailWarning' => clienttranslate('${genre}${letter} currently in jail will be trashed'),
    'keyboard' => clienttranslate('Click or type the wild letter'),
    'myDiscard' => clienttranslate('My Discard Pile (${count})'),
    'myHand' => clienttranslate('My Hand (${count})'),
    'offer' => clienttranslate('Offer Row'),
    'penny' => clienttranslate('Penny Dreadful'),
    'playAll' => clienttranslate('Play All'),
    'previewButton' => clienttranslate('Preview'),
    'purchaseButton' => clienttranslate('Purchase (${coins}¢)'),
    'remover' => clienttranslate('Remover'),
    'resetAll' => clienttranslate('Reset All'),
    'resetButton' => clienttranslate('Reset (${x})'),
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
    'tableau' => clienttranslate('${player_name}\'s Word (${count})'),
    'timeless' => clienttranslate('Timeless Classics (${count})'),
    'timelessTip' => clienttranslate('Timeless Classic: ${player_name} receives benefits each turn'),
    'trashButton' => clienttranslate('Trash'),
    'trashCoinsButton' => clienttranslate('Trash (${coins}¢)'),
    'uncoverButton' => clienttranslate('Uncover (${x})'),
    'useRemoverButton' => clienttranslate('Remove Ink'),
    'wildButton' => clienttranslate('Wild'),
    'voteAcceptButton' => clienttranslate('Accept ${word}'),
    'voteRejectButton' => clienttranslate('Reject ${word}'),
];

$this->msg = [
    'acceptedWord' => clienttranslate('${word} is accepted by ${dict}'),
    'awardEnd' => clienttranslate('${player_name} earns ${amount}${icon} for the literary award'),
    'awardFirst' => clienttranslate('${player_name} spells the first ${length}-letter word and takes the literary award (worth ${points}${iconPoints} at game end)${award}'),
    'awardSecond' => clienttranslate('${player_name} spells the first ${length}-letter word and takes the literary award from ${player_name2} (worth ${points}${iconPoints} at game end)${award}'),
    'confirmWord' => clienttranslate('${player_name} spells ${word}${definitions}'),
    'convert' => clienttranslate('${player_name} converts ${ink} ink to ${amount}${icon}'),
    'coopLose' => clienttranslate('Failure! The players are defeated and ${player_name} wins!'),
    'coopWin' => clienttranslate('Success! ${player_name} is defeated and the players win!'),
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
    'errorWild' => self::_('Cards cannot be wild: %s'),
    'flush' => clienttranslate('${player_name} flushes the offer row'),
    'jailOffer' => clienttranslate('${player_name} jails ${genre}${letter} from the offer row'),
    'preview' => clienttranslate('${player_name} previews ${count} cards from their deck'),
    'purchase' => clienttranslate('${player_name} purchases ${genre}${letter} for ${coins}${iconCoins}'),
    'purchase2' => clienttranslate('${player_name} purchases ${genre}${letter} for ${coins}${iconCoins} and earns ${points}${iconPoints}'),
    'purchaseAdvert' => clienttranslate('${player_name} purchases the ${points}${iconPoints} advertisement for ${coins}${iconCoins}'),
    'purchaseCoop' => clienttranslate('${player_name} purchases ${genre}${letter} and earns ${points}${iconPoints}'),
    'rejectedWord' => $this->i18n['invalid'],
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
    'votesReject' => clienttranslate('${player_name} rejects ${word}'),
];

$this->benefits = [
    COINS => [
        'short' => '${value}¢',
        'long' => clienttranslate('Gain ${value}¢'),
    ],
    DOUBLE_ADJ => [
        'short' => clienttranslate('Double'),
        'long' => clienttranslate('Double the ¢ and ${star} values of an adjacent card'),
    ],
    EITHER_BASIC => [
        'short' => clienttranslate('${value}¢ or ${star}'),
        'long' => clienttranslate('Gain ${value}¢ or ${value}${star}'),
    ],
    EITHER_GENRE => [
        'short' => clienttranslate('${value}¢ or ${star}'),
        'long' => clienttranslate('Gain ${value}¢ or ${value}${star}'),
    ],
    EITHER_INK => [
        'short' => clienttranslate('Ink'),
        'long' => clienttranslate('Gain an ink or remover'),
    ],
    JAIL => [
        'short' => clienttranslate('Jail'),
        'long' => clienttranslate('Reserve or remove an offer row card so others cannot purchase it'),
    ],
    POINTS => [
        'short' => '${value}${star}',
        'long' => clienttranslate('Gain ${value}${star}'),
    ],
    SPECIAL_ADVENTURE => [
        'short' => '${value}¢/${adventure}',
        'long' => clienttranslate('Gain ${value}¢ for each ${adventure} card in play'),
    ],
    SPECIAL_HORROR => [
        'short' => clienttranslate('${value}${star}/Ink'),
        'long' => clienttranslate('Gain ${value}${star} for each inked card in play'),
    ],
    SPECIAL_MYSTERY => [
        'short' => clienttranslate('${value}${star}/Wild'),
        'long' => clienttranslate('Gain ${value}${star} for each wild card in play'),
    ],
    SPECIAL_ROMANCE => [
        'short' => clienttranslate('Preview'),
        'long' => clienttranslate('Preview 3 cards from your deck. Return or discard each.'),
    ],
    TRASH_COINS => [
        'short' => clienttranslate('Trash: ${value}¢'),
        'long' => clienttranslate('Remove this card to gain ${value}¢'),
    ],
    TRASH_DISCARD => [
        'short' => clienttranslate('Dump: ${value}¢'),
        'long' => clienttranslate('Remove a discarded card to gain ${value}¢'),
    ],
    TRASH_POINTS => [
        'short' => clienttranslate('Trash: ${value}${star}'),
        'long' => clienttranslate('Remove this card to gain ${value}${star}'),
    ],
    UNCOVER_ADJ => [
        'short' => clienttranslate('Uncover'),
        'long' => clienttranslate('Uncover an adjacent wild card'),
    ],
];

// UPDATE card SET `location` = 'hand_2305326', `origin` = 'hand_2305326' WHERE `refId` IN (5, 12, 23, 31, 40, 42, 53, 64, 73, 86, 92, 96, 107, 113, 123, 134) OR `refId` IN (1, 7, 21, 26, 36, 43, 46, 71, 76, 95, 106, 111, 124);

$this->cards = [
    1 => ['genre' => ADVENTURE, 'letter' => 'A', 'cost' => 5, 'points' => 1, 'basicBenefits' => [POINTS => 2, TRASH_COINS => 3], 'genreBenefits' => [POINTS => 1]],
    2 => ['genre' => ADVENTURE, 'letter' => 'A', 'cost' => 7, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2]],
    3 => ['genre' => ADVENTURE, 'letter' => 'B', 'cost' => 4, 'points' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
    4 => ['genre' => ADVENTURE, 'letter' => 'C', 'cost' => 3, 'basicBenefits' => [COINS => 1, TRASH_COINS => 2], 'genreBenefits' => [COINS => 1]],
    5 => ['genre' => ADVENTURE, 'letter' => 'C', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
    6 => ['genre' => ADVENTURE, 'letter' => 'D', 'cost' => 4, 'points' => 1, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [POINTS => 3]],
    7 => ['genre' => ADVENTURE, 'letter' => 'E', 'cost' => 3, 'basicBenefits' => [POINTS => 1, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
    8 => ['genre' => ADVENTURE, 'letter' => 'F', 'cost' => 8, 'points' => 1, 'basicBenefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 2]],
    9 => ['genre' => ADVENTURE, 'letter' => 'G', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
    10 => ['genre' => ADVENTURE, 'letter' => 'G', 'cost' => 6, 'basicBenefits' => [POINTS => 4, TRASH_COINS => 4], 'genreBenefits' => [POINTS => 1]],
    11 => ['genre' => ADVENTURE, 'letter' => 'H', 'cost' => 3, 'points' => 3, 'basicBenefits' => [POINTS => 1, TRASH_POINTS => 1], 'genreBenefits' => [POINTS => 1]],
    12 => ['genre' => ADVENTURE, 'letter' => 'I', 'cost' => 3, 'timeless' => true, 'basicBenefits' => [POINTS => 2]],
    13 => ['genre' => ADVENTURE, 'letter' => 'I', 'cost' => 6, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1]],
    14 => ['genre' => ADVENTURE, 'letter' => 'J', 'cost' => 3, 'basicBenefits' => [POINTS => 2, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
    15 => ['genre' => ADVENTURE, 'letter' => 'J', 'cost' => 5, 'basicBenefits' => [POINTS => 3, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 2]],
    16 => ['genre' => ADVENTURE, 'letter' => 'K', 'cost' => 9, 'points' => 2, 'basicBenefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 3]],
    17 => ['genre' => ADVENTURE, 'letter' => 'L', 'cost' => 2, 'points' => 1, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1]],
    18 => ['genre' => ADVENTURE, 'letter' => 'L', 'cost' => 4, 'points' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
    19 => ['genre' => ADVENTURE, 'letter' => 'M', 'cost' => 6, 'points' => 3, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2]],
    20 => ['genre' => ADVENTURE, 'letter' => 'N', 'cost' => 4, 'points' => 1, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, POINTS => 1]],
    21 => ['genre' => ADVENTURE, 'letter' => 'O', 'cost' => 6, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [SPECIAL_ADVENTURE => 2]],
    22 => ['genre' => ADVENTURE, 'letter' => 'P', 'cost' => 4, 'points' => 1, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
    23 => ['genre' => ADVENTURE, 'letter' => 'P', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
    24 => ['genre' => ADVENTURE, 'letter' => 'Q', 'cost' => 7, 'basicBenefits' => [POINTS => 3, TRASH_POINTS => 3], 'genreBenefits' => [POINTS => 4]],
    25 => ['genre' => ADVENTURE, 'letter' => 'R', 'cost' => 3, 'points' => 1, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
    26 => ['genre' => ADVENTURE, 'letter' => 'S', 'cost' => 5, 'points' => 1, 'basicBenefits' => [POINTS => 2, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 1]],
    27 => ['genre' => ADVENTURE, 'letter' => 'T', 'cost' => 4, 'points' => 2, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
    28 => ['genre' => ADVENTURE, 'letter' => 'U', 'cost' => 4, 'basicBenefits' => [POINTS => 1, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 3]],
    29 => ['genre' => ADVENTURE, 'letter' => 'V', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
    30 => ['genre' => ADVENTURE, 'letter' => 'W', 'cost' => 3, 'basicBenefits' => [POINTS => 2, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
    31 => ['genre' => ADVENTURE, 'letter' => 'W', 'cost' => 5, 'points' => 2, 'timeless' => true, 'basicBenefits' => [POINTS => 2]],
    32 => ['genre' => ADVENTURE, 'letter' => 'X', 'cost' => 4, 'basicBenefits' => [COINS => 2, TRASH_POINTS => 2], 'genreBenefits' => [COINS => 2]],
    33 => ['genre' => ADVENTURE, 'letter' => 'Y', 'cost' => 2, 'basicBenefits' => [POINTS => 1, TRASH_COINS => 1], 'genreBenefits' => [POINTS => 1]],
    34 => ['genre' => ADVENTURE, 'letter' => 'Y', 'cost' => 4, 'points' => 3, 'basicBenefits' => [COINS => 2, TRASH_COINS => 2], 'genreBenefits' => [COINS => 1]],
    35 => ['genre' => ADVENTURE, 'letter' => 'Z', 'cost' => 5, 'points' => 3, 'basicBenefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 1]],

    36 => ['genre' => HORROR, 'letter' => 'A', 'cost' => 3, 'basicBenefits' => [EITHER_BASIC => 2]],
    37 => ['genre' => HORROR, 'letter' => 'B', 'cost' => 6, 'basicBenefits' => [COINS => 3], 'genreBenefits' => [COINS => 2, EITHER_INK => true]],
    38 => ['genre' => HORROR, 'letter' => 'C', 'cost' => 5, 'basicBenefits' => [POINTS => 2, EITHER_INK => true], 'genreBenefits' => [POINTS => 1]],
    39 => ['genre' => HORROR, 'letter' => 'C', 'cost' => 8, 'basicBenefits' => [COINS => 2, EITHER_INK => true], 'genreBenefits' => [COINS => 3]],
    40 => ['genre' => HORROR, 'letter' => 'D', 'cost' => 4, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 1, POINTS => 1]],
    41 => ['genre' => HORROR, 'letter' => 'D', 'cost' => 9, 'basicBenefits' => [POINTS => 3, EITHER_INK => true], 'genreBenefits' => [POINTS => 3]],
    42 => ['genre' => HORROR, 'letter' => 'E', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [POINTS => 1]],
    43 => ['genre' => HORROR, 'letter' => 'E', 'cost' => 8, 'basicBenefits' => [COINS => 2, EITHER_INK => true], 'genreBenefits' => [EITHER_GENRE => 2]],
    44 => ['genre' => HORROR, 'letter' => 'F', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [EITHER_GENRE => 2, EITHER_INK => true]],
    45 => ['genre' => HORROR, 'letter' => 'G', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, EITHER_INK => true]],
    46 => ['genre' => HORROR, 'letter' => 'H', 'cost' => 7, 'basicBenefits' => [COINS => 1, POINTS => 2], 'genreBenefits' => [SPECIAL_HORROR => 1]],
    47 => ['genre' => HORROR, 'letter' => 'I', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [EITHER_GENRE => 2]],
    48 => ['genre' => HORROR, 'letter' => 'J', 'cost' => 5, 'basicBenefits' => [POINTS => 3, EITHER_INK => true], 'genreBenefits' => [POINTS => 2]],
    49 => ['genre' => HORROR, 'letter' => 'K', 'cost' => 2, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 2]],
    50 => ['genre' => HORROR, 'letter' => 'L', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [EITHER_INK => true]],
    51 => ['genre' => HORROR, 'letter' => 'M', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
    52 => ['genre' => HORROR, 'letter' => 'N', 'cost' => 5, 'basicBenefits' => [COINS => 2, EITHER_INK => true], 'genreBenefits' => [COINS => 1]],
    53 => ['genre' => HORROR, 'letter' => 'N', 'cost' => 6, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [POINTS => 2, EITHER_INK => true]],
    54 => ['genre' => HORROR, 'letter' => 'O', 'cost' => 4, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [EITHER_GENRE => 1]],
    55 => ['genre' => HORROR, 'letter' => 'P', 'cost' => 3, 'basicBenefits' => [POINTS => 2, EITHER_INK => true]],
    56 => ['genre' => HORROR, 'letter' => 'Q', 'cost' => 4, 'basicBenefits' => [COINS => 3], 'genreBenefits' => [COINS => 1, EITHER_INK => true]],
    57 => ['genre' => HORROR, 'letter' => 'R', 'cost' => 4, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 2, EITHER_INK => true]],
    58 => ['genre' => HORROR, 'letter' => 'S', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1]],
    59 => ['genre' => HORROR, 'letter' => 'S', 'cost' => 7, 'basicBenefits' => [POINTS => 3, EITHER_INK => true], 'genreBenefits' => [POINTS => 1]],
    60 => ['genre' => HORROR, 'letter' => 'T', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, EITHER_INK => true]],
    61 => ['genre' => HORROR, 'letter' => 'U', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
    62 => ['genre' => HORROR, 'letter' => 'U', 'cost' => 6, 'basicBenefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 1]],
    63 => ['genre' => HORROR, 'letter' => 'V', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 2, EITHER_INK => true]],
    64 => ['genre' => HORROR, 'letter' => 'V', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, EITHER_INK => true]],
    65 => ['genre' => HORROR, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [POINTS => 2, EITHER_INK => true], 'genreBenefits' => [POINTS => 2]],
    66 => ['genre' => HORROR, 'letter' => 'W', 'cost' => 5, 'basicBenefits' => [COINS => 2, EITHER_INK => true], 'genreBenefits' => [COINS => 3]],
    67 => ['genre' => HORROR, 'letter' => 'X', 'cost' => 2, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [POINTS => 2]],
    68 => ['genre' => HORROR, 'letter' => 'X', 'cost' => 6, 'basicBenefits' => [POINTS => 3, EITHER_INK => true], 'genreBenefits' => [POINTS => 3]],
    69 => ['genre' => HORROR, 'letter' => 'Y', 'cost' => 3, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, EITHER_INK => true]],
    70 => ['genre' => HORROR, 'letter' => 'Z', 'cost' => 3, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [EITHER_GENRE => 1, EITHER_INK => true]],

    71 => ['genre' => ROMANCE, 'letter' => 'A', 'cost' => 4, 'basicBenefits' => [COINS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
    72 => ['genre' => ROMANCE, 'letter' => 'B', 'cost' => 3, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [DOUBLE_ADJ => true]],
    73 => ['genre' => ROMANCE, 'letter' => 'B', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
    74 => ['genre' => ROMANCE, 'letter' => 'C', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [TRASH_DISCARD => 1]],
    75 => ['genre' => ROMANCE, 'letter' => 'D', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [DOUBLE_ADJ => true]],
    76 => ['genre' => ROMANCE, 'letter' => 'E', 'cost' => 2, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1]],
    77 => ['genre' => ROMANCE, 'letter' => 'E', 'cost' => 6, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [TRASH_DISCARD => 1]],
    78 => ['genre' => ROMANCE, 'letter' => 'F', 'cost' => 4, 'basicBenefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 1]],
    79 => ['genre' => ROMANCE, 'letter' => 'F', 'cost' => 6, 'basicBenefits' => [COINS => 2, DOUBLE_ADJ => true], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
    80 => ['genre' => ROMANCE, 'letter' => 'G', 'cost' => 3, 'basicBenefits' => [POINTS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 1]],
    81 => ['genre' => ROMANCE, 'letter' => 'H', 'cost' => 3, 'basicBenefits' => [COINS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
    82 => ['genre' => ROMANCE, 'letter' => 'H', 'cost' => 7, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, DOUBLE_ADJ => true]],
    83 => ['genre' => ROMANCE, 'letter' => 'I', 'cost' => 2, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1]],
    84 => ['genre' => ROMANCE, 'letter' => 'J', 'cost' => 6, 'basicBenefits' => [COINS => 2, DOUBLE_ADJ => true], 'genreBenefits' => [COINS => 2, TRASH_DISCARD => 1]],
    85 => ['genre' => ROMANCE, 'letter' => 'K', 'cost' => 3, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
    86 => ['genre' => ROMANCE, 'letter' => 'K', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
    87 => ['genre' => ROMANCE, 'letter' => 'L', 'cost' => 8, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, DOUBLE_ADJ => true]],
    88 => ['genre' => ROMANCE, 'letter' => 'M', 'cost' => 2, 'basicBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
    89 => ['genre' => ROMANCE, 'letter' => 'N', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [TRASH_DISCARD => 1]],
    90 => ['genre' => ROMANCE, 'letter' => 'N', 'cost' => 5, 'basicBenefits' => [COINS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
    91 => ['genre' => ROMANCE, 'letter' => 'O', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [DOUBLE_ADJ => true]],
    92 => ['genre' => ROMANCE, 'letter' => 'O', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [COINS => 2, POINTS => 1], 'genreBenefits' => [COINS => 1, POINTS => 1]],
    93 => ['genre' => ROMANCE, 'letter' => 'P', 'cost' => 6, 'basicBenefits' => [POINTS => 2, DOUBLE_ADJ => true], 'genreBenefits' => [POINTS => 1]],
    94 => ['genre' => ROMANCE, 'letter' => 'Q', 'cost' => 4, 'basicBenefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
    95 => ['genre' => ROMANCE, 'letter' => 'R', 'cost' => 5, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, SPECIAL_ROMANCE => true]],
    96 => ['genre' => ROMANCE, 'letter' => 'R', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [TRASH_DISCARD => 1]],
    97 => ['genre' => ROMANCE, 'letter' => 'S', 'cost' => 4, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1, DOUBLE_ADJ => true]],
    98 => ['genre' => ROMANCE, 'letter' => 'T', 'cost' => 3, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
    99 => ['genre' => ROMANCE, 'letter' => 'U', 'cost' => 9, 'basicBenefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 1, DOUBLE_ADJ => true]],
    100 => ['genre' => ROMANCE, 'letter' => 'V', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, TRASH_DISCARD => 1]],
    101 => ['genre' => ROMANCE, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [POINTS => 1, DOUBLE_ADJ => true], 'genreBenefits' => [POINTS => 1]],
    102 => ['genre' => ROMANCE, 'letter' => 'X', 'cost' => 7, 'basicBenefits' => [POINTS => 4, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
    103 => ['genre' => ROMANCE, 'letter' => 'Y', 'cost' => 4, 'basicBenefits' => [POINTS => 1, DOUBLE_ADJ => true], 'genreBenefits' => [TRASH_DISCARD => 1]],
    104 => ['genre' => ROMANCE, 'letter' => 'Z', 'cost' => 4, 'basicBenefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
    105 => ['genre' => ROMANCE, 'letter' => 'Z', 'cost' => 5, 'basicBenefits' => [POINTS => 2, DOUBLE_ADJ => true], 'genreBenefits' => [COINS => 2]],

    106 => ['genre' => MYSTERY, 'letter' => 'A', 'cost' => 3, 'basicBenefits' => [POINTS => 1, UNCOVER_ADJ => true]],
    107 => ['genre' => MYSTERY, 'letter' => 'A', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1]],
    108 => ['genre' => MYSTERY, 'letter' => 'B', 'cost' => 4, 'basicBenefits' => [COINS => 2, JAIL => true], 'genreBenefits' => [COINS => 2]],
    109 => ['genre' => MYSTERY, 'letter' => 'C', 'cost' => 5, 'basicBenefits' => [POINTS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [JAIL => true]],
    110 => ['genre' => MYSTERY, 'letter' => 'D', 'cost' => 4, 'basicBenefits' => [POINTS => 1, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 2]],
    111 => ['genre' => MYSTERY, 'letter' => 'E', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [UNCOVER_ADJ => true]],
    112 => ['genre' => MYSTERY, 'letter' => 'F', 'cost' => 2, 'basicBenefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 1]],
    113 => ['genre' => MYSTERY, 'letter' => 'F', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 1]],
    114 => ['genre' => MYSTERY, 'letter' => 'G', 'cost' => 6, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, UNCOVER_ADJ => true]],
    115 => ['genre' => MYSTERY, 'letter' => 'H', 'cost' => 3, 'basicBenefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 2]],
    116 => ['genre' => MYSTERY, 'letter' => 'I', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [JAIL => true]],
    117 => ['genre' => MYSTERY, 'letter' => 'I', 'cost' => 5, 'basicBenefits' => [COINS => 2, UNCOVER_ADJ => true]],
    118 => ['genre' => MYSTERY, 'letter' => 'J', 'cost' => 8, 'basicBenefits' => [POINTS => 5, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 2]],
    119 => ['genre' => MYSTERY, 'letter' => 'K', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1, UNCOVER_ADJ => true]],
    120 => ['genre' => MYSTERY, 'letter' => 'K', 'cost' => 4, 'basicBenefits' => [COINS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [COINS => 2]],
    121 => ['genre' => MYSTERY, 'letter' => 'L', 'cost' => 6, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1, UNCOVER_ADJ => true]],
    122 => ['genre' => MYSTERY, 'letter' => 'M', 'cost' => 3, 'basicBenefits' => [COINS => 1, UNCOVER_ADJ => true], 'genreBenefits' => [COINS => 1]],
    123 => ['genre' => MYSTERY, 'letter' => 'M', 'cost' => 4, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, JAIL => true]],
    124 => ['genre' => MYSTERY, 'letter' => 'N', 'cost' => 7, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1, SPECIAL_MYSTERY => 1]],
    125 => ['genre' => MYSTERY, 'letter' => 'O', 'cost' => 3, 'basicBenefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [UNCOVER_ADJ => true]],
    126 => ['genre' => MYSTERY, 'letter' => 'P', 'cost' => 2, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, JAIL => true]],
    127 => ['genre' => MYSTERY, 'letter' => 'P', 'cost' => 4, 'basicBenefits' => [POINTS => 1, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 2]],
    128 => ['genre' => MYSTERY, 'letter' => 'Q', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, UNCOVER_ADJ => true]],
    129 => ['genre' => MYSTERY, 'letter' => 'Q', 'cost' => 5, 'basicBenefits' => [POINTS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 3]],
    130 => ['genre' => MYSTERY, 'letter' => 'R', 'cost' => 4, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 2, UNCOVER_ADJ => true]],
    131 => ['genre' => MYSTERY, 'letter' => 'R', 'cost' => 6, 'basicBenefits' => [POINTS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 1, JAIL => true]],
    132 => ['genre' => MYSTERY, 'letter' => 'S', 'cost' => 4, 'basicBenefits' => [COINS => 1, JAIL => true], 'genreBenefits' => [COINS => 2]],
    133 => ['genre' => MYSTERY, 'letter' => 'T', 'cost' => 6, 'basicBenefits' => [POINTS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 2]],
    134 => ['genre' => MYSTERY, 'letter' => 'T', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, JAIL => true]],
    135 => ['genre' => MYSTERY, 'letter' => 'U', 'cost' => 2, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, UNCOVER_ADJ => true]],
    136 => ['genre' => MYSTERY, 'letter' => 'V', 'cost' => 9, 'basicBenefits' => [POINTS => 4, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 4]],
    137 => ['genre' => MYSTERY, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 2, UNCOVER_ADJ => true]],
    138 => ['genre' => MYSTERY, 'letter' => 'X', 'cost' => 3, 'basicBenefits' => [POINTS => 3, JAIL => true]],
    139 => ['genre' => MYSTERY, 'letter' => 'Y', 'cost' => 7, 'basicBenefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 2, UNCOVER_ADJ => true]],
    140 => ['genre' => MYSTERY, 'letter' => 'Z', 'cost' => 5, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, UNCOVER_ADJ => true]],

    141 => ['genre' => STARTER, 'letter' => 'A', 'cost' => 0, 'basicBenefits' => [COINS => 1]],
    142 => ['genre' => STARTER, 'letter' => 'B', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    143 => ['genre' => STARTER, 'letter' => 'C', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    144 => ['genre' => STARTER, 'letter' => 'D', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    145 => ['genre' => STARTER, 'letter' => 'E', 'cost' => 0, 'basicBenefits' => [COINS => 1]],
    146 => ['genre' => STARTER, 'letter' => 'G', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    147 => ['genre' => STARTER, 'letter' => 'H', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    148 => ['genre' => STARTER, 'letter' => 'I', 'cost' => 0, 'basicBenefits' => [COINS => 1]],
    149 => ['genre' => STARTER, 'letter' => 'L', 'cost' => 0, 'basicBenefits' => [COINS => 1]],
    150 => ['genre' => STARTER, 'letter' => 'M', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    151 => ['genre' => STARTER, 'letter' => 'N', 'cost' => 0, 'basicBenefits' => [COINS => 1]],
    152 => ['genre' => STARTER, 'letter' => 'O', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    153 => ['genre' => STARTER, 'letter' => 'P', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    154 => ['genre' => STARTER, 'letter' => 'R', 'cost' => 0, 'basicBenefits' => [COINS => 1]],
    155 => ['genre' => STARTER, 'letter' => 'S', 'cost' => 0, 'basicBenefits' => [COINS => 1]],
    156 => ['genre' => STARTER, 'letter' => 'T', 'cost' => 0, 'basicBenefits' => [COINS => 1]],
    157 => ['genre' => STARTER, 'letter' => 'U', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
    158 => ['genre' => STARTER, 'letter' => 'Y', 'cost' => 0, 'basicBenefits' => [POINTS => 1]],
];
