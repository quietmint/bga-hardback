<?php

class PlayerMgr extends APP_GameClass
{
    public static function getMaxAward(): ?array
    {
        return hardback::$instance->getObjectFromDB("SELECT player_id, award FROM player WHERE award > 0 ORDER BY award DESC LIMIT 1");
    }

    public static function getAwardLosers(int $points): ?array
    {
        return hardback::$instance->getObjectListFromDB("SELECT player_id FROM player WHERE award > 0 AND award < $points ORDER BY award", true);
    }

    public static function getMaxScore(): int
    {
        $max = intval(hardback::$instance->getUniqueValueFromDB("SELECT MAX(player_score) FROM player WHERE player_eliminated = 0 AND player_zombie = 0"));
        if (hardback::$instance->getGlobal(H_OPTION_COOP) != H_NO) {
            $max = max($max, self::getPenny()->getScore());
        }
        return $max;
    }

    public static function getPlayerCount(): int
    {
        return intval(hardback::$instance->getUniqueValueFromDB("SELECT COUNT(*) FROM player"));
    }

    public static function getPlayerIds(int $exclude = null): array
    {
        $sql = "SELECT player_id FROM player";
        if ($exclude != null) {
            $sql .= " WHERE player_id != $exclude";
        }
        $sql .= " ORDER BY player_no";
        return hardback::$instance->getObjectListFromDB($sql, true);
    }

    public static function getPlayer(int $playerId = null): HPlayer
    {
        if ($playerId === null) {
            $playerId = hardback::$instance->getActivePlayerId();
        }
        return self::getPlayers([$playerId])[$playerId];
    }

    public static function getPlayers(array $playerIds = []): array
    {
        $sql = "SELECT player_id, player_no, player_name, player_color, player_eliminated, player_zombie, player_score, coins, ink, remover, award, advert, word, vote, replayFrom FROM player";
        if (!empty($playerIds)) {
            $sql .= " WHERE player_id IN (" . implode(", ", $playerIds) . ")";
        }
        $sql .= " ORDER BY player_no";
        $players = array_map(function ($dbplayer) {
            return new HPlayer($dbplayer);
        }, hardback::$instance->getCollectionFromDb($sql));
        if (!empty($playerIds) && count($players) != count($playerIds)) {
            $expected = array_map('intval', $playerIds);
            $actual = array_map('intval', array_keys($players));
            $invalid = array_diff($expected, $actual);
            throw new BgaVisibleSystemException("Invalid player IDs: " . implode(", ", $invalid));
        }
        return $players;
    }

    public static function getPenny(): HPenny
    {
        return new HPenny();
    }

    public static function getVoteResult(): ?string
    {
        $accept = intval(hardback::$instance->getUniqueValueFromDB("SELECT COUNT(*) FROM player WHERE vote = 1"));
        $reject = intval(hardback::$instance->getUniqueValueFromDB("SELECT COUNT(*) FROM player WHERE vote = 0"));
        $max = self::getPlayerCount() - 1;
        if (hardback::$instance->getGlobal(H_OPTION_VOTE) == H_VOTE_50) {
            $majority = $max / 2;
            if ($accept >= $majority) {
                // 50% accepts (includes ties)
                return 'accept';
            } else if ($reject > $majority || $accept + $reject == $max) {
                // >50% rejects or voting is complete
                return 'reject';
            }
        } else {
            if ($reject > 0) {
                // Any voter rejects
                return 'reject';
            } else if ($accept == $max) {
                // All voters accept
                return 'accept';
            }
        }
        return null;
    }

    public static function resetVoteResult(): void
    {
        self::DbQuery("UPDATE player SET vote = NULL");
    }
}
