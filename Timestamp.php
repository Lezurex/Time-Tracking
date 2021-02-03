<?php

namespace Zeiterfassung;

use DateTime;

class Timestamp {

    private DateTime $start;
    private DateTime $end;
    private string $project;

    /**
     * Timestamp constructor.
     * @param DateTime $start
     * @param string $project
     */
    public function __construct(DateTime $start, string $project) {
        $this->start = $start;
        $this->project = $project;
    }

}