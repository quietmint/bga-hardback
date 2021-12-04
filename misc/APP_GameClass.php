<?php

/*
 * This dummy class makes VSCode PHP Intelephense plugin happy
 * Do not treat BGA framework function calls as errors 
 */

define('APP_GAMEMODULE_PATH', '');
define('AT_alphanum', 0);
define('AT_base64', 0);
define('AT_bool', 0);
define('AT_enum', 0);
define('AT_float', 0);
define('AT_int', 0);
define('AT_numberlist', 0);
define('AT_posint', 0);

function totranslate(string $text): string
{
    return $text;
}

function clienttranslate(string $text): string
{
    return $text;
}

function _(string $text): string
{
    return $text;
}


class APP_GameClass
{
    function __construct()
    {
    }

    static function _($text)
    {
        return '';
    }

    /* Logging */
    static function dump($name, $variable)
    {
    }

    static function debug($message)
    {
    }

    static function trace($message)
    {
    }

    static function warn($message)
    {
    }

    static function error($message)
    {
    }

    /* Database */

    static function DbQuery($sql)
    {
    }

    static function applyDbUpgradeToAllDB($sql)
    {
    }

    static function DbGetLastId()
    {
        return 0;
    }

    static function DbAffectedRow()
    {
        return 0;
    }

    static function getCollectionFromDB($sql)
    {
        return [];
    }

    static function getUniqueValueFromDB($sql)
    {
        return "";
    }

    static function getObjectFromDB($sql)
    {
        return [];
    }

    static function getObjectListFromDb($sql)
    {
        return [];
    }

    static function getNonEmptyObjectFromDB($sql)
    {
        return [];
    }

    /* Other */

    static function getNew($component)
    {
        return (object) $component;
    }
}


class APP_GameAction extends APP_GameClass
{
    static function setAjaxMode()
    {
    }

    static function ajaxResponse()
    {
    }

    static function getArg($argName, $argType, $mandatory = false, $default = NULL, $argTypeDetails = array(), $bCanFail = false)
    {
    }

    static function isArg($argName)
    {
    }
}

class Table extends APP_GameClass
{

    static function getGameinfos()
    {
    }
    static function reloadPlayersBasicInfos()
    {
    }

    static function reattributeColorsBasedOnPreferences($players, $colors)
    {
    }

    /* Globals */

    static function initGameStateLabels($array)
    {
    }

    static function setGameStateInitialValue($value_label, $value_value)
    {
    }

    function getGameStateValue($value_label): string
    {
        return "";
    }

    function setGameStateValue($value_label, $value_value)
    {
    }

    function incGameStateValue($value_label, $increment)
    {
        return 0;
    }

    /* Statistics */
    static function initStat(string $table_or_player, $name, $value, int $player_id = null)
    {
    }

    function setStat(int $value, string $name, int $player_id = null): void
    {
    }

    function incStat(int $delta, string $name, int $player_id = null): void
    {
    }

    function getStat(string $name, int $player_id = null)
    {
        return 0;
    }

    /* State functions */

    function checkAction($actionName, $bThrowException = true)
    {
    }

    function activeNextPlayer()
    {
    }

    function activePrevPlayer()
    {
    }

    static function getCurrentPlayerId()
    {
        return 0;
    }

    static function getActivePlayerId()
    {
        return 0;
    }

    static function getActivePlayerName()
    {
        return '';
    }

    static function giveExtraTime($player_id, $specific_time = null)
    {
    }

    function getPlayerAfter($player_id)
    {
        return 0;
    }

    function getPlayerBefore($player_id)
    {
        return 0;
    }

    /* Notify */

    static function notifyAllPlayers($notification_type, $notification_log, $notification_args)
    {
    }

    static function notifyPlayer($player_id, $notification_type, $notification_log, $notification_args)
    {
    }
}

class BgaVisibleSystemException
{
}

class BgaUserException
{
}
