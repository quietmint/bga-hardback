<?php

class HCard extends APP_GameClass implements JsonSerializable
{
    private $id;
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
        $this->id = intval($dbcard['card_id']);
        $this->ink = intval($dbcard['ink']);
        $this->location = $dbcard['card_location'];
        $this->order = intval($dbcard['card_location_arg']);
        $this->origin = $dbcard['card_type'];
        $this->refId = intval($dbcard['card_type_arg']);
        $this->resolve = json_decode($dbcard['resolve']);
        $this->wild = $dbcard['wild'];
    }

    public function __toString(): string
    {
        return $this->getGenreName() . ' ' . $this->getLetter();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'ink' => $this->hasInk(),
            'location' => $this->location,
            'order' => $this->order,
            'origin' => $this->origin,
            'refId' => $this->refId,
            'wild' => $this->wild,
        ];
    }

    public function getId(): int
    {
        return $this->id;
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
        return in_array($this->location, $locations);
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

    public function isOrigin(array ...$origins): bool
    {
        if (count($origins) == 1 && is_array($origins[0])) {
            $origins = $origins[0];
        }
        return in_array($this->origin, $origins);
    }

    public function getRefId(): int
    {
        return $this->refId;
    }

    public function isWild(): bool
    {
        return $this->wild != null;
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

    public function getGenre(): int
    {
        return CardMgr::$refCards[$this->refId]['genre'];
    }

    public function isGenre(int $genre): bool
    {
        return $this->getGenre() == $genre;
    }

    public function getGenreName(): string
    {
        switch ($this->getGenre()) {
            case STARTER:
                return 'Starter';
            case ADVENTURE:
                return 'Adventure';
            case HORROR:
                return 'Horror';
            case MYSTERY:
                return 'Mystery';
            case ROMANCE:
                return 'Romance';
        }
    }

    public function getLetter(): string
    {
        return $this->wild ?? CardMgr::$refCards[$this->refId]['letter'];
    }

    public function getBenefits(): array
    {
        if ($this->wild) {
            return [];
        }
        $benefits = CardMgr::$refCards[$this->refId]['basicBenefits'] ?? [];
        if (CardMgr::isGenreActive($this->getGenre())) {
            $genre = CardMgr::$refCards[$this->refId]['genreBenefits'] ?? [];
            foreach ($genre as $k => $v) {
                if (isset($benefits[$k])) {
                    $benefits[$k] += $v;
                } else {
                    $benefits[$k] = $v;
                }
            }
        }
        $benefits = array_filter($benefits, function ($benefit) {
            return !in_array($benefit, $this->resolve);
        }, ARRAY_FILTER_USE_KEY);
        return $benefits;
    }

    public function hasBenefit(int $benefitId): bool
    {
        return array_key_exists($benefitId, $this->getBenefits());
    }

    public function getBenefitValue(int $benefitId)
    {
        return $this->getBenefits()[$benefitId] ?? null;
    }

    public function getCost(): int
    {
        return CardMgr::$refCards[$this->refId]['cost'] ?? 0;
    }

    public function isTimeless(): bool
    {
        return CardMgr::$refCards[$this->refId]['timeless'] ?? false;
    }
}
