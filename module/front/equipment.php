<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str=' and uid='.$_G['uid'].' and big_type=1';
if($_GET['act']=='learn'){
	if($_GET['formhash']==formhash()){
		$depot=C::t('#gfarm#gfarm_depot')->fetch($_GET['depotid']);	
		$equip=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);
		$str.=' and small_type='.$equip['small_type'].' and status=1';
		$equipment=C::t('#gfarm#gfarm_depot')->fetch_first_data($str);
		$depotlog=array(
			'uid'=>$_G['uid'],
			'type'=>5,
			'goods_id'=>$depot['goods_id'],
			'goods_number'=>1,
			'create_time'=>time(),
		);
		C::t('#gfarm#gfarm_depot_log')->insert($depotlog);
		if(!empty($equipment)){
			C::t('#gfarm#gfarm_depot')->update($equipment['id'],array(
				'status'=>0,
			));
		}	
		C::t('#gfarm#gfarm_depot')->update($depot['id'],array(
			'status'=>1,
		));	
		$mymainequip=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$depot['goods_id']);	
		include template('gfarm:ajax/equipmentajax');
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
		include template('gfarm:ajax/eforajax');
		exit;		
	}	
}
$ownmainequip=C::t('#gfarm#gfarm_depot')->fetch_first_data($str.' and status=1 and small_type=2');
$ownimportequip=C::t('#gfarm#gfarm_depot')->fetch_first_data($str.' and status=1 and small_type=3');
if(!empty($ownmainequip)){
	$mymainequip=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$ownmainequip['goods_id']);
}
if(!empty($ownimportequip)){
	$myimportequip=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$ownimportequip['goods_id']);
}
$equipments=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);
include template('gfarm:front/equipment');

?>