<?php

use function PHPSTORM_META\map;

class CardMgr extends APP_GameClass
{
    private static $cards = null;

    private static $refBenefits = [
        COINS => [
            'text' => '%¢',
        ],
        DOUBLE => [
            'text' => 'Double adjacent card',
        ],
        EITHER => [
            'text' => '%¢ or %',
            'icon' => 'star',
        ],
        INK => [
            'text' => '1',
            'icon' => 'ink',
            'text2' => ' or ',
            'icon2' => 'remover',
        ],
        JAIL => [
            'text' => 'Jail offer row card',
        ],
        SPECIAL_ADVENTURE => [
            'text' => '2',
            'icon' => 'star',
            'text2' => ' for each',
            'icon2' => 'adventure',
        ],
        SPECIAL_HORROR => [
            'text' => 'Opponents return 1',
            'icon' => 'ink',
            'text2' => ' or ',
            'icon2' => 'remover',
        ],
        SPECIAL_MYSTERY => [
            'text' => '1',
            'icon' => 'star',
            'text2' => ' for each wild',
        ],
        SPECIAL_ROMANCE => [
            'text' => 'Peek at top 3 cards of your deck',
        ],
        POINTS => [
            'text' => '%',
            'icon' => 'star',
        ],
        TRASH_COINS => [
            'icon' => 'trash',
            'text2' => 'Trash this',
            'icon2' => 'chevron',
            'text3' => '%¢',
        ],
        TRASH_DISCARD => [
            'icon' => 'trash',
            'text2' => 'Trash discard',
            'icon2' => 'chevron',
            'text3' => '%¢',
        ],
        TRASH_POINTS => [
            'icon' => 'trash',
            'text2' => 'Trash this',
            'icon2' => 'chevron',
            'text3' => '%',
            'icon3' => 'star',
        ],
        UNCOVER => [
            'text' => 'Uncover adjacent wild',
        ],
    ];

