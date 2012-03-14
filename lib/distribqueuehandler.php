<?php

if (!defined('SHAISHAI')) { exit(1); }

/**
 * Base class for queue handlers.
 *
 * As extensions of the Daemon class, each queue handler has the ability
 * to launch itself in the background, at which point it'll pass control
 * to the configured QueueManager class to poll for updates.
 *
 * Subclasses must override at least the following methods:
 * - transport
 * - handle_notice
 */

class DistribQueueHandler
{
    /**
     * Return transport keyword which identifies items this queue handler
     * services; must be defined for all subclasses.
     *
     * Must be 8 characters or less to fit in the queue_item database.
     * ex "email", "jabber", "sms", "irc", ...
     *
     * @return string
     */

    function transport()
    {
        return 'distrib';
    }

    /**
     * Here's the meat of your queue handler -- you're handed a Notice
     * object, which you may do as you will with.
     *
     * If this function indicates failure, a warning will be logged
     * and the item is placed back in the queue to be re-run.
     *
     * @param Notice $notice
     * @return boolean true on success, false on failure
     */
    function handle($notice)
    {
        // XXX: do we need to change this for remote users?

//    	common_debug('distribe');
        try {
            $notice->addToInboxes();
        } catch (Exception $e) {
            $this->logit($notice, $e);
        }

        try {
            Event::handle('EndNoticeSave', array($notice));
            // Enqueue for other handlers
        } catch (Exception $e) {
            $this->logit($notice, $e);
        }

//        try {
//            common_enqueue_notice($notice);
//        } catch (Exception $e) {
//            $this->logit($notice, $e);
//        }

        return true;
    }

    protected function logit($notice, $e)
    {
        common_log(LOG_ERR, "Distrib queue exception saving notice $notice->id: " .
            $e->getMessage() . ' ' .
            str_replace("\n", " ", $e->getTraceAsString()));

        // We'll still return true so we don't get stuck in a loop
        // trying to run a bad insert over and over...
    }
}

