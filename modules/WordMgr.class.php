<?php

class WordMgr extends APP_GameClass
{
    private static function getDictionary(string $letter): array
    {
        // Actually a plain-text file, but BGA requires .php extension
        $path = __DIR__ . "/wordlist/" . hardback::$instance->gamestate->table_globals[OPTION_DICTIONARY] . "/$letter.inc.php";
        if (!is_readable($path)) {
            throw new BgaVisibleSystemException("getDictionary: Missing dictionary file: $path");
        }
        $dictionary = array_flip(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        return $dictionary;
    }

    public static function isWord(string $word): bool
    {
        $word = trim(strtolower($word));
        $letter = substr($word, 0, 1);
        $dictionary = self::getDictionary($letter);
        return isset($dictionary[$word]);
    }
}
