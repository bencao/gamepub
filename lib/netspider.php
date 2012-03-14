<?php

if (!defined('SHAISHAI')) { exit(1); }



function net_getgamenews($id)
{
	try {
		$newslink_url = null;
		$mainlink_url = null;
		$reports = array();
		switch($id)
		{
			case 1:
    		$newslink_url = "http://dnf.qq.com/main.shtml";
    		$mainlink_url = "http://dnf.qq.com";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_DNF($news_content,$mainlink_url);
			break;
			case 2:
    		$newslink_url = "http://www.warcraftchina.com/index.html";
    		$mainlink_url = "http://www.warcraftchina.com";
			$news_content = file_get_contents($newslink_url);
			//$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_WOW($news_content,$mainlink_url);
			break;
			//梦幻西游
			case 3:
    		$newslink_url = "http://xyq.163.com/";
    		$mainlink_url = "http://xyq.163.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_XYQ163($news_content,$mainlink_url);
			//天龙八部
			case 4:
    		$newslink_url = "http://tl.changyou.com/index.shtml";
    		$mainlink_url = "http://tl.changyou.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_TLBB($news_content,$mainlink_url);
			break;
			//天下贰
			case 5:
    		$newslink_url = "http://tx2.163.com/index.html";
    		$mainlink_url = "http://tx2.163.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_TX2($news_content,$mainlink_url);
			break;
			
			//穿越火线
			case 10:
    		$newslink_url = "http://cf.qq.com/main.shtml";
    		$mainlink_url = "http://cf.qq.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_CFQQ($news_content,$mainlink_url);
			break;
			//大明龙权
			case 13:
    		$newslink_url = "http://dm.qq.com/main.shtml";
    		$mainlink_url = "http://dm.qq.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_DMLQQQ($news_content,$mainlink_url);
			break;
			//反恐精英Online
			case 15:
    		$newslink_url = "http://csol.tiancity.com/homepage/article/Class_4_Time_1.html";
    		$mainlink_url = "http://csol.tiancity.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_CSONLINE($news_content,$mainlink_url);
			break;
			
			//龙之谷
			case 23:
    		$newslink_url = "http://dn.sdo.com/web7/home/index.asp";
    		$mainlink_url = "http://dn.sdo.com/web7/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_DragonHollow($news_content,$mainlink_url);
			break;
			
			//冒险岛
			case 25:
    		$newslink_url = "http://mxd.sdo.com/web5/home/home.asp";
    		$mainlink_url = "http://mxd.sdo.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_MXD($news_content,$mainlink_url);
			break;
			//梦幻诛仙
			case 27:
    		$newslink_url = "http://mhzx.wanmei.com/main.htm";
    		$mainlink_url = "http://mhzx.wanmei.com/";
			$news_content = file_get_contents($newslink_url);
			//$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_MHZX($news_content,$mainlink_url);
			break;
			//名将三国
			case 28:
    		$newslink_url = "http://wof.the9.com/main.shtml";
    		$mainlink_url = "http://wof.the9.com/";
			$news_content = file_get_contents($newslink_url);
			//$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_MJSG($news_content,$mainlink_url);
			break;
			//QQ炫舞
			case 30:
    		$newslink_url = "http://x5.qq.com/main.shtml";
    		$mainlink_url = "http://x5.qq.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_X5QQ($news_content,$mainlink_url);
			break;
			//热血江湖
			case 32:
    		$newslink_url = "http://rxjh.17game.com/index.htm";
    		$mainlink_url = "http://rxjh.17game.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_RXJH($news_content,$mainlink_url);
			break;
			//问道
			case 39:
    		$newslink_url = "http://wd.gyyx.cn/Index_wd.aspx";
    		$mainlink_url = "http://wd.gyyx.cn/";
			$news_content = file_get_contents($newslink_url);
			//$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_WD($news_content,$mainlink_url);
			break;
			//永恒之塔
			case 47:
    		$newslink_url = "http://aion.sdo.com/web4/home/index.html";
    		$mainlink_url = "http://aion.sdo.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_YHZT($news_content,$mainlink_url);
			break;
			//QQ飞车
			case 62:
    		$newslink_url = "http://speed.qq.com/index.shtml";
    		$mainlink_url = "http://speed.qq.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_SPEEDQQ($news_content,$mainlink_url);
			break;
			//QQ三国
			case 63:
    		$newslink_url = "http://sg.qq.com/webplat/info/54/169/list_1.shtml";
    		$mainlink_url = "http://sg.qq.com/";
			$news_content = file_get_contents($newslink_url);
			$news_content = iconv('gb2312','utf-8//IGNORE',$news_content);
			$reports = net_getnews_SANGUOQQ($news_content,$mainlink_url);
			break;
		}

	} catch (Exception $e) {
		$this->serverError($e->getMessage());
		return array();
	}
	return $reports;
}
function net_getnews_DNF($news_content,$mainlink_url)
{
	$newlist = array();

	//$mode = "{<div[^>]*class=[^>]*news_dtl[^>]*>([.|\x80-\xFF]*)</ul>}";
	$mode = "{<h2\\s*class=\"hidden\">新闻([\\W|\\w]*)更多新闻}";
	if (preg_match($mode, $news_content, $news) == true) {
	//	$mode = "{<img[^>]*src=\"([^\">]*)\"[^>]*>}";
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>([^>]{10,255})</a>}";
//		echo "OK1";

//		echo $news[0];
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
					$url = $outsidelink_imgarr[1][$i];
					$content = $outsidelink_imgarr[2][$i];
					if (strpos($url,"ttp://",0) == false)
					{
						$url = $mainlink_url.$url;	
					}
					$report = new Report();
					$report->url = $url;
					
					$report->content = "[新闻]".$content;
					
				//	echo $report->url;
				
				//	echo $report->content;
					array_push($newlist,$report);
			}
		}
	}
	
	$mode = "{<h2\\s*class=\"hidden\">公告([\\W|\\w]*)更多公告}";
	if (preg_match($mode, $news_content, $news) == true) {
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>([^>]{10,255})</a>}";
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
					$url = $outsidelink_imgarr[1][$i];
					$content = $outsidelink_imgarr[2][$i];
					if (strpos($url,"ttp://",0) == false)
					{
						$url = $mainlink_url.$url;	
					}
					$report = new Report();
					$report->url = $url;
					$report->content = "[公告]".$content;		
					array_push($newlist,$report);
			}
		}
	}
	
	return $newlist;
}

