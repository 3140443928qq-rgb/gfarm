<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}

for ($i=0;$i<15;$i++){
	for ($j=0;$j<15;$j++){
		$map[$i][$j]=1;
	}
}
$landid=0;
for ($i=2;$i<6;$i++){
	$k=5-$i;
	for ($j=1;$j<$i;$j++){
		$landid=$landid+1;
		$test[$i][$k+$j*2]=$landid;;
	}
}
for ($i=6;$i<10;$i++){
	$k=$i-5;
	for ($j=0;$j<5;$j++){
		$landid=$landid+1;
		$test[$i][$k+$j*2]=$landid;
	}
}
for ($i=10;$i<14;$i++){
	$k=$i-5;
	for ($j=0;$j<14-$i;$j++){
		$landid=$landid+1;
		$test[$i][$k+$j*2]=$landid;
	}
}
$landInfos=C::t('#gfarm#gfarm_member_land')->fetch_all_data(' and uid='.$uid);
foreach ($landInfos as $value) {
	if(!empty($value['land_number'])&&$value['use_number']<=$value['land_number']){
		$landflag[$value['land_id']]=1;
	}
}
$users=C::t('#gfarm#gfarm_member')->fetch_all_data(' order by experience desc',0,8);
$allusers=C::t('#gfarm#gfarm_member')->fetch_all_data(' order by experience desc',0,8);
$cuser=C::t('#gfarm#gfarm_member')->count_all_data();
$cnum=ceil($cuser/8);
foreach ($users as $key=>$value) {
	$levels[$key]=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$value['experience'].' order by level desc');
}
$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$currentuser['experience'].' order by level');
if(empty($nextlevel)){
	$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
}
$allusers=C::t('#gfarm#gfarm_member')->fetch_all_data(' order by experience desc');
foreach ($allusers as $key=>$value) {
	if($value['uid']==$_G['uid']){
		$mysort=$key+1;
	}
}
foreach ($landInfos as $value) {
	$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$value['germ_id']);
	$germs[$value['land_id']]=$germ;	
}
$str=' and uid='.$uid.' and status=1 and big_type=4';
$mydecorates=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);
foreach ($mydecorates as $value) {
	if($value['small_type']=='5'){//告示牌
		$fived=$value;
	}
	if($value['small_type']=='6'){//地貌
		$sixd=$value;
	}
	if($value['small_type']=='7'){//屋子
		$sevend=$value;
	}
	if($value['small_type']=='8'){//宠物
		$eightd=$value;
	}
}
foreach ($landInfos as $value) {
	$landInfoss[$value['land_id']]=$value;
}
if($_GET['act']=='refresh'){
	include template('gfarm:ajax/refreshajax');
	exit;
}
if($_GET['act']=='ajax'){
	$hascollect=C::t('#gfarm#gfarm_member_collect')->fetch_first_data(' and uid='.$_G['uid'].' and receive_uid='.$uid);
	include template('gfarm:ajax/indexajax');
	exit;
}
include template('gfarm:front/index');

?>