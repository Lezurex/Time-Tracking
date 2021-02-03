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

    private static array $DEFAULT_DATA_STRUCTURE = array(
        "persons" => array(),
        "timestamps" => array()
    );

    private Person $currentPerson;

    /**
     * App constructor.
     */
    public function __construct() {
        $this->persons = array();
        $this->timestamps = array();

        $this->loadData();

        print "\nWelcome to the time tracking application!";
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
        } while (!$validInput);
        print "\nWelcome, {$this->currentPerson->getFullName()}!";
        var_dump($this->persons);
        var_dump($this->timestamps);
    }

    private function loadData() {
        $json = null;
        if (false == ($json = @file_get_contents("data.json"))) {
            file_put_contents("data.json", json_encode(self::$DEFAULT_DATA_STRUCTURE));
        } else {
            $data = json_decode($json, true);
            if (!is_null($data) && isset($data['timestamps']) && isset($data['persons'])) {
                foreach ($data['persons'] as $personData) {
                    $person = Person::fromArray($personData);
                    $this->persons[$person->getUuid()] = $person;
                }
                foreach ($data['timestamps'] as $timestampData) {
                    $timestamp = Timestamp::fromArray($timestampData);
                    $timestamp->setPerson($this->persons[$timestampData['person']]);
                    $this->timestamps[] = $timestamp;
                }
            } else {
                $this->regenerationSequence();
            }
        }
    }

    private function regenerationSequence() {
        print "The data file is corrupt or not readable. Do you want to regenerate it? This will result in data loss! (y/n) ";
        $input = readline();
        if (strtoupper($input) == "Y") {
            file_put_contents("data.json", json_encode(self::$DEFAULT_DATA_STRUCTURE));
            print "\nData file regenerated!";
            $this->loadData();
            return;
        }
        print "\nTry repairing data.json by yourself, then restart. Exiting now.";
        exit();
    }


}

$app = new App();