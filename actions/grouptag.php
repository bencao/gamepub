<?php
/*
 * ShaiShai - the distributed microblogging tool
 * Copyright (C) 2008, 2009, ShaiShai, Inc.
 */

if (!defined('SHAISHAI')) { exit(1); }

require_once INSTALLDIR.'/lib/noticelist.php';

class GrouptagAction extends GroupDesignAction
{

    var $notice;
    var $tag;
    var $page;

    function prepare($args)
    {
    	if (! parent::prepare($args)) {return false;}

//        if (!common_config('inboxes','enabled')) {
//            $this->serverError('使' . GROUP_NAME() . '工作，收件箱必须是可用的。');
//            return false;
//        }

        $id = $this->arg('id');

        if (!$id) {
            $this->clientError('此ID不存在。');
            return false;
        }

        $this->cur_group = User_group::staticGet('id', $id);

        if (!$this->cur_group) {
            $this->clientError('此' . GROUP_NAME() . '不存在', 404);
            return false;
        }
        
        $this->tag = $this->trimmed('tag');
        
        if (!$this->tag) {
            $this->clientError('缺少tag参数', 404);
            return false;
        }

        common_set_returnto($this->selfUrl());
    

        $this->notice = Notice_tag::getGroupStream($this->tag, $this->cur_group->id, (($this->cur_page-1)*NOTICES_PER_PAGE), NOTICES_PER_PAGE + 1);

        if($this->cur_page > 1 && $this->notice->_count == 0){
            $this->serverError('此页不存在',$code=404);
        }

        return true;
    }

    function handle($args)
    {
        parent::handle($args);

        $tag_object = Second_tag::staticGet('id', $this->tag);
        if(!$tag_object) {
        	$this->clientError('您查看的标签不存在.');
        	return;
        }
        $tag_name = $tag_object->name;

        $this->addPassVariable('group', $this->cur_group);
//        $this->addPassVariable('thispage', $this->page);
        $this->addPassVariable('tag', $tag_name);
        $this->addPassVariable('notice', $this->notice);
        $this->displayWith('GrouptagHTMLTemplate');
    }

    function isReadOnly($args)
    {
        return true;
    }
}
