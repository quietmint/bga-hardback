<?php

// Genres
define('STARTER', 0);
define('ADVENTURE', 1);
define('HORROR', 2);
define('ROMANCE', 3);
define('MYSTERY', 4);

// Benefits
define('COINS', 1);
define('DOUBLE', 2);
define('EITHER', 3);
define('INK', 4);
define('JAIL', 5);
define('SPECIAL_ADVENTURE', 6); // 2 coins for each adventure
define('SPECIAL_HORROR', 7); // opponents return ink
define('SPECIAL_MYSTERY', 8); // 1 star for each wild
define('SPECIAL_ROMANCE', 9); // peek top 3 cards
define('POINTS', 10);
define('TRASH_COINS', 11);
define('TRASH_DISCARD', 12);
define('TRASH_POINTS', 13);
define('UNCOVER', 14);

// Game options
define('OPTION_DICTIONARY', 100);
define('TWELVEDICTS', 1);
define('NWL', 2);
define('COLLINS', 3);

// Game states
define('ST_BGA_GAME_SETUP', 1);
define('ST_PLAYER_TURN', 2);
define('ST_NEXT_PLAYER', 90);
define('ST_GAME_END', 98);
define('ST_BGA_GAME_END', 99);
