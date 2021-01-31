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
    ST_BGA_GAME_SETUP => [
        'name' => 'gameSetup',
        'description' => '',
        'type' => 'manager',
        'action' => 'stGameSetup',
        'transitions' => [
            '' => ST_PLAYER_TURN,
        ],
    ],

    ST_PLAYER_TURN => [
        'name' => 'playerTurn',
        'description' => clienttranslate('${actplayer} must spell a word'),
        'descriptionmyturn' => clienttranslate('${you} must spell a word'),
        'type' => 'activeplayer',
        'action' => 'stPlayerTurn',
        'possibleactions' => ['confirmWord', 'skipWord'],
        'transitions' => [
            'next' => ST_UNCOVER,
            'skip' => ST_COOP_TURN,
            'zombie' => ST_NEXT_PLAYER,
        ],
        'updateGameProgression' => true,
    ],

    ST_UNCOVER => [
        'name' => 'uncover',
        'description' => clienttranslate('${actplayer} may uncover a wild card'),
        'descriptionmyturn' => clienttranslate('${you} may uncover a wild card'),
        'type' => 'activeplayer',
        'args' => 'argUncover',
        'action' => 'stAutoSkip',
        'possibleactions' => ['uncover', 'skip'],
        'transitions' => [
            'again' => ST_UNCOVER,
            'next' => ST_DOUBLE,
            'zombie' => ST_CLEANUP,
        ],
    ],

    ST_DOUBLE => [
        'name' => 'double',
        'description' => clienttranslate('${actplayer} may double an adjacent card'),
        'descriptionmyturn' => clienttranslate('${you} may double an adjacent card'),
        'type' => 'activeplayer',
        'args' => 'argDouble',
        'action' => 'stAutoSkip',
        'possibleactions' => ['double', 'skip'],
        'transitions' => [
            'again' => ST_DOUBLE,
            'next' => ST_BASIC,
            'zombie' => ST_CLEANUP,
        ],
    ],

    ST_BASIC => [
        'name' => 'basic',
        'description' => '',
        'type' => 'game',
        'action' => 'stBasic',
        'transitions' => [
            'next' => ST_TRASH,
        ],
    ],

    ST_TRASH => [
        'name' => 'trash',
        'description' => clienttranslate('${actplayer} may trash a card'),
        'descriptionmyturn' => clienttranslate('${you} may trash a card (${coins}¢ and ${points}${icon} earned so far)'),
        'type' => 'activeplayer',
        'args' => 'argTrash',
        'action' => 'stAutoSkip',
        'possibleactions' => ['trash', 'skip'],
        'transitions' => [
            'again' => ST_TRASH,
            'next' => ST_TRASH_DISCARD,
            'zombie' => ST_CLEANUP,
        ],
    ],

    ST_TRASH_DISCARD => [
        'name' => 'trashDiscard',
        'description' => clienttranslate('${actplayer} may trash a card'),
        'descriptionmyturn' => clienttranslate('${you} may trash a card (${coins}¢ and ${points}${icon} earned so far)'),
        'type' => 'activeplayer',
        'args' => 'argTrashDiscard',
        'action' => 'stAutoSkip',
        'possibleactions' => ['trashDiscard', 'skip'],
        'transitions' => [
            'again' => ST_TRASH_DISCARD,
            'next' => ST_EITHER,
            'zombie' => ST_CLEANUP,
        ],
    ],

    ST_EITHER => [
        'name' => 'either',
        'description' => clienttranslate('${actplayer} must choose a benefit'),
        'descriptionmyturn' => clienttranslate('${you} must choose a benefit (${coins}¢ and ${points}${icon} earned so far)'),
        'type' => 'activeplayer',
        'args' => 'argEither',
        'action' => 'stAutoSkip',
        'possibleactions' => ['either'],
        'transitions' => [
            'again' => ST_EITHER,
            'next' => ST_JAIL,
            'zombie' => ST_CLEANUP,
        ],
    ],

    ST_JAIL => [
        'name' => 'jail',
        'description' => clienttranslate('${actplayer} may jail or trash an offer row card'),
        'descriptionmyturn' => clienttranslate('${you} may jail or trash an offer row card'),
        'type' => 'activeplayer',
        'args' => 'argJail',
        'action' => 'stAutoSkip',
        'possibleactions' => ['jail', 'skip'],
        'transitions' => [
            'again' => ST_JAIL,
            'next' => ST_FLUSH,
            'zombie' => ST_CLEANUP,
        ],
    ],

    ST_FLUSH => [
        'name' => 'flush',
        'description' => clienttranslate('${actplayer} may flush the offer row (${coins}¢ available)'),
        'descriptionmyturn' => clienttranslate('${you} may flush the offer row (${coins}¢ available)'),
        'type' => 'activeplayer',
        'args' => 'argFlush',
        'action' => 'stFlush',
        'possibleactions' => ['flush', 'skip'],
        'transitions' => [
            'next' => ST_PURCHASE,
            'zombie' => ST_CLEANUP,
        ],
    ],

    ST_PURCHASE => [
        'name' => 'purchase',
        'description' => clienttranslate('${actplayer} may purchase (${coins}¢ available)'),
        'descriptionmyturn' => clienttranslate('${you} may purchase (${coins}¢ available)'),
        'type' => 'activeplayer',
        'args' => 'argPurchase',
        'action' => 'stAutoSkip',
        'possibleactions' => ['purchase', 'doctor', 'convert', 'skip'],
        'transitions' => [
            'again' => ST_PURCHASE,
            'next' => ST_CLEANUP,
            'zombie' => ST_CLEANUP,
        ],
        'updateGameProgression' => true,
    ],

    ST_CLEANUP => [
        'name' => 'cleanup',
        'description' => '',
        'type' => 'game',
        'action' => 'stCleanup',
        'transitions' => [
            'next' => ST_COOP_TURN,
        ],
    ],

    ST_COOP_TURN => [
        'name' => 'coopTurn',
        'description' => '',
        'type' => 'game',
        'action' => 'stCoopTurn',
        'transitions' => [
            'next' => ST_NEXT_PLAYER,
            'gameEnd' => ST_BGA_GAME_END,
        ],
    ],

    ST_NEXT_PLAYER => [
        'name' => 'nextPlayer',
        'description' => '',
        'type' => 'game',
        'action' => 'stNextPlayer',
        'transitions' => [
            'playerTurn' => ST_PLAYER_TURN,
            'gameEnd' => ST_BGA_GAME_END,
        ],
        'updateGameProgression' => true,
    ],


    // Final state.
    // Please do not modify (and do not overload action/args methods).
    ST_BGA_GAME_END => [
        'name' => 'gameEnd',
        'description' => clienttranslate('End of game'),
        'type' => 'manager',
        'action' => 'stGameEnd',
        'args' => 'argGameEnd',
    ]

];
