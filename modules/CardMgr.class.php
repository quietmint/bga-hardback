<?php

class CardMgr extends APP_GameClass
{
    private static $cards = null;

    public static $refBenefits = [
        COINS => [
            'text' => '%¢',
        ],
        DOUBLE_ADJ => [
            'text' => 'Double adjacent card',
        ],
        EITHER_BASIC => [
            'text' => '%¢ or %',
            'icon' => 'star',
        ],
        EITHER_GENRE => [
            'text' => '%¢ or %',
            'icon' => 'star',
        ],
        INK => [
            'text' => '1 ink or remover',
        ],
        JAIL => [
            'text' => 'Jail offer card',
        ],
        SPECIAL_ADVENTURE => [
            'text' => '2',
            'icon' => 'star',
            'text2' => ' for each ',
            'icon2' => 'adventure',
        ],
        SPECIAL_HORROR => [
            'text' => 'Opponents return 1 ink/remover',
        ],
        SPECIAL_MYSTERY => [
            'text' => '1',
            'icon' => 'star',
            'text2' => ' for each wild',
        ],
        SPECIAL_ROMANCE => [
            'text' => 'Draw 3 cards. Return or discard each.',
        ],
        POINTS => [
            'text' => '%',
            'icon' => 'star',
        ],
        TRASH_COINS => [
            'text' => 'Trash this for %¢',
        ],
        TRASH_DISCARD => [
            'text' => 'Trash discard for %¢',
        ],
        TRASH_POINTS => [
            'text' => 'Trash this for %',
            'icon3' => 'star',
        ],
        UNCOVER_ADJ => [
            'text' => 'Uncover adjacent wild',
        ],
    ];

