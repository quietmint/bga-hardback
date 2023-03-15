<?php

class CardMgr extends APP_GameClass
{
    private static function populateCards(array $dbcards): array
    {
        $cards = array_map(function ($dbcard) {
            return new HCard($dbcard);
        }, $dbcards);
        $sequence = array_values($cards);
        $count = count($cards);
        $i = 0;
        foreach ($cards as &$card) {
            if ($i > 0) {
                $prev = $sequence[$i - 1];
                if ($card->getLocation() == $prev->getLocation()) {
                    $card->setPrevious($prev);
                }
            }
            if ($i < $count - 1) {
                $next = $sequence[$i + 1];
                if ($card->getLocation() == $next->getLocation()) {
                    $card->setNext($next);
                }
            }
            $i++;
        }
        return $cards;
    }

    public static function setup(): void
    {
        // Create genre cards
        // Deal 7 to the offer row
        $cards = hardback::$instance->cards;
        $create = [];
        foreach ($cards as $refId => $ref) {
            if ($ref['genre'] != H_STARTER) {
                $create[] = "($refId, 'discard', 'discard')";
            }
        }
        $sql = "INSERT INTO card (`refId`, `location`, `origin`) VALUES " . implode(', ', $create);
        self::DbQuery($sql);
        self::drawCards(7, 'deck', 'offer');

        // Create starter cards
        // Deal 5 to each player
        $starterCoins = [];
        $starterPoints = [];
        foreach ($cards as $refId => $ref) {
            if ($ref['genre'] == H_STARTER) {
                if (array_key_exists(H_COINS, $ref['basicBenefits'])) {
                    $starterCoins[] = $refId;
                } else {
                    $starterPoints[] = $refId;
                }
            }
        }
        shuffle($starterPoints);
        $playerIds = PlayerMgr::getPlayerIds();
        foreach ($playerIds as $playerId) {
            $two = array_splice($starterPoints, 0, 2);
            sort($two);
            hardback::$instance->setStat(ord($cards[$two[0]]['letter']), 'starterCard1', $playerId);
            hardback::$instance->setStat(ord($cards[$two[1]]['letter']), 'starterCard2', $playerId);
            $starter = array_merge($starterCoins, $two);

            $location = self::getDiscardLocation($playerId);
            $create = [];
            foreach ($starter as $refId) {
                $create[] = "($refId, '$location', '$location')";
            }
            $sql = "INSERT INTO card (`refId`, `location`, `origin`) VALUES " . implode(', ', $create);
            self::DbQuery($sql);
            self::drawCards(5, self::getDrawLocation($playerId), self::getTableauLocation($playerId), self::getHandLocation($playerId));
        }
    }

    public static function drawCards(int $count, string $fromLocation, string $toLocation, string $origin = null, bool $returnReshuffle = false): array
    {
        $order = self::getStartOfLocation($toLocation);

        // Draw cards from deck
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($fromLocation) . " ORDER BY `location`, `order` LIMIT $count";
        $ids = self::getObjectListFromDB($sql, true);

        $missing = $count - count($ids);
        $reshuffle = [];
        if ($missing > 0) {
            // Rehuffle and continue drawing
            $shuffleIds = self::reshuffleDeck($fromLocation);
            $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($fromLocation);
            if (!empty($ids)) {
                $sql .= " AND `id` NOT IN (" . implode(',', $ids) . ")";
            }
            $sql .= " ORDER BY `location`, `order` LIMIT $missing";
            $ids = array_merge($ids, self::getObjectListFromDB($sql, true));
            if ($fromLocation != 'deck') {
                $reshuffleIds = array_values(array_diff($shuffleIds, $ids));
                $reshuffle = self::getCards($reshuffleIds);
                foreach ($reshuffle as $reshuffleCard) {
                    $reshuffleCard->setOrder(-1);
                }
            }
        }

        // Populate from database and sort
        $cards = self::getCards($ids);

        // Reposition in front and update origin
        if ($origin == null) {
            $origin = $toLocation;
        }
        foreach ($cards as &$card) {
            $order--;
            self::DbQuery("UPDATE card SET `origin` = '$origin', `location` = '$toLocation', `order` = $order WHERE `id` = {$card->getId()}");
            $card->setLocation($toLocation);
            $card->setOrigin($origin);
            $card->setOrder($order);
        }

        if (!empty($reshuffle)) {
            // Required to return or notify reshuffled cards
            if ($returnReshuffle) {
                $cards = array_merge($cards, $reshuffle);
            } else {
                self::notifyCards($reshuffle);
            }
        }

        return $cards;
    }

