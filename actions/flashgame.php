<?php
/**
 * Shaishai, the distributed microblog
 *
 * Get all flash game
 *
 * PHP version 5
 *
 * @category  FlashGame
 * @package   Shaishai
 * @author    AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

define('FLASH_PER_PAGE', 20);

class FlashgameAction extends ShaiAction
{
	var $cat_type = array('all'=>0, 'puzzle'=>1, 'act'=>2, 'shoot'=>3, 'fun'=>4, 'sport'=>5, 'chess'=>6);
	var $cat;
	var $which;		//hottest, latest
	var $offset;
    var $limit;

	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
		$this->cache_allowed = false;
	}
	
    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
        $this->cat = $this->trimmed('cat', 'all');
        $this->which = $this->trimmed('which', 'hottest');
		$this->offset = ($this->cur_page - 1) * FLASH_PER_PAGE;
		$this->limit = FLASH_PER_PAGE;
		
		$type = $this->cat_type[$this->cat];
		if ($this->which === 'hottest') {
			$flashes = Flash::getHottestFlash($type, $this->offset, $this->limit);
		} else {
			$flashes = Flash::getLatestFlash($type, $this->offset, $this->limit);
		}

        $this->addPassVariable('cat', $this->cat);
        $this->addPassVariable('which', $this->which);
        $this->addPassVariable('total', Flash::getFlashTotal($type));
		$this->addPassVariable('subs', $flashes);
		
    	$this->displayWith('FlashgameHTMLTemplate');
    }
}
