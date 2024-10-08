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

$dictionaryStartCondition = [
    H_NO => [
        [
            'type' => 'minplayers',
            'value' => 2,
            'message' => totranslate('Dictionary = No Dictionary is not available for solo play'),
            'gamestartonly' => true,
        ]
    ]
];

$voteStartCondition = [
    [
        'type' => 'minplayers',
        'value' => 2,
        'message' => totranslate('Vote on Invalid Words is not available for solo play'),
        'gamestartonly' => true,
    ],
    [
        'type' => 'otheroption',
        'id' => H_OPTION_GAME_MODE,
        'value' => H_FRIENDLY_MODE,
        'message' => totranslate('Vote on Invalid Words must be played in friendly mode (no ELO)'),
    ]
];

$coopStartCondition = [
    [
        'type' => 'otheroption',
        'id' => H_OPTION_GAME_MODE,
        'value' => H_FRIENDLY_MODE,
        'message' => totranslate('Cooperative game must be played in friendly mode (no ELO)'),
    ],
    [
        'type' => 'maxplayers',
        'value' => 4,
        'message' => totranslate('Cooperative game supports 1 - 4 players'),
    ]
];

$game_options = [
    H_OPTION_DICTIONARY => [
        'name' => totranslate('Dictionary'),
        'level' => 'base',
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
            H_NO => [
                'name' => totranslate('No Dictionary'),
                'tmdisplay' => totranslate('No Dictionary'),
                'description' => totranslate('Must use with Vote on Invalid Words and play in friendly mode (no ELO). Recommended with friends only.'),
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
        'startcondition' => $dictionaryStartCondition,
        'notdisplayedmessage' => totranslate('IMPORTANT: Currently, the language option changes only the dictionary and does NOT affect the cards (letter frequency, cost, benefits)'),
    ],

    H_OPTION_DICTIONARY_DE => [
        'name' => totranslate('Dictionary'),
        'level' => 'base',
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
            H_NO => [
                'name' => totranslate('No Dictionary'),
                'tmdisplay' => totranslate('No Dictionary'),
                'description' => totranslate('Must use with Vote on Invalid Words and play in friendly mode (no ELO). Recommended with friends only.'),
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
        'startcondition' => $dictionaryStartCondition,
    ],

    H_OPTION_DICTIONARY_FR => [
        'name' => totranslate('Dictionary'),
        'level' => 'base',
        'default' => H_MORPHALOU,
        'values' => [
            H_MORPHALOU => [
                'name' => totranslate('Morphalou'),
                'tmdisplay' => totranslate('Morphalou'),
                'description' => totranslate('680,000 words (2019 edition)'),
                'beta' => true,
            ],
            H_NO => [
                'name' => totranslate('No Dictionary'),
                'tmdisplay' => totranslate('No Dictionary'),
                'description' => totranslate('Must use with Vote on Invalid Words and play in friendly mode (no ELO). Recommended with friends only.'),
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
        'startcondition' => $dictionaryStartCondition,
    ],

    H_OPTION_VOTE => [
        'name' => totranslate('Vote on Invalid Words'),
        'level' => 'additional',
        'default' => H_NO,
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
                'description' => totranslate('Reject non-dictionary words'),
            ],
            H_VOTE_50 => [
                'name' => totranslate('Majority Vote'),
                'tmdisplay' => totranslate('Majority Vote'),
                'description' => totranslate('Accept non-dictionary words if half the players agree. Must play in friendly mode (no ELO). Recommended with friends only.'),
                'nobeginner' => true,
            ],
            H_VOTE_100 => [
                'name' => totranslate('Unanimous Vote'),
                'tmdisplay' => totranslate('Unanimous Vote'),
                'description' => totranslate('Accept non-dictionary words if all players agree. Must play in friendly mode (no ELO). Recommended with friends only.'),
                'nobeginner' => true,
            ],
        ],
        'displaycondition' => [
            [
                'type' => 'minplayers',
                'value' => [2, 3, 4, 5],
            ],
        ],
        'startcondition' => [
            H_NO => [
                [
                    'type' => 'otheroptionisnot',
                    'id' => H_OPTION_DICTIONARY,
                    'value' => H_NO,
                    'message' => totranslate('Vote on Invalid Words is required when Dictionary = No Dictionary. You must also play in friendly mode (no ELO).'),
                ],
                [
                    'type' => 'otheroptionisnot',
                    'id' => H_OPTION_DICTIONARY_DE,
                    'value' => H_NO,
                    'message' => totranslate('Vote on Invalid Words is required when Dictionary = No Dictionary. You must also play in friendly mode (no ELO).'),
                ],
                [
                    'type' => 'otheroptionisnot',
                    'id' => H_OPTION_DICTIONARY_FR,
                    'value' => H_NO,
                    'message' => totranslate('Vote on Invalid Words is required when Dictionary = No Dictionary. You must also play in friendly mode (no ELO).'),
                ]
            ],
            H_VOTE_50 => $voteStartCondition,
            H_VOTE_100 => $voteStartCondition,
        ],
    ],

    H_OPTION_COOP => [
        'name' => totranslate('Game Type'),
        'level' => 'base',
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
                    'message' => totranslate('Game Type = Competetive is not available for solo play'),
                    'gamestartonly' => true,
                ],
            ],
            H_COOP_BASIC => $coopStartCondition,
            H_COOP_RANDOM => $coopStartCondition,
            H_COOP_ADVENTURE => $coopStartCondition,
            H_COOP_HORROR => $coopStartCondition,
            H_COOP_MYSTERY => $coopStartCondition,
            H_COOP_ROMANCE => $coopStartCondition,
        ],
    ],

    H_OPTION_LENGTH => [
        'name' => totranslate('Game Length'),
        'level' => 'base',
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

    H_OPTION_OPEN => [
        'name' => totranslate('Open Hands'),
        'default' => H_NO,
        'level' => 'base',
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
                'tmdisplay' => totranslate('Closed Hands'),
            ],
            H_YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Open Hands'),
            ]
        ],
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => H_OPTION_COOP,
                'value' => H_NO,
            ],
        ],
        'notdisplayedmessage' => totranslate('Yes'),
    ],

    H_OPTION_AWARDS => [
        'name' => totranslate('Literary Awards'),
        'default' => H_YES,
        'level' => 'base',
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
        'level' => 'base',
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

    H_OPTION_UNIQUE => [
        'name' => totranslate('Unique Words'),
        'default' => H_NO,
        'level' => 'additional',
        'values' => [
            H_NO => [
                'name' => totranslate('No'),
            ],
            H_UNIQUE_PLAYER => [
                'name' => totranslate('Unique Words Per Player'),
                'tmdisplay' => totranslate('Unique Words Per Player'),
                'description' => totranslate("The same player cannot spell the same word twice during the game"),
                'nobeginner' => true,
            ],
            H_UNIQUE_GAME => [
                'name' => totranslate('Unique Words Per Game'),
                'tmdisplay' => totranslate('Unique Words Per Game'),
                'description' => totranslate("The same word cannot be spelled twice during the game"),
                'nobeginner' => true,
            ]
        ],
    ],
];

$game_preferences = [
    H_DARK_MODE => [
        'name' => totranslate('Dark mode'),
        'needReload' => false,
        'values' => [
            0 => ['name' => totranslate('Automatic (system default)')],
            1 => ['name' => totranslate('Light')],
            2 => ['name' => totranslate('Dark'), 'cssPref' => 'dark'],
        ],
    ],

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
