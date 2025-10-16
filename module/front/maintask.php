<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$type=$_GET['type'];
if(!empty($type)){
	$tid=$maintask['myid'];
}else{
	$tid=$_GET['tid'];
}
$mytask=C::t('#gfarm#gfarm_member_main_task')->fetch_first_data(' and a.id='.$tid);
$str='and uid='.$_G['uid'];
if(!empty($mytask['target_object'])){
	$object=C::t('#gfarm#gfarm_goods')->fetch($mytask['target_object']);
}
$csnum=taskway($str,$mytask);
if($_GET['act']=='reward'){//主线领取奖励
	if($_GET['formhash']==formhash()){
		if($csnum<$mytask['target_num']){
			$mess=lang('plugin/gfarm','087');
			include template('gfarm:ajax/taskajax');
			exit;
		}
		if(!empty($mytask['reward_flag'])){
			$mess=lang('plugin/gfarm','088');
			include template('gfarm:ajax/taskajax');
			exit;
		}
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
		if (!empty($mytask['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($mytask['reward_items']);
			$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$mytask['reward_items']);
			adddepotfile($depots,$mytask['items_num'],$item['depot_pile'],$mytask['reward_items'],$_G['uid']);
		}
		$mtaskarr=array(
			'reward_flag'=>1,
			'reward_time'=>time(),
		);
		C::t('#gfarm#gfarm_member_main_task')->update($tid,$mtaskarr);
		include template('gfarm:ajax/maintaskajax');//主线任务
		exit;
	}
}
if($_GET['act']=='showtask'){//主线任务详情
	$wardstr='';
	if (!empty($mytask['reward_money'])){
		$wardstr.=','.$config['moneyname'].'x'.$mytask['reward_money'];
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
	$wardstr=substr($wardstr, 1);
	include template('gfarm:front/maintask');
	exit;
}


?>