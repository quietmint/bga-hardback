<?php

class HPenny extends APP_GameClass implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'gameLength' => hardback::$instance->getGameLength(),
            'genre' => $this->getGenre(),
            'score' => $this->getScore(),
        ];
    }

    /***** Coop Functions *****/

    public function getId(): int
    {
        // In studio, it should be player _guest01 = 2332442
        // In prod, it should be player _hotseat01 = 84634030
        global $g_config;
        return $g_config['hotseat_acccounts'][0];
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
        hardback::$instance->enqueuePenny();
    }
}
