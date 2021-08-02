<?php

class HCard extends APP_GameClass implements JsonSerializable
{
    private $id;
    private $factor;
    private $ink;
    private $location;
    private $order;
    private $origin;
    private $refId;
    private $resolve;
    private $wild;

    private $next;
    private $previous;

    public function __construct($dbcard)
    {
        $this->id = intval($dbcard['id']);
        $this->factor = intval($dbcard['factor']);
        $this->ink = intval($dbcard['ink']);
        $this->location = $dbcard['location'];
        $this->order = intval($dbcard['order']);
        $this->origin = $dbcard['origin'];
        $this->refId = intval($dbcard['refId']);
        $this->resolve = json_decode($dbcard['resolve']);
        $this->wild = $dbcard['wild'];
    }

    public function __toString(): string
    {
        return ucfirst($this->getGenreName()) . ' ' . $this->getLetter();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'factor' => $this->factor,
            'ink' => $this->hasInk(),
            'location' => $this->location,
            'order' => $this->order,
            'origin' => $this->origin,
            'refId' => $this->refId,
            'wild' => $this->isWild() ? $this->wild : null,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFactor(): int
    {
        return $this->factor;
    }

    public function setFactor(int $factor): void
    {
        $this->factor = $factor;
    }

    public function hasInk(): bool
    {
        return $this->ink == 1;
    }

    public function hasRemover(): bool
    {
        return $this->ink == 2;
    }

    public function setInk(int $inkValue): void
    {
        $this->ink = $inkValue;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function isLocation(...$locations): bool
    {
        if (count($locations) == 1 && is_array($locations[0])) {
            $locations = $locations[0];
        }
        foreach ($locations as $location) {
            if (strpos($this->location, $location) === 0) {
                return true;
            }
        }
        return false;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    public function isOrigin(...$origins): bool
    {
        if (count($origins) == 1 && is_array($origins[0])) {
            $origins = $origins[0];
        }
        foreach ($origins as $origin) {
            if (strpos($this->origin, $origin) === 0) {
                return true;
            }
        }
        return false;
    }

    public function getRefId(): int
    {
        return $this->refId;
    }

    public function isWild(): bool
    {
        return $this->wild != null && !$this->isUncovered();
    }

    public function isUncovered(): bool
    {
        return $this->wild == '_';
    }

    public function setWild(?string $wild): void
    {
        $this->wild = $wild;
    }

    /***** Temporary properties *****/

    public function getNext(): ?HCard
    {
        return $this->next;
    }

    public function setNext(?HCard $next): void
    {
        $this->next = $next;
    }


    public function getPrevious(): ?HCard
    {
        return $this->previous;
    }

    public function setPrevious(?HCard $previous)
    {
        $this->previous = $previous;
    }

    /***** Computed properties *****/

    public function getBenefits(int $benefitId = null): array
    {
        $benefits = [];
        if (!$this->isWild() && !in_array(H_ALL_BENEFITS, $this->resolve)) {
            // Basic benefits
            $basicBenefits = hardback::$instance->cards[$this->refId]['basicBenefits'] ?? [];
            foreach ($basicBenefits as $k => $v) {
                // Filter by ID and already-resolved benefits
                if (($benefitId == null || $benefitId == $k) && !in_array($k, $this->resolve)) {
                    if ($this->factor > 1 && is_numeric($v)) {
                        $v *= $this->factor;
                    }
                    $benefits[] = [
                        'id' => $k,
                        'value' => $v,
                        'activation' => H_FROM_BASIC,
                    ];
                }
            }

            if (CardMgr::isGenreActive($this->getGenre())) {
                // Genre benefits if active
                $genreBenefits = hardback::$instance->cards[$this->refId]['genreBenefits'] ?? [];
                foreach ($genreBenefits as $k => $v) {
                    // Filter by ID and already-resolved benefits
                    if (($benefitId == null || $benefitId == $k) && !in_array($k, $this->resolve)) {
                        if ($this->factor > 1 && is_numeric($v)) {
                            $v *= $this->factor;
                        }
                        $benefits[] = [
                            'id' => $k,
                            'value' => $v,
                            'activation' => H_FROM_GENRE,
                        ];
                    }
                }
            }
        }
        return $benefits;
    }

    public function hasBenefit(int $benefitId): bool
    {
        return !empty($this->getBenefits($benefitId));
    }

    public function getGenre(): int
    {
        return hardback::$instance->cards[$this->refId]['genre'];
    }

    public function isGenre(int $genre): bool
    {
        return $this->getGenre() == $genre;
    }

    public function getGenreName(): string
    {
        switch ($this->getGenre()) {
            case H_STARTER:
                return 'starter';
            case H_ADVENTURE:
                return 'adventure';
            case H_HORROR:
                return 'horror';
            case H_MYSTERY:
                return 'mystery';
            case H_ROMANCE:
                return 'romance';
        }
    }

    public function getLetter(): string
    {
        return $this->isWild() ? $this->wild : hardback::$instance->cards[$this->refId]['letter'];
    }

    public function getCost(): int
    {
        return hardback::$instance->cards[$this->refId]['cost'] ?? 0;
    }

    public function getPoints(): int
    {
        return hardback::$instance->cards[$this->refId]['points'] ?? 0;
    }

    public function isTimeless(): bool
    {
        return hardback::$instance->cards[$this->refId]['timeless'] ?? false;
    }

    public function getOwner(): ?int
    {
        if ($this->isOrigin('timeless')) {
            return intval(substr($this->origin, strrpos($this->origin, '_') + 1));
        }
        return null;
    }
}
