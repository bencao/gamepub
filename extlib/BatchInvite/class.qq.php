<?php
/** 
* @file class.qqhttp.php
* qq邮箱登陆获取类
* @author wc<cao8222@gmail.com>
* @date 2009-04-27
 */

class Httpqq {

    var $cookie = '';

    function __construt() {
    }

    function makeForm($username, $password) {
        $form = array(
            'url' => "https://mail.qq.com/cgi-bin/loginpage",
        	'header' => true
        );
        $data = $this->curlFunc($form);
        preg_match('/name="sid"\svalue="([^"]*)"/',$data['html'], $sida);
        $sid = $sida[1];
        preg_match('/name="ts"\svalue="(\d+)"/',$data['html'], $tspre);
        $ts = $tspre[1];
        preg_match('/ssl_edition=([^\.]+)\.mail\.qq\.com/',$data['html'], $server);
        $server_no = $server[1];

        /*  login.html 载入 */
        $html = file_get_contents(dirname(__FILE__).'/qqtemplate.html');
        $html = str_replace('{_sid_}', $sid, $html);
        $html = str_replace('{_uin_}',$username, $html);
        $html = str_replace('{_pp_}',$password, $html);
        $html = str_replace('{_ts_}',$ts, $html);
        $html = str_replace('{_server_no_}',$server_no, $html);
        $html = str_replace('{_token_}', common_session_token(), $html);
        return $html;
    }

