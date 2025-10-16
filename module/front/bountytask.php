<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$do=$_GET['do'];
if($_GET['act']=='bountytask'){	//赏金任务
	$str=' and task_type=2 and visible=0 order by create_time desc';
	$taskInfos=C::t('#gfarm#gfarm_task')->fetch_all_data($str);
	foreach ($taskInfos as $key=>$value) {
		if(!empty($value['task_in'])){
			$membertask=C::t('#gfarm#gfarm_member_task')->fetch_first_data(' and task_status=0 and task_id='.$value['id']);
			if(!empty($value['task_time'])){
				if($membertask['ctime']+$value['task_time']*60*60<time()){
					C::t('#gfarm#gfarm_task')->update($value['id'],array(
						'task_in'=>0,
						'update_time'=>time(),
					));
					C::t('#gfarm#gfarm_member_task')->update($membertask['myid'],array(
						'task_status'=>1,
					));
				}
			}		
		}
	}
	$taskInfos=C::t('#gfarm#gfarm_task')->fetch_all_data($str);
	foreach ($taskInfos as $key=>$value) {
		if(!empty($value['task_in'])){
			$membertask=C::t('#gfarm#gfarm_member_task')->fetch_first_data(' and task_status=0 and task_id='.$value['id']);
			$taskuser[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($membertask['uid']);
		}
		$rewardstr='';
		$items[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
		$users[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['task_uid']);
		$targets[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['target_uid']);
		if(!empty($value['reward_money'])){
			$rewardstr.=$value['reward_money'].$config['moneyname'].',';
		}
		if(!empty($value['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
			$rewardstr.=$item['name'].'x'.$value['items_num'].',';
		}
		$rewardstrs[$key]=substr($rewardstr,0,strlen($rewardstr)-1);
	}
	include template('gfarm:front/bountytask');
	exit;
}
if($_GET['act']=='ajax'){//赏金ajax
	$str=' and task_type=2';
	if(!empty($do)){
		$str.=' and task_uid='.$_G['uid'];
	}else{
		$str.=' and visible=0';
	}
	$str.=' order by create_time desc';
	$taskInfos=C::t('#gfarm#gfarm_task')->fetch_all_data($str);	
	foreach ($taskInfos as $key=>$value) {
		if(!empty($value['task_in'])){
			$membertask=C::t('#gfarm#gfarm_member_task')->fetch_first_data(' and task_status=0 and task_id='.$value['id']);
			if(!empty($value['task_time'])){
				if($membertask['ctime']+$value['task_time']*60*60<time()){
					C::t('#gfarm#gfarm_task')->update($value['id'],array(
						'task_in'=>0,
						'update_time'=>time(),
					));
					C::t('#gfarm#gfarm_member_task')->update($membertask['myid'],array(
						'task_status'=>1,
					));
				}
			}		
		}
	}
	$taskInfos=C::t('#gfarm#gfarm_task')->fetch_all_data($str);
	foreach ($taskInfos as $key=>$value) {
		if(!empty($value['task_in'])){
			$membertask=C::t('#gfarm#gfarm_member_task')->fetch_first_data(' and task_status=0 and task_id='.$value['id']);
			$taskuser[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($membertask['uid']);
		}
		$rewardstr='';
		$items[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
		$users[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['task_uid']);
		$targets[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['target_uid']);
		if(!empty($value['reward_money'])){
			$rewardstr.=$value['reward_money'].$config['moneyname'].',';
		}
		if(!empty($value['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
			$rewardstr.=$item['name'].'x'.$value['items_num'].',';
		}
		$rewardstrs[$key]=substr($rewardstr,0,strlen($rewardstr)-1);
	}
	include template('gfarm:ajax/bountytaskajax');
	exit;
}
if($_GET['act']=='gettask'){//领取赏金任务
	if($_GET['formhash']==formhash()){
		C::t('#gfarm#gfarm_task')->update($_GET['taskid'],array(
		'task_in'=>1,
		'update_time'=>time(),
		));
		C::t('#gfarm#gfarm_member_task')->insert(array(
		'uid'=>$_G['uid'],
		'task_id'=>$_GET['taskid'],
		'create_time'=>time(),
		));
		include template('gfarm:ajax/taskgetajax');
		exit;
	}	
}
if($_GET['act']=='ajax1'){//我领取的赏金任务
	$str=' and task_type=2 and task_status=0 and uid='.$_G['uid'].' order by a.create_time desc';
	$taskInfos=C::t('#gfarm#gfarm_member_task')->fetch_all_data($str);
	foreach ($taskInfos as $key=>$value) {
		if(!empty($value['task_time'])){
			if($value['ctime']+$value['task_time']*60*60<time()){
				C::t('#gfarm#gfarm_task')->update($value['task_id'],array(
					'task_in'=>0,
					'update_time'=>time(),
				));
				C::t('#gfarm#gfarm_member_task')->update($value['myid'],array(
					'task_status'=>1,
				));
			}
		}
	}
	$taskInfos=C::t('#gfarm#gfarm_member_task')->fetch_all_data($str);
	foreach ($taskInfos as $key=>$value) {
		$str1=' and type=5 and uid='.$_G['uid'].' and good_id='.$value['target_object'].' and target_uid='.$value['target_uid']." and a.create_time>".$value['ctime'];
		$csnum[$key]=C::t('#gfarm#gfarm_land_log')->count_num($str1);		
		$rewardstr='';
		$items[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
		$targets[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['target_uid']);
		if(!empty($value['reward_money'])){
			$rewardstr.=$value['reward_money'].$config['moneyname'].',';
		}
		if(!empty($value['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
			$rewardstr.=$item['name'].'x'.$value['items_num'].',';
		}
		$rewardstrs[$key]=substr($rewardstr,0,strlen($rewardstr)-1);
	}
	include template('gfarm:ajax/bountyajax');
	exit;
}
if($_GET['act']=='posttask'){//发布赏金任务
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['target_num'],$result0); 
		preg_match($tt,$_GET['reward_money'],$result1); 	
		preg_match($tt,$_GET['reward_yuan'],$result2); 
		preg_match($tt,$_GET['items_num'],$result3); 	
		preg_match($tt,$_GET['reward_bene'],$result4); 
		preg_match($tt,$_GET['reward_exp'],$result5); 	
		preg_match($tt,$_GET['task_time'],$result6); 	
		if(empty($_GET['task_name'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','002')."');";
			echo "</script>";
			exit;
		}else{
			$countname=C::t('#gfarm#gfarm_task')->count_all_data(" and task_type=2 and task_name='".$_GET['task_name']."'");
			if(empty($tid)){
				if(!empty($countname)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
					echo "</script>";
					exit;
				}
			}else{
				if($task['task_name']!=$_GET['task_name']){
					if(!empty($countname)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
						echo "</script>";
						exit;
					}
				}
			}		
		}
		if(mb_strlen($_GET['task_name'],$g_charset)>25){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','004')."');";
			echo "</script>";
			exit;
		}	
		if($_GET['target_obj']=='1'){
			$_GET['target_uid']=$_G['uid'];
		}else{
			preg_match($tt,$_GET['target_uid'],$result); 
			if(empty($result)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','092')."');";
				echo "</script>";
				exit;
			}else{
				$hasuid=C::t('#gfarm#gfarm_member')->fetch($_GET['target_uid']);
				if(empty($hasuid)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','093')."');";
					echo "</script>";
					exit;
				}
			}
		}		
		if(empty($_GET['target_object'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','094')."');";
			echo "</script>";
			exit;
		}			
		if(empty($result0)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','005')."');";
			echo "</script>";
			exit;
		}
		if(empty($_GET['reward_money'])&&empty($_GET['reward_items'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','006')."');";
			echo "</script>";
			exit;
		}
		if(empty($_GET['reward_money'])){
			$_GET['reward_money']=0;
		}elseif(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','007')."');";
			echo "</script>";
			exit;
		}else{
			if($_GET['reward_money']>$user['money']){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','095')."');";
				echo "</script>";
				exit;				
			}				
		}	
		if(!empty($_GET['reward_items'])){
			if(empty($result3)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','008')."');";
				echo "</script>";
				exit;
			}else{
				$sumdepot=C::t('#gfarm#gfarm_depot')->fetch_sum_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$_GET['reward_items']);
				if($_GET['items_num']>$sumdepot){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','096')."');";
					echo "</script>";
					exit;
				}
			}
		}else{
			$_GET['items_num']=0;
		}	
		if(empty($_GET['task_time'])){
			$_GET['task_time']=0;
		}elseif(empty($result6)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','097')."');";
			echo "</script>";
			exit;
		}	
		$updatearray=array(
			'task_uid'=>$_G['uid'],
			'task_name'=>$_GET['task_name'],
			'task_way'=>8,	
			'task_type'=>2,
			'target_object'=>$_GET['target_object'],
			'target_num'=>$_GET['target_num'],
			'reward_money'=>$_GET['reward_money'],
			'reward_yuan'=>$_GET['reward_yuan'],
			'reward_items'=>$_GET['reward_items'],
			'items_num'=>$_GET['items_num'],
			'task_describe'=>$_GET['task_describe'],
			'task_time'=>$_GET['task_time'],
			'target_uid'=>$_GET['target_uid'],
			'create_time'=>time(),
			'update_time'=>time(),
		);
		C::t('#gfarm#gfarm_task')->insert($updatearray);
		if(!empty($result1)){
			C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
				'money'=>$user['money']-$_GET['reward_money'],
			));
		}
		$user=C::t('#gfarm#gfarm_member')->fetch_by_uid($_G['uid']);
		if(!empty($result2)){
			C::t("common_member_count")->increase($_G['uid'],array('extcredits'.$config['bullionstype']=>-$_GET['reward_yuan']));
		}
		$user_credit = DB::result_first($sql,array('common_member_count',$_G['uid']));
		if(!empty($_GET['reward_items'])){
			$depot=C::t('#gfarm#gfarm_depot')->fetch_first_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$_GET['reward_items'].' order by id desc');
			deledepot($depot,$_GET['items_num']);
		}
		echo "<script>";
		echo "parent.window.hideWindow('gfarm1');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."',".$user['money'].",".$user_credit.");";
		echo "</script>";
		exit;
	}
	$object=C::t('#gfarm#gfarm_items')->fetch_all_data(' and big_type=19 and use_target!=1');
	$items=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and big_type=19 and uid='.$_G['uid']);
	include template('gfarm:front/posttask');
	exit;
}
if($_GET['act']=='reward'){//赏金领取奖励
	if($_GET['formhash']==formhash()){
		$str1=' and type=5 and uid='.$_G['uid'].' and good_id='.$mytask['target_object'].' and target_uid='.$mytask['target_uid']." and a.create_time>".$mytask['ctime'];
		$csnum=C::t('#gfarm#gfarm_land_log')->count_num($str1);
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
		}
		if (!empty($mytask['reward_bene'])){
			$userarr['benevolence']=$user['benevolence']+$mytask['reward_bene'];	
		}
		C::t('#gfarm#gfarm_member')->update($_G['uid'],$userarr);
		if (!empty($mytask['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($mytask['reward_items']);
			$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and big_type=19 and status=0 and uid='.$_G['uid'].' and goods_id='.$mytask['reward_items']);
			adddepotfile($depots,$mytask['items_num'],$item['depot_pile'],$mytask['reward_items'],$_G['uid']);
		}
		$mtaskarr=array(
			'reward_flag'=>1,
			'reward_time'=>time(),
		);
		$mtaskarr['task_status']=1;
		C::t('#gfarm#gfarm_main_task')->update($mytask['task_id'],array(
			'visible'=>1,
			'update_time'=>time(),
		));
		C::t('#gfarm#gfarm_member_main_task')->update($tid,$mtaskarr);
		include template('gfarm:ajax/taskajax');//主线任务
		exit;
	}
}

?>