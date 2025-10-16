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
	$land=C::t('#gfarm#gfarm_member_land')->fetch($lid);
	if($_GET['formhash']==formhash()){
		$tt='/^(([1-9]+[0-9]*)|[0])$/';
		$tt1='/^[+-]?(([1-9]+[0-9]*)|[0])$/';
		preg_match($tt,$_GET['land_number'],$result1); 
		preg_match($tt1,$_GET['add_gain_number'],$result2); 
		/*if(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('开垦种植次数填写错误');";
			echo "</script>";
			exit;
		}*/
		if(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','067')."');";
			echo "</script>";
			exit;
		}	
		C::t('#gfarm#gfarm_member_land')->update($lid,array(
			//'land_number'=>$_GET['land_number'],
			'add_gain_number'=>$_GET['add_gain_number'],
			'update_time'=>time(),
		));
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	include template('gfarm:cp/user/updateland');
	exit;
}
$uid=$_GET['uid'];
$tt='/^([1-9]+[0-9]*)$/';
preg_match($tt,$uid,$result); 
if(!empty($result)){
	$str.=' and uid='.$_GET['uid'];
	$str1=' and uid='.$_GET['uid'];
	$text.='&uid='.$_GET['uid'];
}else{
	$uid='';
}
$orderfield1=$_GET['orderfield'];
$ordertype1=$_GET['ordertype'];
if(empty($orderfield1)){
	$orderfield1='id';
}
if($ordertype1=='asc'||empty($ordertype1)){
	$ordertype='desc';
	$ordertype1=='asc';	
}else{
	$ordertype='asc';
}
$str.=' order by '.$orderfield1.' '.$ordertype1;
$text.='&orderfield='.$orderfield1.'&ordertype='.$ordertype1;
$landInfos=C::t('#gfarm#gfarm_member_land')->fetch_all_data($str,$start,$perpage);
$num=C::t('#gfarm#gfarm_member_land')->count_all_data($str1);
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/user/land');
	

?>