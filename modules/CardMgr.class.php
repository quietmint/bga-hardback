<?php

class CardMgr extends APP_GameClass
{
    private static $cards = null;

    public static function __constructStatic()
    {
        self::$cards = self::getNew('module.common.deck');
        self::$cards->init('card');
        self::$cards->autoreshuffle = true;
        self::$cards->autoreshuffle_custom = ['deck' => 'discard'];
        foreach (PlayerMgr::getPlayerIds() as $playerId) {
            self::$cards->autoreshuffle_custom["deck$playerId"] = "discard$playerId";
        }
    }

    private static function populateCard($dbcard)
    {
        if ($dbcard == null) {
            return null;
        }
        return [
            'id' => intval($dbcard['id']),
            'genre' => intval($dbcard['type']),
            'letter' => chr($dbcard['type_arg']),
            'weight' => intval($dbcard['type_arg']),
        ];
    }

    private static function populateCards($dbcards)
    {
        $cards = [];
        if ($dbcards != null) {
            foreach ($dbcards as $dbcard) {
                $card = self::populateCard($dbcard);
                $cards[$card['id']] = $card;
            }
        }
        return $cards;
        // return array_map(['CardMgr', 'populateCard'], $dbcards);
    }

    public static function sortCards(&$cards)
    {
        uasort($cards, function ($a, $b) {
            $result = strcmp($a['letter'], $b['letter']);
            if ($result === 0) {
                return $a['genre'] - $b['genre'];
            }
            if ($result === 0) {
                return $a['id'] - $b['id'];
            }
            return $result;
        });
        return $cards;
    }

    public static function setup()
    {
        // Create player decks, draw 5 cards each
        $startLetters = ['A', 'E', 'I', 'L', 'N', 'R', 'S', 'T'];
        $randomLetters = ['B', 'C', 'D', 'G', 'H', 'M', 'O', 'P', 'U', 'Y'];
        foreach (PlayerMgr::getPlayerIds() as $playerId) {
            $letters = array_rand(array_flip($randomLetters), 2);
            $letters += $startLetters;
            $cards = [];
            foreach ($letters as $letter) {
                $cards[] = ['type' => STARTER, 'type_arg' => ord($letter), 'nbr' => 1];
            }
            self::$cards->createCards($cards, "deck$playerId");
            self::$cards->pickCards(5, "deck$playerId", $playerId);
        }

        // Create draw deck, draw 7 cards
        $letters = range('A', 'Z');
        $cards = array();
        foreach (hardback::$instance->genres as $genre) {
            foreach ($letters as $letter) {
                $cards[] = ['type' => $genre, 'type_arg' => ord($letter), 'nbr' => 1];
            }
        }
        self::$cards->createCards($cards, 'deck');
        self::$cards->pickCardsForLocation(7, "deck", "table");
    }

    public static function getCard($cardId)
    {
        return self::populateCard(self::$cards->getCard($cardId));
    }

    public static function getCards($cardIds)
    {
        $cards = self::populateCards(self::$cards->getCards($cardIds));
        return $cards;
    }

    public static function getPlayerHand($playerId)
    {
        $cards = self::populateCards(self::$cards->getCardsInLocation('hand', $playerId)) + self::populateCards(self::$cards->getCardsInLocation("deck$playerId"));
        self::sortCards($cards);
        return $cards;
    }

    public static function getPlayerDeckCount($playerId)
    {
        return self::$cards->countCardInLocation("deck$playerId");
    }

    public static function getPlayerDiscardCount($playerId)
    {
        return self::$cards->countCardInLocation("discard$playerId");
    }

    public static function getPlayerHandCount($playerId)
    {
        return self::$cards->countCardInLocation('hand', $playerId);
    }

    public static function getPlayerTotalCount($playerId)
    {
        return self::getPlayerDeckCount($playerId) + self::getPlayerDiscardCount($playerId) + self::getPlayerHandCount($playerId);
    }

    public static function setTableau($cardIds)
    {
    }
}

CardMgr::__constructStatic();
