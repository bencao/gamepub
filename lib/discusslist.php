<?php

class DiscussionList extends NoticeList
{
    function show()
    {        
		$this->out->elementStart('ul');
        $cnt = 0;
        
        while ($this->notice != null 
        	&& $this->notice->fetch()) {
            $cnt++;
            if ($cnt >= NOTICES_PER_PAGE) {
                break;
            }
            
            $item = new DiscussListItem($this->notice, $this->out);
            $item->show();
        }                 
       
        $this->out->elementEnd('ul');
        
        return $cnt;
    }
}

class DiscussListItem  extends NoticeListItem
{
	function __construct($notice, $out=null)
    {
        $this->out = $out;
        $this->notice  = $notice;
        $this->profile = Profile::staticGet($this->notice->user_id);
        $this->profileUser = $this->profile->getUser();
        $this->user = common_current_user();
    }
    
	function showNoticeBar()
    {
    	$this->out->elementStart('div', 'bar clearfix');
    	$this->out->elementStart('div', 'info');
    	$this->out->elementStart('a', array('rel' => 'bookmark', 'target' => '_blank',
                                            'href' => $this->profile->profileurl));
    	$dt = common_date_iso8601($this->notice->created);
        $this->out->element('span', array('class' => 'time timestamp',
                                          'title' => $dt, 'time' => strtotime($this->notice->created)),
                            common_date_string($this->notice->created));
		$this->out->elementEnd('a');                            
		$this->showNoticeSource();   
		$this->out->elementEnd('div');  	
		
    	$this->showNoticeOptions();
    	$this->out->elementEnd('div');
    }
    
	function showNoticeOptions()
    {
    	if ($this->user) {
	        $this->out->elementStart('ul', array('class' => 'op'));
	
	        if($this->user->id != $this->profile->id) {
		        $this->out->elementStart('li', array('class' => 'discuss'));
		        $address = array('href' => '#','title' => '回复', 'class' => 'toggle','toreply' => $this->profile->nickname);
		    	$this->out->element('a',$address,'回复');
		    	$this->out->elementEnd('li');	
	        }        
	    	
	    	if($this->user->id == $this->profile->id) {	    		
	    		$this->out->elementStart('li', 'delete');
	    		$address = array('href' => common_path('discuss/delete/' . $this->notice->id),'title' => '删除', 'class' => 'delete');
	    		$this->out->element('a',$address, '删除');
	    		$this->out->elementEnd('li'); 
	    	}
	    	$this->out->elementEnd('ul');
    	}
    }
    
	function showStart()
    {
        $this->out->elementStart('li', array('class' => 'notice', 'did' => $this->notice->id));
    }
}

class NoticeDiscussList {
	var $root_notice;
	var $dislist;
	var $cur_user;
	var $total_count;
	var $out;
	
	function __construct($out, $dislist, $root_notice, $total_count, $cur_user) {
		$this->out = $out;
		$this->root_notice = $root_notice;
		$this->dislist = $dislist;
		$this->cur_user = $cur_user;
		$this->total_count = $total_count;
	}
	
