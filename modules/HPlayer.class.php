<?php

class HPlayer extends APP_GameClass implements JsonSerializable
{
    private $id;
    private $coins;
    private $color;
    private $eliminated;
    private $ink;
    private $name;
    private $order;
    private $remover;
    private $score;
    private $zombie;

    public function __construct($dbplayer)
    {
        $this->id = intval($dbplayer['player_id']);
        $this->coins = intval($dbplayer['coins']);
        $this->color = $dbplayer['player_color'];
        $this->eliminated = intval($dbplayer['player_eliminated']);
        $this->ink = intval($dbplayer['ink']);
        $this->name = $dbplayer['player_name'];
        $this->order = intval($dbplayer['player_no']);
        $this->remover = intval($dbplayer['remover']);
        $this->score = intval($dbplayer['player_score']);
        $this->zombie = intval($dbplayer['player_zombie']);
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'coins' => $this->coins,
            'color' => $this->color,
            'colorName' => $this->getColorName(),
            'deckCount' => $this->getDeckCount(),
            'discardCount' => $this->getDiscardCount(),
            'eliminated' => $this->eliminated,
            'ink' => $this->ink,
            'name' => $this->name,
            'remover' => $this->remover,
            'score' => $this->score,
            'zombie' => $this->zombie,
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCoins()
    {
        return $this->coins;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getColorName()
    {
        switch ($this->color) {
            case RED:
                return 'red';
            case GREEN:
                return 'green';
            case BLUE:
                return 'blue';
            case YELLOW:
                return 'yellow';
            case PURPLE:
                return 'purple';
        }
    }

    public function isEliminated()
    {
        return $this->eliminated == 1;
    }

    public function getInk()
    {
        return $this->ink;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getRemover()
    {
        return $this->remove;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function isZombie()
    {
        return $this->zombie == 1;
    }

    /***** Card Functions *****/

    public function getHand($inkValue = null)
    {
        return CardMgr::getHand($this->id, $inkValue);
    }

    public function getHandCount()
    {
        return CardMgr::getHandCount($this->id);
    }

    public function getHandLocation()
    {
        return CardMgr::getHandLocation($this->id);
    }

    public function getDeckCount()
    {
        return CardMgr::getDeckCount($this->id);
    }

    public function getDeckLocation()
    {
        return CardMgr::getDeckLocation($this->id);
    }

    public function getDiscard($inkValue = null)
    {
        return CardMgr::getDiscard($this->id);
    }

    public function getDiscardCount()
    {
        return CardMgr::getDiscardCount($this->id);
    }

    public function getDiscardLocation()
    {
        return CardMgr::getDiscardLocation($this->id);
    }

    /***** Player Functions *****/

    public function isActive()
    {
        return $this->id == hardback::$instance->getActivePlayerId();
    }

    public function notifyPanel()
    {
        hardback::$instance->notifyAllPlayers('panel', '', [
            'player' => $this,
        ]);
    }

    public function notifyInk($card)
    {
        hardback::$instance->notifyAllPlayers('ink', '${player_name} uses ink to draw ${icon}${letter}', [
            'player_name' => $this->name,
            'icon' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
        ]);
    }

    public function notifyRemover($card)
    {
        hardback::$instance->notifyAllPlayers('ink', '${player_name} uses remover to avoid ${icon}${letter}', [
            'player_name' => $this->name,
            'icon' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
        ]);
    }

    public function addCoins($amount, $notify = true)
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET coins = coins + $amount WHERE player_id = {$this->id}");

        $this->notifyPanel();
        if ($notify) {
            hardback::$instance->notifyAllPlayers('message', '${player_name} earns ${amount}¢.', [
                'player_name' => $this->name,
                'amount' => $amount,
            ]);
        }
    }

    public function addPoints($amount, $notify = true)
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET player_score = player_score + $amount WHERE player_id = {$this->id}");

        $this->notifyPanel();
        if ($notify) {
            hardback::$instance->notifyAllPlayers('message', '${player_name} earns ${amount}${icon}.', [
                'player_name' => $this->name,
                'amount' => $amount,
                'icon' => ' star',
            ]);
        }
    }

    public function spendCoins($amount)
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET coins = coins - $amount WHERE player_id = {$this->id} AND coins >= $amount");
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have {$amount}¢");
        }
        $this->notifyPanel();
    }

    public function spendInk($amount = 1)
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET ink = ink - $amount WHERE player_id = {$this->id} AND ink >= $amount");
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have $amount ink");
        }
        $this->notifyPanel();
    }

    public function spendRemover($amount = 1)
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET remover = remover - $amount WHERE player_id = {$this->id} AND remover >= $amount");
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have $amount remover");
        }
        $this->notifyPanel();
    }
}