    public static function reshuffleDeck(string $location): array
    {
        $shuffleLocation = str_replace(['draw', 'deck'], ['discard', 'discard'], $location);
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($shuffleLocation);
        $ids = self::getObjectListFromDB($sql, true);
        shuffle($ids);
        $order = 0;
        foreach ($ids as $id) {
            self::DbQuery("UPDATE card SET `origin` = '$location', `location` = '$location', `order` = $order WHERE `id` = $id");
            $order++;
        }
        return $ids;
    }

    public static function sortCards(array &$cards, string $sort): void
    {
        if ($sort == 'letter') {
            uasort($cards, function ($a, $b) {
                $result = strcmp($a->getLetter(), $b->getLetter());
                if ($result == 0) {
                    $result = $a->getOrder() - $b->getOrder();
                }
                if ($result == 0) {
                    $result = $a->getId() - $b->getId();
                }
                return $result;
            });
        } else if ($sort == 'cost') {
            uasort($cards, function ($a, $b) {
                $result = $a->getCost() - $b->getCost();
                if ($result == 0) {
                    $result = strcmp($a->getLetter(), $b->getLetter());
                }
                if ($result == 0) {
                    $result = $a->getOrder() - $b->getOrder();
                }
                if ($result == 0) {
                    $result = $a->getId() - $b->getId();
                }
                return $result;
            });
        } else {
            throw new BgaVisibleSystemException("sortCards: Invalid sort order: $sort");
        }
    }

    /* Query (generic) */

    public static function getCard(int $cardId): HCard
    {
        $cards = self::getCards([$cardId]);
        return array_shift($cards);
    }

    public static function getCards($cardIds, string $wildMask = null): array
    {
        $cardIds = self::getIds($cardIds);
        if (empty($cardIds)) {
            return [];
        }
        $sql = "SELECT card.*, JSON_ARRAYAGG(resolve.benefit_id) AS resolve FROM card LEFT OUTER JOIN resolve USING (id) WHERE id IN (" . implode(',', $cardIds) . ") GROUP BY card.id";
        $dbcards = self::getCollectionFromDB($sql);

        // Reorder to match input
        $seqcards = [];
        foreach ($cardIds as $cardId) {
            if (array_key_exists($cardId, $dbcards)) {
                $seqcards[$cardId] = $dbcards[$cardId];
            }
        }
        $cards = self::populateCards($seqcards);

        // Apply wild mask
        if ($wildMask) {
            $countCards = count($cards);
            $countWilds = strlen($wildMask);
            if ($countCards != $countWilds) {
                throw new BgaVisibleSystemException("getCards: Invalid wildcard mask (cards: $countCards, wilds: $countWilds)");
            }
            $wilds = array_combine($cardIds, str_split($wildMask));
            foreach ($cards as $card) {
                $wild = $wilds[$card->getId()];
                $card->setWild($wild != '_' ? $wild : null);
            }
        }

        return $cards;
    }

    private static function getLocationWhereClause($locations): string
    {
        $parts = [];
        $in = [];
        if (!is_array($locations)) {
            $locations = [$locations];
        }
        foreach ($locations as $location) {
            if (strpos($location, '%') !== false) {
                $parts[] = "`location` LIKE '" . $location . "'";
            } else {
                $in[] = $location;
            }
        }
        if (!empty($in)) {
            $parts[] = "`location` IN ('" . implode("', '", $in) . "')";
        }
        return "(" . implode(" OR ", $parts) . ")";
    }

    public static function getCardsInLocation($locations, int $inkValue = null, string $sort = null): array
    {
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($locations);
        if ($inkValue != null) {
            $sql .= " AND `ink` = $inkValue";
        }
        if ($sort == 'jail') {
            // Sort timeless classics by age, then other cards by order
            $sql .= " ORDER BY CASE WHEN `origin` LIKE 'timeless_%' THEN 0 ELSE 1 END, CASE WHEN `origin` LIKE 'timeless_%' THEN `age` ELSE `order` END";
        } else {
            $sql .= " ORDER BY `location`, `order`";
        }
        $cardIds = self::getObjectListFromDB($sql, true);
        return self::getCards($cardIds);
    }

