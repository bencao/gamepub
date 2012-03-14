<?php

if (!defined('SHAISHAI')) {
    exit(1);
}

class GetjobsbygameAction extends ShaiAction
{
	function __construct() {
		parent::__construct();
		$this->no_anonymous = false;
	}
	
	function prepare($args) {
		if (! parent::prepare($args)) {return false;}
		$this->gid = $this->trimmed('gid');
		if (empty($this->gid)) {
			$this->clientError('请求缺少参数');
			return false;
		}
		return true;
	}
	
	function handle($args) {
		parent::handle($args);
		
		$jsonstringer = common_stream('games:jobs:' . $this->gid, array($this, "_getJsonStringer"), null, 24 * 3600);

		$jsonstringer->flush();
	}
	
	function _getJsonStringer() {
		$jsonstringer = new JsonStringer();
        $jsonstringer->init_document($this->paras);
        $game = Game::staticGet('id', $this->gid);
        $jsonstringer->show_json_objects(array('jobs' => $game->getJobs(), 'jobname' => $game->game_job_name, 'groupname' => $game->game_group_name));
        $jsonstringer->end_document();
        return $jsonstringer;
	}
	
}

?>