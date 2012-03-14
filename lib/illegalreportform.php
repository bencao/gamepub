<?php
/**
 * ShaiShai, the distributed microblogging tool
 *
 * Form for editing user feedback
 *
 * PHP version 5
 *
 * @category  Form
 * @package   ShaiShai
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/form.php';

/**
 * Form for editing a illegal report
 *
 * @category Form
 * @package  ShaiShai
 */

class IllegalReportForm extends Form
{
	var $illtype = null;
	var $targetid = null;
	
	
    function __construct($out=null, $illtype, $targetid)
    {
    	parent::__construct($out);
        $this->out = $out;
        $this->illtype = $illtype;
        $this->targetid = $targetid;
    }

    /**
     * ID of the form
     *
     * @return string ID of the form
     */

    function id()
    {
        return 'form_illegal_report';
    }

    /**
     * class of the form
     *
     * @return string of the form class
     */

    function formClass()
    {
        return 'form_illegal_report';
    }

    /**
     * Action of the form
     *
     * @return string URL of the action
     */

    function action()
    {
    	return common_path('main/illegalreport');
    }

    /**
     * Name of the form
     *
     * @return void
     */

    function formLegend()
    {
        $this->out->element('legend', null, null);
    }

    /**
     * Data elements of the form
     *
     * @return void
     */

    function formData()
    {

    	$this->out->elementStart('table', array('cellspacing'=>'0', 'cellpadding'=>'0', 'border'=>'0'));
    	$this->out->elementStart('tbody');
    	
    	$this->out->elementStart('tr');
    	$this->out->element('td', 'b_cbf_l', '举报原因 *');
    	$this->out->elementStart('td', 'b_cbf_c');
    	$this->out->elementStart('select', array('name' => 'reason', 'id' => 'reason'));
    	$this->out->option('0', '请选择');
		$this->out->option('1', '内容反动');
		$this->out->option('2', '内容色情');
		$this->out->option('3', '骚扰诈骗');
		$this->out->option('4', '张贴广告');
		$this->out->option('5', '滥发垃圾信息');
    	$this->out->elementEnd('select');
    	$this->out->elementEnd('td');
    	$this->out->elementEnd('tr');
    	
    	$this->out->elementStart('tr', array('id' =>'reasonTip', 'style' => 'display:none; '));
    	$this->out->element('td');
    	$this->out->elementStart('td');
    	$this->out->text('请输选择举报原因');
    	$this->out->elementEnd('td');
    	$this->out->element('td');
    	$this->out->elementEnd('tr');
    	
    	
    	$this->out->elementStart('tr');
    	$this->out->element('td', 'b_cbf_l', '附加说明 *');
    	$this->out->elementStart('td', array('class'=>'b_cbf_ta', 'colspan'=>'2'));
    	$this->out->element('textarea', array('id'=>'description',
                                              'name'=>'description', 
                                              'cols'=>'40', 'rows'=>'5'));
    	$this->out->elementEnd('td');
    	$this->out->elementEnd('tr');
    	
    	$this->out->elementStart('tr', array('id' =>'desTip', 'style' => 'display:none; '));
    	$this->out->element('td');
    	$this->out->elementStart('td');
    	$this->out->text('请添加对这个非法消息的描述, 不超过255个字');
    	$this->out->elementEnd('td');
    	$this->out->element('td');
    	$this->out->elementEnd('tr');
    	
        $this->out->element('input', array('id'=>'illtype', 'name'=>'illtype', 'type'=>'hidden', 
                                           'value'=>$this->illtype));
        $this->out->element('input', array('id'=>'targetid', 'name'=>'targetid', 'type'=>'hidden', 
                                           'value'=>$this->targetid));

    }

    /**
     * Action elements
     *
     * @return void
     */

    function formActions()
    {
    	$this->out->elementStart('tr');
    	$this->out->element('td');
    	$this->out->elementStart('td', 'b_cbf_sm');
    	$this->out->element('input', array('type'=>'submit', 'value'=>'举报'));
    	$this->out->elementStart('a', array('href'=>'#', 'id'=>'cancel_report'));
    	$this->out->text('取消');
    	$this->out->elementEnd('a');
    	$this->out->elementEnd('td');
    	$this->out->element('td');
    	$this->out->elementEnd('tr');
    	
        $this->out->elementEnd('tbody');
        $this->out->elementEnd('table');
    }
}