<?php

if (!defined('SHAISHAI')) {
    exit(1);
}


class MessageList extends Widget
{
    var $message = null;
    var $action = null;
    /**
     * constructor
     *
     * @param Notice $notice stream of notices from DB_DataObject
     */

    function __construct($message, $out=null, $action='inbox')
    {
        parent::__construct($out);
        $this->message = $message;
        $this->action = $action;
    }

    function show()
    {
    	$this->out->elementStart('ol', array('id' => 'notices'));
        $cnt = 0;

    	while ($this->message &&
    			$this->message->fetch() && $cnt <= MESSAGES_PER_PAGE) {
            $cnt++;

            if ($cnt > MESSAGES_PER_PAGE) {
                break;
            }

            $item = $this->newListItem($this->message);
            $item->show();                        
        }
        
        if (0 == $cnt) {
            $this->out->showEmptyList();
        }
        
        $this->out->elementEnd('ol');
        
        return $cnt;
    }


    function newListItem($message)
    {
        return new MessageListItem($message, $this->out, $this->action);
    }
}

class MessageListItem extends Widget
{
	var $message = null;
    var $user = null;
    var $action = null;
    var $profile = null;
    
	function __construct($message, $out=null, $action='inbox')
    {
        parent::__construct($out);
        $this->message  = $message;
        $this->user = common_current_user();
        $this->action = $action;
    }
    
    function show()
    {
    	$this->out->elementStart('li', array('class' => 'notice message',
                                         'id' => 'message-' . $this->message->id));
		    	
        if($this->action == 'inbox')
        	$this->profile = $this->message->getFrom();
        else 
        	$this->profile = $this->message->getTo();      
        	
        $this->showImage($this->profile, $this->message);
        
        $this->out->elementStart('h3'); 
        $this->out->elementStart('a', array('href' => common_path($this->profile->uname),
        			'class' => 'name'));
        $this->out->text($this->profile->nickname);
        $this->out->elementEnd('a');
        if ($this->profile->is_vip) {
        	$this->out->element('strong', null, 'V');
        }
        $this->out->elementEnd('h3');
        
        $this->showNoticeInfo($this->message);
        $this->showNoticeBar();        
        
        $this->out->element('input', array('class' => 'uname', 'type' => 'hidden', 'value'  => $this->profile->uname));
        $this->out->elementEnd('li');
    }
    
    function showImage() {
    	$this->out->elementStart('div', array('class' => 'avatar'));
    	
    	$avatar = $this->profile->getAvatar(AVATAR_STREAM_SIZE, AVATAR_STREAM_SIZE);
        $attrs = array('href' => $this->profile->profileurl);
        if (!empty($this->profile->nickname)) {
            $attrs['title'] = $this->profile->nickname . ' (' . $this->profile->uname . ') ';
        }
    	$this->out->elementStart('a', $attrs);
    	$this->out->element('img', array('src' => ($avatar) ? $avatar->displayUrl() : Avatar::defaultImage(AVATAR_STREAM_SIZE, $this->profile->id, $this->profile->sex),
                                         'alt' => $this->profile->uname));
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('div');
    }
 
    function showNoticeInfo()
    {
    	$this->out->elementStart('div', array('class' => 'content'));
    	$this->out->raw($this->message->rendered);
    	$this->out->elementEnd('div');
    }
    
    function showNoticeBar()
    {
    	$this->out->elementStart('div', 'bar clearfix');
    	$this->out->elementStart('div', 'info');
    	$dt = common_date_iso8601($this->message->created);
        $this->out->element('span', array('class' => 'time timestamp',
                                          'title' => $dt, 'time' => strtotime($this->message->created)),
                            common_date_string($this->message->created));
		$this->showNoticeSource();   
		$this->out->elementEnd('div');  	
		
    	$this->showNoticeOptions();
    	$this->out->elementEnd('div');
    }

    function showNoticeOptions()
    {
    	//收件箱
        $this->out->elementStart('ul', array('class' => 'op'));
        if ($this->message->from_user != $this->user->id) {
        	$this->out->elementStart('li');
    		$this->out->element('a', array('href' => '#',//common_local_url('newmessage'),//message
                'title' => '回复', 'class' => 'mesreply',
    			'nid'=>$this->profile->id,
    			'nickname'=>$this->profile->nickname),
    			'回复');
    		$this->out->elementEnd('li');
				
    		$this->out->element('li', null, '|');	
    		
    		$this->out->elementStart('li');
            $this->out->element('a', array('href' => common_path('message/deleteinbox'),
                'title' => '删除', 'class' => 'mesdelete', 
        		'nid' => $this->message->id),
        		'删除');
			$this->out->elementEnd('li');
			
        } else {
        	$this->out->elementStart('li');
        	$this->out->element('a', array('href' => common_path('message/deleteoutbox'),
                'title' => '删除', 'class' => 'mesdelete', 
        		'nid' => $this->message->id),
        		'删除');
			$this->out->elementEnd('li');
			
        }
        $this->out->elementEnd('ul');
    }
    
    function showNoticeSource()
    {
    	$this->out->raw(common_source_link($this->message->source));
    }
}