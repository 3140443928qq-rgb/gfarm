<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$lid=$_GET['lid'];
$start=($currpage-1)*$perpage;
$maxlevel=C::t('#gfarm#gfarm_exp_level')->fetch_maxlevel();
if($_GET['act']=='delete'){	
	if($_GET['formhash']==formhash()){
		if($lid<$maxlevel){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','068')."');";
			echo "</script>";
			exit;
		}
		C::t('#gfarm#gfarm_exp_level')->delete($lid);
		echo "<script>";
		echo "parent.window.showmsg('".lang('plugin/gfarm','069')."');";
		echo "</script>";
		exit;
	}	
}
if($_GET['act']=='update'){		
	$level=C::t('#gfarm#gfarm_exp_level')->fetch($lid);
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		$tt1='/^(([1-9]+[0-9]*)|[0])$/';
		preg_match($tt,$_GET['level'],$result1); 
		preg_match($tt1,$_GET['experience'],$result2); 
		preg_match($tt1,$_GET['strength'],$result3); 
		preg_match($tt,$_GET['land_number'],$result4); 
		if(empty($_GET['name'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','002')."');";
			echo "</script>";
			exit;
		}else{
			$count=C::t('#gfarm#gfarm_exp_level')->count_all_data(" and name='".$_GET['name']."'");
		}	
		if(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','070')."');";
			echo "</script>";
			exit;
		}	
		if(empty($result3)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','071')."');";
			echo "</script>";
			exit;
		}	
		if(empty($result4)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','072')."');";
			echo "</script>";
			exit;
		}	
		$updatearr=array(
			'name'=>$_GET['name'],
			'strength'=>$_GET['strength'],
			'land_number'=>$_GET['land_number'],
			'update_time'=>time(),
		);
		if(empty($lid)){
			if(!empty($count)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
				echo "</script>";
				exit;
			}
			$lastlevel=C::t('#gfarm#gfarm_exp_level')->fetch($maxlevel);
			if($lastlevel['experience']>=$_GET['experience']){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','073')."');";
				echo "</script>";
				exit;
			}
			$updatearr['level']=$maxlevel+1;
			if(!empty($maxlevel)){
				$updatearr['experience']=$_GET['experience'];
			}
			$updatearr['create_time']=time();
			C::t('#gfarm#gfarm_exp_level')->insert($updatearr);
		}else{
			if($level['name']!=$_GET['name']){
				if(!empty($count)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
					echo "</script>";
					exit;
				}
			}
			$lastid=$lid-1;
			if(!empty($lastid)){
				$lastlevel=C::t('#gfarm#gfarm_exp_level')->fetch($lastid);
				if($lastlevel['experience']>=$_GET['experience']){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','073')."');";
					echo "</script>";
					exit;
				}
			}		
			if($level['level']!='1'){
				$updatearr['experience']=$_GET['experience'];
			}	
			C::t('#gfarm#gfarm_exp_level')->update($lid,$updatearr);
		}
		
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	
	include template('gfarm:cp/level/newlevel');
	exit;
}
$do=$_GET['do'];
if(empty($do)){
	$str=' order by level desc';
	$do=1;
	$text="&do=0";
}else{
	$str=' order by level';
	$do=0;
	$text="&do=1";
}
$levelInfos=C::t('#gfarm#gfarm_exp_level')->fetch_all_data($str,$start,$perpage);
$num=C::t('#gfarm#gfarm_exp_level')->count_all_data();
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/level/level');
	

?>