function net_getnews_WOW($news_content,$mainlink_url)
{
	$newlist = array();
	$mode = "{<a\\s*href=\"/news\"\\s*title=\"新闻\">新闻</a>([\\W|\\w]*)全部新闻}";
	if (preg_match($mode, $news_content, $news) == true) {
	//	$mode = "{<img[^>]*src=\"([^\">]*)\"[^>]*>}";
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>([^>]{10,255})</a>}";

		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
					$url = $outsidelink_imgarr[1][$i];
					$content = $outsidelink_imgarr[2][$i];
					if (strpos($url,"ttp://",0) == false)
					{
						$url = $mainlink_url.$url;	
					}
					$report = new Report();
					$report->url = $url;
					
					$report->content = $content;
					
					array_push($newlist,$report);
			}
		}
	}
	

	$mode = "{<a\\s*href=\"/events\"\\s*title=\"活动\">活动</a>([\\W|\\w]*)全部活动}";
	if (preg_match($mode, $news_content, $news) == true) {
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>([^>]{10,255})</a>}";
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
					$url = $outsidelink_imgarr[1][$i];
					$content = $outsidelink_imgarr[2][$i];
					if (strpos($url,"ttp://",0) == false)
					{
						$url = $mainlink_url.$url;	
					}
					$report = new Report();
					$report->url = $url;
					$report->content = $content;		
					array_push($newlist,$report);
			}
		}
	}
	
	return $newlist;
}

