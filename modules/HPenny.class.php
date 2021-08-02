<?php

class HPenny extends APP_GameClass implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return [
            'gameLength' => hardback::$instance->getGameLength(),
            'genre' => $this->getGenre(),
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
        return intval(hardback::$instance->getStat('coopGenre'));
    }

    public function isGenreActive(int $genre): bool
    {
        if ($this->getGenre() == $genre) {
            if ($genre == H_ADVENTURE || $genre == H_HORROR) {
                $offer = CardMgr::getOffer();
                foreach ($offer as $offerCard) {
                    if ($offerCard->getGenre() == $genre) {
                        return true;
                    }
                }
                return false;
            } else {
                return true;
            }
        }
        return false;
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
