<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$vipid=$_GET['vipid'];
$start=($currpage-1)*$perpage;
if($_GET['act']=='delete'){	
	if($_GET['formhash']==formhash()){
		C::t('#gfarm#gfarm_vip')->delete($vipid);
		echo "<script>";
		echo "parent.window.showmsg('".lang('plugin/gfarm','069')."');";
		echo "</script>";
		exit;
	}	
}
if($_GET['act']=='update'){		
	$vip=C::t('#gfarm#gfarm_vip')->fetch($vipid);
	if($_GET['formhash']==formhash()){
		if(empty($_GET['vipname'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','002')."');";
			echo "</script>";
			exit;
		}else{
			$count=C::t('#gfarm#gfarm_vip')->count_all_data(" and vipname='".$_GET['vipname']."'");
			$vip1=C::t('#gfarm#gfarm_vip')->fetch_first_data(' and group_id='.$_GET['group_id']);
			if(empty($vipid)){
				if(!empty($count)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
					echo "</script>";
					exit;
				}
				if(!empty($vip1)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','085')."');";
					echo "</script>";
					exit;
				}
			}else{
				if($_GET['vipname']!=$vip['vipname']){
					if(!empty($count)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
						echo "</script>";
						exit;
					}
				}
				if($_GET['group_id']!=$vip['group_id']){
					if(!empty($vip1)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','085')."');";
						echo "</script>";
						exit;
					}
				}
			}
		}		
		$tt='/^(([1-9]+[0-9]*)|[0])$/';
		preg_match($tt,$_GET['reshopmoney'],$result1); 
		preg_match($tt,$_GET['relockmoney'],$result2); 
		preg_match($tt,$_GET['restealnumber'],$result3); 
		preg_match($tt,$_GET['restealodd'],$result4); 
		preg_match($tt,$_GET['addstealnumber'],$result5); 
		preg_match($tt,$_GET['addstealodd'],$result6); 
		preg_match($tt,$_GET['addstrength'],$result7); 
		if(empty($_GET['reshopmoney'])){
			$_GET['reshopmoney']=0;
		}elseif(empty($result1)||$_GET['reshopmoney']>100){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','140')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['relockmoney'])){
			$_GET['relockmoney']=0;
		}elseif(empty($result2)||$_GET['relockmoney']>100){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','141')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['restealnumber'])){
			$_GET['restealnumber']=0;
		}elseif(empty($result3)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','142')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['restealodd'])){
			$_GET['restealodd']=0;
		}elseif(empty($result4)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','143')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['addstealnumber'])){
			$_GET['addstealnumber']=0;
		}elseif(empty($result5)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','144')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['addstealodd'])){
			$_GET['addstealodd']=0;
		}elseif(empty($result6)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','086')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['addstrength'])){
			$_GET['addstrength']=0;
		}elseif(empty($result7)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','145')."');";
			echo "</script>";
			exit;
		}	
		$updatearr=array(
			'vipname'=>$_GET['vipname'],
			'group_id'=>$_GET['group_id'],
			'reshopmoney'=>$_GET['reshopmoney'],
			'relockmoney'=>$_GET['relockmoney'],
			'restealnumber'=>$_GET['restealnumber'],
			'restealodd'=>$_GET['restealodd'],
			'addstealnumber'=>$_GET['addstealnumber'],
			'addstealodd'=>$_GET['addstealodd'],
			'addstrength'=>$_GET['addstrength'],
			'opensteal'=>$_GET['opensteal'],
			'openget'=>$_GET['openget'],
			'openindex'=>$_GET['openindex'],
			'create_time'=>time(),
		);
		
		if(empty($vipid)){
			C::t('#gfarm#gfarm_vip')->insert($updatearr);
		}else{
			C::t('#gfarm#gfarm_vip')->update($vipid,$updatearr);
		}		
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	$usergroup=C::t('#gfarm#gfarm_vip')->fetch_by_group();
	include template('gfarm:cp/vip/newvip');
	exit;
}
$vipInfos=C::t('#gfarm#gfarm_vip')->fetch_all_data($str,$start,$perpage);
$num=C::t('#gfarm#gfarm_vip')->count_all_data();
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/vip/vip');
	

?>