function net_getnews_DragonHollow($news_content,$mainlink_url)
{
	$newlist = array();
	
	$count = 0;
	while ($count < 4)
	{
		$count++;
		switch($count)
		{
			case 1:
			$mode = "{<div\\s*class=\"r_c1Content\"\\s*id=\"r_c1content2\"\\s*style=\"display:none;\">([\\W|\\w]*)<div\\s*class=\"r_c1Content\"\\s*id=\"r_c1content3\"\\s*style=\"display:none;\">}";
			break;
			case 2:
			$mode = "{<div\\s*class=\"r_c1Content\"\\s*id=\"r_c1content3\"\\s*style=\"display:none;\">([\\W|\\w]*)<div\\s*class=\"r_c1Content\"\\s*id=\"r_c1content4\"\\s*style=\"display:none;\">}";
			break;
			case 3:
			$mode = "{<div\\s*class=\"r_c1Content\"\\s*id=\"r_c1content4\"\\s*style=\"display:none;\">([\\W|\\w]*)<div\\s*class=\"r_c1Content\"\\s*id=\"r_c1content5\"\\s*style=\"display:none;\">}";
			break;
			case 4:
			$mode = "{<div\\s*class=\"r_c1Content\"\\s*id=\"r_c1content5\"\\s*style=\"display:none;\">([\\W|\\w]*)<!--新闻\\s*end-->}";
			break;
		}
	if (preg_match($mode, $news_content, $news) == true) {
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>([^>]{10,255})</a>}";

		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
					$url = $outsidelink_imgarr[1][$i];
					$content = $outsidelink_imgarr[2][$i];
					if (strpos($url,"../",0) != false)
					{
						$url = substr_replace($url,$mainlink_url,0,3);	
					}else if (strpos($url,"ttp://",0) == false)
					{
						$url = $mainlink_url.$url;	
					}
					$report = new Report();
					$report->url = $url;
					
					$report->content = $content;
					
					array_push($newlist,$report);
			}
		}
	}
	}
	
	return $newlist;
}

function net_getnews_MXD($news_content,$mainlink_url)
{
	$newlist = array();
	$mode = "{<strong>([^>]*)</strong><span\\s*class=\"tClass\\s*UnityNews_\"><a href=\"([^\"]*)\"[^>]*?>([^>]{10,255})</a>}";

	if (preg_match_all($mode, $news_content, $outsidelink_imgarr) == true) {
			
		foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
	
				$url = $outsidelink_imgarr[2][$i];
				$content = $outsidelink_imgarr[3][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
				$report->content = $outsidelink_imgarr[1][$i].$content;
					
				array_push($newlist,$report);
		}
	}
	
	return $newlist;
}

function net_getnews_CFQQ($news_content,$mainlink_url)
{
	$newlist = array();
	
	$count = 0;
	while ($count < 4)
	{
		$count++;
		switch($count)
		{
			case 1:
			$mode = "{<div\\s*id=\"newscate_1\"([\\W|\\w]*)<div\\s*id=\"newscate_2\"}";
			break;
			case 2:
			$mode = "{<div\\s*id=\"newscate_2\"([\\W|\\w]*)<div\\s*id=\"newscate_3\"}";
			break;
			case 3:
			$mode = "{<div\\s*id=\"newscate_3\"([\\W|\\w]*)<div\\s*id=\"newscate_4\"}";
			break;
			case 4:
			$mode = "{<div\\s*id=\"newscate_4\"([\\W|\\w]*)<div\\s*id=\"player_site\"}";
			break;
		}
	if (preg_match($mode, $news_content, $news) == true) {
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>([^>]{10,255})</a>}";
		
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"../",0) != false)
				{
					$url = substr_replace($url,$mainlink_url,0,3);	
				}else if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				switch($count)
				{
				case 1:
					$report->content = "[新闻]".$content;
				break;
				case 2:
					$report->content = "[公告]".$content;
				break;
				case 3:
					$report->content = "[活动]".$content;
				break;
				case 4:
					$report->content = "[赛事]".$content;
				break;
				}
				array_push($newlist,$report);
			}
		}
	}
	}
	
	return $newlist;
}

