<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/widget.php';

class ShaiMessageForm extends Widget
{
	
	/**
     * Current action, used for returning to this page.
     */

    var $action = null;

    /**
     * Pre-filled content of the form
     */

    var $content = null;
    
	 /**
     * The current user
     */

    var $user = null;
    
    // used for special module to direct conversion
    var $mode = null;
    var $mode_identifier = null;
    
	/**
     * Constructor
     *
     * @param HTMLOutputter $out     output channel
     * @param string        $action  action to return to, if any
     * @param string        $content content to pre-fill
     */

    function __construct($out=null, $action=null, $content=null, $user=null, $mode=null, $mode_identifier=null)
    {
        parent::__construct($out);

        $this->action  = $action;
        $this->content = $content;
        $this->mode = $mode;
        $this->mode_identifier = $mode_identifier;

        if ($user) {
            $this->user = $user;
        } else {
            $this->user = common_current_user();
        }
        
//        if (common_config('attachments', 'uploads')) {
//            $this->enctype = 'multipart/form-data';
//        }
    }
	
	/**
     * Show the form
     *
     * Uses a recipe to output the form.
     *
     * @return void
     * @see Widget::show()
     */

    function show()
    {
    	$this->out->elementStart('form', array('method' => 'post',
    					'action' => common_path('message/new'),
    					'id' => 'message_form'));
    	$this->out->elementStart('fieldset');
    	$this->out->element('legend', null, '消息表单');
    	
    	$this->showTitle();
    	$this->showForm();
    	
    	$this->out->elementEnd('fieldset');
    	$this->out->elementEnd('form');
    }
    
    function showForm() 
    {
    		$this->out->elementStart('div', 'form');
    		$this->out->element('textarea', array('id' => 'message_data-text',
                                              'class' => 'rounded8',
                                              'name' => 'status_textarea'),
                            ($this->content) ? $this->content : '');    	
    		    	
	    	$this->out->elementStart('span', 'char');
    		$this->out->element('em', array('id' => 'message_text-count'), '280');
    		$this->out->text('字剩余');
    		$this->out->elementEnd('span');
    		
    		$this->out->element('input', array('id' => 'message_action_submit',
                                           'class' => 'submit button76 gray76',
                                           'name' => 'status_submit',
                                           'type' => 'submit',
                                           'value' => '',
    										'title' => '发送'));
    		
    		$this->out->hidden('token', common_session_token());
	        $this->out->hidden('messageto2', null, 'messageto');
    		
    		$this->out->elementEnd('div');
    }

    function showTitle() {			
    	$this->out->elementStart('div', array('class' => 'replyto'));
    	$this->out->element('label', array('for' => 'replyto'), '回复: ');

        $profile = $this->user->getProfile();
        $subs = $profile->getSubscribers(); 
        $subids = array();
        while ($subs->fetch()) {
            $subids[$subs->id] = $subs->nickname;
        }        
        $subs->free();
        unset($subs);
        
       	$this->out->elementStart('select', array('id' => 'replyto', 'name' => 'messageto'));
       	$this->out->element('option', array('value' => 'tip'), '选择关注您的游友');
       	foreach ($subids as $id => $nickname) {
        	$this->out->elementStart('option', array('value' => $id));
        	$this->out->text($nickname);
        	$this->out->elementEnd('option');
        }
		$this->out->elementEnd('select');
    	$this->out->elementEnd('div');
    }
    
}