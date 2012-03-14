<?php
if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR . '/lib/validatetool.php';

class UpdatesubscriptionstagAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	
    	$user = common_current_user();
    	$tagged = $this->trimmed('tagged');
    	$tid = $this->trimmed('tid');
    	$add = $this->trimmed('add');
    	$tag = $this->trimmed('tag');
    	
    	if (! isValidFriendGroupName($tag)) {
    		$this->clientError('组名长度应为1~8个字');
    		return ;
    	}
    	
    	$this->view = TemplateFactory::get('JsonTemplate');
        $this->view->init_document($this->paras);
        
    	if ($this->arg('new')) {
    		$tid = User_tag::getTagid($user->id, $tag);
    		
    		if (! $tid) {
    			$tid = User_tag::addATag($user->id, $tag);
    		}
            if (Tagtions::addTag($user->id, $tagged, $tid)) {
                $tagLink = common_local_url($user->uname . '/subscriptions?tag=' . $tag);
            	$this->view->show_json_objects(array('result' => 'true', 'tag' => $tag, 'pid' => $tagged, 'link' => $tagLink));
            } else {
            	$this->view->show_json_objects(array('result' => 'false'));
            }
    		
    	} else if (($add == 'true' && Tagtions::addTag($user->id, $tagged, $tid))
        	|| ($add == 'false' && Tagtions::delTag($user->id, $tagged, $tid))) {
        	$this->view->show_json_objects(array('result' => 'true', 'tag' => $tag, 'pid' => $tagged, 'add' => $add));
        } else {
        	$this->view->show_json_objects(array('result' => 'false'));
        }
        
        $this->view->end_document();
        
    }
}


?>