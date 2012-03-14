<?php
/** 
* @file class.tomHttp.php
* 获得tom邮箱通讯录列表
* @author jvones<jvones@gmail.com>
* @date 2009-09-26
**/
define("COOKIEJAR", tempnam("/var/tmp", "t1_"));
define("TIMEOUT", '5000');
define("USERAGENT", 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.366.2 Safari/533.4');


class Httptom
{

	public function checklogin($user, $password)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://login.mail.tom.com/cgi/login");
		curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR);
		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
		$fileds = "user=".$user."&pass={$password}";
		$fileds .= "&style=0&verifycookie";
		$fileds .= "&type=0&url=http://bjweb.mail.tom.com/cgi/login2";
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fileds);
		ob_start();
		curl_exec($ch);
		$result = ob_get_contents();
		ob_end_clean();
		curl_close($ch);
		/*if (preg_match("/warning|", $result))
		{
			return 0;
		}*/
		return 1;
	}

	public function getAddressList($user, $password)
	{
		if (!$this->checklogin($user, $password))
		{
			return 0;
		}
		$this->_readcookies(COOKIEJAR, $res);
		if ($res['Coremail'] == "")
		{
			return 0;
		}
		$sid = substr(trim($res['Coremail']), -16);
		
		$url = "http://bjapp2.mail.tom.com/cgi/ldvcapp?funcid=xportadd&sid=" . $sid . "&ifirstv=&showlist=all";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
		curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		
		preg_match('<input type="hidden" name="postid" value="(?<id>.*)" >', $result, $matches);
		$postid = $matches['id'];
		
		$url = "http://bjapp2.mail.tom.com/cgi/ldvcapp";
//		$url .= "?funcid=address&sid=".$sid."&showlist=all&listnum=0";

		$fields = "funcid=xportadd&postid=". $postid ."&sid=". $sid . "&ifirstv=&group=&outport.x=1&outformat=8";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
		curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		
		$res = curl_exec($ch);
		curl_close($ch);
		//file_put_contents('./res.txt',$res);
		$email_pattern = "/([\\w_-])+@([\\w])+([\\w.]+)/";
		
		$result = array();
		
		$pattern = '/\n"[^"]*","[^"]*","[^"]*","(?<email>[^"]*)","[^"]*","[^"]*",";(?<name>[^"]*)","[^"]*","[^"]*","[^"]*","[^"]*","[^"]*","[^"]*","[^"]*","[^"]*"/';
		
		preg_match_all($pattern, $res, $matches1);
		
		for ($i = 0; $i < count($matches1['email']); $i ++)
		{
			$u_name = $matches1['name'][$i] == '' ? $matches1['email'][$i] : $matches1['name'][$i];
			$result[] = array(mb_convert_encoding($u_name, "UTF-8", "GB2312"), $matches1['email'][$i]);
		}
		
		return $result;
	}
	
	public function _readcookies( $file, &$result )
	{
		$fp = fopen( $file, "r" );
		while ( !feof( $fp ) )
		{
			$buffer = fgets( $fp, 4096 );
			$tmp = preg_split('/\t/', $buffer );
			if (array_key_exists(5, $tmp)
				&& array_key_exists(6, $tmp)) {
				$result[trim( $tmp[5] )] = trim( $tmp[6] );
			}
		}
		return 1;
	}
}

?>