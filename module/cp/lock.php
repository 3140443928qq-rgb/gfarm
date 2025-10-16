<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$lid=$_GET['lid'];
$start=($currpage-1)*$perpage;
if($_GET['act']=='update'){		
	$lock=C::t('#gfarm#gfarm_lock')->fetch($lid);
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		$tt1='/^(([1-9]+[0-9]*)|[0])$/';
		preg_match($tt,$_GET['land_num'],$result1); 
		preg_match($tt1,$_GET['money'],$result2); 
		if(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','078')."');";
			echo "</script>";
			exit;
		}	
		$count=C::t('#gfarm#gfarm_lock')->count_all_data(' and land_num='.$_GET['land_num']);
		if(empty($lid)){
			if(!empty($count)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','099')."');";
				echo "</script>";
				exit;	
			}
		}else{
			if($lock['land_num']!=$_GET['land_num']){
				if(!empty($count)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','099')."');";
					echo "</script>";
					exit;
				}
			}
		}
		
		if(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','116')."');";
			echo "</script>";
			exit;
		}	
		$updatearr=array(
			'land_num'=>$_GET['land_num'],
			'money_type'=>$_GET['money_type'],
			'money'=>$_GET['money'],
			'visible'=>$_GET['visible'],
		);
		if(empty($lid)){
			$updatearr['create_time']=time();
			C::t('#gfarm#gfarm_lock')->insert($updatearr);
		}else{
			C::t('#gfarm#gfarm_lock')->update($lid,$updatearr);
		}		
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}	
	include template('gfarm:cp/set/newlock');
	exit;
}
$do=$_GET['do'];
if(empty($do)){
	$str=' order by id desc';
	$do=1;
	$text="&do=0";
}else{
	$str=' order by id';
	$do=0;
	$text="&do=1";
}
$lockInfos=C::t('#gfarm#gfarm_lock')->fetch_all_data($str,$start,$perpage);
$num=C::t('#gfarm#gfarm_lock')->count_all_data();
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/set/lock');
	

?>