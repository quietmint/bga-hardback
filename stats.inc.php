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

require_once('modules/constants.inc.php');

$genres = [
    H_ADVENTURE => clienttranslate('Adventure'),
    H_HORROR => clienttranslate('Horror'),
    H_MYSTERY => clienttranslate('Mystery'),
    H_ROMANCE => clienttranslate('Romance'),
];

$letters = [];
foreach (range('A', 'Z') as $letter) {
    $letters[ord($letter)] = $letter;
}

$stats_type = [
    'table' => [
        'turns' => [
            'id' => H_STAT_TURNS,
            'name' => totranslate('Rounds'),
            'type' => 'int',
        ],
        'words' => [
            'id' => H_STAT_WORDS,
            'name' => totranslate('Words'),
            'type' => 'int',
        ],
        'longestWord' => [
            'id' => H_STAT_LONGEST_WORD,
            'name' => totranslate('Longest word'),
            'type' => 'int',
        ],
        'bestWord' => [
            'id' => H_STAT_BEST_WORD,
            'name' => totranslate('Highest-scoring word'),
            'type' => 'int',
        ],
        'invalidWords' => [
            'id' => H_STAT_INVALID_WORDS,
            'name' => totranslate('Invalid words'),
            'type' => 'int',
        ],
        'flush' => [
            'id' => H_STAT_FLUSH,
            'name' => totranslate('Flush Offer Row'),
            'type' => 'int',
        ],
        'cycled' => [
            'id' => H_STAT_CYCLED,
            'name' => totranslate('Cycle Offer Row'),
            'type' => 'int',
        ],
        'coopScore' => [
            'id' => H_STAT_COOP_SCORE,
            'name' => totranslate('Penny Dreadful: Score'),
            'type' => 'int',
        ],
        'coopTurns' => [
            'id' => H_STAT_COOP_TURNS,
            'name' => totranslate('Penny Dreadful: Turns'),
            'type' => 'int',
        ],
        'coopAvg' => [
            'id' => H_STAT_COOP_AVG,
            'name' => totranslate('Penny Dreadful: Points per turn'),
            'type' => 'float',
        ],
        'coopGenre' => [
            'id' => H_STAT_COOP_GENRE,
            'name' => totranslate('Penny Dreadful: Signature Genre'),
            'type' => 'int',
        ],
    ],

    'player' => [
        'pointsBasic' => [
            'id' => H_STAT_POINTS_BASIC,
            'name' => totranslate('Points from basic benefits'),
            'type' => 'int',
        ],
        'pointsGenre' => [
            'id' => H_STAT_POINTS_GENRE,
            'name' => totranslate('Points from genre benefits'),
            'type' => 'int',
        ],
        'pointsPurchase' => [
            'id' => H_STAT_POINTS_PURCHASE,
            'name' => totranslate('Points from card purchases'),
            'type' => 'int',
        ],
        'pointsAward' => [
            'id' => H_STAT_POINTS_AWARD,
            'name' => totranslate('Points from literary awards'),
            'type' => 'int',
        ],
        'pointsAdvert' => [
            'id' => H_STAT_POINTS_ADVERT,
            'name' => totranslate('Points from adverts'),
            'type' => 'int',
        ],
        'coins' => [
            'id' => H_STAT_COINS,
            'name' => totranslate('Coins earned'),
            'type' => 'int',
        ],
        'cardsPurchase' => [
            'id' => H_STAT_CARDS_PURCHASE,
            'name' => totranslate('Cards purchased'),
            'type' => 'int',
        ],
        'cardsTrash' => [
            'id' => H_STAT_CARDS_TRASH,
            'name' => totranslate('Cards trashed'),
            'type' => 'int',
        ],
        'words' => [
            'id' => H_STAT_WORDS,
            'name' => totranslate('Words'),
            'type' => 'int',
        ],
        'longestWord' => [
            'id' => H_STAT_LONGEST_WORD,
            'name' => totranslate('Longest word'),
            'type' => 'int',
        ],
        'bestWord' => [
            'id' => H_STAT_BEST_WORD,
            'name' => totranslate('Highest-scoring word'),
            'type' => 'int',
        ],
        'invalidWords' => [
            'id' => H_STAT_INVALID_WORDS,
            'name' => totranslate('Invalid words'),
            'type' => 'int',
        ],
        'votesAccept' => [
            'id' => H_STAT_VOTES_ACCEPT,
            'name' => totranslate('Votes to accept'),
            'type' => 'int',
        ],
        'votesReject' => [
            'id' => H_STAT_VOTES_REJECT,
            'name' => totranslate('Votes to reject'),
            'type' => 'int',
        ],
        'useInk' => [
            'id' => H_STAT_USE_INK,
            'name' => totranslate('Ink used'),
            'type' => 'int',
        ],
        'useRemover' => [
            'id' => H_STAT_USE_REMOVER,
            'name' => totranslate('Remover used'),
            'type' => 'int',
        ],
        'starterCard1' => [
            'id' => H_STAT_STARTER_CARD1,
            'name' => totranslate('Starter card'),
            'type' => 'int',
        ],
        'starterCard2' => [
            'id' => H_STAT_STARTER_CARD2,
            'name' => totranslate('Starter card'),
            'type' => 'int',
        ],
        'deck' . H_STARTER => [
            'id' => H_STAT_STARTER,
            'name' => totranslate('Starter cards in deck'),
            'type' => 'int',
        ],
        'deck' . H_ADVENTURE => [
            'id' => H_STAT_ADVENTURE,
            'name' => totranslate('Adventure cards in deck'),
            'type' => 'int',
        ],
        'deck' . H_HORROR => [
            'id' => H_STAT_HORROR,
            'name' => totranslate('Horror cards in deck'),
            'type' => 'int',
        ],
        'deck' . H_MYSTERY => [
            'id' => H_STAT_MYSTERY,
            'name' => totranslate('Mystery cards in deck'),
            'type' => 'int',
        ],
        'deck' . H_ROMANCE => [
            'id' => H_STAT_ROMANCE,
            'name' => totranslate('Romance cards in deck'),
            'type' => 'int',
        ],
    ],

    'value_labels' => [
        H_STAT_STARTER_CARD1 => $letters,
        H_STAT_STARTER_CARD2 => $letters,
        H_STAT_COOP_GENRE => $genres,
    ],
];
