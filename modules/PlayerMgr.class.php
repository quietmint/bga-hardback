<?php

class PlayerMgr extends APP_GameClass
{
    public static function getMaxScore()
    {
        $max = intval(self::getUniqueValueFromDB("SELECT MAX(player_score) FROM player WHERE player_eliminated = 0 AND player_zombie = 0"));
        if (hardback::$instance->gamestate->table_globals[OPTION_COOP] != NO) {
            $max = max($max, self::getPenny()->getScore());
        }
        return $max;
    }

    public static function getPlayerCount()
    {
        return intval(self::getUniqueValueFromDB("SELECT COUNT(*) FROM player"));
    }

    public static function getPlayerIds()
    {
        return self::getObjectListFromDb("SELECT player_id FROM player ORDER BY player_no", true);
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
        $sql = "SELECT player_id, player_no, player_name, player_color, player_eliminated, player_zombie, player_score, coins, ink, remover, advert, word FROM player";
        if (!empty($playerIds)) {
            $sql .= " WHERE player_id IN (" . implode(", ", $playerIds) . ")";
        }
        $sql .= " ORDER BY player_no";
        return array_map(function ($dbplayer) {
            return new HPlayer($dbplayer);
        }, self::getCollectionFromDb($sql));
    }

    public static function getPenny(): HPenny
    {
        return new HPenny();
    }
}
