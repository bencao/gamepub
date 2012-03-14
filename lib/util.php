<?php

function common_munge_password($password, $id)
{
    return md5($password . $id);
}

function common_have_session()
{
    return (0 != strcmp(session_id(), ''));
}

function common_ensure_session()
{
    $c = null;
    if (array_key_exists(session_name(), $_COOKIE)) {
        $c = $_COOKIE[session_name()];
    }
    if (!common_have_session()) {
        if (common_config('sessions', 'handle')) {
            Session::setSaveHandler();
        }
        @session_start();
        if (!isset($_SESSION['started'])) {
            $_SESSION['started'] = time();
            if (!empty($c)) {
                common_log(LOG_WARNING, 'Session cookie "' . $_COOKIE[session_name()] . '" ' .
                           ' is set but started value is null');
            }
        }
    }
}

$_cur = false;

function common_set_user($user)
{

    global $_cur;

    if (is_null($user) && common_have_session()) {
        $_cur = null;
        unset($_SESSION['userid']);
        return true;
    } else if (is_string($user)) {
        $uname = $user;
        $user = User::staticGet('uname', $uname);
    } else if (!($user instanceof User)) {
        return false;
    }

    if ($user) {
        common_ensure_session();
        $_SESSION['userid'] = $user->id;
        $_SESSION['banned_count'] = 0;
    	$_SESSION['floatbar'] = 1;
        $_cur = $user;
        return $_cur;
    }
    return false;
}

//设置Cookie的路径是Linux的?
function common_set_cookie($key, $value, $expiration=0)
{
    $path = common_config('site', 'path');
    $server = common_config('site', 'server');

    if ($path && ($path != '/')) {
        $cookiepath = '/' . $path . '/';
    } else {
        $cookiepath = '/';
    }
    return setcookie($key,
                     $value,
                     $expiration,
                     $cookiepath,
                     $server);
}

function common_current_user()
{
    global $_cur;

    if (!_have_config()) {
        return null;
    }

    if ($_cur === false) {
    	//Session过期, 或者重启浏览器/或者有Session名字, 但是Session的内容没有, 比如userid不存在, 或者只重启了单个浏览器
        if (isset($_REQUEST[session_name()]) || (isset($_SESSION['userid']) && $_SESSION['userid'])) {
            common_ensure_session();
            $id = isset($_SESSION['userid']) ? $_SESSION['userid'] : false;
            //id=1?
            if ($id) {
                $_cur = User::staticGet($id);
                return $_cur;
            }
        }
        // that didn't work; try to remember; will init $_cur to null on failure
        //如果仅仅使用记住密码是可以恢复$_SESSION['banned]=0
        $_cur = Remember_me::getRememberedUser();
        if ($_cur) {
            // XXX: Is this necessary?
            $_SESSION['userid'] = $_cur->id;
        }
    }
    return $_cur;
}


function common_source_link($source_name)
{
	switch ($source_name) {
		case 'web':
			$link = '通过网站';
			break;
		case 'mail':
			$link = '通过邮件';
			break;
		case 'feed':
			$link = '通过<span class="source"><a href="' . common_path('settings/feed') . '">Feed</a></span>';
			break;
		default:
			$ns = Notice_source::staticGet('code', $source_name);
			if ($ns) {
				$link = '通过<span class="source"><a href="' . $ns->url . '" rel="external" target="_blank">' . $ns->name . '</a></span>';
			} else {
				$link = '通过' . $source_name;
			}
			break;
	}
	return $link;
}

function common_shorten_links($text)
{
	require_once INSTALLDIR . '/lib/renderhelper.php';
    return common_replace_urls_callback($text, 'common_shorten_url');
}

// Generate a rendered message with html including the group link in it
function common_group_linker($group)
{
    return '<a href="' . htmlspecialchars($group->permalink()) . '">' . $group->nickname . '</a>';
}

// Generate a rendered message with html including the user link in it
function common_user_linker($id)
{
    $profile = Profile::staticGet('id', $id);
    if ($profile) {
    	return '<a href="' . htmlspecialchars($profile->profileurl) . '">' . $profile->nickname . '</a>';
    } else {
        return $id;
    }
}

