<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$combin=C::t('#gfarm#gfarm_combin')->fetch($_G['uid']);
if($_GET['act']=='delete'){//取消材料
	C::t('#gfarm#gfarm_combin')->update($_G['uid'],array(
		'goods_id'.$_GET['position']=>0,
	));	
	exit;
}
if($_GET['act']=='formula'){//配方
	$formulas=C::t('#gfarm#gfarm_formula')->fetch_all_data();
	foreach ($formulas as $key=>$value) {
		$userformula=C::t('#gfarm#gfarm_member_formula')->fetch_all_data(' and uid='.$_G['uid'].' and goods_type=1 and goods_id='.$value['id']);
		if(!empty($userformula)){
			$userformulas[$key]=$userformula;
		}
	}
	include template('gfarm:ajax/mixformula');
	exit;
}
if($_GET['act']=='crop'){//材料
	$str=' and uid='.$_G['uid'].' and status=0 and small_type=12';
	$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);
	include template('gfarm:ajax/mixcrop');
	exit;
}
if($_GET['act']=='addform'){//添加配方
	$formulagood=C::t('#gfarm#gfarm_formula')->fetch($_GET['formid']);
	$strs=explode('/', $formulagood['good_ids']);
	foreach ($strs as $key=> $value) {
		$goods[$key]=C::t('#gfarm#gfarm_goods')->fetch($value);
		$depot[$value]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$value);
	}
	$good=C::t('#gfarm#gfarm_goods')->fetch($formulagood['success_id']);
	C::t('#gfarm#gfarm_combin')->update($_G['uid'],array(
		'goods_id1'=>$strs[0],
		'goods_id2'=>$strs[1],
		'goods_id3'=>$strs[2],
		'goods_id4'=>$strs[3],
	));
	include template('gfarm:ajax/mixformajax');
	exit;
}
if($_GET['act']=='react'){//合成
	$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$_G['uid'].' and status=0 and small_type=12');
	if($user['now_strength']<$setInfo['compose_power']){
		$mess=lang('plugin/gfarm','104');
		include template('gfarm:ajax/mixajax');
		exit;
	}
	if(empty($combin['goods_id1'])||empty($combin['goods_id2'])||empty($combin['goods_id3'])||empty($combin['goods_id4'])){
		$mess=lang('plugin/gfarm','112');
		include template('gfarm:ajax/mixajax');
		exit;
	}
	$depot[$combin['goods_id1']]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id1']);
	$dep[0]=C::t('#gfarm#gfarm_goods')->fetch($combin['goods_id1']);
	$depot[$combin['goods_id2']]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id2']);
	$dep[1]=C::t('#gfarm#gfarm_goods')->fetch($combin['goods_id2']);
	$depot[$combin['goods_id3']]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id3']);
	$dep[2]=C::t('#gfarm#gfarm_goods')->fetch($combin['goods_id3']);
	$depot[$combin['goods_id4']]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id4']);
	$dep[3]=C::t('#gfarm#gfarm_goods')->fetch($combin['goods_id4']);
	if($depot[$combin['goods_id1']]>0){
		$depot[$combin['goods_id1']]-=1;
	}else{
		$mess=$dep[0]['name'].lang('plugin/gfarm','090');
		include template('gfarm:ajax/mixajax');
		exit;
	}
	if($depot[$combin['goods_id2']]>0){
		$depot[$combin['goods_id2']]-=1;
	}else{
		$mess=$dep[1]['name'].lang('plugin/gfarm','090');
		include template('gfarm:ajax/mixajax');
		exit;
	}
	if($depot[$combin['goods_id3']]>0){
		$depot[$combin['goods_id3']]-=1;
	}else{
		$mess=$dep[2]['name'].lang('plugin/gfarm','090');
		include template('gfarm:ajax/mixajax');
		exit;
	}
	if($depot[$combin['goods_id4']]>0){
		$depot[$combin['goods_id4']]-=1;
	}else{
		$mess=$dep[3]['name'].lang('plugin/gfarm','090');
		include template('gfarm:ajax/mixajax');
		exit;
	}
	$formulas=C::t('#gfarm#gfarm_formula')->fetch_all_data();
	foreach ($formulas as $key1=>$value) {//获取匹配的配方
		$strs=explode('/', $value['good_ids']);
		foreach ($strs as $key=>$valu) {
			if($valu!=$dep[$key]['id']){
				$flag[$key1]=1;
				break;
			}
		}
		if(empty($flag[$key1])){
			$formula=$value;
			break;
		}
	}	
	if(!empty($formula)){//获取配方产物
		$rand1=rand(1, 100);
		//判断是否产出成功
		if($rand1<=$formula['success_odd']){
			$successflag=1;
			$prodid=$formula['success_id'];
		}else{
			$rand2=rand(1, 100);
			if($rand2<=$formula['fail_odd']){		
				$successflag=2;	
				$str1=explode('/', $formula['stuff_ids']);
				if(!empty($formula['stuff_odds'])){
					$str2=explode('/', $formula['stuff_odds']);
				}
				$max=0;
				foreach ($str1 as $key1=>$value) {
					$minprod[$value]=$max;
					if(empty($formula['stuff_odds'])){
						$max+=1;
					}else{
						$max+=$str2[$key1];
					}
					$maxprod[$value]=$max;
				}
				$rand=rand(1, $max);
				foreach ($maxprod as $key=>$value) {
					if($rand>$minprod[$key]&&$rand<=$value){
						$prodid=$key;
						break;
					}
				}
			}else{
				$successflag=3;
			}
		}
	}else{
		if(!empty($setInfo['formgood'])){
			$successflag=2;
			$prodid=$setInfo['formgood'];
		}else{
			$successflag=3;
		}
	}
	foreach ($dep as $key=>$value) {
		$depo[$key]=C::t('#gfarm#gfarm_depot')->fetch_first_data(' and uid='.$_G['uid'].' and goods_id='.$value['id'].' order by id desc');
		if($depo[$key]['number']-1<=0){
			C::t('#gfarm#gfarm_depot')->delete($depo[$key]['id']);
		}else{
			C::t('#gfarm#gfarm_depot')->update($depo[$key]['id'],array(
				'number'=>$depo[$key]['number']-1,
			));
		}
	}
	if(!empty($prodid)){
		$randnum=1;
		$good=C::t('#gfarm#gfarm_goods')->fetch($prodid);
		$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$prodid);
		//仓库堆叠
		adddepotfile($depots,$randnum,$good['depot_pile'],$prodid,$_G['uid']);
	}
	C::t('#gfarm#gfarm_formula_log')->insert(array(
		'uid'=>$_G['uid'],
		'type'=>1,
		'formula_id'=>$formula['id'],
		'formula_name'=>$formula['name'],
		'goods'=>$depo[0]['name'].'x1'.','.$depo[1]['name'].'x1'.','.$depo[2]['name'].'x1'.','.$depo[3]['name'].'x1',
		'produce'=>$good['name'].'x1',
		'create_time'=>time(),
	));
	C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
		'experience'=>$user['experience']+$setInfo['compose_exp'],
		'now_strength'=>$user['now_strength']-$setInfo['compose_power'],
		'last_visit'=>time(),
	));
	if(!empty($formula)){
		$userformula=C::t('#gfarm#gfarm_member_formula')->fetch_first_data(' and uid='.$_G['uid'].' and goods_type=1'.' and goods_id='.$formula['id']);	
		if(empty($userformula)){
			if($successflag=='1'){
				C::t('#gfarm#gfarm_member_formula')->insert(array(
					'uid'=>$_G['uid'],
					'goods_id'=>$formula['id'],
					'goods_type'=>1,
					'use_number'=>1,
					'create_time'=>time(),
					'use_first_time'=>time(),
					'update_time'=>time(),
				));
			}		
			$jiesuoflag=1;	
		}else{
			C::t('#gfarm#gfarm_member_formula')->update($userformula['id'],array(
				'use_number'=>$userformula['use_number']+1,
				'update_time'=>time(),
			));
		}		
	}	
	$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.($user['experience']+$setInfo['compose_exp']).' order by level desc');
	if($mylevel1!=$mylevel){
		$levelflag=1;
	}
	$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.($user['experience']+$setInfo['compose_exp']).' order by level');
	if(empty($nextlevel)){
		$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
	}
	$power=$user['now_strength']-$setInfo['compose_power'];
	$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$_G['uid'].' and status=0 and small_type=12');
	$depot1=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id1']);
	$depot2=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id2']);
	$depot3=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id3']);
	$depot4=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id4']);
	if(empty($depot1)){
		$depot1=0;
	}
	if(empty($depot2)){
		$depot2=0;
	}
	if(empty($depot3)){
		$depot3=0;
	}
	if(empty($depot4)){
		$depot4=0;
	}
	include template('gfarm:ajax/mixajax');
	exit;

}
if(!empty($combin['goods_id1'])){
	$goods['1']=C::t('#gfarm#gfarm_goods')->fetch($combin['goods_id1']);
}
if(!empty($combin['goods_id2'])){
	$goods['2']=C::t('#gfarm#gfarm_goods')->fetch($combin['goods_id2']);
}
if(!empty($combin['goods_id3'])){
	$goods['3']=C::t('#gfarm#gfarm_goods')->fetch($combin['goods_id3']);
}
if(!empty($combin['goods_id4'])){
	$goods['4']=C::t('#gfarm#gfarm_goods')->fetch($combin['goods_id4']);
}
if($_GET['act']=='ajax'){//添加材料
	$depot=C::t('#gfarm#gfarm_depot')->fetch($_GET['depotid']);
	$good=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);
	$pos=$_GET['depotposit'];
	C::t('#gfarm#gfarm_combin')->update($_G['uid'],array(
		'goods_id'.$_GET['depotposit']=>$depot['goods_id'],
	));	
	$depotnum=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$depot['goods_id']);
	$combin=C::t('#gfarm#gfarm_combin')->fetch($_G['uid']);
	if(!(empty($combin['goods_id1'])||empty($combin['goods_id2'])||empty($combin['goods_id3'])||empty($combin['goods_id4']))){
		$userformula=C::t('#gfarm#gfarm_member_formula')->fetch_all_data(' and uid='.$_G['uid'].' and goods_type=1');
		foreach ($userformula as $key1=>$value) {//获取匹配的配方
			$formul=C::t('#gfarm#gfarm_formula')->fetch_first_data(' and a.id='.$value['goods_id']);
			$strs=explode('/', $formul['good_ids']);
			foreach ($strs as $key=>$valu) {
				if($valu!=$combin['goods_id'.($key+1)]){
					$flag[$key1]=1;
					break;
				}
			}
			if(empty($flag[$key1])){
				$formula=$formul;
				break;
			}
		}
	}
	include template('gfarm:ajax/mixgoods');
	exit;
}
$depot[$combin['goods_id1']]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id1']);
$depot[$combin['goods_id2']]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id2']);
$depot[$combin['goods_id3']]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id3']);
$depot[$combin['goods_id4']]=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and uid='.$_G['uid'].' and goods_id='.$combin['goods_id4']);
if(!(empty($combin['goods_id1'])||empty($combin['goods_id2'])||empty($combin['goods_id3'])||empty($combin['goods_id4']))){
	$userformula=C::t('#gfarm#gfarm_member_formula')->fetch_all_data(' and uid='.$_G['uid'].' and goods_type=1');
	foreach ($userformula as $key1=>$value) {//获取匹配的配方
		$formul=C::t('#gfarm#gfarm_formula')->fetch_first_data(' and a.id='.$value['goods_id']);
		$strs=explode('/', $formul['good_ids']);
		foreach ($strs as $key=>$valu) {
			if($valu!=$combin['goods_id'.($key+1)]){
				$flag[$key1]=1;
				break;
			}
		}
		if(empty($flag[$key1])){
			$formula=$formul;
			break;
		}
	}
}
$str=' and uid='.$_G['uid'].' and status=0 and small_type=12';
$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);
$formulas=C::t('#gfarm#gfarm_formula')->fetch_all_data();
foreach ($formulas as $key=>$value) {
	$userformula=C::t('#gfarm#gfarm_member_formula')->fetch_all_data(' and uid='.$_G['uid'].' and goods_type=1 and goods_id='.$value['id']);
	if(!empty($userformula)){
		$userformulas[$key]=$userformula;
	}
}
include template('gfarm:front/mixture');

?>