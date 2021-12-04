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
 * states.inc.php
 *
 * hardback game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with 'game' type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by 'st' (ex: 'stMyGameStateName').
   _ possibleactions: array that specify possible player actions on this step. It allows you to use 'checkAction'
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in 'nextState' PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on 'onEnteringState' or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!


$machinestates = [

    // The initial state. Please do not modify.
    H_ST_BGA_GAME_SETUP => [
        'name' => 'gameSetup',
        'description' => '',
        'type' => 'manager',
        'action' => 'stGameSetup',
        'transitions' => [
            '' => H_ST_START,
        ],
    ],

    H_ST_START => [
        'name' => 'start',
        'description' => '',
        'type' => 'game',
        'action' => 'stStart',
        'transitions' => [
            'next' => H_ST_PLAYER_TURN,
        ],
    ],

    H_ST_PLAYER_TURN => [
        'name' => 'playerTurn',
        'description' => clienttranslate('${actplayer} must spell a word'),
        'descriptionmyturn' => clienttranslate('${you} must spell a word'),
        'type' => 'activeplayer',
        'possibleactions' => ['confirmWord', 'skipWord'],
        'transitions' => [
            'next' => H_ST_UNCOVER,
            'vote' => H_ST_VOTE,
            'skip' => H_ST_SKIP_TURN,
            'zombie' => H_ST_SKIP_TURN,
        ],
        'updateGameProgression' => true,
    ],

    H_ST_VOTE => [
        'name' => 'vote',
        'description' => clienttranslate('Others must vote on ${player_name}\'s word: ${word}'),
        'descriptionmyturn' => clienttranslate('${you} must vote on ${player_name}\'s word: ${word}'),
        'type' => 'multipleactiveplayer',
        'args' => 'argVote',
        'action' => 'stVote',
        'possibleactions' => ['voteAccept', 'voteReject'],
        'transitions' => [
            'accept' => H_ST_UNCOVER,
            'reject' => H_ST_PLAYER_TURN,
            'skip' => H_ST_SKIP_TURN,
        ],
    ],

    H_ST_UNCOVER => [
        'name' => 'uncover',
        'description' => clienttranslate('${actplayer} must uncover a wild card using ${genre}${letter}'),
        'descriptionmyturn' => clienttranslate('${you} must uncover a wild card using ${genre}${letter}'),
        'type' => 'activeplayer',
        'args' => 'argUncover',
        'action' => 'stAutoSkip',
        'possibleactions' => ['uncover'],
        'transitions' => [
            'again' => H_ST_UNCOVER,
            'next' => H_ST_DOUBLE,
            'zombie' => H_ST_CLEANUP,
        ],
    ],

    H_ST_DOUBLE => [
        'name' => 'double',
        'description' => clienttranslate('${actplayer} must double an adjacent card using ${genre}${letter}'),
        'descriptionmyturn' => clienttranslate('${you} must double an adjacent card using ${genre}${letter}'),
        'type' => 'activeplayer',
        'args' => 'argDouble',
        'action' => 'stAutoSkip',
        'possibleactions' => ['double'],
        'transitions' => [
            'again' => H_ST_DOUBLE,
            'next' => H_ST_BASIC,
            'zombie' => H_ST_CLEANUP,
        ],
    ],

    H_ST_BASIC => [
        'name' => 'basic',
        'description' => '',
        'type' => 'game',
        'action' => 'stBasic',
        'transitions' => [
            'next' => H_ST_SPECIAL,
        ],
    ],

    H_ST_SPECIAL => [
        'name' => 'special',
        'description' => '',
        'type' => 'game',
        'action' => 'stSpecial',
        'transitions' => [
            'next' => H_ST_TRASH,
        ],
    ],

    H_ST_TRASH => [
        'name' => 'trash',
        'description' => clienttranslate('${actplayer} may trash cards (${coins}¢ and ${points}${icon} earned so far)'),
        'descriptionmyturn' => clienttranslate('${you} may trash cards (${coins}¢ and ${points}${icon} earned so far)'),
        'type' => 'activeplayer',
        'args' => 'argTrash',
        'action' => 'stAutoSkip',
        'possibleactions' => ['trash', 'previewDraw', 'skip'],
        'transitions' => [
            'romance' => H_ST_SPECIAL_ROMANCE,
            'again' => H_ST_TRASH,
            'next' => H_ST_TRASH_DISCARD,
            'zombie' => H_ST_CLEANUP,
        ],
    ],

    H_ST_TRASH_DISCARD => [
        'name' => 'trashDiscard',
        'description' => clienttranslate('${actplayer} may trash discarded cards (${coins}¢ and ${points}${icon} earned so far)'),
        'descriptionmyturn' => clienttranslate('${you} may trash discarded cards (${coins}¢ and ${points}${icon} earned so far)'),
        'type' => 'activeplayer',
        'args' => 'argTrashDiscard',
        'action' => 'stAutoSkip',
        'possibleactions' => ['trashDiscard', 'previewDraw', 'skip'],
        'transitions' => [
            'romance' => H_ST_SPECIAL_ROMANCE,
            'again' => H_ST_TRASH_DISCARD,
            'next' => H_ST_SPECIAL_ROMANCE_PROMPT,
            'zombie' => H_ST_CLEANUP,
        ],
    ],

    H_ST_SPECIAL_ROMANCE_PROMPT => [
        'name' => 'specialRomancePrompt',
        'description' => clienttranslate('${actplayer} may preview cards'),
        'descriptionmyturn' => clienttranslate('${you} may preview cards'),
        'type' => 'activeplayer',
        'args' => 'argSpecialRomancePrompt',
        'action' => 'stAutoSkip',
        'possibleactions' => ['previewDraw', 'skip'],
        'transitions' => [
            'romance' => H_ST_SPECIAL_ROMANCE,
            'next' => H_ST_EITHER,
        ],
    ],

    H_ST_SPECIAL_ROMANCE => [
        'name' => 'specialRomance',
        'description' => clienttranslate('${actplayer} must return or discard previewed cards'),
        'descriptionmyturn' => clienttranslate('${you} must return or discard previewed cards'),
        'type' => 'activeplayer',
        'possibleactions' => ['previewReturn', 'previewDiscard'],
        'transitions' => [
            'next' => H_ST_TRASH,
            'zombie' => H_ST_CLEANUP,
        ],
    ],

    H_ST_EITHER => [
        'name' => 'either',
        'description' => clienttranslate('${actplayer} must choose a benefit for ${genre}${letter} (${coins}¢ and ${points}${icon} earned so far)'),
        'descriptionmyturn' => clienttranslate('${you} must choose a benefit for ${genre}${letter} (${coins}¢ and ${points}${icon} earned so far)'),
        'type' => 'activeplayer',
        'args' => 'argEither',
        'action' => 'stAutoSkip',
        'possibleactions' => ['either'],
        'transitions' => [
            'again' => H_ST_EITHER,
            'next' => H_ST_JAIL,
            'zombie' => H_ST_CLEANUP,
        ],
    ],

    H_ST_JAIL => [
        'name' => 'jail',
        'description' => clienttranslate('${actplayer} may jail offer row cards (${coins}¢ and ${points}${icon} earned so far)'),
        'descriptionmyturn' => clienttranslate('${you} may jail offer row cards (${coins}¢ and ${points}${icon} earned so far)'),
        'type' => 'activeplayer',
        'args' => 'argJail',
        'action' => 'stAutoSkip',
        'possibleactions' => ['jail', 'skip'],
        'transitions' => [
            'again' => H_ST_JAIL,
            'next' => H_ST_FLUSH,
            'zombie' => H_ST_CLEANUP,
        ],
    ],

    H_ST_FLUSH => [
        'name' => 'flush',
        'description' => clienttranslate('${actplayer} may flush the offer row (${coins}¢ available)'),
        'descriptionmyturn' => clienttranslate('${you} may flush the offer row (${coins}¢ available)'),
        'type' => 'activeplayer',
        'args' => 'argFlush',
        'action' => 'stFlush',
        'possibleactions' => ['flush', 'skip'],
        'transitions' => [
            'next' => H_ST_PURCHASE,
            'zombie' => H_ST_CLEANUP,
        ],
    ],

    H_ST_PURCHASE => [
        'name' => 'purchase',
        'description' => clienttranslate('${actplayer} may purchase (${coins}¢ available)'),
        'descriptionmyturn' => clienttranslate('${you} may purchase (${coins}¢ available)'),
        'type' => 'activeplayer',
        'args' => 'argPurchase',
        'action' => 'stAutoSkip',
        'possibleactions' => ['purchase', 'skipPurchase', 'doctor', 'convert'],
        'transitions' => [
            'again' => H_ST_PURCHASE,
            'next' => H_ST_CLEANUP,
            'zombie' => H_ST_CLEANUP,
        ],
        'updateGameProgression' => true,
    ],

    H_ST_CLEANUP => [
        'name' => 'cleanup',
        'description' => clienttranslate('${actplayer}\'s turn ends'),
        'descriptionmyturn' => clienttranslate('${actplayer}\'s turn ends'),
        'type' => 'activeplayer',
        'action' => 'stCleanup',
        'possibleactions' => [],
        'transitions' => [
            'next' => H_ST_COOP_TURN,
        ],
    ],

    H_ST_SKIP_TURN => [
        'name' => 'skipTurn',
        'description' => clienttranslate('${actplayer}\'s turn ends'),
        'descriptionmyturn' => clienttranslate('${actplayer}\'s turn ends'),
        'type' => 'activeplayer',
        'action' => 'stSkipTurn',
        'possibleactions' => [],
        'transitions' => [
            'next' => H_ST_COOP_TURN,
        ],
    ],

    H_ST_COOP_TURN => [
        'name' => 'coopTurn',
        'description' => clienttranslate('Penny Dreadful takes a turn'),
        'type' => 'game',
        'action' => 'stCoopTurn',
        'transitions' => [
            'next' => H_ST_NEXT_PLAYER,
            'end' => H_ST_END,
        ],
    ],

    H_ST_NEXT_PLAYER => [
        'name' => 'nextPlayer',
        'description' => '',
        'type' => 'game',
        'action' => 'stNextPlayer',
        'transitions' => [
            'playerTurn' => H_ST_PLAYER_TURN,
            'end' => H_ST_END,
        ],
        'updateGameProgression' => true,
    ],

    H_ST_END => [
        'name' => 'end',
        'description' => '',
        'type' => 'game',
        'action' => 'stEnd',
        'transitions' => [
            'gameEnd' => H_ST_BGA_GAME_END,
        ],
        'updateGameProgression' => true,
    ],

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    H_ST_BGA_GAME_END => [
        'name' => 'gameEnd',
        'description' => clienttranslate('End of game'),
        'type' => 'manager',
        'action' => 'stGameEnd',
        'args' => 'argGameEnd',
    ]

];
