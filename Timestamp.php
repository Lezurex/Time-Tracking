<?php

namespace Zeiterfassung;

use DateTime;

class Timestamp {

    private DateTime $start;
    private DateTime $end;
    private string $project;
    private Person $person;

    /**
     * Timestamp constructor.
     * @param string $project
     * @param Person $person
     */
    public function __construct(string $project) {
        $this->project = $project;
    }

    public static function fromArray($array) : Timestamp {
        return new Timestamp($array['project']);
    }

    public function toArray() : array {
        return array(
            'project' => $this->project,
            'start' => $this->start,
            'end' => $this->end,
            'person' => $this->person->getUuid()
        );
    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime {
        return $this->start;
    }

    /**
     * @param DateTime $start
     */
    public function setStart(DateTime $start): void {
        $this->start = $start;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime {
        return $this->end;
    }

    /**
     * @param DateTime $end
     */
    public function setEnd(DateTime $end): void {
        $this->end = $end;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson(Person $person): void {
        $this->person = $person;
    }

    /**
     * @return string
     */
    public function getProject(): string {
        return $this->project;
    }

}