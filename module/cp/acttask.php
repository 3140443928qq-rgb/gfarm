<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str=' order by visible';
if($_GET['act']=='update'){
	$tid=$_GET['tid'];
	$task=C::t('#gfarm#gfarm_act_task')->fetch($tid);
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['task_num'],$result2); 
		preg_match($tt,$_GET['member_num'],$result6); 
		preg_match($tt,$_GET['target_num'],$result0); 
		preg_match($tt,$_GET['reward_money'],$result1); 
		preg_match($tt,$_GET['reward_yuan'],$result7); 	
		preg_match($tt,$_GET['items_num'],$result3); 	
		preg_match($tt,$_GET['reward_bene'],$result4); 
		preg_match($tt,$_GET['reward_exp'],$result5); 	
		if(empty($_GET['task_name'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','002')."');";
			echo "</script>";
			exit;
		}else{
			$countname=C::t('#gfarm#gfarm_act_task')->count_all_data(" and task_name='".$_GET['task_name']."'");
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
		if(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','013')."');";
			echo "</script>";
			exit;
		}	
		if(empty($result6)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','014')."');";
			echo "</script>";
			exit;
		}
		if(empty($result0)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','015')."');";
			echo "</script>";
			exit;
		}
		if(empty($_GET['start_time'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','016')."');";
			echo "</script>";
			exit;
		}
		if(empty($_GET['end_time'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','017')."');";
			echo "</script>";
			exit;
		}
		if($_GET['start_time']>=$_GET['end_time']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','018')."');";
			echo "</script>";
			exit;
		}		
		$start=strtotime($_GET['start_time']);
		$end=strtotime($_GET['end_time']);
		$str1='';
		if(!empty($tid)){
			$str1=' and id!='.$tid;
		}
		$catask=C::t('#gfarm#gfarm_act_task')->count_all_data(" and ((start_time<".$end.' and end_time>'.$end.') or (start_time<'.$start.' and end_time>'.$start.'))'.$str1);
		if(!empty($catask)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','019')."');";
			echo "</script>";
			exit;
		}
		if(empty($_GET['reward_money'])&&empty($_GET['reward_yuan'])&&empty($_GET['reward_items'])&&empty($_GET['reward_bene'])&&empty($_GET['reward_exp'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','006')."');";
			echo "</script>";
			exit;
		}
		if(empty($_GET['reward_money'])){
			$_GET['reward_money']=0;
		}elseif(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','007').$config['moneyname'].lang('plugin/gfarm','082')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['reward_yuan'])){
			$_GET['reward_yuan']=0;
		}elseif(empty($result7)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','007').$config['bullionsname'].lang('plugin/gfarm','082')."');";
			echo "</script>";
			exit;
		}	
		if(!empty($_GET['reward_items'])){
			if(empty($result3)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','008')."');";
				echo "</script>";
				exit;
			}
		}else{
			$_GET['items_num']=0;
		}	
		if(empty($_GET['reward_bene'])){
			$_GET['reward_bene']=0;
		}elseif(empty($result4)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','009')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['reward_exp'])){
			$_GET['reward_exp']=0;
		}elseif(empty($result5)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','010')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['task_describe'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','011')."');";
			echo "</script>";
			exit;
		}
		$updatearray=array(
			'task_name'=>$_GET['task_name'],
			'task_num'=>$_GET['task_num'],
			'member_num'=>$_GET['member_num'],
			'reward_yuan'=>$_GET['reward_yuan'],
			'target_object'=>$_GET['target_object'],
			'target_num'=>$_GET['target_num'],
			'reward_money'=>$_GET['reward_money'],
			'reward_items'=>$_GET['reward_items'],
			'items_num'=>$_GET['items_num'],
			'reward_exp'=>$_GET['reward_exp'],
			'reward_bene'=>$_GET['reward_bene'],
			'visible'=>$_GET['visible'],
			'task_describe'=>$_GET['task_describe'],
			'update_time'=>time(),
			'start_time'=>$start,
			'end_time'=>$end,
		);
		if(empty($tid)){		
			$updatearray['create_time']=time();
			C::t('#gfarm#gfarm_act_task')->insert($updatearray);
		}else{				
			C::t('#gfarm#gfarm_act_task')->update($tid,$updatearray);	
			if(!empty($_GET['visible'])){
				$dailyact=C::t('#gfarm#gfarm_daily_act')->fetch_first_data(" and visible=0 and task_id=".$tid);
				if(!empty($dailyact)){
					C::t('#gfarm#gfarm_daily_act')->update($dailyact['id'],array(
						'visible'=>1,
					));
				}
			}
		}		
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}	
	$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and small_type=12');		
	$items=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type!=11');
	foreach ($items as $key=>$value) {
		$sep[$key]=C::t('#gfarm#gfarm_goods_separate')->fetch($value['big_type']);
		if(!empty($value['small_type'])){
			$sep[$key]=C::t('#gfarm#gfarm_goods_separate')->fetch($value['small_type']);
		}
	}
	include template('gfarm:cp/task/newacttask');
	exit;
}
$text='';
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$start=($currpage-1)*$perpage;
$num=C::t('#gfarm#gfarm_act_task')->count_all_data($str);		
$taskInfos=C::t('#gfarm#gfarm_act_task')->fetch_all_data($str,$start,$perpage);
foreach ($taskInfos as $key=>$value) {
	if(!empty($value['target_object'])){
		$objects[$key]=C::t('#gfarm#gfarm_goods')->fetch($value['target_object']);
	}
}
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/task/acttask');

?>