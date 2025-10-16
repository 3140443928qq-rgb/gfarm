<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
if(empty($dailyact)){
	$mess=lang('plugin/gfarm','115');
	include template('gfarm:ajax/commonajax');
	exit;
}
$mytask=$dailyact;
$good=C::t('#gfarm#gfarm_goods')->fetch($mytask['target_object']);
$wardstr='';
if (!empty($mytask['reward_money'])){
	$wardstr.=','.$config['moneyname'].'x'.$mytask['reward_money'];
}
if (!empty($mytask['reward_yuan'])){
	$wardstr.=','.$config['bullionsname'].'x'.$mytask['reward_yuan'];
}
if (!empty($mytask['reward_exp'])){
	$wardstr.=','.lang('plugin/gfarm','079').'x'.$mytask['reward_exp'];
}
if (!empty($mytask['reward_bene'])){
	$wardstr.=','.lang('plugin/gfarm','080').'x'.$mytask['reward_bene'];
}
if (!empty($mytask['reward_items'])){
	$item=C::t('#gfarm#gfarm_goods')->fetch($mytask['reward_items']);
	$wardstr.=','.$item['name'].'x'.$mytask['items_num'];
}
$mysum=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$mytask['target_object']);
$mynum=C::t('#gfarm#gfarm_member_act')->count_all_data(' and uid='.$_G['uid'].' and act_id='.$mytask['id']);
$wardstr=substr($wardstr, 1);
if($_GET['act']=='reward'){//上缴作物
	if($_GET['formhash']==formhash()){
		if(empty($mytask)){
			$mess=lang('plugin/gfarm','089');
			include template('gfarm:ajax/taskajax');
			exit;
		}
		if($mysum<$mytask['target_num']){
			$mess=lang('plugin/gfarm','090');
			include template('gfarm:ajax/taskajax');
			exit;
		}	
		$useract=C::t('#gfarm#gfarm_member_act')->fetch_all_data(' and uid='.$_G['uid'].' and act_id='.$mytask['id']);
		if(count($useract)>=$mytask['member_num']){
			$mess=lang('plugin/gfarm','091');
			include template('gfarm:ajax/taskajax');
			exit;
		}
		$cuser=C::t('#gfarm#gfarm_daily_act')->updateflag($mytask['id'],$mytask['task_num']);
		if(!empty($mytask['success_flag'])||empty($cuser)){
			$mess=lang('plugin/gfarm','089');
			include template('gfarm:ajax/taskajax');
			exit;
		}		
		$dailyact=C::t('#gfarm#gfarm_daily_act')->fetch_first_data(" and visible=0 and start_time<=".time().' and end_time>='.time());
		$actarr=array(
			'uid'=>$_G['uid'],
			'act_id'=>$mytask['id'],
			'create_time'=>time(),
		);	
		if(empty($mytask['ready_num'])){
			$actarr['first_flag']=1;
		}
		if($dailyact['ready_num']>=$dailyact['task_num']){
			$actarr['last_flag']=1;
			C::t('#gfarm#gfarm_daily_act')->update($dailyact['id'],array(
				'success_flag'=>1,
			));
		}
		C::t('#gfarm#gfarm_member_act')->insert($actarr);
		$userarr=array();
		if (!empty($mytask['reward_money'])){
			$userarr['money']=$user['money']+$mytask['reward_money'];			
		}
		if (!empty($mytask['reward_exp'])){
			$userarr['experience']=$user['experience']+$mytask['reward_exp'];		
			$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$userarr['experience'].' order by level desc');	
			if($mylevel1!=$mylevel){
				$levelflag=1;
			}
			$mylevel=$mylevel1;
			$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$userarr['experience'].' order by level');
			if(empty($nextlevel)){
				$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
			}
		}
		if (!empty($mytask['reward_bene'])){
			$userarr['benevolence']=$user['benevolence']+$mytask['reward_bene'];	
		}
		C::t('#gfarm#gfarm_member')->update($_G['uid'],$userarr);
		if (!empty($mytask['reward_yuan'])){
			C::t("common_member_count")->increase($_G['uid'],array('extcredits'.$config['bullionstype']=>$mytask['reward_yuan']));
		}
		$depot=C::t('#gfarm#gfarm_depot')->fetch_first_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$mytask['target_object'].' order by id desc');
		deledepot($depot,$mytask['target_num']);
		if (!empty($mytask['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($mytask['reward_items']);
			$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$mytask['reward_items']);
			adddepotfile($depots,$mytask['items_num'],$item['depot_pile'],$mytask['reward_items'],$_G['uid']);
		}		
		include template('gfarm:ajax/actajax');
		exit;
	}
}
include template('gfarm:front/acttask');
exit;


?>