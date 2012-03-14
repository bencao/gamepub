<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/form.php';

/**
 * Form for favoring a notice
 *
 * @category Form
 * @package  LShai
 *
 * @see      DisfavorForm
 */

class FavorForm extends Form
{
    /**
     * Notice to favor
     */

    var $notice = null;
    var $favorgroups = null;

    /**
     * Constructor
     *
     * @param HTMLOutputter $out    output channel
     * @param Notice        $notice notice to favor
     */

    function __construct($out=null, $notice=null, $favorgroups=null)
    {
        parent::__construct($out);

        $this->notice = $notice;
        $this->favorgroups = $favorgroups;
    }

    /**
     * ID of the form
     *
     * @return int ID of the form
     */

    function id()
    {
        return 'favor-' . $this->notice->id;
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */

    function action()
    {
        return common_path('notice/favor');
    }

    /**
     * Include a session token for CSRF protection
     *
     * @return void
     */

    function sessionToken()
    {
        $this->out->element('input', array('name' => 'token', 'type' => 'hidden', 'value' => common_session_token()));
    }


    /**
     * Legend of the Form
     *
     * @return void
     */
    function formLegend()
    {
        $this->out->element('legend', null, '收藏');
    }


    /**
     * Data elements
     *
     * @return void
     */

    function formData()
    {                          
		//都删除了咋办?
		$content = array();
		$first = true;
	    foreach ($this->favorgroups as $favorgroup) {
	    	if($first) {
	       		$content[$favorgroup->id] = $favorgroup->name;
	       		$first = false;
	       		$value = $favorgroup->name;
	    	} else 
	    		$content[$favorgroup->id] = $favorgroup->name;
	    }
	    $content['sep'] = '--------------------'; //20
	    $content['new'] = '新建收藏夹';
	    $this->out->element('p',null,$this->notice->content);
	    $this->out->elementStart('dl','super_fav clearfix');
	    $this->out->element('dt', null, '添加到收藏夹:');
	    
	    $this->out->elementStart('dd');
	    $this->out->element('input', array('type' => 'text', 'name' => 'favorgroup', 
	    	'id' => 'favorgroup', 'class' => 'text200', 'value' => $value));
	    $this->out->elementStart('a',array('class'=> 'show_dropdown','href'=>'#'));
	    $this->out->element('small',null,'▼');
	    $this->out->elementEnd('a');
	    $this->out->elementStart('ul', array('id' => 'favorselect', 'name' => 'favorselect'));
        foreach ($content as $value => $option) {
            $this->out->elementStart('li');
            $this->out->element('a', array('fid' => $value,  'href' => '#'), $option);
            $this->out->elementEnd('li');
        }
        $this->out->elementEnd('ul');
        $this->out->elementEnd('dd');
        
        $this->out->elementEnd('dl');
        
	    $this->out->elementStart('div', 'op');
	    $this->out->element('input', array('class'=>'confirm button60','type' => 'submit', 'value' => '收藏', 'id' => 'favor-submit-' . $this->notice->id));
	    $this->out->element('a',array('class'=>'cancel button60','href'=>'#'),'取消');
	    $this->out->elementEnd('div');
	    
//	    $this->out->element('div', array('id' => 'theTip', 'style' => 'color: rgb(255, 0, 0);'));
	    
	    $this->out->element('input', array('name' => 'nid', 'type' => 'hidden', 'value' => $this->notice->id));
	    
	    $this->out->element('input', array('name' => 'url', 'type' => 'hidden', 'value' => common_path('notice/favor')));
	    
//	    $this->out->hidden('notice-n'.$this->notice->id,
//                           $this->notice->id,
//                           'nid');  
//		$this->out->hidden('url', common_local_url('favor'));
	    
    }

    /**
     * Action elements
     *
     * @return void
     */

    function formActions()
    {
    }
    
    /**
     * Class of the form.
     *
     * @return string the form's class
     */
    
    function formClass()
    {
        return 'favor';
    }
}
