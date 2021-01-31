<?php

class PlayerMgr extends APP_GameClass
{
    public static function getMaxScore()
    {
        return intval(self::getUniqueValueFromDB("SELECT MAX(player_score) FROM player WHERE player_eliminated = 0 AND player_zombie = 0"));
    }

    public static function getPlayerCount()
    {
        return intval(self::getUniqueValueFromDB("SELECT COUNT(*) FROM player WHERE player_eliminated = 0 AND player_zombie = 0"));
    }

    public static function getPlayerIds()
    {
        return self::getObjectListFromDb("SELECT player_id FROM player", true);
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
        $sql = "SELECT player_id, player_no, player_name, player_color, player_eliminated, player_zombie, player_score, coins, ink, remover, advert FROM player";
        if (!empty($playerIds)) {
            $sql .= " WHERE player_id IN (" . implode(", ", $playerIds) . ")";
        }
        $players = array_map(function ($dbplayer) {
            return new HPlayer($dbplayer);
        }, self::getCollectionFromDb($sql));

        if (hardback::$instance->gamestate->table_globals[OPTION_COOP] != NO) {
            $players[0] = new HPlayerCoop();
        }
        return $players;
    }
}
