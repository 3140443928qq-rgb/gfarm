<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$do=$_GET['do'];
$goodid=$_GET['goodid'];
$good=C::t('#gfarm#gfarm_goods')->fetch($goodid);
$tflag=0;
if(!empty($good['add_date'])){
	$goodupdate=C::t('#gfarm#gfarm_goods_update')->fetch_first_data(' and goods_id='.$goodid);
	$timecount=floor((time()-$goodupdate['update_time'])/($good['add_date']*60*60));
	if(!empty($timecount)){
		C::t('#gfarm#gfarm_goods_update')->update($goodupdate['id'],array(
			'still_num'=>$good['sale_count'],
			'update_time'=>$goodupdate['update_time']+$good['add_date']*60*60*$timecount,
		));
		$goodupdate=C::t('#gfarm#gfarm_goods_update')->fetch_first_data(' and goods_id='.$goodid);
	}
	$tflag=1;
}
if($_GET['act']=='showgoods'){
	if($good['use_level']>$mylevel['level']){
		exit;
	}
	if($good['small_type']=='13'){
		$do=$good['small_type'];
	}else{
		$do=$good['big_type'];
	}
	$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($do);	
	if($do=='9'){
		$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$goodid);
	}
	if($do=='1'){
		$equip=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$goodid);
	}
	if($do=='4'){
		$deco=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$goodid);
	}
	if($do=='19'){
		$item=C::t('#gfarm#gfarm_items')->fetch_first_data(' and relation_id='.$goodid);
	}
	include template('gfarm:front/shopgoods');
	exit;
}
if($_GET['act']=='buy'){
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		if(!in_array($good['big_type'], array(1,4))){
			preg_match($tt,$_GET['dnum'],$result);
			if($_GET['dnum']<0||empty($result)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','023')."');";
				echo "</script>";
				exit;
			}
			$edflag=0;
		}else{
			$equipordeco=C::t('#gfarm#gfarm_depot')->fetch_first_data(' and uid='.$_G['uid'].' and goods_id='.$goodid);
			if(!empty($equipordeco)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','113')."');";
				echo "</script>";
				exit;
			}
			$_GET['dnum']=1;
			$edflag=1;
		}		
		if(empty($good['money_typeb'])){
			$money=$user['money'];
		}else{
			$money=$user_credit;
		}
		$buymoney=$_GET['dnum']*$good['buy_price'];
		if(!empty($myvip)){
			if(!empty($myvip['reshopmoney'])){
				$buymoney=ceil($buymoney*(1-$myvip['reshopmoney']*0.01));
			}
		}
		if($money<$buymoney){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','095')."');";
			echo "</script>";
			exit;
		}
		if(!empty($good['add_date'])){
			if($goodupdate['still_num']<$_GET['dnum']){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','110')."');";
				echo "</script>";
				exit;
			}
			C::t('#gfarm#gfarm_goods_update')->update($goodupdate['id'],array(
				'still_num'=>$goodupdate['still_num']-$_GET['dnum'],
			));
		}
		$depotlog=array(
			'uid'=>$_G['uid'],
			'type'=>1,
			'goods_id'=>$good['id'],
			'goods_number'=>$_GET['dnum'],
			'money_type'=>$good['money_typeb'],
			'money_price'=>-$buymoney,
			'create_time'=>time(),
		);
		C::t('#gfarm#gfarm_depot_log')->insert($depotlog);		
		$landalllogarr=array(
			'uid'=>$_G['uid'],
			'type'=>6,
			'goods_id'=>$good['id'],
			'create_time'=>time(),
		);
		$landalllog=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=6 and uid='.$_G['uid'].' and goods_id='.$good['id']);
		if(empty($landalllog)){
			$landalllogarr['number']=$_GET['dnum'];
			C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr);
		}else{
			$landalllogarr['number']=$landalllog['number']+$_GET['dnum'];
			C::t('#gfarm#gfarm_land_all_log')->update($landalllog['id'],$landalllogarr);
		}
		if(empty($good['money_typeb'])){
			$landalllogarr1=array(
				'uid'=>$_G['uid'],
				'type'=>7,
				'goods_id'=>$good['id'],
				'create_time'=>time(),
			);
			$landalllog1=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=7 and uid='.$_G['uid'].' and goods_id='.$good['id']);
			if(empty($landalllog1)){
				$landalllogarr1['number']=-$buymoney;
				C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr1);
			}else{
				$landalllogarr1['number']=$landalllog1['number']-$buymoney;
				C::t('#gfarm#gfarm_land_all_log')->update($landalllog1['id'],$landalllogarr1);
			}
		}else{
			$landalllogarr1=array(
				'uid'=>$_G['uid'],
				'type'=>10,
				'goods_id'=>$good['id'],
				'create_time'=>time(),
			);
			$landalllog1=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=10 and uid='.$_G['uid'].' and goods_id='.$good['id']);
			if(empty($landalllog1)){
				$landalllogarr1['number']=-$buymoney;
				C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr1);
			}else{
				$landalllogarr1['number']=$landalllog1['number']-$buymoney;
				C::t('#gfarm#gfarm_land_all_log')->update($landalllog1['id'],$landalllogarr1);
			}
		}		
		if(empty($good['money_typeb'])){
			$flag=0;			
			C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
				'money'=>$user['money']-$buymoney,
				'last_visit'=>time(),
			));			
			$money=$user['money']-$buymoney;
			$msg=lang('plugin/gfarm','111').$good['name'].$_GET['dnum'].lang('plugin/gfarm','114').$config['moneyname'].$buymoney;
		}else{
			$flag=1;
			C::t("common_member_count")->increase($user['uid'],array('extcredits'.$config['bullionstype']=>-$buymoney));
			$money = DB::result_first($sql,array('common_member_count',$_G['uid']));
			$msg=lang('plugin/gfarm','111').$good['name'].$_GET['dnum'].lang('plugin/gfarm','114').$config['bullionsname'].$buymoney;
		}
		$rand=$_GET['dnum'];
		$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$goodid);
		//仓库堆叠
		adddepotfile($depots,$rand,$good['depot_pile'],$goodid,$_G['uid']);
		echo "<script>";
		echo "parent.window.hideWindow('gfarm1');";
		echo "parent.window.showsajax('".$msg."',".$do.",".$money.",".$flag.",".$goodid.",".($goodupdate['still_num']-$_GET['dnum']).",".$tflag.",".$edflag.");";
		echo "</script>";
		exit;
	}	
}
$str=' and sale_count!=0';
if(empty($do)){//种子
	$do=9;
}
$separat=C::t('#gfarm#gfarm_goods_separate')->fetch($do);
if($do==13){
	$str.=' and small_type='.$do.' order by use_level';
}else{
	$str.=' and big_type='.$do.' order by use_level';
}
$shopInfos=C::t('#gfarm#gfarm_goods')->fetch_all_data($str);
foreach ($shopInfos as $key=>$value) {
	if($value['use_level']<=$mylevel['level']){
		$tshop[$key]=1;
	}
	$goodupdate=C::t('#gfarm#gfarm_goods_update')->fetch_first_data(' and goods_id='.$value['id']);
	if(!empty($value['small_type'])){
		$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($value['small_type']);
	}else{
		$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($value['big_type']);
	}
	if($do=='9'||empty($do)){
		$datas[$key]=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$value['id']);
	}elseif($do=='1'){
		$datas[$key]=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$value['id']);
	}elseif($do=='4'){
		$datas[$key]=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$value['id']);
	}elseif($do=='19'){
		$datas[$key]=C::t('#gfarm#gfarm_items')->fetch_first_data(' and relation_id='.$value['id']);
	}
	if(in_array($do, array(1,4))){
		$equipordeco=C::t('#gfarm#gfarm_depot')->fetch_first_data(' and uid='.$_G['uid'].' and goods_id='.$value['id']);
		if(!empty($equipordeco)){
			$equipordecos[$key]=1;
		}
	}
	$separates[$key]=$separate;
	$timecount=floor((time()-$goodupdate['update_time'])/($value['add_date']*60*60));
	if(!empty($timecount)){
		C::t('#gfarm#gfarm_goods_update')->update($goodupdate['id'],array(
			'still_num'=>$value['sale_count'],
			'update_time'=>$goodupdate['update_time']+$value['add_date']*60*60*$timecount,
		));
		$goodupdate=C::t('#gfarm#gfarm_goods_update')->fetch_first_data(' and goods_id='.$value['id']);
	}
	$goodupdates[$key]=$goodupdate;
}
if($_GET['act']=='ajax'){
	include template('gfarm:ajax/shopajax');
	exit;
}

include template('gfarm:front/shop');

?>