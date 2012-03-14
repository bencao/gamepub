<?php
/** 
* @file class.sinaHttp.php
* 获得sina邮箱通讯录列表
* @author jvones<jvones@gmail.com>
* @date 2009-09-26
**/
define("COOKIEJAR", tempnam("/var/tmp", "s1_"));
define("TIMEOUT", '5000');
define("USERAGENT", 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.366.2 Safari/533.4');

class Httpsina
{

	public $host = "";

	function checklogin( $user, $password )
	{
		if ( empty( $user ) || empty( $password ) )
		{
			return 0;
		}
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_REFERER, "http://mail.sina.com.cn/index.html" );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_USERAGENT, USERAGENT );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		curl_setopt( $ch, CURLOPT_URL, "http://mail.sina.com.cn/cgi-bin/login.cgi" );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, "&logintype=uid&u=".urlencode( $user )."&psw=".$password );
		$contents = curl_exec( $ch );
		curl_close( $ch );
		if ( !preg_match( "/Location: (.*)\\/cgi\\/index\\.php\\?check_time=(.*)\n/", $contents, $matches ) )
		{
			return 0;
		}
		$this->host = $matches[1];
		return 1;
	}

	public function getAddressList( $user, $password)
	{
		if ( !$this->checklogin( $user, $password ) )
		{
			return 0;
		}
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_USERAGENT, USERAGENT );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		curl_setopt( $ch, CURLOPT_URL, "http://mail.sina.com.cn/cgi-bin/login.cgi" );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, "&logintype=uid&u=".urlencode( $user )."&psw=".$password );
		curl_exec( $ch );
		curl_close( $ch );
		$cookies = array( );
		$bRet = $this->_readcookies( COOKIEJAR, $cookies );
		if ( !$bRet && !$cookies['SWEBAPPSESSID'] )
		{
			return 0;
		}
		$ch = curl_init( );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, COOKIEJAR );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
		curl_setopt( $ch, CURLOPT_URL, $this->host."/classic/addr_member.php" );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, "&act=list&sort_item=letter&sort_type=desc" );
		$content = curl_exec( $ch );
		curl_close( $ch );
		$res = $this->_parsedata( $content);
//		if ( !$res )
//		{
//			return 0;//没有联系人
//		}
		return $res;
	}

	public function _parsedata( $content)
	{
		$ar = array( );
		if ( !$content )
		{
			return 0;
		}
		$data = json_decode( $content );
		unset( $content );
		foreach ( $data->data->contact as $value )
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