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
 * stats.inc.php
 *
 * hardback game statistics description
 *
 */

/*
    In this file, you are describing game statistics, that will be displayed at the end of the
    game.
    
    !! After modifying this file, you must use 'Reload  statistics configuration' in BGA Studio backoffice
    ('Control Panel' / 'Manage Game' / 'Your Game')
    
    There are 2 types of statistics:
    _ table statistics, that are not associated to a specific player (ie: 1 value for each game).
    _ player statistics, that are associated to each players (ie: 1 value for each player in the game).

    Statistics types can be 'int' for integer, 'float' for floating point values, and 'bool' for boolean
    
    Once you defined your statistics there, you can start using 'initStat', 'setStat' and 'incStat' method
    in your game logic, using statistics names defined below.
    
    !! It is not a good idea to modify this file when a game is running !!

    If your game is already public on BGA, please read the following before any change:
    http://en.doc.boardgamearena.com/Post-release_phase#Changes_that_breaks_the_games_in_progress
    
    Notes:
    * Statistic index is the reference used in setStat/incStat/initStat PHP method
    * Statistic index must contains alphanumerical characters and no space. Example: 'turn_played'
    * Statistics IDs must be >=10
    * Two table statistics can't share the same ID, two player statistics can't share the same ID
    * A table statistic can have the same ID than a player statistics
    * Statistics ID is the reference used by BGA website. If you change the ID, you lost all historical statistic data. Do NOT re-use an ID of a deleted statistic
    * Statistic name is the English description of the statistic as shown to players
    
*/

require_once('constants.inc.php');

$stats_type = [
    'table' => [
        'turns' => [
            'id' => STAT_TURNS,
            'name' => 'Turns',
            'type' => 'int',
        ],
        'longestWord' => [
            'id' => STAT_LONGEST_WORD,
            'name' => 'Longest word',
            'type' => 'int',
        ],
    ],

    'player' => [
        'words' => [
            'id' => STAT_WORDS,
            'name' => 'Words',
            'type' => 'int',
        ],
        'longestWord' => [
            'id' => STAT_LONGEST_WORD,
            'name' => 'Longest word',
            'type' => 'int',
        ],
        'invalidWords' => [
            'id' => STAT_INVALID_WORDS,
            'name' => 'Misspelled words',
            'type' => 'int',
        ],
        'useInk' => [
            'id' => STAT_USE_INK,
            'name' => 'Ink used',
            'type' => 'int',
        ],
        'useRemover' => [
            'id' => STAT_USE_REMOVER,
            'name' => 'Remover used',
            'type' => 'int',
        ],
        'coins' => [
            'id' => STAT_COINS,
            'name' => 'Coins earned',
            'type' => 'int',
        ],
        'pointsBasic' => [
            'id' => STAT_POINTS_BASIC,
            'name' => 'Points from basic benefits',
            'type' => 'int',
        ],
        'pointsGenre' => [
            'id' => STAT_POINTS_GENRE,
            'name' => 'Points from genre benefits',
            'type' => 'int',
        ],
        'pointsPurchase' => [
            'id' => STAT_POINTS_PURCHASE,
            'name' => 'Points from card purchases',
            'type' => 'int',
        ],
        'pointsAward' => [
            'id' => STAT_POINTS_AWARD,
            'name' => 'Points from literary awards',
            'type' => 'int',
        ],
        'pointsAdvert' => [
            'id' => STAT_POINTS_ADVERT,
            'name' => 'Points from adverts',
            'type' => 'int',
        ],
    ],
];
