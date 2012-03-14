<?php
/**
 * LShai, the distributed microblogging tool
 *
 * class for a server exception (user error)
 *
 * @category  Exception
 * @package   LShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Class for server exceptions
 *
 * Subclass of PHP Exception for server errors. The user typically can't fix these.
 *
 * @category Exception
 * @package  LShai
 */

class ServerException extends Exception
{
    public function __construct($message = null, $code = 400) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