function common_local_url($action, $args=null, $params=null, $fragment=null)
{
    static $sensitive = array('login', 'register', 'passwordsettings', 'api', 'invitewithpass', 'invite');

    $r = Router::get();
    $path = $r->build($action, $args, $params, $fragment);
    
    $ssl = in_array($action, $sensitive);

    if (common_config('site','fancy')) {
        $url = common_path(mb_substr($path, 1), $ssl);
    } else {
        if (mb_strpos($path, '/index.php') === 0) {
            $url = common_path(mb_substr($path, 1), $ssl);
        } else {
            $url = common_path('index.php'.$path, $ssl);
        }
    }
    return $url;
}

function common_path($relative, $ssl=false)
{
    $pathpart = (common_config('site', 'path')) ? common_config('site', 'path')."/" : '';

    if (($ssl && (common_config('site', 'ssl') === 'sometimes'))
        || common_config('site', 'ssl') === 'always') {
        $proto = 'https';
        if (is_string(common_config('site', 'sslserver')) &&
            mb_strlen(common_config('site', 'sslserver')) > 0) {
            $serverpart = common_config('site', 'sslserver');
        } else {
            $serverpart = common_config('site', 'server');
        }
    } else {
        $proto = 'http';
        $serverpart = common_config('site', 'server');
    }
    
    //common_debug($serverpart . ' ' . $pathpart . ' '. $relative);

    return $proto.'://'.$serverpart.'/'.$pathpart.$relative;
}

function common_date_string($dt)
{
    // XXX: do some sexy date formatting
    // return date(DATE_RFC822, $dt);
    $t = strtotime($dt);
    $now = time();
    $diff = $now - $t;

    if ($now < $t) { // that shouldn't happen!
        return common_exact_date($dt);
    } else if ($diff < 10) {
        return '就在刚才'; 
    } else if ($diff < 60) {
        return sprintf('%d 秒钟前', $diff);
    } else if ($diff < 3600) {
        return sprintf('%d 分钟前', round($diff/60));
    } else if ($diff < 24 * 3600) {
        return sprintf('%d 小时前', round($diff/3600));
    } else {
    	$cur = strftime('%Y-%m-%d', $now);
	    $today = strtotime($cur);		
		$yesterday = $today - 3600*24;
		$twodays = $today - 3600*24*2;
    	
    	if ($t > $yesterday) {
	        return sprintf('昨天');
	    } else if ($t > $twodays) {
	        return sprintf('前天');
	    } else {
	        return common_exact_date($dt);
	    }
	}
}

function common_exact_date($dt)
{
    static $_utc;
    static $_siteTz;

    if (!$_utc) {
        $_utc = new DateTimeZone('PRC');
        $_siteTz = new DateTimeZone(common_config('site', 'timezone'));
    }

    $dateStr = date('d F Y H:i:s', strtotime($dt));
    //$dateStr = date('Y-m-d H:i', strtotime($dt));
    $d = new DateTime($dateStr, $_utc);
    $d->setTimezone($_siteTz);
    return $d->format('Y-m-d H:i'); //DATE_RFC850
}

function common_date_w3dtf($dt)
{
    $dateStr = date('d F Y H:i:s', strtotime($dt));
    $d = new DateTime($dateStr, new DateTimeZone('PRC'));
    $d->setTimezone(new DateTimeZone(common_config('site', 'timezone')));
    return $d->format(DATE_W3C);
}

function common_date_rfc2822($dt)
{
    $dateStr = date('d F Y H:i:s', strtotime($dt));
    $d = new DateTime($dateStr, new DateTimeZone('PRC'));
    $d->setTimezone(new DateTimeZone(common_config('site', 'timezone')));
    return $d->format('r');
}

function common_date_iso8601($dt)
{
    $dateStr = date('d F Y H:i:s', strtotime($dt));
    $d = new DateTime($dateStr, new DateTimeZone('PRC'));
    $d->setTimezone(new DateTimeZone(common_config('site', 'timezone')));
    return $d->format('Y-m-d H:i:s'); //c
}

function common_sql_now()
{
    return common_sql_date(time());
}

function common_sql_date($datetime)
{
    return strftime('%Y-%m-%d %H:%M:%S', $datetime);
}

function common_redirect($url, $code=307)
{
    static $status = array(301 => "Moved Permanently",
                           302 => "Found",
                           303 => "See Other",
                           307 => "Temporary Redirect");

    header('HTTP/1.1 '.$code.' '.$status[$code]);
    header("Location: $url");

    $xo = new XMLOutputter();
    $xo->startXML('a',
                  '-//W3C//DTD XHTML 1.0 Strict//EN',
                  'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd');
    $xo->element('a', array('href' => $url), $url);
    $xo->endXML();
    exit;
}

