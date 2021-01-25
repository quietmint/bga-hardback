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
 * material.inc.php
 *
 * hardback game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

require_once('constants.inc.php');

$this->genres = [STARTER, ADVENTURE, HORROR, ROMANCE, MYSTERY];

$this->awards = [
    7 => 5,
    8 => 6,
    9 => 7,
    10 => 9,
    11 => 12,
    12 => 15,
];

$this->adverts = [
    3 => 6,
    6 => 9,
    10 => 12,
    15 => 15,
    20 => 18,
];
