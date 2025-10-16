<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str='';
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=8;
$start=($currpage-1)*$perpage;
if(!empty($_GET['name'])){
	$str.=" and username like '%".$_GET['name']."%'";
}
if($_GET['act']=='collect'){
	$users=C::t('#gfarm#gfarm_member_collect')->fetch_all_data(' and a.uid='.$_G['uid'].$str.' order by experience desc',$start,$perpage);
	foreach ($users as $key=>$value) {
		$levels[$key]=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$value['experience'].' order by level desc');
	}
	$cuser=C::t('#gfarm#gfarm_member_collect')->count_all_data();
	$cnum=ceil(count($cuser)/$perpage);
	if(empty($cnum)){
		$cnum=1;
	}
	include template('gfarm:ajax/friendcoajax');
	exit;
}
if($_GET['act']=='decollect'){
	$colluser=C::t('#gfarm#gfarm_member_collect')->fetch_first_data(' and uid='.$_G['uid'].' and receive_uid='.$_GET['cuid']);
	C::t('#gfarm#gfarm_member_collect')->delete($colluser['id']);
	$users=C::t('#gfarm#gfarm_member_collect')->fetch_all_data(' and a.uid='.$_G['uid'].$str.' order by experience desc',$start,$perpage);
	foreach ($users as $key=>$value) {
		$levels[$key]=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$value['experience'].' order by level desc');
	}
	$cuser=C::t('#gfarm#gfarm_member_collect')->count_all_data();
	$cnum=ceil(count($cuser)/$perpage);
	if(empty($cnum)){
		$cnum=1;
	}
	include template('gfarm:ajax/friendcoajax');
	exit;
}
$cuser=C::t('#gfarm#gfarm_member')->count_all_data($str);
$cnum=ceil($cuser/$perpage);
if(empty($cnum)){
	$cnum=1;
}
$ordertype=$_GET['ordertype'];
if(empty($ordertype)){
	$ordertype='experience';
}
$str.=' order by '.$ordertype.' desc';
$users=C::t('#gfarm#gfarm_member')->fetch_all_data($str,$start,$perpage);
foreach ($users as $key=>$value) {
	$levels[$key]=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$value['experience'].' order by level desc');
}
$allusers=C::t('#gfarm#gfarm_member')->fetch_all_data($str);
foreach ($allusers as $key=>$value) {
	if($value['uid']==$_G['uid']){
		$mysort=$key+1;
	}
}
if($_GET['act']=='ajax'){
	include template('gfarm:ajax/friendbeajax');
	exit;
}
include template('gfarm:ajax/friendajax');

?>