function common_profile_url($uname)
{
    return common_path($uname);
}

// returns $bytes bytes of random data as a hexadecimal string
// "good" here is a goal and not a guarantee

function common_good_rand($bytes)
{
    // XXX: use random.org...?
    if (@file_exists('/dev/urandom')) {
        return common_urandom($bytes);
    } else { // FIXME: this is probably not good enough
        return common_mtrand($bytes);
    }
}

function common_urandom($bytes)
{
    $h = fopen('/dev/urandom', 'rb');
    // should not block
    $src = fread($h, $bytes);
    fclose($h);
    $enc = '';
    for ($i = 0; $i < $bytes; $i++) {
        $enc .= sprintf("%02x", (ord($src[$i])));
    }
    return $enc;
}

function common_mtrand($bytes)
{
    $enc = '';
    for ($i = 0; $i < $bytes; $i++) {
        $enc .= sprintf("%02x", mt_rand(0, 255));
    }
    return $enc;
}

function common_set_returnto($url)
{
    common_ensure_session();
    $_SESSION['returnto'] = $url;
}

function common_get_returnto()
{
    common_ensure_session();
    return array_key_exists('returnto', $_SESSION) ? $_SESSION['returnto'] : null;
}

function common_timestamp()
{
    return date('YmdHis');
}

function common_ensure_syslog()
{
    static $initialized = false;
    if (!$initialized) {
        openlog(common_config('syslog', 'appname'), 0,
            common_config('syslog', 'facility'));
        $initialized = true;
    }
}

function common_log_line($priority, $msg)
{
    static $syslog_priorities = array('LOG_EMERG', 'LOG_ALERT', 'LOG_CRIT', 'LOG_ERR',
                                      'LOG_WARNING', 'LOG_NOTICE', 'LOG_INFO', 'LOG_DEBUG');
    return date('Y-m-d H:i:s') . ' ' . $syslog_priorities[$priority] . ': ' . $msg . "\n";
}

function common_log($priority, $msg, $filename=null)
{
	if (is_array($msg)) {
		$msg = print_r($msg, true);
	}
    $logfile = common_config('site', 'logfile');
    if ($logfile) {
        $log = fopen($logfile, "a");
        if (! $log) {
     	   $log = fopen(INSTALLDIR. '/' .$logfile, "a");
        }
        if ($log) {
        	//上线时可关闭LOG_INFO, LOG_DEBUG
        	if($priority < common_config('syslog', 'level')) {
	            $output = common_log_line($priority, $msg);
	            fwrite($log, $output);
	            fclose($log);
        	}
        }
    } else {
        common_ensure_syslog();
        syslog($priority, $msg);
    }
}

function common_debug($msg, $filename=null)
{
    if ($filename) {
        common_log(LOG_DEBUG, basename($filename).' - '.$msg);
    } else {
    	common_log(LOG_DEBUG, $msg);
    }
}

function common_info($msg, $filename=null)
{
	if ($filename) {
        common_log(LOG_INFO, basename($filename).' - '.$msg);
    } else {
    	common_log(LOG_INFO, $msg);
    }
}

function common_log_db_error(&$object, $verb, $filename=null)
{
    $objstr = common_log_objstring($object);
    $last_error = &PEAR::getStaticProperty('DB_DataObject','lastError');
    common_log(LOG_ERR, $last_error->message . '(' . $verb . ' on ' . $objstr . ')', $filename);
}

function common_log_objstring(&$object)
{
    if (is_null($object)) {
        return "null";
    }
    if (!($object instanceof DB_DataObject)) {
        return "(unknown)";
    }
    $arr = $object->toArray();
    $fields = array();
    foreach ($arr as $k => $v) {
        $fields[] = "$k='$v'";
    }
    $objstring = $object->tableName() . '[' . implode(',', $fields) . ']';
    return $objstring;
}

function GROUP_NAME()
{
	global $group_name;
	return $group_name;
}

function SET_GROUP_NAME($name)
{
	global $group_name;
	$group_name = $name;
}

function JOB_NAME()
{
	global $job_name;
	return $job_name;
}

function SET_JOB_NAME($name)
{
	global $job_name;
	$job_name = $name;
}

function common_copy_args($from)
{
    $to = array();
    $strip = get_magic_quotes_gpc();
    foreach ($from as $k => $v) {
    	if (is_array($v)) {
    		$to[$k] = common_copy_args($v);
    	} else {
        	$to[$k] = ($strip) ? stripslashes($v) : $v;
    	}
    }
    return $to;
}