function net_getnews_TX2($news_content,$mainlink_url)
{
	$newlist = array();

	$count = 0;
	while ($count < 4)
	{
		$count++;
		switch($count)
		{
			case 1:
			$mode = "{\\[新闻\\]</em><a[^>]*href=\"([^\"]*)\"[^>]*>([^>]*)</a>}";
			break;
			case 2:
			$mode = "{\\[公告\\]</em><a[^>]*href=\"([^\"]*)\"[^>]*>([^>]*)</a>}";
			break;
			case 3:
			$mode = "{\\[活动\\]</em><a[^>]*href=\"([^\"]*)\"[^>]*>([^>]*)</a>}";
			break;
			case 4:
			$mode = "{\\[媒体\\]</em><a[^>]*href=\"([^\"]*)\"[^>]*>([^>]*)</a>}";
			break;
		}

		if (preg_match_all($mode, $news_content, $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"../",0) != false)
				{
					$url = substr_replace($url,$mainlink_url,0,3);	
				}else if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				switch($count)
				{
				case 1:
					$report->content = "[新闻]".$content;
				break;
				case 2:
					$report->content = "[公告]".$content;
				break;
				case 3:
					$report->content = "[活动]".$content;
				break;
				case 4:
					$report->content = "[媒体]".$content;
				break;
				}
				array_push($newlist,$report);
			}
		}
	}
	
	
	return $newlist;
}

function net_getnews_XYQ163($news_content,$mainlink_url)
{
	$newlist = array();
	

	$mode = "{<!--pn-news-summary-->([\\W|\\w]*)<!--news-list 公告-->}";

	if (preg_match($mode, $news_content, $news) == true) {
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>\\s*([^>]{10,255})</a>}";
		
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				$report->content = "[公告]".$content;
				array_push($newlist,$report);
			}
			
		}
	}
	
	
	return $newlist;
}

function net_getnews_X5QQ($news_content,$mainlink_url)
{
	$newlist = array();
	
	$mode = "{<div\\s*style=\"display\\:none\">([\\W|\\w]*)<div\\s*style=\"display\\:none\">}";
	$count = 0;
	if (preg_match($mode, $news_content, $news) == true) {
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>\\s*<font\\s*color[^>]*>([^>]{10,255})</font>\\s*</a>}";
		
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {

				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				if ($count< 6)
				{
					$report->content = "[新闻]".$content;
				}else if($count < 12)
				{
					$report->content = "[公告]".$content;
				}else if($count < 18)
				{
					$report->content = "[活动]".$content;
				}
				array_push($newlist,$report);
				$count++;
			}
			
		}
	}
	
	
	return $newlist;
}


function net_getnews_YHZT($news_content,$mainlink_url)
{
	$newlist = array();

	$count = 0;
	while ($count < 4)
	{
		$count++;
		switch($count)
		{
			case 1:
			$mode = "{<div[^>]*id=\"tab_show7_2\"([\\W|\\w]*)</div}";
			break;
			case 2:
			$mode = "{<div[^>]*id=\"tab_show7_3\"([\\W|\\w]*)</div}";
			break;
			case 3:
			$mode = "{<div[^>]*id=\"tab_show7_4\"([\\W|\\w]*)</div}";
			break;
			case 4:
			$mode = "{<div[^>]*id=\"tab_show7_5\"([\\W|\\w]*)</div}";
			break;
		}
	if (preg_match($mode, $news_content, $news) == true) {
		
		$mode = "{<a[^>]*href=\'([^\']*)\'[^>]*title[^>]*>([^>]{10,255})</a>}";

		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				switch($count)
				{
				case 1:
					$report->content = "[焦点]".$content;
				break;
				case 2:
					$report->content = "[公告]".$content;
				break;
				case 3:
					$report->content = "[活动]".$content;
				break;
				case 4:
					$report->content = "[支付]".$content;
				break;
				}
				array_push($newlist,$report);
			}
		}
	}
	}
	
	return $newlist;
}



