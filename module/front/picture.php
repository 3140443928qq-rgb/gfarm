<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
if($_GET['act']=='showajax'){		
	$germ=C::t('#gfarm#gfarm_germ')->fetch($_GET['gid']);
	$germcrop=C::t('#gfarm#gfarm_germ_crop')->fetch_first_data(' and germ_id='.$germ['relation_id']);
	$good=C::t('#gfarm#gfarm_goods')->fetch($germ['relation_id']);
	include template('gfarm:ajax/showpic');
	exit;
}
$str=' and big_type=9 order by use_level';
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=16;
$start=($currpage-1)*$perpage;
$num=C::t('#gfarm#gfarm_germ')->count_all_data();	
$ispage=ceil($num/$perpage)==0?1:ceil($num/$perpage);
$cropInfos=C::t('#gfarm#gfarm_germ')->fetch_all_data($str,$start,$perpage);
$mycrops=C::t('#gfarm#gfarm_member_picture')->fetch_all_data(' and uid='.$_G['uid']);
foreach ($mycrops as $value) {
	$mycrop[$value['crop_id']]=1;
}
if($_GET['act']=='ajax'){		
	include template('gfarm:ajax/picajax');
	exit;
}
include template('gfarm:front/picture');

?>