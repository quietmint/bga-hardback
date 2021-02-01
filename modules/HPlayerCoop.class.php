<?php

class HPlayerCoop extends HPlayer
{
    protected $genre;

    public function __construct()
    {
        $this->id = 0;
        $this->advert = 0;
        $this->coins = 0;
        $this->color = BLACK;
        $this->eliminated = 0;
        $this->ink = 0;
        $this->name = 'Penny Dreadful';
        $this->order = 99;
        $this->remover = 0;
        $this->score = hardback::$instance->getGameStateValue('coopScore');
        $this->zombie = 0;
    }

    public function jsonSerialize(): array
    {
        $json = parent::jsonSerialize();
        $json['genre'] = $this->getGenre();
        return $json;
    }

    /***** Coop Functions *****/

    public function getGenre()
    {
        return hardback::$instance->getGameStateValue('coopGenre');
    }

    public function addPoints(int $amount, string $stat): void
    {
        if ($amount == 0) {
            return;
        }
        hardback::$instance->incGameStateValue('coopScore', $amount);
        $this->score += $amount;
        $this->notifyPanel();
    }
}
