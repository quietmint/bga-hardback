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
        [
            'type' => 'otheroptionisnot',
            'id' => H_OPTION_GAME_MODE,
            'value' => H_ARENA_MODE,
            'message' => totranslate('Cannot use a voting dictionary in Arena Mode'),
        ],
    ],
    H_VOTE_100 => [
        [
            'type' => 'minplayers',
            'value' => 2,
            'message' => totranslate('Cannot use a voting dictionary to play solo'),
        ],
        [
            'type' => 'otheroptionisnot',
            'id' => H_OPTION_GAME_MODE,
            'value' => H_ARENA_MODE,
            'message' => totranslate('Cannot use a voting dictionary in Arena Mode'),
        ],
    ],
];

$game_options = [
    H_OPTION_DICTIONARY => [
        'name' => totranslate('Dictionary'),
        'default' => H_TWELVEDICTS,
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
        'default' => H_YANDEX,
        'values' => [
            H_YANDEX => [
                'name' => totranslate('Yandex.Dictionary'),
                'tmdisplay' => totranslate('Yandex.Dictionary'),
                'description' => totranslate('Powered by machine learning from Yandex, a Russian technology company'),
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
            H_YANDEX => [
                'name' => totranslate('Yandex.Dictionary'),
                'tmdisplay' => totranslate('Yandex.Dictionary'),
                'description' => totranslate('Powered by machine learning from Yandex, a Russian technology company'),
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

    H_OPTION_DICTIONARY_ES => [
        'name' => totranslate('Dictionary'),
        'default' => H_YANDEX,
        'values' => [
            H_YANDEX => [
                'name' => totranslate('Yandex.Dictionary'),
                'tmdisplay' => totranslate('Yandex.Dictionary'),
                'description' => totranslate('Powered by machine learning from Yandex, a Russian technology company'),
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
                'value' => H_LANG_ES,
            ],
        ],
        'startcondition' => $dictionaryStartCond,
    ],

    H_OPTION_DICTIONARY_IT => [
        'name' => totranslate('Dictionary'),
        'default' => H_YANDEX,
        'values' => [
            H_YANDEX => [
                'name' => totranslate('Yandex.Dictionary'),
                'tmdisplay' => totranslate('Yandex.Dictionary'),
                'description' => totranslate('Powered by machine learning from Yandex, a Russian technology company'),
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
                'value' => H_LANG_IT,
            ],
        ],
        'startcondition' => $dictionaryStartCond,
    ],

    H_OPTION_LENGTH => [
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
                'id' => H_OPTION_COOP,
                'value' => H_NO,
            ],
        ],
        'notdisplayedmessage' => totranslate('Standard (60 Points)'),
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

    /*
    H_OPTION_POWERS => [
        'name' => totranslate('Player Powers'),
        'default' => H_NO,
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
            ],
            H_YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Powers'),
                'description' => totranslate('Unique powers to either out-wit or penalize opponents (everyone receives the same type)',
                'nobeginner' => true,
            ],
            H_PASSIVE => [
                'name' => totranslate('Passive Powers'),
                'tmdisplay' => totranslate('Passive Powers'),
                'description' => totranslate('Unique powers to out-wit opponents'),
                'nobeginner' => true,
            ],
            H_AGRESSIVE => [
                'name' => totranslate('Agressive Powers'),
                'tmdisplay' => totranslate('Agressive Powers'),
                'description' => totranslate('Unique powers to penalize opponents'),
                'nobeginner' => true,
            ],
        ],
        'startcondition' => [
            H_AGRESSIVE => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_COOP,
                    'value' => H_NO,
                    'message' => totranslate('Cannot use Agressive Powers with Cooperative Anthology'),
                ],
            ],
        ],
    ],
    */

    H_OPTION_COOP => [
        'name' => totranslate('Cooperative Anthology'),
        'default' => H_NO,
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
                'description' => totranslate('Compete to be the finest novelist of the age'),
            ],
            H_COOP_BASIC => [
                'name' => totranslate('Cooperative'),
                'tmdisplay' => totranslate('Cooperative'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful (challenging)'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
            H_COOP_SIGNATURE => [
                'name' => totranslate('Cooperative With Signature Genre'),
                'tmdisplay' => totranslate('Cooperative With Signature Genre'),
                'description' => totranslate('Win or lose as a team against archrival Penny Dreadful, who receives an advantage in her signature genre (more challenging)'),
                'nobeginner' => true,
                'is_coop' => true,
            ],
        ],
        'startcondition' => [
            H_NO => [
                [
                    'type' => 'minplayers',
                    'value' => 2,
                    'message' => totranslate('Must enable Cooperative Anthology to play solo'),
                ],
            ],
            H_COOP_BASIC => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_GAME_MODE,
                    'value' => H_TRAINING_MODE,
                    'message' => totranslate('Must set game mode = training mode (no ELO) to enable Cooperative Anthology'),
                ],
                [
                    'type' => 'maxplayers',
                    'value' => 4,
                    'message' => totranslate('Must set max players = 1 - 4 to enable Cooperative Anthology'),
                ],
            ],
            H_COOP_SIGNATURE => [
                [
                    'type' => 'otheroption',
                    'id' => H_OPTION_GAME_MODE,
                    'value' => H_TRAINING_MODE,
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
    H_PREF_DRAG_DROP => [
        'name' => totranslate('Drag and drop'),
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
        ],
    ],
];
