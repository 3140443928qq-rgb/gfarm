<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once 'source/function/function_plugin.php';

function table_exists($tablename) {
	global $_G;
	$dbname=$_G['config']['db'][1]['dbname'];
	$query=DB::query("SHOW TABLES FROM $dbname");
	$tables=array();
	while ($row = mysql_fetch_array($query,MYSQL_ASSOC)) {
		foreach($row as $key => $val) {
			$tables[] = $val;
		}
	}
	$tablename=DB::table($tablename);
	return in_array($tablename, $tables);
}
function exitscolumn($field, $table) {
	$query=DB::query('SHOW COLUMNS FROM '.DB::table($table));
	$columns=array();
	while ($row = mysql_fetch_array($query,MYSQL_ASSOC)) {
		$columns[]=$row;
	}
	$arraycolumns=array();
	foreach($columns as $v) {
		$arraycolumns[]=$v['Field'];
	}
	return in_array($field,$arraycolumns) ? TRUE:FALSE;
}
if(!table_exists('gfarm_lock')){
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_gfarm_lock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `land_num` int(11) NOT NULL,
  `money_type` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
ALTER TABLE `pre_gfarm_lock` AUTO_INCREMENT=100001;
EOF;
runquery($sql,'SILENT');	
}
if(!table_exists('gfarm_lock_log')){
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_gfarm_lock_log` (
 `id` INT( 11 ) NOT NULL AUTO_INCREMENT,
 `uid` INT( 11 ) NOT NULL ,
 `land_num` INT( 11 ) NOT NULL ,
 `money_type` INT( 11 ) NOT NULL ,
 `money` INT( 11 ) NOT NULL ,
 `create_time` INT( 11 ) NOT NULL,
 PRIMARY KEY (`id`)
);
ALTER TABLE `gfarm_lock_log` AUTO_INCREMENT=100001;
EOF;
runquery($sql,'SILENT');	
}
if(!table_exists('gfarm_food')){
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_gfarm_food` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_id` int(11) NOT NULL,
  `addexp` int(11) NOT NULL,
  `addbene` int(11) NOT NULL,
  `addstren` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ;
ALTER TABLE `pre_gfarm_food` AUTO_INCREMENT=100001;
EOF;
runquery($sql,'SILENT');
$sql=lang('plugin/gfarm','install4');
runquery($sql,'SILENT');
}
if(!exitscolumn('outstyle','gfarm_exchange_log')){
$sql = <<<EOF
ALTER TABLE `pre_gfarm_exchange_log` ADD `outstyle` int(11) NOT NULL,
ADD `exnum` int(11) NOT NULL,
ADD `usermoney` int(11) NOT NULL;
EOF;
runquery($sql,'SILENT');	
}
if(!exitscolumn('reshopmoney','gfarm_vip')){
$sql = <<<EOF
ALTER TABLE  `pre_gfarm_vip` ADD  `reshopmoney` INT NOT NULL ,
ADD  `relockmoney` INT NOT NULL ,
ADD  `restealnumber` INT NOT NULL ,
ADD  `restealodd` INT NOT NULL ,
ADD  `addstealnumber` INT NOT NULL ,
ADD  `openget` INT NOT NULL ,
ADD  `opensteal` INT NOT NULL ,
ADD  `addstrength` INT NOT NULL;
EOF;
runquery($sql,'SILENT');	
}
if(!exitscolumn('openindex','gfarm_vip')){
$sql = <<<EOF
ALTER TABLE  `pre_gfarm_vip` ADD  `openindex` INT NOT NULL;
EOF;
runquery($sql,'SILENT');	
$sql=lang('plugin/gfarm','install5');
runquery($sql,'SILENT');
}
$card=DB::fetch_all("select * from %t where mod_en='exout'", array('gfarm_set'));
if(empty($card)){
	$sql=lang('plugin/gfarm','install3');
	runquery($sql,'SILENT');
}
if(!table_exists('gfarm_land_all_log')){
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_gfarm_land_all_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
ALTER TABLE `pre_gfarm_land_all_log` AUTO_INCREMENT=100001;
EOF;
runquery($sql,'SILENT');	
$users=C::t('#gfarm#gfarm_member')->fetch_all_data();
foreach ($users as $value) {
	$str=' and uid='.$value['uid'];
	$str1=$str.' and type=2';
	$landcl1=C::t('#gfarm#gfarm_land_log')->fetch_clname($str1);
	foreach ($landcl1 as $value1) {
		$strr=$str1.' and good_id='.$value1['good_id'];
		$cland1=C::t('#gfarm#gfarm_land_log')->count_num($strr);//种次数
		C::t('#gfarm#gfarm_land_all_log')->insert(array(
			'uid'=>$value['uid'],
			'type'=>1,	
			'goods_id'=>$value1['good_id'],
			'number'=>$cland1,
			'create_time'=>time(),
		));
	}
	$str2=$str.' and type=3';
	$landcl2=C::t('#gfarm#gfarm_land_log')->fetch_clname($str2);
	foreach ($landcl2 as $value1) {
		$strr=$str2.' and good_id='.$value1['good_id'];
		$cland1=C::t('#gfarm#gfarm_land_log')->count_num($strr);//收获次数
		C::t('#gfarm#gfarm_land_all_log')->insert(array(
			'uid'=>$value['uid'],
			'type'=>2,	
			'goods_id'=>$value1['good_id'],
			'number'=>$cland1,
			'create_time'=>time(),
		));
		$cland2=C::t('#gfarm#gfarm_land_log')->sum_num($strr);//收获个数
		C::t('#gfarm#gfarm_land_all_log')->insert(array(
			'uid'=>$value['uid'],
			'type'=>3,	
			'goods_id'=>$value1['good_id'],
			'number'=>$cland2,
			'create_time'=>time(),
		));
	}
	$str3=$str.' and type=6';
	$landcl3=C::t('#gfarm#gfarm_land_log')->fetch_clname($str3);
	foreach ($landcl3 as $value1) {
		$strr=$str3.' and good_id='.$value1['good_id'];
		$cland1=C::t('#gfarm#gfarm_land_log')->count_num($strr);//偷次数
		C::t('#gfarm#gfarm_land_all_log')->insert(array(
			'uid'=>$value['uid'],
			'type'=>4,	
			'goods_id'=>$value1['good_id'],
			'number'=>$cland1,
			'create_time'=>time(),
		));
		$cland2=C::t('#gfarm#gfarm_land_log')->sum_num($strr);//偷个数
		C::t('#gfarm#gfarm_land_all_log')->insert(array(
			'uid'=>$value['uid'],
			'type'=>5,	
			'goods_id'=>$value1['good_id'],
			'number'=>$cland2,
			'create_time'=>time(),
		));
	}
	$str4=$str.' and type=1';
	$landcl4=C::t('#gfarm#gfarm_depot_log')->fetch_clname($str4);
	foreach ($landcl4 as $value1) {
		$strr=$str4.' and goods_id='.$value1['goods_id'];
		$cland1=C::t('#gfarm#gfarm_depot_log')->count_num($strr);//买个数
		C::t('#gfarm#gfarm_land_all_log')->insert(array(
			'uid'=>$value['uid'],
			'type'=>6,	
			'goods_id'=>$value1['goods_id'],
			'number'=>$cland1,
			'create_time'=>time(),
		));
		$strr.=' and money_type='.$value1['money_type'];
		$cland2=C::t('#gfarm#gfarm_depot_log')->sum_num($strr);//买金钱
		$buylandarr=array(
			'uid'=>$value['uid'],
			'goods_id'=>$value1['goods_id'],
			'number'=>$cland2,
			'create_time'=>time(),
		);
		if(empty($value1['money_type'])){
			$buylandarr['type']=7;
		}else{
			$buylandarr['type']=10;
		}
		C::t('#gfarm#gfarm_land_all_log')->insert($buylandarr);		
	}
	$str5=$str.' and type=2';
	$landcl5=C::t('#gfarm#gfarm_depot_log')->fetch_clname($str5);
	foreach ($landcl5 as $value1) {
		$strr=$str5.' and goods_id='.$value1['goods_id'];
		$cland1=C::t('#gfarm#gfarm_depot_log')->count_num($strr);//卖个数
		C::t('#gfarm#gfarm_land_all_log')->insert(array(
			'uid'=>$value['uid'],
			'type'=>8,	
			'goods_id'=>$value1['goods_id'],
			'number'=>$cland1,
			'create_time'=>time(),
		));
		$cland2=C::t('#gfarm#gfarm_depot_log')->sum_num($strr);//卖金钱
		C::t('#gfarm#gfarm_land_all_log')->insert(array(
			'uid'=>$value['uid'],
			'type'=>9,	
			'goods_id'=>$value1['goods_id'],
			'number'=>$cland2,
			'create_time'=>time(),
		));
	}
}
}
$finish = TRUE;
?>