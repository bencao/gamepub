<?php
/*
 * LShai - a distributed microblogging tool
 */

if (!defined('SHAISHAI')) { exit(1); }

require_once(INSTALLDIR.'/lib/queuehandler.php');

define('PING_INTERVAL', 120);

/**
 * Common superclass for all XMPP-using queue handlers. They all need to
 * service their message queues on idle, and forward any incoming messages
 * to the XMPP listener connection. So, we abstract out common code to a
 * superclass.
 */

class XmppQueueHandler extends QueueHandler
{
    var $pingid = 0;
    var $lastping = null;

    function start()
    {
        # Low priority; we don't want to receive messages
        $this->log(LOG_INFO, "INITIALIZE");
        $this->conn = jabber_connect($this->_id.$this->transport());
        if ($this->conn) {
            $this->conn->addEventHandler('message', 'forward_message', $this);
            $this->conn->addEventHandler('reconnect', 'handle_reconnect', $this);
            $this->conn->setReconnectTimeout(600);
            jabber_send_presence("Send me a message to post a notice", 'available', null, 'available', -1);
        }
        return !is_null($this->conn);
    }

    function timeout()
    {
        return 10;
    }

    function handle_reconnect(&$pl)
    {
        $this->conn->processUntil('session_start');
        $this->conn->presence(null, 'available', null, 'available', -1);
    }

    function idle($timeout=0)
    {
        # Process the queue for as long as needed
        try {
            if ($this->conn) {
                $this->log(LOG_DEBUG, "Servicing the XMPP queue.");
                $this->conn->processTime($timeout);
                $now = time();
                if (empty($this->lastping) || $now - $this->lastping > PING_INTERVAL) {
                    $this->sendPing();
                    $this->lastping = $now;
                }
            }
        } catch (XMPPHP_Exception $e) {
            $this->log(LOG_ERR, "Got an XMPPHP_Exception: " . $e->getMessage());
            die($e->getMessage());
        }
    }

    function sendPing()
    {
        $jid = jabber_daemon_address().'/'.$this->_id.$this->transport();
        $server = common_config('xmpp', 'server');

        if (!isset($this->pingid)) {
            $this->pingid = 0;
        } else {
            $this->pingid++;
        }

        $this->log(LOG_DEBUG, "Sending ping #{$this->pingid}");

		$this->conn->send("<iq from='{$jid}' to='{$server}' id='ping_{$this->pingid}' type='get'><ping xmlns='urn:xmpp:ping'/></iq>");
    }

    function forward_message(&$pl)
    {
        if ($pl['type'] != 'chat') {
            $this->log(LOG_DEBUG, 'Ignoring message of type ' . $pl['type'] . ' from ' . $pl['from']);
            return;
        }
        $listener = $this->listener();
        if (strtolower($listener) == strtolower($pl['from'])) {
            $this->log(LOG_WARNING, 'Ignoring loop message.');
            return;
        }
        $this->log(LOG_INFO, 'Forwarding message from ' . $pl['from'] . ' to ' . $listener);
        $this->conn->message($this->listener(), $pl['body'], 'chat', null, $this->ofrom($pl['from']));
    }

    function ofrom($from)
    {
        $address = "<addresses xmlns='http://jabber.org/protocol/address'>\n";
        $address .= "<address type='ofrom' jid='$from' />\n";
        $address .= "</addresses>\n";
        return $address;
    }

    function listener()
    {
        if (common_config('xmpp', 'listener')) {
            return common_config('xmpp', 'listener');
        } else {
            return jabber_daemon_address() . '/' . common_config('xmpp','resource') . 'daemon';
        }
    }

    function getSockets()
    {
        return array($this->conn->getSocket());
    }
}