// Neutralise the evil effects of magic_quotes_gpc in the current request.
// This is used before handing a request off to OAuthRequest::from_request.
function common_remove_magic_from_request()
{
    if(get_magic_quotes_gpc()) {
        $_POST=array_map('stripslashes',$_POST);
        $_GET=array_map('stripslashes',$_GET);
    }
}

function common_notice_uri(&$notice)
{
    return common_path('discussionlist/' . $notice->id);
}

// 36 alphanums - lookalikes (0, O, 1, I) = 32 chars = 5 bits

function common_confirmation_code($bits)
{
    // 36 alphanums - lookalikes (0, O, 1, I) = 32 chars = 5 bits
    static $codechars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $chars = ceil($bits/5);
    $code = '';
    for ($i = 0; $i < $chars; $i++) {
        // XXX: convert to string and back
        $num = hexdec(common_good_rand(1));
        // XXX: randomness is too precious to throw away almost
        // 40% of the bits we get!
        $code .= $codechars[$num%32];
    }
    return $code;
}

// convert markup to HTML

function common_markup_to_html($c)
{
    $c = preg_replace('/%%action.(\w+)%%/e', "common_local_url('\\1')", $c);
    $c = preg_replace('/%%doc.(\w+)%%/e', "common_local_url('doc', array('title'=>'\\1'))", $c);
    $c = preg_replace('/%%(\w+).(\w+)%%/e', 'common_config(\'\\1\', \'\\2\')', $c);
    require_once 'markdown.php';
    return Markdown($c);
}

function common_session_token()
{
    common_ensure_session();
    if (!array_key_exists('token', $_SESSION)) {
        $_SESSION['token'] = common_good_rand(64);
    }
    return $_SESSION['token'];
}

function common_tokenize($string)
{
	if (common_config('site', 'usetokenizer')) {
		
		require_once INSTALLDIR . '/lib/ShaiNoticeSearchEngine.php';
	    $engine = new ShaiNoticeSearchEngine();
	    $pieces = $engine->tokenize($string);
		if ($pieces) {
			return preg_split('/[ ]+/', $pieces);
		} else {
			return preg_split('/[ ]+/', $string);
		}
	} else {
		return preg_split('/[ ]+/', $string);
	}
}

function common_stream($key, $functionToCall, $funcArgs = array(), $expireSecond = 0) {
    if (! $funcArgs) {
    	$funcArgs = array();	
    }
    
    $cache = common_memcache();
	
	if (! empty($cache)) {
		$idkey = common_cache_key($key);
		$result = @$cache->get($idkey);

		if (empty($result)) {
			$result = call_user_func_array($functionToCall, $funcArgs);
			$cache->set($idkey, $result, 0, $expireSecond);
		}
	} else {
		$result = call_user_func_array($functionToCall, $funcArgs);
	}
	
    return $result;
}

function common_random_fetch($array, $fetchCount) {
	if (! is_array($array)) {
		throw new Exception('Error first parameter : should be array');
	}
	if (! is_int($fetchCount)) {
		throw new Exception('Error second parameter : should be integer');
	}
	$arraySize = count($array);
	if ($arraySize <= $fetchCount) {
		return $array;
	}
	if ($fetchCount > $arraySize/2) {
		return array_diff($array, common_random_fetch($array, $arraySize - $fetchCount));
	}
	$arrayKeys = array_keys($array);
	$fetched = array();
	$fetchedIndex = array();
	$fetchedCount = 0;
	while ($fetchedCount < $fetchCount) {
		do {
			$randomIndex = rand(0, $arraySize - 1);
		} while (in_array($randomIndex, $fetchedIndex));
		$fetchedIndex[] = $randomIndex;
		$fetched[] = $array[$arrayKeys[$randomIndex]];
		$fetchedCount ++;
	}
	return $fetched;
}

function common_cut_string($string, $length) {
   $strcut = '';
   $strLength = 0;
   if(strlen($string) > $length) {
       //将$length换算成实际UTF8格式编码下字符串的长度
       for($i = 0; $i < $length; $i++) {
           if ( $strLength >= $length )
               break;
           //当检测到一个中文字符时
           if( ord($string[$strLength]) > 127 )
               $strLength += 3;
           else
               $strLength += 1;
       }
       return substr($string, 0, $strLength);
   } else {
       return $string;
   }
}

?>