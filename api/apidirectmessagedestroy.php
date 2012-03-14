<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/apiauth.php';

class ApiDirectMessageDestroyAction extends ApiAuthAction
{
	var $message    = null;
	var $message_id = null;

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
        $this->message_id = (int)$this->trimmed('id');

        if (empty($this->message_id)) {
            $this->message_id = (int)$this->arg('id');
        }

        $this->message = Message::staticGet((int)$this->message_id);

        return true;
     }

    /**
     * Handle the request
     *
     * Delete the notice and all related replies
     *
     * @param array $args $_REQUEST data (unused)
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);

        if (!in_array($this->format, array('xml', 'json'))) {
             $this->clientError('API方法未找到!', $code = 404);
             return;
        }

         if (!in_array($_SERVER['REQUEST_METHOD'], array('POST', 'DELETE'))) {
             $this->clientError('此方法需要POST或DELETE.',
                 400, $this->format);
             return;
         }

         if (empty($this->message)) {
             $this->clientError('没有找到此ID的私信.',
                 404, $this->format);
             return;
         }

         if ($this->user->id == $this->message->from_user || $this->user->id == $this->message->to_user) {
         	if ($this->arg('sent') == 'true') {
	            $this->message->deleteOutbox();
	        } else {
	            $this->message->deleteInbox();
	        }
	
	         if ($this->format == 'xml') {
	            $this->showSingleXmlDirectMessage($this->message);
	        } elseif ($this->format == 'json') {
	            $this->showSingleJsondirectMessage($this->message);
	        }
         } else {
             $this->clientError('您不能删除其他用户的私信.',
                 403, $this->format);
         }
    }

}