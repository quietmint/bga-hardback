<?php

class WordMgr extends APP_GameClass
{
    public static function getDictionaryInfo(): array
    {
        $langs = [
            H_LANG_EN => 'English',
            H_LANG_DE => 'Deutsch',
            H_LANG_FR => 'Français',
        ];
        $lang = intval(hardback::$instance->gamestate->table_globals[H_OPTION_LANG]);
        $opt = $lang == H_LANG_EN ? H_OPTION_DICTIONARY : 120 + $lang;
        $dict = intval(hardback::$instance->gamestate->table_globals[$opt]);

        $info = [
            'i18n' => ['dict'],
            'preserve' => ['link'],
            'dictId' => $dict,
            'langId' => $lang,
            'lang' => $langs[$lang],
            'voting' => $dict == H_VOTE_50 || $dict == H_VOTE_100,
        ];
        if ($dict == H_TWELVEDICTS) {
            $info['dict'] = clienttranslate('12dicts');
            $info['link'] = 'http://wordlist.aspell.net/12dicts-readme/';
        } else if ($dict == H_US) {
            $info['dict'] = clienttranslate('American Scrabble');
        } else if ($dict == H_UK) {
            $info['dict'] =  clienttranslate('British Scrabble');
        } else if ($dict == H_LETTERPRESS) {
            $info['dict'] = clienttranslate('Letterpress');
            $info['link'] = 'https://github.com/lorenbrichter/Words';
        } else if ($dict == H_BEOLINGUS) {
            $info['dict'] = clienttranslate('BEOLINGUS (TU Chemnitz)');
            $info['link'] = 'https://dict.tu-chemnitz.de/doc/about.de.html';
        } else if ($dict == H_FREE_DE) {
            $info['dict'] = clienttranslate('Free German Dictionary');
            $info['link'] = 'https://sourceforge.net/projects/germandict/files/';
        } else if ($dict == H_MORPHALOU) {
            $info['dict'] = clienttranslate('Morphalou');
            $info['link'] = 'https://www.ortolang.fr/market/lexicons/morphalou/v3.1';
        } else if ($dict == H_VOTE_50) {
            $info['dict'] = clienttranslate('Majority Vote');
        } else if ($dict == H_VOTE_100) {
            $info['dict'] = clienttranslate('Unanimous Vote');
        }
        return $info;
    }

    public static function isWord(string $word): bool
    {
        $word = trim(strtolower($word));
        if (empty($word)) {
            return false;
        }

        $info = self::getDictionaryInfo();
        try {
            $letter = substr($word, 0, 1);
            $path = __DIR__ . "/wordlist/{$info['dictId']}/$letter.inc.php";
            if (!is_readable($path)) {
                throw new Exception("Missing file $path");
            }
            $dictionary = array_flip(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            return isset($dictionary[$word]);
        } catch (Exception $e) {
            throw new BgaVisibleSystemException("{$info['dict']} ({$info['lang']}) error: " . $e->getMessage());
        }
    }

    public static function getHistory(): array
    {
        $history = [];
        try {
            $sql = "SELECT `id`, `player_id`, `word`, `score`, `coins` FROM word ORDER BY `id`";
            $history = array_values(array_map(function ($dbrow) {
                return [
                    'id' => intval($dbrow['id']),
                    'player_id' => intval($dbrow['player_id']),
                    'word' => $dbrow['word'],
                    'score' => intval($dbrow['score']),
                    'coins' => intval($dbrow['coins']),
                ];
            }, self::getCollectionFromDb($sql)));
        } catch (Exception $e) {
            self::warn("Cannot get word history");
        }
        return $history;
    }

    public static function isHistory(string $word): bool
    {
        return self::getUniqueValueFromDB("SELECT 1 FROM word WHERE `word` = '$word' LIMIT 1") != null;
    }

    public static function recordHistory(int $player_id, string $word, int $coins, int $score): void
    {
        try {
            self::DbQuery("INSERT INTO word (`player_id`, `word`, `coins`, `score`) VALUES ($player_id, '$word', $coins, $score)");
        } catch (Exception $e) {
            self::warn("Cannot record word history: $word");
        }
    }
}