	function show() {
		$this->out->elementStart('ol',array('class'=>'discussions rounded5', 'nid' => $this->root_notice->id));                 
   		
        $cur_user_profile = $this->cur_user->getProfile();
        
        $this->out->elementStart('li', 'create');
		$this->out->elementStart('div','avatar');
		$avatar = $cur_user_profile->getAvatar(AVATAR_STREAM_SIZE, AVATAR_STREAM_SIZE);
		$this->out->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $cur_user_profile->id, $cur_user_profile->sex),
                                         'width' => '50',
                                         'height' => '50',
                                         'alt' => $cur_user_profile->nickname));
		$this->out->elementEnd('div');
		
		$this->out->elementStart('form', array('method' => 'post',
	    					'action' => common_path('discuss/new?discussto=' . $this->root_notice->id)));
		$this->out->elementStart('fieldset');
		$this->out->element('legend', null, '发表评论');
		$this->out->element('input', array('type' => 'hidden', 'name' => 'token', 'value' => common_session_token()));
		$this->out->element('textarea',array('name'=>'status_textarea', 'id' => 'status_textarea_discuss_'.$this->root_notice->id));
		$this->out->element('input', array('class' => 'submit button76 green76',
	                                           'name' => 'status_submit',
	                                           'type' => 'submit',
	                                           'value' => '评论'));
		$this->out->element('a', array('href' => '#', 'title' => '插入表情', 'class' => 'emotion'), '表情');
	    $this->out->element('input', array('type' => 'hidden',
	                                               'value' => $this->root_notice->id,
	                                               'name' => 'indiscussto'));
	    $this->out->element('input', array('type' => 'hidden',
	                                               'value' => 'notice',
	                                               'name' => 'from'));
		$this->out->elementEnd('fieldset');
		$this->out->elementEnd('form');
		
		$this->out->elementEnd('li');
		
		$cnt = 0;
        while ($this->dislist != null 
        	&& $this->dislist->fetch()) {
            $cnt ++;
           
            if ($cnt >= 10) {
                break;
            }
			$item = new NoticeDiscussListItem($this->out, $this->dislist, $this->root_notice, $this->cur_user);
            $item->show();
        }
         
         if ($this->total_count - 10 > 0) {
	    	$this->out->elementStart('li','seemore');
	        $this->out->text('后面还有'.($this->total_count - 10).'条评论');
			$this->out->elementStart('a', array('target' => '_blank', 'href'=>common_path('discussionlist/' . $this->root_notice->id)));
	        $this->out->text('点击查看>>');
	        $this->out->elementEnd('a');
	        $this->out->elementEnd('li');
        }
         
        $this->out->element('li','pointer');
    	$this->out->elementEnd('ol');
    	
    	return $cnt;
	}
}

class NoticeDiscussListItem {
	var $discussion;
	var $root_notice;
	var $out;
	var $cur_user;
	
	function __construct($out, $discussion, $root_notice, $cur_user) {
		$this->discussion = $discussion;
		$this->out = $out;
		$this->root_notice = $root_notice;
		$this->cur_user = $cur_user;
	}
	
	function show() {
		$this->out->elementStart('li', array('did' => $this->discussion->id, 'nid' => $this->root_notice->id));
    	
		$user_profile = Profile::staticGet('id', $this->discussion->user_id);
		
    	$this->out->elementStart('div', 'avatar');
    	$avatar = $user_profile->getAvatar(AVATAR_STREAM_SIZE, AVATAR_STREAM_SIZE);
        
    	$this->out->elementStart('a', array('href' => $user_profile->profileurl, 
        	'title' => $user_profile->nickname . '(' . $user_profile->uname . ')'));
    	$this->out->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $user_profile->id, $user_profile->sex),
                                         'width' => '48',
                                         'height' => '48',
                                         'alt' => $user_profile->nickname));
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('div');
    	$this->out->elementStart('h4','clearfix');
    	$this->out->elementStart('a',array('href' => $user_profile->profileurl, 'title'=> $user_profile->nickname));
    	$this->out->text($user_profile->nickname);
    	$this->out->elementEnd('a');

    	$dt = common_date_iso8601($this->discussion->created);
    	$this->out->element('span', array('class' => 'time timestamp',
                                          'title' => $dt, 'time' => strtotime($this->discussion->created)),
                            common_date_string($this->discussion->created));
    	$this->out->elementEnd('h4');
    	
    	if($this->cur_user->id == $user_profile->id)    	
    		$this->out->element('a', array('href' => common_path('discuss/delete/' . $this->discussion->id),
    				'title' => '删除', 'class' => 'delete'), '删除');
    	
    	if($this->cur_user->id != $user_profile->id)
    		$this->out->element('a', array('href' => '#','title' => '回复', 'class' => 'toggle',
    			'toreply' => $user_profile->nickname), '回复');
    	
    	$this->out->elementStart('div', 'content');
//    	$this->out->raw(htmlentities($this->discussion->rendered, ENT_COMPAT, 'utf-8'));
		$this->out->raw($this->discussion->rendered);
    	$this->out->elementEnd('div');
    	$this->out->elementEnd('li');
	}
}
