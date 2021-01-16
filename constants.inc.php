<?php

// Genres
define('STARTER', 0);
define('ADVENTURE', 1);
define('HORROR', 2);
define('ROMANCE', 3);
define('MYSTERY', 4);

// Benefits
define('COINS', 1);
define('POINTS', 2);
define('EITHER', 3);
define('INK', 4);
define('UNCOVER_ADJ', 5);
define('DOUBLE_ADJ', 6);
define('JAIL', 7);
define('TRASH_COINS', 8);
define('TRASH_POINTS', 9);
define('TRASH_DISCARD', 10);
define('SPECIAL_ADVENTURE', 20); // 2 coins for each adventure
define('SPECIAL_HORROR', 21); // opponents return ink
define('SPECIAL_MYSTERY', 22); // 1 star for each wild
define('SPECIAL_ROMANCE', 23); // peek top 3 cards

// Player colors
define('RED', 'ff0000');
define('GREEN', '008000');
define('BLUE', '0000ff');
define('YELLOW', 'ffa500');
define('PURPLE', '982fff');

// Ink values
define('HAS_INK', 1);
define('HAS_REMOVER', 2);

// Globals
define('COUNT_STARTER', 10);
define('COUNT_ADVENTURE', 11);
define('COUNT_HORROR', 12);
define('COUNT_ROMANCE', 13);
define('COUNT_MYSTERY', 14);

// Game options
define('OPTION_DICTIONARY', 100);
define('TWELVEDICTS', 1);
define('NWL', 2);
define('COLLINS', 3);

// Game states
define('ST_BGA_GAME_SETUP', 1);
define('ST_PLAYER_TURN', 2);

define('ST_UNCOVER', 10);
define('ST_DOUBLE', 11);
define('ST_EITHER', 12);
define('ST_BASIC', 16);
define('ST_TRASH', 17);

define('ST_FLUSH', 80);
define('ST_PURCHASE', 81);

define('ST_CLEANUP', 90);
define('ST_NEXT_PLAYER', 91);

define('ST_GAME_END', 98);
define('ST_BGA_GAME_END', 99);
