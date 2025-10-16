<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
if($_GET['act']=='clear'){	
	DB::delete(C::t('#gfarm#gfarm_member'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_combin'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_collect'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_collect'), ' receive_uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_signin'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_signin_reward'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_act'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_daily_task'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_main_task'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_achieve_task'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_picture'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_formula'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_depot'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_depot_log'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_formula_log'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_member_land'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_operat_land'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_exchange_log'), ' uid='.$_GET['uid']);
	DB::delete(C::t('#gfarm#gfarm_land_log'), ' uid='.$_GET['uid'].' and target_uid='.$_GET['uid']);
}
$str="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$uid=$_GET['uid'];
$start=($currpage-1)*$perpage;
if($_GET['act']=='update'){	
	$user=C::t('#gfarm#gfarm_member')->fetch($uid);
	if($_GET['formhash']==formhash()){
		$tt='/^[-+]?(([1-9]+[0-9]*)|[0])$/';
		preg_match($tt,$_GET['experience'],$result2); 
		preg_match($tt,$_GET['money'],$result3); 
		preg_match($tt,$_GET['now_strength'],$result4); 
		preg_match($tt,$_GET['benevolence'],$result5); 
		if(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','081')."');";
			echo "</script>";
			exit;
		}	
		if(empty($result3)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','077').$config['moneyname'].lang('plugin/gfarm','082')."');";
			echo "</script>";
			exit;
		}	
		if(empty($result4)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','083')."');";
			echo "</script>";
			exit;
		}	
		if(empty($result5)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','084')."');";
			echo "</script>";
			exit;
		}
		C::t('#gfarm#gfarm_member')->update($uid,array(
			'experience'=>$user['experience']+$_GET['experience'],
			'money'=>$user['money']+$_GET['money'],
			'now_strength'=>$user['now_strength']+$_GET['now_strength'],
			'benevolence'=>$user['benevolence']+$_GET['benevolence'],
			'status'=>$_GET['status'],
		));
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	
	include template('gfarm:cp/user/updateuser');
	exit;
}
$tt='/^([1-9]+[0-9]*)$/';
preg_match($tt,$uid,$result); 
if(!empty($result)){
	$str1=' and a.uid='.$uid;
	$str.=' and a.uid='.$uid;
	$text.='&uid='.$uid;
}else{
	$uid='';
}
$orderfield1=$_GET['orderfield'];
$ordertype1=$_GET['ordertype'];
if(empty($orderfield1)){
	$orderfield1='uid';
}
if($ordertype1=='asc'||empty($ordertype1)){
	$ordertype='desc';
	$ordertype1=='asc';	
}else{
	$ordertype='asc';
}
$str.=' order by '.$orderfield1.' '.$ordertype1;
$text.='&orderfield='.$orderfield1.'&ordertype='.$ordertype1;
$userInfos=C::t('#gfarm#gfarm_member')->fetch_all_data($str,$start,$perpage);
foreach ($userInfos as	$key=> $value) {
	$level=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$value['experience'].' order by level desc');
	$levels[$key]=$level['level'];
}

$num=C::t('#gfarm#gfarm_member')->count_all_data($str1);
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/user/user');
	

?>