<?php
/** 
* @file class.sohuHttp.php
* 获得sohu邮箱通讯录列表
* @author jvones<jvones@gmail.com> http://www.jvones.com/blog
* @date 2009-10-10
**/
define("COOKIEJAR1", tempnam("/var/tmp", "s1_"));
define("COOKIEJAR2", tempnam("/var/tmp", "s2_"));
define("COOKIEJAR3", tempnam("/var/tmp", "s3_"));
define("TIMEOUT", 2000);

class Httpsohu
{

	private function login($username, $password)
	{		
		//第一步：初步登陆
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		curl_setopt($ch, CURLOPT_URL, "https://passport.sohu.com/sso/login.jsp?userid=".$username."@sohu.com&password=".md5($password)."&appid=1000&persistentcookie=0&s=".time()."&b=2&w=1440&pwdtype=1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR1);
		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
		$contents = curl_exec( $ch );
		
		//file_put_contents('./sohuresult.txt', $contents);	
		curl_close( $ch );
		
		if ( strpos( $contents, "success" ) === false )
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

//		$cookies = array( );
//		$bRet = $this->readcookies( COOKIEJAR1, $cookies );
//		file_put_contents('./JSESSIONID.txt', $cookies['JSESSIONID']);	
//		if ( !$bRet && !$cookies['JSESSIONID'] )
//		{
//			return 100;
//		}

		//第二步：再次跳转到到上面$_url1
//		$ch = curl_init("http://login.mail.sohu.com/servlet/LoginServlet?appid=1000");		
//		
//		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($ch,CURLOPT_COOKIEFILE,COOKIEJAR1);
//		curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEJAR2);		
//		curl_setopt($ch,CURLOPT_HEADER,1);	
//		$str2 = curl_exec($ch);
//		file_put_contents('./sohuresult2222.txt', $str2);
//		curl_close($ch);		
		
//		$ch = curl_init();
//		curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR1);
//		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
//		curl_setopt($ch, CURLOPT_URL, "http://mail.sohu.com/bapp/95/main");
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//		$contents = curl_exec( $ch );
		
//		file_put_contents('./sohuresult_end.txt', $contents);	
//		curl_close( $ch );
//		$bRet = $this->_parsedata( $contents, $result );
//		if ( !$bRet )
//		{
//			return -1;
//		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEJAR1);
		curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT);
		curl_setopt($ch, CURLOPT_URL, "http://mail.sohu.com/address/export");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$contents = curl_exec( $ch );
		
		/*
		 * 名字,电子邮件地址,移动电话,个人网页,商务传真,商务电话,公司所在街道,公司所在地的邮政编码,住宅电话,家庭所在街道,家庭所在地的邮政编码,寻呼机,附注
			曹振华,benb88@gmail.com,,,,,,,,,,,
			刘冉,hbyy4@gmail.com,,,,,,,,,,,
		 */
		preg_match_all('/\n(?<name>[^,]*),(?<email>([\\w_-])+@([\\w])+([\\w.]+)),.*/', $contents, $matches);
		
		$resultArr = array();
		
		for ($i = 0; $i < count($matches['email']); $i ++) {
			$u_name = $matches['name'][$i] == '' ? $matches['email'][$i] : $matches['name'][$i];
			$resultArr[] = array(mb_convert_encoding($u_name, "UTF-8", "GB2312"), $matches['email'][$i]);
		}
		
		return $resultArr;
	}
	
	function _parsedata( $content, &$ar )
	{
		$ar = array( );
		if ( !$content )
		{
			return $ar;
		}
		$data = json_decode( $content );
		unset( $content );
		foreach ( $data->listString as $value )
		{
			if ( preg_match_all( "/[a-z0-9_\\.\\-]+@[a-z0-9\\-]+\\.[a-z]{2,6}/i", $value->email, $matches ) )
			{
				$emails = array_unique( $matches[0] );
				unset( $matches );
				foreach ( $emails as $email )
				{
//					$ar[$email] = $value->name;
					$ar[] = array($value->name, $email);
				}
			}
		}
		return $ar;
	}	
	
	function readcookies( $file, &$result )
	{
		$fp = fopen( $file, "r" );
		while ( !feof( $fp ) )
		{
			$buffer = fgets( $fp, 4096 );
			$tmp = preg_split('/\t/', $buffer );
			$result[trim( $tmp[5] )] = trim( $tmp[6] );
		}
		return 1;
	}
}

?>