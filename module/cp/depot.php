<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str="";
$str1="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$did=$_GET['did'];
$start=($currpage-1)*$perpage;
if($_GET['act']=='lookp'){	
	$formula=C::t('#gfarm#gfarm_member_formula')->fetch_first_data(' and depot_id='.$did);
	include template('gfarm:cp/depot/formula');
	exit;
}
if($_GET['act']=='lookn'){	
	$certificate=C::t('#gfarm#gfarm_land_skill')->fetch_first_data(' and depot_id='.$did);
	include template('gfarm:cp/depot/certificate');
	exit;
}
if($_GET['act']=='update'){	
	$depot=C::t('#gfarm#gfarm_depot')->fetch($did);
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['number'],$result1); 
		if(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','023')."');";
			echo "</script>";
			exit;
		}	
		C::t('#gfarm#gfarm_depot')->update($did,array(
			'number'=>$_GET['number'],
			'islock'=>$_GET['islock'],
			'update_time'=>time(),
		));
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	include template('gfarm:cp/user/updatedepot');
	exit;
}
$uid=$_GET['uid'];
$goodname=$_GET['goodname'];
$tt='/^([1-9]+[0-9]*)$/';
preg_match($tt,$uid,$result); 
if(!empty($result)){
	$str.=' and uid='.$_GET['uid'];
	$str1.=' and uid='.$_GET['uid'];
	$text.='&uid='.$_GET['uid'];
}else{
	$uid='';
}
if(!empty($goodname)){
	$str1.=" and name like '%".$goodname."%'";
	$str.=" and name like '%".$goodname."%'";
	$text.='&goodname='.$goodname;
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
$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data($str,$start,$perpage);
$num=C::t('#gfarm#gfarm_depot')->count_all_data($str1);
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/user/depot');
	

?>