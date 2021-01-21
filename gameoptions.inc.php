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
 * gameoptions.inc.php
 *
 * hardback game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in hardback.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

require_once('constants.inc.php');

$game_options = [
    OPTION_DICTIONARY => [
        'name' => 'Dictionary',
        'default' => TWELVEDICTS,
        'values' => [
            TWELVEDICTS => [
                'name' => 'Common English (65,000 words)',
                'description' => 'Words appearing in at least three advanced-level dictionaries for learners of English',
            ],
            NWL => [
                'name' => 'North American Scrabble (115,000 words)',
                'tmdisplay' => 'North American Scrabble Dictionary',
                'description' => 'Words allowed in North American Scrabble tournaments',
            ],
            COLLINS => [
                'name' => 'Collins Scrabble (280,000 words)',
                'tmdisplay' => 'Collins Scrabble Dictionary',
                'description' => 'Words allowed in British Scrabble tournaments',
            ],
        ]
    ],

    OPTION_AWARDS => [
        'name' => 'Literary Awards',
        'default' => YES,
        'values' => [
            NO => [
                'name' => 'No',
            ],
            YES => [
                'name' => 'Yes',
                'tmdisplay' => 'Literary Awards',
                'description' => 'Bonus for the player who spells the longest word',
            ]
        ]
    ],

    OPTION_ADVERTS => [
        'name' => 'Adverts',
        'default' => NO,
        'values' => [
            NO => [
                'name' => 'No',
            ],
            YES => [
                'name' => 'Yes',
                'tmdisplay' => 'Adverts',
                'description' => 'Players can purchase Prestige points with coins',
            ]
        ]
    ],

    OPTION_EVENTS => [
        'name' => 'Events',
        'default' => NO,
        'values' => [
            NO => [
                'name' => 'No',
            ],
            YES => [
                'name' => 'Yes',
                'tmdisplay' => 'Events',
                'description' => 'Words restrictions and rule changes provide additional challenge',
                'nobeginner' => true,
            ]
        ]
    ],

    OPTION_POWERS => [
        'name' => 'Player Powers',
        'default' => NO,
        'values' => [
            NO => [
                'name' => 'No',
            ],
            YES => [
                'name' => 'Passive or Agressive',
                'tmdisplay' => 'Powers',
                'description' => 'Players receive a unique power to either to out-wit or penalize opponents (all players receive the same type)',
                'nobeginner' => true,
            ],
            PASSIVE => [
                'name' => 'Passive',
                'tmdisplay' => 'Passive Powers',
                'description' => 'Players receive a unique power to out-wit opponents',
                'nobeginner' => true,
            ],
            AGRESSIVE => [
                'name' => 'Agressive',
                'tmdisplay' => 'Agressive Powers',
                'description' => 'Players receive a unique power to penalize opponents',
                'nobeginner' => true,
            ],
        ]
    ],

    OPTION_COOP => [
        'name' => 'Cooperative Anthology',
        'default' => NO,
        'values' => [
            NO => [
                'name' => 'No',
                'description' => 'Players compete to be the finest novelist of the age',
            ],
            YES => [
                'name' => 'Cooperative',
                'tmdisplay' => 'Cooperative',
                'description' => 'Players win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre',
                'nobeginner' => true,
            ],
            COOP_EASY => [
                'name' => 'Cooperative without signature genre (easier)',
                'tmdisplay' => 'Cooperative (Easier)',
                'description' => 'Players win or lose as a team against archrival Penny Dreadful, who receives no advantage',
            ],
        ]
    ],
];
