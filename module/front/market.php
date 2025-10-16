<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$do=$_GET['do'];
$str='';
$depotid=$_GET['depotid'];
$depot=C::t('#gfarm#gfarm_depot')->fetch($depotid);	
$good=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);	
if($_GET['act']=='showgoods'){	
	$sale=C::t('#gfarm#gfarm_sale')->fetch_first_data(' and depot_id='.$depotid);	
	$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($good['big_type']);	
	if(!empty($good['small_type'])){
		$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($good['small_type']);	
	}
	$do=$good['big_type'];
	if($do=='9'){
		$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$depot['goods_id']);
	}
	include template('gfarm:front/marketgoods');
	exit;
}
if($_GET['act']=='buy'){
	if($_GET['formhash']==formhash()){
		$sale=C::t('#gfarm#gfarm_sale')->fetch_first_data(' and depot_id='.$depotid);	
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['dnum'],$result);
		if($_GET['dnum']<0||empty($result)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','023')."');";
			echo "</script>";
			exit;
		}
		if($user['money']<$_GET['dnum']*$sale['good_price']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','095')."');";
			echo "</script>";
			exit;
		}
		if($depot['number']<$_GET['dnum']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','110')."');";
			echo "</script>";
			exit;
		}
		//对方日志
		if($depot['number']>$_GET['dnum']){
			$tflag=0;
			C::t('#gfarm#gfarm_depot')->update($depot['id'],array(
				'number'=>$depot['number']-$_GET['dnum'],
				'update_time'=>time(),
			));
		}else{
			$tflag=1;
			C::t('#gfarm#gfarm_depot')->delete($depot['id']);
			C::t('#gfarm#gfarm_sale')->delete($sale['id']);
		}
		$currentuser=C::t('#gfarm#gfarm_member')->fetch_by_uid($depot['uid']);
		C::t('#gfarm#gfarm_member')->update($depot['uid'],array(
			'money'=>$currentuser['money']+$_GET['dnum']*$sale['good_price'],
			'last_visit'=>time(),
		));
		C::t('#gfarm#gfarm_depot_log')->insert(array(
			'uid'=>$depot['uid'],
			'type'=>4,
			'goods_id'=>$good['id'],
			'goods_number'=>$_GET['dnum'],
			'money_type'=>0,
			'money_price'=>$_GET['dnum']*$sale['good_price'],
			'create_time'=>time(),
		));
		//自己日志
		C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
			'money'=>$user['money']-$_GET['dnum']*$sale['good_price'],
			'last_visit'=>time(),
		));
		$money=$user['money']-$_GET['dnum']*$sale['good_price'];
		$msg=lang('plugin/gfarm','111').$_GET['dnum']*$sale['good_price'];
		C::t('#gfarm#gfarm_depot_log')->insert(array(
			'uid'=>$_G['uid'],
			'type'=>3,
			'goods_id'=>$good['id'],
			'goods_number'=>$_GET['dnum'],
			'money_type'=>0,
			'money_price'=>-$_GET['dnum']*$sale['good_price'],
			'create_time'=>time(),
		));
		$rand=$_GET['dnum'];
		$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$good['id']);
		//仓库堆叠
		adddepotfile($depots,$rand,$good['depot_pile'],$good['id'],$_G['uid']);
		echo "<script>";
		echo "parent.window.hideWindow('gfarm1');";
		echo "parent.window.showsajax('".$msg."',".$money.",".$depot['id'].",".($depot['number']-$_GET['dnum']).",".$tflag.");";
		echo "</script>";
		exit;
	}	
}
if(empty($do)){//种子
	$do=11;
}
$str.=' and uid!='.$_G['uid'].' and big_type='.$do;
$str.=' order by use_level';
$shopInfos=C::t('#gfarm#gfarm_sale')->fetch_all_data($str);
foreach ($shopInfos as $key=>$value) {
	$users[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['uid']);
	if(!empty($value['small_type'])){
		$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($value['small_type']);
	}else{
		$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($value['big_type']);
	}
	$separates[$key]=$separate['separate_name'];
}
if($_GET['act']=='ajax'){
	include template('gfarm:ajax/marketajax');
	exit;
}
include template('gfarm:front/market');

?>