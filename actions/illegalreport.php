<?php
/**
 * ShaiShai
 * Collect report for illegal notices
 *
 * @author  Andray
 * @category  Feedback
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

require_once INSTALLDIR . '/lib/common.php';

/**
 * Collect report for illegal notices
 *
 * This is the form for entering/saving illegal report
 *
 * @category Feedback
 */

class IllegalreportAction extends ShaiAction
{

    /**
     * Handle the request
     *
     * On GET, show the form. On POST, try to save the message.
     *
     * @param array $args unused
     *
     * @return void
     */

    function handle($args)
    {
        parent::handle($args);
        $this->trySave();
    }

    function trySave()
    {
        $reason      = $this->trimmed('reason');
        $description = $this->trimmed('description');
        $illtype     = $this->trimmed('illtype');
        $targetid    = $this->trimmed('targetid');
        $from_url = $this->trimmed('from_url');

        // report reason 0 - 请选择, 1 - 内容反动, 2 - 内容色情, 
        //               3 - 骚扰诈骗, 4 - 张贴广告', 5 - 滥发垃圾信息
        if ($reason == 0) {
        	$this->clientError('请选择举报原因。');
        	return;
        }
        if (mb_strlen($description, 'utf-8')>255) {
        	$this->clientError('您的附加描述说明太长了，请尝试缩短它，然后再提交。');
        	return;
        } else if (mb_strlen($description, 'utf-8')<1) {
        	$this->clientError('请添加对这个非法消息的描述');
        	return;
        }

        $cur = common_current_user();

        $report = new Illegal_report();
        $report->reporter    = $cur->id;
        $report->illtype     = $illtype;
        $report->targetid    = $targetid;
        $report->reason      = $reason;
        $report->description = $description;
        $report->from_url = $from_url;

        $result = $report->insert();

        if (!$result) {
            common_log_db_error($report, 'INSERT', __FILE__);
            $this->serverError('提交非法报告失败');
        }
        
        if ($this->boolean('ajax')) {
            $this->showJsonResult(array('result'=>'successful'));
        } else if ($illtype == 0){
            common_redirect(common_path('discussionlist/' . $targetid), 303);
        } else {
        	common_redirect(common_path(User::staticGet('id', $targetid)->uname), 303);
        }
        
    }
}
