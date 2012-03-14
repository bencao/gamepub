<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

require_once(INSTALLDIR.'/lib/daemon.php');
require_once(INSTALLDIR.'/classes/Queue_item.php');
require_once(INSTALLDIR.'/classes/Notice.php');

define('CLAIM_TIMEOUT', 1200);
define('QUEUE_HANDLER_MISS_IDLE', 10);
define('QUEUE_HANDLER_HIT_IDLE', 0);

class QueueHandler extends Daemon
{
    var $_id = 'generic';

    function __construct($id=null, $daemonize=true)
    {
        parent::__construct($daemonize);

        if ($id) {
            $this->set_id($id);
        }
    }

    function timeout()
    {
        return 60;
    }

    function class_name()
    {
        return ucfirst($this->transport()) . 'Handler';
    }

    function name()
    {
        return strtolower($this->class_name().'.'.$this->get_id());
    }

    function get_id()
    {
        return $this->_id;
    }

    function set_id($id)
    {
        $this->_id = $id;
    }

    function transport()
    {
        return null;
    }

    function start()
    {
    }

    function finish()
    {
    }

    function handle_notice($notice)
    {
        return true;
    }

    function run()
    {
        if (!$this->start()) {
            return false;
        }

        $this->log(LOG_INFO, 'checking for queued notices');

        $queue   = $this->transport();
        $timeout = $this->timeout();

        $qm = QueueManager::get();

        $qm->service($queue, $this);

        if (!$this->finish()) {
            return false;
        }
        return true;
    }

    function idle($timeout=0)
    {
        if ($timeout > 0) {
            sleep($timeout);
        }
    }

    function log($level, $msg)
    {
        common_log($level, $this->class_name() . ' ('. $this->get_id() .'): '.$msg);
    }

    function getSockets()
    {
        return array();
    }
}

