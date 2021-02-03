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
    private array $ownTimestamps;

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
        $this->ownTimestamps = array();

        $this->loadData();

        print "\nWelcome to the time tracking application!";
        $validInput = false;
        do {
            print "\nPlease identify with your first and last name: ";
            $input = readline();
            $splitted = explode(" ", $input);
            if (sizeof($splitted) == 2) {
                $isNew = true;
                if ($this->persons != false) {
                    foreach ($this->persons as $person) {
                        if ($person->getFirstName() == $splitted[0]) {
                            if ($person->getLastName() == $splitted[1]) {
                                $isNew = false;
                                $this->currentPerson = $person;
                                break;
                            }
                        }
                    }
                } else
                    $isNew = true;

                if ($isNew) {
                    $this->currentPerson = new Person($splitted[0], $splitted[1]);
                    $this->addPersonToData($this->currentPerson);
                    $this->persons[] = $this->currentPerson;
                }
                $validInput = true;
            }
        } while (!$validInput);
        $this->loadOwnTimestamps();
        $openTimestamp = $this->getOpenTimestamp();
        print "\nWelcome, {$this->currentPerson->getFullName()}!";
        if ($openTimestamp == null) {
            print "\nCurrently there's no uncompleted timestamp. Create one!";
        } else {
            print "\nYour last timestamp:\n{$openTimestamp->getStart()->format('d.m.Y G:i')}   {$openTimestamp->getProject()}";
        }

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

    private function addPersonToData(Person $person) {
        $json = file_get_contents("data.json");
        $data = json_decode($json, true);
        $data['persons'][$person->getUuid()] = $person->toArray();
        $json = json_encode($data);
        file_put_contents("data.json", $json);
    }

    private function getOpenTimestamp() : Timestamp|null {
        foreach ($this->ownTimestamps as $timestamp) {
            if ($timestamp->getEnd() == null) {
                return $timestamp;
            }
        }
        return null;
    }

    private function loadOwnTimestamps() {
        foreach ($this->timestamps as $timestamp) {
            if ($timestamp->getPerson() == $this->currentPerson) {
                $this->ownTimestamps[] = $timestamp;
            }
        }
    }


}

$app = new App();