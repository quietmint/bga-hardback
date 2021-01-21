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
            'adverts' => OPTION_ADVERTS,
            'awards' => OPTION_AWARDS,
            'awardWinner' => AWARD_WINNER,
            'coop' => OPTION_COOP,
            'count' . STARTER => COUNT_STARTER,
            'count' . ADVENTURE => COUNT_ADVENTURE,
            'count' . HORROR => COUNT_HORROR,
            'count' . MYSTERY => COUNT_MYSTERY,
            'count' . ROMANCE => COUNT_ROMANCE,
            'dictionary' => OPTION_DICTIONARY,
            'events' => OPTION_EVENTS,
            'powers' => OPTION_POWERS,
            'startInk' => START_INK,
            'startRemover' => START_REMOVER,
            'startScore' => START_SCORE,
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
        self::setGameStateInitialValue('awardWinner', 0);
        self::setGameStateInitialValue('count' . STARTER, 0);
        self::setGameStateInitialValue('count' . ADVENTURE, 0);
        self::setGameStateInitialValue('count' . HORROR, 0);
        self::setGameStateInitialValue('count' . MYSTERY, 0);
        self::setGameStateInitialValue('count' . ROMANCE, 0);
        self::setGameStateInitialValue('startInk', 0);
        self::setGameStateInitialValue('startRemover', 0);
        self::setGameStateInitialValue('startScore', 0);

        // Init game statistics
        self::initStat('table', 'turns', 0);
        self::initStat('table', 'longestWord', 0);
        self::initStat('player', 'longestWord', 0);
        self::initStat('player', 'invalidWords', 0);
        self::initStat('player', 'useInk', 0);
        self::initStat('player', 'useRemover', 0);
        self::initStat('player', 'coins', 0);

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
            'cards' => CardMgr::getCardsInLocation([CardMgr::getHandLocation($playerId), CardMgr::getDiscardLocation($playerId), 'tableau', 'offer', 'jail', 'timeless%']),
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
            $this->incStat(1, 'invalidWords', $player->getId());
            self::notifyAllPlayers('invalid', '${player_name} spells an invalid word, ${invalid}', [
                'player_id' => $player->getId(),
                'player_name' => $player->getName(),
                'invalid' => $word,
            ]);
            return;
        }
        self::notifyAllPlayers('message', '${player_name} spells ${word}', [
            'player_name' => $player->getName(),
            'word' => $word,
        ]);

        // Database commit
        CardMgr::playWord($player->getId(), $cards);
        $this->setGameStateValue('startInk', $player->getInk());
        $this->setGameStateValue('startRemover', $player->getRemover());
        $this->setGameStateValue('startScore', $player->getScore());

        // Check length
        $length = strlen($word);
        $longest = $this->getStat('longestWord', $player->getId());
        if ($length > $longest) {
            $this->setStat($length, 'longestWord', $player->getId());
        }
        $longest = $this->getStat('longestWord');
        if ($length > $longest) {
            $this->setStat($length, 'longestWord');
            if ($length >= 7 && $this->gamestate->table_globals[OPTION_AWARDS]) {
                // Issue new literary award
                $points = $this->awards[min($length, 12)];
                $player->addPoints($points);
                self::notifyAllPlayers('award', '${player_name} earns the ${points}${icon} literary award for spelling the first ${award}', [
                    'player_name' => $player->getName(),
                    'points' => $points,
                    'icon' => ' star',
                    'award' => $length,
                ]);

                // Revoke old literary award
                $previous = $this->getGameStateValue('awardWinner');
                if ($previous) {
                    $points = $this->awards[min($longest, 12)];
                    $loser = PlayerMgr::getPlayer($previous);
                    $loser->addPoints(-1 * $points);
                    self::notifyAllPlayers('message', '... and ${player_name} loses the tarnished ${points}${icon} literary award', [
                        'player_name' => $loser->getName(),
                        'points' => $points,
                        'icon' => ' star',
                        'length' => $length,
                    ]);
                }
                $this->setGameStateValue('awardWinner', $player->getId());
            }
        }

        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 3: RESOLVE CARD BENEFITS
     */

    function argUncover(): array
    {
        $cardIds = [];
        $sources = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            if ($card->hasBenefit(UNCOVER_ADJ)) {
                if ($card->getPrevious() != null && $card->getPrevious()->isWild()) {
                    $id = $card->getPrevious()->getId();
                    $cardIds[] = $id;
                    $sources[$id][] = $card->getId();
                }
                if ($card->getNext() != null && $card->getNext()->isWild()) {
                    $id = $card->getNext()->getId();
                    $cardIds[] = $id;
                    $sources[$id][] = $card->getId();
                }
            }
        }
        return [
            'cardIds' => array_values(array_unique($cardIds)),
            'sources' => $sources,
        ];
    }

    function stUncover(): void
    {
        $sources = $this->gamestate->state()['args']['sources'];
        if (empty($sources)) {
            $this->notifyAllPlayers("message", "No cards to uncover", []);
            $this->gamestate->nextState('next');
            return;
        }

        // Apply the first automatic action we find
        /*
        foreach ($sources as $cardIds) {
            if (count($cardIds) == 1) {
                $cardId = reset($cardIds);
                $this->uncover($cardId);
            }
        }
        */
    }

    function uncover(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        $sourceIds = $this->gamestate->state()['args']['sources'][$cardId] ?? null;
        if (!$sourceIds || empty($sourceIds)) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to uncover card $cardId");
        }
        $sourceId = reset($sourceIds);
        $cards = CardMgr::getCards([$cardId, $sourceId]);
        $card = $cards[$cardId];
        $source = $cards[$sourceId];

        CardMgr::uncover($card, $source);
        self::notifyAllPlayers('message', '${player_name} uncovers ${genre}${letter}', [
            'player_name' => $player->getName(),
            'genre' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
        ]);
        $this->gamestate->nextState('again');
    }

    function argDouble(): array
    {
        $cardIds = [];
        $sources = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            if ($card->hasBenefit(DOUBLE_ADJ)) {
                if ($card->getPrevious() != null && !$card->getPrevious()->isWild()) {
                    $id = $card->getPrevious()->getId();
                    $cardIds[] = $id;
                    $sources[$id][] = $card->getId();
                }
                if ($card->getNext() != null && !$card->getNext()->isWild()) {
                    $id = $card->getNext()->getId();
                    $cardIds[] = $id;
                    $sources[$id][] = $card->getId();
                }
            }
        }
        return [
            'cardIds' => array_values(array_unique($cardIds)),
            'sources' => $sources,
        ];
    }

    function stDouble(): void
    {
        $sources = $this->gamestate->state()['args']['sources'];
        if (empty($sources)) {
            $this->notifyAllPlayers("message", "No cards to double", []);
            $this->gamestate->nextState('next');
            return;
        }

        // Apply the first automatic action we find
        /*
        foreach ($sources as $cardIds) {
            if (count($cardIds) == 1) {
                $cardId = reset($cardIds);
                $this->double($cardId);
            }
        }
        */
    }

    function double(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        $sourceIds = $this->gamestate->state()['args']['sources'][$cardId] ?? null;
        if (!$sourceIds || empty($sourceIds)) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to double card $cardId");
        }
        $sourceId = reset($sourceIds);
        $cards = CardMgr::getCards([$cardId, $sourceId]);
        $card = $cards[$cardId];
        $source = $cards[$sourceId];

        CardMgr::double($card, $source);
        self::notifyAllPlayers('message', '${player_name} doubles ${genre}${letter}', [
            'player_name' => $player->getName(),
            'genre' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
        ]);
        $this->gamestate->nextState('again');
    }

    function argEither(): array
    {
        $player = PlayerMgr::getPlayer();
        $possible = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            $p = null;
            $value = $card->getBenefitValue(EITHER_BASIC);
            if ($value) {
                $p = ['benefit' => EITHER_BASIC, 'amount' => $value];
            } else {
                $value = $card->getBenefitValue(EITHER_GENRE);
                if ($value) {
                    $p = ['benefit' => EITHER_GENRE, 'amount' => $value];
                } else {
                    $value = $card->getBenefitValue(EITHER_INK);
                    if ($value) {
                        $p = ['benefit' => EITHER_INK, 'amount' => 1];
                    }
                }
            }
            if ($p) {
                $possible[$card->getId()] = $p;
            }
        }
        return [
            'possible' => $possible,
            'coins' => $player->getCoins(),
            'points' => $player->getScore() - $this->getGameStateValue('startScore'),
            'icon' => ' star',
        ];
    }

    function stEither(): void
    {
        $basic = $this->gamestate->state()['args']['possible'];
        if (empty($basic) && empty($genre)) {
            $this->notifyAllPlayers("message", "No cards with either-or choices", []);
            $this->gamestate->nextState('next');
            return;
        }
    }

    function either(int $cardId, int $benefit, string $choice): void
    {
        $player = PlayerMgr::getPlayer();
        $state = $this->gamestate->state();
        $p = $state['args']['possible'][$cardId] ?? null;
        if (!$p || $p['benefit'] != $benefit) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to choose this benefit for card $cardId (benefit $benefit, choice $choice)");
        }

        $card = CardMgr::getCard($cardId);
        CardMgr::useBenefit($card, $benefit);
        if (($benefit == EITHER_BASIC || $benefit == EITHER_GENRE) && $choice == 'coins') {
            $player->addCoins($p['amount']);
        } else if (($benefit == EITHER_BASIC || $benefit == EITHER_GENRE) && $choice == 'points') {
            $player->addPoints($p['amount']);
        } else if ($benefit == EITHER_INK && $choice == 'ink') {
            $player->addInk();
        } else if ($benefit == EITHER_INK && $choice = 'remover') {
            $player->addRemover();
        } else {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to choose this benefit for $card (benefit $benefit, choice $choice)");
        }
        $this->gamestate->nextState('again');
    }

    function stBasic(): void
    {
        $player = PlayerMgr::getPlayer();
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
        $player->addPoints($points);
        $player->addCoins($coins);
        $this->gamestate->nextState('next');
    }

    function argTrash(): array
    {
        $cardIds = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            if ($card->hasBenefit(TRASH_COINS) || $card->hasBenefit(TRASH_POINTS)) {
                $cardIds[] = $card->getId();
            }
        }
        return [
            'cardIds' => $cardIds,
        ];
    }

    function stTrash(): void
    {
        $cardIds = $this->gamestate->state()['args']['cardIds'];
        if (empty($cardIds)) {
            $this->notifyAllPlayers("message", "No cards to trash", []);
            $this->gamestate->nextState('next');
            return;
        }
    }

    function trash(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        if (!in_array($cardId, $this->gamestate->state()['args']['cardIds'])) {
            throw new BgaVisibleSystemException("Not possible for $player to trash card $cardId");
        }

        $card = CardMgr::getCard($cardId);
        $coins = $card->getBenefitValue(TRASH_COINS);
        $icon = null;
        if ($coins) {
            CardMgr::useBenefit($card, TRASH_COINS);
            $player->addCoins($coins);
            $icon = '¢';
        } else {
            CardMgr::useBenefit($card, TRASH_POINTS);
            $points = $card->getBenefitValue(TRASH_POINTS);
            $player->addPoints($points);
            $icon = ' star';
        }

        self::notifyAllPlayers('message', '${player_name} trashes ${genre}${letter} to earn ${amount}${icon}', [
            'player_name' => $player->getName(),
            'genre' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
            'amount' => $coins ?? $points,
            'icon' => $icon,
        ]);
        CardMgr::discard($card, 'trash');
        $this->gamestate->nextState('again');
    }

    function argJail(): array
    {
        $sourceIds = [];
        $tableau = CardMgr::getTableau();
        foreach ($tableau as $card) {
            if ($card->hasBenefit(JAIL)) {
                $sourceIds[] = $card->getId();
            }
        }
        return [
            'sourceIds' => $sourceIds,
        ];
    }

    function stJail(): void
    {
        $sourceIds = $this->gamestate->state()['args']['sourceIds'];
        if (empty($sourceIds)) {
            $this->notifyAllPlayers("message", "No cards to jail", []);
            $this->gamestate->nextState('next');
            return;
        }
    }

    function jail(int $cardId, string $choice): void
    {
        $player = PlayerMgr::getPlayer();
        $sourceIds = $this->gamestate->state()['args']['sourceIds'];
        if (empty($sourceIds)) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to jail any card");
        }
        $source = CardMgr::getCard($sourceIds[0]);
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation('offer')) {
            throw new BgaVisibleSystemException("Not possible for {$player->getName()} to jail card $card");
        }

        CardMgr::useBenefit($source, JAIL);
        if ($choice == 'trash') {
            CardMgr::discard($card, 'trash');
            self::notifyAllPlayers('message', '${player_name} trashes ${genre}${letter} from the offer row', [
                'player_name' => $player->getName(),
                'genre' => $card->getGenreName() . ' ',
                'letter' => $card->getLetter(),
            ]);
        } else {
            CardMgr::jail($player->getId(), $card);
            self::notifyAllPlayers('message', '${player_name} jails ${genre}${letter} from the offer row', [
                'player_name' => $player->getName(),
                'genre' => $card->getGenreName() . ' ',
                'letter' => $card->getLetter(),
            ]);
        }
        CardMgr::drawCards(1, 'deck', 'offer', null, true);
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
        // Summary of all earnings
        $player = PlayerMgr::getPlayer();
        $earnings = [
            '${icon}' => $player->getScore() - $this->getGameStateValue('startScore'),
            '¢' => $player->getCoins(),
            ' ink' => $player->getInk() - $this->getGameStateValue('startInk'),
            ' remover' => $player->getRemover() - $this->getGameStateValue('startRemover')
        ];
        $msg = [];
        foreach ($earnings as $k => $v) {
            if ($v > 0) {
                $msg[] = "$v$k";
                if ($k == '¢') {
                    $this->incStat($v, 'coins', $player->getId());
                }
            }
        }
        if (empty($msg)) {
            $msg[] = 'nothing';
        }
        self::notifyAllPlayers('message', '${player_name} earns ' . implode(', ', $msg) . ' this round', [
            'player_name' => $player->getName(),
            'icon' => ' star',
        ]);

        if (!$this->gamestate->state()['args']['possible']) {
            $this->skip();
        }
    }

    function flush(): void
    {
        if ($this->gamestate->state()['args']['possible']) {
            CardMgr::flushOffer();
        }
        $this->skip();
    }


    function argPurchase(): array
    {
        $player = PlayerMgr::getPlayer();
        $offer = CardMgr::getOffer($player->getId());
        $cardIds = CardMgr::getIds(array_filter($offer, function ($card) use ($player) {
            return $player->getCoins() >= $card->getCost();
        }));
        return [
            'cardIds' => $cardIds,
            'convert' => $player->getInk() >= 3,
            'coins' => $player->getCoins(),
        ];
    }

    function stPurchase(): void
    {
        if (empty($this->gamestate->state()['args']['cardIds']) && !$this->gamestate->state()['args']['convert']) {
            $this->skip();
        }
    }

    function purchase(int $cardId): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        if (!in_array($cardId, $this->gamestate->state()['args']['cardIds'])) {
            throw new BgaVisibleSystemException("Not possible for $player to purchase card $cardId");
        }
        $card = CardMgr::getCard($cardId);
        $oldLocation = $card->getLocation();
        $msg = '${player_name} spends ${coins}¢ to purchase ${genre}${letter}';
        $player->spendCoins($card->getCost());
        if ($card->getPoints() > 0) {
            $msg = '${player_name} spends ${coins}¢ to purchase ${genre}${letter} and earns ${points}${icon}';
            $player->addPoints($card->getPoints());
        }
        CardMgr::discard($card, $player->getDiscardLocation());
        self::notifyAllPlayers('message', $msg, [
            'player_name' => $player->getName(),
            'coins' => $card->getCost(),
            'genre' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
            'points' => $card->getPoints(),
            'icon' => ' star',
        ]);
        if ($oldLocation == 'offer') {
            CardMgr::drawCards(1, 'deck', 'offer', null, true);
        }
        $this->gamestate->nextState('again');
    }

    function convert(): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $player->spendInk(3);
        $player->addCoins(1);
        self::notifyAllPlayers('message', '${player_name} spends 3 ink for 1¢', [
            'player_name' => $player->getName(),
        ]);
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
        $amount = $player->getCoins();
        if ($amount > 0) {
            // Purchase ink with remaining coins
            $player->spendCoins($amount);
            $player->addInk($amount, false);
            $this->notifyAllPlayers('pause', '${player_name} spends ${amount}¢ to purchase ${amount} ink and ends their turn', [
                'player_name' => $player->getName(),
                'amount' => $amount,
                'duration' => 2000,
            ]);
        } else {
            $this->notifyAllPlayers('pause', '${player_name} ends their turn', [
                'player_name' => self::getActivePlayerName(),
                'duration' => 2000,
            ]);
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

        $this->giveExtraTime($player->getId());
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
        if ($player->isActive()) {
            $player->notifyInk(reset($cards));
        }
        CardMgr::inkCards($cards);
        $this->incStat(1, 'useInk', $player->getId());
    }

    function useRemover(int $cardId): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $player->spendRemover();
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation($player->getHandLocation(), 'tableau')) {
            throw new BgaUserException("Card $card is unavailable to $player");
        }
        if ($player->isActive()) {
            $player->notifyRemover($card);
        }
        CardMgr::inkCards($card, HAS_REMOVER);
        $this->incStat(1, 'useRemover', $player->getId());
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