    private static $refCards = [
        1 => ['genre' => ADVENTURE, 'letter' => 'A', 'cost' => 5, 'points' => 1, 'benefits' => [POINTS => 2, TRASH_COINS => 3], 'genreBenefits' => [POINTS => 1]],
        2 => ['genre' => ADVENTURE, 'letter' => 'A', 'cost' => 7, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2]],
        3 => ['genre' => ADVENTURE, 'letter' => 'B', 'cost' => 4, 'points' => 4, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
        4 => ['genre' => ADVENTURE, 'letter' => 'C', 'cost' => 3, 'benefits' => [COINS => 1, TRASH_COINS => 2], 'genreBenefits' => [COINS => 1]],
        5 => ['genre' => ADVENTURE, 'letter' => 'C', 'cost' => 5, 'timeless' => true, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        6 => ['genre' => ADVENTURE, 'letter' => 'D', 'cost' => 4, 'points' => 1, 'benefits' => [COINS => 2], 'genreBenefits' => [POINTS => 3]],
        7 => ['genre' => ADVENTURE, 'letter' => 'E', 'cost' => 3, 'benefits' => [POINTS => 1, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
        8 => ['genre' => ADVENTURE, 'letter' => 'F', 'cost' => 8, 'points' => 1, 'benefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 2]],
        9 => ['genre' => ADVENTURE, 'letter' => 'G', 'cost' => 2, 'benefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        10 => ['genre' => ADVENTURE, 'letter' => 'G', 'cost' => 6, 'benefits' => [POINTS => 4, TRASH_COINS => 4], 'genreBenefits' => [POINTS => 1]],
        11 => ['genre' => ADVENTURE, 'letter' => 'H', 'cost' => 3, 'points' => 3, 'benefits' => [POINTS => 1, TRASH_POINTS => 1], 'genreBenefits' => [POINTS => 1]],
        12 => ['genre' => ADVENTURE, 'letter' => 'I', 'cost' => 3, 'timeless' => true, 'benefits' => [POINTS => 2], 'genreBenefits' => []],
        13 => ['genre' => ADVENTURE, 'letter' => 'I', 'cost' => 6, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1]],
        14 => ['genre' => ADVENTURE, 'letter' => 'J', 'cost' => 3, 'benefits' => [POINTS => 2, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
        15 => ['genre' => ADVENTURE, 'letter' => 'J', 'cost' => 5, 'benefits' => [POINTS => 3, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 2]],
        16 => ['genre' => ADVENTURE, 'letter' => 'K', 'cost' => 9, 'points' => 2, 'benefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 3]],
        17 => ['genre' => ADVENTURE, 'letter' => 'L', 'cost' => 2, 'points' => 1, 'benefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1]],
        18 => ['genre' => ADVENTURE, 'letter' => 'M', 'cost' => 4, 'points' => 3, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        19 => ['genre' => ADVENTURE, 'letter' => 'M', 'cost' => 6, 'points' => 3, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2]],
        20 => ['genre' => ADVENTURE, 'letter' => 'N', 'cost' => 4, 'points' => 1, 'benefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, POINTS => 1]],
        21 => ['genre' => ADVENTURE, 'letter' => 'O', 'cost' => 6, 'benefits' => [POINTS => 2], 'genreBenefits' => [SPECIAL_ADVENTURE => true]],
        22 => ['genre' => ADVENTURE, 'letter' => 'P', 'cost' => 4, 'points' => 1, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
        23 => ['genre' => ADVENTURE, 'letter' => 'P', 'cost' => 8, 'timeless' => true, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
        24 => ['genre' => ADVENTURE, 'letter' => 'Q', 'cost' => 7, 'benefits' => [POINTS => 3, TRASH_POINTS => 3], 'genreBenefits' => [POINTS => 4]],
        25 => ['genre' => ADVENTURE, 'letter' => 'R', 'cost' => 3, 'points' => 1, 'benefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        26 => ['genre' => ADVENTURE, 'letter' => 'S', 'cost' => 5, 'points' => 1, 'benefits' => [POINTS => 2, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        27 => ['genre' => ADVENTURE, 'letter' => 'T', 'cost' => 4, 'points' => 2, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        28 => ['genre' => ADVENTURE, 'letter' => 'U', 'cost' => 4, 'benefits' => [POINTS => 1, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 3]],
        29 => ['genre' => ADVENTURE, 'letter' => 'V', 'cost' => 2, 'benefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        30 => ['genre' => ADVENTURE, 'letter' => 'W', 'cost' => 3, 'benefits' => [POINTS => 2, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
        31 => ['genre' => ADVENTURE, 'letter' => 'W', 'cost' => 5, 'points' => 2, 'timeless' => true, 'benefits' => [POINTS => 2], 'genreBenefits' => []],
        32 => ['genre' => ADVENTURE, 'letter' => 'X', 'cost' => 4, 'benefits' => [COINS => 2, TRASH_POINTS => 2], 'genreBenefits' => [COINS => 2]],
        33 => ['genre' => ADVENTURE, 'letter' => 'Y', 'cost' => 2, 'benefits' => [POINTS => 1, TRASH_COINS => 1], 'genreBenefits' => [POINTS => 1]],
        34 => ['genre' => ADVENTURE, 'letter' => 'Y', 'cost' => 4, 'points' => 3, 'benefits' => [COINS => 2, TRASH_COINS => 2], 'genreBenefits' => [COINS => 1]],
        35 => ['genre' => ADVENTURE, 'letter' => 'Z', 'cost' => 5, 'points' => 3, 'benefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 1]],

        36 => ['genre' => HORROR, 'letter' => 'A', 'cost' => 3, 'benefits' => [EITHER => 2], 'genreBenefits' => []],
        37 => ['genre' => HORROR, 'letter' => 'B', 'cost' => 6, 'benefits' => [COINS => 3], 'genreBenefits' => [COINS => 2, INK => true]],
        38 => ['genre' => HORROR, 'letter' => 'C', 'cost' => 5, 'benefits' => [POINTS => 2, INK => true], 'genreBenefits' => [POINTS => 1]],
        39 => ['genre' => HORROR, 'letter' => 'C', 'cost' => 8, 'benefits' => [COINS => 2, INK => true], 'genreBenefits' => [COINS => 3]],
        40 => ['genre' => HORROR, 'letter' => 'D', 'cost' => 4, 'timeless' => true, 'benefits' => [EITHER => 1], 'genreBenefits' => [COINS => 1, POINTS => 1]],
        41 => ['genre' => HORROR, 'letter' => 'D', 'cost' => 9, 'benefits' => [POINTS => 3, INK => true], 'genreBenefits' => [POINTS => 3]],
        42 => ['genre' => HORROR, 'letter' => 'E', 'cost' => 5, 'timeless' => true, 'benefits' => [EITHER => 2], 'genreBenefits' => [POINTS => 1]],
        43 => ['genre' => HORROR, 'letter' => 'E', 'cost' => 8, 'benefits' => [COINS => 2, INK => true], 'genreBenefits' => [EITHER => 2]],
        44 => ['genre' => HORROR, 'letter' => 'F', 'cost' => 3, 'benefits' => [POINTS => 2], 'genreBenefits' => [EITHER => 2, INK => true]],
        45 => ['genre' => HORROR, 'letter' => 'G', 'cost' => 4, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, INK => true]],
        46 => ['genre' => HORROR, 'letter' => 'H', 'cost' => 7, 'benefits' => [COINS => 2, POINTS => 1], 'genreBenefits' => [COINS => 1, POINTS => 2, SPECIAL_HORROR => true]],
        47 => ['genre' => HORROR, 'letter' => 'I', 'cost' => 4, 'benefits' => [POINTS => 2], 'genreBenefits' => [EITHER => 2]],
        48 => ['genre' => HORROR, 'letter' => 'J', 'cost' => 5, 'benefits' => [POINTS => 3, INK => true], 'genreBenefits' => [POINTS => 2]],
        49 => ['genre' => HORROR, 'letter' => 'K', 'cost' => 2, 'benefits' => [EITHER => 1], 'genreBenefits' => [COINS => 2]],
        50 => ['genre' => HORROR, 'letter' => 'L', 'cost' => 3, 'benefits' => [POINTS => 2], 'genreBenefits' => [INK => true]],
        51 => ['genre' => HORROR, 'letter' => 'M', 'cost' => 3, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        52 => ['genre' => HORROR, 'letter' => 'N', 'cost' => 5, 'benefits' => [COINS => 2, INK => true], 'genreBenefits' => [COINS => 1]],
        53 => ['genre' => HORROR, 'letter' => 'N', 'cost' => 6, 'timeless' => true, 'benefits' => [EITHER => 1], 'genreBenefits' => [POINTS => 2, INK => true]],
        54 => ['genre' => HORROR, 'letter' => 'O', 'cost' => 4, 'benefits' => [EITHER => 2], 'genreBenefits' => [EITHER => 1]],
        55 => ['genre' => HORROR, 'letter' => 'P', 'cost' => 3, 'benefits' => [POINTS => 2, INK => true], 'genreBenefits' => []],
        56 => ['genre' => HORROR, 'letter' => 'Q', 'cost' => 4, 'benefits' => [COINS => 3], 'genreBenefits' => [COINS => 1, INK => true]],
        57 => ['genre' => HORROR, 'letter' => 'R', 'cost' => 4, 'benefits' => [EITHER => 1], 'genreBenefits' => [COINS => 2, INK => true]],
        58 => ['genre' => HORROR, 'letter' => 'S', 'cost' => 2, 'benefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1]],
        59 => ['genre' => HORROR, 'letter' => 'S', 'cost' => 7, 'benefits' => [POINTS => 3, INK => true], 'genreBenefits' => [POINTS => 1]],
        60 => ['genre' => HORROR, 'letter' => 'T', 'cost' => 4, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, INK => true]],
        61 => ['genre' => HORROR, 'letter' => 'U', 'cost' => 2, 'benefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        62 => ['genre' => HORROR, 'letter' => 'U', 'cost' => 6, 'benefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 1]],
        63 => ['genre' => HORROR, 'letter' => 'V', 'cost' => 4, 'benefits' => [COINS => 2], 'genreBenefits' => [COINS => 2, INK => true]],
        64 => ['genre' => HORROR, 'letter' => 'V', 'cost' => 5, 'timeless' => true, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, INK => true]],
        65 => ['genre' => HORROR, 'letter' => 'W', 'cost' => 4, 'benefits' => [POINTS => 2, INK => true], 'genreBenefits' => [POINTS => 2]],
        66 => ['genre' => HORROR, 'letter' => 'W', 'cost' => 5, 'benefits' => [COINS => 2, INK => true], 'genreBenefits' => [COINS => 3]],
        67 => ['genre' => HORROR, 'letter' => 'X', 'cost' => 2, 'benefits' => [EITHER => 1], 'genreBenefits' => [POINTS => 2]],
        68 => ['genre' => HORROR, 'letter' => 'X', 'cost' => 6, 'benefits' => [POINTS => 3, INK => true], 'genreBenefits' => [POINTS => 3]],
        69 => ['genre' => HORROR, 'letter' => 'Y', 'cost' => 3, 'benefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, INK => true]],
        70 => ['genre' => HORROR, 'letter' => 'Z', 'cost' => 3, 'benefits' => [EITHER => 2], 'genreBenefits' => [EITHER => 1, INK => true]],

        71 => ['genre' => ROMANCE, 'letter' => 'A', 'cost' => 4, 'benefits' => [COINS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
        72 => ['genre' => ROMANCE, 'letter' => 'B', 'cost' => 3, 'benefits' => [COINS => 2], 'genreBenefits' => [DOUBLE => true]],
        73 => ['genre' => ROMANCE, 'letter' => 'B', 'cost' => 5, 'timeless' => true, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        74 => ['genre' => ROMANCE, 'letter' => 'C', 'cost' => 3, 'benefits' => [POINTS => 2], 'genreBenefits' => [TRASH_DISCARD => 1]],
        75 => ['genre' => ROMANCE, 'letter' => 'D', 'cost' => 4, 'benefits' => [COINS => 2], 'genreBenefits' => [DOUBLE => true]],
        76 => ['genre' => ROMANCE, 'letter' => 'E', 'cost' => 2, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1]],
        77 => ['genre' => ROMANCE, 'letter' => 'E', 'cost' => 6, 'benefits' => [POINTS => 3], 'genreBenefits' => [TRASH_DISCARD => 1]],
        78 => ['genre' => ROMANCE, 'letter' => 'F', 'cost' => 4, 'benefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 1]],
        79 => ['genre' => ROMANCE, 'letter' => 'F', 'cost' => 6, 'benefits' => [COINS => 2, DOUBLE => true], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        80 => ['genre' => ROMANCE, 'letter' => 'G', 'cost' => 3, 'benefits' => [POINTS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 1]],
        81 => ['genre' => ROMANCE, 'letter' => 'H', 'cost' => 3, 'benefits' => [COINS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
        82 => ['genre' => ROMANCE, 'letter' => 'H', 'cost' => 7, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, DOUBLE => true]],
        83 => ['genre' => ROMANCE, 'letter' => 'I', 'cost' => 2, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1]],
        84 => ['genre' => ROMANCE, 'letter' => 'J', 'cost' => 6, 'benefits' => [COINS => 2, DOUBLE => true], 'genreBenefits' => [COINS => 2, TRASH_DISCARD => 1]],
        85 => ['genre' => ROMANCE, 'letter' => 'K', 'cost' => 3, 'benefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        86 => ['genre' => ROMANCE, 'letter' => 'K', 'cost' => 5, 'timeless' => true, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        87 => ['genre' => ROMANCE, 'letter' => 'L', 'cost' => 8, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, DOUBLE => true]],
        88 => ['genre' => ROMANCE, 'letter' => 'M', 'cost' => 2, 'benefits' => [COINS => 1, TRASH_DISCARD => 1], 'genreBenefits' => []],
        89 => ['genre' => ROMANCE, 'letter' => 'N', 'cost' => 2, 'benefits' => [POINTS => 1], 'genreBenefits' => [TRASH_DISCARD => 1]],
        90 => ['genre' => ROMANCE, 'letter' => 'N', 'cost' => 5, 'benefits' => [COINS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
        91 => ['genre' => ROMANCE, 'letter' => 'O', 'cost' => 4, 'benefits' => [COINS => 2], 'genreBenefits' => [DOUBLE => true]],
        92 => ['genre' => ROMANCE, 'letter' => 'O', 'cost' => 8, 'timeless' => true, 'benefits' => [COINS => 2, POINTS => 1], 'genreBenefits' => [COINS => 1, POINTS => 1]],
        93 => ['genre' => ROMANCE, 'letter' => 'P', 'cost' => 6, 'benefits' => [POINTS => 2, DOUBLE => true], 'genreBenefits' => [POINTS => 1]],
        94 => ['genre' => ROMANCE, 'letter' => 'Q', 'cost' => 4, 'benefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
        95 => ['genre' => ROMANCE, 'letter' => 'R', 'cost' => 5, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, SPECIAL_ROMANCE => true]],
        96 => ['genre' => ROMANCE, 'letter' => 'R', 'cost' => 5, 'timeless' => true, 'benefits' => [POINTS => 2], 'genreBenefits' => [TRASH_DISCARD => 1]],
        97 => ['genre' => ROMANCE, 'letter' => 'S', 'cost' => 4, 'benefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1, DOUBLE => true]],
        98 => ['genre' => ROMANCE, 'letter' => 'T', 'cost' => 3, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        99 => ['genre' => ROMANCE, 'letter' => 'U', 'cost' => 9, 'benefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 1, DOUBLE => true]],
        100 => ['genre' => ROMANCE, 'letter' => 'V', 'cost' => 3, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, TRASH_DISCARD => 1]],
        101 => ['genre' => ROMANCE, 'letter' => 'W', 'cost' => 4, 'benefits' => [POINTS => 1, DOUBLE => true], 'genreBenefits' => [POINTS => 1]],
        102 => ['genre' => ROMANCE, 'letter' => 'X', 'cost' => 7, 'benefits' => [POINTS => 4, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
        103 => ['genre' => ROMANCE, 'letter' => 'Y', 'cost' => 4, 'benefits' => [POINTS => 1, DOUBLE => true], 'genreBenefits' => [TRASH_DISCARD => 1]],
        104 => ['genre' => ROMANCE, 'letter' => 'Z', 'cost' => 4, 'benefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
        105 => ['genre' => ROMANCE, 'letter' => 'Z', 'cost' => 5, 'benefits' => [POINTS => 2, DOUBLE => true], 'genreBenefits' => [COINS => 2]],

        106 => ['genre' => MYSTERY, 'letter' => 'A', 'cost' => 3, 'benefits' => [POINTS => 1, UNCOVER => true], 'genreBenefits' => []],
        107 => ['genre' => MYSTERY, 'letter' => 'A', 'cost' => 5, 'timeless' => true, 'benefits' => [COINS => 2], 'genreBenefits' => [COINS => 1]],
        108 => ['genre' => MYSTERY, 'letter' => 'B', 'cost' => 4, 'benefits' => [COINS => 2, JAIL => true], 'genreBenefits' => [COINS => 2]],
        109 => ['genre' => MYSTERY, 'letter' => 'C', 'cost' => 5, 'benefits' => [POINTS => 2, UNCOVER => true], 'genreBenefits' => [JAIL => true]],
        110 => ['genre' => MYSTERY, 'letter' => 'D', 'cost' => 4, 'benefits' => [POINTS => 1, UNCOVER => true], 'genreBenefits' => [POINTS => 2]],
        111 => ['genre' => MYSTERY, 'letter' => 'E', 'cost' => 4, 'benefits' => [COINS => 2], 'genreBenefits' => [UNCOVER => true]],
        112 => ['genre' => MYSTERY, 'letter' => 'F', 'cost' => 2, 'benefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 1]],
        113 => ['genre' => MYSTERY, 'letter' => 'F', 'cost' => 5, 'timeless' => true, 'benefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 1]],
        114 => ['genre' => MYSTERY, 'letter' => 'G', 'cost' => 6, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, UNCOVER => true]],
        115 => ['genre' => MYSTERY, 'letter' => 'H', 'cost' => 3, 'benefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 2]],
        116 => ['genre' => MYSTERY, 'letter' => 'I', 'cost' => 3, 'benefits' => [POINTS => 2], 'genreBenefits' => [JAIL => true]],
        117 => ['genre' => MYSTERY, 'letter' => 'I', 'cost' => 5, 'benefits' => [COINS => 2, UNCOVER => true], 'genreBenefits' => []],
        118 => ['genre' => MYSTERY, 'letter' => 'J', 'cost' => 8, 'benefits' => [POINTS => 5, UNCOVER => true], 'genreBenefits' => [POINTS => 2]],
        119 => ['genre' => MYSTERY, 'letter' => 'K', 'cost' => 2, 'benefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1, UNCOVER => true]],
        120 => ['genre' => MYSTERY, 'letter' => 'K', 'cost' => 4, 'benefits' => [COINS => 2, UNCOVER => true], 'genreBenefits' => [COINS => 2]],
        121 => ['genre' => MYSTERY, 'letter' => 'L', 'cost' => 6, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1, UNCOVER => true]],
        122 => ['genre' => MYSTERY, 'letter' => 'M', 'cost' => 3, 'benefits' => [COINS => 1, UNCOVER => true], 'genreBenefits' => [COINS => 1]],
        123 => ['genre' => MYSTERY, 'letter' => 'M', 'cost' => 4, 'timeless' => true, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, JAIL => true]],
        124 => ['genre' => MYSTERY, 'letter' => 'N', 'cost' => 7, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1, SPECIAL_MYSTERY => true]],
        125 => ['genre' => MYSTERY, 'letter' => 'O', 'cost' => 3, 'benefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [UNCOVER => true]],
        126 => ['genre' => MYSTERY, 'letter' => 'P', 'cost' => 2, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, JAIL => true]],
        127 => ['genre' => MYSTERY, 'letter' => 'P', 'cost' => 4, 'benefits' => [POINTS => 1, UNCOVER => true], 'genreBenefits' => [POINTS => 2]],
        128 => ['genre' => MYSTERY, 'letter' => 'Q', 'cost' => 3, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, UNCOVER => true]],
        129 => ['genre' => MYSTERY, 'letter' => 'Q', 'cost' => 5, 'benefits' => [POINTS => 2, UNCOVER => true], 'genreBenefits' => [POINTS => 3]],
        130 => ['genre' => MYSTERY, 'letter' => 'R', 'cost' => 4, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 2, UNCOVER => true]],
        131 => ['genre' => MYSTERY, 'letter' => 'R', 'cost' => 6, 'benefits' => [POINTS => 2, UNCOVER => true], 'genreBenefits' => [POINTS => 1, JAIL => true]],
        132 => ['genre' => MYSTERY, 'letter' => 'S', 'cost' => 4, 'benefits' => [COINS => 1, JAIL => true], 'genreBenefits' => [COINS => 2]],
        133 => ['genre' => MYSTERY, 'letter' => 'T', 'cost' => 6, 'benefits' => [POINTS => 2, UNCOVER => true], 'genreBenefits' => [POINTS => 2]],
        134 => ['genre' => MYSTERY, 'letter' => 'T', 'cost' => 8, 'timeless' => true, 'benefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, JAIL => true]],
        135 => ['genre' => MYSTERY, 'letter' => 'U', 'cost' => 2, 'benefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, UNCOVER => true]],
        136 => ['genre' => MYSTERY, 'letter' => 'V', 'cost' => 9, 'benefits' => [POINTS => 4, UNCOVER => true], 'genreBenefits' => [POINTS => 4]],
        137 => ['genre' => MYSTERY, 'letter' => 'W', 'cost' => 4, 'benefits' => [COINS => 2], 'genreBenefits' => [COINS => 2, UNCOVER => true]],
        138 => ['genre' => MYSTERY, 'letter' => 'X', 'cost' => 3, 'benefits' => [POINTS => 3, JAIL => true], 'genreBenefits' => []],
        139 => ['genre' => MYSTERY, 'letter' => 'Y', 'cost' => 7, 'benefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 2, UNCOVER => true]],
        140 => ['genre' => MYSTERY, 'letter' => 'Z', 'cost' => 5, 'benefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, UNCOVER => true]],

        141 => ['genre' => STARTER, 'letter' => 'A', 'cost' => 0, 'benefits' => [COINS => 1], 'genreBenefits' => []],
        142 => ['genre' => STARTER, 'letter' => 'B', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        143 => ['genre' => STARTER, 'letter' => 'C', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        144 => ['genre' => STARTER, 'letter' => 'D', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        145 => ['genre' => STARTER, 'letter' => 'E', 'cost' => 0, 'benefits' => [COINS => 1], 'genreBenefits' => []],
        146 => ['genre' => STARTER, 'letter' => 'G', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        147 => ['genre' => STARTER, 'letter' => 'H', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        148 => ['genre' => STARTER, 'letter' => 'I', 'cost' => 0, 'benefits' => [COINS => 1], 'genreBenefits' => []],
        149 => ['genre' => STARTER, 'letter' => 'L', 'cost' => 0, 'benefits' => [COINS => 1], 'genreBenefits' => []],
        150 => ['genre' => STARTER, 'letter' => 'M', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        151 => ['genre' => STARTER, 'letter' => 'N', 'cost' => 0, 'benefits' => [COINS => 1], 'genreBenefits' => []],
        152 => ['genre' => STARTER, 'letter' => 'O', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        153 => ['genre' => STARTER, 'letter' => 'P', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        154 => ['genre' => STARTER, 'letter' => 'R', 'cost' => 0, 'benefits' => [COINS => 1], 'genreBenefits' => []],
        155 => ['genre' => STARTER, 'letter' => 'S', 'cost' => 0, 'benefits' => [COINS => 1], 'genreBenefits' => []],
        156 => ['genre' => STARTER, 'letter' => 'T', 'cost' => 0, 'benefits' => [COINS => 1], 'genreBenefits' => []],
        157 => ['genre' => STARTER, 'letter' => 'U', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
        158 => ['genre' => STARTER, 'letter' => 'Y', 'cost' => 0, 'benefits' => [POINTS => 1], 'genreBenefits' => []],
    ];

    public static function __constructStatic()
    {
        self::$cards = self::getNew('module.common.deck');
        self::$cards->init('card');
        self::$cards->autoreshuffle = true;
        self::$cards->autoreshuffle_custom = ['deck' => 'discard'];
        foreach (PlayerMgr::getPlayerIds() as $playerId) {
            self::$cards->autoreshuffle_custom[self::getDeckLocation($playerId)] = self::getDiscardLocation($playerId);
        }
    }

    public static function populateCard($dbcard, $minimal = false)
    {
        if ($dbcard == null) {
            return null;
        }
        $output = [];
        if (!$minimal) {
            $type = self::$refCards[intval($dbcard['card_type_arg'])];
            $output = $type;
            $output['location'] = $dbcard['card_location'];
            $output['genreName'] = 'Starter';
            if ($output['genre'] == ADVENTURE) {
                $output['genreName'] = 'Adventure';
            } else if ($output['genre'] == HORROR) {
                $output['genreName'] = 'Horror';
            } else if ($output['genre'] == MYSTERY) {
                $output['genreName'] = 'Mystery';
            } else if ($output['genre'] == ROMANCE) {
                $output['genreName'] = 'Romance';
            }
            $output['desc'] = $output['genreName'] . '-' . $output['letter'];
        }
        $output['id'] = intval($dbcard['id'] ?? $dbcard['card_id']);
        $output['refId'] = intval($dbcard['refId'] ?? $dbcard['card_type_arg']);
        $output['order'] = intval($dbcard['order'] ?? $dbcard['card_location_arg']);
        $output['origin'] = $dbcard['origin'] ?? $dbcard['card_type'];
        if (array_key_exists('ink', $dbcard) && $dbcard['ink'] == 1) {
            $output['ink']  = true;
        }
        return $output;
    }

    public static function populateCards($dbcards, $minimal = false)
    {
        $cards = array_map(function ($card) use ($minimal) {
            return self::populateCard($card, $minimal);
        }, $dbcards);
        /*
        if ($minimal) {
            // Minimal always returns an array, never an object
            $cards = array_values($cards);
        }
        */
        return $cards;
    }

    public static function getCardRef()
    {
        return self::$refCards;
    }

    public static function getBenefitRef()
    {
        return self::$refBenefits;
    }

    public static function getTimelessRefIds()
    {
        $refIds = [];
        foreach (self::$refCards as $refId => $ref) {
            if ($ref['timeless']) {
                $refIds[] = $refId;
                break;
            }
        }
        return $refIds;
    }

    private static function getWhereClause($cardIds)
    {
        if (is_numeric($cardIds)) {
            return "card_id = $cardIds";
        } else if (is_array($cardIds)) {
            return "card_id IN (" . implode(',', $cardIds) . ")";
        }
        return "0 = 1";
    }

    /* Change */

    public static function setup()
    {
        // Create starter cards
        // Deal 5 to each player
        $randomLetters = ['B', 'C', 'D', 'G', 'H', 'M', 'O', 'P', 'U', 'Y'];
        shuffle($randomLetters);
        $playerIds = PlayerMgr::getPlayerIds();
        self::dump("playerIds", $playerIds);
        foreach ($playerIds as $playerId) {
            $letters = array_merge(['A', 'E', 'I', 'L', 'N', 'R', 'S', 'T'], array_splice($randomLetters, 0, 2));
            $create = [];
            foreach ($letters as $letter) {
                // Find the type ID
                foreach (self::$refCards as $refId => $ref) {
                    if ($ref['genre'] == STARTER && $ref['letter'] == $letter) {
                        $create[] = ['type' => '', 'type_arg' => $refId, 'nbr' => 1];
                        break;
                    }
                }
            }
            self::$cards->createCards($create, self::getDeckLocation($playerId));
            self::$cards->shuffle(self::getDeckLocation($playerId));
            self::drawCards(5, self::getDeckLocation($playerId), self::getHandLocation($playerId), 'letter');
            self::$cards->shuffle(self::getHandLocation($playerId));
        }

        // Create genre cards
        // Deal 7 to the table
        $create = [];
        foreach (self::$refCards as $refId => $ref) {
            if ($ref['genre'] != STARTER) {
                $create[] = ['type' => '', 'type_arg' => $refId, 'nbr' => 1];
            }
        }
        self::$cards->createCards($create, 'deck');
        self::$cards->shuffle('deck');
        self::drawCards(7, 'deck', 'offer', 'cost');
        self::$cards->shuffle('offer');
        self::updateOrigin();
    }

    public static function updateOrigin()
    {
        self::DbQuery("UPDATE card SET card_type = card_location");
    }

    public static function drawCards($count, $fromLocation, $toLocation, $sort = null)
    {
        $dbcards = self::$cards->pickCardsForLocation($count, $fromLocation, $toLocation);
        $cardIds = array_map(function ($dbcard) {
            return intval($dbcard['id']);
        }, $dbcards);
        if (count($cardIds) > 1) {
            if ($sort == 'letter') {
                // TODO
            } else if ($sort == 'cost') {
                // TODO
            }
        }
        return $cardIds;
    }

    public static function inkCards($cardIds, $inkValue = 1)
    {
        $sql = "UPDATE card SET ink = $inkValue";
        if ($inkValue == 1) {
            $sql .= ", card_type = card_location";
        }
        $sql .= " WHERE " . self::getWhereClause($cardIds);
        self::DbQuery($sql);
    }

    public static function orderCard($cardId, $order)
    {
        self::moveAndOrderCard($cardId, null, $order);
    }

    public static function moveAndOrderCard($cardId, $location = null, $order)
    {
        $card = self::getCard($cardId);
        if ($location == null) {
            $location = $card['location'];
        }
        self::DbQuery("UPDATE card SET card_location_arg = card_location_arg - 1 WHERE card_location = '{$card['location']}' AND card_location_arg > {$card['order']}");
        self::DbQuery("UPDATE card SET card_location_arg = card_location_arg + 1 WHERE card_location = '$location' AND card_location_arg >= $order");
        self::DbQuery("UPDATE card SET card_location = '$location', card_location_arg = $order WHERE card_id = $cardId");
    }

    public static function moveAndOrderCards($cardIds, $location, $order = null)
    {
        if ($order = null) {
            $order = self::getCountInLocation($location);
        }
        foreach ($cardIds as $cardId) {
            self::moveAndOrderCard($cardId, $location, $order++);
        }
    }

    public static function reset($playerId)
    {
        // Clear ink and remover
        self::DbQuery("UPDATE card SET ink = NULL WHERE ink IS NOT NULL AND card_location IN ('tableau', '" . self::getHandLocation($playerId) . "')");

        // Tableau and hand return to current player's discard
        self::DbQuery("UPDATE card SET card_location = '" . self::getDiscardLocation($playerId) . "' WHERE card_location IN ('tableau', '" . self::getHandLocation($playerId) . "')");

        // Draw new hand for current player
        self::drawCards(5, self::getDeckLocation($playerId), self::getHandLocation($playerId), 'letter');
        self::updateOrigin();
    }

    /* Query (generic) */

    public static function getCard($cardId)
    {
        $cards = self::getCards([$cardId]);
        return array_shift($cards);
    }

    public static function getCards($cardIds)
    {
        $sql = "SELECT * FROM card WHERE " . self::getWhereClause($cardIds);
        $dbcards = self::getCollectionFromDB($sql);
        $cards = self::populateCards($dbcards);
        $output = [];
        foreach ($cardIds as $cardId) {
            if (array_key_exists($cardId, $cards)) {
                $output[] = $cards[$cardId];
            }
        }
        return $output;
    }

    public static function getCardsInLocation($location, $inkValue = null)
    {
        $sql = "SELECT * FROM card WHERE card_location = '$location'";
        if ($inkValue != null) {
            $sql .= " AND ink = $inkValue";
        }
        $sql .= " ORDER BY card_location_arg";
        $dbcards = self::getObjectListFromDb($sql);
        return self::populateCards($dbcards);
    }

    public static function getCountInLocation($location)
    {
        return self::$cards->countCardInLocation($location);
    }

    public static function getHandLocation($playerId)
    {
        return "hand_$playerId";
    }

    public static function getDeckLocation($playerId)
    {
        return "deck_$playerId";
    }

    public static function getDiscardLocation($playerId)
    {
        return "discard_$playerId";
    }

    /* Query (specific) */

    public static function getHand($playerId, $inkValue = null)
    {
        return self::getCardsInLocation(self::getHandLocation($playerId), $inkValue);
    }

    public static function getHandCount($playerId)
    {
        return self::getCountInLocation(self::getHandLocation($playerId));
    }

    public static function getDeckCount($playerId)
    {
        return self::getCountInLocation(self::getDeckLocation($playerId));
    }

    public static function getDiscard($playerId)
    {
        return self::getCardsInLocation(self::getDiscardLocation($playerId));
    }

    public static function getDiscardCount($playerId)
    {
        return self::getCountInLocation(self::getDiscardLocation($playerId));
    }

    public static function getTableau()
    {
        return self::getCardsInLocation("tableau");
    }

    public static function getTimeless()
    {
        return self::getCardsInLocation("timeless");
    }

    public static function getOffer()
    {
        return self::getCardsInLocation("offer");
    }

    public static function getOfferDeckCount()
    {
        return self::getCountInLocation("deck");
    }
}

CardMgr::__constructStatic();
