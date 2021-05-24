<?php

class WordMgr extends APP_GameClass
{
    public static function getLanguageId(): int
    {
        return hardback::$instance->gamestate->table_globals[OPTION_LANG];
    }

    public static function getDictionaryId(): int
    {
        $lang = self::getLanguageId();
        if ($lang == LANG_EN) {
            return hardback::$instance->gamestate->table_globals[OPTION_DICTIONARY];
        } else if ($lang == LANG_FR) {
            return hardback::$instance->gamestate->table_globals[OPTION_DICTIONARY_FR];
        }
    }

    public static function getDictionaryInfo(): array
    {
        $dict = self::getDictionaryId();
        $info = [
            'i18n' => ['dict'],
            'preserve' => ['link'],
            'dict' => hardback::$instance->dicts[$dict],
        ];
        if ($dict == TWELVEDICTS) {
            $info['link'] = 'http://wordlist.aspell.net/12dicts-readme/';
        } else if ($dict == YANDEX) {
            $info['link'] = 'https://yandex.com/dev/dictionary/';
        }
        return $info;
    }

    public static function isWord(string $word): bool
    {
        $word = trim(strtolower($word));
        $dict = self::getDictionaryId();
        $dictName = hardback::$instance->dicts[$dict];
        if ($dict == YANDEX) {
            // Web-based dictionary
            try {
                $lang = self::getLanguageId();
                if ($lang == LANG_EN) {
                    $langPair = "en-en";
                } else if ($lang == LANG_FR) {
                    $langPair = "fr-fr";
                } else {
                    throw new Exception("Unsupported language $lang");
                }

                $path = __DIR__ . "/wordlist/$dict/key.inc.php";
                if (!is_readable($path)) {
                    throw new Exception("Missing file $path");
                }
                $key = trim(file_get_contents($path));
                if (empty($key)) {
                    throw new Exception("Missing key");
                }

                $svc = "https://dictionary.yandex.net/api/v1/dicservice.json/lookup?key=$key&lang=$langPair&flags=4&text=$word";
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => "Accept: application/json\r\n",
                        'timeout' => 10, // seconds
                        'ignore_errors' => true,
                    ]
                ]);
                $data = file($svc, 0, $context);
                if ($data !== false) {
                    $data = implode("", $data);
                }
                self::trace("$dictName reply from $svc: $data");

                if ($data === false) {
                    // Handle HTTP timeout
                    throw new Exception("Service unavailable");
                }

                $result = json_decode($data, true, 512, JSON_INVALID_UTF8_IGNORE);
                if ($result === null) {
                    // Handle invalid JSON
                    throw new Exception("Invalid response");
                }

                if (isset($result['code']) && $result['code'] != 200) {
                    // Handle error codes
                    // ERR_OK - 200 - Operation completed successfully.
                    // ERR_KEY_INVALID - 401 - Invalid API key.
                    // ERR_KEY_BLOCKED - 402 - This API key has been blocked.
                    // ERR_DAILY_REQ_LIMIT_EXCEEDED - 403 - Exceeded the daily limit on the number of requests.
                    // ERR_TEXT_TOO_LONG - 413 - The text size exceeds the maximum.
                    // ERR_LANG_NOT_SUPPORTED - 501 - The specified translation direction is not supported.
                    if ($result['code'] == 403) {
                        throw new Exception('Daily limit reached');
                    } else {
                        throw new Exception('Response code ' . $result['code']);
                    }
                }

                if (!isset($result['def'])) {
                    // Handle unexpected response
                    throw new Exception("Invalid response");
                }
                return !empty($result['def']);
            } catch (Exception $e) {
                self::error("$dictName reply from $svc: $data");
                throw new BgaVisibleSystemException("$dictName error: " . $e->getMessage());
            }
        } else {
            // File-based dictionary
            try {
                $letter = substr($word, 0, 1);
                $path = __DIR__ . "/wordlist/$dict/$letter.inc.php";
                if (!is_readable($path)) {
                    throw new Exception("Missing file $path");
                }
                $dictionary = array_flip(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
                return isset($dictionary[$word]);
            } catch (Exception $e) {
                $dictName = hardback::$instance->dicts[$dict];
                throw new BgaVisibleSystemException("$dictName error: " . $e->getMessage());
            }
        }
    }
}
