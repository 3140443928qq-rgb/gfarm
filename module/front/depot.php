<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$depotid=$_GET['depotid'];
$do=$_GET['do'];
$str=' and uid='.$_G['uid'].' and big_type in(9,11,14,19) and status=0';
if($_GET['act']=='showgoods'){//物品详情
	$depot=C::t('#gfarm#gfarm_depot')->fetch($depotid);
	$good=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);
	if(in_array($good['small_type'], array(12,13))){
		$do=$good['small_type'];
	}else{
		$do=$good['big_type'];
	}	
	include template('gfarm:front/depotgoods');
	exit;
}
if($_GET['act']=='lock'){//单个锁定or解锁
	if($_GET['formhash']==formhash()){
		$depot=C::t('#gfarm#gfarm_depot')->fetch($depotid);
		$good=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);
		if(empty($depot['islock'])){
			$lock=1;
		}else{
			$lock=0;
		}
		C::t('#gfarm#gfarm_depot')->update($depotid,array(
		'islock'=>$lock,
		'update_time'=>time(),
		));
		if(in_array($good['small_type'], array(12,13))){
			$do=$good['small_type'];
			$str.=' and small_type='.$good['small_type'];
		}else{
			$do=$good['big_type'];
			$str.=' and big_type='.$good['big_type'];
		}
		$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);		
		foreach ($depotInfos as $key=> $value) {
			if(empty($value['islock'])){
				$flag=1;
			}				
		}
		include template('gfarm:ajax/suoajax');
		exit;
	}
}
if($_GET['act']=='allsuo'){//一键锁定or解锁
	if($_GET['formhash']==formhash()){		
		if(!empty($_GET['sflag'])){
			$lock=1;			
		}else{
			$lock=0;
			$flag=1;
		}
		if(in_array($do, array(12,13))){
			$str.=' and small_type='.$do;
		}else{
			$str.=' and big_type='.$do;
		}
		$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);		
		$yuan=0;
		$jin=0;
		foreach ($depotInfos as $key=> $value) {
			C::t('#gfarm#gfarm_depot')->update($value['id'],array(
				'islock'=>$lock,
				'update_time'=>time(),
			));
			if(empty($lock)){
				if($value['money_types']=='1'){
					$yuan+=$value['sale_price']*$value['number'];
				}else{
					$jin+=$value['sale_price']*$value['number'];
				}
			}				
		}
		include template('gfarm:ajax/allsuoajax');
		exit;
	}	
}
if($_GET['act']=='allsale'){//一键卖出
	if($_GET['formhash']==formhash()){
		if(in_array($do, array(12,13))){
			$str.=' and small_type='.$do;
		}else{
			$str.=' and big_type='.$do;
		}
		$str1=$str.' and islock=0';
		$str.=' and islock=1';
		$depotInfos1=C::t('#gfarm#gfarm_depot')->fetch_all_data($str1);
		$yuan=0;
		$jin=0;
		foreach ($depotInfos1 as $value) {
			if($value['money_types']=='1'){
				$yuan+=$value['sale_price']*$value['number'];
			}else{
				$jin+=$value['sale_price']*$value['number'];
			}
			$depotlog=array(
				'uid'=>$_G['uid'],
				'type'=>2,
				'goods_id'=>$value['goods_id'],
				'goods_number'=>$value['number'],
				'money_type'=>$value['money_types'],
				'money_price'=>$value['number']*$value['sale_price'],
				'create_time'=>time(),
			);
			C::t('#gfarm#gfarm_depot_log')->insert($depotlog);
			$landalllogarr=array(
				'uid'=>$_G['uid'],
				'type'=>8,
				'goods_id'=>$value['goods_id'],
				'create_time'=>time(),
			);		
			$landalllog=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=8 and uid='.$_G['uid'].' and goods_id='.$value['goods_id']);
			if(empty($landalllog)){
				$landalllogarr['number']=$value['number'];
				C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr);
			}else{
				$landalllogarr['number']=$landalllog['number']+$value['number'];
				C::t('#gfarm#gfarm_land_all_log')->update($landalllog['id'],$landalllogarr);
			}
			$landalllogarr1=array(
				'uid'=>$_G['uid'],
				'type'=>9,
				'goods_id'=>$value['goods_id'],
				'create_time'=>time(),
			);
			$landalllog1=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=9 and uid='.$_G['uid'].' and goods_id='.$value['goods_id']);
			if(empty($landalllog1)){
				$landalllogarr1['number']=$value['number']*$value['sale_price'];
				C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr1);
			}else{
				$landalllogarr1['number']=$landalllog1['number']+$value['number']*$value['sale_price'];
				C::t('#gfarm#gfarm_land_all_log')->update($landalllog1['id'],$landalllogarr1);
			}
			C::t('#gfarm#gfarm_depot')->delete($value['id']);			
		}
		C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
			'money'=>$user['money']+$jin,
		));
		C::t("common_member_count")->increase($user['uid'],array('extcredits'.$config['bullionstype']=>$yuan));
		$user=C::t('#gfarm#gfarm_member')->fetch_by_uid($_G['uid']);
		$user_credit = DB::result_first($sql,array('common_member_count',$_G['uid']));
		$issale=1;
		$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);
		include template('gfarm:ajax/depotajax');
		exit;
	}	
}
/*if($_GET['act']=='salemarket'){//市场卖出
	if($_GET['formhash']==formhash()){
		$depot=C::t('#gfarm#gfarm_depot')->fetch($depotid);
		$good=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);
		if($depot['status']==2){
			exit;
		}
		C::t('#gfarm#gfarm_depot')->update($depotid,array(
			'status'=>2,
			'update_time'=>time(),
		));
		C::t('#gfarm#gfarm_sale')->insert(array(
			'depot_id'=>$depotid,
			'good_price'=>$_GET['dnum'],
			'create_time'=>time(),
		));
		if($good['money_types']){
			$ljin=$depot['number']*$good['sale_price'];
		}else{
			$lyuan=$depot['number']*$good['sale_price'];;
		}
		include template('gfarm:ajax/depajax');
		exit;
	}	
}*/
if($_GET['act']=='sale'){//单个卖出
	if($_GET['formhash']==formhash()){
		$depot=C::t('#gfarm#gfarm_depot')->fetch($depotid);
		if($depot['islock']){
			exit;
		}
		$good=C::t('#gfarm#gfarm_goods')->fetch($depot['goods_id']);
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['dnum'],$result); 
		if($_GET['dnum']>$depot['number']||$_GET['dnum']<0||empty($result)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','023')."');";
			echo "</script>";
			exit;
		}
		$depotlog=array(
			'uid'=>$_G['uid'],
			'type'=>2,
			'goods_id'=>$good['id'],
			'goods_number'=>$_GET['dnum'],
			'money_type'=>$good['money_types'],
			'money_price'=>$_GET['dnum']*$good['sale_price'],
			'create_time'=>time(),
		);
		C::t('#gfarm#gfarm_depot_log')->insert($depotlog);
		$landalllogarr=array(
			'uid'=>$_G['uid'],
			'type'=>8,
			'goods_id'=>$good['id'],
			'create_time'=>time(),
		);		
		$landalllog=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=8 and uid='.$_G['uid'].' and goods_id='.$good['id']);
		if(empty($landalllog)){
			$landalllogarr['number']=$_GET['dnum'];
			C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr);
		}else{
			$landalllogarr['number']=$landalllog['number']+$_GET['dnum'];
			C::t('#gfarm#gfarm_land_all_log')->update($landalllog['id'],$landalllogarr);
		}
		$landalllogarr1=array(
			'uid'=>$_G['uid'],
			'type'=>9,
			'goods_id'=>$good['id'],
			'create_time'=>time(),
		);
		$landalllog1=C::t('#gfarm#gfarm_land_all_log')->fetch_first_data(' and type=9 and uid='.$_G['uid'].' and goods_id='.$good['id']);
		if(empty($landalllog1)){
			$landalllogarr1['number']=$_GET['dnum']*$good['sale_price'];
			C::t('#gfarm#gfarm_land_all_log')->insert($landalllogarr1);
		}else{
			$landalllogarr1['number']=$landalllog1['number']+$_GET['dnum']*$good['sale_price'];
			C::t('#gfarm#gfarm_land_all_log')->update($landalllog1['id'],$landalllogarr1);
		}
		if(empty($good['money_types'])){
			$flag=0;						
			C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
				'money'=>$user['money']+$_GET['dnum']*$good['sale_price'],
				'last_visit'=>time(),
			));		
			$money=$user['money']+$_GET['dnum']*$good['sale_price'];
			$msg=lang('plugin/gfarm','098').$config['moneyname'].$_GET['dnum']*$good['sale_price'];
		}else{
			$flag=1;
			C::t("common_member_count")->increase($user['uid'],array('extcredits'.$config['bullionstype']=>$_GET['dnum']*$good['sale_price']));
			$money = DB::result_first($sql,array('common_member_count',$_G['uid']));
			$msg=lang('plugin/gfarm','098').$config['bullionsname'].$_GET['dnum']*$good['sale_price'];
		}

		if($_GET['dnum']<$depot['number']){
			C::t('#gfarm#gfarm_depot')->update($depotid,array(
				'number'=>$depot['number']-$_GET['dnum'],
			));
		}else{
			C::t('#gfarm#gfarm_depot')->delete($depotid);
		}
		echo "<script>";
		echo "parent.window.hideWindow('gfarm1');";
		echo "parent.window.showajax('".$msg."',".$do.",".$money.",".$flag.");";
		echo "</script>";
		exit;
	}	
}
if(!empty($do)){
	if(in_array($do, array(12,13))){
		$str.=' and small_type='.$do;
	}else{
		$str.=' and big_type='.$do;
	}
}else{
	$str.=' and small_type=12';
}
$str.=' order by use_level';
$depotInfos=C::t('#gfarm#gfarm_depot')->fetch_all_data($str);
$yuan=0;
$jin=0;
foreach ($depotInfos as $key=> $value) {
	if(!empty($value['small_type'])){
		$separate=C::t('#gfarm#gfarm_goods_separate')->fetch($value['small_type']);
		$separates[$key]=$separate['separate_name'];
	}else{
		$separates[$key]=$value['separate_name'];
	}
	if(empty($value['islock'])){
		$flag=1;
		if($value['money_types']=='1'){
			$yuan+=$value['sale_price']*$value['number'];
		}else{
			$jin+=$value['sale_price']*$value['number'];
		}
	}
}
if($_GET['act']=='ajax'){
	include template('gfarm:ajax/depotajax');
	exit;
}
include template('gfarm:front/depot');

?>