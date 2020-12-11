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

    public static function getPlayer($id)
    {
        return self::getPlayers([$id])[$id];
    }

    public static function getPlayers($ids = null)
    {
        $sql = "SELECT player_id id, player_name name, player_score score, coins, ink, remover FROM player";
        if (is_array($ids)) {
            $sql .= " WHERE player_id IN ('" . implode("','", $ids) . "')";
        }
        //$sql .= " ORDER BY no";
        $players = self::getCollectionFromDb($sql);
        foreach ($players as &$player) {
            $player['id'] = intval($player['id']);
            $player['score'] = intval($player['score']);
            $player['coins'] = intval($player['coins']);
            $player['ink'] = intval($player['ink']);
            $player['remover'] = intval($player['remover']);
            $player['totalCount'] = CardMgr::getPlayerTotalCount($player['id']);
        }
        return $players;
    }
}
