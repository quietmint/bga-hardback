<?php

class HPlayer extends APP_GameClass implements JsonSerializable
{
    protected $id;
    protected $advert;
    protected $attempts;
    protected $award;
    protected $coins;
    protected $color;
    protected $eliminated;
    protected $ink;
    protected $name;
    protected $order;
    protected $remover;
    protected $score;
    protected $vote;
    protected $word;
    protected $zombie;

    public function __construct($dbplayer)
    {
        $this->id = intval($dbplayer['player_id']);
        $this->advert = intval($dbplayer['advert']);
        $this->attempts = intval($dbplayer['attempts']);
        $this->award = intval($dbplayer['award']);
        $this->coins = intval($dbplayer['coins']);
        $this->color = $dbplayer['player_color'];
        $this->eliminated = intval($dbplayer['player_eliminated']);
        $this->ink = intval($dbplayer['ink']);
        $this->name = $dbplayer['player_name'];
        $this->order = intval($dbplayer['player_no']);
        $this->remover = intval($dbplayer['remover']);
        $this->score = intval($dbplayer['player_score']);
        $this->vote = $dbplayer['vote'] == null ? null : boolval($dbplayer['vote']);
        $this->word = $dbplayer['word'];
        $this->zombie = intval($dbplayer['player_zombie']);
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function jsonSerialize(): array
    {
        return $this->jsonSerializeForPanel() + [
            'color' => $this->color,
            'colorName' => $this->getColorName(),
            'eliminated' => $this->eliminated,
            'name' => $this->name,
            'order' => $this->order,
            'zombie' => $this->zombie,
        ];
    }

    public function jsonSerializeForPanel(): array
    {
        $json = [
            'id' => $this->id,
            'attempts' => $this->attempts,
            'coins' => $this->coins,
            'discardLocation' => $this->getDiscardLocation(),
            'drawLocation' => $this->getDrawLocation(),
            'handLocation' => $this->getHandLocation(),
            'ink' => $this->ink,
            'jailLocation' => $this->getJailLocation(),
            'remover' => $this->remover,
            'score' => $this->score,
            'tableauLocation' => $this->getTableauLocation(),
        ];
        if (hardback::$instance->gamestate->table_globals[H_OPTION_AWARDS]) {
            $json['award'] = $this->award;
        }
        if (hardback::$instance->gamestate->table_globals[H_OPTION_ADVERTS]) {
            $json['advert'] = $this->advert;
        }
        return $json;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAdvert(): int
    {
        return $this->advert;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function getAward(): int
    {
        return $this->award;
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
            case H_RED:
                return 'red';
            case H_GREEN:
                return 'green';
            case H_BLUE:
                return 'blue';
            case H_YELLOW:
                return 'yellow';
            case H_PURPLE:
                return 'purple';
            case H_BLACK:
                return 'black';
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

    public function getVote(): ?bool
    {
        return $this->vote;
    }

    public function getWord(): string
    {
        return $this->word ?? '';
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

    public function getDrawCount(): int
    {
        return CardMgr::getDrawCount($this->id);
    }

    public function getDrawLocation(): string
    {
        return CardMgr::getDrawLocation($this->id);
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

    public function getTableau(string $sort = null, bool $includeTimeless = true): array
    {
        return CardMgr::getTableau($this->id, $sort, $includeTimeless);
    }

    public function getTableauCount(): int
    {
        return CardMgr::getTableauCount($this->id);
    }

    public function getTableauLocation(): string
    {
        return CardMgr::getTableauLocation($this->id);
    }

    public function getJail(): ?HCard
    {
        return CardMgr::getJail($this->id);
    }

    public function getJailLocation(): string
    {
        return CardMgr::getJailLocation($this->id);
    }

    public function getTimeless(bool $origin = false): array
    {
        return CardMgr::getTimeless($this->id, $origin);
    }

    public function getTimelessLocation(): string
    {
        return CardMgr::getTimelessLocation($this->id);
    }

    /***** Player Functions *****/

    public function isActive(): bool
    {
        return $this->id == hardback::$instance->getActivePlayerId();
    }

    public function notifyPanel(string $hint = null): void
    {
        $args = [
            'player' => $this->jsonSerializeForPanel(),
        ];
        if ($hint == 'allScore') {
            $args['allScore'] = $this->score;
        }
        hardback::$instance->notifyAllPlayers('player', '', $args);
    }

    public function notifyInk(HCard $card): void
    {
        hardback::$instance->notifyAllPlayers('ink', hardback::$instance->msg['useInk'], [
            'player_id' => $this->id,
            'player_name' => $this->name,
            'genre' => $card->getGenreName(),
            'letter' => $card->getLetter(),
        ]);
    }

    public function notifyRemover(HCard $card): void
    {
        hardback::$instance->notifyAllPlayers('ink', hardback::$instance->msg['useRemover'], [
            'player_id' => $this->id,
            'player_name' => $this->name,
            'genre' => $card->getGenreName(),
            'letter' => $card->getLetter(),
        ]);
    }

    public function addAttempt(): void
    {
        $this->DbLock();
        self::DbQuery("UPDATE player SET attempts = attempts + 1 WHERE player_id = {$this->id}");
        $this->attempts += 1;
        $this->notifyPanel();
    }

    public function addCoins(int $amount): void
    {
        if ($amount <= 0) {
            return;
        }
        $this->DbLock();
        self::DbQuery("UPDATE player SET coins = coins + $amount WHERE player_id = {$this->id}");
        $this->coins += $amount;
        $this->notifyPanel();
    }

    public function addPoints(int $amount, string $stat): void
    {
        if ($amount == 0) {
            return;
        }
        hardback::$instance->incStat($amount, $stat, $this->id);
        $hint = null;
        $this->DbLock();
        $sql = "UPDATE player SET player_score = player_score + $amount";
        if (hardback::$instance->gamestate->table_globals[H_OPTION_COOP] == H_NO) {
            $sql .= " WHERE player_id = {$this->id}";
        } else {
            $hint = 'allScore';
        }
        self::DbQuery($sql);
        $this->score += $amount;
        $this->notifyPanel($hint);
    }

    public function addInk(int $amount = 1): void
    {
        if ($amount <= 0) {
            return;
        }
        $this->DbLock();
        self::DbQuery("UPDATE player SET ink = ink + $amount WHERE player_id = {$this->id}");
        $this->ink += $amount;
        $this->notifyPanel();
    }

    public function addRemover(int $amount = 1): void
    {
        if ($amount <= 0) {
            return;
        }
        $this->DbLock();
        self::DbQuery("UPDATE player SET remover = remover + $amount WHERE player_id = {$this->id}");
        $this->remover += $amount;
        $this->notifyPanel();
    }

    public function spendCoins(int $amount, bool $notifyPanel = true): void
    {
        if ($amount <= 0) {
            return;
        }
        $this->DbLock();
        self::DbQuery("UPDATE player SET coins = coins - $amount WHERE player_id = {$this->id} AND coins >= $amount");
        $this->coins -= $amount;
        if (self::DbAffectedRow() == 0) {
            throw new BgaVisibleSystemException("spendCoins: $this does not have {$amount}¢");
        }
        if ($notifyPanel) {
            $this->notifyPanel();
        }
    }

    public function spendInk(int $amount = 1, bool $notifyPanel = true): void
    {
        if ($amount <= 0) {
            return;
        }
        $this->DbLock();
        self::DbQuery("UPDATE player SET ink = ink - $amount WHERE player_id = {$this->id} AND ink >= $amount");
        $this->ink -= $amount;
        if (self::DbAffectedRow() == 0) {
            throw new BgaVisibleSystemException("spendInk: $this does not have $amount ink");
        }
        if ($notifyPanel) {
            $this->notifyPanel();
        }
    }

    public function spendRemover(int $amount = 1, bool $notifyPanel = true): void
    {
        if ($amount <= 0) {
            return;
        }
        $this->DbLock();
        self::DbQuery("UPDATE player SET remover = remover - $amount WHERE player_id = {$this->id} AND remover >= $amount");
        $this->remover -= $amount;
        if (self::DbAffectedRow() == 0) {
            throw new BgaVisibleSystemException("spendRemover: $this does not have $amount remover");
        }
        if ($notifyPanel) {
            $this->notifyPanel();
        }
    }

    public function resetAttempts(): void
    {
        $this->DbLock();
        self::DbQuery("UPDATE player SET attempts = 0 WHERE player_id = {$this->id}");
        $this->attempts = 0;
        $this->notifyPanel();
    }

    public function buyAdvert(int $points, int $coins): void
    {
        $this->DbLock();
        self::DbQuery("UPDATE player SET advert = $points WHERE player_id = {$this->id}");
        $this->advert = $points;
        $this->spendCoins($coins, false);
        $this->addPoints($points, 'pointsAdvert');
    }

    public function setAward(int $points): void
    {
        $this->DbLock();
        self::DbQuery("UPDATE player SET award = $points WHERE player_id = {$this->id}");
        $this->award = $points;
    }

    public function setVote(bool $vote): void
    {
        $this->DbLock();
        self::DbQuery("UPDATE player SET vote = " . intval($vote) . " WHERE player_id = {$this->id}");
        hardback::$instance->incStat(1, $vote ? 'votesAccept' : 'votesReject', $this->id);
        $this->vote = $vote;
    }

    public function setWord(?string $word): void
    {
        $this->DbLock();
        self::DbQuery("UPDATE player SET word = " . ($word == null ? "NULL" : "'$word'") . " WHERE player_id = {$this->id}");
        $this->word = $word;
    }

    private function DbLock()
    {
        self::DbQuery("SELECT * FROM global WHERE global_id < 10 FOR UPDATE");
    }
}
