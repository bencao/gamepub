<?php

if (!defined('SHAISHAI')) {
	exit(1);
}

require_once 'simple_html_dom.php';

class NewnoticeLib
{
	var $blankpath = null;
	var $video_width = null;
	var $video_height = null;
	var $photo_small = null;
	var $photo_big = null;

	function __construct() {
		$this->video_width = 529;
		$this->video_height = 425;
		$this->photo_small = 120;
		$this->photo_big = 396;

		$this->blankpath = common_path('theme/default/i/blank.png');
	}

	static $video_reg = array(
	//url:   http://v.youku.com/v_show/id_XMjIxMzIxNzA4.html
	//flash: http://player.youku.com/player.php/sid/XMjIxMzIxNzA4/v.swf
	//url:   http://v.youku.com/v_playlist/f5298452o1p0.html
	//flash: http://player.youku.com/player.php/Type/Folder/Fid/5298452/Ob/1/Pt/0/sid/XMjIxNDQ2NDk2/v.swf
    	'youku' => array('/^http:\/\/v\.youku\.com\/v_show\/id_(.{13})\.html/i', 
    					'/^http:\/\/v\.youku\.com\/v_playlist\/f(\d+)o(\d+)p(\d+)\.html/i'),
	//url:   http://www.tudou.com/programs/view/E7iQRy0hzAw/
	//flash: http://www.tudou.com/v/E7iQRy0hzAw/v.swf
	//url:   http://www.tudou.com/playlist/playindex.do?lid=9741061&iid=74490252
	//flash: http://www.tudou.com/l/cn9KqgUJDQw/&iid=74490252/v.swf
	//url:	 http://www.tudou.com/playlist/p/a64618.html?iid=68662954
	//flash: http://www.tudou.com/l/B4urxqXZUv8/&iid=68662954/v.swf
    	'tudou' => array('/^http:\/\/www\.tudou\.com\/programs\/view\/(.{11})/i',
    					'/^http:\/\/www\.tudou\.com\/playlist\/playindex.do\?lid=(\d+)(?:&iid=(\d+))?/i',
						'/^http:\/\/www\.tudou\.com\/playlist\/p\/a(\d+)\.html(?:\?iid=(\d+))?/i'),
	//url:   http://v.ku6.com/show/KrNnjuJ91q5cBLX9.html
	//flash: http://player.ku6.com/refer/VM_MKfB05xSvfn6c/v.swf&auto=1
	//url:   http://v.ku6.com/special/show_3869332/hAz5kUxkD8AecQXg.html
	//flash: http://player.ku6.com/refer/hAz5kUxkD8AecQXg/v.swf&auto=1
	//url:   http://v.ku6.com/special/index_3869332.html
	//flash: http://player.ku6.com/refer/hAz5kUxkD8AecQXg/v.swf&auto=1
    	'ku6' =>   array('/^http:\/\/v\.ku6\.com\/show\/(.{16})\.html/i',
    					'/^http:\/\/v\.ku6\.com\/special\/show_(\d+)\/(.{16})\.html/i',
    					'/^http:\/\/v\.ku6\.com\/special\/index_(\d+)\.html/i'),
	//url:   http://cgi.video.qq.com/v1/videopl?v=68nssR4ivlE
	//flash: http://static.video.qq.com/v1/res/qqplayerout.swf?vid=7ErWtCZC2sZ&skin=http://static.video.qq.com/v1/res/skins/QQPlayerSkin.swf&autoplay=1
	//url:   http://cgi.video.qq.com/v1/videopl/vbar?g=03901448394c3196e201
	//flash: http://static.video.qq.com/v1/res/qqplayerout.swf?vid=7T2E8SCPtAn&skin=http://static.video.qq.com/v1/res/skins/QQPlayerSkin.swf&autoplay=1
    	'qq' =>    array('/^http:\/\/cgi\.video\.qq\.com\/v1\/videopl\?v=(.{11})/i', 
    					'/^http:\/\/cgi\.video\.qq\.com\/v1\/videopl\/vbar\?g=(.*)/i'),
	//url:   http://video.sina.com.cn/v/b/41381425-1290074964.html
	//flash: http://you.video.sina.com.cn/api/sinawebApi/outplayrefer.php/vid=41381425_1290074964&autoPlay=1/s.swf
	//url:   http://video.sina.com.cn/p/news/s/v/2010-11-12/103961181673.html
	//flash: http://you.video.sina.com.cn/api/sinawebApi/outplayrefer.php/vid=41440919_1&autoPlay=1/s.swf
    	'sina' =>  array('/^http:\/\/video\.sina\.com\.cn.*\/(\d+)-(\d+)\.html$/i',
    					'/^http:\/\/video\.sina\.com\.cn.*\/\d+\.html$/i'),
	//url:   http://www.aipai.com/c5/PjU_PicgKm8maSc.html
	//flash: http://www.aipai.com/c5/PjU_PicgKm8maSc/playerOut.swf
    	'aipai' => array('/^http:\/\/www\.aipai\.com\/[a-z][0-9]\/.*\.html/i'),
	//url:   http://www.56.com/u57/v_NTY2ODQxMDI.html
	//flash: http://player.56.com/v_NTY2ODQxMDI.swf
	//url:   http://www.56.com/w81/play_album-aid-935282_vid-NDI1NTMzNDg.html
	//flash: http://player.56.com/v_NDI1NTMzNDg.swf
    	'56' =>    array('/^http:\/\/www\.56\.com\/u\d{2}\/v_(.{11})\.html/i',
    					'/^http:\/\/www\.56\.com\/w\d{2}\/play_album-aid-(\d*)_vid-(.{11})\.html/i'),
	//url:   http://v.game.sohu.com/v/1/20011/82/ODIxMjI4
	//flash: http://v.game.sohu.com/playercs2008.swf?Flvid=821228
	//url:   http://v.game.sohu.com/b/32/a_51305_MzI0MjIy.shtml
	//flash: http://v.game.sohu.com/playercs2008.swf?Flvid=824644
    	'17173' => array('/^http:\/\/v\.game\.sohu\.com\/v\/\d+\/\d+\/\d+\/.{8}/i',
    					'/^http:\/\/v\.game\.sohu\.com\/b\/(\d+)\/a_\d+_(.{8})/i'),
	//url:   http://games.joy.cn/video/2072149.htm
	//flash: http://client.joy.cn/flvplayer/2072149_1_2_1.swf
	//url:   http://real.joy.cn/Album/356061/1/1/2077463.htm
	//flash: http://client.joy.cn/flvplayer/2077463_1_2_1.swf
    	'joy' =>   array('/^http:\/\/.*\.joy\.cn\/video\/(\d+).htm/i',
    					'/^http:\/\/.*\.joy\.cn\/Album\/\d+\/\d+\/\d+\/(\d+).htm/i')
	);

