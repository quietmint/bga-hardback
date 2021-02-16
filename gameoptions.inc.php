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

require_once('modules/constants.inc.php');

$game_options = [
    OPTION_DICTIONARY => [
        'name' => 'Dictionary',
        'default' => TWELVEDICTS,
        'values' => [
            TWELVEDICTS => [
                'name' => '12dicts (85,000 words)',
                'description' => 'Common words appearing in at least 2 of 12 English dictionaries',
            ],
            US => [
                'name' => 'American Scrabble (190,000 words)',
            ],
            UK => [
                'name' => 'British Scrabble (280,000 words)',
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
        ],
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => OPTION_COOP,
                'value' => NO,
            ],
        ],
        'notdisplayedmessage' => 'Standard (60 Points)',
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

    /*
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
                'description' => 'Word restrictions (challenging)',
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
                'description' => 'Unique powers to either out-wit or penalize opponents (everyone receives the same type)',
                'nobeginner' => true,
            ],
            PASSIVE => [
                'name' => 'Passive Powers',
                'tmdisplay' => 'Passive Powers',
                'description' => 'Unique powers to out-wit opponents',
                'nobeginner' => true,
            ],
            AGRESSIVE => [
                'name' => 'Agressive Powers',
                'tmdisplay' => 'Agressive Powers',
                'description' => 'Unique powers to penalize opponents',
                'nobeginner' => true,
            ],
        ],
        'startcondition' => [
            AGRESSIVE => [
                [
                    'type' => 'otheroption',
                    'id' => OPTION_COOP,
                    'value' => NO,
                    'message' => 'Cannot use Agressive Powers with Cooperative Anthology',
                ],
            ],
        ],
    ],
    */

    OPTION_COOP => [
        'name' => 'Cooperative Anthology',
        'default' => NO,
        'values' => [
            NO => [
                'name' => 'No',
                'description' => 'Compete to be the finest novelist of the age',
            ],
            COOP_BASIC => [
                'name' => 'Cooperative',
                'tmdisplay' => 'Cooperative',
                'description' => 'Win or lose as a team against archrival Penny Dreadful (challenging)',
                'nobeginner' => true,
                'is_coop' => true,
            ],
            COOP_SIGNATURE => [
                'name' => 'Cooperative With Signature Genre',
                'tmdisplay' => 'Cooperative With Signature Genre',
                'description' => 'Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre (more challenging)',
                'nobeginner' => true,
                'is_coop' => true,
            ],
        ],
        'startcondition' => [
            NO => [
                [
                    'type' => 'minplayers',
                    'value' => 2,
                    'message' => 'Solo mode can only be played with Cooperative Anthology',
                ],
            ],
            COOP_BASIC => [
                [
                    'type' => 'otheroption',
                    'id' => GAMESTATE_RATING_MODE,
                    'value' => ELO_OFF,
                    'message' => 'Cooperative Anthology can only be played in Training mode (no ELO)',
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => 'Cooperative Anthology supports 1 - 4 players',
                ],
            ],
            COOP_SIGNATURE => [
                [
                    'type' => 'otheroption',
                    'id' => GAMESTATE_RATING_MODE,
                    'value' => ELO_OFF,
                    'message' => 'Cooperative Anthology can only be played in Training mode (no ELO)',
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => 'Cooperative Anthology supports 1 - 4 players',
                ],
            ],
        ],
    ],
];
