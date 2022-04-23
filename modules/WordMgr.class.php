<?php

class WordMgr extends APP_GameClass
{
    public static function getLanguageId(): int
    {
        return hardback::$instance->gamestate->table_globals[H_OPTION_LANG];
    }

    public static function getDictionaryId(): int
    {
        $lang = self::getLanguageId();
        if ($lang == H_LANG_EN) {
            return hardback::$instance->gamestate->table_globals[H_OPTION_DICTIONARY];
        } else {
            $opt = 120 + $lang;
            return hardback::$instance->gamestate->table_globals[$opt];
        }
    }

    public static function getDictionaryInfo(): array
    {
        $dict = self::getDictionaryId();
        $lang = self::getLanguageId();
        $info = [
            'i18n' => ['dict'],
            'preserve' => ['link'],
            'dict' => hardback::$instance->dicts[$dict],
            'lang' => hardback::$instance->langs[$lang],
        ];
        if ($dict == H_TWELVEDICTS) {
            $info['link'] = 'http://wordlist.aspell.net/12dicts-readme/';
        } else if ($dict == H_LETTERPRESS) {
            $info['link'] = 'https://github.com/lorenbrichter/Words';
        } else if ($dict == H_BEOLINGUS) {
            $info['link'] = 'https://dict.tu-chemnitz.de/doc/about.de.html';
        } else if ($dict == H_FREE_DE) {
            $info['link'] = 'https://sourceforge.net/projects/germandict/files/';
        } else if ($dict == H_MORPHALOU) {
            $info['link'] = 'https://www.ortolang.fr/market/lexicons/morphalou/v3.1';
        }
        return $info;
    }

    public static function isWord(string $word): bool
    {
        $word = trim(strtolower($word));
        if (empty($word)) {
            return false;
        }

        $dict = self::getDictionaryId();
        try {
            $letter = substr($word, 0, 1);
            $path = __DIR__ . "/wordlist/$dict/$letter.inc.php";
            if (!is_readable($path)) {
                throw new Exception("Missing file $path");
            }
            $dictionary = array_flip(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            return isset($dictionary[$word]);
        } catch (Exception $e) {
            $info = self::getDictionaryInfo();
            throw new BgaVisibleSystemException("{$info['dict']} ({$info['lang']}) error: " . $e->getMessage());
        }
    }
}
