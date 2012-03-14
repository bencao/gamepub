<?php
/**
 * Shaishai, the distributed microblog
 *
 * Get my flash game
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

class FlashmineAction extends ShaiAction
{
	var $offset;
    var $limit;

    function isReadOnly($args)
    {
        return true;
    }

    function handle($args)
    {
        parent::handle($args);
        
		$this->offset = ($this->cur_page - 1) * FLASH_PER_PAGE;
		$this->limit = FLASH_PER_PAGE;
		
		$flashes = Flash::getUserFlash(0, $this->offset, $this->limit, $this->cur_user->id);

		$this->addPassVariable('cat', 'none');
		$this->addPassVariable('which', 'lattest');
        $this->addPassVariable('total', Flash::getFlashTotal(0, $this->cur_user->id));
		$this->addPassVariable('subs', $flashes);
		
    	$this->displayWith('FlashmineHTMLTemplate');
    }
}