	static function isVideoUrl($attach_videourl)
	{
		foreach(self::$video_reg as $website => $regs)
		{
		   foreach($regs as $reg)
		   {
		      if(preg_match($reg, $attach_videourl)) 
		      	return true;
		   }
		}
		
		return false;
	}
	
	static function isPhotoUrl($attach_photourl)
	{
		return preg_match("/^https?:\/\/(.*)?\.(jpg|jpeg|gif|png)(\?.*)?/i", $attach_photourl);
	}
	
	static function isAudioUrl($attach_audiourl)
	{
		return preg_match("/^https?:\/\/(.*)\.mp3(\?.*)?/i", $attach_audiourl);
	}
	

	function handleVideoUrl($content_shortened, $attach_videourl)
	{
		$paras = null;
		$err = null;

		if(preg_match("/youku\.com/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['youku'][0], $attach_videourl, $out)){
				$paras = $this->handleYouku($content_shortened, $attach_videourl, $out[1]);
			} else if(preg_match(self::$video_reg['youku'][1], $attach_videourl, $out)) {
				$paras = $this->handleYouku($content_shortened, $attach_videourl, '0', $out[1], $out[2], $out[3]);
			} else {
				$err = '目前只支持以 http://v.youku.com/v_show(v_playlist)/ 开头的优酷视频，后期会加入更多支持，敬请期待.';
			}
		} else if (preg_match("/tudou\.com/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['tudou'][0], $attach_videourl, $out)) {
				$paras = $this->handleTudou($content_shortened, $attach_videourl, $out[1]);
			} else if(preg_match(self::$video_reg['tudou'][1], $attach_videourl, $out)) {
				$paras = $this->handleTudou($content_shortened, $attach_videourl, '0', count($out)> 2 ? $out[2] : '0');
			} else if(preg_match(self::$video_reg['tudou'][2], $attach_videourl, $out)) {
				$paras = $this->handleTudou($content_shortened, $attach_videourl, '0', count($out)> 2 ? $out[2] : '0');
			} else {
				$err = '目前只支持以 http://www.tudou.com/programs(playlist)/view/ 开头的土豆视频，后期会加入更多支持，敬请期待.';
			}
		} else if (preg_match("/ku6\.com/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['ku6'][0], $attach_videourl, $out)) {
				$paras = $this->handleKu6($content_shortened, $attach_videourl, $out[1]);
			} else if(preg_match(self::$video_reg['ku6'][1], $attach_videourl, $out)) {
				$paras = $this->handleKu6($content_shortened, $attach_videourl, $out[2]);
			} else if(preg_match(self::$video_reg['ku6'][2], $attach_videourl, $out)) {
				$paras = $this->handleKu6($content_shortened, $attach_videourl, '0');
			} else {
				$err = '目前只支持以http://v.ku6.com/show(special)/ 开头的酷6视频, 后期会加入更多支持, 敬请期待.';
			}
		} else if (preg_match("/qq\.com/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['qq'][0], $attach_videourl, $out)) {
				$paras = $this->handleQQ($content_shortened, $attach_videourl, $out[1]);
			} else if(preg_match(self::$video_reg['qq'][1], $attach_videourl, $out)) {
				$paras = $this->handleQQ($content_shortened, $attach_videourl, '0');
			} else {
				$err = '目前只支持以http://cgi.video.qq.com/ 开头的QQ视频, 后期会加入更多支持, 敬请期待.';
			}
		} else if (preg_match("/sina\.com\.cn/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['sina'][0], $attach_videourl, $out)) {
				$paras = $this->handleSina($content_shortened, $attach_videourl, $out[1], $out[2]);
			} else if (preg_match(self::$video_reg['sina'][1], $attach_videourl, $out)){
				$paras = $this->handleSina($content_shortened, $attach_videourl, '0');
			} else {
				$err = '目前只支持以http://video.sina.com.cn/ 开头的新浪视频, 后期会加入更多支持, 敬请期待.';
			}
		} else if (preg_match("/aipai\.com/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['aipai'][0], $attach_videourl)) {
				$paras = $this->handleAipai($content_shortened, $attach_videourl);
			} else {
				$err = '目前只支持以http://www.aipai.com/[a-z][0-9]/ 开头的爱拍游戏视频, 后期会加入更多支持, 敬请期待.';
			}
		} else if (preg_match("/56\.com/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['56'][0], $attach_videourl, $out)) {
				$paras = $this->handle56($content_shortened, $attach_videourl, $out[1]);
			} else if(preg_match(self::$video_reg['56'][1], $attach_videourl, $out)) {
				$paras = $this->handle56($content_shortened, $attach_videourl, $out[2], $out[1]);
			} else {
				$err = '目前只支持以http://www.56.com/[a-z][0-9]/ 开头的我乐视频, 后期会加入更多支持, 敬请期待.';
			}
		} else if (preg_match("/sohu\.com/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['17173'][0], $attach_videourl)) {
				$paras = $this->handle17173($content_shortened, $attach_videourl);
			} else if(preg_match(self::$video_reg['17173'][1], $attach_videourl, $out)) {
				$paras = $this->handle17173($content_shortened, $attach_videourl, $out[1], $out[2]);
			} else {
				$err = '目前只支持以http://v.game.sohu.com/ 开头的17173游戏视频, 后期会加入更多支持, 敬请期待.';
			}
		} else if (preg_match("/joy\.cn/i", $attach_videourl)) {
			if(preg_match(self::$video_reg['joy'][0], $attach_videourl, $out)) {
				$paras = $this->handleJoy($content_shortened, $attach_videourl, $out[1]);
			} else if(preg_match(self::$video_reg['joy'][1], $attach_videourl, $out)) {
				$paras = $this->handleJoy($content_shortened, $attach_videourl, $out[1]);
			} else {
				$err = '目前只支持以http://*.joy.cn/video(Album)/ 开头的激动视频, 后期会加入更多支持, 敬请期待.';
			}
		} else {
			$err = '本站只支持酷6, 优酷, 土豆, 爱拍游戏, 新浪, QQ, 我乐, 17173游戏, 激动的视频.';
		}

		if ($paras) {
			return $paras;
		} else {
			$err = '您输入的视频网址无法解析，请直接拷贝视频页的地址再试一次。';
			return $err;
		}
	}
    
