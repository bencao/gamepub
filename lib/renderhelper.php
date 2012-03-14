<?php

function common_replace_urls_callback($text, $callback, $notice_id = null) {
    // Start off with a regex
    //(?:pattern) 匹配 pattern 但不获取匹配结果，也就是说这是一个非获取匹配，不进行存储供以后使用
    //'industr(?:y|ies) 就是一个比 'industry|industries'
    //##: 修正符, ix: 忽略大小写, 去掉空白字符
    $regex = '#'.
    '(?:'.
        '(?:'.
            '(?:https?|ftps?|mms|rtsp|gopher|news|nntp|telnet|wais|file|prospero|webcal|xmpp|irc)://'.
            '|'.
            '(?:mailto|aim|tel):'.
        ')'.
        '[^.\s\x{4e00}-\x{9fa5}，。？；“”：！《》￥*（）—‘’、~…¦【】]+\.[^\s\x{4e00}-\x{9fa5}，。？；“”：！《》￥*（）—‘’、~…¦【】]+'.
        '|'.
        '(?:[^.\s/:\x{4e00}-\x{9fa5}，。？；“”：！《》￥*（）—‘’、~…¦【】]+\.)+'.
        '(?:museum|travel|[a-z]{2,4})'.
        '(?:[:/][^\s\x{4e00}-\x{9fa5}，。？；“”：！《》￥*（）—‘’、~…¦【】]*)?'.
    ')'.
    '#ixu';
    preg_match_all($regex, $text, $matches);

    // Then clean up what the regex left behind
    $offset = 0;
    //对结果排序使 $matches[0] 为全部模式匹配的数组，$matches[1] 为第一个括号中的子模式所匹配的字符串组成的数组，以此类推
    foreach($matches[0] as $orig_url) {
        $url = htmlspecialchars_decode($orig_url);

        // Make sure we didn't pick up an email address
        if (preg_match('#^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$#i', $url)) continue;

        // Remove surrounding punctuation
        $url = trim($url, '.?!,;:\'"`([<');

        // Remove surrounding parens and the like
        //以), ], >结束的
        preg_match('/[)\]>]+$/', $url, $trailing);
        if (isset($trailing[0])) {
            preg_match_all('/[(\[<]/', $url, $opened);
            preg_match_all('/[)\]>]/', $url, $closed);
            $unopened = count($closed[0]) - count($opened[0]);

            // Make sure not to take off more closing parens than there are at the end
            $unopened = ($unopened > mb_strlen($trailing[0])) ? mb_strlen($trailing[0]):$unopened;

            $url = ($unopened > 0) ? mb_substr($url, 0, $unopened * -1):$url;
        }

        // Remove trailing punctuation again (in case there were some inside parens)
        $url = rtrim($url, '.?!,;:\'"`');

        // Make sure we didn't capture part of the next sentence
        //域名的后缀, 国家域名
        preg_match('#((?:[^.\s/]+\.)+)(museum|travel|[a-z]{2,4})#i', $url, $url_parts);

        // Were the parts capitalized any?
        $last_part = (mb_strtolower($url_parts[2]) !== $url_parts[2]) ? true:false;
        $prev_part = (mb_strtolower($url_parts[1]) !== $url_parts[1]) ? true:false;

        // If the first part wasn't cap'd but the last part was, we captured too much
        if ((!$prev_part && $last_part)) {
            $url = mb_substr($url, 0 , mb_strpos($url, '.'.$url_parts['2'], 0));
        }

        // Capture the new TLD
        preg_match('#((?:[^.\s/]+\.)+)(museum|travel|[a-z]{2,4})#i', $url, $url_parts);

        $tlds = array('ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'biz', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cat', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'edu', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'info', 'int', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jobs', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'name', 'nc', 'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'org', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'st', 'su', 'sv', 'sy', 'sz', 'tc', 'td', 'tel', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'yt', 'yu', 'za', 'zm', 'zw');

        if (!in_array($url_parts[2], $tlds)) continue;

        // Make sure we didn't capture a hash tag
        if (strpos($url, '#') === 0) continue;

        // Put the url back the way we found it.
        $url = (mb_strpos($orig_url, htmlspecialchars($url)) === FALSE) ? $url:htmlspecialchars($url);

        // Call user specified func
        if (empty($notice_id)) {
            $modified_url = call_user_func($callback, $url);
        } else {
            $modified_url = call_user_func($callback, array($url, $notice_id));
        }

        // Replace it!
        $start = mb_strpos($text, $url, $offset);
        $text = mb_substr($text, 0, $start).$modified_url.mb_substr($text, $start + mb_strlen($url), mb_strlen($text));
        $offset = $start + mb_strlen($modified_url);
    }

    return $text;
}

