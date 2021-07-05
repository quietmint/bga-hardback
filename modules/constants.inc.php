<?php

// Genres
define('STARTER', 0);
define('ADVENTURE', 1);
define('HORROR', 2);
define('MYSTERY', 3);
define('ROMANCE', 4);

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
define('SPECIAL_HORROR', 21); // 1 star for each inked card
define('SPECIAL_MYSTERY', 22); // 1 star for each wild
define('SPECIAL_ROMANCE', 23); // peek top 3 cards
define('ALL_BENEFITS', 99);

// Player colors
define('RED', 'ff0000');
define('GREEN', '008000');
define('BLUE', '0000ff');
define('YELLOW', 'ffa500');
define('PURPLE', '982fff');
define('BLACK', '000000');

// Ink values
define('HAS_INK', 1);
define('HAS_REMOVER', 2);

// Benefit activation values
define('FROM_BASIC', 1);
define('FROM_GENRE', 2);

// Globals
define('COUNT_ACTIVE_ADVENTURE', 11);
define('COUNT_ACTIVE_HORROR', 12);
define('COUNT_ACTIVE_MYSTERY', 13);
define('COUNT_ACTIVE_ROMANCE', 14);
define('START_SCORE', 20);
define('START_INK', 21);
define('START_REMOVER', 22);
define('AWARD_WINNER', 30);

// Game statistics
define('STAT_TURNS', 10);
define('STAT_POINTS_BASIC', 10);
define('STAT_POINTS_GENRE', 11);
define('STAT_POINTS_PURCHASE', 12);
define('STAT_POINTS_AWARD', 13);
define('STAT_POINTS_ADVERT', 14);
define('STAT_COINS', 20);
define('STAT_CARDS_PURCHASE', 30);
define('STAT_CARDS_TRASH', 31);
define('STAT_WORDS', 50);
define('STAT_LONGEST_WORD', 51);
define('STAT_INVALID_WORDS', 52);
define('STAT_FLUSH', 53);
define('STAT_VOTES_ACCEPT', 54);
define('STAT_VOTES_REJECT', 55);
define('STAT_BEST_WORD', 56);
define('STAT_USE_INK', 60);
define('STAT_USE_REMOVER', 61);
define('STAT_STARTER_CARD1', 70);
define('STAT_STARTER_CARD2', 71);
define('STAT_STARTER', 75);
define('STAT_ADVENTURE', 76);
define('STAT_HORROR', 77);
define('STAT_MYSTERY', 78);
define('STAT_ROMANCE', 79);
define('STAT_COOP_SCORE', 80);
define('STAT_COOP_TURNS', 81);
define('STAT_COOP_AVG', 82);
define('STAT_COOP_POINTS_PURCHASE', 83);
define('STAT_COOP_POINTS_GENRE', 84);
define('STAT_COOP_GENRE', 89);

// Game options
define('OPTION_DICTIONARY', 100);
define('OPTION_DICTIONARY_DE', 122);
define('OPTION_DICTIONARY_FR', 123);
define('OPTION_DICTIONARY_ES', 124);
define('OPTION_DICTIONARY_IT', 125);
define('TWELVEDICTS', 1);
define('US', 2);
define('UK', 3);
define('MORPHALOU', 30);
define('YANDEX', 80);
define('VOTE_50', 90);
define('VOTE_100', 91);

define('OPTION_LENGTH', 105);
define('OPTION_AWARDS', 101);
define('OPTION_ADVERTS', 102);
define('OPTION_POWERS', 104);
define('NO', 0);
define('YES', 1);
define('PASSIVE', 2);
define('AGRESSIVE', 3);

define('OPTION_COOP', 110);
// 0 = no
define('COOP_BASIC', 1);
define('COOP_SIGNATURE', 2);

if (!defined('GAMESTATE_RATING_MODE')) {
    define('GAMESTATE_RATING_MODE', 201);
}
if (!defined('ELO_OFF')) {
    define('ELO_OFF', 1);
}
if (!defined('ARENA_MODE')) {
    define('ARENA_MODE', 2);
}
define('OPTION_LANG', 207);
define('LANG_EN', 1);
define('LANG_DE', 2);
define('LANG_FR', 3);
define('LANG_ES', 4);
define('LANG_IT', 5);

// Game preferences
define('PREF_DRAG_DROP', 100);
define('PREF_CARD_SIZE', 151);

// Game states
define('ST_BGA_GAME_SETUP', 1);
define('ST_PLAYER_TURN', 2);
define('ST_COOP_TURN', 3);
define('ST_UNCOVER', 10);
define('ST_DOUBLE', 11);
define('ST_BASIC', 12);
define('ST_SPECIAL', 30);
define('ST_SPECIAL_ROMANCE_PROMPT', 31);
define('ST_SPECIAL_ROMANCE', 32);
define('ST_TRASH', 14);
define('ST_TRASH_DISCARD', 15);
define('ST_EITHER', 16);
define('ST_JAIL', 17);
define('ST_FLUSH', 20);
define('ST_PURCHASE', 21);
define('ST_VOTE', 80);
define('ST_CLEANUP', 90);
define('ST_SKIP_TURN', 92);
define('ST_NEXT_PLAYER', 91);
define('ST_START', 97);
define('ST_END', 98);
define('ST_BGA_GAME_END', 99);
