<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/apiauth.php';

class ApiStatusesDiscussListAction extends ApiAuthAction
{
	var $notice   = null;
	var $notice_id = null;

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
        $this->notice_id = (int)$this->trimmed('id');

        if (empty($message_id)) {
            $this->notice_id = (int)$this->arg('id');
        }

        $this->notice = Notice::staticGet((int)$this->notice_id);
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
             $this->clientError('API方法未找到!', 404);
             return;
        }
        if (empty($this->notice)) {
             $this->clientError('没有找到此ID的消息.',
                 404, $this->format);
             return;
         }

         //应当最新的比较好
        $discus_list = Discussion::disListStream($this->notice_id, 0, 3);
        
    	if ($this->format == 'xml') {
            $this->showXmlDiscussion($discus_list);
        } elseif ($this->format == 'json') {
        	$this->showJsonDiscussion($discus_list);
       	}
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

}