    public static function getCountInLocation(string $location): int
    {
        $count = self::getUniqueValueFromDB("SELECT COUNT(*) FROM card WHERE `location` = '$location'");
        return intval($count);
    }

    public static function getStartOfLocation(string $location): int
    {
        return intval(self::getUniqueValueFromDB("SELECT COALESCE(MIN(`order`), 0) FROM card WHERE `location` = '$location'"));
    }

    public static function getHandLocation(int $playerId): string
    {
        return "hand_$playerId";
    }

    public static function getDrawLocation(int $playerId): string
    {
        return "draw_$playerId";
    }

    public static function getDiscardLocation(int $playerId): string
    {
        return "discard_$playerId";
    }

    public static function getJailLocation(int $playerId): string
    {
        return "jail_$playerId";
    }

    public static function getTableauLocation(int $playerId): string
    {
        return "tableau_$playerId";
    }

    public static function getTimelessLocation(int $playerId): string
    {
        return "timeless_$playerId";
    }

    /* Query (specific) */

    public static function getHand(int $playerId, int $inkValue = null): array
    {
        return self::getCardsInLocation(self::getHandLocation($playerId), $inkValue);
    }

    public static function getHandCount(int $playerId): int
    {
        return self::getCountInLocation(self::getHandLocation($playerId));
    }

    public static function getDrawCount(int $playerId): int
    {
        return self::getCountInLocation(self::getDrawLocation($playerId));
    }

    public static function getDiscard(int $playerId): array
    {
        return self::getCardsInLocation(self::getDiscardLocation($playerId));
    }

    public static function getDiscardCount(int $playerId): int
    {
        return self::getCountInLocation(self::getDiscardLocation($playerId));
    }

    public static function getTableau(int $playerId = null, string $sort = null, bool $includeTimeless = true): array
    {
        $locations = [self::getTableauLocation($playerId)];
        if ($includeTimeless) {
            $locations[] = self::getTimelessLocation($playerId);
        }
        return self::getCardsInLocation($locations, null, $sort);
    }

    public static function getTableauCount(int $playerId): int
    {
        return self::getCountInLocation(self::getTableauLocation($playerId));
    }

    public static function getTimeless(int $playerId, bool $origin = false): array
    {
        $location = self::getTimelessLocation($playerId);
        if ($origin) {
            $sql = "SELECT `id` FROM card WHERE `origin` = '$location' ORDER BY `age`";
            $cardIds = self::getObjectListFromDB($sql, true);
            return self::getCards($cardIds);
        } else {
            return self::getCardsInLocation($location, null);
        }
    }

    public static function getOffer(int $playerId = null): array
    {
        $cards = self::getCardsInLocation('offer');
        if ($playerId) {
            $jail = self::getJail($playerId);
            if ($jail) {
                $cards[$jail->getId()] = $jail;
            }
        }
        return $cards;
    }

    public static function getJail(int $playerId): ?HCard
    {
        $card = null;
        $jailId = self::getJailId($playerId);
        if ($jailId) {
            $card = self::getCard($jailId);
        }
        return $card;
    }

    private static function getJailId(int $playerId): int
    {
        $location = self::getJailLocation($playerId);
        return intval(self::getUniqueValueFromDB("SELECT `id` FROM card WHERE `location` = '$location'", true));
    }

    public static function getOfferDeckCount(): int
    {
        return self::getCountInLocation('deck');
    }

    /* Array utilties */

    public static function getIds($cards): array
    {
        $ids = [];
        if ($cards == null) {
            return $ids;
        }
        if (!is_array($cards)) {
            $cards = [$cards];
        }
        foreach ($cards as $c) {
            if ($c instanceof HCard) {
                $ids[] = $c->getId();
            } else if (is_numeric($c)) {
                $ids[] = $c;
            } else if (is_array($c)) {
                $ids[] = intval($c['id']);
            }
        }
        return $ids;
    }