function net_getnews_SPEEDQQ($news_content,$mainlink_url)
{
	$newlist = array();
	
	$mode = "{<div[^>]*class=\"news_list\"[^>]*id=\"news_list\">([\\W|\\w]*)</div}";
	$count = 0;
	if (preg_match($mode, $news_content, $news) == true) {
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>\\s*<font\\s*color[^>]*>([^>]{10,255})</font>\\s*</a>}";
		
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {

			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];

				$count++;
				if ($count < 9 )
					continue;
				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				if($count< 18)
				{
					$report->content = "[新闻]".$content;
				}else if($count <27)
				{
					$report->content = "[公告]".$content;
				}else if($count < 36)
				{
					$report->content = "[活动]".$content;
				}
				array_push($newlist,$report);
			}
			
		}
	}
	
	
	return $newlist;
}

function net_getnews_MHZX($news_content,$mainlink_url)
{
	$newlist = array();

	$count = 0;
	while ($count < 3)
	{
		$count++;
		switch($count)
		{
			case 1:
			$mode = "{<div[^>]*id=\"divhid2([\\W|\\w]*)<div[^>]*id=\"divhid3}";
			break;
			case 2:
			$mode = "{<div[^>]*id=\"divhid3([\\W|\\w]*)<div[^>]*id=\"divhid4}";
			break;
			case 3:
			$mode = "{<div[^>]*id=\"divhid4([\\W|\\w]*)<div[^>]*class=\"search}";
			break;
		}
	if (preg_match($mode, $news_content, $news) == true) {
	
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*title[^>]*>([^>]{10,255})</a>}";

		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				switch($count)
				{
				case 1:
					$report->content = "[新闻]".$content;
				break;
				case 2:
					$report->content = "[公告]".$content;
				break;
				case 3:
					$report->content = "[维护]".$content;
				break;
				}
				array_push($newlist,$report);
			}
		}
	}
	}
	
	return $newlist;
}


function net_getnews_DMLQQQ($news_content,$mainlink_url)
{
	$newlist = array();

	$count = 0;
	while ($count < 3)
	{
		$count++;
		switch($count)
		{
			case 1:
			$mode = "{<h2[^>]*class=\"hidden\">公告([\\W|\\w]*)</ul}";
			break;
			case 2:
			$mode = "{<h2[^>]*class=\"hidden\">维护([\\W|\\w]*)</ul}";
			break;
			case 3:
			$mode = "{<h2[^>]*class=\"hidden\">活动([\\W|\\w]*)</ul}";
			break;
		}
	if (preg_match($mode, $news_content, $news) == true) {
		
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>\\s*<font\\s*color[^>]*>([^>]{10,255})</font>\\s*</a>}";

		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				switch($count)
				{
				case 1:
					$report->content = "[公告]".$content;
				break;
				case 2:
					$report->content = "[论坛]".$content;
				break;
				case 3:
					$report->content = "[活动]".$content;
				break;
				}
				array_push($newlist,$report);
			}
		}
	}
	}
	
	return $newlist;
}


function net_getnews_SANGUOQQ($news_content,$mainlink_url)
{
	$newlist = array();
	
	$mode = "{<ul[^>]*class=\"r_info_news_list([\\W|\\w]*)</ul}";

	if (preg_match($mode, $news_content, $news) == true) {
		//echo $news[0];
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>\\s*<font\\s*color[^>]*>([^>]{10,255})</font>\\s*</a>}";
	
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {

			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];

				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				$report->content = "[新闻]".$content;
				
				array_push($newlist,$report);
			}
			
		}
	}
	
	
	return $newlist;
}


