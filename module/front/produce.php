<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
if($_GET['act']=='react'){//加工
	if($_GET['formhash']==formhash()){
		if($user['now_strength']<$setInfo['process_power']){
			$mess=lang('plugin/gfarm','104');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		if(empty($_GET['formid'])){
			$mess=lang('plugin/gfarm','121');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		$formulagood=C::t('#gfarm#gfarm_formula_goods')->fetch($_GET['formid']);
		$strs=explode('/', $formulagood['good_ids']);
		$goodnums=explode('/', $formulagood['good_numbers']);
		if(count($goodnums)==1){
			$goodnums[0]=$goodnums[0];
			$goodnums[1]=$goodnums[0];
			$goodnums[2]=$goodnums[0];
			$goodnums[3]=$goodnums[0];
		}
		foreach ($strs as $key=>$value) {
			$depots[$value]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$value);
			$goodname[$key]=C::t('#gfarm#gfarm_goods')->fetch($value);
		}
		foreach ($strs as $key=>$value) {
			$depots[$value]-=$goodnums[$key];
		}
		foreach ($depots as $key=>$value) {
			if($value<0){
				$good=C::t('#gfarm#gfarm_goods')->fetch($key);
				$mess=$good['name'].lang('plugin/gfarm','090');
				include template('gfarm:ajax/commonajax');
				exit;
			}
		}
		$rand=rand($formulagood['produce_small'], $formulagood['produce_big']);
		$good=C::t('#gfarm#gfarm_goods')->fetch($formulagood['produce_id']);
		$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$formulagood['produce_id']);
		foreach ($strs as $key=>$value) {
			$dep=C::t('#gfarm#gfarm_depot')->fetch_first_data(' and uid='.$_G['uid'].' and goods_id='.$value.' order by id desc');
			deledepot($dep,$goodnums[$key]);
		}
		//仓库堆叠
		C::t('#gfarm#gfarm_formula_log')->insert(array(
		'uid'=>$_G['uid'],
		'type'=>2,
		'formula_id'=>$formulagood['id'],
		'formula_name'=>$formulagood['name'],
		'goods'=>$goodname[0]['name'].'x'.$goodnums[0].','.$goodname[1]['name'].'x'.$goodnums[1].','.$goodname[2]['name'].'x'.$goodnums[2].','.$goodname[3]['name'].'x'.$goodnums[3],
		'produce'=>$good['name'].'x'.$rand,
		'create_time'=>time(),
		));
		C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
		'experience'=>$user['experience']+$setInfo['process_exp'],
		'now_strength'=>$user['now_strength']-$setInfo['process_power'],
		'last_visit'=>time(),
		));
		adddepotfile($depots,$rand,$good['depot_pile'],$formulagood['produce_id'],$_G['uid']);	
		$userformula=C::t('#gfarm#gfarm_member_formula')->fetch_first_data(' and uid='.$_G['uid'].' and goods_type=2 and goods_id='.$formulagood['id']);
		if(empty($userformula)){
			C::t('#gfarm#gfarm_member_formula')->insert(array(
			'uid'=>$_G['uid'],
			'goods_id'=>$formulagood['id'],
			'goods_type'=>2,
			'use_number'=>1,
			'create_time'=>time(),
			'use_first_time'=>time(),
			'update_time'=>time(),
			));
		}else{
			C::t('#gfarm#gfarm_member_formula')->update($userformula['id'],array(
			'use_number'=>$userformula['use_number']+1,
			'update_time'=>time(),
			));
		}		
		$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.($user['experience']+$setInfo['process_exp']).' order by level desc');
		if($mylevel1!=$mylevel){
			$levelflag=1;
		}
		$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.($user['experience']+$setInfo['process_exp']).' order by level');
		if(empty($nextlevel)){
			$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
		}
		$power=$user['now_strength']-$setInfo['process_power'];
		$strs=explode('/', $formulagood['good_ids']);
		foreach ($strs as $key=>$value) {
			$depots[$key]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$value);
			if(empty($depots[$key])){
				$depots[$key]=0;
			}
		}
		$mylog=C::t('#gfarm#gfarm_formula_log')->fetch_first_data(' and type=2 and uid='.$_G['uid'].' order by create_time desc');
		$spstr=explode('x', $mylog['produce']);
		$crop=C::t('#gfarm#gfarm_goods')->fetch($mylog['produce_id']);
		include template('gfarm:ajax/proajax');
		exit;
	}
}
if($_GET['act']=='ajax'){//添加配方
	$formulagood=C::t('#gfarm#gfarm_formula_goods')->fetch($_GET['formid']);
	$strs=explode('/', $formulagood['good_ids']);
	$goodnums=explode('/', $formulagood['good_numbers']);
	if(count($goodnums)==1){
		$goodnums[0]=$goodnums[0];
		$goodnums[1]=$goodnums[0];
		$goodnums[2]=$goodnums[0];
		$goodnums[3]=$goodnums[0];
	}
	$allcount=0;
	foreach ($strs as $key=> $value) {
		$goods[$key]=C::t('#gfarm#gfarm_goods')->fetch($value);
		$depot[$value]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$value);
		$allcount+=$goods[$key]['sale_price']*$goodnums[$key];
	}
	$good=C::t('#gfarm#gfarm_goods')->fetch($formulagood['produce_id']);
	$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($good['big_type']);
	if(!empty($good['small_type'])){
		$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($good['small_type']);
	}
	include template('gfarm:ajax/produceajax');
	exit;
}
$mylog=C::t('#gfarm#gfarm_formula_log')->fetch_first_data(' and type=2 and uid='.$_G['uid'].' order by create_time desc');
$spstr=explode('x', $mylog['produce']);
$formulagoods=C::t('#gfarm#gfarm_formula_goods')->fetch_all_data(' and a.use_level<='.$mylevel['level']);
include template('gfarm:front/produce');

?>