<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$type=$_GET['type'];
$mytask=C::t('#gfarm#gfarm_achieve_task')->fetch($_GET['taskid']);
$str='and uid='.$_G['uid'];
if(!empty($mytask['target_object'])){
	$object=C::t('#gfarm#gfarm_goods')->fetch($mytask['target_object']);
}
$csnum=taskway($str,$mytask);
if($_GET['act']=='reward'){//领取奖励
	if($_GET['formhash']==formhash()){
		if($csnum<$mytask['target_num']){
			$mess=".lang('plugin/gfarm','087').";
			include template('gfarm:ajax/taskajax');
			exit;
		}
		$membertask=C::t('#gfarm#gfarm_member_achieve_task')->fetch_first_data(' and uid='.$_G['uid'].' and task_id='.$_GET['taskid']);
		if(!empty($membertask)){
			$mess=".lang('plugin/gfarm','088').";
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
			'uid'=>$_G['uid'],
			'task_id'=>$_GET['taskid'],
			'reward_flag'=>1,
			'reward_time'=>time(),
			'create_time'=>time(),
		);
		C::t('#gfarm#gfarm_member_achieve_task')->insert($mtaskarr);	
		include template('gfarm:ajax/taskajax');
		exit;
	}
}
if($_GET['act']=='ajax'){//成就ajax
	$str2=' and visible=0 order by create_time desc';
	$taskInfos=C::t('#gfarm#gfarm_achieve_task')->fetch_all_data($str2);
	foreach ($taskInfos as $key=>$value) {
		$membertask[$key]=C::t('#gfarm#gfarm_member_achieve_task')->fetch_first_data(' and uid='.$_G['uid'].' and task_id='.$value['id']);
		$str1=' and uid='.$_G['uid'];
		if(!empty($value['target_object'])){
			$objects[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
		}
		$csnums[$key]=taskway($str1,$value);	
		$rewardstr='';
		if(!empty($value['reward_money'])){
			$rewardstr.=$value['reward_money'].$config['moneyname'].',';
		}
		if(!empty($value['reward_bene'])){
			$rewardstr.=$value['reward_bene'].lang('plugin/gfarm','080').',';
		}
		if(!empty($value['reward_exp'])){
			$rewardstr.=$value['reward_exp'].lang('plugin/gfarm','079').',';
		}
		if(!empty($value['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
			$rewardstr.=$item['name'].'x'.$value['items_num'].',';
		}
		$rewardstrs[$key]=substr($rewardstr,0,strlen($rewardstr)-1);
	}
	include template('gfarm:ajax/achievetaskajax');
	exit;
}
if($_GET['act']=='myajax'){//已完成成就ajax
	$str1=' and uid='.$_G['uid'].' order by a.create_time desc';
	$taskInfos=C::t('#gfarm#gfarm_member_achieve_task')->fetch_all_data($str1);	
	foreach ($taskInfos as $key=>$value) {
		$ways[$key]=C::t('#gfarm#gfarm_task_way')->fetch($value['task_way']);	
		if(!empty($value['target_object'])){
			$objects[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
		}
		$csnums[$key]=taskway($str,$value);	
		$rewardstr='';
		if(!empty($value['reward_money'])){
			$rewardstr.=$value['reward_money'].$config['moneyname'].',';
		}
		if(!empty($value['reward_bene'])){
			$rewardstr.=$value['reward_bene'].lang('plugin/gfarm','080').',';
		}
		if(!empty($value['reward_exp'])){
			$rewardstr.=$value['reward_exp'].lang('plugin/gfarm','079').',';
		}
		if(!empty($value['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
			$rewardstr.=$item['name'].'x'.$value['items_num'].',';
		}
		$rewardstrs[$key]=substr($rewardstr,0,strlen($rewardstr)-1);
	}
	include template('gfarm:ajax/achieveajax');
	exit;
}
$str2=' and visible=0 order by create_time desc';
$taskInfos=C::t('#gfarm#gfarm_achieve_task')->fetch_all_data($str2);
foreach ($taskInfos as $key=>$value) {
	$membertask[$key]=C::t('#gfarm#gfarm_member_achieve_task')->fetch_first_data(' and uid='.$_G['uid'].' and task_id='.$value['id']);
	$str1=' and uid='.$_G['uid'];
	if(!empty($value['target_object'])){
		$objects[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
	}
	$csnums[$key]=taskway($str1,$value);
	$rewardstr='';
	if(!empty($value['reward_money'])){
		$rewardstr.=$value['reward_money'].$config['moneyname'].',';
	}
	if(!empty($value['reward_exp'])){
		$rewardstr.=$value['reward_exp'].lang('plugin/gfarm','079').',';
	}
	if(!empty($value['reward_bene'])){
		$rewardstr.=$value['reward_bene'].lang('plugin/gfarm','080').',';
	}
	if(!empty($value['reward_items'])){
		$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
		$rewardstr.=$item['name'].'x'.$value['items_num'].',';
	}
	$rewardstrs[$key]=substr($rewardstr,0,strlen($rewardstr)-1);
}
include template('gfarm:front/achieve');
exit;


?>