<?php


if (!defined('SHAISHAI')) {
    exit(1);
}

class BatchinviteHTMLTemplate extends RegisterWizardHTMLTemplate
{
	function greeting() {
		return '邀请已发送给您的好友！';
	}
	
    function title()
    {
        return '已发送邀请';
    }

    function showContent()
	{
		$contacts = $this->arg('invite_contacts');
        
		$this->elementStart('div', array('id' => 'recoverpass'));
		
        $this->element('p', 'intro', '您的朋友已经收到您的邀请');
        
//        $this->elementStart('ul', array('style' => 'padding:5px 20px;'));
//		foreach ($contacts as $c) {
//        	$this->element('li', null, $c[0]);
//        }
//        $this->elementEnd('ul');
        
        $this->elementEnd('div');
        
        $this->elementStart('div', array('style' => 'width:560px;margin:0 auto;font-size:14px;'));
        $this->text('感谢您为社区壮大做出的贡献！当您的朋友受邀注册后，系统将为您增加1个铜G币的财富。请您手动');
        $this->element('a', array('href' => 'javascript:window.close();'), '关闭');
		$this->text('此窗口');
        $this->elementEnd('div');
	}
	
}



?>