<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/apiauth.php';

class ApiSystemMessageAction extends ApiAuthAction
{
    var $messages     = null;

    /**
     * Take arguments for running
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     *
     */

    function prepare($args)
    {
        if (! parent::prepare($args)) {return false;}

        $this->user = $this->auth_user;

        if (empty($this->user)) {
            $this->clientError('无此用户!', 404, $this->format);
            return;
        }

        $this->messages = $this->getMessages();

        return true;
    }

    /**
     * Handle the request
     *
     * Show the messages
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        $this->showMessages();
    }

    /**
     * Show the messages
     *
     * @return void
     */

    function showMessages()
    {
        switch($this->format) {
        case 'xml':
            $this->showXmlDirectMessages();
            break;
        case 'json':
            $this->showJsonDirectMessages();
            break;
        default:
            $this->clientError('API方法未找到!', $code = 404);
            break;
        }
    }

    /**
     * Get notices
     *
     * @return array notices
     */

    function getMessages()
    {
    	
        $message = Receive_sysmes::stream($this->user->id, 0, NOTICES_PER_PAGE, 0, 0, $this->since, true);

        $messages = array();

        while ($message->fetch()) {
            $messages[] = clone($message);
        }

        return $messages;
    }

    /**
     * Is this action read only?
     *
     * @param array $args other arguments
     *
     * @return boolean true
     */

    function isReadOnly($args)
    {
        return true;
    }

    /**
     * When was this notice last modified?
     *
     * @return string datestamp of the latest notice in the stream
     */

    function lastModified()
    {
        if (!empty($this->messages)) {
            return strtotime($this->messages[0]->created);
        }

        return null;
    }

    /**
     * Shows a list of direct messages as Twitter-style XML array
     *
     * @return void
     */

    function showXmlDirectMessages()
    {
        $this->initDocument('xml');
        $this->elementStart('system-messages', array('type' => 'array'));

        foreach ($this->messages as $m) {
//        while ($this->messages && $this->messages->fetch()){
        	$dmsg = array();
        	$sys = $m;//System_message::staticGet('id', $m->sysmes_id);
	        $dmsg['content'] = $sys->content;
	        $dmsg['message_type'] = $sys->message_type;
	        $dmsg['created_at'] = $this->dateTwitter($sys->created);
            $this->showTwitterXmlUser($dmsg, 'system-message');
        }

        $this->elementEnd('system-messages');
        $this->endDocument('xml');
    }

    /**
     * Shows a list of direct messages as a JSON encoded array
     *
     * @return void
     */

    function showJsonDirectMessages()
    {
        $this->initDocument('json');

        $dmsgs = array();

        foreach ($this->messages as $m) {
            $dm_array = array();
        	$sys = System_message::staticGet('id', $m->sysmes_id);
	        $dm_array['content'] = $sys->content;
	        $dm_array['message_type'] = $sys->message_type;  
	        $dm_array['created_at'] = $this->dateTwitter($sys->created);      	
            array_push($dmsgs, $dm_array);
        }

        $this->showJsonObjects($dmsgs);
        $this->endDocument('json');
    }

    /**
     * An entity tag for this notice
     *
     * Returns an Etag based on the action name, language, and
     * timestamps of the notice
     *
     * @return string etag
     */

    function etag()
    {
        if (!empty($this->messages)) {

            $last = count($this->messages) - 1;

            return '"' . implode(
                ':',
                array($this->arg('action'),
//                      common_language(),
                      strtotime($this->messages[0]->created),
                      strtotime($this->messages[$last]->created)
                )
            )
            . '"';
        }

        return null;
    }

}
