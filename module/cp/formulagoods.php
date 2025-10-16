<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$fgid=$_GET['fgid'];
$start=($currpage-1)*$perpage;
$cropInfos=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=11');
$foodInfos=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=14');
if($_GET['act']=='update'){	
	$formulagoods=C::t('#gfarm#gfarm_formula_goods')->fetch($fgid);
	if($_GET['formhash']==formhash()){
		if(empty($_GET['name'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','002')."');";
			echo "</script>";
			exit;
		}else{
			$cname=C::t('#gfarm#gfarm_formula_goods')->count_all_data(" and name='".$_GET['name']."'");
			if(empty($fgid)){
				if(!empty($cname)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
					echo "</script>";
					exit;
				}
			}else{
				if($formulagoods['name']!=$_GET['name']){
					if(!empty($cname)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
						echo "</script>";
						exit;
					}
				}
			}
		}
		$tt='/^([1-9]+[0-9]*)$/';
		$good_id=$_GET['good_id1'].'/'.$_GET['good_id2'].'/'.$_GET['good_id3'].'/'.$_GET['good_id4'];
		$strs=explode('/', $good_id);
		$cgoodid=C::t('#gfarm#gfarm_formula_goods')->count_all_data(" and good_ids='".$good_id."'");
		if(empty($fgid)){
			if(!empty($cgoodid)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','054')."');";
				echo "</script>";
				exit;
			}
		}else{
			if($formulagoods['good_ids']!=$good_id){
				if(!empty($cgoodid)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','054')."');";
					echo "</script>";
					exit;
				}
			}
		}
		if(count($strs)!=4){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','055')."');";
			echo "</script>";
			exit;
		}else{
			foreach ($strs as $key=>$value) {
				preg_match($tt,$value,$bol);
				if(empty($bol)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','048').($key+1).lang('plugin/gfarm','049')."');";
					echo "</script>";
					exit;
				}
				$good=C::t('#gfarm#gfarm_goods')->fetch($value);
				if(empty($good)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','048').($key+1).lang('plugin/gfarm','056')."');";
					echo "</script>";
					exit;
				}
			}
		}
		$good_number=trim($_GET['good_numbers']);
		$strs0=explode('/', $good_number);
		if((count($strs0)>1&&count($strs0)<4)||count($strs0)>4){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','051')."');";
				echo "</script>";
				exit;
		}else{				
			foreach ($strs0 as $key=>$value) {
				preg_match($tt,$value,$bol);
				if(empty($bol)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','048').($key+1).lang('plugin/gfarm','052')."');";
					echo "</script>";
					exit;
				}
			}
		}
		$cgood=C::t('#gfarm#gfarm_formula_goods')->count_all_data(" and good_ids='".$good_id."' and good_numbers='".$good_number."'");
		if(empty($fgid)){
			if(!empty($cgood)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','063')."');";
				echo "</script>";
				exit;
			}
		}else{
			if($formulagoods['good_ids']!=$good_id||$formulagoods['good_numbers']!=$good_number){			
				if(!empty($cgood)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','063')."');";
					echo "</script>";
					exit;
				}
			}
		}
		preg_match($tt,$_GET['produce_id'],$result5);
		preg_match($tt,$_GET['produce_big'],$result1); 
		preg_match($tt,$_GET['produce_small'],$result2); 
		preg_match($tt,$_GET['use_level'],$result3); 
		if(empty($result5)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','057')."');";
			echo "</script>";
			exit;
		}else{
			$produce=C::t('#gfarm#gfarm_goods')->fetch($_GET['produce_id']);
			if(empty($produce)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','058')."');";
				echo "</script>";
				exit;
			}
			foreach ($strs as $key=>$value) {
				if($value==$_GET['produce_id']){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','059')."');";
					echo "</script>";
					exit;
				}
			}
		}
		if(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','064')."');";
			echo "</script>";
			exit;
		}
		if(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','065')."');";
			echo "</script>";
			exit;
		}
		if($_GET['produce_big']<$_GET['produce_small']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','066')."');";
			echo "</script>";
			exit;
		}	
		if(empty($result3)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','032')."');";
			echo "</script>";
			exit;
		}
		$updatearr=array(
			'name'=>$_GET['name'],
			'good_ids'=>$good_id,
			'good_numbers'=>$good_number,
			'produce_id'=>$_GET['produce_id'],
			'produce_big'=>$_GET['produce_big'],
			'produce_small'=>$_GET['produce_small'],
			'use_level'=>$_GET['use_level'],
			'update_time'=>time(),
			'update_uid'=>$_G['uid'],
		);
		if(empty($fgid)){
			$updatearr['create_time']=time();
			$updatearr['create_uid']=$_G['uid'];
			C::t('#gfarm#gfarm_formula_goods')->insert($updatearr);
		}else{
			C::t('#gfarm#gfarm_formula_goods')->update($fgid,$updatearr);
		}
		
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	$strs=explode('/', $formulagoods['good_ids']);
	include template('gfarm:cp/formulagoods/newformulagoods');
	exit;
}
$orderfield1=$_GET['orderfield'];
$ordertype1=$_GET['ordertype'];
if(empty($orderfield1)){
	$orderfield1='id';
}
if($ordertype1=='asc'||empty($ordertype1)){
	$ordertype='desc';
	$ordertype1=='asc';	
}else{
	$ordertype='asc';
}
$str.=' order by '.$orderfield1.' '.$ordertype1;
$text.='&orderfield='.$orderfield1.'&ordertype='.$ordertype1;
$fgInfos=C::t('#gfarm#gfarm_formula_goods')->fetch_all_data($str,$start,$perpage);
foreach ($fgInfos as $key1=>$value) {
	$strs=explode('/', $value['good_ids']);
	$strs1=explode('/', $value['good_numbers']);
	$str='';
	foreach ($strs as $key=>$value1) {
		$good=C::t('#gfarm#gfarm_goods')->fetch($value1);
		if(count($strs1)==1){
			$number=$strs1[0];
		}else{
			$number=$strs1[$key];
		}
		$str.=$good['name'].'x'.$number.',';
	}
	$strss[$key1]=substr($str, 0,-1);
}
$num=C::t('#gfarm#gfarm_formula_goods')->count_all_data();
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/formulagoods/formulagoods');
?>