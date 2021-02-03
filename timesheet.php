<?php

use Zeiterfassung\Timestamp;
use Zeiterfassung\Person;

require_once 'Timestamp.php';
require_once 'Person.php';
/**
 * Main class for the time tracking application
 */
class App {

    public array $persons;
    public array $timestamps;

    private Person $currentPerson;

    /**
     * App constructor.
     */
    public function __construct() {
        $this->persons = array();

        print "Welcome to the time tracking application!";
        $validInput = false;
        do {
            print "\nPlease identify with your first and last name: ";
            $input = readline();
            $splitted = explode(" ", $input);
            if (sizeof($splitted) == 2) {
                $this->currentPerson = new Person($splitted[0], $splitted[1]);
                $this->persons[] = $this->currentPerson;
                $validInput = true;
            }
        } while(!$validInput);
        print "\nWelcome, {$this->currentPerson->getFullName()}";
    }


}

new App();