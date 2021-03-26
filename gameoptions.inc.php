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
        'name' => totranslate('Dictionary'),
        'default' => TWELVEDICTS,
        'values' => [
            TWELVEDICTS => [
                'name' => totranslate('12dicts'),
                'description' => totranslate('85,000 words (2016 edition)'),
            ],
            US => [
                'name' => totranslate('American Scrabble'),
                'description' => totranslate('190,000 words (2020 edition)'),
            ],
            UK => [
                'name' => totranslate('British Scrabble'),
                'description' => totranslate('280,000 words (2019 edition)'),
            ],
        ]
    ],

    OPTION_LENGTH => [
        'name' => totranslate('Game Length'),
        'default' => 60,
        'values' => [
            40 => [
                'name' => totranslate('Shorter (40 Points)'),
                'tmdisplay' => totranslate('Shorter (40 Points)'),
            ],
            60 => [
                'name' => totranslate('Standard (60 Points)'),
            ],
            80 => [
                'name' => totranslate('Longer (80 Points)'),
                'tmdisplay' => totranslate('Longer (80 Points)'),
            ],
        ],
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => OPTION_COOP,
                'value' => NO,
            ],
        ],
        'notdisplayedmessage' => totranslate('Standard (60 Points)'),
    ],

    OPTION_AWARDS => [
        'name' => totranslate('Literary Awards'),
        'default' => YES,
        'values' => [
            NO => [
                'name' => totranslate('No'),
            ],
            YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Literary Awards'),
                'description' => totranslate('Bonus points for the longest word'),
            ]
        ]
    ],

    OPTION_ADVERTS => [
        'name' => totranslate('Adverts'),
        'default' => YES,
        'values' => [
            NO => [
                'name' => totranslate('No'),
            ],
            YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Adverts'),
                'description' => totranslate('Purchase points with coins'),
            ]
        ]
    ],

    /*
    OPTION_EVENTS => [
        'name' => totranslate('Events'),
        'default' => NO,
        'values' => [
            NO => [
                'name' => totranslate('No'),
            ],
            YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Events'),
                'description' => totranslate('Word restrictions (challenging)'),
                'nobeginner' => true,
            ]
        ]
    ],

    OPTION_POWERS => [
        'name' => totranslate('Player Powers'),
        'default' => NO,
        'values' => [
            NO => [
                'name' => totranslate('No'),
            ],
            YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Powers'),
                'description' => totranslate('Unique powers to either out-wit or penalize opponents (everyone receives the same type)',
                'nobeginner' => true,
            ],
            PASSIVE => [
                'name' => totranslate('Passive Powers'),
                'tmdisplay' => totranslate('Passive Powers'),
                'description' => totranslate('Unique powers to out-wit opponents'),
                'nobeginner' => true,
            ],
            AGRESSIVE => [
                'name' => totranslate('Agressive Powers'),
                'tmdisplay' => totranslate('Agressive Powers'),
                'description' => totranslate('Unique powers to penalize opponents'),
                'nobeginner' => true,
            ],
        ],
        'startcondition' => [
            AGRESSIVE => [
                [
                    'type' => 'otheroption',
                    'id' => OPTION_COOP,
                    'value' => NO,
                    'message' => totranslate('Cannot use Agressive Powers with Cooperative Anthology'),
                ],
            ],
        ],
    ],
    */

    OPTION_COOP => [
        'name' => totranslate('Cooperative Anthology'),
        'default' => NO,
        'values' => [
            NO => [
                'name' => totranslate('No'),
                'description' => totranslate('Compete to be the finest novelist of the age'),
            ],
            COOP_BASIC => [
                'name' => totranslate('Cooperative'),
                'tmdisplay' => totranslate('Cooperative'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful (challenging)'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
            COOP_SIGNATURE => [
                'name' => totranslate('Cooperative With Signature Genre'),
                'tmdisplay' => totranslate('Cooperative With Signature Genre'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre (more challenging)'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
        ],
        'startcondition' => [
            NO => [
                [
                    'type' => 'minplayers',
                    'value' => 2,
                    'message' => totranslate('Must enable Cooperative Anthology to play solo'),
                ],
            ],
            COOP_BASIC => [
                [
                    'type' => 'otheroption',
                    'id' => GAMESTATE_RATING_MODE,
                    'value' => ELO_OFF,
                    'message' => totranslate('Must set game mode = training mode (no ELO) to enable Cooperative Anthology'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Must set max players = 1 - 4 to enable Cooperative Anthology'),
                ],
            ],
            COOP_SIGNATURE => [
                [
                    'type' => 'otheroption',
                    'id' => GAMESTATE_RATING_MODE,
                    'value' => ELO_OFF,
                    'message' => totranslate('Must set game mode = training mode (no ELO) to enable Cooperative Anthology'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Must set max players = 1 - 4 to enable Cooperative Anthology'),
                ],
            ],
        ],
    ],
];

$game_preferences = [
    PREF_DRAG_DROP => [
        'name' => totranslate('Drag and drop'),
        'needReload' => false,
        'values' => [
            0 => ['name' => totranslate('Enabled')],
            1 => ['name' => totranslate('Disabled')],
        ],
    ],

    PREF_ZOOM => [
        'name' => totranslate('Mobile zoom level'),
        'needReload' => false,
        'default' => 750,
        'values' => [
            580 => ['name' => totranslate('Large (3 cards per row)')],
            750 => ['name' => totranslate('Medium (4 cards per row)')],
            920 => ['name' => totranslate('Small (5 cards per row)')],
        ],
    ],
];
