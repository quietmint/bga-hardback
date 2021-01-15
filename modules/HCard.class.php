<?php

class HCard extends APP_GameClass implements JsonSerializable
{
    private $id;
    private $ink;
    private $location;
    private $order;
    private $origin;
    private $refId;
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
        $this->wild = $dbcard['wild'];
    }

    public function __toString()
    {
        return $this->getGenreName() . ' ' . $this->getLetter();
    }

    public function jsonSerialize()
    {
        $array = [
            'id' => $this->id,
            'location' => $this->location,
            'order' => $this->order,
            'origin' => $this->origin,
            'refId' => $this->refId,
        ];
        if ($this->ink == 1) {
            $array['ink'] = true;
        }
        if ($this->wild) {
            $array['wild'] = $this->wild;
        }
        return $array;
    }

    public function getId()
    {
        return $this->id;
    }

    public function hasInk()
    {
        return $this->ink == 1;
    }

    public function hasRemover()
    {
        return $this->ink == 2;
    }

    public function setInk($inkValue)
    {
        $this->ink = $inkValue;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function isLocation(...$locations)
    {
        if (count($locations) == 1 && is_array($locations[0])) {
            $locations = $locations[0];
        }
        return in_array($this->location, $locations);
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder()
    {
        return $this->order;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    public function getRefId()
    {
        return $this->refId;
    }

    public function isWild()
    {
        return $this->wild != null;
    }

    public function setWild($wild)
    {
        $this->wild = $wild;
    }

    /***** Temporary properties *****/

    public function getNext()
    {
        return $this->next;
    }

    public function setNext($next)
    {
        $this->next = $next;
    }


    public function getPrevious()
    {
        return $this->previous;
    }

    public function setPrevious($previous)
    {
        $this->previous = $previous;
    }

    /***** Computed properties *****/

    public function getRef()
    {
        return CardMgr::$refCards[$this->refId];
    }

    public function getGenre()
    {
        return CardMgr::$refCards[$this->refId]['genre'];
    }

    public function isGenre($genre)
    {
        return in_array($this->genre, $genre);
    }

    public function getGenreName()
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

    public function getLetter()
    {
        return $this->wild ?? CardMgr::$refCards[$this->refId]['letter'];
    }

    public function getBenefits()
    {
        $active = CardMgr::isGenreActive($this->getGenre());
        if ($this->wild) {
            return [];
        }
        $basic = CardMgr::$refCards[$this->refId]['basicBenefits'] ?? [];
        $genre = CardMgr::$refCards[$this->refId]['genreBenefits'] ?? [];
        return ($active && !empty($genre)) ? array_merge($basic, $genre) : $basic;
    }

    public function hasBenefit($benefitId)
    {
        return array_key_exists($benefitId, $this->getBenefits());
    }

    public function getBenefitValue($benefitId)
    {
        return $this->getBenefits()[$benefitId] ?? null;
    }

    public function getCost()
    {
        return CardMgr::$refCards[$this->refId]['cost'] ?? 0;
    }

    public function isTimeless()
    {
        return CardMgr::$refCards[$this->refId]['timeless'] ?? false;
    }
}
