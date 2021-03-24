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
require_once('modules/constants.inc.php');
require_once('modules/HCard.class.php');
require_once('modules/HPlayer.class.php');
require_once('modules/HPenny.class.php');
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
        self::initGameStateLabels([
            'adverts' => OPTION_ADVERTS,
            'awards' => OPTION_AWARDS,
            'awardWinner' => AWARD_WINNER,
            'coop' => OPTION_COOP,
            'countActive' . ADVENTURE => COUNT_ACTIVE_ADVENTURE,
            'countActive' . HORROR => COUNT_ACTIVE_HORROR,
            'countActive' . MYSTERY => COUNT_ACTIVE_MYSTERY,
            'countActive' . ROMANCE => COUNT_ACTIVE_ROMANCE,
            'countOffer' . ADVENTURE => COUNT_OFFER_ADVENTURE,
            'countOffer' . HORROR => COUNT_OFFER_HORROR,
            'countOffer' . MYSTERY => COUNT_OFFER_MYSTERY,
            'countOffer' . ROMANCE => COUNT_OFFER_ROMANCE,
            'dictionary' => OPTION_DICTIONARY,
            // 'events' => OPTION_EVENTS,
            'length' => OPTION_LENGTH,
            // 'powers' => OPTION_POWERS,
            'startInk' => START_INK,
            'startRemover' => START_REMOVER,
            'startScore' => START_SCORE,
            'bestSpecialMystery' => BEST_SPECIAL_MYSTERY,
        ]);
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
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        // Create players
        $values = [];
        foreach ($players as $player_id => $player) {
            $color = array_shift($default_colors);
            $values[] = "('" . $player_id . "','$color','" . $player['player_canal'] . "','" . addslashes($player['player_name']) . "','" . addslashes($player['player_avatar']) . "')";
        }
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES " . implode(',', $values);
        self::DbQuery($sql);
        self::reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        self::reloadPlayersBasicInfos();

        // Init global values with their initial values
        self::setGameStateInitialValue('awardWinner', 0);
        self::setGameStateInitialValue('bestSpecialMystery', 0);
        self::setGameStateInitialValue('countActive' . ADVENTURE, 0);
        self::setGameStateInitialValue('countActive' . HORROR, 0);
        self::setGameStateInitialValue('countActive' . MYSTERY, 0);
        self::setGameStateInitialValue('countActive' . ROMANCE, 0);
        self::setGameStateInitialValue('countOffer' . ADVENTURE, 0);
        self::setGameStateInitialValue('countOffer' . HORROR, 0);
        self::setGameStateInitialValue('countOffer' . MYSTERY, 0);
        self::setGameStateInitialValue('countOffer' . ROMANCE, 0);
        self::setGameStateInitialValue('startInk', 0);
        self::setGameStateInitialValue('startRemover', 0);
        self::setGameStateInitialValue('startScore', 0);
        if ($this->gamestate->table_globals[OPTION_COOP] == COOP_SIGNATURE) {
            $genre = rand(ADVENTURE, ROMANCE);
            self::initStat('table', 'coopGenre', $genre);
        }

        // Init game statistics
        self::initStat('table', 'invalidWords', 0);
        self::initStat('table', 'longestWord', 0);
        self::initStat('table', 'turns', 0);
        self::initStat('table', 'words', 0);
        if ($this->gamestate->table_globals[OPTION_COOP]) {
            self::initStat('table', 'coopAvg', 0);
            self::initStat('table', 'coopScore', 0);
            self::initStat('table', 'coopTurns', 0);
        } else {
            self::initStat('table', 'flush', 0);
        }
        self::initStat('player', 'cardsPurchase', 0);
        self::initStat('player', 'cardsTrash', 0);
        self::initStat('player', 'coins', 0);
        self::initStat('player', 'invalidWords', 0);
        self::initStat('player', 'longestWord', 0);
        if ($this->gamestate->table_globals[OPTION_ADVERTS]) {
            self::initStat('player', 'pointsAdvert', 0);
        }
        if ($this->gamestate->table_globals[OPTION_AWARDS]) {
            self::initStat('player', 'pointsAward', 0);
        }
        self::initStat('player', 'pointsBasic', 0);
        self::initStat('player', 'pointsGenre', 0);
        self::initStat('player', 'pointsPurchase', 0);
        self::initStat('player', 'starterCard1', 0);
        self::initStat('player', 'starterCard2', 0);
        self::initStat('player', 'useInk', 0);
        self::initStat('player', 'useRemover', 0);
        self::initStat('player', 'words', 0);

        // Deal the cards
        CardMgr::setup();

        // Activate first player
        $this->activeNextPlayer();
    }

    public function isCurrentPlayerActive(): bool
    {
        return self::getCurrentPlayerId() == self::getActivePlayerId();
    }

    public function checkActionCustom(string $action, bool $throwException = true): bool
    {
        $stateName = $this->gamestate->state()['name'];
        $invalid =  $stateName == 'gameEnd' || ($this->isCurrentPlayerActive() && $stateName != 'playerTurn');
        if ($invalid && $throwException) {
            throw new BgaVisibleSystemException("This game action is impossible right now: $action");
        }
        return !$invalid;
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

        $locations = [CardMgr::getHandLocation($playerId), CardMgr::getDiscardLocation($playerId), 'tableau', 'offer', 'jail', 'timeless%'];
        if ($this->gamestate->state()['name'] == 'gameEnd') {
            $locations[] = CardMgr::getDeckLocation($playerId);
        }

        $cards = $this->cards;
        if ($this->gamestate->table_globals[OPTION_COOP] != NO) {
            // Hide Special Horror benefit for co-op games
            unset($cards[46]['genreBenefits'][SPECIAL_HORROR]);
        }

        $data = [
            'players' => $playersAsArray,
            'cards' => CardMgr::getCardsInLocation($locations),
            'finalRound' => $this->getGameProgression() >= 100,
            'options' => [
                'adverts' => $this->gamestate->table_globals[OPTION_ADVERTS] > 0,
                'awards' => $this->gamestate->table_globals[OPTION_AWARDS] > 0,
                'coop' => $this->gamestate->table_globals[OPTION_COOP] > 0,
                // 'events' => $this->gamestate->table_globals[OPTION_EVENTS] > 0,
                // 'powers' => $this->gamestate->table_globals[OPTION_POWERS] > 0,
            ],
            'refs' => [
                'benefits' => $this->benefits,
                'cards' => $cards,
                'i18n' => $this->i18n,
            ],
        ];
        if ($this->gamestate->table_globals[OPTION_ADVERTS]) {
            $data['refs']['adverts'] = $this->adverts;
        }
        if ($this->gamestate->table_globals[OPTION_AWARDS]) {
            $data['refs']['awards'] = $this->awards;
        }
        if ($this->gamestate->table_globals[OPTION_COOP]) {
            $data['penny'] = PlayerMgr::getPenny();
            $data['refs']['signatures'] = $this->signatures;
        }
        return $data;
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
        $score = PlayerMgr::getMaxScore();
        return min(round($score / $this->getGameLength() * 100), 100);
    }

    function getGameLength(): int
    {
        if ($this->gamestate->table_globals[OPTION_COOP]) {
            return 60 * PlayerMgr::getPlayerCount();
        } else {
            return $this->gamestate->table_globals[OPTION_LENGTH];
        }
    }

    /*
     * PHASE 1: SPELL A WORD
     * PHASE 2: DISCARD UNUSED CARDS
     */

    function stPlayerTurn()
    {
        $player = PlayerMgr::getPlayer();

        // Reset draw counts
        if ($this->gamestate->table_globals[OPTION_COOP] != NO) {
            foreach ([ADVENTURE, HORROR, MYSTERY, ROMANCE] as $genre) {
                hardback::$instance->setGameStateValue("countOffer$genre", 0);
            }
        }

        // Notify about ink used out-of-turn
        $inkCount = 0;
        foreach ($player->getHand(HAS_INK) as $card) {
            $player->notifyInk($card);
            $inkCount++;
        }
        $penny = PlayerMgr::getPenny();
        if ($inkCount && $penny->isGenreActive(HORROR)) {
            // Horror: Penny earns 1 per ink
            $penny->addPoints($inkCount);
            self::notifyAllPlayers('message', $this->msg['earn'], [
                'player_name' => $penny->getName(),
                'amount' => $inkCount,
                'icon' => 'star',
            ]);
        }
        foreach ($player->getHand(HAS_REMOVER) as $card) {
            $player->notifyRemover($card);
        }
    }

    function previewWord(array $handIds, string $handMask, ?array $tableauIds, ?string $tableauMask): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $origins = [$player->getHandLocation(), 'timeless'];

        $hand = CardMgr::getCards($handIds, $handMask);
        $invalid = array_filter($hand, function ($card) use ($origins) {
            return !$card->isOrigin($origins);
        });
        if (!empty($invalid)) {
            throw new BgaVisibleSystemException("previewWord: Not possible for $player to use hand cards " .  CardMgr::getString($invalid));
        }

        $notify = false;
        $tableau = null;
        if ($this->isCurrentPlayerActive() && $tableauIds !== null) {
            $notify = PlayerMgr::getPlayerCount() > 1;
            $tableau = CardMgr::getCards($tableauIds, $tableauMask);
            $invalid = array_filter($tableau, function ($card) use ($origins) {
                return !$card->isOrigin($origins);
            });
            if (!empty($invalid)) {
                throw new BgaVisibleSystemException("previewWord: Not possible for $player to use tableau cards " .  CardMgr::getString($invalid));
            }
        }

        CardMgr::previewWord($player->getId(), $hand, $tableau, $notify);
    }

    function confirmWord(array $cardIds, string $wildMask): void
    {
        // Minimum 2 letters
        if (count($cardIds) < 2) {
            throw new BgaUserException($this->msg['errorLength']);
        }

        $player = PlayerMgr::getPlayer();

        // Inked cards must be used
        $inked = CardMgr::getCardsInLocation([$player->getHandLocation(), 'tableau'], HAS_INK);
        $unusedInk = array_filter($inked, function ($card) use ($cardIds) {
            return !in_array($card->getId(), $cardIds);
        });
        if (!empty($unusedInk)) {
            throw new BgaUserException(sprintf($this->msg['errorInk'], CardMgr::getString($unusedInk)));
        }

        // Inked and timeless cards cannot be wild
        $cards = CardMgr::getCards($cardIds, $wildMask);
        $invalidWilds = array_filter($cards, function ($card) {
            return $card->isWild() && ($card->hasInk() || $card->isOrigin("timeless"));
        });
        if (!empty($invalidWilds)) {
            throw new BgaUserException(sprintf($this->msg['errorWild'], CardMgr::getString($invalidWilds)));
        }

        // Cards must originate from a valid location
        $origins = [$player->getHandLocation(), 'timeless'];
        $invalid = array_filter($cards, function ($card) use ($origins) {
            return !$card->isOrigin($origins);
        });
        if (!empty($invalid)) {
            throw new BgaVisibleSystemException("confirmWord: Not possible for $player to use cards " .  CardMgr::getString($invalid));
        }

        // Word must be valid
        $word = implode('', array_map(function ($card) {
            return $card->getLetter();
        }, $cards));
        $valid = WordMgr::isWord($word);
        if (!$valid) {
            $this->incStat(1, 'invalidWords');
            $this->incStat(1, 'invalidWords', $player->getId());
            self::notifyAllPlayers('invalid', $this->msg['invalidWord'], [
                'i18n' => ['dict'],
                'player_id' => $player->getId(),
                'player_name' => $player->getName(),
                'invalid' => $word,
                'dict' => $this->dicts[$this->gamestate->table_globals[OPTION_DICTIONARY]],
            ]);
            return;
        }
        $this->incStat(1, 'words');
        $this->incStat(1, 'words', $player->getId());
        self::notifyAllPlayers('message', $this->msg['confirmWord'], [
            'player_name' => $player->getName(),
            'word' => $word,
        ]);

        // Database commit
        CardMgr::commitWord($player->getId(), $cards);
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
                $msg = $this->msg['awardFirst'];
                $points = $this->awards[min($length, 12)];
                $args = [
                    'player_name' => $player->getName(),
                    'points' => $points,
                    'iconPoints' => 'star',
                    'length' => $length,
                    'award' => $length,
                ];
                $previous = $this->getGameStateValue('awardWinner');
                $this->setGameStateValue('awardWinner', $player->getId());
                if ($previous && $previous != $player->getId()) {
                    $msg = $this->msg['awardSecond'];
                    $loser = PlayerMgr::getPlayer($previous);
                    $args['player_name2'] = $loser->getName();
                    $loser->notifyPanel();
                }
                $player->notifyPanel();
                self::notifyAllPlayers('award', $msg, $args);
            }
        }

        // Compute best score for Special Mystery
        $tableau = CardMgr::getTableau($player->getId());
        $this->computeSpecialMystery($tableau);

        // Give extra time
        $this->giveExtraTime($player->getId());
        $this->gamestate->nextState('next');
    }

    /*
     * PHASE 3: RESOLVE CARD BENEFITS
     */

    function stAutoSkip(): void
    {
        $name = $this->gamestate->state()['name'];
        $skip = $this->gamestate->state()['args']['skip'] ?? false;
        $auto = $this->gamestate->state()['args']['auto'] ?? false;
        if ($skip) {
            $this->skip();
        } else if ($auto) {
            if ($name == 'uncover') {
                $this->uncover($auto);
            } else if ($name == 'double') {
                $this->double($auto);
            }
        }
    }

    /*
     * UNCOVER
     */

    function argUncover(): array
    {
        $cardIds = [];
        $source = null;
        $tableau = CardMgr::getTableau(self::getActivePlayerId());
        foreach ($tableau as $card) {
            if ($card->hasBenefit(UNCOVER_ADJ)) {
                if ($card->getPrevious() != null && $card->getPrevious()->isWild()) {
                    $cardIds[] = $card->getPrevious()->getId();
                }
                if ($card->getNext() != null && $card->getNext()->isWild()) {
                    $cardIds[] = $card->getNext()->getId();
                }
                if (!empty($cardIds)) {
                    $source = $card;
                    break;
                }
            }
        }
        return [
            'auto' => count($cardIds) === 1 ? reset($cardIds) : null,
            'cardIds' => $cardIds,
            'genre' => $source !== null ?  $source->getGenreName() : '',
            'letter' => $source !== null ? $source->getLetter() : '',
            'skip' => $source === null,
            'sourceId' => $source !== null ? $source->getId() : null,
        ];
    }

    function uncover(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        if (!in_array($cardId, $this->gamestate->state()['args']['cardIds'])) {
            throw new BgaVisibleSystemException("uncover: Not possible for $player to uncover card ID $cardId");
        }
        $sourceId = $this->gamestate->state()['args']['sourceId'];
        $cards = CardMgr::getCards([$cardId, $sourceId]);
        $card = $cards[$cardId];
        $source = $cards[$sourceId];

        CardMgr::uncover($card, $source);
        self::notifyAllPlayers('message', $this->msg['uncover'], [
            'player_name' => $player->getName(),
            'genre' => $card->getGenreName(),
            'letter' => $card->getLetter(),
        ]);

        // Recompute best score for Special Mystery
        $tableau = CardMgr::getTableau($player->getId());
        $this->computeSpecialMystery($tableau);

        $this->gamestate->nextState('again');
    }

    /*
     * DOUBLE
     */

    function argDouble(): array
    {
        $cardIds = [];
        $source = null;
        $tableau = CardMgr::getTableau(self::getActivePlayerId());
        foreach ($tableau as $card) {
            if ($card->hasBenefit(DOUBLE_ADJ)) {
                if ($card->getPrevious() != null && !$card->getPrevious()->isWild()) {
                    $cardIds[] = $card->getPrevious()->getId();
                }
                if ($card->getNext() != null && !$card->getNext()->isWild()) {
                    $cardIds[] = $card->getNext()->getId();
                }
                if (!empty($cardIds)) {
                    $source = $card;
                    break;
                }
            }
        }
        return [
            'auto' => count($cardIds) === 1 ? reset($cardIds) : null,
            'cardIds' => $cardIds,
            'genre' => $source !== null ?  $source->getGenreName() : '',
            'letter' => $source !== null ? $source->getLetter() : '',
            'skip' => $source === null,
            'sourceId' => $source !== null ? $source->getId() : null,
        ];
    }

    function double(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        if (!in_array($cardId, $this->gamestate->state()['args']['cardIds'])) {
            throw new BgaVisibleSystemException("double: Not possible for $player to double card ID $cardId");
        }
        $sourceId = $this->gamestate->state()['args']['sourceId'];
        $cards = CardMgr::getCards([$cardId, $sourceId]);
        $card = $cards[$cardId];
        $source = $cards[$sourceId];

        CardMgr::double($card, $source);
        self::notifyAllPlayers('message', $this->msg['double'], [
            'player_name' => $player->getName(),
            'genre' => $card->getGenreName(),
            'letter' => $card->getLetter(),
        ]);

        // Recompute best score for Special Mystery
        if ($card->hasBenefit(SPECIAL_MYSTERY)) {
            $tableau = CardMgr::getTableau($player->getId());
            $this->computeSpecialMystery($tableau);
        }

        $this->gamestate->nextState('again');
    }

    /*
     * EITHER
     */

    function argEither(): array
    {
        $player = PlayerMgr::getPlayer();
        $source = null;
        $amount = null;
        $benefit = null;
        $tableau = CardMgr::getTableau($player->getId());
        foreach ($tableau as $card) {
            $benefits = $card->getBenefits(EITHER_BASIC);
            if (!empty($benefits)) {
                $benefit = EITHER_BASIC;
                $amount = $benefits[0]['value'];
            } else {
                $benefits = $card->getBenefits(EITHER_GENRE);
                if (!empty($benefits)) {
                    $benefit = EITHER_GENRE;
                    $amount = $benefits[0]['value'];
                } else {
                    $benefits = $card->getBenefits(EITHER_INK);
                    if (!empty($benefits)) {
                        $benefit = EITHER_INK;
                        $amount =  1;
                    }
                }
            }
            if ($benefit) {
                $source = $card;
                break;
            }
        }
        return [
            'amount' => $amount,
            'benefit' => $benefit,
            'coins' => $player->getCoins(),
            'genre' => $source !== null ?  $source->getGenreName() : '',
            'icon' => 'star',
            'letter' => $source !== null ? $source->getLetter() : '',
            'points' => $player->getScore() - $this->getGameStateValue('startScore'),
            'skip' => $source === null,
            'sourceId' => $source !== null ? $source->getId() : null,
        ];
    }

    function either(int $cardId, int $benefitId, string $choice): void
    {
        $player = PlayerMgr::getPlayer();
        if ($cardId != $this->gamestate->state()['args']['sourceId'] || $benefitId != $this->gamestate->state()['args']['benefit']) {
            throw new BgaVisibleSystemException("either: Not possible for $player to choose benefit $benefitId for card $cardId");
        }
        $card = CardMgr::getCard($cardId);
        $benefits = $card->getBenefits($benefitId);
        if (empty($benefits)) {
            throw new BgaVisibleSystemException("either: Not possible for $player to choose benefit $benefitId for card $card");
        }

        CardMgr::useBenefit($card, $benefitId);
        $amount = $this->gamestate->state()['args']['amount'];
        if (($benefitId == EITHER_BASIC || $benefitId == EITHER_GENRE) && $choice == 'coins') {
            $player->addCoins($amount);
        } else if (($benefitId == EITHER_BASIC || $benefitId == EITHER_GENRE) && $choice == 'points') {
            $stat = $benefits[0]['activation'] == FROM_GENRE ? 'pointsGenre' : 'pointsBasic';
            $player->addPoints($amount, $stat);
        } else if ($benefitId == EITHER_INK && $choice == 'ink') {
            $player->addInk();
        } else if ($benefitId == EITHER_INK && $choice = 'remover') {
            $player->addRemover();
        }
        $this->gamestate->nextState('again');
    }

    /*
     * BASIC
     */

    function stBasic(): void
    {
        $player = PlayerMgr::getPlayer();
        $tableau = CardMgr::getTableau($player->getId());
        foreach ($tableau as $card) {
            $benefits = $card->getBenefits(COINS);
            foreach ($benefits as $benefit) {
                $player->addCoins($benefit['value']);
            }
            $benefits = $card->getBenefits(POINTS);
            foreach ($benefits as $benefit) {
                $stat = $benefit['activation'] == FROM_GENRE ? 'pointsGenre' : 'pointsBasic';
                $player->addPoints($benefit['value'], $stat);
            }
        }
        $this->gamestate->nextState('next');
    }

    /*
     * SPECIAL
     */

    function argSpecial(): array
    {
        $player = PlayerMgr::getPlayer();
        return [
            'coins' => $player->getCoins(),
            'points' => $player->getScore() - $this->getGameStateValue('startScore'),
            'icon' => 'star',
        ];
    }

    function stSpecial(): void
    {
        $player = PlayerMgr::getPlayer();
        $tableau = CardMgr::getTableau($player->getId());
        $specials = [];
        foreach ($tableau as $card) {
            if ($card->hasBenefit(SPECIAL_ADVENTURE)) {
                $specials[SPECIAL_ADVENTURE] = $card;
            } else if ($card->hasBenefit(SPECIAL_HORROR) && $this->gamestate->table_globals[OPTION_COOP] == NO) {
                $specials[SPECIAL_HORROR] = $card;
            } else if ($card->hasBenefit(SPECIAL_MYSTERY)) {
                $specials[SPECIAL_MYSTERY] = $card;
            } else if ($card->hasBenefit(SPECIAL_ROMANCE)) {
                $specials[SPECIAL_ROMANCE] = $card;
            }
        }

        if (isset($specials[SPECIAL_ADVENTURE])) {
            // Adventure: 2 coins per adventure
            $benefits = $specials[SPECIAL_ADVENTURE]->getBenefits(SPECIAL_ADVENTURE);
            $benefit = reset($benefits);
            CardMgr::useBenefit($specials[SPECIAL_ADVENTURE], SPECIAL_ADVENTURE);
            $coins = $this->getGameStateValue('countActive' . ADVENTURE) * $benefit['value'];
            $player->addCoins($coins);
        }

        if (isset($specials[SPECIAL_MYSTERY])) {
            // Mystery: 1 point per wild
            $score = $this->getGameStateValue('bestSpecialMystery');
            CardMgr::useBenefit($specials[SPECIAL_MYSTERY], SPECIAL_MYSTERY);
            $player->addPoints($score, 'pointsGenre');
        }
        $this->setGameStateValue('bestSpecialMystery', 0);

        if (isset($specials[SPECIAL_HORROR])) {
            // Horror: Opponents discard ink/remover
            CardMgr::useBenefit($specials[SPECIAL_HORROR], SPECIAL_HORROR);
            $multi = [];
            foreach (PlayerMgr::getPlayers() as $opponent) {
                if ($opponent->getId() == $player->getId()) {
                    continue;
                }
                if ($opponent->getInk() > 0 && $opponent->getRemover() > 0) {
                    $multi[] = $opponent->getId();
                } else if ($opponent->getInk() > 0) {
                    $opponent->spendInk();
                    $this->notifyAllPlayers('message', $this->msg['forceDiscard'], [
                        'i18n' => ['icon'],
                        'player_name' => $player->getName(),
                        'player_name2' => $opponent->getName(),
                        'amount' => 1,
                        'icon' => 'ink',
                    ]);
                } else if ($opponent->getRemover() > 0) {
                    $opponent->spendRemover();
                    $this->notifyAllPlayers('message', $this->msg['forceDiscard'], [
                        'i18n' => ['icon'],
                        'player_name' => $player->getName(),
                        'player_name2' => $opponent->getName(),
                        'amount' => 1,
                        'icon' => 'remover',
                    ]);
                }
            }
            if (!empty($multi)) {
                $this->gamestate->setPlayersMultiactive($multi, '', true);
                $this->gamestate->nextState('horror');
                return;
            }
        }

        if (isset($specials[SPECIAL_ROMANCE])) {
            // Romance: Draw 3 and return or discard
            CardMgr::useBenefit($specials[SPECIAL_ROMANCE], SPECIAL_ROMANCE);
            CardMgr::notifyCards(CardMgr::drawCards(3, $player->getDeckLocation(), $player->getHandLocation()));
            $this->gamestate->nextState('romance');
            return;
        }

        $this->gamestate->nextState('next');
    }

    function computeSpecialMystery(array $tableau): void
    {
        foreach ($tableau as $card) {
            $benefits = $card->getBenefits(SPECIAL_MYSTERY);
            if (!empty($benefits)) {
                $benefit = reset($benefits);
                $specials[SPECIAL_MYSTERY] = $card;
                $wilds = 0;
                foreach ($tableau as $card) {
                    if ($card->isWild()) {
                        $wilds++;
                    }
                }
                $score = $wilds * $benefit['value'];
                $best = $this->getGameStateValue('bestSpecialMystery');
                if ($score > $best) {
                    $this->setGameStateValue('bestSpecialMystery', $score);
                }
            }
        }
    }

    function discardInk(): void
    {
        $active = PlayerMgr::getPlayer();
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $player->spendInk();
        $this->notifyAllPlayers('message', $this->msg['forceDiscard'], [
            'i18n' => ['icon'],
            'player_name' => $active->getName(),
            'player_name2' => $player->getName(),
            'amount' => 1,
            'icon' => 'ink',
        ]);
        $this->gamestate->setPlayerNonMultiactive($player->getId(), 'next');
    }

    function discardRemover(): void
    {
        $active = PlayerMgr::getPlayer();
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $player->spendRemover();
        $this->notifyAllPlayers('message', $this->msg['forceDiscard'], [
            'i18n' => ['icon'],
            'player_name' => $active->getName(),
            'player_name2' => $player->getName(),
            'amount' => 1,
            'icon' => 'remover',
        ]);
        $this->gamestate->setPlayerNonMultiactive($player->getId(), 'next');
    }

    function previewReturn(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation($player->getHandLocation())) {
            throw new BgaVisibleSystemException("previewReturn: Not possible for $player to use card $card");
        }
        CardMgr::previewReturn($card, $player->getDeckLocation());
        $player->notifyPanel();
        if (CardMgr::getHandCount($player->getId()) == 0) {
            $this->gamestate->nextState('next');
        }
    }

    function previewDiscard(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation($player->getHandLocation())) {
            throw new BgaVisibleSystemException("previewDiscard: Not possible for $player to use card $card");
        }
        CardMgr::discard($card, $player->getDiscardLocation());
        $player->notifyPanel();
        if (CardMgr::getHandCount($player->getId()) == 0) {
            $this->gamestate->nextState('next');
        }
    }

    /*
     * TRASH
     */

    function argTrash(): array
    {
        $player = PlayerMgr::getPlayer();
        $cardIds = [];
        $tableau = CardMgr::getTableau($player->getId());
        foreach ($tableau as $card) {
            if ($card->hasBenefit(TRASH_COINS) || $card->hasBenefit(TRASH_POINTS)) {
                $cardIds[] = $card->getId();
            }
        }
        return [
            'cardIds' => $cardIds,
            'skip' => empty($cardIds),
            'coins' => $player->getCoins(),
            'points' => $player->getScore() - $this->getGameStateValue('startScore'),
            'icon' => 'star',
        ];
    }

    function trash(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        if (!in_array($cardId, $this->gamestate->state()['args']['cardIds'])) {
            throw new BgaVisibleSystemException("trash: Not possible for $player to use card ID $cardId");
        }

        $card = CardMgr::getCard($cardId);
        $benefits = $card->getBenefits(TRASH_POINTS);
        if (empty($benefits)) {
            $benefits = $card->getBenefits(TRASH_COINS);
        }
        if (empty($benefits)) {
            $benefits = $card->getBenefits(TRASH_DISCARD);
        }
        if (empty($benefits)) {
            throw new BgaVisibleSystemException("trash: Not possible for $player to use card $card");
        }

        CardMgr::useBenefit($card, $benefits[0]['id']);
        CardMgr::discard($card, 'trash');
        $icon = null;
        $amount = $benefits[0]['value'];
        if ($benefits[0]['id'] == TRASH_POINTS) {
            $stat = $benefits[0]['activation'] == FROM_GENRE ? 'pointsGenre' : 'pointsBasic';
            $player->addPoints($amount, $stat);
            $icon = 'star';
        } else {
            $player->addCoins($amount);
            $icon = '¢';
        }
        self::notifyAllPlayers('message', $this->msg['trash'], [
            'player_name' => $player->getName(),
            'genre' => $card->getGenreName(),
            'letter' => $card->getLetter(),
            'amount' => $amount,
            'icon' => $icon,
        ]);
        $this->incStat(1, 'cardsTrash', $player->getId());
        $this->gamestate->nextState('again');
    }


    function argTrashDiscard(): array
    {
        $player = PlayerMgr::getPlayer();
        $sources = [];
        if ($player->getDiscardCount() > 0) {
            $tableau = CardMgr::getTableau($player->getId());
            foreach ($tableau as $card) {
                $benefits = $card->getBenefits(TRASH_DISCARD);
                if (!empty($benefits)) {
                    $sources[$card->getId()] = $benefits[0]['value'];
                }
            }
            arsort($sources);
        }
        return [
            'sourceIds' => array_keys($sources),
            'skip' => empty($sources),
            'amount' => reset($sources),
            'coins' => $player->getCoins(),
            'points' => $player->getScore() - $this->getGameStateValue('startScore'),
            'icon' => 'star',
        ];
    }

    function trashDiscard(int $cardId): void
    {
        $player = PlayerMgr::getPlayer();
        $sourceIds = $this->gamestate->state()['args']['sourceIds'];
        if (empty($sourceIds)) {
            throw new BgaVisibleSystemException("trashDiscard: Not possible for $player to trash any card");
        }
        $source = CardMgr::getCard($sourceIds[0]);
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation($player->getDiscardLocation())) {
            throw new BgaVisibleSystemException("trashDiscard: Not possible for $player to use card $card");
        }

        $amount = $this->gamestate->state()['args']['amount'];
        CardMgr::useBenefit($source, TRASH_DISCARD);
        CardMgr::discard($card, 'trash');
        $player->addCoins($amount);
        self::notifyAllPlayers('message', $this->msg['trash'], [
            'player_name' => $player->getName(),
            'genre' => $card->getGenreName(),
            'letter' => $card->getLetter(),
            'amount' => $amount,
            'icon' => '¢',
        ]);
        $this->incStat(1, 'cardsTrash', $player->getId());
        $this->gamestate->nextState('again');
    }

    /*
     * JAIL
     */

    function argJail(): array
    {
        $player = PlayerMgr::getPlayer();
        $sourceIds = [];
        $tableau = CardMgr::getTableau($player->getId());
        foreach ($tableau as $card) {
            if ($card->hasBenefit(JAIL)) {
                $sourceIds[] = $card->getId();
            }
        }
        $jailCard = CardMgr::getJail($player->getId());
        $jail = $jailCard ? ['genre' => $jailCard->getGenreName(), 'letter' => $jailCard->getLetter()] : null;
        return [
            'sourceIds' => $sourceIds,
            'skip' => empty($sourceIds),
            'coins' => $player->getCoins(),
            'points' => $player->getScore() - $this->getGameStateValue('startScore'),
            'icon' => 'star',
            'jail' => $jail,
        ];
    }

    function jail(int $cardId, string $choice): void
    {
        $player = PlayerMgr::getPlayer();
        $sourceIds = $this->gamestate->state()['args']['sourceIds'];
        if (empty($sourceIds)) {
            throw new BgaVisibleSystemException("jail: Not possible for $player to jail any card");
        }
        $source = CardMgr::getCard($sourceIds[0]);
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation('offer')) {
            throw new BgaVisibleSystemException("jail: Not possible for $player to use card $card");
        }

        CardMgr::useBenefit($source, JAIL);
        if ($choice == 'trash') {
            self::notifyAllPlayers('message', $this->msg['trashOffer'], [
                'player_name' => $player->getName(),
                'genre' => $card->getGenreName(),
                'letter' => $card->getLetter(),
            ]);
            CardMgr::discard($card, 'trash');
        } else {
            self::notifyAllPlayers('message', $this->msg['jailOffer'], [
                'player_name' => $player->getName(),
                'genre' => $card->getGenreName(),
                'letter' => $card->getLetter(),
            ]);
            CardMgr::jail($player->getId(), $card);
        }
        CardMgr::notifyCards(CardMgr::drawCards(1, 'deck', 'offer'));
        $this->gamestate->nextState('again');
    }

    /*
     * PHASE 4: PURCHASE NEW CARDS AND INK
     */

    function argFlush(): array
    {
        $player = PlayerMgr::getPlayer();
        return [
            'coins' => $player->getCoins(),
            'possible' => CardMgr::canFlushOffer(),
        ];
    }

    function stFlush(): void
    {
        // Summary of all earnings
        $player = PlayerMgr::getPlayer();
        $earnings = [
            '¢' => $player->getCoins(),
            'star' => $player->getScore() - $this->getGameStateValue('startScore'),
            'ink' => $player->getInk() - $this->getGameStateValue('startInk'),
            'remover' => $player->getRemover() - $this->getGameStateValue('startRemover'),
        ];

        $args = [
            'player_name' => $player->getName(),
        ];
        $i = 0;
        foreach ($earnings as $k => $v) {
            if ($v > 0) {
                $i++;
                $args["amount$i"] = $v;
                $args["icon$i"] = $k;
                if ($k == '¢') {
                    $this->incStat($v, 'coins', $player->getId());
                } else if ($k == 'ink' || $k == 'remover') {
                    $args['i18n'][] = "icon$i";
                }
            }
        }
        self::notifyAllPlayers('message', $this->msg["summary$i"], $args);

        if (!$this->gamestate->state()['args']['possible']) {
            $this->skip();
        }
    }

    function flush(): void
    {
        $player = PlayerMgr::getPlayer();
        if ($this->gamestate->state()['args']['possible']) {
            CardMgr::flushOffer();
        }
        self::notifyAllPlayers('message', $this->msg['flush'], [
            'player_name' => $player->getName(),
        ]);
        $this->skip();
    }


    function argPurchase(): array
    {
        $player = PlayerMgr::getPlayer();
        $offer = CardMgr::getOffer($player->getId());
        $cardIds = CardMgr::getIds(array_filter($offer, function ($card) use ($player) {
            return $player->getCoins() >= $card->getCost();
        }));
        $advert = null;
        if ($this->gamestate->table_globals[OPTION_ADVERTS]) {
            foreach ($this->adverts as $coins => $points) {
                if ($points > $player->getAdvert()) {
                    $advert = ['coins' => $coins, 'points' => $points, 'icon' => 'star'];
                    break;
                }
            }
        }
        $convert = null;
        $marginal = 999;
        foreach ($offer as $card) {
            $diff = $card->getCost() - $player->getCoins();
            if ($diff > 0 && $diff <= $marginal) {
                $marginal = $diff;
            }
        };
        if ($advert && $advert['coins'] > $player->getCoins()) {
            $diff = $advert['coins'] - $player->getCoins();
            if ($diff > 0 && $diff <= $marginal) {
                $marginal = $diff;
            }
            $advert = null;
        }
        if ($player->getInk() >= $marginal * 3) {
            $convert = ['coins' => $marginal, 'ink' => $marginal * 3];
        }
        return [
            'advert' => $advert,
            'cardIds' => $cardIds,
            'coins' => $player->getCoins(),
            'convert' => $convert,
            'skip' => $advert == null && $convert == null && empty($cardIds),
        ];
    }

    function purchase(int $cardId): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        if (!in_array($cardId, $this->gamestate->state()['args']['cardIds'])) {
            throw new BgaVisibleSystemException("purchase: Not possible for $player to use card ID $cardId");
        }

        $card = CardMgr::getCard($cardId);
        $oldLocation = $card->getLocation();
        CardMgr::discard($card, $player->getDiscardLocation());
        $msg = 'purchase';
        $args = [
            'player_name' => $player->getName(),
            'genre' => $card->getGenreName(),
            'letter' => $card->getLetter(),
            'coins' => $card->getCost(),
            'iconCoins' => '¢',
        ];
        if ($card->getPoints() > 0) {
            $msg = 'purchase2';
            $args['points'] = $card->getPoints();
            $args['iconPoints'] = 'star';
            $player->addPoints($card->getPoints(), 'pointsPurchase');
        }
        $player->spendCoins($card->getCost());
        self::notifyAllPlayers('message', $this->msg[$msg], $args);

        $penny = PlayerMgr::getPenny();
        if ($penny->isGenreActive(ADVENTURE)) {
            // Adventure: Penny earns 1 per purchase
            $penny->addPoints(1);
            self::notifyAllPlayers('message', $this->msg['earn'], [
                'player_name' => $penny->getName(),
                'amount' => 1,
                'icon' => 'star',
            ]);
        }

        if ($oldLocation == 'offer') {
            CardMgr::notifyCards(CardMgr::drawCards(1, 'deck', 'offer'));
        }
        $this->incStat(1, 'cardsPurchase', $player->getId());
        $this->gamestate->nextState('again');
    }

    function convert(): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $convert = $this->gamestate->state()['args']['convert'];
        if ($convert == null) {
            throw new BgaVisibleSystemException("convert: Not possible for $player to convert ink");
        }

        $player->spendInk($convert['ink'], false);
        $player->addCoins($convert['coins']);
        self::notifyAllPlayers('message', $this->msg['convert'], [
            'player_name' => $player->getName(),
            'ink' => $convert['ink'],
            'amount' => $convert['coins'],
            'icon' => '¢',
        ]);

        $penny = PlayerMgr::getPenny();
        if ($penny->isGenreActive(HORROR)) {
            // Horror: Penny earns 1 per ink
            $penny->addPoints($convert['ink']);
            self::notifyAllPlayers('message', $this->msg['earn'], [
                'player_name' => $penny->getName(),
                'amount' => $convert['ink'],
                'icon' => 'star',
            ]);
        }

        $this->gamestate->nextState('again');
    }

    function advert(): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $advert = $this->gamestate->state()['args']['advert'];
        if ($advert == null) {
            throw new BgaVisibleSystemException("advert: Not possible for $player to buy any advert");
        }

        $player->buyAdvert($advert['points'], $advert['coins']);
        $this->notifyAllPlayers('message', $this->msg['purchaseAdvert'], [
            'player_name' => $player->getName(),
            'points' => $advert['points'],
            'iconPoints' => 'star',
            'coins' => $advert['coins'],
            'iconCoins' => '¢',
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

    function skipWord(): void
    {
        $this->gamestate->nextState('skip');
    }

    function stSkipTurn(): void
    {
        $player = PlayerMgr::getPlayer();
        self::notifyAllPlayers('message', $this->msg['skipWord'], [
            'player_name' => $player->getName(),
            'duration' => 2000,
        ]);

        // Reset hand and tableau
        CardMgr::reset($player->getId(), true);
        $player->notifyPanel();
        $this->gamestate->nextState('next');
    }

    function stCleanup(): void
    {
        $player = PlayerMgr::getPlayer();
        $amount = $player->getCoins();
        if ($amount > 0) {
            // Purchase ink with remaining coins
            $player->spendCoins($amount, false);
            $player->addInk($amount);
            $this->notifyAllPlayers('pause', $this->msg['endTurnInk'], [
                'player_name' => $player->getName(),
                'amount' => $amount,
                'duration' => 2000,
            ]);
        } else {
            $this->notifyAllPlayers('pause', $this->msg['endTurn'], [
                'player_name' => $player->getName(),
                'duration' => 2000,
            ]);
        }

        // Reset hand and tableau
        CardMgr::reset($player->getId());
        $player->notifyPanel();
        $this->gamestate->nextState('next');
    }

    /*
     * COOPERATIVE ANTHOLOGY
     */

    function stCoopTurn()
    {
        $player = PlayerMgr::getPlayer();
        if ($this->gamestate->table_globals[OPTION_COOP] == NO || $player->isEliminated() || $player->isZombie()) {
            $this->gamestate->nextState('next');
            return;
        }

        // Check for win
        $penny = PlayerMgr::getPenny();
        if ($this->getGameProgression() >= 100) {
            $this->gamestate->nextState('end');
            return;
        }

        $offer = CardMgr::getOffer();
        $card = end($offer);
        $points = $card->getCost();
        if ($penny->getGenre() == ROMANCE && $card->getGenre() == ROMANCE) {
            // Romance: Penny earns double
            $points *= 2;
        }

        // Discard the last card
        CardMgr::discard($card, $penny->getDiscardLocation());
        $penny->addPoints($points);
        $this->incStat(1, 'coopTurns');
        $turns = $this->getStat('coopTurns');
        $avg = $this->getStat('coopAvg');
        $avg = $avg + (($points - $avg) / $turns);
        $this->setStat($avg, 'coopAvg');
        self::notifyAllPlayers('message', $this->msg['purchaseCoop'], [
            'player_name' => $penny->getName(),
            'genre' => $card->getGenreName(),
            'letter' => $card->getLetter(),
            'points' => $points,
            'iconPoints' => 'star',
        ]);

        // Draw a new card
        $draw = CardMgr::drawCards(1, 'deck', 'offer');
        CardMgr::notifyCards($draw);
        $draw = reset($draw);
        unset($offer[$card->getId()]);
        $offer[$draw->getId()] = $draw;
        if ($penny->getGenre() == MYSTERY && $card->getGenre() == MYSTERY) {
            // Mystery: Penny discards cheapest
            CardMgr::sortCards($offer, 'cost');
            $card = reset($offer);
            self::notifyAllPlayers('message', $this->msg['trashOffer'], [
                'player_name' => $penny->getName(),
                'genre' => $card->getGenreName(),
                'letter' => $card->getLetter(),
            ]);
            CardMgr::discard($card, $penny->getDiscardLocation());
            $penny->notifyPanel();
            CardMgr::notifyCards(CardMgr::drawCards(1, 'deck', 'offer'));
        }

        // Discard timeless cards
        $timeless = $player->getTimeless();
        $discardIds = [];
        foreach ($timeless as $card) {
            $countOffer = $this->getGameStateValue("countOffer{$card->getGenre()}");
            if ($countOffer > 0) {
                $discardIds[] = $card->getId();
                self::notifyAllPlayers('message', $this->msg['forceTimeless'], [
                    'player_name' => $penny->getName(),
                    'player_name2' => $player->getName(),
                    'genre' => $card->getGenreName(),
                    'letter' => $card->getLetter(),
                ]);
            }
        }
        if (!empty($discardIds)) {
            CardMgr::discard($discardIds, $player->getDiscardLocation());
            $player->notifyPanel();
        }

        // Pause
        $this->notifyAllPlayers('pause', '', ['duration' => 2000]);

        // Check for lose
        if ($penny->getScore() >= $this->getGameLength()) {
            $this->gamestate->nextState('end');
        } else {
            $this->gamestate->nextState('next');
        }
    }

    /*
     * PHASE 7: DRAW YOUR NEXT HAND
     */

    function stNextPlayer(): void
    {
        // Activate next player
        $this->activeNextPlayer();
        $player = PlayerMgr::getPlayer();

        if ($player->getOrder() == 1) {
            // Check for game end
            $this->incStat(1, 'turns');
            if ($this->getGameProgression() >= 100) {
                self::DbQuery('UPDATE player SET player_score_aux = ink');
                $this->gamestate->nextState('end');
                return;
            }
        }

        // Give extra time
        $this->giveExtraTime($player->getId());
        $this->gamestate->nextState('playerTurn');
    }

    /*
     * PHASE 8: USE INK AND REMOVER
     */

    function useInk(): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $location = $player->isActive() ? 'tableau' : $player->getHandLocation();
        $cards = CardMgr::drawCards(1, $player->getDeckLocation(), $location, $player->getHandLocation());
        if (empty($cards)) {
            throw new BgaUserException($this->msg['errorEmpty']);
        }
        $card = reset($cards);
        $player->spendInk();
        if ($player->isActive()) {
            $player->notifyInk($card);
            $penny = PlayerMgr::getPenny();
            if ($penny->isGenreActive(HORROR)) {
                // Horror: Penny earns 1 per ink
                $penny->addPoints(1);
                self::notifyAllPlayers('message', $this->msg['earn'], [
                    'player_name' => $penny->getName(),
                    'amount' => 1,
                    'icon' => 'star',
                ]);
            }
        }
        CardMgr::inkCard($card);
        $this->incStat(1, 'useInk', $player->getId());
    }

    function useRemover(int $cardId): void
    {
        $player = PlayerMgr::getPlayer(self::getCurrentPlayerId());
        $card = CardMgr::getCard($cardId);
        if ($card == null || !$card->isLocation($player->getHandLocation(), 'tableau')) {
            throw new BgaVisibleSystemException("useRemover: Card $card is unavailable to $player");
        }
        $player->spendRemover();
        if ($player->isActive()) {
            $player->notifyRemover($card);
        }
        CardMgr::inkCard($card, HAS_REMOVER);
        $this->incStat(1, 'useRemover', $player->getId());
    }

    /*
     * END OF GAME
     */

    function stEnd(): void
    {
        // Literary award points
        if ($this->gamestate->table_globals[OPTION_AWARDS]) {
            $winner = $this->getGameStateValue('awardWinner');
            if ($winner) {
                $length = min($this->getStat('longestWord'), 12);
                $points = $this->awards[$length];
                $player = PlayerMgr::getPlayer($winner);
                $player->addPoints($points, 'pointsAward');
            }
        }

        if ($this->gamestate->table_globals[OPTION_COOP]) {
            $penny = PlayerMgr::getPenny();
            if ($penny->getScore() >= PlayerMgr::getMaxScore()) {
                // Coop lose
                self::DbQuery("UPDATE player SET player_score = player_score - {$this->getGameLength()}");
                self::notifyAllPlayers('message', $this->msg['coopLose'], [
                    'player_name' => $penny->getName(),
                ]);
            } else {
                // Coop win
                self::notifyAllPlayers('message', $this->msg['coopWin'], [
                    'player_name' => $penny->getName(),
                ]);
            }
        } else {
            // Ink is the tiebreaker
            self::DbQuery('UPDATE player SET player_score_aux = ink');
        }

        // Discard all cards
        CardMgr::endGameDiscard();
        CardMgr::deletePreviewNotifications();
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
        if ($state['type'] == 'multipleactiveplayer') {
            $this->gamestate->setPlayerNonMultiactive($active_player, 'next');
        } else {
            $this->gamestate->nextState('zombie');
        }
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

    ///////////////////////////////////////////////////////////////////////////////////:
    ////////// Production bug report handler
    //////////

    public function loadBug($reportId)
    {
        $db = explode('_', self::getUniqueValueFromDB("SELECT SUBSTRING_INDEX(DATABASE(), '_', -2)"));
        $game = $db[0];
        $tableId = $db[1];
        self::notifyAllPlayers('loadBug', "Trying to load <a href='https://boardgamearena.com/bug?id=$reportId' target='_blank'>bug report $reportId</a>", [
            'urls' => [
                "https://studio.boardgamearena.com/admin/studio/getSavedGameStateFromProduction.html?game=$game&report_id=$reportId&table_id=$tableId",
                "https://studio.boardgamearena.com/table/table/loadSaveState.html?table=$tableId&state=1",
                "https://studio.boardgamearena.com/1/$game/$game/loadBugSQL.html?table=$tableId&report_id=$reportId",
                "https://studio.boardgamearena.com/admin/studio/clearGameserverPhpCache.html?game=$game",
            ]
        ]);
    }

    public function loadBugSQL($reportId)
    {
        $studioPlayer = self::getCurrentPlayerId();
        $playerIds = self::getObjectListFromDb("SELECT player_id FROM player", true);

        $sql = [
            "UPDATE global SET global_value=2 WHERE global_id=1 AND global_value=99"
        ];
        foreach ($playerIds as $pId) {
            $sql[] = "UPDATE player SET player_id=$studioPlayer WHERE player_id=$pId";
            $sql[] = "UPDATE global SET global_value=$studioPlayer WHERE global_value=$pId";
            $sql[] = "UPDATE stats SET stats_player_id=$studioPlayer WHERE stats_player_id=$pId";
            $sql[] = "UPDATE card SET `location`=REPLACE(`location`, $pId, $studioPlayer)";
            $sql[] = "UPDATE card SET `origin`=REPLACE(`origin`, $pId, $studioPlayer)";
            $studioPlayer++;
        }
        $msg = "<b>Loaded <a href='https://boardgamearena.com/bug?id=$reportId' target='_blank'>bug report $reportId</a></b><hr><ul><li>" . implode(';</li><li>', $sql) . ';</li></ul>';
        self::warn($msg);
        self::notifyAllPlayers('message', $msg, []);

        foreach ($sql as $q) {
            self::DbQuery($q);
        }
        self::reloadPlayersBasicInfos();
    }
}