    public static function getString($cards): string
    {
        $string = '';
        if (is_array($cards) && !empty($cards)) {
            $string = implode(', ', array_map(function ($card) {
                return (string) $card;
            }, $cards));
        }
        return $string;
    }

    public static function getGenreCounts($cards, bool $includeWilds = false): array
    {
        $counts = [];
        if (is_array($cards) && !empty($cards)) {
            if (!$includeWilds) {
                $cards = array_filter($cards, function ($card) {
                    return !$card->isWild();
                });
            }
            $counts = array_count_values(array_map(function ($card) {
                return $card->getGenre();
            }, $cards));
        }
        return [
            $counts[H_STARTER] ?? 0,
            $counts[H_ADVENTURE] ?? 0,
            $counts[H_HORROR] ?? 0,
            $counts[H_MYSTERY] ?? 0,
            $counts[H_ROMANCE] ?? 0,
        ];
    }

    /* Notify utilities */

    public static function notifyCards($cards, int $ignorePlayerId = null): void
    {
        if ($cards instanceof HCard) {
            $cards = [
                $cards->getId() => $cards
            ];
        }
        if (!empty($cards)) {
            $action = 'cards';
            $args = ['cards' => $cards];
            if ($ignorePlayerId) {
                $action = 'cardsPreview';
                $args['ignorePlayerId'] = $ignorePlayerId;
                hardback::$instance->not_a_move_notification = true;
            }
            hardback::$instance->notifyAllPlayers($action, '', $args);
        }
    }

    public static function deletePreviewNotifications(): void
    {
        // Delete transient preview notifications
        self::DbQuery("DELETE FROM `gamelog` WHERE gamelog_move_id IS NULL AND gamelog_notification LIKE '%cardsPreview%'");
    }

    /* Change (specific) */

    public static function endGameCleanup(): void
    {
        $locations = [];
        foreach (PlayerMgr::getPlayers() as $player) {
            $location = $player->getTableauLocation();
            $locations[] = $location;
            $sql = "UPDATE card SET `ink` = NULL, `wild` = NULL, `factor` = 1, `origin` = '$location', `location` = '$location', `order` = -1 WHERE `origin` LIKE '%_{$player->getId()}'";
            self::DbQuery($sql);
        }
        self::notifyCards(self::getCardsInLocation($locations));
    }

    public static function previewWord(int $playerId, array $hand, array $tableau = null, $notify = true): void
    {
        $updatedIds = [];

        // Update hand
        $oldHand = self::getHand($playerId);
        $order = 0;
        $handLocation = self::getHandLocation($playerId);
        foreach ($hand as $card) {
            $sql = "UPDATE card SET `wild` = " . ($card->isWild() ? "'{$card->getLetter()}'" : "NULL") . ", `location` = '$handLocation', `order` = $order WHERE `id` = {$card->getId()}";
            self::DbQuery($sql);
            $order++;
            $updatedIds[] = $card->getId();
        }

        // Update tableau
        $oldTableau = [];
        $tableauLocation = self::getTableauLocation($playerId, null, false);
        if ($tableau !== null) {
            $oldTableau = self::getTableau($playerId);
            $order = 0;
            foreach ($tableau as $card) {
                $sql = "UPDATE card SET `wild` = " . ($card->isWild() ? "'{$card->getLetter()}'" : "NULL") . ", `location` = '$tableauLocation', `order` = $order WHERE `id` = {$card->getId()}";
                self::DbQuery($sql);
                $order++;
                $updatedIds[] = $card->getId();
            }
        }

        // Return remaining cards to origin
        $remainder = [];
        foreach ($oldHand as $card) {
            if (!in_array($card->getId(), $updatedIds)) {
                $remainder[] = $card;
            }
        }
        foreach ($oldTableau as $card) {
            if (!in_array($card->getId(), $updatedIds)) {
                $remainder[] = $card;
            }
        }
        $updatedIds = array_merge($updatedIds, self::discardToOrigin($remainder));

        // Notify
        if ($notify) {
            self::notifyCards(self::getCards($updatedIds), $playerId);
        }
    }

