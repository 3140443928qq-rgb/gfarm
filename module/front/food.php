<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$foodInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and big_type=14 and uid='.$_G['uid']);
foreach ($foodInfos as $key=>$value) {
	$foods[$key]=C::t('#gfarm#gfarm_food')->fetch_first_data(' and relation_id='.$value['goods_id']);
}
if($_GET['act']=='eat'){//食用
	if($_GET['formhash']==formhash()){
		$foodid=$_GET['foodid'];
		$depot=C::t('#gfarm#gfarm_depot')->fetch_first_data(' and a.id='.$foodid.' and uid='.$_G['uid']);
		if(empty($depot)){
			$mess=lang('plugin/gfarm','146');
			include template('gfarm:ajax/foodajax');
			exit;
		}
		$food=C::t('#gfarm#gfarm_food')->fetch_first_data(' and relation_id='.$depot['goods_id']);
		if(empty($food)){
			$mess=lang('plugin/gfarm','147');
			include template('gfarm:ajax/foodajax');
			exit;
		}
		$depotlog=array(
			'uid'=>$_G['uid'],
			'type'=>5,
			'goods_id'=>$depot['goods_id'],
			'goods_number'=>1,
			'create_time'=>time(),
		);
		C::t('#gfarm#gfarm_depot_log')->insert($depotlog);
		if($depot['number']>1){
			C::t('#gfarm#gfarm_depot')->update($depot['id'],array(
				'number'=>$depot['number']-1,
				'update_time'=>time(),
			));
		}else{
			C::t('#gfarm#gfarm_depot')->delete($depot['id']);
		}
		$userarr=array(
			'experience'=>$user['experience']+$food['addexp'],
			'benevolence'=>$user['benevolence']+$food['addbene'],
			'now_strength'=>$user['now_strength']+$food['addstren'],
			'last_visit'=>time(),
		);
		c::t('#gfarm#gfarm_member')->update($_G['uid'],$userarr);
		$foodInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and big_type=14 and uid='.$_G['uid']);
		foreach ($foodInfos as $key=>$value) {
			$foods[$key]=C::t('#gfarm#gfarm_food')->fetch_first_data(' and relation_id='.$value['goods_id']);
		}
		$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$userarr['experience'].' order by level desc');
		if($mylevel1!=$mylevel){
			$levelflag=1;
		}
		$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$userarr['experience'].' order by level');
		if(empty($nextlevel)){
			$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
		}
		$power=$userarr['now_strength'];
		include template('gfarm:ajax/foodajax');
		exit;
	}
}
include template('gfarm:front/food');

?>