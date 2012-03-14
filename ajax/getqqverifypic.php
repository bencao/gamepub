<?php
if (!defined('SHAISHAI')) { exit(1); }

require_once 'BatchInvite/class.qq.php';

class GetqqverifypicAction extends ShaiAction
{
    function handle($args)
    {
    	parent::handle($args);
    	
    	$q = new Httpqq();
    	
    	$r = $q->getVFCode();
    	header("Cache-Control: no-cache");
    	header("Pragma: no-cache");
    	header('Content-type: image/png');
    	$_SESSION['qq_vf_session'] = $r['cookie'];
    	
    	echo $r['img'];
        
    }
}
?>