<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str=' and uid='.$_G['uid'];
if($_GET['act']=='landajax'){//解锁
	if($_GET['formhash']==formhash()){
		$landid=$_GET['landid'];
		if(!empty($mylevel)){
			if($countland>=$mylevel['land_number']){
				$mess=lang('plugin/gfarm','103');
				include template('gfarm:ajax/commonajax');
				exit;
			}
		}
		$countland1=C::t('#gfarm#gfarm_member_land')->fetch_first_data(' and uid='.$_G['uid'].' and land_id='.$landid);
		if(!empty($countland1)){
			$mess=lang('plugin/gfarm','120');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		if($user['now_strength']<$setInfo['plant_power']){
			$mess=lang('plugin/gfarm','104');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		if(!empty($lock)){
			if(!empty($myvip)){
				if(!empty($myvip['relockmoney'])){
					$lock['money']=ceil($lock['money']*(1-$myvip['relockmoney']*0.01));
				}
			}
			if(empty($lock['money_type'])){
				if($lock['money']>$user['money']){
					$mess=$config['moneyname'].lang('plugin/gfarm','101');
					include template('gfarm:ajax/commonajax');
					exit;
				}
			}else{
				if($lock['money']>$user_credit){
					$mess=$config['bullionsname'].lang('plugin/gfarm','101');
					include template('gfarm:ajax/commonajax');
					exit;
				}
			}
		}
		$landarr=array(
			'uid'=>$_G['uid'],
			'lock_time'=>time(),
			'land_id'=>$_GET['landid'],
		);
		$landlogarr=array(
			'uid'=>$_G['uid'],
			'type'=>1,
			'target_land'=>$_GET['landid'],
			'create_time'=>time(),
		);
		jiesuofunc($landarr, $landlogarr);
		$locklogarr=array(
			'uid'=>$_G['uid'],
			'land_num'=>$countland+1,
			'create_time'=>time(),
		);
		if(!empty($lock)){
			$locklogarr['money_type']=$lock['money_type'];
			$locklogarr['money']=$lock['money'];
		}	
		C::t('#gfarm#gfarm_lock_log')->insert($locklogarr);
		if(!empty($lock)){
			if(empty($lock['money_type'])){
				C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
					'money'=>$user['money']-$lock['money'],
					'last_visit'=>time(),
				));	
				$user=C::t('#gfarm#gfarm_member')->fetch_by_uid($_G['uid']);
			}else{
				C::t("common_member_count")->increase($_G['uid'],array('extcredits'.$config['bullionstype']=>-$lock['money']));
				$user_credit = DB::result_first($sql,array('common_member_count',$_G['uid']));
			}
		}		
		$lock=C::t('#gfarm#gfarm_lock')->fetch_first_data(' and visible=0 and land_num='.($countland+2));
		include template('gfarm:ajax/jiesuoajax');
		exit;
	}
}
if($_GET['act']=='germajax'){//种植
	if($_GET['formhash']==formhash()){
		if($user['now_strength']<$setInfo['plant_power']){
			$mess=lang('plugin/gfarm','104');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		$depot=C::t('#gfarm#gfarm_depot')->fetch($_GET['depotid']);	
		$currentland=C::t('#gfarm#gfarm_member_land')->fetch_first_data($str.' and land_id='.$_GET['landid']);	
		if($currentland['germ_id']){
			exit;
		}
		$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$depot['goods_id']);		
		$crops=C::t('#gfarm#gfarm_germ_crop')->fetch_all_data(' and germ_id='.$depot['goods_id']);
		$max=0;
		foreach ($crops as $key=> $value) {
			$mincrop[$value['crop_id']]=$max;
			$max+=$value['produce_odds'];
			$maxcrop[$value['crop_id']]=$max;
		}
		$rand=rand(1, $max);
		foreach ($maxcrop as $key=>$value) {
			if($rand>$mincrop[$key]&&$rand<=$value){
				$cropid=$key;
				break;
			}
		}
		$germcrop=C::t('#gfarm#gfarm_germ_crop')->fetch_first_data(' and germ_id='.$depot['goods_id'].' and crop_id='.$cropid);
		$userarr=array(
			'experience'=>$user['experience']+$setInfo['plant_exp'],
			'now_strength'=>$user['now_strength']-$setInfo['plant_power'],
			'last_visit'=>time(),
		);
		$num=rand($germcrop['produce_small'], $germcrop['produce_big'])*(1+$currentland['add_gain_number']*0.01);
		$germtime=$germ['mature_time']*60;
		if($cequipment==1){
			$decorates=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$_G['uid'].' and big_type=4 and status=1');
			$addnum=0;
			$addtime=0;
			foreach ($decorates as $value) {
				$decorate=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$value['goods_id']);
				$addnum+=$decorate['add_gain_number'];
				$addtime+=$decorate['add_gain_time'];
			}
			$num=$num*(1+$addnum*0.01);
			$germtime=$germtime*(1+$addtime*0.01);
		}
		$snum=ceil($num*$setInfo['steal_rate']*0.01);
		if($snum<$setInfo['steal_number']){
			$snum=$setInfo['steal_number'];
		}
		$landarr=array(
			'germ_id'=>$depot['goods_id'],
			'crop_id'=>$cropid,
			'germ_time'=>time(),
			'gain_time'=>time()+$germtime,
			'gain_number'=>$num,
			'steal_number'=>$snum,
		);		
		$landlogarr=array(
			'uid'=>$_G['uid'],
			'type'=>2,
			'target_uid'=>$_G['uid'],
			'target_land'=>$currentland['id'],
			'good_id'=>$depot['goods_id'],
			'good_name'=>$germ['name'],
			'create_time'=>time(),
		);
		landfunc($currentland['id'], $userarr, $landarr, $landlogarr,$_G['uid']);
		$landalllogarr=array(
			'uid'=>$_G['uid'],
			'type'=>1,
			'goods_id'=>$depot['goods_id'],
			'create_time'=>time(),
		);
		$landalllog=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=1 and uid='.$_G['uid'].' and goods_id='.$depot['goods_id']);
		if(empty($landalllog)){
			$landalllogarr['number']=1;
			C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr);
		}else{
			$landalllogarr['number']=$landalllog['number']+1;
			C::t('#gfarm#gfarm_land_all_log')->update($landalllog['id'],$landalllogarr);
		}
		if($depot['number']-1>0){
			C::t('#gfarm#gfarm_depot')->update($depot['id'],array(
				'number'=>$depot['number']-1,
			));
		}else{
			C::t('#gfarm#gfarm_depot')->delete($depot['id']);
			$nogerm=1;
		}	
		$mypicture=C::t('#gfarm#gfarm_member_picture')->fetch_all_data(' and uid='.$_G['uid'].' and crop_id='.$depot['goods_id']);
		if(empty($mypicture)){
			C::t('#gfarm#gfarm_member_picture')->insert(array(
				'uid'=>$_G['uid'],
				'crop_id'=>$depot['goods_id'],
				'create_time'=>time(),
			));
		}	
		$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$userarr['experience'].' order by level desc');
		if($mylevel1!=$mylevel){
			$levelflag=1;
		}
		$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$userarr['experience'].' order by level');
		if(empty($nextlevel)){
			$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
		}
		$power=$user['now_strength']-$setInfo['plant_power'];
		include template('gfarm:ajax/zhongzhiajax');
		exit;
	}	
}
if($_GET['act']=='itemajax'){//道具
	$str=' and uid='.$uid;
	if($_GET['formhash']==formhash()){		
		$currentland=C::t('#gfarm#gfarm_member_land')->fetch_first_data($str.' and land_id='.$_GET['landid']);	
		if($currentland['gain_time']<=time()){
			$mess=lang('plugin/gfarm','105');
			include template('gfarm:ajax/itemajax');
			exit;
		}
		$depot=C::t('#gfarm#gfarm_depot')->fetch($_GET['depotid']);	
		$item=C::t('#gfarm#gfarm_items')->fetch_first_data(' and relation_id='.$depot['goods_id']);		
		if($item['use_target']==2&&$uid==$_G['uid']||$item['use_target']==1&&$uid!=$_G['uid']){
			$mess=lang('plugin/gfarm','106');
			include template('gfarm:ajax/itemajax');
			exit;
		}
		$operatland=C::t('#gfarm#gfarm_operat_land')->fetch_first_data(' and uid='.$_G['uid'].' and land_id='.$currentland['id']);
		if(empty($operatland)){
			C::t('#gfarm#gfarm_operat_land')->insert(array(
				'uid'=>$_G['uid'],
				'land_id'=>$currentland['id'],
			));
			$operatland=C::t('#gfarm#gfarm_operat_land')->fetch_first_data(' and uid='.$_G['uid'].' and land_id='.$currentland['id']);
		}
		if($operatland['item_time']>=$currentland['germ_time']){
			$mess=lang('plugin/gfarm','107');
			include template('gfarm:ajax/itemajax');
			exit;
		}
		$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$currentland['germ_id']);	
		$crop=C::t('#gfarm#gfarm_goods')->fetch($currentland['crop_id']);	
		if($item['add_gain_time']<0){
			$time1=ceil($currentland['germ_time']+($currentland['gain_time']-$currentland['germ_time'])*(1+$item['add_gain_time']*0.01));
		}else{
			$time1=floor($currentland['germ_time']+($currentland['gain_time']-$currentland['germ_time'])*(1+$item['add_gain_time']*0.01));
		}		
		if($time1<=time()){
			$cropflag=1;
			$time1=time();
		}elseif($currentland['germ_time']+($time1-$currentland['germ_time'])/3<=time()){
			$cropflag=2;
		}
		if($item['add_gain_number']<0){
			$num1=ceil($currentland['gain_number']*(1+$item['add_gain_number']*0.01));
		}else{
			$num1=floor($currentland['gain_number']*(1+$item['add_gain_number']*0.01));
		}		
		$userarr=array(
			'benevolence'=>$user['benevolence']+$item['add_benevolence'],
			'last_visit'=>time(),
		);
		$snum=ceil($num1*$setInfo['steal_rate']*0.01);
		if($snum<5){
			$snum=5;
		}
		$landarr=array(
			'gain_time'=>$time1,
			'gain_number'=>$num1,
			'steal_number'=>$snum,
			'update_time'=>time(),
		);
		$landlogarr=array(
			'uid'=>$_G['uid'],
			'type'=>5,
			'target_uid'=>$uid,
			'target_land'=>$currentland['id'],
			'good_id'=>$item['relation_id'],
			'good_name'=>$item['name'],
			'germ_id'=>$germ['relation_id'],
			'germ_name'=>$germ['name'],
			'add_gain_number'=>$item['add_gain_number'],
			'add_gain_time'=>$item['add_gain_time'],
			'create_time'=>time(),
		);
		landfunc($currentland['id'], $userarr, $landarr, $landlogarr,$_G['uid']);
		if($depot['number']-1>0){
			C::t('#gfarm#gfarm_depot')->update($depot['id'],array(
				'number'=>$depot['number']-1,
			));
		}else{
			C::t('#gfarm#gfarm_depot')->delete($depot['id']);
			$noitem=1;
		}		
		C::t('#gfarm#gfarm_operat_land')->update($operatland['id'],array(
			'item_time'=>time(),
		));
		include template('gfarm:ajax/itemajax');
		exit;
	}	
}
if($_GET['act']=='delete'){//铲除植物
	if($_GET['formhash']==formhash()){
		if($user['now_strength']<$setInfo['uproot_power']){
			$mess=lang('plugin/gfarm','104');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		$currentland=C::t('#gfarm#gfarm_member_land')->fetch_first_data($str.' and land_id='.$_GET['landid']);
		$germ=C::t('#gfarm#gfarm_goods')->fetch($currentland['germ_id']);
		$userarr=array(
			'experience'=>$user['experience']+$setInfo['uproot_exp'],
			'now_strength'=>$user['now_strength']-$setInfo['uproot_power'],
			'last_visit'=>time(),
		);
		$landarr=array(
			'germ_id'=>0,
			'crop_id'=>0,
			'land_number'=>$currentland['land_number']+1,
		);
		$landlogarr=array(
			'uid'=>$_G['uid'],
			'type'=>4,
			'target_uid'=>$_G['uid'],
			'target_land'=>$currentland['id'],
			'germ_id'=>$currentland['germ_id'],
			'germ_name'=>$germ['name'],
			'create_time'=>time(),
		);
		landfunc($currentland['id'], $userarr, $landarr, $landlogarr,$_G['uid']);
		$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$userarr['experience'].' order by level desc');
		if($mylevel1!=$mylevel){
			$levelflag=1;
		}
		$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$userarr['experience'].' order by level');
		if(empty($nextlevel)){
			$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
		}
		$power=$user['now_strength']-$setInfo['uproot_power'];
		include template('gfarm:ajax/deleteajax');
		exit;
	}	
}
if($_GET['act']=='seedetail'){//查看农田信息
	$currentland=C::t('#gfarm#gfarm_member_land')->fetch_first_data($str.' and land_id='.$_GET['landid']);
	$good=C::t('#gfarm#gfarm_goods')->fetch($currentland['crop_id']);
	include template('gfarm:ajax/detailajax');
	exit;
}
if($_GET['act']=='getcrop'){//收获
	if($_GET['formhash']==formhash()){
		if($user['now_strength']<$setInfo['crop_power']){
			$mess=lang('plugin/gfarm','104');
			include template('gfarm:ajax/commonajax');
			exit;
		}		
		$currentland=C::t('#gfarm#gfarm_member_land')->fetch_first_data($str.' and land_id='.$_GET['landid']);		
		$rand=$currentland['gain_number'];
		$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$currentland['germ_id']);
		$crop=C::t('#gfarm#gfarm_goods')->fetch($currentland['crop_id']);
		$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data($str.' and status=0 and goods_id='.$currentland['crop_id']);
		//仓库堆叠	
		adddepotfile($depots,$rand,$crop['depot_pile'],$currentland['crop_id'],$_G['uid']);
		$userarr=array(
			'experience'=>$user['experience']+$setInfo['crop_exp']+$germ['experience'],
			'now_strength'=>$user['now_strength']-$setInfo['crop_power'],
			'last_visit'=>time(),
		);
		$landarr=array(
			'germ_id'=>0,
			'crop_id'=>0,
			'land_number'=>$currentland['land_number']+1,
			'gain_number'=>0,
		);
		$landlogarr=array(
			'uid'=>$_G['uid'],
			'type'=>3,
			'target_uid'=>$_G['uid'],
			'target_land'=>$currentland['id'],
			'good_id'=>$currentland['crop_id'],
			'good_name'=>$crop['name'],
			'good_number'=>$currentland['gain_number'],
			'create_time'=>time(),
		);
		landfunc($currentland['id'], $userarr, $landarr, $landlogarr,$_G['uid']);
		$landalllogarr=array(
			'uid'=>$_G['uid'],
			'type'=>2,
			'goods_id'=>$currentland['crop_id'],
			'create_time'=>time(),
		);		
		$landalllog=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=2 and uid='.$_G['uid'].' and goods_id='.$currentland['crop_id']);
		if(empty($landalllog)){
			$landalllogarr['number']=1;
			C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr);
		}else{
			$landalllogarr['number']=$landalllog['number']+1;
			C::t('#gfarm#gfarm_land_all_log')->update($landalllog['id'],$landalllogarr);
		}
		$landalllogarr1=array(
			'uid'=>$_G['uid'],
			'type'=>3,
			'goods_id'=>$currentland['crop_id'],
			'create_time'=>time(),
		);
		$landalllog1=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=3 and uid='.$_G['uid'].' and goods_id='.$currentland['crop_id']);
		if(empty($landalllog1)){
			$landalllogarr1['number']=$currentland['gain_number'];
			C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr1);
		}else{
			$landalllogarr1['number']=$landalllog1['number']+$currentland['gain_number'];
			C::t('#gfarm#gfarm_land_all_log')->update($landalllog1['id'],$landalllogarr1);
		}
		$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$userarr['experience'].' order by level desc');
		if($mylevel1!=$mylevel){
			$levelflag=1;
		}
		$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$userarr['experience'].' order by level');
		if(empty($nextlevel)){
			$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
		}
		$power=$user['now_strength']-$setInfo['crop_power'];
		include template('gfarm:ajax/shouhuoajax');
		exit;
	}	
}
if($_GET['act']=='allgot'){//一键收获
	if($_GET['formhash']==formhash()){	
		if($myvip['openget']!='1'){
			exit;
		}		
		if($user['now_strength']<$setInfo['crop_power']){
			$mess=lang('plugin/gfarm','104');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		$lands=C::t('#gfarm#gfarm_member_land')->fetch_all_data($str.' and germ_id!=0 and gain_time<='.time());
		if(!empty($setInfo['crop_power'])){
			$landlength=floor($user['now_strength']/$setInfo['crop_power']);
			if($landlength>count($lands)){
				$landlength=count($lands);
			}
		}else{
			$landlength=count($lands);
		}
		
		foreach ($lands as $key=>$value) {		
			if($key>$landlength-1){
				break;
			}	
			$currentland=$value;
			$rand[$key]=$currentland['gain_number'];
			if(!empty($rand[$key])){			
				$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$currentland['germ_id']);
				$crop[$key]=C::t('#gfarm#gfarm_goods')->fetch($currentland['crop_id']);
				$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data($str.' and status=0 and goods_id='.$currentland['crop_id']);
				//仓库堆叠
				adddepotfile($depots,$rand[$key],$crop[$key]['depot_pile'],$currentland['crop_id'],$_G['uid']);
				C::t('#gfarm#gfarm_member')->update_by_uid($setInfo['crop_exp']+$germ['experience'],$setInfo['crop_power'],$_G['uid']);
				$userarr=array(
					'last_visit'=>time(),
				);
				$landarr=array(
					'germ_id'=>0,
					'crop_id'=>0,
					'land_number'=>$currentland['land_number']+1,
					'gain_number'=>0,
				);
				$landlogarr=array(
					'uid'=>$_G['uid'],
					'type'=>3,
					'target_uid'=>$_G['uid'],
					'target_land'=>$currentland['id'],
					'good_id'=>$currentland['crop_id'],
					'good_name'=>$crop[$key]['name'],
					'good_number'=>$currentland['gain_number'],
					'create_time'=>time(),
				);
				landfunc($currentland['id'], $userarr, $landarr, $landlogarr,$_G['uid']);
				$landalllogarr=array(
					'uid'=>$_G['uid'],
					'type'=>2,
					'goods_id'=>$currentland['crop_id'],
					'create_time'=>time(),
				);	
				$landalllog=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=2 and uid='.$_G['uid'].' and goods_id='.$currentland['crop_id']);
				if(empty($landalllog)){
					$landalllogarr['number']=1;
					C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr);
				}else{
					$landalllogarr['number']=$landalllog['number']+1;
					C::t('#gfarm#gfarm_land_all_log')->update($landalllog['id'],$landalllogarr);
				}
				$landalllogarr1=array(
					'uid'=>$_G['uid'],
					'type'=>3,
					'goods_id'=>$currentland['crop_id'],
					'create_time'=>time(),
				);
				$landalllog1=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=3 and uid='.$_G['uid'].' and goods_id='.$currentland['crop_id']);
				if(empty($landalllog1)){
					$landalllogarr1['number']=$currentland['gain_number'];
					C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr1);
				}else{
					$landalllogarr1['number']=$landalllog1['number']+$currentland['gain_number'];
					C::t('#gfarm#gfarm_land_all_log')->update($landalllog1['id'],$landalllogarr1);
				}
			}
		}
		$user=C::t('#gfarm#gfarm_member')->fetch_by_uid($_G['uid']);
		$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$user['experience'].' order by level desc');
		if($mylevel1!=$mylevel){
			$levelflag=1;
		}
		$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$user['experience'].' order by level');
		if(empty($nextlevel)){
			$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
		}
		include template('gfarm:ajax/allshouhuoajax');
		exit;
	}	
}
if($_GET['act']=='stealcrop'){//偷窃
	if($_GET['formhash']==formhash()){
		if($user['now_strength']<$setInfo['steal_power']){
			$mess=lang('plugin/gfarm','104');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		$currentland=C::t('#gfarm#gfarm_member_land')->fetch_first_data(' and uid='.$uid.' and land_id='.$_GET['landid']);
		$succnum=rand(1, 100);
		$operatland=C::t('#gfarm#gfarm_operat_land')->fetch_first_data(' and uid='.$_G['uid'].' and land_id='.$currentland['id']);
		if(empty($operatland)){
			C::t('#gfarm#gfarm_operat_land')->insert(array(
				'uid'=>$_G['uid'],
				'land_id'=>$currentland['id'],
			));
			$operatland=C::t('#gfarm#gfarm_operat_land')->fetch_first_data(' and uid='.$_G['uid'].' and land_id='.$currentland['id']);
		}
		if($operatland['steal_time']>$currentland['gain_time']){
			$mess=lang('plugin/gfarm','108');
			include template('gfarm:ajax/touqieajax');
			exit;
		}		
		$stealodds=$setInfo['steal_odds'];
		$stealnumber=0;
		if($cequipment==1){
			$equipments=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$_G['uid'].' and big_type=1 and status=1');
			$decorates=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$currentland['uid'].' and big_type=4 and status=1');
			foreach ($equipments as $value) {
				$equipment=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$value['goods_id']);
				$stealodds+=$equipment['add_steal_odds'];
				$stealnumber+=$equipment['add_steal_number'];
			}
			foreach ($decorates as $value) {
				$decorate=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$value['goods_id']);
				$stealodds-=$decorate['add_steal_odds'];
				$stealnumber-=$decorate['add_steal_number'];
			}
		}
		$curvip=C::t('#gfarm#gfarm_vip')->fetch_first_data(' and group_id='.$currentuser['groupid']);
		if(!empty($curvip)){
			if(!empty($curvip['restealodd'])){
				$stealodds-=$curvip['restealodd'];
			}
			if(!empty($curvip['restealnumber'])){
				$stealnumber-=$curvip['restealnumber'];
			}
		}
		if(!empty($myvip)){
			if(!empty($myvip['addstealodd'])){
				$stealodds+=$myvip['addstealodd'];
			}
			if(!empty($myvip['addstealnumber'])){
				$stealnumber+=$myvip['addstealnumber'];
			}
		}
		if($succnum>$stealodds){
			$mess=lang('plugin/gfarm','109');
		}else{			
			$rand=randnum($stealnumber);
			if($currentland['gain_number']<=$currentland['steal_number']){
				$rand=0;
			}elseif($currentland['gain_number']-$rand<$currentland['steal_number']){
				$rand=$currentland['gain_number']-$currentland['steal_number'];
			}
			if(!empty($rand)){
				C::t('#gfarm#gfarm_member_land')->update($currentland['id'],array(
					'gain_number'=>$currentland['gain_number']-$rand,
				));
				$crop=C::t('#gfarm#gfarm_goods')->fetch($currentland['crop_id']);
				$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data($str.' and status=0 and goods_id='.$currentland['crop_id']);
				//仓库堆叠
				adddepotfile($depots,$rand,$crop['depot_pile'],$currentland['crop_id'],$_G['uid']);					
				$userarr=array(
					'experience'=>$user['experience']+$setInfo['steal_exp'],
					'now_strength'=>$user['now_strength']-$setInfo['steal_power'],
					'last_visit'=>time(),
				);
				$landarr=array(
					'gain_number'=>$currentland['gain_number']-$rand,
					'update_time'=>time(),
				);
				$landlogarr=array(
					'uid'=>$_G['uid'],
					'type'=>6,
					'target_uid'=>$currentland['uid'],
					'target_land'=>$currentland['id'],
					'good_id'=>$currentland['crop_id'],
					'good_name'=>$crop['name'],
					'good_number'=>$rand,
					'create_time'=>time(),
				);
				landfunc($currentland['id'], $userarr, $landarr, $landlogarr,$_G['uid']);
				$landalllogarr=array(
					'uid'=>$_G['uid'],
					'type'=>4,
					'goods_id'=>$currentland['crop_id'],
					'create_time'=>time(),
				);		
				$landalllog=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=4 and uid='.$_G['uid'].' and goods_id='.$currentland['crop_id']);
				if(empty($landalllog)){
					$landalllogarr['number']=1;
					C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr);
				}else{
					$landalllogarr['number']=$landalllog['number']+1;
					C::t('#gfarm#gfarm_land_all_log')->update($landalllog['id'],$landalllogarr);
				}
				$landalllogarr1=array(
					'uid'=>$_G['uid'],
					'type'=>5,
					'goods_id'=>$currentland['crop_id'],
					'create_time'=>time(),
				);
				$landalllog1=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=5 and uid='.$_G['uid'].' and goods_id='.$currentland['crop_id']);
				if(empty($landalllog1)){
					$landalllogarr1['number']=$rand;
					C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr1);
				}else{
					$landalllogarr1['number']=$landalllog1['number']+$rand;
					C::t('#gfarm#gfarm_land_all_log')->update($landalllog1['id'],$landalllogarr1);
				}
				$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$userarr['experience'].' order by level desc');
				if($mylevel1!=$mylevel){
					$levelflag=1;
				}
				$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$userarr['experience'].' order by level');
				if(empty($nextlevel)){
					$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
				}
				$power=$user['now_strength']-$setInfo['steal_power'];
			}
		}
		C::t('#gfarm#gfarm_operat_land')->update($operatland['id'],array(
			'steal_time'=>time(),
		));
		include template('gfarm:ajax/touqieajax');
		exit;
	}	
}
if($_GET['act']=='allsteal'){//一键偷窃
	if($_GET['formhash']==formhash()){
		if($user['now_strength']<$setInfo['steal_power']){
			$mess=lang('plugin/gfarm','104');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		$currentlands=C::t('#gfarm#gfarm_member_land')->fetch_all_data(' and germ_id!=0 and uid='.$uid.' and gain_time<='.time());
		$c=0;
		foreach ($currentlands as $value) {
			$operatland=C::t('#gfarm#gfarm_operat_land')->fetch_first_data(' and uid='.$_G['uid'].' and land_id='.$value['id']);
			if(empty($operatland)){
				C::t('#gfarm#gfarm_operat_land')->insert(array(
				'uid'=>$_G['uid'],
				'land_id'=>$value['id'],
				));
				$operatland=C::t('#gfarm#gfarm_operat_land')->fetch_first_data(' and uid='.$_G['uid'].' and land_id='.$value['id']);
			}
			if($operatland['steal_time']<$value['gain_time']&&$value['gain_number']>$value['steal_number']){
				$lands[$c]=$value;
				$c++;
			}
			C::t('#gfarm#gfarm_operat_land')->update($operatland['id'],array(
				'steal_time'=>time(),
			));
		}
		if(empty($lands)){
			$mess=lang('plugin/gfarm','155');
			include template('gfarm:ajax/commonajax');
			exit;
		}
		if(!empty($setInfo['steal_power'])){
			$landlength=floor($user['now_strength']/$setInfo['steal_power']);
			if($landlength>count($lands)){
				$landlength=count($lands);
			}
		}else{
			$landlength=count($lands);
		}
		$stealodds=$setInfo['steal_odds'];
		$stealnumber=0;
		if($cequipment==1){
			$equipments=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$_G['uid'].' and big_type=1 and status=1');
			$decorates=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$uid.' and big_type=4 and status=1');
			foreach ($equipments as $value) {
				$equipment=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$value['goods_id']);
				$stealodds+=$equipment['add_steal_odds'];
				$stealnumber+=$equipment['add_steal_number'];
			}
			foreach ($decorates as $value) {
				$decorate=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$value['goods_id']);
				$stealodds-=$decorate['add_steal_odds'];
				$stealnumber-=$decorate['add_steal_number'];
			}
		}
		$curvip=C::t('#gfarm#gfarm_vip')->fetch_first_data(' and group_id='.$currentuser['groupid']);
		if(!empty($curvip)){
			if(!empty($curvip['restealodd'])){
				$stealodds-=$curvip['restealodd'];
			}
			if(!empty($curvip['restealnumber'])){
				$stealnumber-=$curvip['restealnumber'];
			}
		}
		if(!empty($myvip)){
			if(!empty($myvip['addstealodd'])){
				$stealodds+=$myvip['addstealodd'];
			}
			if(!empty($myvip['addstealnumber'])){
				$stealnumber+=$myvip['addstealnumber'];
			}
		}
		foreach ($lands as $key=>$currentland) {		
			if($key>$landlength-1){
				break;
			}	
			$succnum=rand(1, 100);						
			$rand[$key]=randnum($stealnumber);
			if($succnum>$stealodds){
				$rand[$key]=0;
			}elseif($currentland['gain_number']-$rand[$key]<$currentland['steal_number']){
				$rand[$key]=$currentland['gain_number']-$currentland['steal_number'];
			}
			if(!empty($rand[$key])){
				C::t('#gfarm#gfarm_member_land')->update($currentland['id'],array(
					'gain_number'=>$currentland['gain_number']-$rand[$key],
				));
				$crop[$key]=C::t('#gfarm#gfarm_goods')->fetch($currentland['crop_id']);
				$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data($str.' and status=0 and goods_id='.$currentland['crop_id']);
				//仓库堆叠
				adddepotfile($depots,$rand[$key],$crop[$key]['depot_pile'],$currentland['crop_id'],$_G['uid']);
				C::t('#gfarm#gfarm_member')->update_by_uid($setInfo['steal_exp'],$setInfo['steal_power'],$_G['uid']);	
				$userarr=array(
					'last_visit'=>time(),
				);
				$landarr=array(
					'gain_number'=>$currentland['gain_number']-$rand[$key],
					'update_time'=>time(),
				);
				$landlogarr=array(
					'uid'=>$_G['uid'],
					'type'=>6,
					'target_uid'=>$currentland['uid'],
					'target_land'=>$currentland['id'],
					'good_id'=>$currentland['crop_id'],
					'good_name'=>$crop[$key]['name'],
					'good_number'=>$rand[$key],
					'create_time'=>time(),
				);
				landfunc($currentland['id'], $userarr, $landarr, $landlogarr,$_G['uid']);
				$landalllogarr=array(
					'uid'=>$_G['uid'],
					'type'=>4,
					'goods_id'=>$currentland['crop_id'],
					'create_time'=>time(),
				);
				$landalllog=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=4 and uid='.$_G['uid'].' and goods_id='.$currentland['crop_id']);
				if(empty($landalllog)){
					$landalllogarr['number']=1;
					C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr);
				}else{
					$landalllogarr['number']=$landalllog['number']+1;
					C::t('#gfarm#gfarm_land_all_log')->update($landalllog['id'],$landalllogarr);
				}
				$landalllogarr1=array(
					'uid'=>$_G['uid'],
					'type'=>5,
					'goods_id'=>$currentland['crop_id'],
					'create_time'=>time(),
				);
				$landalllog1=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=5 and uid='.$_G['uid'].' and goods_id='.$currentland['crop_id']);
				if(empty($landalllog1)){
					$landalllogarr1['number']=$rand[$key];
					C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr1);
				}else{
					$landalllogarr1['number']=$landalllog1['number']+$rand[$key];
					C::t('#gfarm#gfarm_land_all_log')->update($landalllog1['id'],$landalllogarr1);
				}
			}
		}
		$user=C::t('#gfarm#gfarm_member')->fetch_by_uid($_G['uid']);
		$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$user['experience'].' order by level desc');
		if($mylevel1!=$mylevel){
			$levelflag=1;
		}
		$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$user['experience'].' order by level');
		if(empty($nextlevel)){
			$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
		}
		include template('gfarm:ajax/alltouqieajax');
		exit;
	}	
}
$str=' and a.uid='.$_G['uid'].' and use_level<='.$mylevel['level'];
$do=$_GET['do'];
if(!empty($do)){//播种
	$str.=' and big_type='.$do;
}
$name=$_GET['name'];
if(!empty($name)){
	$str.=" and name like '%".$name."%'";
}
$str.=' and status=0';
$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);
include template('gfarm:ajax/menuajax');

?>