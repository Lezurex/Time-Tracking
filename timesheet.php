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

    private const DEFAULT_DATA_STRUCTURE = array(
        "persons" => array(),
        "timestamps" => array()
    );
    private const MENU_ITEMS = array(
        "Make stamp",
        "See current project",
        "See summary of timestamps"
    );


    private Person $currentPerson;

    /**
     * App constructor.
     */
    public function __construct() {
        $this->persons = array();
        $this->timestamps = array();
        $this->ownTimestamps = array();

        date_default_timezone_set("Europe/Zurich");

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
            $this->clear();
        } while (!$validInput);
        $this->clear();
        $this->loadOwnTimestamps();
        $openTimestamp = $this->getOpenTimestamp();
        print "\nWelcome, {$this->currentPerson->getFullName()}! You can cancel an action by typing C and Enter.";
        if ($openTimestamp == null) {
            print "\nCurrently there's no uncompleted project. Create one!";
        } else {
            print "\nYour last timestamp:\n{$openTimestamp->getStart()->format('d.m.Y G:i')}   {$openTimestamp->getProject()}";
        }
        $exit = false;
        do {
            print "\n";
            foreach (self::MENU_ITEMS as $key => $item) {
                $id = $key + 1;
                print "\n$id) $item";
            }
            print "\n";
            $input = readline("Selection (1-" . sizeof(self::MENU_ITEMS) . "): ");
            $this->doAction($input);
        } while ($exit == false);
    }

    /**
     * Execute an action associated with an id
     * @param $id
     * @return bool|null
     */
    private function doAction($id): ?bool {
        $this->clear();
        if (!is_numeric($id)) {
            print "\nThis selection is not valid!";
            return false;
        }
        switch ($id) {
            case 1: // Make stamp
                $openTimestamp = $this->getOpenTimestamp();
                if ($openTimestamp != null) {
                    // close existing timestamp
                    print "\nYour last timestamp was:\n{$openTimestamp->getStart()->format('d.m.Y G:i')}   {$openTimestamp->getProject()}";
                    $validInput = false;
                    $date = null;
                    do {
                        print "\n";
                        $input = readline("When do you want to end this timestamp (DD.MM.YYYY HH:MM)? (Leave blank for current time) ");
                        if ($input == "") {
                            $validInput = true;
                            $date = new DateTime();
                            $openTimestamp->setEnd($date);
                            print "\nTimestamp ended successfully!";
                        } elseif (strtoupper($input) == "C") {
                            print "\nAction cancelled.";
                            return null;
                        } else {
                            $date = DateTime::createFromFormat("d.m.Y H:i", $input);
                            if ($date == false) {
                                print "\nThis is not a valid date! (DD.MM.YYYY HH:MM)";
                            } else {
                                if ($date->getTimestamp() > $openTimestamp->getStart()->getTimestamp()) {
                                    $openTimestamp->setEnd($date);
                                    $validInput = true;
                                    print "\nTimestamp ended successfully!";
                                } else {
                                    print "\nThe ending date has to be after the starting date!";
                                }
                            }
                        }
                    } while ($validInput == false);
                } else {
                    print "\nCurrently there's no uncompleted project. Let's create one!";
                    $validInput = false;
                    $timestamp = null;
                    do {
                        print "\n";
                        $input = readline("What is your activity/project called? ");
                        if (strtoupper($input) == "C") {
                            print "\nAction cancelled.";
                            return null;
                        } elseif ($input != "") {
                            $timestamp = new Timestamp($input);
                            $validInput = true;
                        }
                    } while ($validInput == false);

                    $validInput = false;
                    do {
                        print "\n";
                        $input = readline("When do you want to start this timestamp (DD.MM.YYYY HH:MM)? (Leave blank for current time) ");
                        if ($input == "") {
                            $validInput = true;
                            $date = new DateTime();
                            $timestamp->setStart($date);
                        } elseif (strtoupper($input) == "C") {
                            print "\nAction cancelled.";
                            unset($timestamp);
                            return null;
                        } else {
                            $date = DateTime::createFromFormat("d.m.Y H:i", $input);
                            if ($date == false) {
                                print "\nThis is not a valid date! (DD.MM.YYYY HH:MM)";
                            } else {
                                $timestamp->setStart($date);
                                $validInput = true;
                            }
                        }
                    } while ($validInput == false);
                    $timestamp->setPerson($this->currentPerson);
                    $this->timestamps[$timestamp->getUuid()] = $timestamp;
                    $this->ownTimestamps[$timestamp->getUuid()] = $timestamp;
                    print "\nNew timestamp for activity {$timestamp->getProject()} which started on "
                        . $timestamp->getStart()->format("d.m.Y") . " at "
                        . $timestamp->getStart()->format("H:i") . " was created!";
                }
                break;
            case 2:
                $timestamp = $this->getOpenTimestamp();
                if ($timestamp == null) {
                    print "\nCurrently there's no uncompleted project. Create one!";
                } else {
                    print "\nYour last timestamp was:\n{$timestamp->getStart()->format('d.m.Y G:i')}   {$timestamp->getProject()}";
                }
                break;
            case 3:
                $validInput = false;
                if (sizeof($this->ownTimestamps) != 0) {
                    do {
                        print "\n";
                        $input = readline("How many timestamps would you like to see? ");
                        if (is_numeric($input)) {
                            $validInput = true;
                            $timestamps = array_reverse($this->ownTimestamps);
                            $count = 0;
                            foreach ($timestamps as $timestamp) {
                                if ($count < $input) {
                                    $end = $timestamp->getEnd() != null ? $timestamp->getEnd()->format('d.m.Y H:i') : '-               ';
                                    print "\n{$timestamp->getStart()->format('d.m.Y H:i')} until $end   {$timestamp->getProject()}";
                                    $count++;
                                } else {
                                    break;
                                }
                            }
                        } elseif (strtoupper($input) == "C") {
                            print "\nAction cancelled.";
                            return null;
                        }
                    } while (!$validInput);
                } else {
                    print "\nYou currently don't have any timestamps. Create one! ";
                    readline();
                }
                break;
            default:
                print "\nThis selection is not valid!";
                return false;
        }
        return true;
    }

    /**
     * Load the data from the data.json file
     */
    private function loadData() {
        $json = null;
        if (($json = @file_get_contents("data.json")) == false) {
            file_put_contents("data.json", json_encode(self::DEFAULT_DATA_STRUCTURE));
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

    /**
     * Start the data file regeneration sequence
     */
    private function regenerationSequence() {
        print "The data file is corrupt or not readable. Do you want to regenerate it? This will result in data loss! (y/n) ";
        $input = readline();
        if (strtoupper($input) == "Y") {
            file_put_contents("data.json", json_encode(self::DEFAULT_DATA_STRUCTURE));
            print "\nData file regenerated!";
            $this->loadData();
            return;
        }
        print "\nTry repairing data.json by yourself, then restart. Exiting now.\n";
        readline("Press any key to terminate the application.");
        exit();
    }

    /**
     * Add a new person to the data file
     * @param Person $person
     */
    private function addPersonToData(Person $person) {
        $json = file_get_contents("data.json");
        $data = json_decode($json, true);
        $data['persons'][$person->getUuid()] = $person->toArray();
        $json = json_encode($data);
        file_put_contents("data.json", $json);
    }

    /**
     * Gets an opened timestamp, if existent
     * @return Timestamp|null
     */
    private function getOpenTimestamp(): Timestamp|null {
        foreach ($this->ownTimestamps as $timestamp) {
            if ($timestamp->getEnd() == null) {
                return $timestamp;
            }
        }
        return null;
    }

    /**
     * Loads timestamps only for the user
     */
    private function loadOwnTimestamps() {
        foreach ($this->timestamps as $timestamp) {
            if ($timestamp->getPerson() == $this->currentPerson) {
                $this->ownTimestamps[] = $timestamp;
            }
        }
    }

    private function clear() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $clear = "cls";
        } else {
            $clear = "clear";
        }
        popen($clear, "w");
    }
}

$app = new App();