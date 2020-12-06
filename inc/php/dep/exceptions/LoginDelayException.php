<?php
class LoginDelayException extends Exception {
    private $delay = 0;

    public function __construct($delay) {
        $this->delay = $delay;
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return $this->delay;
    }
}