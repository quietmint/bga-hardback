<?php

class WordMgr extends APP_GameClass
{
    public static function isWord($word)
    {
        $word = trim(strtolower($word));
        $letter = substr($word, 0, 1);
        $dictionary = self::getDictionary($letter);
        return isset($dictionary[$word]);
    }

    private static function getDictionary($letter)
    {
        $path = null;
        $dictionary = null;
        $option = self::getGameStateValue('dictionary');
        switch ($option) {
            case TWELVEDICTS:
                $path = __DIR__ . "/modules/wordlist/12dicts/$letter.txt";
                break;
            case NWL:
                $path = __DIR__ . "/modules/wordlist/nwl/$letter.txt";
                break;
            case COLLINS:
                $path = __DIR__ . "/modules/wordlist/collins/$letter.txt";
                break;
            default:
                throw new BgaVisibleSystemException("Invalid dictionary: $option");
        }
        if (!is_readable($path)) {
            throw new BgaVisibleSystemException("Missing dictionary file: $path");
        }
        $dictionary = array_flip(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        return $dictionary;
    }
}
