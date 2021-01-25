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

    public static function getPlayer($playerId = null)
    {
        if ($playerId == null) {
            $playerId = hardback::$instance->getActivePlayerId();
        }
        return self::getPlayers([$playerId])[$playerId];
    }

    public static function getPlayers($playerIds = [])
    {
        $sql = "SELECT player_id, player_no, player_name, player_color, player_eliminated, player_zombie, player_score, coins, ink, remover, advert FROM player";
        if (!empty($playerIds)) {
            $sql .= " WHERE player_id IN (" . implode(", ", $playerIds) . ")";
        }
        return array_map(function ($dbplayer) {
            return new HPlayer($dbplayer);
        }, self::getCollectionFromDb($sql));
    }
}
