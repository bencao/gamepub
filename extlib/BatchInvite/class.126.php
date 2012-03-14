<?php
/** 
* @file class.126http.php
* 获得126邮箱通讯录列表
* @author jvones<jvones@gmail.com> http://www.jvones.com/blog
* @date 2009-09-26
**/

define("COOKIEJAR", tempnam("/var/tmp", "c126_"));
define("TIMEOUT", '5000');

class Http126
{

	private function login($username, $password)
	{		
		//第一步：初步登陆
		$cookies = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		curl_setopt($ch, CURLOPT_URL, "https://reg.163.com/logins.jsp?type=1&product=mail126&url=http://entry.mail.126.com/cgi/ntesdoor?hid%3D10010102%26lightweight%3D1%26verifycookie%3D1%26language%3D0%26style%3D-1");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "username=".$username."@126.com&password=".$password);
		
		curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR);
		curl_setopt($ch,CURLOPT_HEADER,1);		
		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$str = curl_exec($ch);	
//		file_put_contents('./126result.txt', $str);		
		curl_close($ch);
			
		//获取redirect_url跳转地址，可以从126result.txt中查看，通过正则在$str返回流中匹配该地址
		preg_match("/replace\(\"(.*?)\"\)\;/", $str, $mtitle);
		$_url1 = $mtitle[1];
		
		//file_put_contents('./126resulturl.txt', $redirect_url);	
		//第二步：再次跳转到到上面$_url1
		$ch = curl_init($_url1);		
		
		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_COOKIEFILE,COOKIEJAR);
		curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR);		
		curl_setopt($ch,CURLOPT_HEADER,1);	
		$str2 = curl_exec($ch);
		curl_close($ch);
						
		if (strpos($str2, "安全退出") !== false)
		{			
			return 0;
		}		
		return 1;
	}
	
	/**
	 * 获取邮箱通讯录-地址
	 * @param $user
	 * @param $password
	 * @param $result
	 * @return array
	 */
	public function getAddressList($username, $password)
	{		
		if (!$this->login($username, $password))
		{
			return 0;
		}
	    
		$header = $this->_getheader($username);
		if (!$header['sid'])
        {
            return 0;
        }
        
        //测试找出sid(很重要)和host
        //file_put_contents('./host.txt', $header['host']);
        //file_put_contents('./sid.txt', $header['sid']);
        
		//开始进入模拟抓取
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://".$header['host']."/a/s?sid=".$header['sid']."&func=global:sequential");
		curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml"));
		$str = "<?xml version=\"1.0\"?><object><array name=\"items\"><object><string name=\"func\">pab:searchContacts</string><object name=\"var\"><array name=\"order\"><object><string name=\"field\">FN</string><boolean name=\"ignoreCase\">true</boolean></object></array></object></object><object><string name=\"func\">user:getSignatures</string></object><object><string name=\"func\">pab:getAllGroups</string></object></array></object>";
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
		ob_start();
		curl_exec($ch);
		$contents = ob_get_contents();

		ob_end_clean();
		curl_close($ch);
		
        //get mail list from the page information username && emailaddress
        preg_match_all("/<string\s*name=\"EMAIL;PREF\">(.*)<\/string>/Umsi",$contents,$mails);
        preg_match_all("/<string\s*name=\"FN\">(.*)<\/string>/Umsi",$contents,$names);
        $users = array();
        foreach($names[1] as $k=>$user)
        {
            //$user = iconv($user,'utf-8','gb2312');
//            $users[$mails[1][$k]] = $user;
            $users[] = array($user, $mails[1][$k]);
        }
//        if (!$users)
//        {
//            return '您的邮箱中尚未有联系人';
//        }      
        
        return $users;
	}
	
	/**
    * Get Header info
    */
    private function _getheader($username)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://entry.mail.126.com/cgi/ntesdoor?hid=10010102&lightweight=1&verifycookie=1&language=0&style=-1&username=".$username."@126.com");
		curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR);  //当前使用的cookie
		curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR);   //服务器返回的新cookie
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		$content=curl_exec($ch);
		
		preg_match_all('/Location:\s*(.*?)\r\n/i',$content,$regs);
        $refer = $regs[1][0];
        preg_match_all('/http\:\/\/(.*?)\//i',$refer,$regs);		
        $host = $regs[1][0];
        preg_match_all("/sid=(.*)/i",$refer,$regs);
        $sid = $regs[1][0];
		
		curl_close($ch);
		return array('sid'=>$sid,'refer'=>$refer,'host'=>$host);
    }
}

?>