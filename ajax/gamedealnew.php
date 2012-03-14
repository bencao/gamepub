<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GamedealnewAction extends ShaiAction
{
    function handle($args)
    {
		parent::handle($args);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        	if ($this->_validateForm()) {      		
        		$this->saveNewDeal($args);
        	} else {
        		$this->showError($this->errormessage);
        	}
        } else {
        	$this->showError('不接受非POST的请求');
        }
    }
    
    function saveNewDeal($args)
    {
    	$gameserver = Game_server::staticGet('id',$this->game_server_id);
        $gamebigzone = $gameserver->getGameBigZone();
        $game = $gamebigzone->getGame();
        
        $options = array('price' => $this->price, 'expire_time' => $this->expire_time, 'description' => $this->description);
       	$deal_id = Deal::saveNew($this->cur_user->id,
       		$game->id,
       		$gamebigzone->id,
       		$gameserver->id,
       		$this->deal_tag,
       		$options);
        
       	// 移动文件至目标路径
       	$picfilename = substr($this->picfilepath, strrpos($this->picfilepath, '/') + 1);
       	$targetPath = Avatar::dealsubpath($this->cur_user->id);
		$fromPath = Avatar::tmpsubpath($this->cur_user->id);
		rename($fromPath . $picfilename, $targetPath . $picfilename);
       	$deal_image = Deal_images::saveNew($deal_id, common_path($targetPath . $picfilename));
       	
       	Notice::saveNew($this->cur_user->id, 
       		'我发布了一个新商品：' . $this->description . '。数量有限，机不可失失不再来哦！' . common_path('game/' . $game->id . '/deal?game_server=' . $gameserver->id . '&q=' . $this->cur_user->nickname), 
        	'', 'web', true);
       	
		$this->showJsonResult(array('result' => 'true'));
    }
    
	function showError($error=null) {		
    	$datas = array('result' => 'false', 'msg' => $error);
    	$this->showJsonResult($datas);
    }
    
    function _validateForm() 
    {
        $this->deal_tag = $this->trimmed('deal_tag');
    	if (empty($this->deal_tag)) {
           $this->errormessage='您没有选择商品的类型信息';
           return false;
        }
        $this->game_server_id = $this->trimmed('game_server');
    	if (empty($this->game_server_id)) {
           $this->errormessage='您没有选择商品的游戏服务器信息';
           return false;
        }
        $this->price = $this->trimmed('price');
    	if (empty($this->price)) {
           $this->errormessage='您没有输入商品的价格';
           return false;
        }
    	if (! is_numeric($this->price)) {
           $this->errormessage = '商品价格必须为数字';
           return false;
        }
        
  		$this->expire_time = $this->trimmed('expire_time');
    	if (empty($this->expire_time) || ! is_numeric($this->expire_time)) {
           $this->errormessage='您没有输入过期时间';
           return false;
        }
	
        $this->description = $this->trimmed('description');
        if (empty($this->description)) {
           $this->errormessage='您没有输入商品描述';
           return false;
        }

        if (mb_strlen($this->description, 'utf-8') > 280) {
            $this->errormessage='商品描述太长, 最大为280个字。';
            return false;
        }
        
    	$this->picfilepath = $this->trimmed('picfilename');
		if (! preg_match('/.*\.(jpg|png|gif|jpeg)/i', $this->picfilepath)) {
        	$this->errormessage = '截图文件不符合要求';
        	return false;
        }
        
        return true;
    }
    
}