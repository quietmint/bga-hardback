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
 * hardback.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */


require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');
require_once('constants.inc.php');
require_once('modules/PlayerMgr.class.php');
require_once('modules/CardMgr.class.php');
require_once('modules/WordMgr.class.php');

class hardback extends Table
{
    public static $instance = null;

    function __construct()
    {
        parent::__construct();
        self::$instance = $this;

        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        self::initGameStateLabels(array(
            'dictionary' => OPTION_DICTIONARY,
        ));
    }

    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return 'hardback';
    }

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array())
    {
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach ($players as $player_id => $player) {
            $color = array_shift($default_colors);
            $values[] = "('" . $player_id . "','$color','" . $player['player_canal'] . "','" . addslashes($player['player_name']) . "','" . addslashes($player['player_avatar']) . "')";
        }
        $sql .= implode($values, ',');
        self::DbQuery($sql);
        self::reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        self::reloadPlayersBasicInfos();

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );

        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        CardMgr::setup();

        // Activate first player
        $this->activeNextPlayer();
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $playerId = self::getCurrentPlayerId();
        $handLocation = "hand$playerId";

        $result = [
            'players' => PlayerMgr::getPlayers(),
            'refs' => [
                'cards' => CardMgr::getCardRef(),
                'benefits' => CardMgr::getBenefitRef(),
            ],
            'locations' => [
                $handLocation => CardMgr::populateCards(CardMgr::getPlayerHand($playerId)),
                'tableau' => CardMgr::populateCards(CardMgr::getTableau()),
                'timeless' => CardMgr::populateCards(CardMgr::getTimeless()),
                'offer' => CardMgr::populateCards(CardMgr::getOffer())
            ]
        ];
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        return 0;
    }


    function setupCards()
    {
        CardMgr::setup();
    }

    function buildTableau($cardIds)
    {
        $cards = CardMgr::getCards($cardIds);
        $letters = [];
        foreach ($cards as $card) {
            $letters[] = $card['letter'];
        };
        self::notifyAllPlayers('message', 'Tableau: ' . implode('', $letters), []);
    }

    function dragOrder($cardId, $order, $location)
    {
        $playerId = self::getCurrentPlayerId();
        $validLocations = ["hand$playerId"];
        if ($playerId == self::getActivePlayerId()) {
            $validLocations[] = "tableau";
        }
        $card = CardMgr::getCard($cardId);
        if ($card['location'] != $location) {
            throw new BgaUserException("Card $cardId is in location {$card['location']} (expected: $location)");
        }
        if (!in_array($location, $validLocations)) {
            throw new BgaUserException("Player $playerId is not allowed to order cards in location $location");
        }
        CardMgr::orderCard($cardId, $order);
        $locations = [
            $location => CardMgr::populateCards(CardMgr::getCardsInLocation($location))
        ];
        self::notifyAllPlayers('cards', "dragOrder notify $location", [
            'playerId' => self::getCurrentPlayerId(),
            'locations' => $locations,
        ]);
    }

    function dragMove($cardId, $order, $from, $to)
    {
        $playerId = self::getCurrentPlayerId();
        $validLocations = [];
        if ($playerId == self::getActivePlayerId()) {
            $validLocations = ["hand$playerId", "tableau", "timeless"];
        }
        $card = CardMgr::getCard($cardId);
        if ($card['location'] != $from) {
            throw new BgaUserException("Card $cardId is in location {$card['location']} (expected: $from)");
        }
        if (!in_array($from, $validLocations)) {
            throw new BgaUserException("Player $playerId is not allowed to remove cards from location $from");
        }
        if (!in_array($to, $validLocations)) {
            throw new BgaUserException("Player $playerId is not allowed to add cards to location $to");
        }
        CardMgr::moveAndOrderCard($cardId, $to, $order);
        $locations = [
            $from => CardMgr::populateCards(CardMgr::getCardsInLocation($from)),
            $to => CardMgr::populateCards(CardMgr::getCardsInLocation($to))
        ];
        self::notifyAllPlayers('cards', "dragMove notify $from and $to", [
            'playerId' => self::getCurrentPlayerId(),
            'locations' => $locations,
        ]);
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Zombie
    ////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn($state, $active_player)
    {
        $statename = $state['name'];

        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState("zombiePass");
                    break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive($active_player, '');

            return;
        }

        throw new feException("Zombie mode not supported at this game state: " . $statename);
    }

    ///////////////////////////////////////////////////////////////////////////////////:
    ////////// DB upgrade
    //////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

    function upgradeTableDb($from_version)
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
    }
}
