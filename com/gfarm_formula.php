<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
if($componentInfo['flag']=='1'){
	$sql=lang('plugin/gfarm','install6');
	runquery($sql);
	$sql=lang('plugin/gfarm','install7');
	runquery($sql);
}else{
$sql=<<<EOF
DELETE FROM `pre_gfarm_food` WHERE id<5;
DELETE FROM `pre_gfarm_goods` WHERE id<1045 and id>1040;
EOF;
runquery($sql);
	
}

?>