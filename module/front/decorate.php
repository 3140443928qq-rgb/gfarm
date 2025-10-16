<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str=' and uid='.$_G['uid'].' and big_type=4';
if($_GET['act']=='learn'){
	if($_GET['formhash']==formhash()){
		$depot=C::t('#gfarm#gfarm_depot')->fetch($_GET['depotid']);	
		$deco=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);
		$data=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$depot['goods_id']);
		$cdepot=C::t('#gfarm#gfarm_depot')->count_all_data($str.' and small_type='.$deco['small_type']);
		$str.=' and small_type='.$deco['small_type'].' and status=1';
		$decorate=C::t('#gfarm#gfarm_depot')->fetch_first_data($str);
		$depotlog=array(
			'uid'=>$_G['uid'],
			'type'=>5,
			'goods_id'=>$depot['goods_id'],
			'goods_number'=>1,
			'create_time'=>time(),
		);
		C::t('#gfarm#gfarm_depot_log')->insert($depotlog);
		if(!empty($decorate)){
			C::t('#gfarm#gfarm_depot')->update($decorate['id'],array(
				'status'=>0,
			));
		}		
		C::t('#gfarm#gfarm_depot')->update($depot['id'],array(
			'status'=>1,
		));		
		include template('gfarm:ajax/decorateajax');
		exit;		
	}	
}
if($_GET['act']=='forget'){
	if($_GET['formhash']==formhash()){
		$depot=C::t('#gfarm#gfarm_depot')->fetch($_GET['depotid']);	
		$deco=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);
		C::t('#gfarm#gfarm_depot')->update($_GET['depotid'],array(
			'status'=>0,
		));	
		include template('gfarm:ajax/decoforajax');
		exit;		
	}	
}
$gaoshi=C::t('#gfarm#gfarm_depot')->fetch_first_data($str.' and status=1 and small_type=5');
$dimao=C::t('#gfarm#gfarm_depot')->fetch_first_data($str.' and status=1 and small_type=6');
$wuzi=C::t('#gfarm#gfarm_depot')->fetch_first_data($str.' and status=1 and small_type=7');
$dog=C::t('#gfarm#gfarm_depot')->fetch_first_data($str.' and status=1 and small_type=8');
if(!empty($gaoshi)){
	$gaoshi1=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$gaoshi['goods_id']);
}
if(!empty($dimao)){
	$dimao1=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$dimao['goods_id']);
}
if(!empty($wuzi)){
	$wuzi1=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$wuzi['goods_id']);
}
if(!empty($dog)){
	$dog1=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$dog['goods_id']);
}
$decorates=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);
include template('gfarm:front/decorate');

?>