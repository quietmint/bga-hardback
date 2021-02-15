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
            if ($ref['genre'] != STARTER) {
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
            if ($ref['genre'] == STARTER) {
                if (array_key_exists(COINS, $ref['basicBenefits'])) {
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
            self::drawCards(5, self::getDeckLocation($playerId), self::getHandLocation($playerId), 'letter');
        }
    }

    public static function updateOrigin(): void
    {
        self::DbQuery("UPDATE card SET `origin` = `location`");
    }

    public static function drawCards(int $count, string $fromLocation, string $toLocation, string $sort = null, bool $notify = false): array
    {
        $order = self::getMaxOrderInLocation($toLocation) + 1;

        // Draw cards from deck
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($fromLocation) . " ORDER BY `location`, `order` LIMIT $count";
        $ids = self::getObjectListFromDB($sql, true);

        $missing = $count - count($ids);
        if ($missing > 0) {
            // Rehuffle and continue drawing
            $shuffleIds = self::reshuffleDeck($fromLocation);
            $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($fromLocation);
            if (!empty($ids)) {
                $sql .= " AND `id` NOT IN (" . implode(',', $ids) . ")";
            }
            $sql .= " ORDER BY `location`, `order` LIMIT $missing";
            $ids = array_merge($ids, self::getObjectListFromDB($sql, true));

            // Required to notify reshuffled cards as "unknown"
            $unknownIds = array_values(array_diff($shuffleIds, $ids));
            $unknown = [];
            foreach ($unknownIds as $id) {
                $unknown[intval($id)] = [
                    'id' => intval($id),
                    'location' => 'unknown',
                ];
            }
            self::notifyCards($unknown);
        }

        // Populate from database and sort
        $cards = self::getCards($ids);
        if (count($cards) > 1 && $sort) {
            self::sortCards($cards, $sort);
        }

        // Reposition at the end and update origin
        foreach ($cards as &$card) {
            self::DbQuery("UPDATE card SET `origin` = '$toLocation', `location` = '$toLocation', `order` = $order WHERE `id` = {$card->getId()}");
            $card->setLocation($toLocation);
            $card->setOrigin($toLocation);
            $card->setOrder($order);
            $order++;

            // Count offer row draws for timeless classic discard condition
            if (hardback::$instance->gamestate->table_globals[OPTION_COOP] != NO && $fromLocation == 'deck' && $toLocation == 'offer') {
                hardback::$instance->incGameStateValue("countOffer{$card->getGenre()}", 1);
            }
        }
        if ($notify) {
            self::notifyCards($cards);
        }
        return $cards;
    }

    public static function reshuffleDeck(string $location): array
    {
        $shuffleLocation = str_replace('deck', 'discard', $location);
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($shuffleLocation);
        $ids = self::getObjectListFromDB($sql, true);
        shuffle($ids);
        $order = 1;
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
            throw new BgaVisibleSystemException("Invalid sort order: $sort");
        }
    }

    /*
    public static function positionCard(&$card, $location = null, $order = null)
    {
        if ($location == null) {
            $location = $card->getLocation();
        }
        if ($order == null) {
            $order = self::getCountInLocation($location);
        }
        self::DbQuery("UPDATE card SET order = order - 1 WHERE location = '{$card->getLocation()}' AND order > {$card->getOrder()}");
        self::DbQuery("UPDATE card SET order = order + 1 WHERE location = '$location' AND order >= $order");
        self::DbQuery("UPDATE card SET location = '$location', order = $order WHERE id = {$card->getId()}");
        $card->setLocation($location);
        $card->setOrder($order);
    }

    public static function positionCards($cardIds, $location, $order = null)
    {
        if ($order = null) {
            $order = self::getCountInLocation($location);
        }
        foreach ($cardIds as $cardId) {
            self::positionCard($cardId, $location, $order++);
        }
    }
*/

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
        $wilds = [];
        if ($wildMask) {
            $countCards = count($cards);
            $countWilds = strlen($wildMask);
            if ($countCards != $countWilds) {
                throw new BgaVisibleSystemException("Invalid wildcard mask (cards: $countCards, wilds: $countWilds)");
            }
            $wilds = array_combine($cardIds, str_split($wildMask));
        }
        foreach ($cards as $card) {
            if (isset($wilds[$card->getId()]) && $wilds[$card->getId()] != '_') {
                $card->setWild($wilds[$card->getId()]);
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

    public static function getCardsInLocation($locations, int $inkValue = null): array
    {
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($locations);
        if ($inkValue != null) {
            $sql .= " AND `ink` = $inkValue";
        }
        $sql .= " ORDER BY `location`, `order`";
        $cardIds = self::getObjectListFromDB($sql, true);
        return self::getCards($cardIds);
    }

    public static function getCardsOwnedByPlayer(int $playerId): array
    {
        $sql = "SELECT `id` FROM card WHERE `origin` LIKE '%_$playerId'";
        $cardIds = self::getObjectListFromDB($sql, true);
        return self::getCards($cardIds);
    }

    public static function getCountInLocation(string $location): int
    {
        $count = self::getUniqueValueFromDB("SELECT COUNT(*) FROM card WHERE `location` = '$location'");
        return intval($count);
    }

    public static function getMaxOrderInLocation(string $location): int
    {
        $max = self::getUniqueValueFromDB("SELECT COALESCE(MAX(`order`), 0) FROM card WHERE `location` = '$location'");
        return intval($max);
    }

    public static function getHandLocation(int $playerId): string
    {
        return "hand_$playerId";
    }

    public static function getDeckLocation(int $playerId): string
    {
        return "deck_$playerId";
    }

    public static function getDiscardLocation(int $playerId): string
    {
        return "discard_$playerId";
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

    public static function getDeckCount(int $playerId): int
    {
        return self::getCountInLocation(self::getDeckLocation($playerId));
    }

    public static function getDiscard(int $playerId): array
    {
        return self::getCardsInLocation(self::getDiscardLocation($playerId));
    }

    public static function getDiscardCount(int $playerId): int
    {
        return self::getCountInLocation(self::getDiscardLocation($playerId));
    }

    public static function getTableau(int $playerId): array
    {
        return self::getCardsInLocation(['tableau', self::getTimelessLocation($playerId)]);
    }

    public static function getTimeless(int $playerId): array
    {
        return self::getCardsInLocation(self::getTimelessLocation($playerId));
    }

    public static function getOffer(int $playerId = null): array
    {
        $cards = self::getCardsInLocation('offer');
        if ($playerId) {
            $jailId = self::getJailId($playerId);
            if ($jailId) {
                $cards[$jailId] = self::getCard($jailId);
            }
        }
        return $cards;
    }

    private static function getJailId(int $playerId): int
    {
        return intval(self::getUniqueValueFromDB("SELECT `id` FROM card WHERE `location` = 'jail' AND `order` = '$playerId'", true));
    }

    public static function getOfferDeckCount(): int
    {
        return self::getCountInLocation('deck');
    }

    /* Array utilties */

    public static function getIds($cards): array
    {
        $ids = [];
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
            $counts[STARTER] ?? 0,
            $counts[ADVENTURE] ?? 0,
            $counts[HORROR] ?? 0,
            $counts[MYSTERY] ?? 0,
            $counts[ROMANCE] ?? 0,
        ];
    }

    /* Notify utilities */

    private static function notifyCards($cards): void
    {
        if ($cards instanceof HCard) {
            $cards = [
                $cards->getId() => $cards
            ];
        }
        hardback::$instance->notifyAllPlayers('cards', '', [
            'cards' => $cards,
        ]);
    }

    /* Change (specific) */

    public static function playWord(int $playerId, array $cards): void
    {
        $updatedIds = self::getIds(self::getCardsInLocation(self::getHandLocation($playerId)));

        // Update tableau
        $ids = self::getIds($cards);
        $remainder = array_filter(self::getCardsInLocation('tableau'), function ($card) use ($ids) {
            return !in_array($card->getId(), $ids);
        });
        if (!empty($remainder)) {
            throw new BgaVisibleSystemException("Tableau contains unexpected cards: " . self::getString($cards));
        }
        $order = 0;
        $opponent = [];
        $tableauIds = [];
        foreach ($cards as $card) {
            $sql = "UPDATE card SET `wild` = " . ($card->isWild() ? "'{$card->getLetter()}'" : "NULL") . ", `location` = 'tableau', `order` = $order WHERE `id` = {$card->getId()}";
            self::DbQuery($sql);
            $order++;
            $tableauIds[] = $card->getId();

            // Cancel all benefits on opponent timeless cards
            if ($card->isOrigin('timeless') && !$card->isOrigin(self::getTimelessLocation($playerId))) {
                self::useBenefit($card, ALL_BENEFITS);
                $opponent[] = $card;
                $updatedIds[] = $card->getId();
            }
        }

        // Discard unused cards
        $discardIds = array_diff($updatedIds, $tableauIds);
        self::discard($discardIds, self::getDiscardLocation($playerId), false);

        // Compute active genres
        // Add own timeless cards
        // Subtract opponent timeless cards
        $counts = self::getGenreCounts($cards);
        $myCounts = self::getGenreCounts(self::getTimeless($playerId));
        $opponentCounts = self::getGenreCounts($opponent);
        foreach ($counts as $genre => $count) {
            if ($genre != STARTER) {
                $val = $count + $myCounts[$genre] - $opponentCounts[$genre];
                hardback::$instance->setGameStateValue("countActive$genre", $val);
            }
        }

        // Notify
        self::notifyCards(self::getCards($updatedIds));
    }

    public static function isGenreActive(int $genre): bool
    {
        return $genre != STARTER && hardback::$instance->getGameStateValue("countActive$genre") >= 2;
    }

    public static function discard($cards, string $location, bool $notify = true): void
    {
        $updatedIds = self::getIds($cards);
        if (!empty($updatedIds)) {
            self::DbQuery("UPDATE card SET `ink` = NULL, `wild` = NULL, `factor` = 1, `origin` = '$location', `location` = '$location', `order` = -1 WHERE `id` IN (" . implode(',', $updatedIds) . ")");

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

    public static function reset(int $playerId): void
    {
        $cards = self::getCardsInLocation([self::getHandLocation($playerId), 'tableau']);
        $updatedIds = self::getIds($cards);
        $discardIds = $updatedIds;

        // Clear used benefits
        self::DbQuery('TRUNCATE resolve');

        // Discard Timeless classics
        $timeless = array_filter($cards, function (HCard $card) {
            return $card->isTimeless() && !$card->isWild() && $card->isLocation('tableau');
        });
        if (!empty($timeless)) {
            foreach ($timeless as $card) {
                if (hardback::$instance->gamestate->table_globals[OPTION_COOP] == NO && $card->isOrigin('timeless') && !$card->isOrigin(self::getTimelessLocation($playerId))) {
                    // Discard to owner
                    self::discard($card, self::getDiscardLocation($card->getOwner()), false);
                } else {
                    // Remain in play for owner
                    $owner = $card->getOwner() ?? $playerId;
                    self::discard($card, self::getTimelessLocation($owner), false);
                }
            }
            $timelessIds = self::getIds($timeless);
            $discardIds = array_diff($updatedIds, $timelessIds);
        }

        // Discard remaining cards
        self::discard($discardIds, self::getDiscardLocation($playerId), false);
        self::notifyCards(self::getCards($updatedIds));

        // Draw new hand
        self::drawCards(5, self::getDeckLocation($playerId), self::getHandLocation($playerId), 'letter', true);
    }

    public static function canFlushOffer(): bool
    {
        if (hardback::$instance->gamestate->table_globals[OPTION_COOP] != NO) {
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
        self::discard($updatedIds, 'trash');

        // Draw new offer and notify
        $newCards = self::drawCards(7, 'deck', 'offer');
        self::notifyCards($newCards);
    }

    public static function inkCard(HCard $card, int $inkValue = HAS_INK): void
    {
        $sql = "UPDATE card SET `ink` = $inkValue WHERE `id` = {$card->getId()}";
        self::DbQuery($sql);
        $card->setInk($inkValue);

        // Notify
        self::notifyCards($card);
    }

    public static function uncover(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, UNCOVER_ADJ);
        self::DbQuery("UPDATE card SET `wild` = NULL WHERE `id` = {$card->getId()}");
        $card->setWild(null);
        if ($card->getGenre() != STARTER) {
            hardback::$instance->incGameStateValue("countActive{$card->getGenre()}", 1);
        }

        // Notify
        self::notifyCards($card);
    }

    public static function double(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, DOUBLE_ADJ);
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
            self::discard($jailId, 'trash', false);
            $updatedIds[] = $jailId;
        }
        self::DbQuery("UPDATE card SET `origin` = 'jail', `location` = 'jail', `order` = '$playerId' WHERE `id` = {$card->getId()}");

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
