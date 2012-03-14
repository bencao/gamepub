<?php
// this daemon service should be called every a minute.
define('SHAISHAI', true);
define('INSTALLDIR', realpath(dirname(__FILE__) . '/..'));

require INSTALLDIR . '/lib/environment.php';
require INSTALLDIR . '/lib/cache.php';
require INSTALLDIR . '/lib/common.php';
require INSTALLDIR . '/lib/router.php';

require_once INSTALLDIR . '/classes/Design.php';

$bgcolor = '#faf3ed';
$wrapbgcolor = '#edced9';

$cssContent = '#header_outter_wrap{opacity:0.8;filter:alpha(opacity=80);}';
$cssContent .= "\n";

$cssContent .= 'body{background:url(bg.jpg) repeat fixed center top;}';
$cssContent .= "\n";

$cssContent .= '#notice_form{background:url(nfbg.jpg);}';
$cssContent .= "\n";

$cssContent .= 'a.outbound{color:' . $wrapbgcolor . ';}';
$cssContent .= "\n";

$color = new WebColor();
$color->parseColor($wrapbgcolor);
$design->sidebarcolor = $color->intValue();
$cssContent .= '#wrap{background-color:#' . $color->hexValue() . ';}';
$cssContent .= "\n";
    		
// 计算右侧栏部分透明颜色 add by cao 2010-05-16
$sepcolor = Design::getCompositeColor('#' . $color->hexValue(), '#FFFFFF', 0.13);
$cssContent .= '#widgets div.split{border-top-color:#' . $sepcolor . ';}';
$cssContent .= '#widgets div.sub_info a:link,#widgets div.sub_info a:visited{border-left-color:#' . $sepcolor . ';}';
$cssContent .= '#widgets dl.grid-6 dd p.op{color:#' . $sepcolor . ';}';

$cssContent .= "\n";

$navbgcolor = Design::getCompositeColor('#' . $color->hexValue(), '#000000', 0.1);
$cssContent .= '#w_nav li{border-bottom-color:#' . $sepcolor . ';background-color:#' . $navbgcolor . ';}';

$cssContent .= "\n";

$grouplightcolor = Design::getCompositeColor('#' . $color->hexValue(), '#ffffff', 0.284);
$cssContent .= '#widgets div.group_info div.avatar{border-color:#' . $grouplightcolor .';}';
    		
$groupheavycolor = Design::getCompositeColor('#' . $color->hexValue(), '#000000', 0.44);
$cssContent .= '#widgets div.group_info dl.detail dt a:link, #widgets div.group_info dl.detail dt a:visited{border-top-color:#' . $grouplightcolor .';border-bottom-color:#' . $groupheavycolor . '}';
    		
$groupmediumcolor = Design::getCompositeColor('#' . $color->hexValue(), '#000000', 0.22);
$cssContent .= '#widgets div.group_info dl.detail dt{border-color:#'. $groupmediumcolor . ';}';

$cssContent .= "\n";



echo $cssContent;