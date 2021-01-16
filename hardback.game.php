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

    protected function getGameName(): string
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
    protected function setupNewGame($players, $options = []): void
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
    protected function getAllDatas(): array
    {
        $playerId = self::getCurrentPlayerId();

        // BGA requires 'players' to be an array
        $playersAsArray = array_map(function ($player) {
            return $player->jsonSerialize();
        }, PlayerMgr::getPlayers());

        return [
            'players' => $playersAsArray,
            'cards' => CardMgr::getCardsInLocation([CardMgr::getHandLocation($playerId), 'tableau', 'offer', 'timeless']),
            'refs' => [
                'cards' => CardMgr::getCardRef(),
                'benefits' => CardMgr::getBenefitRef(),
            ],
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
    function getGameProgression(): int
    {
        $max = intval(self::getUniqueValueFromDB("SELECT MAX(player_score) FROM player WHERE player_zombie = 0 and player_eliminated = 0"));
        return min(round($max / .6), 100);
    }

    /*
     * PHASE 1: SPELL A WORD
     * PHASE 2: DISCARD UNUSED CARDS
     */

    function confirmWord(array $cardIds, string $wildMask): void
    {
        // Minimum 2 letters
        if (count($cardIds) < 2) {
            throw new BgaUserException("Your word must use at least 2 letters");
        }

        $player = PlayerMgr::getPlayer();
        $cards = CardMgr::getCards($cardIds, $wildMask);

        // Cards must originate from a valid location
        $locations = [$player->getHandLocation(), "timeless"];
        $invalid = array_filter($cards, function ($card) use ($locations) {
            return !$card->isLocation($locations);
        });
        if (!empty($invalid)) {
            throw new BgaUserException("Your word cannot use cards unavailable to $player: " .  CardMgr::getString($invalid));
        }

        // All inked cards must be used and cannot be wild
        $inked = array_filter($player->getHand(HAS_INK), function ($card) use ($cardIds) {
            return $card->isWild() || !in_array($card->getId(), $cardIds);
        });
        if (!empty($inked)) {
            throw new BgaUserException("Your word must use all inked cards: " .  CardMgr::getString($inked));
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
            return;
        }
        self::notifyAllPlayers('message', '${player_name} spells ${word}', [
            'player_name' => $player->getName(),
            'word' => $word,
        ]);

        // Database commit
        CardMgr::playWord($player->getId(), $cards);

        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 3: RESOLVE CARD BENEFITS
     */

    function argUncover(): array
    {
        $possible = [];
        $sources = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            if ($card->hasBenefit(UNCOVER_ADJ)) {
                $s = [];
                if ($card->getPrevious() != null && $card->getPrevious()->isWild()) {
                    $id = $card->getPrevious()->getId();
                    if (!array_key_exists($id, $possible)) {
                        $possible[$id] = [];
                    }
                    $possible[$id][] = $card->getId();
                    $s[] = $id;
                }
                if ($card->getNext() != null && $card->getNext()->isWild()) {
                    $id = $card->getNext()->getId();
                    if (!array_key_exists($id, $possible)) {
                        $possible[$id] = [];
                    }
                    $possible[$id][] = $card->getId();
                    $s[] = $id;
                }
                if (!empty($s)) {
                    $sources[$card->getId()] = $s;
                }
            }
        }
        return [
            'possible' => $possible,
            'sources' => $sources,
        ];
    }

    function stUncover(): void
    {
        $sources = $this->gamestate->state()['args']['sources'];
        if (empty($sources)) {
            $this->gamestate->nextState('next');
            return;
        }

        // Apply the first automatic action we find
        foreach ($sources as $cardIds) {
            if (count($cardIds) == 1) {
                $cardId = reset($cardIds);
                $this->uncover($cardId);
                return;
            }
        }
    }

    function uncover(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        $possible = $this->gamestate->state()['args']['possible'];
        $sourceIds = $possible[$cardId] ?? null;
        if (!$sourceIds || empty($sourceIds)) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to uncover card $cardId (no source)");
        }
        $sourceId = reset($sourceIds);
        $cards = CardMgr::getCards([$cardId, $sourceId]);
        $card = $cards[$cardId];
        $source = $cards[$sourceId];
        if (!$card->isWild()) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to uncover card $card (not wild)");
        }

        CardMgr::uncover($card, $source);
        self::notifyAllPlayers('message', '${player_name} uncovers ${icon}${letter}', [
            'player_name' => $player->getName(),
            'icon' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
        ]);
        $this->gamestate->nextState('again');
    }

    function argDouble(): array
    {
        $possible = [];
        $sources = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            if ($card->hasBenefit(DOUBLE_ADJ)) {
                $s = [];
                if ($card->getPrevious() != null && !$card->getPrevious()->isWild()) {
                    $id = $card->getPrevious()->getId();
                    if (!array_key_exists($id, $possible)) {
                        $possible[$id] = [];
                    }
                    $possible[$id][] = $card->getId();
                    $s[] = $id;
                }
                if ($card->getNext() != null && !$card->getNext()->isWild()) {
                    $id = $card->getNext()->getId();
                    if (!array_key_exists($id, $possible)) {
                        $possible[$id] = [];
                    }
                    $possible[$id][] = $card->getId();
                    $s[] = $id;
                }
                if (!empty($s)) {
                    $sources[$card->getId()] = $s;
                }
            }
        }
        return [
            'possible' => $possible,
            'sources' => $sources,
        ];
    }

    function stDouble(): void
    {
        $sources = $this->gamestate->state()['args']['sources'];
        if (empty($sources)) {
            $this->gamestate->nextState('next');
            return;
        }

        // Apply the first automatic action we find
        foreach ($sources as $cardIds) {
            if (count($cardIds) == 1) {
                $cardId = reset($cardIds);
                $this->double($cardId);
                return;
            }
        }
    }

    function double(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        $possible = $this->gamestate->state()['args']['possible'];
        $sourceIds = $possible[$cardId] ?? null;
        if (!$sourceIds || empty($sourceIds)) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to double card $cardId (no source)");
        }
        $sourceId = reset($sourceIds);
        $cards = CardMgr::getCards([$cardId, $sourceId]);
        $card = $cards[$cardId];
        $source = $cards[$sourceId];
        if ($card->isWild()) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to double card $card (wild)");
        }

        //CardMgr::uncover($card, $source);
        self::notifyAllPlayers('message', '${player_name} doubles ${icon}${letter}', [
            'player_name' => $player->getName(),
            'icon' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
        ]);
        //$this->gamestate->nextState('again');
    }

    function argEither(): array
    {
        $possible = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            $value = $card->getBenefitValue(EITHER);
            if ($value) {
                $possible[$card->getId()] = $value;
            }
        }
        return [
            'possible' => $possible,
        ];
    }

    function stEither(): void
    {
        $possible = $this->gamestate->state()['args']['possible'];
        if (empty($possible)) {
            $this->gamestate->nextState('next');
            return;
        }
    }

    function either(int $cardId, int $benefitId): void
    {
        $player = PlayerMgr::getPlayer();
        $possible = $this->gamestate->state()['args']['possible'];
        $value = $possible[$cardId] ?? null;
        if (!$value) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to choose a benefit for card $cardId");
        }

        $card = CardMgr::getCard($cardId);
        CardMgr::useBenefit($card, EITHER);

        $icon = null;
        if ($benefitId == COINS) {
            $player->addCoins($value);
            $icon = '¢';
        } else {
            $player->addPoints($value);
            $icon = ' star';
        }
        self::notifyAllPlayers('message', '${player_name} earns ${amount}${icon}', [
            'player_name' => $player->getName(),
            'amount' => $value,
            'icon' => $icon,
        ]);
        $this->gamestate->nextState('again');
    }

    function stBasic(): void
    {
        $tableau = CardMgr::getTableau();
        $points = 0;
        $coins = 0;
        foreach ($tableau as $card) {
            $coinValue = $card->getBenefitValue(COINS);
            if ($coinValue) {
                $coins += $coinValue;
            }

            $pointValue = $card->getBenefitValue(POINTS);
            if ($pointValue) {
                $points += $pointValue;
            }
        }

        $player = PlayerMgr::getPlayer();
        $player->addPoints($points);
        $player->addCoins($coins);
        $this->gamestate->nextState('next');
    }


    function argTrash(): array
    {
        $possible = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            if ($card->hasBenefit(TRASH_COINS) || $card->hasBenefit(TRASH_POINTS)) {
                $possible[] = $card->getId();
            }
        }
        return [
            'possible' => $possible,
        ];
    }

    function stTrash(): void
    {
        $possible = $this->gamestate->state()['args']['possible'];
        if (empty($possible)) {
            $this->gamestate->nextState('next');
            return;
        }
    }

    function trash(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        $card = CardMgr::getCard($cardId);
        $coins = $card->getBenefitValue(TRASH_COINS);
        $points = $card->getBenefitValue(TRASH_POINTS);
        if ($card == null || !($coins || $points)) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to trash card $card");
        }

        $icon = null;
        if ($coins) {
            CardMgr::useBenefit($card, TRASH_COINS);
            $player->addCoins($coins, false);
            $icon = '¢';
        } else {
            CardMgr::useBenefit($card, TRASH_POINTS);
            $player->addPoints($points, false);
            $icon = ' star';
        }

        self::notifyAllPlayers('message', '${player_name} trashes ${icon}${letter} to earn ${amount}${icon2}', [
            'player_name' => $player->getName(),
            'icon' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
            'amount' => $coins ?? $points,
            'icon2' => $icon,
        ]);
        CardMgr::moveCards($card, 'trash');
        $this->gamestate->nextState('again');
    }

    /*
     * PHASE 4: PURCHASE NEW CARDS AND INK
     */

    function argFlush(): array
    {
        return [
            'possible' => CardMgr::canFlushOffer(),
        ];
    }

    function stFlush(): void
    {
        // TODO summary of points
        if (!$this->gamestate->state()['args']['possible']) {
            $this->skip();
        }
    }

    function flush(): void
    {
        CardMgr::flushOffer();
        $this->skip();
    }


    function argPurchase(): array
    {
        $player = PlayerMgr::getPlayer();
        $offer = CardMgr::getOffer();
        $cheapest = min(array_map(function ($card) {
            return $card->getCost();
        }, $offer));
        return [
            'possible' => $player->getCoins() >= $cheapest,
            'coins' => $player->getCoins(),
            'icon' => '¢',
        ];
    }

    function stPurchase(): void
    {
        if (!$this->gamestate->state()['args']['possible']) {
            // Purchase ink with remaining coins
            $player = PlayerMgr::getPlayer();
            $amount = $player->getCoins();
            if ($amount > 0) {
                $player->spendCoins($amount);
                $player->addInk($amount, false);
                $this->notifyAllPlayers('message', '${player_name} cannot afford any cards and purchases ${amount}${icon} for ${amount}${icon2}', [
                    'player_name' => $player->getName(),
                    'amount' => $amount,
                    'icon' => ' ink',
                    'icon2' => '¢',
                ]);
            } else {
                $this->notifyAllPlayers('message', '${player_name} cannot afford any cards or ink', [
                    'player_name' => self::getActivePlayerName(),
                ]);
            }
            $this->skip();
        }
    }

    function purchase(int $cardId): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation('offer')) {
            throw new BgaVisibleSystemException("Card $card is unavailable to $player");
        }
        $player->spendCoins($card->getCost());
        $location = $player->getDiscardLocation();
        CardMgr::moveCards($card, $location);
        self::notifyAllPlayers('message', '${player_name} purchases ${icon}${letter} for ${amount}${icon2}', [
            'player_name' => $player->getName(),
            'icon' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
            'amount' => $card->getCost(),
            'icon2' => '¢',
        ]);
        CardMgr::drawCards(1, 'deck', 'offer', null, true);
        $this->gamestate->nextState('again');
    }

    function skip(): void
    {
        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 5: DISCARD USED CARDS AND INK
     * PHASE 6: DISCARD USED TIMELESS CLASSIC CARDS
     */

    function stCleanup(): void
    {
        $player = PlayerMgr::getPlayer();

        // Purchase ink with remaining coins
        if ($player->getCoins() > 0) {
            $player->addInk($player->getCoins(), true);
        }

        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 7: DRAW YOUR NEXT HAND
     */

    function skipWord(): void
    {
        self::notifyAllPlayers('message', '${player_name} is stymied by writer\'s block and skips their turn.', [
            'player_name' => self::getActivePlayerName(),
        ]);
        $this->gamestate->nextState('skip');
    }

    function stNextPlayer(): void
    {
        // Reset hand
        $player = PlayerMgr::getPlayer();
        CardMgr::reset($player->getId());
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

    /*
     * PHASE 8: USE INK AND REMOVER
     */

    function useInk(): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $player->spendInk();
        $cards = CardMgr::drawCards(1, $player->getDeckLocation(), $player->getHandLocation());
        if (empty($cards)) {
            throw new BgaUserException("Your deck is empty");
        }
        CardMgr::inkCards($cards);
        if ($player->isActive()) {
            $player->notifyInk(array_shift($cards));
        }
    }

    function useRemover(int $cardId): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $player->spendRemover();
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation($player->getHandLocation(), 'tableau')) {
            throw new BgaUserException("Card $card is unavailable to $player");
        }
        CardMgr::inkCards($card, HAS_REMOVER);
        if ($player->isActive()) {
            $player->notifyRemover($card);
        }
    }

    /*
     * END OF THE GAME
     */

    function stGameEndStats(): void
    {
        $this->gamestate->nextState('gameEnd');
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

    function zombieTurn($state, $active_player): void
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
