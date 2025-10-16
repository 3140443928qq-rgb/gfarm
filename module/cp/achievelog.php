<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str='';
$text='';
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$start=($currpage-1)*$perpage;
$uid=$_GET['uid'];
$start_time=strtotime($_GET['start_time']);
$end_time=strtotime($_GET['end_time']);
$tt='/^([1-9]+[0-9]*)$/';
preg_match($tt,$uid,$result); 
if(!empty($result)){
	$str.=' and uid='.$uid;
	$text.='&uid='.$uid;
}else{
	$uid='';
}
if(!empty($start_time)){
	$str.=' and reward_time>='.$start_time;
	$text.='&start_time='.$_GET['start_time'];
}
if(!empty($end_time)){
	$str.=' and reward_time<='.$end_time;
	$text.='&end_time='.$_GET['end_time'];
}	
$ordertype1=$_GET['ordertype'];
if($ordertype1=='desc'||empty($ordertype1)){
	$ordertype='asc';
	$ordertype1='desc';	
}else{
	$ordertype='desc';
}
$str.=' order by reward_time '.$ordertype1;
$text.='&ordertype='.$ordertype1;
$num=C::t('#gfarm#gfarm_member_achieve_task')->count_all_data($str);	
$taskInfos=C::t('#gfarm#gfarm_member_achieve_task')->fetch_all_data($str,$start,$perpage);
foreach ($taskInfos as $key=>$value) {
	$users[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['uid']);
}
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/task/achievelog');

?>