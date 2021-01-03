<?php

class WordMgr extends APP_GameClass
{
    private static function getDictionary($dictionaryId, $letter)
    {
        $path = null;
        $dictionary = null;
        switch ($dictionaryId) {
            case TWELVEDICTS:
                $path = __DIR__ . "/wordlist/12dicts/$letter.txt";
                break;
            case NWL:
                $path = __DIR__ . "/wordlist/nwl/$letter.txt";
                break;
            case COLLINS:
                $path = __DIR__ . "/wordlist/collins/$letter.txt";
                break;
            default:
                throw new BgaVisibleSystemException("Invalid dictionary ID: $dictionaryId");
        }
        if (!is_readable($path)) {
            throw new BgaVisibleSystemException("Missing dictionary file: $path");
        }
        $dictionary = array_flip(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        return $dictionary;
    }

    public static function isWord($dictionaryId, $word)
    {
        $word = trim(strtolower($word));
        $letter = substr($word, 0, 1);
        $dictionary = self::getDictionary($dictionaryId, $letter);
        return isset($dictionary[$word]);
    }
}
