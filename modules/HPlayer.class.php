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

    public function __toString(): string
    {
        return $this->getName();
    }

    public function jsonSerialize(): array
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getCoins(): int
    {
        return $this->coins;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getColorName(): string
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

    public function isEliminated(): bool
    {
        return $this->eliminated == 1;
    }

    public function getInk(): int
    {
        return $this->ink;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getRemover(): int
    {
        return $this->remover;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function isZombie(): bool
    {
        return $this->zombie == 1;
    }

    /***** Card Functions *****/

    public function getHand(int $inkValue = null): array
    {
        return CardMgr::getHand($this->id, $inkValue);
    }

    public function getHandCount(): int
    {
        return CardMgr::getHandCount($this->id);
    }

    public function getHandLocation(): string
    {
        return CardMgr::getHandLocation($this->id);
    }

    public function getDeckCount(): int
    {
        return CardMgr::getDeckCount($this->id);
    }

    public function getDeckLocation(): string
    {
        return CardMgr::getDeckLocation($this->id);
    }

    public function getDiscard(): array
    {
        return CardMgr::getDiscard($this->id);
    }

    public function getDiscardCount(): int
    {
        return CardMgr::getDiscardCount($this->id);
    }

    public function getDiscardLocation(): string
    {
        return CardMgr::getDiscardLocation($this->id);
    }

    /***** Player Functions *****/

    public function isActive(): bool
    {
        return $this->id == hardback::$instance->getActivePlayerId();
    }

    public function notifyPanel(): void
    {
        hardback::$instance->notifyAllPlayers('panel', '', [
            'player' => $this,
        ]);
    }

    public function notifyInk(HCard $card): void
    {
        hardback::$instance->notifyAllPlayers('ink', '${player_name} uses ink to draw ${icon}${letter}', [
            'player_name' => $this->name,
            'icon' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
        ]);
    }

    public function notifyRemover(HCard $card): void
    {
        hardback::$instance->notifyAllPlayers('ink', '${player_name} uses remover to avoid ${icon}${letter}', [
            'player_name' => $this->name,
            'icon' => $card->getGenreName() . ' ',
            'letter' => $card->getLetter(),
        ]);
    }

    public function addCoins(int $amount, $notify = true): void
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET coins = coins + $amount WHERE player_id = {$this->id}");
        $this->coins += $amount;
        $this->notifyPanel();

        if ($notify) {
            hardback::$instance->notifyAllPlayers('message', '${player_name} earns ${amount}${icon}', [
                'player_name' => $this->name,
                'amount' => $amount,
                'icon' => '¢',
            ]);
        }
    }

    public function addPoints(int $amount, bool $notify = true): void
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET player_score = player_score + $amount WHERE player_id = {$this->id}");
        $this->score += $amount;
        $this->notifyPanel();

        if ($notify) {
            hardback::$instance->notifyAllPlayers('message', '${player_name} earns ${amount}${icon}', [
                'player_name' => $this->name,
                'amount' => $amount,
                'icon' => ' star',
            ]);
        }
    }

    public function addInk(int $amount, bool $notify = true): void
    {
        if ($amount <= 0) {
            return;
        }

        self::DbQuery("UPDATE player SET ink = ink + $amount WHERE player_id = {$this->id}");
        $this->ink += $amount;
        $this->notifyPanel();

        if ($notify) {
            hardback::$instance->notifyAllPlayers('message', '${player_name} earns ${amount}${icon}.', [
                'player_name' => $this->name,
                'amount' => $amount,
                'icon' => ' ink',
            ]);
        }
    }

    public function addRemover(int $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        self::DbQuery("UPDATE player SET remover = remover + $amount WHERE player_id = {$this->id}");
        $this->remover += $amount;
        $this->notifyPanel();

        hardback::$instance->notifyAllPlayers('message', '${player_name} earns ${amount}${icon}.', [
            'player_name' => $this->name,
            'amount' => $amount,
            'icon' => ' ink',
        ]);
    }

    public function spendCoins(int $amount): void
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET coins = coins - $amount WHERE player_id = {$this->id} AND coins >= $amount");
        $this->coins -= $amount;
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have {$amount}¢");
        }
        $this->notifyPanel();
    }

    public function spendInk(int $amount = 1): void
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET ink = ink - $amount WHERE player_id = {$this->id} AND ink >= $amount");
        $this->ink -= $amount;
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have $amount ink");
        }
        $this->notifyPanel();
    }

    public function spendRemover(int $amount = 1): void
    {
        if ($amount <= 0) {
            return;
        }
        self::DbQuery("UPDATE player SET remover = remover - $amount WHERE player_id = {$this->id} AND remover >= $amount");
        $this->remover -= $amount;
        if (self::DbAffectedRow() == 0) {
            throw new BgaUserException("You do not have $amount remover");
        }
        $this->notifyPanel();
    }
}
