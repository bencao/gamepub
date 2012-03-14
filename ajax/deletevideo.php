<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class DeletevideoAction extends ShaiAction
{	
	function handle($args)  {
        parent::handle($args);

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
          common_redirect(common_path('uploadvideo'));
          return;
        } else {
			$vid = $this->trimmed('vid');
			
			$status = $this->deleteVideo($vid);
			if($status == 1) {
				;
			} else {
				;
			}
        }
        
        
    }
    
    function deleteVideo($vid) {
    	$skey = 'aff0a6a4521232970b2c1cf539ad0a19';
    	$pass = 'dcce1ea895b0e0d0d782c93f4cafc8d5';
    	
    	$str =  file_get_contents('http://v.ku6vms.com/phpvms/api/delVideo/skey/' . $skey . '/v/1/' . 
    		'vid/' . $vid . '/format/json/md5/'. 
			strtoupper(md5($skey . '1' . $vid . $pass)));

		$obj = json_decode($str, true);
		return $obj['status'];
    }
}