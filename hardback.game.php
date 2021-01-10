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
require_once('modules/HCard.class.php');
require_once('modules/HPlayer.class.php');
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
            'count' . STARTER => COUNT_STARTER,
            'count' . ADVENTURE => COUNT_ADVENTURE,
            'count' . HORROR => COUNT_HORROR,
            'count' . ROMANCE => COUNT_ROMANCE,
            'count' . MYSTERY => COUNT_MYSTERY,
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
        $sql .= implode(',', $values);
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

        // BGA requires 'players' to be an array
        $playersAsArray = array_map(function ($player) {
            return $player->jsonSerialize();
        }, PlayerMgr::getPlayers());

        return [
            'players' => $playersAsArray,
            'refs' => [
                'cards' => CardMgr::getCardRef(),
                'benefits' => CardMgr::getBenefitRef(),
            ],
            'locations' => [
                CardMgr::getHandLocation($playerId) => CardMgr::getHand($playerId),
                'tableau' => CardMgr::getTableau(),
                'timeless' => CardMgr::getTimeless(),
                'offer' => CardMgr::getOffer(),
            ]
        ];
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
        $max = intval(self::getUniqueValueFromDB("SELECT MAX(player_score) FROM player WHERE player_zombie = 0 and player_eliminated = 0"));
        return min(round($max / .6), 100);
    }

    /*
     * Utilities
     */
    function notifyCards($locations)
    {
        foreach ($locations as $location => $cards) {
            $parts = explode('_', $location);
            if (count($parts) == 2) {
                $playerId = intval($parts[1]);
                self::notifyPlayer($playerId, 'cards', '', [
                    'locations' => [
                        $location => $cards,
                    ],
                ]);
            } else {
                self::notifyAllPlayers('cards', '', [
                    'locations' => [
                        $location => $cards,
                    ],
                ]);
            }
        }
    }

    /*
     * States
     */

    function stNextPlayer()
    {
        // Reset hand
        $player = PlayerMgr::getPlayer();
        CardMgr::reset($player->getId());
        $this->notifyCards([
            'tableau' => [],
            $player->getHandLocation() => $player->getHand(),
        ]);
        $player->notifyPanel();

        // Activate next player
        $this->activeNextPlayer();

        // Notify about ink used out-of-turn
        $player = PlayerMgr::getPlayer();
        foreach ($player->getHand(HAS_INK) as $card) {
            $player->notifyInk($card);
        }
        foreach ($player->getHand(HAS_REMOVER) as $card) {
            $player->notifyRemover($card);
        }

        $this->gamestate->nextState('playerTurn');
    }

    function stGameEndStats()
    {
        $this->gamestate->nextState('gameEnd');
    }

    /*
    function dragOrder($cardId, $order, $location)
    {
        $playerId = self::getCurrentPlayerId();
        $validLocations = [CardMgr::getHandLocation($playerId)];
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
        $this->notifyCards([
            $location => CardMgr::getCardsInLocation($location),
        ]);
    }

    function dragMove($cardId, $order, $from, $to)
    {
        $playerId = self::getCurrentPlayerId();
        $validLocations = [];
        if ($playerId == self::getActivePlayerId()) {
            $validLocations = [CardMgr::getHandLocation($playerId), "tableau", "timeless"];
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
        $this->notifyCards([
            $from => CardMgr::getCardsInLocation($from),
            $to => CardMgr::getCardsInLocation($to),
        ]);
    }

    function sort($cardIds, $location)
    {
        $playerId = self::getCurrentPlayerId();
    }
    */



    /*
     * PHASE 1: SPELL A WORD
     * PHASE 2: DISCARD UNUSED CARDS
     */

    function confirmWord($cardIds, $wildMask)
    {
        // Minimum 2 letters
        if (count($cardIds) < 2) {
            throw new BgaUserException("You must use at least 2 letters");
        }

        $player = PlayerMgr::getPlayer();
        $cards = CardMgr::getCards($cardIds);
        CardMgr::applyWildMask($cards, $wildMask);

        // Cards must originate from a valid location
        $locations = [$player->getHandLocation(), "timeless"];
        $invalid = array_filter($cards, function ($card) use ($locations) {
            return !$card->isLocation($locations);
        });
        if (!empty($invalid)) {
            throw new BgaUserException("You cannot use cards unavailable to $player: " .  CardMgr::getString($invalid));
        }

        // All inked cards must be used and cannot be wild
        $inked = array_filter($player->getHand(HAS_INK), function ($card) use ($cardIds) {
            return $card->isWild() || !in_array($card->getId(), $cardIds);
        });
        if (!empty($inked)) {
            throw new BgaUserException("You must use all inked cards: " .  CardMgr::getString($inked));
        }

        // Word must be valid
        $word = implode('', array_map(function ($card) {
            return $card->getLetter();
        }, $cards));
        $dictionaryId = $this->gamestate->table_globals[OPTION_DICTIONARY];
        $valid = WordMgr::isWord($dictionaryId, $word);
        if (!$valid) {
            self::notifyAllPlayers('invalid', '${player_name} spells an invalid word, ${word}', [
                'player_id' => $player->getId(),
                'player_name' => $player->getName(),
                'word' => $word,
            ]);
            // Display the invalid attempt (but don't commit)
            $this->notifyCards([
                'tableau' => $cards,
            ]);
            return;
        }
        self::notifyAllPlayers('message', '${player_name} spells ${word}', [
            'player_name' => $player->getName(),
            'word' => $word,
        ]);

        // Database commit
        CardMgr::playWord($player->getId(), $cards);

        // Check for genre benefit activation
        foreach (CardMgr::getGenreCounts($cards) as $genre => $count) {
            $this->setGameStateValue("count$genre", $count);
        }

        // Display the word
        $this->notifyCards([
            'tableau' => $cards,
            $player->getHandLocation() => [],
        ]);

        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 3: RESOLVE CARD BENEFITS
     */

    function isActive($genre)
    {
        return $this->getGameStateValue("count$genre") >= 2;
    }

    function stResolveUncover()
    {
        $tableau = CardMgr::getTableau();
        $uncover = array_filter($tableau, function ($card) {
            $active = $this->isActive($card->getGenre());
            return $card->hasBenefit($active, UNCOVER);
        });
        self::notifyAllPlayers('message', "stResolveUncover count = " . count($uncover), []);
        if (empty($uncover)) {
            $this->gamestate->nextState('next');
            return;
        }
    }

    function argsUncover()
    {
    }

    function uncover()
    {
    }

    function stResolveEither()
    {
        $tableau = CardMgr::getTableau();
        $choice = array_filter($tableau, function ($card) {
            $active = $this->isActive($card->getGenre());
            return $card->hasBenefit($active, EITHER);
        });
        self::notifyAllPlayers('message', "stResolveChoice count = " . count($choice), []);
        if (empty($choice)) {
            $this->gamestate->nextState('next');
            return;
        }
    }

    function argsEither()
    {
    }

    function either($points)
    {
        $player = PlayerMgr::getPlayer();
        $value = 0; // TODO!
        if ($points) {
            $player->addPoints($value);
        } else {
            $player->addCoins($value);
        }
    }

    function stResolveBasic()
    {
        $tableau = CardMgr::getTableau();
        $points = 0;
        $coins = 0;
        foreach ($tableau as $card) {
            $active = $this->isActive($card->getGenre());

            $pointValue = $card->getBenefitValue($active, POINTS);
            if ($pointValue) {
                $points += $pointValue;
            }

            $coinValue = $card->getBenefitValue($active, COINS);
            if ($coinValue) {
                $coins += $coinValue;
            }
        }

        $player = PlayerMgr::getPlayer();
        $player->addPoints($points);
        $player->addCoins($coins);

        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 4: PURCHASE NEW CARDS AND INK
     */

    function argFlush()
    {
        return [
            'canFlush' => CardMgr::canFlushOffer(),
        ];
    }

    function stFlush()
    {
        $canFlush = $this->gamestate->state()['args']['canFlush'];
        if (!$canFlush) {
            $this->skip();
        }
    }

    function skip()
    {
        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 5: DISCARD USED CARDS AND INK
     * PHASE 6: DISCARD USED TIMELESS CLASSIC CARDS
     */

    function stCleanup()
    {
        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 7: DRAW YOUR NEXT HAND
     */

    function skipWord()
    {
        self::notifyAllPlayers('message', '${player_name} is stymied by writer\'s block and skips their turn.', [
            'player_name' => self::getActivePlayerName(),
        ]);
        $this->gamestate->nextState('skip');
    }

    /*
     * PHASE 8: USE INK AND REMOVER
     */

    function useInk()
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $player->spendInk();
        $cardIds = CardMgr::drawCards(1, $player->getDeckLocation(), $player->getHandLocation());
        if (empty($cardIds)) {
            throw new BgaUserException("Your deck is empty");
        }
        CardMgr::inkCards($cardIds);
        $card = CardMgr::getCard($cardIds[0]);
        if ($player->isActive()) {
            $player->notifyInk($card);
        }
        $this->notifyCards([
            $player->getHandLocation() => $player->getHand()
        ]);
    }

    function useRemover($cardId)
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation($player->getHandLocation(), 'tableau')) {
            throw new BgaUserException("Card $card is unavailable to $player");
        }
        $player->spendRemover();
        CardMgr::inkCards($cardId, 2);
        if ($player->isActive()) {
            $player->notifyRemover($card);
        }
        $this->notifyCards([
            $card->getLocation() => CardMgr::getCardsInLocation($card->getLocation()),
        ]);
    }

    /*
     * END OF THE GAME
     */

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

        throw new BgaVisibleSystemException("Zombie mode not supported at this game state: " . $statename);
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
