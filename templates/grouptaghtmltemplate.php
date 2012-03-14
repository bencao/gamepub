<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GrouptagHTMLTemplate extends GroupdesignHTMLTemplate
{
    function title()
    {
        sprintf("' . GROUP_NAME() . ' %s 中含有标签  %s 的消息", 
                           $this->cur_group->uname, $this->arg('tag'));
    }
    
    function showPageNotice()
    {
    	$this->element('div', array('class'=>'b_t'), '本' . GROUP_NAME() . '标签 > ' . $this->arg('tag'));
    	
    	$this->elementStart('div', array('class'=>'b_pi')); 
    	$this->text(sprintf('以下为本' . GROUP_NAME() . '中含有标签 %s 的消息。  ',$this->arg('tag')));
    	$this->elementEnd('div');
    }

    function showContent()
    {
    	
    	$this->showPageNotice();
    	
        $nl = new NoticeList($this->arg('notice'), $this);

        $cnt = $nl->show();

        $this->pagination($this->cur_page > 1, $cnt > NOTICES_PER_PAGE,
                          $this->cur_page, 'grouptag', array('id'=>$this->cur_group->id, 'tag' => $this->arg('tag')));
    }
    
    // if there is no notice, we show this
    function showEmptyList()
    {
        $message = sprintf('这是' . GROUP_NAME() . '  %s 关于标签 %s 的消息列表 , 但是还没人发消息.', 
        		           $this->cur_group->uname, $this->arg('tag')) . ' ';
        $emptymsg = array();
        $emptymsg['p'] = $message;
        $emptymsg['p'] = '快发送此' . GROUP_NAME() . '中第一条关于这个标签的消息吧！';
        $this->tu->showEmptyListBlock($emptymsg);
    }

}