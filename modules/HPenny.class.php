<?php

class HPenny extends APP_GameClass implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return [
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

    public function getGenre(): int
    {
        return intval(hardback::$instance->getGameStateValue('coopGenre'));
    }

    public function getGenreCounts(): array
    {
        return CardMgr::getGenreCounts(CardMgr::getCardsInLocation('penny'), true);
    }

    public function getName(): string
    {
        return clienttranslate('Penny Dreadful');
    }

    public function getScore(): int
    {
        return intval(hardback::$instance->getGameStateValue('coopScore'));
    }

    public function addPoints(int $amount): void
    {
        if ($amount == 0) {
            return;
        }
        hardback::$instance->incGameStateValue('coopScore', $amount);
        $this->notifyPanel();
    }
}
