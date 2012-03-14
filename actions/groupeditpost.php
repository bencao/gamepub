<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

/**
 * Edit group post
 *
 *
 * @category Group
 */

class GroupeditpostAction extends GroupAdminAction
{

    function handle($args)
    {
        parent::handle($args);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->trySave();
        } else {
        	$this->addPassVariable('post', $this->cur_group->post);
            $this->showForm();
        }
    }

    function showForm($msg=null, $success=null)
    {
        $this->addPassVariable('group', $this->cur_group);
        $this->addPassVariable('msg', $msg);
        if ($success){
            $this->addPassVariable('success', $success);
        }
        $this->displayWith('GroupeditpostHTMLTemplate');
    }

    function trySave()
    {
        $post = $this->trimmed('post');        
        if (!is_null($post) && mb_strlen($post, 'utf-8') > 35) {
        	$this->addPassVariable('post', $post);
            $this->showForm('公告太长了 (超过35个字)。');
            return;
        } 

        $this->cur_group->query('BEGIN');

        $result = $this->cur_group->updatePost($post);

        if (!$result) {
            common_log_db_error($this->cur_group, 'UPDATE', __FILE__);
            $this->serverError('不能更新' . GROUP_NAME() . '的公告。');
        }
        
        $options = array('reply_to' => null, 'uri' => null, 
								'created' => null, 'addRendered' => null, 
								'is_banned' => 0, 'content_type' => 1, 'topic_type' => 4, 
								'retweet_from' => null, 
								'reply_to_uname' => null);
        $notice = Notice::saveNew($this->cur_user->id, '!'. $this->cur_group->uname .' 公告更新：'.$post, null, 
        							'web', 1, $options);

        $this->cur_group->query('COMMIT');

        $this->showForm('公告已保存！', true);

    }
}

