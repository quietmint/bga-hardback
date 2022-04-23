<?php

// Genres
define('H_STARTER', 0);
define('H_ADVENTURE', 1);
define('H_HORROR', 2);
define('H_MYSTERY', 3);
define('H_ROMANCE', 4);

// Benefits
define('H_COINS', 1);
define('H_POINTS', 2);
define('H_EITHER_BASIC', 3);
define('H_EITHER_GENRE', 4);
define('H_EITHER_INK', 5);
define('H_UNCOVER_ADJ', 6);
define('H_DOUBLE_ADJ', 7);
define('H_JAIL', 8);
define('H_TRASH_COINS', 10);
define('H_TRASH_POINTS', 11);
define('H_TRASH_DISCARD', 12);
define('H_SPECIAL_ADVENTURE', 20); // 2 coins for each adventure
define('H_SPECIAL_HORROR', 21); // 1 star for each inked card
define('H_SPECIAL_MYSTERY', 22); // 1 star for each wild
define('H_SPECIAL_ROMANCE', 23); // peek top 3 cards
define('H_ALL_BENEFITS', 99);

// Player colors
define('H_RED', 'ff0000');
define('H_GREEN', '008000');
define('H_BLUE', '0000ff');
define('H_YELLOW', 'ffa500');
define('H_PURPLE', '982fff');
define('H_BLACK', '000000');

// Ink values
define('H_HAS_INK', 1);
define('H_HAS_REMOVER', 2);

// Benefit activation values
define('H_FROM_BASIC', 1);
define('H_FROM_GENRE', 2);

// Globals
define('H_COUNT_ACTIVE_ADVENTURE', 11);
define('H_COUNT_ACTIVE_HORROR', 12);
define('H_COUNT_ACTIVE_MYSTERY', 13);
define('H_COUNT_ACTIVE_ROMANCE', 14);
define('H_START_SCORE', 20);
define('H_START_INK', 21);
define('H_START_REMOVER', 22);
define('H_AWARD_WINNER', 30);
define('H_ATTEMPTS', 40);

// Game statistics
define('H_STAT_TURNS', 10);
define('H_STAT_POINTS_BASIC', 10);
define('H_STAT_POINTS_GENRE', 11);
define('H_STAT_POINTS_PURCHASE', 12);
define('H_STAT_POINTS_AWARD', 13);
define('H_STAT_POINTS_ADVERT', 14);
define('H_STAT_COINS', 20);
define('H_STAT_CARDS_PURCHASE', 30);
define('H_STAT_CARDS_TRASH', 31);
define('H_STAT_WORDS', 50);
define('H_STAT_LONGEST_WORD', 51);
define('H_STAT_INVALID_WORDS', 52);
define('H_STAT_FLUSH', 53);
define('H_STAT_VOTES_ACCEPT', 54);
define('H_STAT_VOTES_REJECT', 55);
define('H_STAT_BEST_WORD', 56);
define('H_STAT_USE_INK', 60);
define('H_STAT_USE_REMOVER', 61);
define('H_STAT_STARTER_CARD1', 70);
define('H_STAT_STARTER_CARD2', 71);
define('H_STAT_STARTER', 75);
define('H_STAT_ADVENTURE', 76);
define('H_STAT_HORROR', 77);
define('H_STAT_MYSTERY', 78);
define('H_STAT_ROMANCE', 79);
define('H_STAT_COOP_SCORE', 80);
define('H_STAT_COOP_TURNS', 81);
define('H_STAT_COOP_AVG', 82);
define('H_STAT_COOP_POINTS_PURCHASE', 83);
define('H_STAT_COOP_POINTS_GENRE', 84);
define('H_STAT_COOP_GENRE', 89);

// Game options
define('H_OPTION_GAME_MODE', 201);
define('H_TRAINING_MODE', 1);

define('H_OPTION_DICTIONARY', 100);
define('H_OPTION_DICTIONARY_DE', 122);
define('H_OPTION_DICTIONARY_FR', 123);
define('H_TWELVEDICTS', 1);
define('H_US', 2);
define('H_UK', 3);
define('H_LETTERPRESS', 4);
define('H_BEOLINGUS', 20);
define('H_FREE_DE', 21);
define('H_MORPHALOU', 30);
define('H_VOTE_50', 90);
define('H_VOTE_100', 91);

define('H_OPTION_ATTEMPTS', 106);
define('H_OPTION_LENGTH', 105);
define('H_OPTION_AWARDS', 101);
define('H_OPTION_ADVERTS', 102);
define('H_OPTION_POWERS', 104);
define('H_NO', 0);
define('H_YES', 1);
define('H_PASSIVE', 2);
define('H_AGRESSIVE', 3);

define('H_OPTION_COOP', 110);
define('H_COOP_BASIC', 1);
define('H_COOP_SIGNATURE', 2);

define('H_OPTION_DECK', 170);

define('H_OPTION_LANG', 207);
define('H_LANG_EN', 1);
define('H_LANG_DE', 2);
define('H_LANG_FR', 3);

// Game preferences
define('H_PREF_DRAG_DROP', 100);
define('H_PREF_CARD_SIZE', 151);

// Game states
define('H_ST_BGA_GAME_SETUP', 1);
define('H_ST_PLAYER_TURN', 2);
define('H_ST_COOP_TURN', 3);
define('H_ST_UNCOVER', 10);
define('H_ST_DOUBLE', 11);
define('H_ST_BASIC', 12);
define('H_ST_SPECIAL', 30);
define('H_ST_SPECIAL_ROMANCE_PROMPT', 31);
define('H_ST_SPECIAL_ROMANCE', 32);
define('H_ST_TRASH', 14);
define('H_ST_TRASH_DISCARD', 15);
define('H_ST_EITHER', 16);
define('H_ST_JAIL', 17);
define('H_ST_FLUSH', 20);
define('H_ST_PURCHASE', 21);
define('H_ST_VOTE', 80);
define('H_ST_CLEANUP', 90);
define('H_ST_SKIP_TURN', 92);
define('H_ST_NEXT_PLAYER', 91);
define('H_ST_START', 97);
define('H_ST_END', 98);
define('H_ST_BGA_GAME_END', 99);
