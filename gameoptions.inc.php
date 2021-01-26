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
                'name' => "Learner's Dictionary (65,000 words)",
                'description' => '65,000 words appearing in at least three advanced-level dictionaries for learners of English',
            ],
            NWL => [
                'name' => 'North American Scrabble (115,000 words)',
                'description' => '115,000 words allowed in North American Scrabble tournaments',
            ],
            COLLINS => [
                'name' => 'Collins Scrabble (280,000 words)',
                'description' => '280,000 words allowed in British Scrabble tournaments',
            ],
        ]
    ],

    OPTION_LENGTH => [
        'name' => 'Game Length',
        'default' => 60,
        'values' => [
            40 => [
                'name' => 'Shorter (40 Points)',
                'tmdisplay' => 'Shorter (40 Points)',
            ],
            60 => [
                'name' => 'Standard (60 Points)',
            ],
            80 => [
                'name' => 'Longer (80 Points)',
                'tmdisplay' => 'Longer (80 Points)',
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
                'description' => 'Bonus points for the longest word',
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
                'description' => 'Purchase points with coins',
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
                'description' => 'Word restrictions and rule changes provide additional challenge',
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
                'name' => 'Yes',
                'tmdisplay' => 'Powers',
                'description' => 'Unique powers can either out-wit or penalize opponents (everyone receives the same type)',
                'nobeginner' => true,
            ],
            PASSIVE => [
                'name' => 'Passive Powers',
                'tmdisplay' => 'Passive Powers',
                'description' => 'Unique powers can out-wit opponents',
                'nobeginner' => true,
            ],
            AGRESSIVE => [
                'name' => 'Agressive Powers',
                'tmdisplay' => 'Agressive Powers',
                'description' => 'Unique powers can penalize opponents',
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
                'description' => 'Compete to be the finest novelist of the age',
            ],
            COOP_EASY => [
                'name' => 'Cooperative (Easier)',
                'tmdisplay' => 'Cooperative (Easier)',
                'description' => 'Win or lose as a team against archrival Penny Dreadful',
            ],
            COOP_HARD => [
                'name' => 'Cooperative (Harder)',
                'tmdisplay' => 'Cooperative (Harder)',
                'description' => 'Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre',
                'nobeginner' => true,
            ],
        ]
    ],
];