function common_shorten_url($long_url)
{
	require_once INSTALLDIR.'/lib/Shorturl_api.php';
    $short_url_service = new SGamePub();
    return $short_url_service->shorten($long_url);
}

function common_linkify($url) {
    $url = htmlspecialchars_decode($url);
    $attrs = array('href' => $url, 'rel' => 'external nofollow', 'target' => '_blank');
    return XMLStringer::estring('a', $attrs, $url);
}

function common_tag_link($tag, $user_id)
{	
    $user = User::staticGet('id', $user_id);    
    $second_tag_id = 100000;    
    $tag = trim($tag);
    
//    $game_id = $user->game_id;
	$game_id = 999;
    
    //通过数据库查询, 也可以存入到内存中查询, 特别是已经定义好的tag
    $second_tag = new Second_tag();
    $second_tag->whereAdd("game_id = " . $game_id);
    $second_tag->whereAdd("name = '" . $tag. "'");
    $cnt = $second_tag->find();
  
    if($cnt == 0) {
    	$second_tag = new Second_tag();
    	$second_tag->game_id = $game_id;
    	$second_tag->name = $tag;
    	// first_tag_id是不同的
    	$second_tag->first_tag_id = $game_id * 10 + 1;
		
    	if (!$second_tag->insert()) {
            common_log_db_error($second_tag, 'INSERT', __FILE__);
            return false;
      	}	
      	$second_tag_id = $second_tag->id;
    } else {
    	$second_tag->fetch();
    	$second_tag_id = $second_tag->id;
    }
    
    //有两种话题形式  hottopics?tag=:id 和 hottopics/:name
    $url = common_path('hottopics?tag=' . $second_tag_id);
    $xs = new XMLStringer();
    $xs->elementStart('em', 'tag');
    $xs->element('a', array('href' => $url, 'target' => '_blank',
                             'tag_id' => $second_tag->first_tag_id, 'rel' => 'tag'),
                 $tag);
    $xs->elementEnd('em');
    return $xs->getString();
}

//发过来的是nickname, 要不要先查uname, 然后查nickname?
//重复调用了好几次, 可以一次调用, 多次使用, 通过数组?
function common_at_link($sender_id, $nickname, $uname=null) 
{
    $sender = User::staticGet($sender_id);
    if(!is_null($uname))
    	$recipient = User::relativeUser($sender, strtolower($uname));
    else {
    	$getuname = User::getUnameByNickname($nickname);
    	if($getuname) 
    		$recipient = User::relativeUser($sender, strtolower($getuname));
    	else 
    		return $nickname;
    }    
    if ($recipient) {
        $url = $recipient->profileurl;
        $xs = new XMLStringer(false);
        $attrs = array('href' => $url,
                       'class' => 'url',
        			   'target' => '_blank');
        if (!empty($recipient->nickname)) {
            $attrs['title'] = $recipient->nickname . ' (' . $recipient->uname . ')';
        }
        //$xs->elementStart('span', 'vcard');
        $xs->elementStart('a', $attrs);
        $xs->text($nickname);
        $xs->elementEnd('a');
        //$xs->elementEnd('span');
        return $xs->getString();
    } else {
        return $nickname;
    }
}

function common_group_link($sender_id, $uname)
{
    $sender = Profile::staticGet($sender_id);
    $group = User_group::getForuname($uname);
    if ($group && $group->hasMember($sender)) {
    	return '';
    } else {
        return '!'.$uname;
    }
}

function common_canonical_tag($tag)
{
    return strtolower(str_replace(array('-', '_', '.'), '', $tag));
}

function common_at_hash_link($sender_id, $tag)
{
    $user = User::staticGet($sender_id);
    if (!$user) {
        return $tag;
    }
    $tagged = Tagtions::getTagged($user->id, common_canonical_tag($tag));
    if ($tagged) {
        $url = common_local_url($user->uname . '/subscriptions?tag=' . $tag);
        $xs = new XMLStringer();
        $xs->elementStart('span', 'tag');
        $xs->element('a', array('href' => $url,
                                'rel' => $tag),
                     $tag);
        $xs->elementEnd('span');
        return $xs->getString();
    } else {
        return $tag;
    }
}