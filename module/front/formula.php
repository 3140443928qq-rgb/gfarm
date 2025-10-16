<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$formulas=C::t('#gfarm#gfarm_formula')->fetch_all_data();
foreach ($formulas as $key=>$value) {
	$userformula=C::t('#gfarm#gfarm_member_formula')->fetch_all_data(' and uid='.$_G['uid'].' and goods_type=1 and goods_id='.$value['id']);
	if(!empty($userformula)){
		$userformulas[$key]=$userformula;
	}
}
include template('gfarm:front/formula');

?>