    public static function preCommitWord(int $playerId, array $cards): array
    {
        self::deletePreviewNotifications();

        // Return remaining cards to origin
        $updatedIds = [];
        $ids = self::getIds($cards);
        $remainder = array_filter(self::getTableau($playerId, null, false), function ($card) use ($ids) {
            return !in_array($card->getId(), $ids);
        });
        $updatedIds = self::discardToOrigin($remainder);

        // Update tableau
        $order = 0;
        $tableauLocation = self::getTableauLocation($playerId);
        foreach ($cards as $card) {
            $sql = "UPDATE card SET `wild` = " . ($card->isWild() ? "'{$card->getLetter()}'" : "NULL") . ", `location` = '$tableauLocation', `order` = $order, `age` = COALESCE(`age`, SYSDATE(6)) WHERE `id` = {$card->getId()}";
            self::DbQuery($sql);
            $order++;
            $updatedIds[] = $card->getId();
        }
        return $updatedIds;
    }

    public static function commitWord(int $playerId, array $cards): void
    {
        $updatedIds = self::preCommitWord($playerId, $cards);
        $opponent = [];
        foreach ($cards as $card) {
            // Cancel all benefits on opponent timeless cards
            if ($card->isOrigin('timeless') && !$card->isOrigin(self::getTimelessLocation($playerId))) {
                self::useBenefit($card, H_ALL_BENEFITS);
                $opponent[] = $card;
            }
        }

        // Discard unused cards
        $discardIds = self::getIds(self::getHand($playerId));
        if (!empty($discardIds)) {
            $updatedIds = array_merge($updatedIds, $discardIds);
            self::discard($discardIds, self::getDiscardLocation($playerId), false);
        }

        // Compute active genres
        // Add own timeless cards
        // Subtract opponent timeless cards
        $counts = self::getGenreCounts($cards);
        $myCounts = self::getGenreCounts(self::getTimeless($playerId));
        $opponentCounts = self::getGenreCounts($opponent);
        foreach ($counts as $genre => $count) {
            if ($genre != H_STARTER) {
                $val = $count + $myCounts[$genre] - $opponentCounts[$genre];
                hardback::$instance->setGameStateValue("countActive$genre", $val);
            }
        }

        // Notify
        self::notifyCards(self::getCards($updatedIds));
    }

    public static function isGenreActive(int $genre): bool
    {
        return $genre != H_STARTER && hardback::$instance->getGameStateValue("countActive$genre") >= 2;
    }

    public static function discardToOrigin($cards): array
    {
        $updatedIds = [];
        foreach ($cards as $card) {
            $card->setOrder(self::getStartOfLocation($card->getOrigin()) - 1);
            $sql = "UPDATE card SET `wild` = NULL, `location` = `origin`, `order` = {$card->getOrder()} WHERE `id` = {$card->getId()}";
            self::DbQuery($sql);
            $updatedIds[] = $card->getId();
        }
        return $updatedIds;
    }

    public static function getTrashLocation(HCard $card): string
    {
        // Starter cards are trashed forever
        // Non-starter cards can eventually reshuffle
        return $card->getGenre() == H_STARTER ? "trash" : "discard";
    }

    public static function discard($cards, string $location, bool $notify = true): void
    {
        $updatedIds = self::getIds($cards);
        if (!empty($updatedIds)) {
            $sql = "UPDATE card SET ";
            if (strpos($location, 'discard') !== false) {
                $sql .= "`age` = NULL, ";
            }
            $sql .= "`ink` = NULL, `wild` = NULL, `factor` = 1, `origin` = '$location', `location` = '$location', `order` = -1 WHERE `id` IN (" . implode(',', $updatedIds) . ")";
            self::DbQuery($sql);

            // Notify
            if ($notify) {
                self::notifyCards(self::getCards($updatedIds));
            }
        }
    }

    public static function previewReturn($card, $location)
    {
        self::DbQuery("UPDATE card SET `order` = `order` + 1 WHERE `location` = '$location' AND `order` >= {$card->getOrder()}");
        self::DbQuery("UPDATE card SET `ink` = NULL, `wild` = NULL, `factor` = 1, `origin` = '$location', `location` = '$location' WHERE id = {$card->getId()}");
        $card->setLocation($location);
        $card->setOrigin($location);
        self::notifyCards($card);
    }

