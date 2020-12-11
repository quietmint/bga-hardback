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
        'values' => [
            TWELVEDICTS => [
                'name' => 'Common English (65,000 words)',
                'description' => 'Words appearing in at least three advanced-level dictionaries for learners of English'
            ],
            NWL => [
                'name' => 'North American Scrabble (115,000 words)',
                'tmdisplay' => 'North American Scrabble Dictionary',
                'description' => 'Words allowed in North American Scrabble tournaments'
            ],
            COLLINS => [
                'name' => 'Collins Scrabble (280,000 words)',
                'tmdisplay' => 'Collins Scrabble Dictionary',
                'description' => 'Words allowed in British Scrabble tournaments'
            ],
        ]
    ],
];
