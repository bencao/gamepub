<?php

if(!defined('SHAISHAI')){
	exit(1);
}

class GetsubscribeAction extends ShaiAction{
	var $nickname = null;
	var $user = null;
	
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->nickname = $this->trimmed('nickname');
		$this->user = $this->cur_user;
		return true;
	}
	
	function handle($args){
		$result = array(
        	'subs' => array()
        );
        $subs = $this->user->getSubscribers();
        if ($subs->N == 0) {
        	$result['result'] = 'false';
        	$result['msg'] = '您还没有关注者哦，不能发私信';
        } else {
        	$result['result'] = 'true';
        	while ($subs->fetch()) {
				$result['subs'][] = array('id'=>$subs->id,'nickname'=>$subs->nickname);
		    }
		    $subs->free();
        }
        
        $this->showJsonResult($result);
	}
}

?>