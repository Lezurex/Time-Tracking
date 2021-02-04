<?php

namespace Zeiterfassung;

class Person {

    private string $firstname;
    private string $lastname;
    private string $uuid;

    /**
     * Person constructor.
     * @param $firstname
     * @param $lastname
     * @param null|string $uuid
     */
    public function __construct($firstname, $lastname, $uuid = null) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        if ($uuid == null) {
            $this->uuid = uniqid();
        } else {
            $this->uuid = $uuid;
        }
    }

    /**
     * Create a new Person object from a data array
     * @param $array
     * @return Person
     */
    public static function fromArray($array): Person {
        return new Person($array['firstname'], $array['lastname'], $array['uuid']);
    }

    /**
     * Save this Person object to the data file
     * @return array
     */
    public function toArray(): array {
        return array(
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'uuid' => $this->uuid
        );
    }

    /**
     * @return string
     */
    public function getFirstname(): string {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string {
        return $this->lastname;
    }

    /**
     * @return string First and last name combined in a single string
     */
    public function getFullName(): string {
        return $this->firstname . " " . $this->lastname;
    }

    /**
     * @return string
     */
    public function getUuid(): string {
        return $this->uuid;
    }

}