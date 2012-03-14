<?php
//Create by xiangyun
//This daemon service should be called everyday to send system message and remove groups which out of date

define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

$groups = User_group::getAllAuditGroups();
while($groups->fetch()){
	$days = abs((strtotime(date("Y-m-d"))-strtotime(substr($groups->created, 0, 10)))/86400);
	if($days == 4){	
    	$groupinv = $groups->getInvites();
    	$invname = '';
    	$link = '';
    	while($groupinv->fetch()) {
    		$invname .= $groupinv->uname;
    		$invname .= ',';
    		$link .= '<a href="' . $groupinv->getProfile()->profileurl . '">' . $groupinv->uname . '</a>&nbsp;';
    	}
    	if($invname != ''){
    		$invname = substr($invname, 0, strlen($invname)-1);
    	}
    	$content = '您所创建的公会' . $groups->uname . '已超过4天，请您提醒共创者' . $invname. '3天内确认，如未确认时间超过7天，系统会自动清理未成功创建的公会，请您谅解。';
    	$render = '您所创建的公会' . $groups->uname . '已超过4天，请您提醒共创者' . $link. '3天内确认，如未确认时间超过7天，系统会自动清理未成功创建的公会，请您谅解。';

    	$result = System_message::saveNew(array($groups->ownerid), $content, $render, 1);
        if (!$result) {
            common_log_db_error($msg, 'INSERT', __FILE__);
            return false;
        }
	}
	if($days >= 7){
		$groups->destroy();
	}
}

?>