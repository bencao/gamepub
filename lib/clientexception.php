<?php
/**
 * LShai, the distributed microblogging tool
 *
 * class for a client exception (user error)
 *
 * @category  Exception
 * @package   LShai
 */

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

class ClientException extends Exception
{
    public function __construct($message = null, $code = 400) {
        parent::__construct($message, $code);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
