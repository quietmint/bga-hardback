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
        'possibleactions' => ['confirmWord', 'skipWord'],
        'transitions' => [
            'next' => ST_RESOLVE_UNCOVER,
            'skip' => ST_NEXT_PLAYER
        ],
    ],

    ST_RESOLVE_UNCOVER => [
        'name' => 'resolveUncover',
        'description' => '',
        'type' => 'game',
        'action' => 'stResolveUncover',
        'transitions' => [
            'again' => ST_UNCOVER,
            'next' => ST_RESOLVE_EITHER,
        ],
    ],

    ST_UNCOVER => [
        'name' => 'uncover',
        'description' => clienttranslate('${actplayer} may uncover a wild card'),
        'descriptionmyturn' => clienttranslate('${you} may uncover a wild card'),
        'type' => 'activeplayer',
        'possibleactions' => ['uncover', 'skip'],
        'transitions' => [
            'again' => ST_UNCOVER,
            'next' => ST_RESOLVE_EITHER,
        ],
    ],

    ST_RESOLVE_EITHER => [
        'name' => 'resolveEither',
        'description' => '',
        'type' => 'game',
        'action' => 'stResolveEither',
        'transitions' => [
            'again' => ST_EITHER,
            'next' => ST_RESOLVE_BASIC,
        ],
    ],

    ST_EITHER => [
        'name' => 'choice',
        'description' => clienttranslate('${actplayer} must choose a benefit'),
        'descriptionmyturn' => clienttranslate('${you} must choose a benefit'),
        'type' => 'activeplayer',
        'possibleactions' => ['eitherCoins', 'eitherPoints'],
        'transitions' => [
            'again' => ST_EITHER,
            'next' => ST_RESOLVE_BASIC,
        ],
    ],

    ST_RESOLVE_BASIC => [
        'name' => 'resolveBasic',
        'description' => '',
        'type' => 'game',
        'action' => 'stResolveBasic',
        'transitions' => [
            'next' => ST_FLUSH,
        ],
    ],

    ST_FLUSH => [
        'name' => 'flush',
        'description' => clienttranslate('${actplayer} may flush the offer row'),
        'descriptionmyturn' => clienttranslate('${you} may flush the offer row'),
        'type' => 'activeplayer',
        'args' => 'argFlush',
        'action' => 'stFlush',
        'possibleactions' => ['flush', 'skip'],
        'transitions' => [
            'next' => ST_PURCHASE,
        ],
    ],

    ST_PURCHASE => [
        'name' => 'purchase',
        'description' => clienttranslate('${actplayer} may purchase cards and ink'),
        'descriptionmyturn' => clienttranslate('${you} may purchase cards and ink'),
        'type' => 'activeplayer',
        'possibleactions' => ['purchase', 'skip'],
        'transitions' => [
            'again' => ST_PURCHASE,
            'next' => ST_CLEANUP,
        ],
    ],

    ST_CLEANUP => [
        'name' => 'cleanup',
        'description' => clienttranslate('${actplayer} must cleanup'),
        'descriptionmyturn' => clienttranslate('${you} must cleanup'),
        'type' => 'activeplayer',
        'possibleactions' => ['skip'],
        //'action' => 'stCleanup',
        'transitions' => [
            'next' => ST_NEXT_PLAYER,
        ],
    ],

    ST_NEXT_PLAYER => [
        'name' => 'nextPlayer',
        'description' => '',
        'type' => 'game',
        'action' => 'stNextPlayer',
        'transitions' => [
            'playerTurn' => ST_PLAYER_TURN,
            'gameEnd' => ST_GAME_END,
        ],
        'updateGameProgression' => true,
    ],

    ST_GAME_END => [
        'name' => 'gameEndStats',
        'description' => '',
        'type' => 'game',
        'action' => 'stGameEndStats',
        'transitions' => [
            'gameEnd' => ST_BGA_GAME_END,
        ],
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
