<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require_once DISCUZ_ROOT.'./source/plugin/gfarm/class/Component.class.php';
$component=new Component();
if(file_exists('source/plugin/gfarm/module/cp/vip.php')){
	$vip=$component->find('vip');
	if(empty($vip)){
		$insertVip=array(
			'pname'=>'VIP',
			'model'=>'vip',
			'flag'=>'1',
			'createtime'=>TIMESTAMP,
		);
		$component->add($insertVip);
	}
}
if(file_exists('source/plugin/gfarm/com/gfarm_formula.php')){
	$sale=$component->find('formula');
	if(empty($sale)){
		$insertSale=array(
			'pname'=>lang('plugin/gfarm', '117'),
			'model'=>'formula',
			'flag'=>'1',
			'type'=>'2',
			'createtime'=>TIMESTAMP,
		);
		$component->add($insertSale);
	}
}
if(file_exists('source/plugin/gfarm/com/gfarm_equipment.php')){
	$sale=$component->find('equipment');
	if(empty($sale)){
		$insertSale=array(
			'pname'=>lang('plugin/gfarm', '118'),
			'model'=>'equipment',
			'flag'=>'1',
			'type'=>'2',
			'createtime'=>TIMESTAMP,
		);
		$component->add($insertSale);
	}
}
?>