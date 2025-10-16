<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$cropInfos=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and small_type=12');
$germid=$_GET['germ_id'];
$germcrop=C::t('#gfarm#gfarm_germ_crop')->fetch_first_data(' and germ_id='.$germid);
$gcid=$germcrop['id'];
$start=($currpage-1)*$perpage;
if($_GET['act']=='update'){	
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['crop_id'],$result5); 
		preg_match($tt,$_GET['produce_big'],$result1); 
	/*	preg_match($tt,$_GET['produce_small'],$result2); 
		preg_match($tt,$_GET['produce_odds'],$result3); */
		if(empty($result5)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','024')."');";
			echo "</script>";
			exit;
		}
		/*if(empty($result3)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('产出几率填写错误');";
			echo "</script>";
			exit;
		}
		if(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('最小收获数量填写错误');";
			echo "</script>";
			exit;
		}*/
		if(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','025')."');";
			echo "</script>";
			exit;
		}
		
		/*if($_GET['produce_big']<$_GET['produce_small']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('最大收获数量不能小于最小收获数量');";
			echo "</script>";
			exit;
		}		*/	
		$crop=C::t('#gfarm#gfarm_goods')->fetch($_GET['crop_id']);
		if(empty($crop)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','026')."');";
			echo "</script>";
			exit;
		}
		$updatearr=array(
			'germ_id'=>$_GET['germ_id'],
			'crop_id'=>$_GET['crop_id'],
			'produce_big'=>$_GET['produce_big'],
			'produce_small'=>$_GET['produce_big'],
			'produce_odds'=>100,
			'update_time'=>time(),
			'update_uid'=>$_G['uid'],
		);
		$haslink=C::t('#gfarm#gfarm_germ_crop')->count_all_data(' and germ_id='.$_GET['germ_id'].' and crop_id='.$_GET['crop_id']);
		if(empty($gcid)){			
			if(!empty($haslink)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','027')."');";
				echo "</script>";
				exit;
			}
			$updatearr['create_time']=time();
			$updatearr['create_uid']=$_G['uid'];
			C::t('#gfarm#gfarm_germ_crop')->insert($updatearr);
		}else{
			if($germcrop['crop_id']!=$_GET['crop_id']){
				if(!empty($haslink)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','027')."');";
					echo "</script>";
					exit;
				}
			}
			C::t('#gfarm#gfarm_germ_crop')->update($gcid,$updatearr);
		}
		
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg1('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	include template('gfarm:cp/germcrop/newgermcrop');
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
$gcInfos=C::t('#gfarm#gfarm_germ_crop')->fetch_all_data($str,$start,$perpage);
$num=C::t('#gfarm#gfarm_germ_crop')->count_all_data();
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/germcrop/germcrop');
	

?>