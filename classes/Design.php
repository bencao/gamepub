<?php
/**
 * Table Definition for design
 */
require_once INSTALLDIR.'/classes/Memcached_DataObject.php';

class Design extends Memcached_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'design';                          // table name
    public $id;                              // int(11)  not_null primary_key auto_increment
    public $backgroundcolor;                 // int(11)  
    public $contentcolor;                    // int(11)  
    public $sidebarcolor;                    // int(11)  
    public $textcolor;                       // int(11)  
    public $linkcolor;                       // int(11)  
    public $backgroundimage;                 // string(255)  binary
//    public $disposition;                     // int(4)
//    public $bgrepeat;
//    public $bgfix;  
	public $navcolor;                        // string(6)
    public $cssurl;                        // string(255)  binary
    public $name;

    /* Static get */
    function staticGet($k,$v=NULL) { return Memcached_DataObject::staticGet('Design',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    function defaultGameDesign($game) {
    	if ($game->design_id == NULL || $game->design_id == 0) {
    		return Design::_defaultDesign();
    	} else {
    		return Design::staticGet('id', $game->design_id);
    	}
    }
    
    function defaultGameDesignByGameId($game_id) {
    	$game = Game::staticGet('id', $game_id);
    	return self::defaultGameDesign($game);
    }
    
    function _defaultDesign() {
    	$defaults = common_config('site', 'design');

        $design = new Design();

        try {

            $color = new WebColor();

            $color->parseColor($defaults['backgroundcolor']);
            $design->backgroundcolor = $color->intValue();

            $color->parseColor($defaults['contentcolor']);
            $design->contentcolor = $color->intValue();

            $color->parseColor($defaults['sidebarcolor']);
            $design->sidebarcolor = $color->intValue();

            $color->parseColor($defaults['textcolor']);
            $design->textcolor = $color->intValue();

            $color->parseColor($defaults['linkcolor']);
            $design->linkcolor = $color->intValue();
            
            $color->parseColor($defaults['navcolor']);
            $design->navcolor = $color->hexValue();

            $design->backgroundimage = $defaults['backgroundimage'];

//            $design->disposition = $defaults['disposition'];
//            
//            $design->bgrepeat = 1;
//            
//            $design->bgfix = 1;

        } catch (WebColorException $e) {
            common_log(LOG_ERR, _('Bad default color settings: ' .
                $e->getMessage()));
        }
        
        return $design;
    }
    
    /**
     * 保存一个设计，包括其对应的css文件
     * @param $csspath
     * @param $props
     */
    function saveNew($csspath, $props = array()) {
    	extract($props);
    	
    	$cssContent = '';
    	
    	try {
            $color = new WebColor();

            $design = new Design();
	    	
	    	$color->parseColor($backgroundcolor);
	    	$design->backgroundcolor = $color->intValue();
	    	$cssContent .= 'body{background:#' . $color->hexValue();
    		
	    	if (! empty($backgroundimage)) {
	    		$design->backgroundimage = $backgroundimage;
	    		$cssContent .= " url('" . $design->backgroundimage . "')";
	    	} else {
	    		$cssContent .= " none";
	    	}
	        
	    	if ($bgrepeat) {
	    		$cssContent .= ' repeat';
	    	} else {
	    		$cssContent .= ' no-repeat';
	    	}
	    	if ($bgfix) {
	    		$cssContent .= ' fixed';
	    	} else {
	    		$cssContent .= ' scroll';
	    	}
	    	
//    		$design->disposition = $disposition;
	    	switch ($disposition) {
	    		case 1 :
	    	    default :
	    	    	$cssContent .= ' left top;}';
	    	        break;
	    		case 2 :
	    			$cssContent .= ' center top;}';
	    			break;
	    		case 3 :
	    			$cssContent .= ' right top;}';
	    			break;
	        }

    		
    		$color->parseColor($contentcolor);
    		$design->contentcolor = $color->intValue();
    		$cssContent .= '#notices, #owner_summary{color:#' . $color->hexValue() . '}';
    		
    		$color->parseColor($sidebarcolor);
    		$design->sidebarcolor = $color->intValue();
    		$cssContent .= '#wrap{background-color:#' . $color->hexValue() . ';}';
    		
    		// 计算右侧栏部分透明颜色 add by cao 2010-05-16
    		$sepcolor = Design::getCompositeColor('#' . $color->hexValue(), '#FFFFFF', 0.13);
    		$cssContent .= '#widgets div.split{border-top-color:#' . $sepcolor . ';}';
    		$cssContent .= '#widgets div.sub_info a:link,#widgets div.sub_info a:visited{border-left-color:#' . $sepcolor . ';}';
    		$cssContent .= '#widgets dl.grid-6 dd p.op {color:#' . $sepcolor . ';}';
    		
    		$navbgcolor = Design::getCompositeColor('#' . $color->hexValue(), '#000000', 0.1);
    		$cssContent .= '#w_nav li{border-bottom-color:#' . $sepcolor . ';background-color:#' . $navbgcolor . ';}';
    		
    		$grouplightcolor = Design::getCompositeColor('#' . $color->hexValue(), '#ffffff', 0.284);
    		$cssContent .= '#widgets div.group_info div.avatar{border-color:#' . $grouplightcolor .';}';
    		
    		
    		$groupheavycolor = Design::getCompositeColor('#' . $color->hexValue(), '#000000', 0.44);
    		$cssContent .= '#widgets div.group_info dl.detail dt a:link, #widgets div.group_info dl.detail dt a:visited{border-top-color:#' . $grouplightcolor .';border-bottom-color:#' . $groupheavycolor . '}';
    		
    		$groupmediumcolor = Design::getCompositeColor('#' . $color->hexValue(), '#000000', 0.22);
    		$cssContent .= '#widgets div.group_info dl.detail dt{border-color:#'. $groupmediumcolor . ';}';
    		// end add
    		
    		$color->parseColor($textcolor);
    		$design->textcolor = $color->intValue();
    		$cssContent .= '#widgets{color:#' . $color->hexValue() . '}';
    		
    		$color->parseColor($linkcolor);
    		$design->linkcolor = $color->intValue();
    		$cssContent .= '#owner_summary a, #notices a, #widgets div.sub_info a, #widgets dl.grid-6 a{color:#' . $color->hexValue() . '}';
    		
    		$color->parseColor('#' . $navcolor);
    		$design->navcolor = $color->hexValue();
    		$cssContent .= '#w_nav li.active span{background:url(/theme/default/i/b/' . strtolower($navcolor) . '.png)}';
    		$cssContent .= '#w_nav li.on span{background:url(/theme/default/i/b/' . strtolower($navcolor) . '_on.png)}';
	    	
	        $cssfilename = $csspath . time() . '.css';
	        
	    	$design->cssurl = common_path($cssfilename);
	    	
	    	$design->name = empty($name) ? '我的作品' : $name;
	    	
	    	$result = $design->insert();
	    	
	    	if (! $result) {
	    		common_log_db_error($design, 'INSERT', __FILE__);
	    	 	return false;
	    	}
	    	
        	$fp = fopen($cssfilename, 'w');
	        fwrite($fp, $cssContent);
	        fclose($fp);
	        
	    	return $design;
    	
    	} catch (WebColorException $e) {
            common_log(LOG_ERR, _('Bad default color settings: ' .
                $e->getMessage()));
        }
    	
        return false;
    }
    
    static function getCompositeColor($bgcolor, $fecolor, $opacity) {
    	$bwc = new WebColor();
    	$bwc->parseColor($bgcolor);

    	$fwc = new WebColor();
    	$fwc->parseColor($fecolor);
    	
    	$n_red = ($bwc->red) * (1 - $opacity) + ($fwc->red) * $opacity;
    	$n_green = ($bwc->green) * (1 - $opacity) + ($fwc->green) * $opacity;
    	$n_blue = ($bwc->blue) * (1 - $opacity) + ($fwc->blue) * $opacity;
    	
    	$hexcolor  = (strlen(dechex($n_red)) < 2 ? '0' : '' ) .
            dechex($n_red);
        $hexcolor .= (strlen(dechex($n_green)) < 2 ? '0' : '') .
            dechex($n_green);
        $hexcolor .= (strlen(dechex($n_blue)) < 2 ? '0' : '') .
            dechex($n_blue);
            
    	return $hexcolor;
    }
}
