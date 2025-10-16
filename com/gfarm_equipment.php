<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
if($componentInfo['flag']=='1'){
	$sql=<<<EOF
DELETE FROM `pre_gfarm_equipment` WHERE id<1000;
DELETE FROM `pre_gfarm_goods` WHERE id<1000 and (big_type=1 or big_type=4);
DELETE FROM `pre_gfarm_decorate` WHERE id<1000;
EOF;
	runquery($sql);
	$sql=lang('plugin/gfarm','install0');
	runquery($sql);
	$sql=lang('plugin/gfarm','install1');
	runquery($sql);
	$sql=lang('plugin/gfarm','install2');
	runquery($sql);
}else{
$sql=<<<EOF
DELETE FROM `pre_gfarm_equipment` WHERE id<5;
DELETE FROM `pre_gfarm_goods` WHERE id<241 and id>126;
DELETE FROM `pre_gfarm_decorate` WHERE id<23;
EOF;
runquery($sql);
	
}

?>