    function curlFunc($array)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $array['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if( isset($array['header']) && $array['header'] ) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        if(isset($array['httpheader'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $array['httpheader']);
        }
        if(isset($array['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $array['referer']);
        }
        if( isset($array['post']) ) {
            curl_setopt($ch, CURLOPT_POST, 1 );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $array['post']);
        }
        if( isset($array['cookie']) ){
            curl_setopt($ch, CURLOPT_COOKIE, $array['cookie']);
        }
        $r['erro'] = curl_error($ch);
        $r['errno'] = curl_errno($ch);
        $r['html'] = curl_exec($ch);
        $r['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $r;
    }

    /** 
     * 获取验证码图片和cookie
     * @param Null
     * 
     * @return array('img'=>String, 'cookie'=>String)
     */
    function getVFCode () 
    {
        $vfcode = array(
            'header' => true,
            'cookie' => 'ssl_edition=' . $_GET['server_no'] . '.mail.qq.com;edition=' . $_GET['server_no'] . '.mail.qq.com',
            'url' => 'https://mail.qq.com/cgi-bin/getverifyimage?aid='.$_GET['aid'].'&f=html&ck=1&'.@$_GET['t'],
        	'referer' => 'https://mail.qq.com/cgi-bin/loginpage',
        	'httpheader' => array(
    			"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.2 (KHTML, like Gecko) Chrome/6.0.453.1 Safari/534.2",
    			"Accept-Charset: utf-8;q=0.7,*;q=0.3",
        		"Accept-Language: zh-CN,zh;q=0.8,en;q=0.6",
        		"Accept: application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
        		"Connection: keep-alive",
        		"Host: mail.qq.com"
        	)
        );
        
        $r = $this->curlFunc($vfcode);
        if ($r['http_code'] != 200 ) return false;
        $data = preg_split('/\n/', $r['html']);
        $img = $data[count($data) - 1];
        $header = '';
        for ($i = 0; $i < count($data) - 1; $i ++) {
        	$header .= $data[$i];
        }
        preg_match('/authimgs_id=([^;]+);/', $header, $temp);
        $authid = trim($temp[1]);
        preg_match('/verifyimagesession=([^;]+);/',$header, $temp);
        $visession = trim($temp[1]);
        
        return  array('img' => $img,'cookie' => 'authimgs_id=' . $authid . ';verifyimagesession=' . $visession . ';edition=' . $_GET['server_no'] . ".mail.qq.com;ssl_edition=" . $_GET['server_no'] . ".mail.qq.com;");
    }

    /** 
     * 登陆qq邮箱
     * 
     * @param $cookie getvfcode中生成的cookie
     * 
     * @return array(
     *   sid=>String , //用户认证的唯一标示
     *   login => Boolean, //true 登陆成功 ，false 登陆失败
     *   server_no => String // 服务器编号
     *   active => Boolean //true 已开通 ，false 未开通 邮箱
     *   cookie => String // 获取数据cookie
     *
     * );
     */
    function login($cookie) 
    {
    	common_debug('start login qq ..');
        /* 生成参数字符串 */
        $post = array();
        foreach($_POST as $k => $v) {
        	if ($k == 'encryp') {
        		$k = 'p';
        	} else if ($k == 'username') {
        		$k = 'uin';
        	} else if ($k == 'password') {
        		$k = 'pp';
        	} else if ($k == 'server_no' || $k == 'source') {
        		continue;
        	}
            $post[] = $k.'='.urlencode($v);
        }
        $poststr = implode('&',$post);
        
        $r['server_no'] = $_POST['server_no'];

        $login = array(
            'url'=>'https://mail.qq.com/cgi-bin/login',
            'header' => true,
            'cookie' => $cookie,
            'referer' => 'https://mail.qq.com/cgi-bin/loginpage',
            'httpheader'=>array(
                "Host: mail.qq.com",
                "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.366.2 Safari/533.4",
                "Content-Type: application/x-www-form-urlencoded",
        		"Origin: https://mail.qq.com"
            ),
            'post' => $poststr
        );
        $data = $this->curlFunc($login);
        $data['html'] = iconv("gb2312", "UTF-8", $data['html']);
        common_debug($data['html']);
        if ($data['http_code'] != 200) {
            $this->error($data);
            return false;
        }
        
        /* 测试数据 */
        //$data['html'] =file_get_contents('./r.txt');
        $r['uin'] = $_POST['username'];
        
        $r['msg'] = '未知错误';
        /* 登陆错误的判断 */
        if (preg_match('|errtype=(\d)|', $data['html'], $temp_err)) {
            $r['login'] = false;
            if ($temp_err[1] == 1) {
                $r['msg'] = '账号和密码错误';
            } else if ($temp_err[1] == 2) {
                $r['msg'] = '验证码错误';
            }
            return $r;
        }
        /* 登陆成功 */
        preg_match('|urlHead="([^"]+)"|i',$data['html'],$temp_url);
        common_debug($data['html']);
        common_debug($temp_url);
        $urlhead = $temp_url[1];
        if (preg_match('|frame_html\?sid=([^"]+)"|i',$data['html'],$temp_sid) ) {
            $r['sid'] = $temp_sid[1];
            $r['active'] = true;
        } elseif (preg_match('|autoactivation\?sid=([^&]+)?&|i',$data['html'],$temp_sid) ) {
            $r['sid'] = $temp_sid[1];
            $r['active'] = false;
        }
        /* 登录后cookie的获取 ，在后续操作中用到 */
        if (preg_match_all('|Set-Cookie:([^=]+=[^;]+)|i', $data['html'], $new_cookies) ) {
            $cookiestr = implode('; ', $new_cookies[1]);
            $cookiestr .= ';'.$cookie;
        }
        
        common_debug('get cookie = ' . $cookiestr);

        $r['login'] = true;
        $r['cookie'] = $cookiestr;
        return $r;
    }

    function openEmail($param) 
    {
        $openEmail = array(
            'url'=>'https://mail.qq.com/cgi-bin/autoactivation?actmode=6&sid='.$param['sid'],
            'header' => true,
            'cookie' => $param['cookie'],
            'referer' => 'https://mail.qq.com/cgi-bin/autoactivation?sid='.$param['sid'].'&action=reg_activate&actmode=6', 
            'httpheader'=>array(
                "Host: " . $param['server_no'] . '.mail.qq.com',
                'Accept-Charset: gb2312,utf-8;q=0.7,*;q=0.7',
                "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.366.2 Safari/533.4",
            ),
        );

        $data =  $this->curlFunc($openEmail);
        if (preg_match('|Set-Cookie:qqmail_activated=0|i', $data['html'])) {
            $param['active'] = true;
            $param['cookie'] = $param['cookie'] .'; qqmail_activated=0; qqmail_alias=';
        }
        return $param;
    }

    /** 
     * 
     * 获取friends数据 
     * 
     * @param $param = array(
     *   sid=>String , //用户认证的唯一标示
     *   login => Boolean, //true 登陆成功 ，false 登陆失败
     *   server_no => String // 服务器编号
     *   active => Boolean //true 已开通 ，false 未开通 邮箱
     *   cookie => String // 获取数据cookie
     *
     * );
     * @return Array(
     *   key=>value, // key:qq号，value: nickname
     * );
     */
    function getFriends($param)
    {
        $friend = array(
            'url'=>'https://mail.qq.com/cgi-bin/addr_listall?type=user&&category=all&sid='.$param['sid'],
            'header' => true,
            'cookie' => $param['cookie'],
            'referer' => 'https://mail.qq.com/cgi-bin/addr_listall?sid='.$param['sid'].'&sorttype=null&category=common',
            'httpheader'=>array(
                "Host: mail.qq.com",
                'Accept-Charset:utf-8;q=0.7,*;q=0.7',
                "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.366.2 Safari/533.4",
            ),
        );
        $r = $this->curlFunc($friend);
        if ($r['http_code'] != 200) {
            $this->error($r);
            return false;
        }
        $data =  $r['html'];
        $preg = preg_match_all('/\<p class="L_n"\><span t="1" u="([^"]+)" n="([^"]+)" e="([^"]+)">&nbsp;[^\<]*\<\/span\>\<\/p\>/i', $data, $temp_list);
        if ($preg == 0) return array();
        $list = array();
        for ($i = 0, $cnt = count($temp_list[1]); $i < $cnt; $i ++) {
			$inviteename = mb_convert_encoding($temp_list[2][$i], "UTF-8", "GBK");
			if ($inviteename == '我自己的邮箱'
				|| $inviteename == '发表到我的Qzone') {
				continue;
			}
        	$list[] = array($inviteename, $temp_list[3][$i]);
        }
        return $list;
    }

    /** 
     * 错误显示
     * 
     * @param $str array
     * 
     * @return 
     */
    function error($str) {
        $str['html'] = str_replace('script','', $str['html']);
        var_dump($str);
        exit;
    }
    
    function getAddressList($username, $password) {
     	$q = new Httpqq();
		// retry for 3 times
     	$times = 1;
		while ($times -- != 0) {
			$r = $q->login($_SESSION['qq_vf_session']);
			
			if ($r['login'] != false) {
		    	common_debug('login ok');
				$active = $r['active'];
		    	if ($active == false) {
		    		common_debug('openmail');
		       		$r = $q->openEmail($r);
		    	}
		    	return $q->getFriends($r);
			}
			sleep(3);
		}
		
		return $r['msg'];
    }
}

?>
