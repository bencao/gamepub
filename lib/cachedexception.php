<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Class for client exceptions
 *
 * Subclass of PHP Exception for user errors.
 *
 * @category Exception
 * @package  LShai
 */

class CachedException extends Exception
{
    public function __construct($message = '已缓存', $code = 200) {
        parent::__construct($message, $code);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}