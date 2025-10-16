<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$do=$_GET['do'];
if(empty($do)){
	$do='experience';
}
$str=' order by '.$do.' desc';
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=10;
$start=($currpage-1)*$perpage;
$num=C::t('#gfarm#gfarm_member')->count_all_data();	
$ispage=ceil($num/$perpage)==0?1:ceil($num/$perpage);
$allusers=C::t('#gfarm#gfarm_member')->fetch_all_data($str,$start,$perpage);
$alluser=C::t('#gfarm#gfarm_member')->fetch_all_data($str);
foreach ($allusers as $key=> $value) {
	$level=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$value['experience'].' order by level desc');
	$levels[$key]=$level['level'];
}
foreach ($alluser as $key=> $value) {
	if($value['uid']==$_G['uid']){
		$mysort=$key+1;
	}
}

if($_GET['act']=='ajax'){
	if($do=='experience'){
		include template('gfarm:ajax/levelajax');
	}elseif($do=='money'){
		include template('gfarm:ajax/moneyajax');
	}else{
		include template('gfarm:ajax/benevolenceajax');
	}	
	exit;
}
include template('gfarm:front/rank');

?>