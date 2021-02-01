<?php

class CardMgr extends APP_GameClass
{
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
        EITHER_INK => [
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
            'icon' => 'star',
        ],
        UNCOVER_ADJ => [
            'text' => 'Uncover adjacent wild',
        ],
    ];

    // Timeless: UPDATE card SET `location` = 'offer', `origin` = 'offer' WHERE `location` IN ('deck', 'discard') AND `refId` IN (5, 12, 23, 31, 40, 42, 53, 64, 73, 86, 92, 96, 107, 113, 123, 134)

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
        37 => ['genre' => HORROR, 'letter' => 'B', 'cost' => 6, 'basicBenefits' => [COINS => 3], 'genreBenefits' => [COINS => 2, EITHER_INK => true]],
        38 => ['genre' => HORROR, 'letter' => 'C', 'cost' => 5, 'basicBenefits' => [POINTS => 2, EITHER_INK => true], 'genreBenefits' => [POINTS => 1]],
        39 => ['genre' => HORROR, 'letter' => 'C', 'cost' => 8, 'basicBenefits' => [COINS => 2, EITHER_INK => true], 'genreBenefits' => [COINS => 3]],
        40 => ['genre' => HORROR, 'letter' => 'D', 'cost' => 4, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 1, POINTS => 1]],
        41 => ['genre' => HORROR, 'letter' => 'D', 'cost' => 9, 'basicBenefits' => [POINTS => 3, EITHER_INK => true], 'genreBenefits' => [POINTS => 3]],
        42 => ['genre' => HORROR, 'letter' => 'E', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [POINTS => 1]],
        43 => ['genre' => HORROR, 'letter' => 'E', 'cost' => 8, 'basicBenefits' => [COINS => 2, EITHER_INK => true], 'genreBenefits' => [EITHER_GENRE => 2]],
        44 => ['genre' => HORROR, 'letter' => 'F', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [EITHER_GENRE => 2, EITHER_INK => true]],
        45 => ['genre' => HORROR, 'letter' => 'G', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 2, EITHER_INK => true]],
        46 => ['genre' => HORROR, 'letter' => 'H', 'cost' => 7, 'basicBenefits' => [COINS => 2, POINTS => 1], 'genreBenefits' => [COINS => 1, POINTS => 2, SPECIAL_HORROR => true]],
        47 => ['genre' => HORROR, 'letter' => 'I', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [EITHER_GENRE => 2]],
        48 => ['genre' => HORROR, 'letter' => 'J', 'cost' => 5, 'basicBenefits' => [POINTS => 3, EITHER_INK => true], 'genreBenefits' => [POINTS => 2]],
        49 => ['genre' => HORROR, 'letter' => 'K', 'cost' => 2, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 2]],
        50 => ['genre' => HORROR, 'letter' => 'L', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [EITHER_INK => true]],
        51 => ['genre' => HORROR, 'letter' => 'M', 'cost' => 3, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1]],
        52 => ['genre' => HORROR, 'letter' => 'N', 'cost' => 5, 'basicBenefits' => [COINS => 2, EITHER_INK => true], 'genreBenefits' => [COINS => 1]],
        53 => ['genre' => HORROR, 'letter' => 'N', 'cost' => 6, 'timeless' => true, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [POINTS => 2, EITHER_INK => true]],
        54 => ['genre' => HORROR, 'letter' => 'O', 'cost' => 4, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [EITHER_GENRE => 1]],
        55 => ['genre' => HORROR, 'letter' => 'P', 'cost' => 3, 'basicBenefits' => [POINTS => 2, EITHER_INK => true], 'genreBenefits' => []],
        56 => ['genre' => HORROR, 'letter' => 'Q', 'cost' => 4, 'basicBenefits' => [COINS => 3], 'genreBenefits' => [COINS => 1, EITHER_INK => true]],
        57 => ['genre' => HORROR, 'letter' => 'R', 'cost' => 4, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [COINS => 2, EITHER_INK => true]],
        58 => ['genre' => HORROR, 'letter' => 'S', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 1]],
        59 => ['genre' => HORROR, 'letter' => 'S', 'cost' => 7, 'basicBenefits' => [POINTS => 3, EITHER_INK => true], 'genreBenefits' => [POINTS => 1]],
        60 => ['genre' => HORROR, 'letter' => 'T', 'cost' => 4, 'basicBenefits' => [POINTS => 2], 'genreBenefits' => [POINTS => 1, EITHER_INK => true]],
        61 => ['genre' => HORROR, 'letter' => 'U', 'cost' => 2, 'basicBenefits' => [POINTS => 1], 'genreBenefits' => [POINTS => 2]],
        62 => ['genre' => HORROR, 'letter' => 'U', 'cost' => 6, 'basicBenefits' => [POINTS => 4], 'genreBenefits' => [POINTS => 1]],
        63 => ['genre' => HORROR, 'letter' => 'V', 'cost' => 4, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 2, EITHER_INK => true]],
        64 => ['genre' => HORROR, 'letter' => 'V', 'cost' => 5, 'timeless' => true, 'basicBenefits' => [COINS => 1], 'genreBenefits' => [COINS => 1, EITHER_INK => true]],
        65 => ['genre' => HORROR, 'letter' => 'W', 'cost' => 4, 'basicBenefits' => [POINTS => 2, EITHER_INK => true], 'genreBenefits' => [POINTS => 2]],
        66 => ['genre' => HORROR, 'letter' => 'W', 'cost' => 5, 'basicBenefits' => [COINS => 2, EITHER_INK => true], 'genreBenefits' => [COINS => 3]],
        67 => ['genre' => HORROR, 'letter' => 'X', 'cost' => 2, 'basicBenefits' => [EITHER_BASIC => 1], 'genreBenefits' => [POINTS => 2]],
        68 => ['genre' => HORROR, 'letter' => 'X', 'cost' => 6, 'basicBenefits' => [POINTS => 3, EITHER_INK => true], 'genreBenefits' => [POINTS => 3]],
        69 => ['genre' => HORROR, 'letter' => 'Y', 'cost' => 3, 'basicBenefits' => [COINS => 2], 'genreBenefits' => [COINS => 1, EITHER_INK => true]],
        70 => ['genre' => HORROR, 'letter' => 'Z', 'cost' => 3, 'basicBenefits' => [EITHER_BASIC => 2], 'genreBenefits' => [EITHER_GENRE => 1, EITHER_INK => true]],

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

    private static function populateCards(array $dbcards): array
    {
        $cards = array_map(function ($dbcard) {
            return new HCard($dbcard);
        }, $dbcards);
        $sequence = array_values($cards);
        $count = count($cards);
        $i = 0;
        foreach ($cards as &$card) {
            if ($i > 0) {
                $card->setPrevious($sequence[$i - 1]);
            }
            if ($i < $count - 1) {
                $card->setNext($sequence[$i + 1]);
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
        // Create genre cards
        // Deal 7 to the offer row
        $create = [];
        foreach (self::$refCards as $refId => $ref) {
            if ($ref['genre'] != STARTER) {
                //$create[] = ['type' => '', 'type_arg' => $refId, 'nbr' => 1];
                $create[] = "($refId, 'discard', 'discard')";
            }
        }
        $sql = "INSERT INTO card (`refId`, `location`, `origin`) VALUES " . implode(', ', $create);
        self::DbQuery($sql);
        self::drawCards(7, 'deck', 'offer');

        // Create starter cards
        // Deal 5 to each player
        $starterCoins = [];
        $starterPoints = [];
        foreach (self::$refCards as $refId => $ref) {
            if ($ref['genre'] == STARTER) {
                if (array_key_exists(COINS, $ref['basicBenefits'])) {
                    $starterCoins[] = $refId;
                } else {
                    $starterPoints[] = $refId;
                }
            }
        }
        shuffle($starterPoints);
        $playerIds = PlayerMgr::getPlayerIds();
        foreach ($playerIds as $playerId) {
            $location = self::getDiscardLocation($playerId);
            $starter = array_merge($starterCoins, array_splice($starterPoints, 0, 2));
            $create = [];
            foreach ($starter as $refId) {
                $create[] = "($refId, '$location', '$location')";
            }
            $sql = "INSERT INTO card (`refId`, `location`, `origin`) VALUES " . implode(', ', $create);
            self::DbQuery($sql);
            self::drawCards(5, self::getDeckLocation($playerId), self::getHandLocation($playerId), 'letter');
        }
    }

    public static function updateOrigin(): void
    {
        self::DbQuery("UPDATE card SET `origin` = `location`");
    }

    public static function drawCards(int $count, string $fromLocation, string $toLocation, string $sort = null, bool $notify = false): array
    {
        $order = self::getMaxOrderInLocation($toLocation) + 1;

        // Draw cards from deck
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($fromLocation) . " ORDER BY `location`, `order` LIMIT $count";
        $ids = self::getObjectListFromDB($sql, true);

        $missing = $count - count($ids);
        if ($missing > 0) {
            // Rehuffle and continue drawing
            $shuffleIds = self::reshuffleDeck($fromLocation);
            $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($fromLocation);
            if (!empty($ids)) {
                $sql .= " AND `id` NOT IN (" . implode(',', $ids) . ")";
            }
            $sql .= " ORDER BY `location`, `order` LIMIT $missing";
            $ids = array_merge($ids, self::getObjectListFromDB($sql, true));

            // Required to notify reshuffled cards as "unknown"
            $unknownIds = array_values(array_diff($shuffleIds, $ids));
            $unknown = [];
            foreach ($unknownIds as $id) {
                $unknown[intval($id)] = [
                    'id' => intval($id),
                    'location' => 'unknown',
                ];
            }
            self::notifyCards($unknown);
        }

        // Populate from database and sort
        $cards = self::getCards($ids);
        if (count($cards) > 1 && $sort) {
            self::sortCards($cards, $sort);
        }

        // Reposition at the end and update origin
        foreach ($cards as &$card) {
            self::DbQuery("UPDATE card SET `origin` = '$toLocation', `location` = '$toLocation', `order` = $order WHERE `id` = {$card->getId()}");
            $card->setLocation($toLocation);
            $card->setOrigin($toLocation);
            $card->setOrder($order);
            $order++;

            // Count offer row draws for timeless classic discard condition
            if (hardback::$instance->gamestate->table_globals[OPTION_COOP] != NO && $fromLocation == 'deck' && $toLocation == 'offer') {
                hardback::$instance->notifyAllPlayers('message', "countDraw{$card->getGenreName()}", []);
                hardback::$instance->incGameStateValue("countDraw{$card->getGenre()}", 1);
            }
        }
        if ($notify) {
            self::notifyCards($cards);
        }
        return $cards;
    }

    public static function reshuffleDeck(string $location): array
    {
        $shuffleLocation = str_replace('deck', 'discard', $location);
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($shuffleLocation);
        $ids = self::getObjectListFromDB($sql, true);
        shuffle($ids);
        $order = 1;
        foreach ($ids as $id) {
            self::DbQuery("UPDATE card SET `origin` = '$location', `location` = '$location', `order` = $order WHERE `id` = $id");
            $order++;
        }
        return $ids;
    }

    public static function sortCards(array &$cards, string $sort): void
    {
        if ($sort == 'letter') {
            uasort($cards, function ($a, $b) {
                $result = strcmp($a->getLetter(), $b->getLetter());
                if ($result == 0) {
                    $result = $a->getOrder() - $b->getOrder();
                }
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
                    $result = $a->getOrder() - $b->getOrder();
                }
                if ($result == 0) {
                    $result = $a->getId() - $b->getId();
                }
                return $result;
            });
        } else {
            throw new BgaVisibleSystemException("Invalid sort order: $sort");
        }
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
        self::DbQuery("UPDATE card SET order = order - 1 WHERE location = '{$card->getLocation()}' AND order > {$card->getOrder()}");
        self::DbQuery("UPDATE card SET order = order + 1 WHERE location = '$location' AND order >= $order");
        self::DbQuery("UPDATE card SET location = '$location', order = $order WHERE id = {$card->getId()}");
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
        $sql = "SELECT card.*, JSON_ARRAYAGG(resolve.benefit_id) AS resolve FROM card LEFT OUTER JOIN resolve USING (id) WHERE id IN (" . implode(',', $cardIds) . ") GROUP BY card.id";
        $dbcards = self::getCollectionFromDB($sql);

        // Reorder to match input
        $seqcards = [];
        foreach ($cardIds as $cardId) {
            if (array_key_exists($cardId, $dbcards)) {
                $seqcards[$cardId] = $dbcards[$cardId];
            }
        }
        $cards = self::populateCards($seqcards);

        // Apply wild mask
        $wilds = [];
        if ($wildMask) {
            $countCards = count($cards);
            $countWilds = strlen($wildMask);
            if ($countCards != $countWilds) {
                throw new BgaVisibleSystemException("Invalid wildcard mask (cards: $countCards, wilds: $countWilds)");
            }
            $wilds = array_combine($cardIds, str_split($wildMask));
        }
        foreach ($cards as $card) {
            if (isset($wilds[$card->getId()]) && $wilds[$card->getId()] != '_') {
                $card->setWild($wilds[$card->getId()]);
            }
        }

        return $cards;
    }

    private static function getLocationWhereClause($locations): string
    {
        $parts = [];
        $in = [];
        if (!is_array($locations)) {
            $locations = [$locations];
        }
        foreach ($locations as $location) {
            if (strpos($location, '%') !== false) {
                $parts[] = "`location` LIKE '" . $location . "'";
            } else {
                $in[] = $location;
            }
        }
        if (!empty($in)) {
            $parts[] = "`location` IN ('" . implode("', '", $in) . "')";
        }
        return "(" . implode(" OR ", $parts) . ")";
    }

    public static function getCardsInLocation($locations, int $inkValue = null): array
    {
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($locations);
        if ($inkValue != null) {
            $sql .= " AND `ink` = $inkValue";
        }
        $sql .= " ORDER BY `location`, `order`";
        $cardIds = self::getObjectListFromDB($sql, true);
        return self::getCards($cardIds);
    }

    public static function getCardsOwnedByPlayer(int $playerId): array
    {
        $sql = "SELECT `id` FROM card WHERE `origin` LIKE '%_$playerId'";
        $cardIds = self::getObjectListFromDB($sql, true);
        return self::getCards($cardIds);
    }

    public static function getCountInLocation(string $location): int
    {
        $count = self::getUniqueValueFromDB("SELECT COUNT(*) FROM card WHERE `location` = '$location'");
        return intval($count);
    }

    public static function getMaxOrderInLocation(string $location): int
    {
        $max = self::getUniqueValueFromDB("SELECT COALESCE(MAX(`order`), 0) FROM card WHERE `location` = '$location'");
        return intval($max);
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

    public static function getTableau(int $playerId): array
    {
        return self::getCardsInLocation(['tableau', self::getTimelessLocation($playerId)]);
    }

    public static function getTimeless(int $playerId): array
    {
        return self::getCardsInLocation(self::getTimelessLocation($playerId));
    }

    public static function getOffer(int $playerId = null): array
    {
        $cards = self::getCardsInLocation('offer');
        if ($playerId) {
            $jailId = self::getJailId($playerId);
            if ($jailId) {
                $cards[$jailId] = self::getCard($jailId);
            }
        }
        return $cards;
    }

    private static function getJailId(int $playerId): int
    {
        return intval(self::getUniqueValueFromDB("SELECT `id` FROM card WHERE `location` = 'jail' AND `order` = '$playerId'", true));
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

    public static function getGenreCounts($cards, bool $includeWilds = false): array
    {
        $counts = [];
        if (is_array($cards) && !empty($cards)) {
            if (!$includeWilds) {
                $cards = array_filter($cards, function ($card) {
                    return !$card->isWild();
                });
            }
            $counts = array_count_values(array_map(function ($card) {
                return $card->getGenre();
            }, $cards));
        }
        return [
            $counts[STARTER] ?? 0,
            $counts[ADVENTURE] ?? 0,
            $counts[HORROR] ?? 0,
            $counts[MYSTERY] ?? 0,
            $counts[ROMANCE] ?? 0,
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

        // Update tableau
        $ids = self::getIds($cards);
        $remainder = array_filter(self::getCardsInLocation('tableau'), function ($card) use ($ids) {
            return !in_array($card->getId(), $ids);
        });
        if (!empty($remainder)) {
            throw new BgaVisibleSystemException("Tableau contains unexpected cards: " . self::getString($cards));
        }
        $order = 0;
        $opponent = [];
        $tableauIds = [];
        foreach ($cards as $card) {
            $sql = "UPDATE card SET `wild` = " . ($card->isWild() ? "'{$card->getLetter()}'" : "NULL") . ", `location` = 'tableau', `order` = $order WHERE `id` = {$card->getId()}";
            self::DbQuery($sql);
            $order++;
            $tableauIds[] = $card->getId();

            // Cancel all benefits on opponent timeless cards
            if ($card->isOrigin('timeless') && !$card->isOrigin(self::getTimelessLocation($playerId))) {
                self::useBenefit($card, ALL_BENEFITS);
                $opponent[] = $card;
                $updatedIds[] = $card->getId();
            }
        }

        // Discard unused cards
        $discardIds = array_diff($updatedIds, $tableauIds);
        self::discard($discardIds, self::getDiscardLocation($playerId), false);

        // Compute active genres
        // Add own timeless cards
        // Subtract opponent timeless cards
        $counts = self::getGenreCounts($cards);
        $myCounts = self::getGenreCounts(self::getTimeless($playerId));
        $opponentCounts = self::getGenreCounts($opponent);
        foreach ($counts as $genre => $count) {
            if ($genre != STARTER) {
                $val = $count + $myCounts[$genre] - $opponentCounts[$genre];
                hardback::$instance->setGameStateValue("countActive$genre", $val);
            }
        }

        // Notify
        self::notifyCards(self::getCards($updatedIds));
    }

    public static function isGenreActive(int $genre): bool
    {
        return $genre != STARTER && hardback::$instance->getGameStateValue("countActive$genre") >= 2;
    }

    public static function discard($cards, string $location, bool $notify = true): void
    {
        $updatedIds = self::getIds($cards);
        if (!empty($updatedIds)) {
            self::DbQuery("UPDATE card SET `ink` = NULL, `wild` = NULL, `factor` = 1, `origin` = '$location', `location` = '$location', `order` = -1 WHERE `id` IN (" . implode(',', $updatedIds) . ")");

            // Notify
            if ($notify) {
                self::notifyCards(self::getCards($updatedIds));
            }
        }
    }

    public static function reset(int $playerId): void
    {
        $cards = self::getCardsInLocation([self::getHandLocation($playerId), 'tableau']);
        $updatedIds = self::getIds($cards);

        // Clear used benefits
        self::DbQuery('TRUNCATE resolve');

        // Discard Timeless classics
        $timeless = array_filter($cards, function (HCard $card) {
            return $card->isTimeless() && !$card->isWild() && $card->isLocation('tableau');
        });
        if (!empty($timeless)) {
            foreach ($timeless as $card) {
                if (hardback::$instance->gamestate->table_globals[OPTION_COOP] == NO && $card->isOrigin('timeless') && !$card->isOrigin(self::getTimelessLocation($playerId))) {
                    // Discard to owner
                    self::discard($card, self::getDiscardLocation($card->getOwner()), false);
                } else {
                    // Remain in play for owner
                    $owner = $card->getOwner() ?? $playerId;
                    self::discard($card, self::getTimelessLocation($owner), false);
                }
            }
            $timelessIds = self::getIds($timeless);
            self::notifyCards(self::getCards($timelessIds));
            $updatedIds = array_diff($updatedIds, $timelessIds);
        }

        // Discard remaining cards
        self::discard($updatedIds, self::getDiscardLocation($playerId));

        // Draw new hand
        self::drawCards(5, self::getDeckLocation($playerId), self::getHandLocation($playerId), 'letter', true);
    }

    public static function canFlushOffer(): bool
    {
        if (hardback::$instance->gamestate->table_globals[OPTION_COOP] != NO) {
            return false;
        }

        $offer = self::getOffer(); // without jail

        $costCondition = count(array_filter($offer, function ($card) {
            return $card->getCost() >= 6;
        }));
        if ($costCondition >= 4) {
            return true;
        }

        $genreCondition = max(self::getGenreCounts($offer));
        if ($genreCondition >= 4) {
            return true;
        }

        return false;
    }

    public static function flushOffer(): void
    {
        $updatedIds = self::getIds(self::getCardsInLocation('offer'));

        // Discard offer and notify
        self::discard($updatedIds, 'discard');

        // Draw new offer and notify
        $newCards = self::drawCards(7, 'deck', 'offer');
        self::notifyCards($newCards);
    }

    public static function inkCard(HCard $card, int $inkValue = HAS_INK): void
    {
        $sql = "UPDATE card SET `ink` = $inkValue WHERE `id` = {$card->getId()}";
        self::DbQuery($sql);
        $card->setInk($inkValue);

        // Notify
        self::notifyCards($card);
    }

    public static function uncover(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, UNCOVER_ADJ);
        self::DbQuery("UPDATE card SET `wild` = NULL WHERE `id` = {$card->getId()}");
        $card->setWild(null);

        // Notify
        self::notifyCards($card);
    }

    public static function double(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, DOUBLE_ADJ);
        self::DbQuery("UPDATE card SET `factor` = `factor` + 1 WHERE `id` = {$card->getId()}");
        $card->setFactor($card->getFactor() + 1);

        // Notify
        self::notifyCards($card);
    }

    public static function jail(int $playerId, HCard &$card): void
    {
        $updatedIds = [$card->getId()];
        $jailId = self::getJailId($playerId);
        if ($jailId) {
            self::discard($jailId, 'trash', false);
            $updatedIds[] = $jailId;
        }
        self::DbQuery("UPDATE card SET `origin` = 'jail', `location` = 'jail', `order` = '$playerId' WHERE `id` = {$card->getId()}");

        // Notify
        self::notifyCards(self::getCards($updatedIds));
    }

    public static function useBenefit($cards, int $benefit): void
    {
        $cardIds = self::getIds($cards);
        foreach ($cardIds as $cardId) {
            self::DbQuery("INSERT INTO resolve VALUES ($cardId, $benefit)");
        }
    }
}
