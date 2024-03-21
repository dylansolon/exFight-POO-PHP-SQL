<?php

class Character {
    private int $id;
    private string $name;
    private int $damage;

    private CONST DEATH = 100;
    private CONST DAMAGE = 5;

    public CONST CHARACTER_SELF = 1;
    public CONST CHARACTER_DEAD = 2;
    public CONST CHARACTER_HIT = 3;

    public function __construct(array $data) {
        $this->hydrate($data);
    }

    
    public function hydrate(array $data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
    }

    public function hit(Character $character) {
        if ($character->getId() === $this->id) {
            return self::CHARACTER_SELF;
        }

        return $character->receiveDamages();
    }

    public function receiveDamages() {
        $this->damage += self::DAMAGE;

        if ($this->damage >= self::DEATH) {
            return self::CHARACTER_DEAD;
        }
        return self::CHARACTER_HIT;
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }

    public function setDamage($damage) {
        if ($damage >=0 && $damage < self::DEATH) {
            $this->damage = $damage;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDamage() {
        return $this->damage;
    }

}