	function handleYouku($content_shortened, $attach_videourl, $vid, $fid = '0', $ob = '1', $pt = '0') {
		$html = file_get_html($attach_videourl);
		
		//
		//vid is necessary, get it from html
		//
		if ($vid == '0') {
			$tranmit_node = $html->find('li.transmit', 0);
			if (!is_null($tranmit_node) && preg_match ('/sendVideoLink\(\'(.{13})\'\)/', $tranmit_node->innertext, $matches)) {
				$vid =  $matches[1];
			} else {
				return false;
			}
		}
		
		//
		//Get title and photo from html
		//
		$video_title = "优酷视频";
		$photo_url = common_path('theme/default/i/video_youku.jpg');
        $title_node = $html->find('title', 0);
        if (!is_null($title_node)) {
        	$video_title = $title_node->innertext;
        	$video_title = substr($video_title, 0, strpos($video_title, '-'));
        }
        $photo_node = $html->find('li.download', 0);
        if (!is_null($photo_node)) {
        	if(preg_match ('/\|(http:\/\/g.*)\|/', $photo_node->innertext, $matches))
				$photo_url = $matches[1];
        }
        
        if ($fid != '0') {
        	//it is playlist
        	$flashsrc = 'http://player.youku.com/player.php/Type/Folder/Fid/' . $fid . '/Ob/' . $ob . '/Pt/' . $pt . '/sid/' . $vid . '/v.swf';
        } else {
        	$flashsrc = 'http://player.youku.com/player.php/sid/' . $vid . '/v.swf';
        }
		$video_rendered = $this->getVideoPlayRendered($flashsrc, 'isAutoPlay=true&winType=interior');				
		$add_content = $attach_videourl;
		$video_source = "优酷";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);
		$add_rendered = $this->getVideoRendered($video_id, $photo_url);
		
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);
	}
	
	function handleTudou($content_shortened, $attach_videourl, $vid, $iid = '0') {
		$html = file_get_contents($attach_videourl);
		
		//
		//Get title and photo from html
		//
		$video_title = "土豆视频";
		$photo_url = common_path('theme/default/i/video_tudou.jpg');
		
		//
		//vid is necessary, get it from html
		//
		$lid_code = '0';
		if ($vid == '0') {
			if (preg_match ('/lid_code = \'(.{11})\'\s,defaultIid = (\d+)/', $html, $matches)) {
				$lid_code = $matches[1];
				$defaultIid = $matches[2];
				if ($iid == '0') {
					$iid = $defaultIid;
				}
				preg_match ('/iid:' . $iid . '\s[\d\D]*?,title:\"(.*)\"\s,(?:shortDesc:\"(.*)\"\s,)?icode:\"(.*)\"[\d\D]*?,pic:\"(.*)\"/', $html, $matches);
				$match_count = count($matches);
				$video_title = iconv("GBK", "UTF-8//IGNORE", $matches[1] . ($match_count == 5 ? $matches[2] : ''));
				$vid = $matches[$match_count-2];
				$photo_url = $matches[$match_count-1];
			} else {
				return false;
			}
		} else {
			if (preg_match ('/thumbnail = \'(.*)\'[\d\D]*,kw = \"(.*)\"/', $html, $matches)) {
				$photo_url = $matches[1];
				$video_title = iconv("GBK", "UTF-8//IGNORE", $matches[2]);
			}
		}
        
		if ($lid_code != '0') {
        	//it is playlist
        	$flashsrc = 'http://www.tudou.com/l/' . $lid_code . '/&iid=' . $iid . '/v.swf'; 
        } else {
        	$flashsrc = 'http://www.tudou.com/v/' . $vid . '/v.swf';
        }
		$video_rendered = $this->getVideoPlayRendered($flashsrc, 'autoPlay=true');    
		$add_content = $attach_videourl;
		$video_source = "土豆";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);	
		$add_rendered = $this->getVideoRendered($video_id, $photo_url); 
		
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);
	}
	
	function handleKu6($content_shortened, $attach_videourl, $vid) {
		$html = file_get_html($attach_videourl);
		
		//
		//vid is necessary, get it from html
		//
		if ($vid == '0') {
			$out_swf_node = $html->find('input#outSideSwfCode', 0);
			if (!is_null($out_swf_node) && preg_match('/^http:\/\/player\.ku6\.com\/refer\/(.{16})\/v\.swf$/', $out_swf_node->getAttribute('value'), $matches)) {
				$vid = $matches[1];
			} else {
				return false;
			}
		}
		
		//
		//Get title and photo from html
		//
		$video_title = "酷6视频";
		$photo_url = common_path('theme/default/i/video_ku6.jpg');
		$title_node = $html->find('title', 0);
        if (!is_null($title_node)) {
        	//convert to utf-8 encoding
        	$video_title = iconv("GBK", "UTF-8//IGNORE", $title_node->innertext);
        	$video_title = substr($video_title, 0, strpos($video_title, '在线观看'));
        }			
        $photo_node = $html->find('span.s_pic', 0);	
        if (!is_null($photo_node)) {
        	$photo_url = $photo_node->innertext; 
        }

        $flashsrc = 'http://player.ku6.com/refer/' . $vid . '/v.swf&auto=1';
		$video_rendered = $this->getVideoPlayRendered($flashsrc);
		$add_content = $attach_videourl;
		$video_source = "酷6";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);
		$add_rendered = $this->getVideoRendered($video_id, $photo_url);  
					
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);	
	}
	
	function handleQQ($content_shortened, $attach_videourl, $vid) {
		$html = file_get_html($attach_videourl);
		
		//
		//vid is necessary, get it from html
		//
		if ($vid == '0') {
			$out_swf_node = $html->find('div#flashbg', 0);
			if (!is_null($out_swf_node)) {
				$vid = $out_swf_node->getAttribute('vid');
			} else {
				return false;
			}
		}
		
		//
		//Get title and photo from html
		//
		$video_title = "QQ视频";
		$photo_url = common_path('theme/default/i/video_qq.jpg');
		$title_node = $html->find('#videoTitle', 0);
        if (!is_null($title_node)) {
        	$video_title = $title_node->plaintext;
        }
		$photo_url = 'http://vpic.video.qq.com/12/' . $vid . '_1.jpg';
		
		$flashsrc = 'http://cache.tv.qq.com/QQPlayer.swf?vid=' . $vid . '&autoplay=1';
		$video_rendered = $this->getVideoPlayRendered($flashsrc);
		$add_content = $attach_videourl;
		$video_source = "腾讯";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);
		$add_rendered = $this->getVideoRendered($video_id, $photo_url);  
					
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);
	}
	
	function handleSina($content_shortened, $attach_videourl, $vid, $uid = '0') {	
		$html = file_get_contents($attach_videourl);

		//
		//Get title and photo from html
		//
		$video_title = '新浪视频';
		$photo_url = common_path("theme/default/i/video_xinlang.jpg");
		if(preg_match ('/vid\s:\'(.*)\',[\d\D]*pic:\s\'(.*)\',[\d\D]*title:\'(.*)\',/', $html, $matches)) {
			$vid = $matches[1];
			$photo_url = $matches[2];
			$video_title = $matches[3];
		} 
		if($vid == '0') {
			return false;
		}
		$flashsrc = 'http://you.video.sina.com.cn/api/sinawebApi/outplayrefer.php/vid=' . $vid . '_' . $uid . '&autoPlay=1/s.swf';
		$video_rendered = $this->getVideoPlayRendered($flashsrc);
		$add_content = $attach_videourl;
		$video_source = "新浪";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);	
		$add_rendered = $this->getVideoRendered($video_id, $photo_url); 
		
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);
	}
	
	function handleAipai($content_shortened, $attach_videourl) {
		$html = file_get_contents($attach_videourl);
		
		//
		//Get vid, title and photo from html
		//
		$video_title = '爱拍游戏视频';
		$photo_url = common_path('theme/default/i/video_aipai.jpg');	
		if(preg_match ('/<title>(.*?) - .*?<\/title>[\d\D]*flashvars\s*:\s*\"(.*)\"/', $html, $vars)) {
			$video_title = $vars[1];
			$info = urldecode($vars[2]);
			$vid = '';
			$baseurl = '';
			$photo_url = '';
			
			if(preg_match ('/\"id\":\"(\d+)\"/', $info, $matches)) {
				$vid = $matches[1];
			}
			
			if(preg_match ('/\"baseURL\":\"(.*)\",\"flvFileName\"/', $info, $matches)) {
				$baseurl = str_replace('\\', '', $matches[1]);
			}
			
			if ($vid != '' && $baseurl != '') {
				$photo_url = $baseurl . $vid . '_big.jpg';
			} 
		} else {
			$vid = '1000000';
		}
		
		$flashsrc = str_replace('.html', '/playerOut.swf', $attach_videourl);
		$video_rendered = $this->getVideoPlayRendered($flashsrc);
		$add_content = $attach_videourl;
		$video_source = "爱拍游戏";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);
		$add_rendered = $this->getVideoRendered($video_id, $photo_url);  
		
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);
	}
	
	function handle56($content_shortened, $attach_videourl, $vid, $aid = '0') {
		$html = file_get_contents($attach_videourl);

		//
		//Get title and photo from html
		//
		$video_title = "我乐视频";
		$photo_url = common_path('theme/default/i/video_56.jpg');
		if ($aid != '0') {
			//it is album
			if(preg_match ('/<title>(.*?) - .*?<\/title>[\d\D]*f_js_playObject\(\'img_host=(.*)&host=.*&pURL=(.*)&sURL=(.*)&user=(.*)&URLid=(.*)&key=.*\'\);/i', $html, $matches)) {
				$video_title = $matches[1];
				$photo_url = 'http://img.' . $matches[2] . '/images/' . $matches[3] . '/' . $matches[4]. '/' . $matches[5] . 'i56olo56i56.com_' . $matches[6] . '.jpg';
			}
		} else {
			if(preg_match ('/<title>(.*?) - .*?<\/title>[\d\D]*var\s_oFlv_c\s=\s\{.*\"img\":\"(.*)\"\};/i', $html, $matches)) {
				$video_title = $matches[1];
				$photo_url =  str_replace('\\', '', $matches[2]);
			}
		}

        $flashsrc = 'http://player.56.com/v_' . $vid . '.swf';
		$video_rendered = $this->getVideoPlayRendered($flashsrc);				
		$add_content = $attach_videourl;
		$video_source = "我乐";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);
		$add_rendered = $this->getVideoRendered($video_id, $photo_url);
		
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);
	}
	
	function handle17173($content_shortened, $attach_videourl, $bid = '0', $vcode = '0') {
		$html = file_get_html($attach_videourl);
		
		//
		//Get vid, title and photo from html
		//
		$flash_node = $html->find('#ExternalInterface embed', 0);
		if (!is_null($flash_node)) {
			$vid = $flash_node->getAttribute('flashvars');
			if(preg_match ('/&*Flvid=(\d*)&*/i', $html, $matches)) {
				$vid = $matches[1];
			}
		} else {
			return false;
		}
		
		$video_title = "17173游戏视频";
		$photo_url = common_path('theme/default/i/video_17173.jpg');
		$title_node = $html->find('title', 0);
        if (!is_null($title_node)) {
        	//convert to utf-8 encoding
        	$video_title = iconv("GBK", "UTF-8//IGNORE", $title_node->innertext);
        	$video_title = substr($video_title, 0, strpos($video_title, '-'));
        }			
        if ($bid != '0') {
        	//it is album
        	if (preg_match ('/fFillAlbumhot\(\'listrela\',\'(\d+)\',\'(\d+)\',\'.*\'\);/i', $html, $matches)) {
        		$ori_video_address = 'http://v.game.sohu.com/v/' . $matches[1] . '/' . $matches[2] . '/' . $bid . '/' . $vcode;
        		$html = file_get_html($ori_video_address);
        	}
        } 
		$photo_node = $html->find('span.s_pic', 0);	
	    if (!is_null($photo_node)) {
	        $photo_url = $photo_node->innertext; 
	    }

        $flashsrc = 'http://v.game.sohu.com/playercs2008.swf?Flvid=' . $vid;
		$video_rendered = $this->getVideoPlayRendered($flashsrc);
		$add_content = $attach_videourl;
		$video_source = "17173游戏";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);
		$add_rendered = $this->getVideoRendered($video_id, $photo_url);  
					
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);	
	}
	
	function handleJoy($content_shortened, $attach_videourl, $vid) {
		$html = file_get_contents($attach_videourl);
		
		//
		//Get title and photo from html
		//
		$video_title = "激动视频";
		$photo_url = common_path('theme/default/i/video_joy.jpg');
		if(preg_match ('/var\s_video_obj=\{.*,cover:\"(.*)\",.*,title:\"(.*)\",desc:.*};/i', $html, $matches)) {
			$video_title = $matches[2];
			$photo_url = $matches[1];
		}

        $flashsrc = 'http://client.joy.cn/flvplayer/' . $vid . '_1_2_1.swf';
		$video_rendered = $this->getVideoPlayRendered($flashsrc);				
		$add_content = $attach_videourl;
		$video_source = "激动";
		$video_id = Video::saveNew($video_title, $video_rendered, $video_source, 100000, $photo_url, $flashsrc, $vid);
		$add_rendered = $this->getVideoRendered($video_id, $photo_url);
		
		return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'video_id' => $video_id);
	}
	
    function getVideoPlayRendered($flashsrc, $flashvars = '') {
    	return '<embed src="' . $flashsrc . '" type="application/x-shockwave-flash" ' .
			'width="' . $this->video_width . '" height="' . $this->video_height . '" align="middle" ' .
			'quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="opaque" ' .
    		'flashvars="' . $flashvars . '"></embed>';
    }
    
	function getVideoRendered($video_id, $picpath) {
		return '<div class="video_message"><a target="_blank" class="smallimagebtn"' .
				' href="'. common_path('share/getvideo/' . $video_id) . '" ><img class="smallimage" width="120" height="90" src="' . (empty($picpath) ? $this->blankpath : $picpath) . '"/><em></em></a></div>';
	}
	
	function easyphpthumbnail($filename, $size, $uname, $subpath) 
    {
    	require_once(INSTALLDIR.'/extlib/easyphpthumbnail.class.php');
    	$thumb = new easyphpthumbnail;
    	//或者形成一个大的水印, 再缩小, 处理三次
//    	if($size == 100) {
//	    	$thumb->Copyrighttext          = 'www.lshai.com/' . $uname;
//			$thumb->Copyrightposition      = '90% 95%';
//			$thumb->Copyrightfontsize      = 0.2;
//			$thumb->Copyrighttextcolor 	   = '#FFFFFF';
//    	} else 
    	if($size == $this->photo_big) {
    		$thumb->Copyrighttext          = 'www.gamepub.cn/' . $uname;
			$thumb->Copyrightposition      = '90% 95%';
			$thumb->Copyrightfontsize      = 3;
			$thumb->Copyrighttextcolor 	   = '#FFFFFF';
    	}    	
		
		$thumb->Thumbwidth = $size;
		$thumb->Percentage = true;
		$filepath = File::dir($subpath);
		$thumb->Thumblocation = $filepath;
		$thumb->Thumbprefix = '';
		$thumb->Thumbsubfix = '_' . $size;
		$cmdext = explode('.', $filename);
		
		$newfilename = $cmdext[0] . '_' . $size . '.' . $cmdext[1];
		$thumb->Createthumb(File::path($filename, $subpath), 'file');
		return $newfilename;
    }
    
    function handlePhotoUrl($attach_photo, $user) {
    	if(! self::isPhotoUrl($attach_photo)){
    		return '您输入的格式无法解析, 我们只支持jpg, png, gif等格式的图片.';
    	}
    	
    	ImageFile::imageSize($user, $attach_photo);
	        		
		preg_match("/((jpg|jpeg|gif|png))/i", $attach_photo, $type);
        $subpath = File::monthSubpath($user->id);
        $filename = File::photoRandomName($type[1]);
        $filepath = File::path($filename, $subpath);
     
        $get_file = file_get_contents($attach_photo);
		$fp = @fopen($filepath, "w");
		@fwrite($fp, $get_file);
		fclose($fp);
		
        return $this->storePhoto($filename, $subpath, $user->uname);
    }
    
    function handleUploadPhoto($tmpfilepath, $user)
    {	
    	// move file to user dir
    	$tmpfilename = substr($tmpfilepath, strrpos($tmpfilepath, '/') + 1);
    	$tmpsubpath = Avatar::tmpsubpath($user->id);
        
        preg_match("/((jpg|jpeg|gif|png))/i", $tmpfilename, $type);
        $subpath = File::monthSubpath($user->id);
        $filename = File::photoRandomName($type[1]);
        $filepath = File::path($filename, $subpath);
        
        rename($tmpsubpath . $tmpfilename, $filepath);
        
		return $this->storePhoto($filename, $subpath, $user->uname);
    }
    
    function handleMimePhoto($mimetype, $user)
    {
    	if (empty($user)) {
            $this->serverError('在保存文件时, 您退出了.');
        }
        $basename = basename($_FILES['photofile']['name']);
        require_once 'MIME/Type/Extension.php';
        $mte = new MIME_Type_Extension();
        $ext = $mte->getExtension($mimetype);
        $subpath =  File::monthSubpath($user->id);
        $filename = File::photoRandomName($ext);     
        $filepath = File::path($filename, $subpath);

        if (move_uploaded_file($_FILES['photofile']['tmp_name'], $filepath)) {
        	return $this->storePhoto($filename, $subpath, $user->uname);
        } else {
            $this->clientError('文件不能移到目标目录.', 400, $this->format);
            return null;
        }
    }
    
    function storePhoto($filename, $subpath, $uname)
    {
        $file = new File;
        $file->filename = $filename;
        $file->url = File::url($filename, $subpath);
        $filepath = File::path($filename, $subpath);
        $file->size = filesize($filepath);
        $file->date = time();
        //$file->mimetype = $mimetype;
        
        $file_id = $file->insert();
        if (!$file_id) {
            common_log_db_error($file, "INSERT", __FILE__);
            $this->clientError('您的文件存储错误, 请再试一次.');
        }
        
		//120 width
        $filename_thumbnail = $this->easyphpthumbnail($filename, $this->photo_small, $uname, $subpath);
        $attachfile_url2 = File::url($filename_thumbnail, $subpath);
        //396 width
        $filename_thumbnail2 = $this->easyphpthumbnail($filename, $this->photo_big, $uname, $subpath);
        $attachfile_url3 = File::url($filename_thumbnail2, $subpath);

        $add_content = $attachfile_url2;
        $add_rendered = $this->getPhotoRendered($attachfile_url2, $attachfile_url3, $filename, $subpath); 

        return array('add_content' => $add_content, 'add_rendered' => $add_rendered, 'photo_id' => $file_id);
    }
    
    function deletePhoto($photo_id)
    {
    	$file = new File;
    	$file->id = $photo_id;
    	$file->find();
    	
    	if($file->fetch()) {
    		$fileurl = $file->url;
    		$filepath = File::pathFromUrl($fileurl);
    		if (!$filepath) {
    			$cmdext = explode('.', $filepath);
				$filename_thumbnail = $cmdext[0] . '_' . $this->photo_small . '.' . $cmdext[1];
				$filename_thumbnail2 = $cmdext[0] . '_' . $this->photo_big . '.' . $cmdext[1];
    			@unlink($filepath);
    			@unlink($filename_thumbnail);
    			@unlink($filename_thumbnail2);
    		}
    		$file->delete();
    	}
    }
 
    function getPhotoRendered($url, $url2, $filename, $subpath) {
    	return '<div class="image_message"><div class="smallpicture"><a class="smallimagebtn" href="javascript:void(0);">'
            .'<img class="smallimage" src="'.$url.'" /></a></div><div class="bigpicture rounded5" style="display:none"><div class="btnbanel">'
            .'<cite class="cite"></cite><cite><a class="pickpicture" href="javascript:void(0);">收起</a></cite><cite class="cite">|</cite>'
            .'<cite><a class="primitivepicture" href="'.File::url($filename, $subpath)
            .'" target="_blank">原始图片</a></cite><cite class="cite">|</cite>'
            .'<cite><a class="rightrotate" href="javascript:void(0);">向右转</a></cite><cite class="cite">|</cite>'
            .'<cite><a class="leftrotate" href="javascript:void(0);">向左转</a></cite></div><div class="wrappicture">'
            .'<a class="bigimagebtn" href="javascript:void(0);"><img class="bigimage" src="'.$url2
            .'" /></a></div></div></div>';
    }
    
    function handleAudioUrl($attach_audiourl) {
    	if(! self::isAudioUrl($attach_photo)){
    		return '我们只支持mp3格式的音乐.';
    	}
    	
    	$add_rendered = '<div class="music_message">' .
	        			'<a href="#" link="' . $attach_audiourl. '"></a></div>';
		        	
        $add_content = $attach_audiourl;
        
        return array('add_content' => $add_content, 'add_rendered' => $add_rendered);
    }
}