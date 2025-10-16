<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
if($_GET['act']=='daily'){//日常任务
	foreach ($dailytasks as $key=>$value) {
		$str1=' and uid='.$_G['uid']." and FROM_UNIXTIME(a.create_time,'%Y%m%d')=".date("Ymd");
		if(!empty($value['target_object'])){
			$objects[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
		}
		$csnums[$key]=taskway1($str1,$value);	
		$rewardstr='';
		if(!empty($value['reward_money'])){
			$rewardstr.=','.$config['moneyname'].'x'.$value['reward_money'];
		}
		if (!empty($value['reward_exp'])){
			$rewardstr.=','.lang('plugin/gfarm','079').'x'.$value['reward_exp'];
		}
		if (!empty($value['reward_bene'])){
			$rewardstr.=','.lang('plugin/gfarm','080').'x'.$value['reward_bene'];
		}
		if(!empty($value['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
			$rewardstr.=','.$item['name'].'x'.$value['items_num'];
		}
		$rewardstrs[$key]=substr($rewardstr,1);
	}
	include template('gfarm:front/dailytask');
	exit;
}
$tid=$_GET['tid'];
$mytask=C::t('#gfarm#gfarm_member_daily_task')->fetch_first_data(' and a.id='.$tid);
$str=' and uid='.$_G['uid']." and FROM_UNIXTIME(a.create_time,'%Y%m%d')=".date("Ymd");
if(!empty($mytask['target_object'])){
	$object=C::t('#gfarm#gfarm_goods')->fetch($mytask['target_object']);
}
$csnum=taskway1($str,$mytask);
if($_GET['act']=='rewardaily'){//日常领取奖励
	if($_GET['formhash']==formhash()){
		if($csnum<$mytask['target_num']){
			$mess=lang('plugin/gfarm','087');
			include template('gfarm:ajax/dailymenu');
			exit;
		}
		if(!empty($mytask['reward_flag'])){
			$mess=lang('plugin/gfarm','088');
			include template('gfarm:ajax/dailymenu');
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
		C::t('#gfarm#gfarm_member_daily_task')->update($tid,$mtaskarr);
		$nexttasks=C::t('#gfarm#gfarm_daily_task')->fetch_all_data(' and visible=0 and (max_level=0 or max_level>='.$mylevel['level'].') and min_level<='.$mylevel['level'].' and task_preid='.$mytask['task_id']);
		if(!empty($nexttasks)){
			foreach ($nexttasks as $key => $value) {
				C::t('#gfarm#gfarm_member_daily_task')->insert(array(
						'uid'=>$_G['uid'],
						'task_id'=>$value['id'],
						'create_time'=>time(),
				));
			}
		}
		$dailytasks=C::t('#gfarm#gfarm_member_daily_task')->fetch_all_data(' and reward_flag=0 and uid='.$_G['uid']." and FROM_UNIXTIME(a.create_time,'%Y%m%d')=".date("Ymd"));
		foreach ($dailytasks as $key=>$value) {
			$str1=' and uid='.$_G['uid']." and FROM_UNIXTIME(a.create_time,'%Y%m%d')=".date("Ymd");
			if(!empty($value['target_object'])){
				$objects[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
			}
			$csnums[$key]=taskway1($str1,$value);
			$rewardstr='';
			if(!empty($value['reward_money'])){
				$rewardstr.=','.$config['moneyname'].'x'.$value['reward_money'];
			}
			if (!empty($value['reward_exp'])){
				$rewardstr.=','.lang('plugin/gfarm','079').'x'.$value['reward_exp'];
			}
			if (!empty($value['reward_bene'])){
				$rewardstr.=','.lang('plugin/gfarm','080').'x'.$value['reward_bene'];
			}
			if(!empty($value['reward_items'])){
				$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
				$rewardstr.=','.$item['name'].'x'.$value['items_num'];
			}
			$rewardstrs[$key]=substr($rewardstr,1);
		}
		include template('gfarm:ajax/dailymenu');
		exit;

	}
}

?>