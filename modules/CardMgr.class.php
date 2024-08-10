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

    public static function drawCards(int $count, string $fromLocation, string $toLocation, string $origin = null): array
    {
        // Draw cards from deck
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($fromLocation) . " ORDER BY `location`, `order` LIMIT $count";
        $ids = hardback::$instance->getObjectListFromDB($sql, true);

        $missing = $count - count($ids);
        if ($missing > 0) {
            // Rehuffle and continue drawing
            self::reshuffle($fromLocation);
            $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($fromLocation);
            if (!empty($ids)) {
                $sql .= " AND `id` NOT IN (" . implode(',', $ids) . ")";
            }
            $sql .= " ORDER BY `location`, `order` LIMIT $missing";
            $ids = array_merge($ids, hardback::$instance->getObjectListFromDB($sql, true));
        }

        // Populate from database and sort
        hardback::$instance->enqueueCards($ids);
        $cards = self::getCards($ids);

        // Reposition in front and update origin
        if ($origin == null) {
            $origin = $toLocation;
        }
        foreach ($cards as &$card) {
            $card->setOrigin($origin);
            $card->setLocation($toLocation);
            $card->setOrder(self::getAppendOrderForLocation($toLocation));
            self::DbQuery("UPDATE card SET `origin` = '$origin', `location` = '$toLocation', `order` = {$card->getOrder()} WHERE `id` = {$card->getId()}");
        }
        return $cards;
    }

    public static function reshuffle(string $location): void
    {
        $shuffleLocation = str_replace(['draw', 'deck'], ['discard', 'discard'], $location);
        $sql = "SELECT `id` FROM card WHERE " . self::getLocationWhereClause($shuffleLocation);
        $ids = hardback::$instance->getObjectListFromDB($sql, true);
        hardback::$instance->enqueueCards($ids);
        shuffle($ids);
        $order = 0;
        foreach ($ids as $id) {
            $order++;
            self::DbQuery("UPDATE card SET `origin` = '$location', `location` = '$location', `order` = $order WHERE `id` = $id");
        }
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
        $dbcards = hardback::$instance->getCollectionFromDB($sql);

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
                if ($card->isWild() && ($card->hasInk() || $card->isOrigin('timeless'))) {
                    throw new BgaVisibleSystemException("getCards: Not possible to make card $card wild");
                }
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

    public static function getIdsInLocation($locations, int $inkValue = null, string $sort = null): array
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
        return hardback::$instance->getObjectListFromDB($sql, true);
    }

    public static function getCardsInLocation($locations, int $inkValue = null, string $sort = null): array
    {
        return self::getCards(self::getIdsInLocation($locations, $inkValue, $sort));
    }

    public static function getCountInLocation(string $location): int
    {
        $count = hardback::$instance->getUniqueValueFromDB("SELECT COUNT(*) FROM card WHERE `location` = '$location'");
        return intval($count);
    }

    public static function getAppendOrderForLocation(string $location): int
    {
        if ($location == 'offer') {
            $sql = "SELECT COALESCE(MIN(`order`), 0) - 1 FROM card WHERE `location` = '$location'";
        } else {
            $sql = "SELECT COALESCE(MAX(`order`), 0) + 1 FROM card WHERE `location` = '$location'";
        }
        return intval(hardback::$instance->getUniqueValueFromDB($sql));
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
            $cardIds = hardback::$instance->getObjectListFromDB($sql, true);
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
        return intval(hardback::$instance->getUniqueValueFromDB("SELECT `id` FROM card WHERE `location` = '$location'", true));
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

    public static function deletePreviewNotifications(bool $all = false): void
    {
        // Delete transient preview notifications
        $sql = "DELETE FROM `gamelog` WHERE gamelog_move_id IS NULL AND gamelog_notification LIKE '%cardsPreview%'";
        if (!$all) {
            $ids = hardback::$instance->getObjectListFromDB("SELECT MAX(gamelog_packet_id) FROM `gamelog` WHERE gamelog_move_id IS NULL AND gamelog_notification LIKE '%cardsPreview%' GROUP BY gamelog_current_player", true);
            if (!empty($ids)) {
                $sql .= " AND gamelog_packet_id NOT IN (" . implode(',', $ids) . ")";
            }
        }
        self::DbQuery($sql);
    }

    /* Change (specific) */

    public static function endGameCleanup(): void
    {
        $locations = [];
        foreach (PlayerMgr::getPlayers() as $player) {
            $location = $player->getTableauLocation();
            $locations[] = $location;
            $sql = "UPDATE card SET `ink` = NULL, `wild` = NULL, `factor` = 1, `origin` = '$location', `location` = '$location', `order` = -1 WHERE `origin` LIKE '%_{$player->getId()}' AND `origin` NOT LIKE 'jail%'";
            self::DbQuery($sql);
        }
        $ids = self::getIdsInLocation($locations);
        hardback::$instance->enqueueCards($ids);
    }

    public static function applyWord(int $playerId, array $tableau, bool $commit = false): array
    {
        $updatedIds = [];
        $oldTableau = self::getTableau($playerId, null, false); // without timeless

        // Update tableau
        $tableauLocation = self::getTableauLocation($playerId);
        $order = 0;
        foreach ($tableau as $card) {
            $order++;
            $sql = "UPDATE card SET `wild` = " . ($card->isWild() ? "'{$card->getLetter()}'" : "NULL") . ", `location` = '$tableauLocation', `order` = $order";
            if ($commit) {
                $sql .= ", `age` = COALESCE(`age`, SYSDATE(6))";
            }
            $sql .= " WHERE `id` = {$card->getId()}";
            self::DbQuery($sql);
            $updatedIds[] = $card->getId();
        }

        // Return remaining cards to origin (but keep wild status)
        $remainder = [];
        foreach ($oldTableau as $card) {
            if (!in_array($card->getId(), $updatedIds)) {
                $remainder[] = $card;
            }
        }
        foreach ($remainder as $card) {
            $card->setLocation($card->getOrigin());
            $card->setOrder(-1);
            $sql = "UPDATE card SET `location` = `origin`, `order` = -1 WHERE `id` = {$card->getId()}";
            self::DbQuery($sql);
            $updatedIds[] = $card->getId();
        }

        return $updatedIds;
    }

    public static function commitWord(int $playerId): void
    {
        // Discard unused cards
        $discardIds = self::getIds(self::getHand($playerId));
        if (!empty($discardIds)) {
            self::discard($discardIds, self::getDiscardLocation($playerId), false);
        }

        $opponent = [];
        $tableau = self::getTableau($playerId);
        $timelessLocation = self::getTimelessLocation($playerId);
        foreach ($tableau as $card) {
            // Cancel all benefits on opponent timeless cards
            if ($card->isOrigin('timeless') && !$card->isOrigin($timelessLocation)) {
                self::useBenefit($card, H_ALL_BENEFITS);
                $opponent[] = $card;
            }
        }

        // Compute active genres
        // Subtract opponent timeless cards
        $counts = self::getGenreCounts($tableau);
        $opponentCounts = self::getGenreCounts($opponent);
        foreach ($counts as $genre => $count) {
            if ($genre != H_STARTER) {
                $val = $count - $opponentCounts[$genre];
                hardback::$instance->setGameStateValue("countActive$genre", $val);
            }
        }
    }

    public static function isGenreActive(int $genre): bool
    {
        return $genre != H_STARTER && hardback::$instance->getGameStateValue("countActive$genre") >= 2;
    }

    public static function getTrashLocation(HCard $card): string
    {
        // Starter cards are trashed forever
        // Non-starter cards can eventually reshuffle
        return $card->getGenre() == H_STARTER ? "trash" : "discard";
    }

    public static function discard($cards, string $location): void
    {
        $updatedIds = self::getIds($cards);
        if (!empty($updatedIds)) {
            $sql = "UPDATE card SET `ink` = NULL, `wild` = NULL, `factor` = 1, `origin` = '$location', `location` = '$location', `order` = -1";
            if (strpos($location, 'discard') !== false) {
                $sql .= ", `age` = NULL";
            }
            $sql .= " WHERE `id` IN (" . implode(',', $updatedIds) . ")";
            self::DbQuery($sql);
            hardback::$instance->enqueueCards($updatedIds);
        }
    }

    public static function previewReturn($card, $location)
    {
        self::DbQuery("UPDATE card SET `order` = `order` + 1 WHERE `location` = '$location' AND `order` >= {$card->getOrder()}");
        self::DbQuery("UPDATE card SET `ink` = NULL, `wild` = NULL, `factor` = 1, `origin` = '$location', `location` = '$location' WHERE id = {$card->getId()}");
        $card->setLocation($location);
        $card->setOrigin($location);
        hardback::$instance->enqueueCards([$card->getId()]);
    }

    public static function reset(HPlayer $player, bool $skipWord = false): void
    {
        $playerId = $player->getId();
        $cards = self::getCardsInLocation([self::getHandLocation($playerId), self::getTableauLocation($playerId)]);

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
                } else if (hardback::$instance->getGlobal(H_OPTION_COOP) != H_NO) {
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
                    $ownerPlayer = PlayerMgr::getPlayer($owner);
                    hardback::$instance->notifyAllPlayers('message', hardback::$instance->msg['timelessDiscard'], [
                        'player_name' => $player->getName(),
                        'player_name2' => $ownerPlayer->getName(),
                        'genre' => $card->getGenreName(),
                        'letter' => $card->getLetter(),
                    ]);
                }
            } else {
                // Discard normally
                $discardIds[] = $card->getId();
            }
        }

        // Discard remaining cards
        self::discard($discardIds, self::getDiscardLocation($playerId));
        hardback::$instance->sendNotify();

        // Draw new hand
        self::drawCards(5, self::getDrawLocation($playerId), self::getTableauLocation($playerId), self::getHandLocation($playerId));
    }

    public static function canFlushOffer(): bool
    {
        if (hardback::$instance->getGlobal(H_OPTION_COOP) != H_NO) {
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
        hardback::$instance->incStat(1, 'flush');
        $updatedIds = self::getIds(self::getCardsInLocation('offer'));
        self::discard($updatedIds, 'discard');
        self::drawCards(7, 'deck', 'offer');
    }

    public static function inkCard(HCard $card, int $inkValue = H_HAS_INK): void
    {
        $sql = "UPDATE card SET `ink` = $inkValue, `wild` = NULL WHERE `id` = {$card->getId()}";
        self::DbQuery($sql);
        $card->setInk($inkValue);
        hardback::$instance->enqueueCards([$card->getId()]);
    }

    public static function uncover(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, H_UNCOVER_ADJ);
        self::DbQuery("UPDATE card SET `wild` = '_' WHERE `id` = {$card->getId()}");
        $card->setWild('_');
        if ($card->getGenre() != H_STARTER) {
            hardback::$instance->incGameStateValue("countActive{$card->getGenre()}", 1);
        }
        hardback::$instance->enqueueCards([$card->getId()]);
    }

    public static function double(HCard &$card, HCard $source): void
    {
        self::useBenefit($source, H_DOUBLE_ADJ);
        self::DbQuery("UPDATE card SET `factor` = `factor` + 1 WHERE `id` = {$card->getId()}");
        $card->setFactor($card->getFactor() + 1);
        hardback::$instance->enqueueCards([$card->getId()]);
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
        hardback::$instance->enqueueCards([$card->getId()]);
    }

    public static function useBenefit($cards, int $benefit): void
    {
        $cardIds = self::getIds($cards);
        foreach ($cardIds as $cardId) {
            self::DbQuery("INSERT INTO resolve VALUES ($cardId, $benefit)");
        }
    }
}
