<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$currentuser['experience'].' order by level');
if(empty($nextlevel)){
	$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
}
$str=' and uid='.$_G['uid']." and FROM_UNIXTIME(create_time,'%Y%m%d')=".date("Ymd").' order by create_time desc';
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$start=($currpage-1)*$perpage;
$num=C::t('#gfarm#gfarm_land_log')->count_all_data($str);	
$ispage=ceil($num/$perpage)==0?1:ceil($num/$perpage);
$mylogs=C::t('#gfarm#gfarm_land_log')->fetch_all_data($str,$start,$perpage);
foreach ($mylogs as $key=> $value) {
	$stealuser[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['target_uid']);
}
$str1=$str2=$currentuser['experience'].'/'.$nextlevel['experience'];
strlen($str1);
if(strlen($str1)>10){
	$str1=substr($str1, 0,10).'..';
}
if($_GET['act']=='ajax'){
	include template('gfarm:ajax/myajax');
	exit;
}
include template('gfarm:front/myself');

?>