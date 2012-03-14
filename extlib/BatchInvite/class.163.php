<?php
/** 
* @file class.163http.php
* 163邮箱登陆获取类
* @author jvones<jvones@gmail.com>
* @date 2009-09-26
**/

define("COOKIEJAR1", tempnam("/var/tmp", "c1_"));
define("COOKIEJAR2", tempnam("/var/tmp", "c2_"));
define("COOKIEJAR3", tempnam("/var/tmp", "c3_"));
define("TIMEOUT", '5000');

class Http163
{
  /**
    * @desc: login in the 163 mail box
    * @param string $username
    * @param string $password
    * @return int  //the login status
    */
    public function login($username, $password)
    {
        $ch = curl_init();
       
        curl_setopt($ch, CURLOPT_URL, "http://reg.163.com/logins.jsp");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=".$username."&password=".$password."&type=1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //文件流形式返回
        curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR1);
        curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
       
        ob_start();
        curl_exec($ch);
        $contents = ob_get_contents();
        ob_end_clean();
        curl_close($ch);

        if (strpos($contents, "安全退出") !== false)
        {
            return 0;
        }
       
        return 1;
    }

    /**
    * @desc: get address list from mail box
    * @param string $username
    * @param string $password
    * @return array  //the address list
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
       
        //get the address list page information
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$header['host']."/a/s?sid=".$header['sid']."&func=global:sequential");
        curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: zh-cn','Connection: Keep-Alive','Content-Type: application/xml; charset=UTF-8'));

        $str = "<?xml version=\"1.0\"?><object><array name=\"items\"><object><string name=\"func\">pab:searchContacts</string>" .
               "<object name=\"var\"><array name=\"order\"><object><string name=\"field\">FN</string><boolean name=\"ignoreCase\">true</boolean></object>" .
              "</array></object></object><object><string name=\"func\">user:getSignatures</string></object>" .
               "<object><string name=\"func\">pab:getAllGroups</string></object></array></object>";

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
        curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
        ob_start();
        curl_exec($ch);
        $contents = ob_get_contents();
		//file_put_contents('./result.txt', $contents);
        ob_end_clean();
        curl_close($ch);
       
        //get mail list from the page information only emailaddress
        /*
        $pattern = "/([\\w_-])+@([\\w])+([\\w.]+)/";
        if (preg_match_all($pattern, $contents, $tmpres, PREG_PATTERN_ORDER))
        {
            $users = array_unique($tmpres[0]);
        }
    	*/
        
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
    * get cookie
    */
    public function _getheader($username)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://entry.mail.163.com/coremail/fcg/ntesdoor2?lightweight=1&verifycookie=1&language=-1&style=-1&username=".$username);
		curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR1);  //当前使用的cookie
		curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR2);   //服务器返回的新cookie
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