    public static $refCards = [
        1 => ['genre' => ADVENTURE, 'letter' => 'A', 'cost' => 5, 'points' => 1, 'basicBenefits' => [POINTS => 2, TRASH_COINS => 3], 'genreBenefits' => [POINTS => 1]],
        2 => ['genre' => ADVENTURE, 'letter' => 'A', 'cost' => 7, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2]],
        3 => ['genre' => ADVENTURE, 'letter' => 'B', 'cost' => 4, 'points' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
        4 => ['genre' => ADVENTURE, 'letter' => 'C', 'cost' => 3, 'basicBenefits' => [COINS => 1, TRASH_COINS => 2], 'genreBenefits' => [COINS => 1]],
        5 => ['genre' => ADVENTURE, 'letter' => 'C', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        6 => ['genre' => ADVENTURE, 'letter' => 'D', 'cost' => 4, 'points' => 1, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [POINTS => 3]],
        7 => ['genre' => ADVENTURE, 'letter' => 'E', 'cost' => 3, 'basicBenefits' => [POINTS => 1, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
        8 => ['genre' => ADVENTURE, 'letter' => 'F', 'cost' => 8, 'points' => 1, 'basicBenefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 2]],
        9 => ['genre' => ADVENTURE, 'letter' => 'G', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        10 => ['genre' => ADVENTURE, 'letter' => 'G', 'cost' => 6, 'basicBenefits' => [POINTS => 4, TRASH_COINS => 4], 'genreBenefits' => [POINTS => 1]],
        11 => ['genre' => ADVENTURE, 'letter' => 'H', 'cost' => 3, 'points' => 3, 'basicBenefits' => [POINTS => 1, TRASH_POINTS => 1], 'genreBenefits' => [POINTS => 1]],
        12 => ['genre' => ADVENTURE, 'letter' => 'I', 'cost' => 3, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => []],
        13 => ['genre' => ADVENTURE, 'letter' => 'I', 'cost' => 6, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1]],
        14 => ['genre' => ADVENTURE, 'letter' => 'J', 'cost' => 3, 'basicBenefits' => [POINTS => 2, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
        15 => ['genre' => ADVENTURE, 'letter' => 'J', 'cost' => 5, 'basicBenefits' => [POINTS => 3, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 2]],
        16 => ['genre' => ADVENTURE, 'letter' => 'K', 'cost' => 9, 'points' => 2, 'basicBenefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 3]],
        17 => ['genre' => ADVENTURE, 'letter' => 'L', 'cost' => 2, 'points' => 1, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1]],
        18 => ['genre' => ADVENTURE, 'letter' => 'M', 'cost' => 4, 'points' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        19 => ['genre' => ADVENTURE, 'letter' => 'M', 'cost' => 6, 'points' => 3, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2]],
        20 => ['genre' => ADVENTURE, 'letter' => 'N', 'cost' => 4, 'points' => 1, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, POINTS => 1]],
        21 => ['genre' => ADVENTURE, 'letter' => 'O', 'cost' => 6, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [SPECIAL_ADVENTURE => true]],
        22 => ['genre' => ADVENTURE, 'letter' => 'P', 'cost' => 4, 'points' => 1, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
        23 => ['genre' => ADVENTURE, 'letter' => 'P', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2]],
        24 => ['genre' => ADVENTURE, 'letter' => 'Q', 'cost' => 7, 'basicBenefits' => [POINTS => 3, TRASH_POINTS => 3], 'genreBenefits' => [POINTS => 4]],
        25 => ['genre' => ADVENTURE, 'letter' => 'R', 'cost' => 3, 'points' => 1, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        26 => ['genre' => ADVENTURE, 'letter' => 'S', 'cost' => 5, 'points' => 1, 'basicBenefits' => [POINTS => 2, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        27 => ['genre' => ADVENTURE, 'letter' => 'T', 'cost' => 4, 'points' => 2, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        28 => ['genre' => ADVENTURE, 'letter' => 'U', 'cost' => 4, 'basicBenefits' => [POINTS => 1, TRASH_POINTS => 2], 'genreBenefits' => [POINTS => 3]],
        29 => ['genre' => ADVENTURE, 'letter' => 'V', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        30 => ['genre' => ADVENTURE, 'letter' => 'W', 'cost' => 3, 'basicBenefits' => [POINTS => 2, TRASH_COINS => 2], 'genreBenefits' => [POINTS => 1]],
        31 => ['genre' => ADVENTURE, 'letter' => 'W', 'cost' => 5, 'points' => 2, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => []],
        32 => ['genre' => ADVENTURE, 'letter' => 'X', 'cost' => 4, 'basicBenefits' => [COINS => 2, TRASH_POINTS => 2], 'genreBenefits' => [COINS => 2]],
        33 => ['genre' => ADVENTURE, 'letter' => 'Y', 'cost' => 2, 'basicBenefits' => [POINTS => 1, TRASH_COINS => 1], 'genreBenefits' => [POINTS => 1]],
        34 => ['genre' => ADVENTURE, 'letter' => 'Y', 'cost' => 4, 'points' => 3, 'basicBenefits' => [COINS => 2, TRASH_COINS => 2], 'genreBenefits' => [COINS => 1]],
        35 => ['genre' => ADVENTURE, 'letter' => 'Z', 'cost' => 5, 'points' => 3, 'basicBenefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 1]],

        36 => ['genre' => HORROR, 'letter' => 'A', 'cost' => 3, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => []],
        37 => ['genre' => HORROR, 'letter' => 'B', 'cost' => 6, 'basicBenefits' => [COINS => 3], 'genreBenefits' => [COINS => 2, INK => true]],
        38 => ['genre' => HORROR, 'letter' => 'C', 'cost' => 5, 'basicBenefits' => [POINTS => 2, INK => true], 'genreBenefits' => [POINTS => 1]],
        39 => ['genre' => HORROR, 'letter' => 'C', 'cost' => 8, 'basicBenefits' => [COINS => 2, INK => true], 'genreBenefits' => [COINS => 3]],
        40 => ['genre' => HORROR, 'letter' => 'D', 'cost' => 4, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 1, POINTS => 1]],
        41 => ['genre' => HORROR, 'letter' => 'D', 'cost' => 9, 'basicBenefits' => [POINTS => 3, INK => true], 'genreBenefits' => [POINTS => 3]],
        42 => ['genre' => HORROR, 'letter' => 'E', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [POINTS => 1]],
        43 => ['genre' => HORROR, 'letter' => 'E', 'cost' => 8, 'basicBenefits' => [COINS => 2, INK => true], 'genreBenefits' => [EITHER_GENRE => 2]],
        44 => ['genre' => HORROR, 'letter' => 'F', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [EITHER_GENRE => 2, INK => true]],
        45 => ['genre' => HORROR, 'letter' => 'G', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, INK => true]],
        46 => ['genre' => HORROR, 'letter' => 'H', 'cost' => 7, 'basicBenefits' => [COINS => 2, POINTS => 1], 'genreBenefits' => [COINS => 1, POINTS => 2, SPECIAL_HORROR => true]],
        47 => ['genre' => HORROR, 'letter' => 'I', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [EITHER_GENRE => 2]],
        48 => ['genre' => HORROR, 'letter' => 'J', 'cost' => 5, 'basicBenefits' => [POINTS => 3, INK => true], 'genreBenefits' => [POINTS => 2]],
        49 => ['genre' => HORROR, 'letter' => 'K', 'cost' => 2, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 2]],
        50 => ['genre' => HORROR, 'letter' => 'L', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [INK => true]],
        51 => ['genre' => HORROR, 'letter' => 'M', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        52 => ['genre' => HORROR, 'letter' => 'N', 'cost' => 5, 'basicBenefits' => [COINS => 2, INK => true], 'genreBenefits' => [COINS => 1]],
        53 => ['genre' => HORROR, 'letter' => 'N', 'cost' => 6, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [POINTS => 2, INK => true]],
        54 => ['genre' => HORROR, 'letter' => 'O', 'cost' => 4, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [EITHER_GENRE => 1]],
        55 => ['genre' => HORROR, 'letter' => 'P', 'cost' => 3, 'basicBenefits' => [POINTS => 2, INK => true], 'genreBenefits' => []],
        56 => ['genre' => HORROR, 'letter' => 'Q', 'cost' => 4, 'basicBenefits' => [COINS => 3], 'genreBenefits' => [COINS => 1, INK => true]],
        57 => ['genre' => HORROR, 'letter' => 'R', 'cost' => 4, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 2, INK => true]],
        58 => ['genre' => HORROR, 'letter' => 'S', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1]],
        59 => ['genre' => HORROR, 'letter' => 'S', 'cost' => 7, 'basicBenefits' => [POINTS => 3, INK => true], 'genreBenefits' => [POINTS => 1]],
        60 => ['genre' => HORROR, 'letter' => 'T', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, INK => true]],
        61 => ['genre' => HORROR, 'letter' => 'U', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        62 => ['genre' => HORROR, 'letter' => 'U', 'cost' => 6, 'basicBenefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 1]],
        63 => ['genre' => HORROR, 'letter' => 'V', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 2, INK => true]],
        64 => ['genre' => HORROR, 'letter' => 'V', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, INK => true]],
        65 => ['genre' => HORROR, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [POINTS => 2, INK => true], 'genreBenefits' => [POINTS => 2]],
        66 => ['genre' => HORROR, 'letter' => 'W', 'cost' => 5, 'basicBenefits' => [COINS => 2, INK => true], 'genreBenefits' => [COINS => 3]],
        67 => ['genre' => HORROR, 'letter' => 'X', 'cost' => 2, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [POINTS => 2]],
        68 => ['genre' => HORROR, 'letter' => 'X', 'cost' => 6, 'basicBenefits' => [POINTS => 3, INK => true], 'genreBenefits' => [POINTS => 3]],
        69 => ['genre' => HORROR, 'letter' => 'Y', 'cost' => 3, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, INK => true]],
        70 => ['genre' => HORROR, 'letter' => 'Z', 'cost' => 3, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [EITHER_GENRE => 1, INK => true]],

        71 => ['genre' => ROMANCE, 'letter' => 'A', 'cost' => 4, 'basicBenefits' => [COINS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
        72 => ['genre' => ROMANCE, 'letter' => 'B', 'cost' => 3, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [DOUBLE_ADJ => true]],
        73 => ['genre' => ROMANCE, 'letter' => 'B', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        74 => ['genre' => ROMANCE, 'letter' => 'C', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [TRASH_DISCARD => 1]],
        75 => ['genre' => ROMANCE, 'letter' => 'D', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [DOUBLE_ADJ => true]],
        76 => ['genre' => ROMANCE, 'letter' => 'E', 'cost' => 2, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1]],
        77 => ['genre' => ROMANCE, 'letter' => 'E', 'cost' => 6, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [TRASH_DISCARD => 1]],
        78 => ['genre' => ROMANCE, 'letter' => 'F', 'cost' => 4, 'basicBenefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 1]],
        79 => ['genre' => ROMANCE, 'letter' => 'F', 'cost' => 6, 'basicBenefits' => [COINS => 2, DOUBLE_ADJ => true], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        80 => ['genre' => ROMANCE, 'letter' => 'G', 'cost' => 3, 'basicBenefits' => [POINTS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 1]],
        81 => ['genre' => ROMANCE, 'letter' => 'H', 'cost' => 3, 'basicBenefits' => [COINS => 1, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
        82 => ['genre' => ROMANCE, 'letter' => 'H', 'cost' => 7, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, DOUBLE_ADJ => true]],
        83 => ['genre' => ROMANCE, 'letter' => 'I', 'cost' => 2, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1]],
        84 => ['genre' => ROMANCE, 'letter' => 'J', 'cost' => 6, 'basicBenefits' => [COINS => 2, DOUBLE_ADJ => true], 'genreBenefits' => [COINS => 2, TRASH_DISCARD => 1]],
        85 => ['genre' => ROMANCE, 'letter' => 'K', 'cost' => 3, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        86 => ['genre' => ROMANCE, 'letter' => 'K', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        87 => ['genre' => ROMANCE, 'letter' => 'L', 'cost' => 8, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, DOUBLE_ADJ => true]],
        88 => ['genre' => ROMANCE, 'letter' => 'M', 'cost' => 2, 'basicBenefits' => [COINS => 1, TRASH_DISCARD => 1], 'genreBenefits' => []],
        89 => ['genre' => ROMANCE, 'letter' => 'N', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [TRASH_DISCARD => 1]],
        90 => ['genre' => ROMANCE, 'letter' => 'N', 'cost' => 5, 'basicBenefits' => [COINS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [COINS => 1]],
        91 => ['genre' => ROMANCE, 'letter' => 'O', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [DOUBLE_ADJ => true]],
        92 => ['genre' => ROMANCE, 'letter' => 'O', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [COINS => 2, POINTS => 1], 'genreBenefits' => [COINS => 1, POINTS => 1]],
        93 => ['genre' => ROMANCE, 'letter' => 'P', 'cost' => 6, 'basicBenefits' => [POINTS => 2, DOUBLE_ADJ => true], 'genreBenefits' => [POINTS => 1]],
        94 => ['genre' => ROMANCE, 'letter' => 'Q', 'cost' => 4, 'basicBenefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
        95 => ['genre' => ROMANCE, 'letter' => 'R', 'cost' => 5, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, SPECIAL_ROMANCE => true]],
        96 => ['genre' => ROMANCE, 'letter' => 'R', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [TRASH_DISCARD => 1]],
        97 => ['genre' => ROMANCE, 'letter' => 'S', 'cost' => 4, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1, DOUBLE_ADJ => true]],
        98 => ['genre' => ROMANCE, 'letter' => 'T', 'cost' => 3, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, TRASH_DISCARD => 1]],
        99 => ['genre' => ROMANCE, 'letter' => 'U', 'cost' => 9, 'basicBenefits' => [POINTS => 5], 'genreBenefits' => [POINTS => 1, DOUBLE_ADJ => true]],
        100 => ['genre' => ROMANCE, 'letter' => 'V', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, TRASH_DISCARD => 1]],
        101 => ['genre' => ROMANCE, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [POINTS => 1, DOUBLE_ADJ => true], 'genreBenefits' => [POINTS => 1]],
        102 => ['genre' => ROMANCE, 'letter' => 'X', 'cost' => 7, 'basicBenefits' => [POINTS => 4, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
        103 => ['genre' => ROMANCE, 'letter' => 'Y', 'cost' => 4, 'basicBenefits' => [POINTS => 1, DOUBLE_ADJ => true], 'genreBenefits' => [TRASH_DISCARD => 1]],
        104 => ['genre' => ROMANCE, 'letter' => 'Z', 'cost' => 4, 'basicBenefits' => [POINTS => 2, TRASH_DISCARD => 1], 'genreBenefits' => [POINTS => 2]],
        105 => ['genre' => ROMANCE, 'letter' => 'Z', 'cost' => 5, 'basicBenefits' => [POINTS => 2, DOUBLE_ADJ => true], 'genreBenefits' => [COINS => 2]],

        106 => ['genre' => MYSTERY, 'letter' => 'A', 'cost' => 3, 'basicBenefits' => [POINTS => 1, UNCOVER_ADJ => true], 'genreBenefits' => []],
        107 => ['genre' => MYSTERY, 'letter' => 'A', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1]],
        108 => ['genre' => MYSTERY, 'letter' => 'B', 'cost' => 4, 'basicBenefits' => [COINS => 2, JAIL => true], 'genreBenefits' => [COINS => 2]],
        109 => ['genre' => MYSTERY, 'letter' => 'C', 'cost' => 5, 'basicBenefits' => [POINTS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [JAIL => true]],
        110 => ['genre' => MYSTERY, 'letter' => 'D', 'cost' => 4, 'basicBenefits' => [POINTS => 1, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 2]],
        111 => ['genre' => MYSTERY, 'letter' => 'E', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [UNCOVER_ADJ => true]],
        112 => ['genre' => MYSTERY, 'letter' => 'F', 'cost' => 2, 'basicBenefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 1]],
        113 => ['genre' => MYSTERY, 'letter' => 'F', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 1]],
        114 => ['genre' => MYSTERY, 'letter' => 'G', 'cost' => 6, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, UNCOVER_ADJ => true]],
        115 => ['genre' => MYSTERY, 'letter' => 'H', 'cost' => 3, 'basicBenefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [POINTS => 2]],
        116 => ['genre' => MYSTERY, 'letter' => 'I', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [JAIL => true]],
        117 => ['genre' => MYSTERY, 'letter' => 'I', 'cost' => 5, 'basicBenefits' => [COINS => 2, UNCOVER_ADJ => true], 'genreBenefits' => []],
        118 => ['genre' => MYSTERY, 'letter' => 'J', 'cost' => 8, 'basicBenefits' => [POINTS => 5, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 2]],
        119 => ['genre' => MYSTERY, 'letter' => 'K', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1, UNCOVER_ADJ => true]],
        120 => ['genre' => MYSTERY, 'letter' => 'K', 'cost' => 4, 'basicBenefits' => [COINS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [COINS => 2]],
        121 => ['genre' => MYSTERY, 'letter' => 'L', 'cost' => 6, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1, UNCOVER_ADJ => true]],
        122 => ['genre' => MYSTERY, 'letter' => 'M', 'cost' => 3, 'basicBenefits' => [COINS => 1, UNCOVER_ADJ => true], 'genreBenefits' => [COINS => 1]],
        123 => ['genre' => MYSTERY, 'letter' => 'M', 'cost' => 4, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, JAIL => true]],
        124 => ['genre' => MYSTERY, 'letter' => 'N', 'cost' => 7, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 1, SPECIAL_MYSTERY => true]],
        125 => ['genre' => MYSTERY, 'letter' => 'O', 'cost' => 3, 'basicBenefits' => [POINTS => 1, JAIL => true], 'genreBenefits' => [UNCOVER_ADJ => true]],
        126 => ['genre' => MYSTERY, 'letter' => 'P', 'cost' => 2, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, JAIL => true]],
        127 => ['genre' => MYSTERY, 'letter' => 'P', 'cost' => 4, 'basicBenefits' => [POINTS => 1, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 2]],
        128 => ['genre' => MYSTERY, 'letter' => 'Q', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, UNCOVER_ADJ => true]],
        129 => ['genre' => MYSTERY, 'letter' => 'Q', 'cost' => 5, 'basicBenefits' => [POINTS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 3]],
        130 => ['genre' => MYSTERY, 'letter' => 'R', 'cost' => 4, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 2, UNCOVER_ADJ => true]],
        131 => ['genre' => MYSTERY, 'letter' => 'R', 'cost' => 6, 'basicBenefits' => [POINTS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 1, JAIL => true]],
        132 => ['genre' => MYSTERY, 'letter' => 'S', 'cost' => 4, 'basicBenefits' => [COINS => 1, JAIL => true], 'genreBenefits' => [COINS => 2]],
        133 => ['genre' => MYSTERY, 'letter' => 'T', 'cost' => 6, 'basicBenefits' => [POINTS => 2, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 2]],
        134 => ['genre' => MYSTERY, 'letter' => 'T', 'cost' => 8, 'timeless' => true, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, JAIL => true]],
        135 => ['genre' => MYSTERY, 'letter' => 'U', 'cost' => 2, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, UNCOVER_ADJ => true]],
        136 => ['genre' => MYSTERY, 'letter' => 'V', 'cost' => 9, 'basicBenefits' => [POINTS => 4, UNCOVER_ADJ => true], 'genreBenefits' => [POINTS => 4]],
        137 => ['genre' => MYSTERY, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 2, UNCOVER_ADJ => true]],
        138 => ['genre' => MYSTERY, 'letter' => 'X', 'cost' => 3, 'basicBenefits' => [POINTS => 3, JAIL => true], 'genreBenefits' => []],
        139 => ['genre' => MYSTERY, 'letter' => 'Y', 'cost' => 7, 'basicBenefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 2, UNCOVER_ADJ => true]],
        140 => ['genre' => MYSTERY, 'letter' => 'Z', 'cost' => 5, 'basicBenefits' => [POINTS => 3], 'genreBenefits' => [POINTS => 2, UNCOVER_ADJ => true]],

        141 => ['genre' => STARTER, 'letter' => 'A', 'cost' => 0, 'basicBenefits' => [COINS => 1], 'genreBenefits' => []],
        142 => ['genre' => STARTER, 'letter' => 'B', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        143 => ['genre' => STARTER, 'letter' => 'C', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        144 => ['genre' => STARTER, 'letter' => 'D', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        145 => ['genre' => STARTER, 'letter' => 'E', 'cost' => 0, 'basicBenefits' => [COINS => 1], 'genreBenefits' => []],
        146 => ['genre' => STARTER, 'letter' => 'G', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        147 => ['genre' => STARTER, 'letter' => 'H', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        148 => ['genre' => STARTER, 'letter' => 'I', 'cost' => 0, 'basicBenefits' => [COINS => 1], 'genreBenefits' => []],
        149 => ['genre' => STARTER, 'letter' => 'L', 'cost' => 0, 'basicBenefits' => [COINS => 1], 'genreBenefits' => []],
        150 => ['genre' => STARTER, 'letter' => 'M', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        151 => ['genre' => STARTER, 'letter' => 'N', 'cost' => 0, 'basicBenefits' => [COINS => 1], 'genreBenefits' => []],
        152 => ['genre' => STARTER, 'letter' => 'O', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        153 => ['genre' => STARTER, 'letter' => 'P', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        154 => ['genre' => STARTER, 'letter' => 'R', 'cost' => 0, 'basicBenefits' => [COINS => 1], 'genreBenefits' => []],
        155 => ['genre' => STARTER, 'letter' => 'S', 'cost' => 0, 'basicBenefits' => [COINS => 1], 'genreBenefits' => []],
        156 => ['genre' => STARTER, 'letter' => 'T', 'cost' => 0, 'basicBenefits' => [COINS => 1], 'genreBenefits' => []],
        157 => ['genre' => STARTER, 'letter' => 'U', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
        158 => ['genre' => STARTER, 'letter' => 'Y', 'cost' => 0, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => []],
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

    private static function populateCards(array $dbcards): array
    {
        $cards = array_map(function ($dbcard) {
            return new HCard($dbcard);
        }, $dbcards);
        $count = count($cards);
        $keys = array_keys($cards);
        $i = 0;
        foreach ($cards as &$card) {
            if ($i > 0) {
                $prev = $keys[$i - 1];
                $card->setPrevious($cards[$prev]);
            }
            if ($i < $count - 1) {
                $next = $keys[$i + 1];
                $card->setNext($cards[$next]);
            }
            $i++;
        }
        return $cards;
    }

    public static function getCardRef(): array
    {
        return self::$refCards;
    }

    public static function getBenefitRef(): array
    {
        return self::$refBenefits;
    }

    /* Change */

    public static function setup(): void
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
        self::drawCards(7, 'deck', 'offer', 'letter');
    }

    public static function updateOrigin(): void
    {
        self::DbQuery("UPDATE card SET card_type = card_location");
    }

    public static function drawCards(int $count, string $fromLocation, string $toLocation, string $sort = null, bool $notify = false): array
    {
        // Draw cards
        $order = self::getNextOrderInLocation($toLocation);
        $dbcards = self::$cards->pickCardsForLocation($count, $fromLocation, $toLocation);
        $ids = self::getIds($dbcards);

        // Populate from database and sort
        $cards = self::getCards($ids);
        if (count($cards) > 1) {
            if ($sort == 'letter') {
                uasort($cards, function ($a, $b) {
                    $result = strcmp($a->getLetter(), $b->getLetter());
                    if ($result == 0) {
                        $result = $a->getId() - $b->getId();
                    }
                    return $result;
                });
            } else if ($sort == 'cost') {
                uasort($cards, function ($a, $b) {
                    $result = $a->getCost() - $b->getCost();
                    if ($result == 0) {
                        $result = strcmp($a->getLetter(), $b->getLetter());
                    }
                    if ($result == 0) {
                        $result = $a->getId() - $b->getId();
                    }
                    return $result;
                });
            }
        }

        // Reposition at the end and update origin
        foreach ($cards as &$card) {
            self::DbQuery("UPDATE card SET card_type = card_location, card_location_arg = $order WHERE card_id = {$card->getId()}");
            $card->setOrigin($toLocation);
            $card->setOrder($order);
            $order++;
        }
        if ($notify) {
            self::notifyCards($cards);
        }
        return $cards;
    }

    /*
    public static function positionCard(&$card, $location = null, $order = null)
    {
        if ($location == null) {
            $location = $card->getLocation();
        }
        if ($order == null) {
            $order = self::getCountInLocation($location);
        }
        self::DbQuery("UPDATE card SET card_location_arg = card_location_arg - 1 WHERE card_location = '{$card->getLocation()}' AND card_location_arg > {$card->getOrder()}");
        self::DbQuery("UPDATE card SET card_location_arg = card_location_arg + 1 WHERE card_location = '$location' AND card_location_arg >= $order");
        self::DbQuery("UPDATE card SET card_location = '$location', card_location_arg = $order WHERE card_id = {$card->getId()}");
        $card->setLocation($location);
        $card->setOrder($order);
    }

    public static function positionCards($cardIds, $location, $order = null)
    {
        if ($order = null) {
            $order = self::getCountInLocation($location);
        }
        foreach ($cardIds as $cardId) {
            self::positionCard($cardId, $location, $order++);
        }
    }
*/

    /* Query (generic) */

    public static function getCard(int $cardId): HCard
    {
        $cards = self::getCards([$cardId]);
        return array_shift($cards);
    }

    public static function getCards($cardIds, string $wildMask = null): array
    {
        $cardIds = self::getIds($cardIds);
        if (empty($cardIds)) {
            return [];
        }
        $sql = "SELECT card.*, JSON_ARRAYAGG(resolve.benefit_id) AS resolve FROM card LEFT OUTER JOIN resolve USING (card_id) WHERE card_id IN (" . implode(',', $cardIds) . ") GROUP BY card.card_id";
        $dbcards = self::getCollectionFromDB($sql);
        $cards = self::populateCards($dbcards);

        $wilds = [];
        if ($wildMask) {
            $countCards = count($cards);
            $countWilds = strlen($wildMask);
            if ($countCards != $countWilds) {
                throw new BgaVisibleSystemException("Invalid wildcard mask (cards: $countCards, wilds: $countWilds)");
            }
            $wilds = array_combine($cardIds, str_split($wildMask));
        }

        // Reorder to match input
        $output = [];
        foreach ($cardIds as $cardId) {
            if (array_key_exists($cardId, $cards)) {
                $card = $cards[$cardId];
                if (isset($wilds[$cardId]) && $wilds[$cardId] != '_') {
                    $card->setWild($wilds[$cardId]);
                }
                $output[$cardId] = $card;
            }
        }
        return $output;
    }

    private static function getLocationWhereClause($locations): string
    {
        $parts = [];
        $in = [];
        if (!is_array($locations)) {
            $locations = [$locations];
        }
        foreach ($locations as $location) {
            if (substr($location, -1) == '%') {
                $parts[] = "card_location LIKE '" . $locations[0] . "'";
            } else {
                $in[] = $location;
            }
        }
        if (!empty($in)) {
            $parts[] = "card_location IN ('" . implode("', '", $in) . "')";
        }
        return "(" . implode(" OR ", $parts) . ")";
    }

    public static function getCardsInLocation($locations, int $inkValue = null): array
    {
        $sql = "SELECT card_id FROM card WHERE " . self::getLocationWhereClause($locations);
        if ($inkValue != null) {
            $sql .= " AND ink = $inkValue";
        }
        $sql .= " ORDER BY card_location, card_location_arg";
        $cardIds = self::getObjectListFromDB($sql, true);
        return self::getCards($cardIds);
    }

    public static function getCountInLocation(string $location): int
    {
        return intval(self::$cards->countCardInLocation($location));
    }

    public static function getNextOrderInLocation(string $location): int
    {
        $max = self::getUniqueValueFromDB("SELECT COALESCE(MAX(card_location_arg), 0) FROM card WHERE card_location = '$location'");
        return intval($max) + 1;
    }

    public static function getHandLocation(int $playerId): string
    {
        return "hand_$playerId";
    }

    public static function getDeckLocation(int $playerId): string
    {
        return "deck_$playerId";
    }

    public static function getDiscardLocation(int $playerId): string
    {
        return "discard_$playerId";
    }

    public static function getTimelessLocation(int $playerId): string
    {
        return "timeless_$playerId";
    }

    /* Query (specific) */

    public static function getHand(int $playerId, int $inkValue = null): array
    {
        return self::getCardsInLocation(self::getHandLocation($playerId), $inkValue);
    }

    public static function getHandCount(int $playerId): int
    {
        return self::getCountInLocation(self::getHandLocation($playerId));
    }

    public static function getDeckCount(int $playerId): int
    {
        return self::getCountInLocation(self::getDeckLocation($playerId));
    }

    public static function getDiscard(int $playerId): array
    {
        return self::getCardsInLocation(self::getDiscardLocation($playerId));
    }

    public static function getDiscardCount(int $playerId): int
    {
        return self::getCountInLocation(self::getDiscardLocation($playerId));
    }

    public static function getTableau(): array
    {
        return self::getCardsInLocation('tableau');
    }

    public static function getTimeless(): array
    {
        return self::getCardsInLocation('timeless%');
    }

    public static function getOffer(): array
    {
        return self::getCardsInLocation('offer');
    }

    public static function getOfferDeckCount(): int
    {
        return self::getCountInLocation('deck');
    }

    /* Array utilties */

    public static function getIds($cards): array
    {
        $ids = [];
        if (!is_array($cards)) {
            $cards = [$cards];
        }
        foreach ($cards as $c) {
            if ($c instanceof HCard) {
                $ids[] = $c->getId();
            } else if (is_numeric($c)) {
                $ids[] = $c;
            } else if (is_array($c)) {
                $ids[] = intval($c['id']);
            }
        }
        return $ids;
    }

    public static function getString($cards): string
    {
        $string = '';
        if (is_array($cards) && !empty($cards)) {
            $string = implode(', ', array_map(function ($card) {
                return (string) $card;
            }, $cards));
        }
        return $string;
    }

    public static function getGenreCounts($cards): array
    {
        $counts = [];
        if (is_array($cards) && !empty($cards)) {
            $cards = array_filter($cards, function ($card) {
                return !$card->isWild();
            });
            $counts = array_count_values(array_map(function ($card) {
                return $card->getGenre();
            }, $cards));
        }
        return $counts + [
            STARTER => 0,
            ADVENTURE => 0,
            HORROR => 0,
            ROMANCE => 0,
            MYSTERY => 0
        ];
    }

    /* Notify utilities */
    private static function notifyCards($cards): void
    {
        if ($cards instanceof HCard) {
            $cards = [
                $cards->getId() => $cards
            ];
        }
        hardback::$instance->notifyAllPlayers('cards', '', [
            'cards' => $cards,
        ]);
    }

    /* Change (specific) */

    public static function playWord(int $playerId, array $cards): void
    {
        $updatedIds = self::getIds(self::getCardsInLocation(self::getHandLocation($playerId)));

        // Clear ink and remover
        self::DbQuery("UPDATE card SET ink = NULL WHERE ink IS NOT NULL AND card_id IN (" . implode(',', $updatedIds) . ")");

        // Discard unused cards
        self::DbQuery("UPDATE card SET card_location = '" . self::getDiscardLocation($playerId) . "' WHERE card_id IN (" . implode(',', $updatedIds) . ")");

        // Update tableau
        $ids = self::getIds($cards);
        $remainder = array_filter(self::getTableau(), function ($card) use ($ids) {
            return !in_array($card->getId(), $ids);
        });
        if (!empty($remainder)) {
            throw new BgaVisibleSystemException("Tableau contains unexpected cards: " . self::getString($cards));
        }

        $order = 0;
        foreach ($cards as $card) {
            $sql = "UPDATE card SET card_location = 'tableau', card_location_arg = $order";
            if ($card->isWild()) {
                $sql .= ", wild = '{$card->getLetter()}'";
            }
            $sql .= " WHERE card_id = {$card->getId()}";
            self::DbQuery($sql);
            $order++;
        }

        // Compute active genres
        foreach (self::getGenreCounts($cards) as $genre => $count) {
            hardback::$instance->setGameStateValue("count$genre", $count);
        }

        // Notify
        self::notifyCards(self::getCards($updatedIds));
    }

    public static function isGenreActive(int $genre): bool
    {
        return hardback::$instance->getGameStateValue("count$genre") >= 2;
    }

    public static function moveCards($cards, string $location): void
    {
        $updatedIds = self::getIds($cards);
        self::DbQuery("UPDATE card SET card_location = '$location', card_location_arg = -1 WHERE card_id IN (" . implode(',', $updatedIds) . ")");

        // Notify
        self::notifyCards(self::getCards($updatedIds));
    }

    public static function reset(int $playerId): void
    {
        $cards = self::getCardsInLocation([self::getHandLocation($playerId), 'tableau']);
        $updatedIds = self::getIds($cards);

        // Timeless classics
        $timeless = array_filter($cards, function (HCard $card) {
            return $card->isTimeless() && !$card->isWild() && $card->isLocation('tableau');
        });
        if (!empty($timeless)) {
            $discardIds = [];
            $keepIds = [];
            foreach ($timeless as $card) {
                // Discard if owned by another player
                if ($card->isOrigin('timeless') && !$card->isOrigin(self::getTimelessLocation($playerId))) {
                    $discardIds[] = $card->getId();
                } else {
                    $keepIds[] = $card->getId();
                }
            }
            if (!empty($discardIds)) {
                // Return to owner's discard
                self::DbQuery("UPDATE card SET factor = NULL, ink = NULL, card_type = CONCAT('discard_', card_location_arg), card_location = CONCAT('discard_', card_location_arg), card_location_arg = -1 WHERE card_id IN (" . implode(',', $discardIds) . ")");
            }
            if (!empty($keepIds)) {
                self::DbQuery("UPDATE card SET factor = NULL, ink = NULL, card_type = '" . self::getTimelessLocation($playerId) . "', card_location = '" . self::getTimelessLocation($playerId) . "', card_location_arg = -1 WHERE card_id IN (" . implode(',', $keepIds) . ")");
            }
        }

        // Discard hand and tableau
        self::DbQuery("UPDATE card SET factor = NULL, ink = NULL, wild = NULL, card_type = '" . self::getDiscardLocation($playerId) . "', card_location = '" . self::getDiscardLocation($playerId) . "', card_location_arg = -1 WHERE card_id IN (" . implode(',', $updatedIds) . ")");

        // Clear used benefits
        self::DbQuery("TRUNCATE resolve");

        // Draw new hand
        $newCards = self::drawCards(5, self::getDeckLocation($playerId), self::getHandLocation($playerId), 'letter');

        // Notify
        self::notifyCards(self::getCards($updatedIds) + $newCards);
    }

    public static function canFlushOffer(): bool
    {
        $offer = self::getOffer();

        $costCondition = count(array_filter($offer, function ($card) {
            return $card->getCost() >= 6;
        }));
        if ($costCondition >= 4) {
            return true;
        }

        $genreCondition = max(array_values(self::getGenreCounts($offer)));
        if ($genreCondition >= 4) {
            return true;
        }

        return false;
    }

    public static function flushOffer(): void
    {
        $updatedIds = self::getIds(self::getCardsInLocation('offer'));

        // Discard offer and notify
        self::DbQuery("UPDATE card SET card_location = 'discard', card_location_arg = -1, card_type = 'discard' WHERE card_id IN (" . implode(',', $updatedIds) . ")");
        self::notifyCards(self::getCards($updatedIds));

        // Draw new offer and notify
        $newCards = self::drawCards(7, 'deck', 'offer', 'letter');
        self::notifyCards($newCards);
    }

    public static function inkCards(array &$cards, int $inkValue = HAS_INK): void
    {
        $updatedIds = self::getIds($cards);
        $sql = "UPDATE card SET ink = $inkValue WHERE card_id IN (" . implode(',', $updatedIds) . ")";
        self::DbQuery($sql);
        foreach ($cards as &$card) {
            $card->setInk($inkValue);
            if ($inkValue == HAS_INK) {
                $card->setOrigin($card->getLocation());
            }
        }

        // Notify
        self::notifyCards($cards);
    }

    public static function uncover(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, UNCOVER_ADJ);
        self::DbQuery("UPDATE card SET wild = NULL WHERE card_id = {$card->getId()}");
        $card->setWild(null);

        // Notify
        self::notifyCards($card);
    }

    public static function useBenefit($cards, int $benefit): void
    {
        $cardIds = self::getIds($cards);
        foreach ($cardIds as $cardId) {
            self::DbQuery("INSERT INTO resolve VALUES ($cardId, $benefit)");
        }
    }
}

CardMgr::__constructStatic();
