<?php
/**
 * Shaishai, the distributed microblog
 *
 * Apply a new web link of the game
 *
 * PHP version 5
 *
 * @category  Game
 * @package   Shaishai
 * @author    AGun Chan <agunchan@gmail.com>
 * @copyright 2009-2010 Shaier, Inc.
 * @link      http://www.shaishai.com/
 */

if (!defined('SHAISHAI')) {
    exit(1);
}

class GamewebnewAction extends GamebasicAction
{ 
    var $webname;
    var $website;
    var $webdetail;
    

    function handle($args)
    {
    	parent::handle($args);
		
		try {
			$this->saveNewMessage($args);
		} catch (Exception $e) {
			$testcac = new AjaxError();
			$testcac->showError($e->getMessage());
			return;
		}
    }
    
	function saveNewMessage($args)
	{
		$failed = $this->_validate();
		if (is_null($failed)) {
			$failed = Game_web::saveNew($this->webname, $this->website, $this->webdetail, $this->cur_game->id, $this->cur_user->id);
		}
		
		if ($this->boolean('ajax')) {
			if (is_null($failed)) {
        		$this->showJsonResult(array('result' => 'true'));
			} else {
				$this->showJsonResult(array('result' => $failed));
			}
		} else {
			$url = commoan_local_url('gamewebnav', array('gameid' => $this->cur_game->id));
			common_redirect($url, 303);
		}
	}
	
	function _validate()
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			$this->clientError('不接受非POST方法', 403);
			return;
		}
		
		$this->webname = $this->trimmed('webname');
		$this->website = $this->trimmed('website');
		$this->webdetail = $this->trimmed('webdetail');
		
		$gameweb = Game_web::staticGet('website', $this->website);
		if ($gameweb) {
			return '此站点已存在或正在审核中。';
		}
		
		if (mb_strlen($this->webdetail, 'utf-8') > 280) {
            return '描述太长, 最大为280个字。';
        }
        
        return null;
	}
}