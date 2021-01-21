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
define('EITHER_BASIC', 3);
define('EITHER_GENRE', 4);
define('EITHER_INK', 5);
define('UNCOVER_ADJ', 6);
define('DOUBLE_ADJ', 7);
define('JAIL', 8);
define('TRASH_COINS', 10);
define('TRASH_POINTS', 11);
define('TRASH_DISCARD', 12);
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
define('START_SCORE', 20);
define('START_INK', 21);
define('START_REMOVER', 22);
define('AWARD_WINNER', 30);

// Game statistics
define('STAT_TURNS', 10);
define('STAT_LONGEST_WORD', 11);
define('STAT_INVALID_WORDS', 12);
define('STAT_USE_INK', 20);
define('STAT_USE_REMOVER', 21);
define('STAT_COINS', 30);

// Game options
define('OPTION_DICTIONARY', 100);
define('TWELVEDICTS', 1);
define('NWL', 2);
define('COLLINS', 3);

define('OPTION_AWARDS', 101);
define('OPTION_ADVERTS', 102);
define('OPTION_EVENTS', 103);
define('OPTION_POWERS', 104);
define('NO', 0);
define('YES', 1);
define('PASSIVE', 2);
define('AGRESSIVE', 3);

define('OPTION_COOP', 110);
// 0 = no, 1 = yes
define('COOP_EASY', 2);

// Game states
define('ST_BGA_GAME_SETUP', 1);
define('ST_PLAYER_TURN', 2);

define('ST_UNCOVER', 10);
define('ST_DOUBLE', 11);
define('ST_BASIC', 20);
define('ST_TRASH', 21);
define('ST_EITHER', 30);
define('ST_JAIL', 31);

define('ST_FLUSH', 80);
define('ST_PURCHASE', 81);
define('ST_CLEANUP', 90);
define('ST_NEXT_PLAYER', 91);

define('ST_GAME_END', 98);
define('ST_BGA_GAME_END', 99);
