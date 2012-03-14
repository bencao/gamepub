<?php

require_once INSTALLDIR . '/plugins/feature/ifeature.php';

class Feature_Hooyou implements IFeature {
	var $clazz = 'hooyou';
	
	public function showHTML($out) {
		$out->raw('<div id="feature_new" cz="' . $this->clazz . '"><a href="#" class="close"></a><a href="/clients/hooyou" target="_blank" class="follow">查看详情</a></div>');
	}
	
	public function showStylesheets($out) {
		$out->cssLink('plugins/feature/css/hy.css');
	}
	
	public function showScripts($out) {
	}
}