function net_getnews_RXJH($news_content,$mainlink_url)
{
	$newlist = array();

	$count = 0;
	while ($count < 3)
	{
		$count++;
		switch($count)
		{
			case 1:
			$mode = "{<UL[^>]*id=\"div_xt([\\W|\\w]*)</UL}";
			break;
			case 2:
			$mode = "{<UL[^>]*id=\"div_hd\"([\\W|\\w]*)</UL}";
			break;
			case 3:
			$mode = "{<UL[^>]*id=\"div_bb\"([\\W|\\w]*)</UL}";
			break;
		}
	if (preg_match($mode, $news_content, $news) == true) {
		
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*title[^>]*>([^>]{10,255})</a>}";
		
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {
			
			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];
				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				switch($count)
				{
				case 1:
					$report->content = "[系统公告]".$content;
				break;
				case 2:
					$report->content = "[活动公告]".$content;
				break;
				case 3:
					$report->content = "[百宝阁]".$content;
				break;
				}
				array_push($newlist,$report);
			}
		}
	}
	}
	
	return $newlist;
}

function net_getnews_WD($news_content,$mainlink_url)
{
	$newlist = array();
	
	$mode = "{<!--游戏公告-->([\\W|\\w]*)<!--游戏公告-->}";

	if (preg_match($mode, $news_content, $news) == true) {
		//echo $news[0];
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>\\s*<font[^>]*>([^>]{10,255})</font>}";
	
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {

			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];

				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				$report->content = "[公告]".$content;
				
				array_push($newlist,$report);
			}
			
		}
	}
	
	
	return $newlist;
}

function net_getnews_TLBB($news_content,$mainlink_url)
{
	$newlist = array();
	
	$mode = "{tl.changyou.com/news/\"\\s*target=\"_blank\">更多([\\W|\\w]*)tl.changyou.com/hdlist}";

	if (preg_match($mode, $news_content, $news) == true) {
		//echo $news[0];
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>([^>]{10,255})</a>}";
	
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {

			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];

				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				$report->content = $content;
				
				array_push($newlist,$report);
			}
		}
	}
	return $newlist;
}


function net_getnews_CSONLINE($news_content,$mainlink_url)
{
	$newlist = array();
	
	$mode = "{<div\\s*class=\"mb25\">([\\W|\\w]*)<div\\s*align=\"center}";

	if (preg_match($mode, $news_content, $news) == true) {
		//echo $news[0];
		$mode = "{<a[^>]*href=\"([^\"]*)\"[^>]*>([^>]{10,255})</a>}";
	
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {

			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];

				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				$report->content = "[新闻]".$content;
				
				array_push($newlist,$report);
			}
		}
	}
	return $newlist;
}

function net_getnews_MJSG($news_content,$mainlink_url)
{
	$newlist = array();
	
	$mode = "{<ul[^>]*class=\"newslist\">([\\W|\\w]*)<ul[^>]*class=\"newslist\">}";

	if (preg_match($mode, $news_content, $news) == true) {
		
		$mode = "{<a[^>]*class[^>]*href=\'([^\']*)\'[^>]*>([^>]{10,255})</a>}";
	
		if (preg_match_all($mode, $news[0], $outsidelink_imgarr) == true) {

			foreach ($outsidelink_imgarr[0] as $i=> $singlenew) {
				//echo $outsidelink_imgarr[1][$i];
				//echo $outsidelink_imgarr[2][$i];

				$url = $outsidelink_imgarr[1][$i];
				$content = $outsidelink_imgarr[2][$i];
				if (strpos($url,"ttp://",0) == false)
				{
					$url = $mainlink_url.$url;	
				}
				$report = new Report();
				$report->url = $url;
					
				$content = "[官方]".$content;
							
				$report->content = $content;	
				
				array_push($newlist,$report);
			}
		
		}
	}
	return $newlist;
}

?>