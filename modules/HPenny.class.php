<?php

class HPenny extends APP_GameClass implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return [
            'gameLength' => hardback::$instance->getGameLength(),
            'genre' => $this->getGenre(),
            'genreCounts' => $this->getGenreCounts(),
            'score' => $this->getScore(),
        ];
    }

    /***** Coop Functions *****/

    public function notifyPanel(): void
    {
        hardback::$instance->notifyAllPlayers('penny', '', [
            'penny' => $this,
        ]);
    }

    public function getDiscardLocation(): string
    {
        return CardMgr::getDiscardLocation(0);
    }

    public function getGenre(): int
    {
        return intval(hardback::$instance->getStat('coopGenre'));
    }

    public function getGenreCounts(): array
    {
        return CardMgr::getGenreCounts(CardMgr::getDiscard(0), true);
    }

    public function getName(): string
    {
        return clienttranslate('Penny Dreadful');
    }

    public function getScore(): int
    {
        return intval(hardback::$instance->getStat('coopScore'));
    }

    public function addPoints(int $amount): void
    {
        if ($amount == 0) {
            return;
        }
        hardback::$instance->incStat($amount, 'coopScore');
        $this->notifyPanel();
    }
}