    public static function reset(int $playerId, bool $skipWord = false): void
    {
        $cards = self::getCardsInLocation([self::getHandLocation($playerId), self::getTableauLocation($playerId)]);
        $updatedIds = self::getIds($cards);

        // Clear used benefits
        self::DbQuery('DELETE FROM resolve');

        // Disposition cards
        $discardIds = [];
        foreach ($cards as $card) {
            if ($card->isTimeless() && !$card->isWild() && $card->isLocation('tableau')) {
                $owner = $card->getOwner() ?? $playerId;
                $keep = false;
                if ($skipWord) {
                    $keep = $card->isOrigin('timeless'); // keep already-played
                } else if (hardback::$instance->gamestate->table_globals[H_OPTION_COOP] != H_NO) {
                    $keep = $owner == $playerId || $card->isOrigin('timeless'); // keep mine and already-played
                } else {
                    $keep = $owner == $playerId; // keep mine
                }
                if ($keep) {
                    // Remain in play for owner
                    self::discard($card, self::getTimelessLocation($owner), false);
                } else {
                    // Discard to owner
                    self::discard($card, self::getDiscardLocation($owner), false);
                }
            } else {
                // Discard normally
                $discardIds[] = $card->getId();
            }
        }

        // Discard remaining cards
        self::discard($discardIds, self::getDiscardLocation($playerId), false);
        self::notifyCards(self::getCards($updatedIds));

        // Draw new hand
        $handAndReshuffle = self::drawCards(5, self::getDrawLocation($playerId), self::getTableauLocation($playerId), self::getHandLocation($playerId), true);
        self::notifyCards($handAndReshuffle);
    }

    public static function canFlushOffer(): bool
    {
        if (hardback::$instance->gamestate->table_globals[H_OPTION_COOP] != H_NO) {
            return false;
        }

        $offer = self::getOffer(); // without jail

        $costCondition = count(array_filter($offer, function ($card) {
            return $card->getCost() >= 6;
        }));
        if ($costCondition >= 4) {
            return true;
        }

        $genreCondition = max(self::getGenreCounts($offer));
        if ($genreCondition >= 4) {
            return true;
        }

        return false;
    }

    public static function flushOffer(): void
    {
        $updatedIds = self::getIds(self::getCardsInLocation('offer'));

        // Trash offer and notify
        self::discard($updatedIds, 'discard');
        hardback::$instance->incStat(1, 'flush');

        // Draw new offer and notify
        self::notifyCards(self::drawCards(7, 'deck', 'offer'));
    }

    public static function inkCard(HCard $card, int $inkValue = H_HAS_INK): void
    {
        $sql = "UPDATE card SET `ink` = $inkValue, `wild` = NULL WHERE `id` = {$card->getId()}";
        self::DbQuery($sql);
        $card->setInk($inkValue);
    }

    public static function uncover(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, H_UNCOVER_ADJ);
        self::DbQuery("UPDATE card SET `wild` = '_' WHERE `id` = {$card->getId()}");
        $card->setWild('_');
        if ($card->getGenre() != H_STARTER) {
            hardback::$instance->incGameStateValue("countActive{$card->getGenre()}", 1);
        }

        // Notify
        self::notifyCards($card);
    }

    public static function double(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, H_DOUBLE_ADJ);
        self::DbQuery("UPDATE card SET `factor` = `factor` + 1 WHERE `id` = {$card->getId()}");
        $card->setFactor($card->getFactor() + 1);

        // Notify
        self::notifyCards($card);
    }

    public static function jail(int $playerId, HCard &$card): void
    {
        $updatedIds = [$card->getId()];
        $jailId = self::getJailId($playerId);
        if ($jailId) {
            self::discard($jailId, 'discard', false);
            $updatedIds[] = $jailId;
        }
        $jailLocation = self::getJailLocation($playerId);
        self::discard($card, $jailLocation, false);

        // Notify
        self::notifyCards(self::getCards($updatedIds));
    }

    public static function useBenefit($cards, int $benefit): void
    {
        $cardIds = self::getIds($cards);
        foreach ($cardIds as $cardId) {
            self::DbQuery("INSERT INTO resolve VALUES ($cardId, $benefit)");
        }
    }
}
