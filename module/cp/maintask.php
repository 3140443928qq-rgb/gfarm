<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
if($_GET['act']=='update'){
	$tid=$_GET['tid'];
	$task=C::t('#gfarm#gfarm_main_task')->fetch($tid);
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['target_num'],$result0); 
		preg_match($tt,$_GET['reward_money'],$result1); 	
		preg_match($tt,$_GET['items_num'],$result3); 	
		preg_match($tt,$_GET['reward_bene'],$result4); 
		preg_match($tt,$_GET['reward_exp'],$result5); 	
		if(empty($_GET['task_name'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','002')."');";
			echo "</script>";
			exit;
		}else{
			$countname=C::t('#gfarm#gfarm_main_task')->count_all_data(" and task_name='".$_GET['task_name']."'");
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
		if(empty($result0)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','005')."');";
			echo "</script>";
			exit;
		}
		if(empty($_GET['reward_money'])&&empty($_GET['reward_items'])&&empty($_GET['reward_bene'])&&empty($_GET['reward_exp'])){
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
			'task_way'=>$_GET['task_way'],	
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
		);
		if(empty($tid)){		
			$updatearray['create_time']=time();
			if(!empty($_GET['aimed'])){
				C::t('#gfarm#gfarm_main_task')->update_by_where(' and id>='.$_GET['aimed']);
				$updatearray['id']=$_GET['aimed'];
			}
			C::t('#gfarm#gfarm_main_task')->insert($updatearray);
		}else{				
			C::t('#gfarm#gfarm_main_task')->update($tid,$updatearray);			
		}
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	if(!empty($task)){
		$way=C::t('#gfarm#gfarm_task_way')->fetch($task['task_way']);
		if($way['way_type']==1){
			$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=9');
		}else if($way['way_type']==2){
			$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and small_type=12');
		}else if($way['way_type']==3){
			$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=19');
		}
	}
	$mtasks=C::t('#gfarm#gfarm_main_task')->fetch_all_data(' and visible=0 order by id');
	$taskways=C::t('#gfarm#gfarm_task_way')->fetch_all_data(' and achieve_flag=0 order by id');
	$items=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type!=11');
	foreach ($items as $key=>$value) {
		$sep[$key]=C::t('#gfarm#gfarm_goods_separate')->fetch($value['big_type']);
		if(!empty($value['small_type'])){
			$sep[$key]=C::t('#gfarm#gfarm_goods_separate')->fetch($value['small_type']);
		}
	}
	include template('gfarm:cp/task/newmaintask');
	exit;
}
if($_GET['act']=='targetajax'){
	$tar=$_GET['tar'];
	if($tar==1){
		$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=9');
	}else if($tar==2){
		$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and small_type=12');
	}else if($tar==3){
		$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=19');
	}
	include template('gfarm:cp/task/taskajax');
	exit;
}
if($_GET['act']=='targetajax'){
	$tar=$_GET['tar'];
	if($tar==1){
		$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=9');
	}else if($tar==2){
		$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and small_type=12');
	}else if($tar==3){
		$object=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=19');
	}
	include template('gfarm:cp/task/taskajax');
	exit;
}
$str=' order by ';
$text='';
$orderflag=$_GET['orderflag'];
$ordertype=$_GET['ordertype'];
if(empty($ordertype)){
	$ordertype='id';
}
$str.=$ordertype;
$text.='&ordertype='.$ordertype;
if(empty($orderflag)){
	$str.=' desc';
	$orderflag=1;
}else{
	$orderflag=0;
}
$text.='&orderflag='.$_GET['orderflag'];
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$start=($currpage-1)*$perpage;
$num=C::t('#gfarm#gfarm_main_task')->count_all_data($str);		
$taskInfos=C::t('#gfarm#gfarm_main_task')->fetch_all_data($str,$start,$perpage);
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
include template('gfarm:cp/task/maintask');

?>