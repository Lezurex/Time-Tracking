<?php

namespace Zeiterfassung;

class Person {

    private $firstname;
    private $lastname;

    /**
     * Person constructor.
     * @param $firstname
     * @param $lastname
     */
    public function __construct($firstname, $lastname) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
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
    public function getFullName() : string {
        return $this->firstname . " " . $this->lastname;
    }

}