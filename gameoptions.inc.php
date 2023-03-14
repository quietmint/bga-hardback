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

$dictionaryStartCond = [
    H_VOTE_50 => [
        [
            'type' => 'minplayers',
            'value' => 2,
            'message' => totranslate('Cannot use a voting dictionary to play solo'),
        ],
    ],
    H_VOTE_100 => [
        [
            'type' => 'minplayers',
            'value' => 2,
            'message' => totranslate('Cannot use a voting dictionary to play solo'),
        ],
    ],
];

$game_options = [
    H_OPTION_RULESET => [
        'name' => totranslate('Game Rules'),
        'default' => 2,
        'values' => [
            1 => [
                'name' => totranslate('First edition'),
                'tmdisplay' => totranslate('First edition'),
                'description' => totranslate('Offer row doesn\'t refresh automatically. Only one player can win the top literary award.'),
            ],
            2 => [
                'name' => totranslate('Second edition (NEW!)'),
                'tmdisplay' => totranslate('Second edition (NEW!)'),
                'description' => totranslate('Discard the oldest offer row card when you do not purchase any cards. All players can win the top literary award.'),
            ],
        ]
    ],

    H_OPTION_DICTIONARY => [
        'name' => totranslate('Dictionary'),
        'default' => H_US,
        'values' => [
            H_TWELVEDICTS => [
                'name' => totranslate('12dicts'),
                'tmdisplay' => totranslate('12dicts'),
                'description' => totranslate('85,000 words (2016 edition)'),
            ],
            H_US => [
                'name' => totranslate('American Scrabble'),
                'tmdisplay' => totranslate('American Scrabble'),
                'description' => totranslate('190,000 words (2020 edition)'),
            ],
            H_UK => [
                'name' => totranslate('British Scrabble'),
                'tmdisplay' => totranslate('British Scrabble'),
                'description' => totranslate('280,000 words (2019 edition)'),
            ],
            H_LETTERPRESS => [
                'name' => totranslate('Letterpress'),
                'tmdisplay' => totranslate('Letterpress'),
                'description' => totranslate('270,000 words (2015 edition)'),
            ],
            H_VOTE_50 => [
                'name' => totranslate('Majority Vote'),
                'tmdisplay' => totranslate('Majority Vote'),
                'description' => totranslate('Instead of a dictionary, words must be accepted by half of the other players. Recommended with friends only.'),
                'nobeginner' => true,
            ],
            H_VOTE_100 => [
                'name' => totranslate('Unanimous Vote'),
                'tmdisplay' => totranslate('Unanimous Vote'),
                'description' => totranslate('Instead of a dictionary, words must be accepted by all other players. Recommended with friends only.'),
                'nobeginner' => true,
            ],
        ],
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => H_OPTION_LANG,
                'value' => H_LANG_EN,
            ],
        ],
        'startcondition' => $dictionaryStartCond,
        'notdisplayedmessage' => totranslate('IMPORTANT: Currently, the language option changes only the dictionary and does NOT affect the cards (letter frequency, cost, benefits)'),
    ],

    H_OPTION_DICTIONARY_DE => [
        'name' => totranslate('Dictionary'),
        'default' => H_FREE_DE,
        'values' => [
            H_BEOLINGUS => [
                'name' => totranslate('BEOLINGUS (TU Chemnitz)'),
                'tmdisplay' => totranslate('BEOLINGUS (TU Chemnitz)'),
                'description' => totranslate('185,000 words (2020 edition)'),
                'beta' => true,
            ],
            H_FREE_DE => [
                'name' => totranslate('Free German Dictionary'),
                'tmdisplay' => totranslate('Free German Dictionary'),
                'description' => totranslate('1,500,000 words (2021 edition)'),
                'beta' => true,
            ],
            H_VOTE_50 => [
                'name' => totranslate('Majority Vote'),
                'tmdisplay' => totranslate('Majority Vote'),
                'description' => totranslate('Instead of a dictionary, words must be accepted by half of the other players. Recommended with friends only.'),
                'beta' => true,
                'nobeginner' => true,
            ],
            H_VOTE_100 => [
                'name' => totranslate('Unanimous Vote'),
                'tmdisplay' => totranslate('Unanimous Vote'),
                'description' => totranslate('Instead of a dictionary, words must be accepted by all other players. Recommended with friends only.'),
                'beta' => true,
                'nobeginner' => true,
            ],
        ],
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => H_OPTION_LANG,
                'value' => H_LANG_DE,
            ],
        ],
        'startcondition' => $dictionaryStartCond,
    ],

    H_OPTION_DICTIONARY_FR => [
        'name' => totranslate('Dictionary'),
        'default' => H_MORPHALOU,
        'values' => [
            H_MORPHALOU => [
                'name' => totranslate('Morphalou'),
                'tmdisplay' => totranslate('Morphalou'),
                'description' => totranslate('680,000 words (2019 edition)'),
                'beta' => true,
            ],
            H_VOTE_50 => [
                'name' => totranslate('Majority Vote'),
                'tmdisplay' => totranslate('Majority Vote'),
                'description' => totranslate('Instead of a dictionary, words must be accepted by half of the other players. Recommended with friends only.'),
                'beta' => true,
                'nobeginner' => true,
            ],
            H_VOTE_100 => [
                'name' => totranslate('Unanimous Vote'),
                'tmdisplay' => totranslate('Unanimous Vote'),
                'description' => totranslate('Instead of a dictionary, words must be accepted by all other players. Recommended with friends only.'),
                'beta' => true,
                'nobeginner' => true,
            ],
        ],
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => H_OPTION_LANG,
                'value' => H_LANG_FR,
            ],
        ],
        'startcondition' => $dictionaryStartCond,
    ],

    H_OPTION_COOP => [
        'name' => totranslate('Game Type'),
        'default' => H_NO,
        'values' => [
            H_NO => [
                'name' => totranslate('Competetive'),
                'description' => totranslate('Compete to be the finest novelist of the age'),
            ],
            H_COOP_BASIC => [
                'name' => totranslate('Cooperative (No Signature Genre)'),
                'tmdisplay' => totranslate('Cooperative (No Signature Genre)'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful (challenging)'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
            H_COOP_RANDOM => [
                'name' => totranslate('Cooperative (Random)'),
                'tmdisplay' => totranslate('Cooperative (Random)'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre (more challenging). Penny\'s genre is randomly selected.'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
            H_COOP_ADVENTURE => [
                'name' => totranslate('Cooperative (Adventure)'),
                'tmdisplay' => totranslate('Cooperative (Adventure)'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre (more challenging). If there is an Adventure card in the offer row, Penny gains 1 point for each card purchased by any player.'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
            H_COOP_HORROR => [
                'name' => totranslate('Cooperative (Horror)'),
                'tmdisplay' => totranslate('Cooperative (Horror)'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre (more challenging). If there is a Horror card in the offer row, Penny gains 1 point for each ink used by any player.'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
            H_COOP_MYSTERY => [
                'name' => totranslate('Cooperative (Mystery)'),
                'tmdisplay' => totranslate('Cooperative (Mystery)'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre (more challenging). Each time Penny claims a Mystery card, she also jails the cheapest card in the offer row.'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
            H_COOP_ROMANCE => [
                'name' => totranslate('Cooperative (Romance)'),
                'tmdisplay' => totranslate('Cooperative (Romance)'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre (more challenging). Double the value of each Romance card Penny claims from the offer row.'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
        ],
        'startcondition' => [
            H_NO => [
                [
                    'type' => 'minplayers',
                    'value' => 2,
                    'message' => totranslate('Cooprative game is required for solo play'),
                ],
            ],
            H_COOP_BASIC => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_GAME_MODE,
                    'value' => H_TRAINING_MODE,
                    'message' => totranslate('Cooperative game must be played in training mode (no ELO)'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Cooprative game supports 1 - 4 players'),
                ],
            ],
            H_COOP_RANDOM => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_GAME_MODE,
                    'value' => H_TRAINING_MODE,
                    'message' => totranslate('Cooperative game must be played in training mode (no ELO)'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Cooprative game supports 1 - 4 players'),
                ],
            ],
            H_COOP_ADVENTURE => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_GAME_MODE,
                    'value' => H_TRAINING_MODE,
                    'message' => totranslate('Cooperative game must be played in training mode (no ELO)'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Cooprative game supports 1 - 4 players'),
                ],
            ],
            H_COOP_HORROR => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_GAME_MODE,
                    'value' => H_TRAINING_MODE,
                    'message' => totranslate('Cooperative game must be played in training mode (no ELO)'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Cooprative game supports 1 - 4 players'),
                ],
            ],
            H_COOP_MYSTERY => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_GAME_MODE,
                    'value' => H_TRAINING_MODE,
                    'message' => totranslate('Cooperative game must be played in training mode (no ELO)'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Cooprative game supports 1 - 4 players'),
                ],
            ],
            H_COOP_ROMANCE => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_GAME_MODE,
                    'value' => H_TRAINING_MODE,
                    'message' => totranslate('Cooperative game must be played in training mode (no ELO)'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Cooprative game supports 1 - 4 players'),
                ],
            ],
        ],
    ],

    H_OPTION_LENGTH => [
        'name' => totranslate('Game Length'),
        'default' => 60,
        'values' => [
            60 => [
                'name' => totranslate('Standard (60 Points)'),
            ],
            90 => [
                'name' => totranslate('Longer (90 Points)'),
                'tmdisplay' => totranslate('Longer (90 Points)'),
            ],
            180 => [
                'name' => totranslate('Marathon (180 Points)'),
                'tmdisplay' => totranslate('Marathon (180 Points)'),
            ],
        ],
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => H_OPTION_COOP,
                'value' => H_NO,
            ],
        ],
        'notdisplayedmessage' => totranslate('Standard (60 Points)'),
    ],

    H_OPTION_LOOKUP => [
        'name' => totranslate('Word Lookups'),
        'default' => H_YES,
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
                'tmdisplay' => totranslate('Word Lookups Disabled'),
            ],
            H_YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Word Lookups Enabled'),
                'description' => totranslate('Query the dictionary anytime (helpful in turn-based games)'),
            ]
        ],
    ],

    H_OPTION_DECK => [
        'name' => totranslate('Draw Pile Visible'),
        'default' => H_YES,
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
                'tmdisplay' => totranslate('Draw Pile Invisible'),
            ],
            H_YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Draw Pile Visible'),
                'description' => totranslate('View cards in your draw pile anytime (helpful in turn-based games)'),
            ]
        ],
    ],

    H_OPTION_AWARDS => [
        'name' => totranslate('Literary Awards'),
        'default' => H_YES,
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
            ],
            H_YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Literary Awards'),
                'description' => totranslate('Bonus points for the longest word'),
            ]
        ],
    ],

    H_OPTION_ADVERTS => [
        'name' => totranslate('Adverts'),
        'default' => H_YES,
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
            ],
            H_YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Adverts'),
                'description' => totranslate('Purchase points with coins'),
            ]
        ],
    ],
];

$game_preferences = [
    H_PREF_DRAG => [
        'name' => totranslate('Drag and drop'),
        'needReload' => false,
        'values' => [
            0 => ['name' => totranslate('Enabled')],
            1 => ['name' => totranslate('Disabled')],
        ],
    ],

    H_PREF_ANIMATION => [
        'name' => totranslate('Animation'),
        'needReload' => false,
        'values' => [
            0 => ['name' => totranslate('Enabled')],
            1 => ['name' => totranslate('Disabled')],
        ],
    ],

    H_PREF_CARD_SIZE => [
        'name' => totranslate('Card size'),
        'needReload' => false,
        'default' => 4,
        'values' => [
            1 => ['name' => ''],
            2 => ['name' => ''],
            3 => ['name' => ''],
            4 => ['name' => ''],
            5 => ['name' => ''],
            6 => ['name' => ''],
            7 => ['name' => ''],
        ],
    ],
];
