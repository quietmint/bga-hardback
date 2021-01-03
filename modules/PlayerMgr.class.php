<?php

class PlayerMgr extends APP_GameClass
{
    public static function getPlayerCount()
    {
        return intval(self::getUniqueValueFromDB("SELECT COUNT(*) FROM player"));
    }

    public static function getPlayerIds()
    {
        return self::getObjectListFromDb("SELECT player_id FROM player", true);
    }

    public static function getPlayer($playerId)
    {
        return self::getPlayers([$playerId])[$playerId];
    }

    public static function getPlayers($playerIds = null)
    {
        $sql = "SELECT player_id id, player_name name, player_score score, player_color color, coins, ink, remover FROM player";
        if (is_array($playerIds)) {
            $sql .= " WHERE player_id IN ('" . implode("','", $playerIds) . "')";
        }
        //$sql .= " ORDER BY no";
        $players = self::getCollectionFromDb($sql);
        foreach ($players as &$player) {
            $player['id'] = intval($player['id']);
            $player['score'] = intval($player['score']);
            $player['coins'] = intval($player['coins']);
            $player['ink'] = intval($player['ink']);
            $player['remover'] = intval($player['remover']);
            $player['deckCount'] = CardMgr::getDeckCount($player['id']);
            $player['discardCount'] = CardMgr::getDiscardCount($player['id']);
        }
        return $players;
    }

    public static function useCoins($playerId, $amount = 1)
    {
        self::DbQuery("UPDATE player SET coins = coins - $amount WHERE coins >= $amount AND player_id = $playerId");
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have {$amount}¢");
        }
    }

    public static function useInk($playerId, $amount = 1)
    {
        self::DbQuery("UPDATE player SET ink = ink - $amount WHERE ink >= $amount AND player_id = $playerId");
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have ink");
        }
    }

    public static function useRemover($playerId, $amount = 1)
    {
        self::DbQuery("UPDATE player SET remover = remover - $amount WHERE remover >= $amount AND player_id = $playerId");
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have remover");
        }
    }
}
