<?php

namespace Zeiterfassung;

use DateTime;

class Timestamp {

    private DateTime $start;
    private DateTime $end;
    private string $project;
    private Person $person;
    private string $uuid;

    /**
     * Timestamp constructor.
     * @param string $project
     * @param null|string $uuid
     */
    public function __construct(string $project, $uuid = null) {
        $this->project = $project;
        if ($uuid == null) {
            $this->uuid = uniqid();
        } else {
            $this->uuid = $uuid;
        }
    }

    public static function fromArray($array) : Timestamp {
        $timestamp = new Timestamp($array['project'], $array['uuid']);
        if ($array['start'] != '') {
            $date = new DateTime();
            $date->setTimestamp($array['start']);
            $timestamp->setStart($date);
        }
        if ($array['end'] != '') {
            $date = new DateTime();
            $date->setTimestamp($array['end']);
            $timestamp->setEnd($date);
        }
        return $timestamp;
    }

    public function toArray() : array {
        return array(
            'project' => $this->project != null ? $this->project : '',
            'start' => isset($this->start) != null ? $this->start->getTimestamp() : '',
            'end' => isset($this->end) ? $this->end->getTimestamp() : '',
            'person' => isset($this->person) ? $this->person->getUuid() : '',
            'uuid' => $this->uuid
        );
    }

    private function save() {
        $content = file_get_contents("data.json");
        $data = json_decode($content, true);
        $data['timestamps'][$this->uuid] = $this->toArray();
        $content = json_encode($data, JSON_UNESCAPED_UNICODE);
        file_put_contents("data.json", $content);
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
        $this->save();
    }

    /**
     * @return DateTime|null
     */
    public function getEnd(): ?DateTime {
        if (isset($this->end)) {
            return $this->end;
        }
        return null;
    }

    /**
     * @param DateTime $end
     */
    public function setEnd(DateTime $end): void {
        $this->end = $end;
        $this->save();
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
        $this->save();
    }

    /**
     * @return string
     */
    public function getProject(): string {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getUuid(): string {
        return $this->uuid;
    }

}