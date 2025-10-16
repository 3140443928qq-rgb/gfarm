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
$cropInfos=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and small_type=12');
$germInfos=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type=9');
if($_GET['act']=='update'){	
	$formula=C::t('#gfarm#gfarm_formula')->fetch($fgid);
	if($_GET['formhash']==formhash()){
		if(empty($_GET['name'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','002')."');";
			echo "</script>";
			exit;
		}else{
			$cname=C::t('#gfarm#gfarm_formula')->count_all_data(" and name='".$_GET['name']."'");
			if(empty($fgid)){
				if(!empty($cname)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
					echo "</script>";
					exit;
				}
			}else{
				if($formula['name']!=$_GET['name']){
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
		$cgoodid=C::t('#gfarm#gfarm_formula')->count_all_data(" and good_ids='".$good_id."'");
		if(empty($fgid)){
			if(!empty($cgoodid)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','054')."');";
				echo "</script>";
				exit;
			}
		}else{
			if($formula['good_ids']!=$good_id){
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
		preg_match($tt,$_GET['success_id'],$result1); 
		preg_match($tt,$_GET['success_odd'],$result2); 
		if(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','057')."');";
			echo "</script>";
			exit;
		}else{
			$produce=C::t('#gfarm#gfarm_goods')->fetch($_GET['success_id']);
			if(empty($produce)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','058')."');";
				echo "</script>";
				exit;
			}
			foreach ($strs as $key=>$value) {
				if($value==$_GET['success_id']){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','059')."');";
					echo "</script>";
					exit;
				}
			}
		}
		$csuccessid=C::t('#gfarm#gfarm_formula')->count_all_data(" and success_id=".$_GET['success_id']);		
		if(empty($fgid)){
			if(!empty($csuccessid)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','060')."');";
				echo "</script>";
				exit;
			}
		}else{
			if($formula['success_id']!=$_GET['success_id']){
				if(!empty($csuccessid)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','060')."');";
					echo "</script>";
					exit;
				}
			}
		}
		if(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','061')."');";
			echo "</script>";
			exit;
		}
		$stuff_id=trim($_GET['stuff_ids']);
		$tt1='/^(([1-9]+[0-9]*)|[0])$/';
		preg_match($tt1,$_GET['fail_odd'],$resultfail);
		if(empty($resultfail)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','062')."');";
			echo "</script>";
			exit;
		}
		/*$strs0=explode('/', $stuff_id);
		if(empty($stuff_id)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('请至少输入一个失败物品id');";
			echo "</script>";
			exit;
		}else{
			foreach ($strs0 as $key=>$value) {
				preg_match($tt,$value,$bol);
				if(empty($bol)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('第".($key+1)."个失败物品id填写错误');";
					echo "</script>";
					exit;
				}
				$good=C::t('#gfarm#gfarm_goods')->fetch($value);
				if(empty($good)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('第".($key+1)."个失败物品id不存在');";
					echo "</script>";
					exit;
				}
			}
		}
		$stuff_odd=trim($_GET['stuff_odds']);
		$strs1=explode('/', $stuff_odd);
		if(!empty($stuff_odd)){
			if(count($strs1)!=count($strs0)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('失败基数组与失败物品组数量不匹配');";
				echo "</script>";
				exit;
			}else{
				foreach ($strs1 as $key=>$value) {
					preg_match($tt,$value,$bol);
					if(empty($bol)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('第".($key+1)."个失败基数填写错误');";
						echo "</script>";
						exit;
					}
				}
			}
		}*/
		$updatearr=array(
			'name'=>$_GET['name'],
			'good_ids'=>$good_id,
			'success_id'=>$_GET['success_id'],
			'success_odd'=>$_GET['success_odd'],
			'stuff_ids'=>$stuff_id,
			'stuff_odds'=>$stuff_odd,
			'fail_odd'=>$_GET['fail_odd'],
			'update_time'=>time(),
			'update_uid'=>$_G['uid'],
		);
		if(empty($fgid)){
			$updatearr['create_time']=time();
			$updatearr['create_uid']=$_G['uid'];
			C::t('#gfarm#gfarm_formula')->insert($updatearr);
		}else{
			C::t('#gfarm#gfarm_formula')->update($fgid,$updatearr);
		}
		
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	$strs=explode('/', $formula['good_ids']);
	include template('gfarm:cp/formulagoods/newformula');
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
$fgInfos=C::t('#gfarm#gfarm_formula')->fetch_all_data($str,$start,$perpage);
foreach ($fgInfos as $key1=>$value) {
	$strs=explode('/', $value['good_ids']);
	$str='';
	foreach ($strs as $key=>$value) {
		$good=C::t('#gfarm#gfarm_goods')->fetch($value);
		$str.=$good['name'].'/';
	}
	$strss[$key1]=substr($str, 0,-1);
}
$num=C::t('#gfarm#gfarm_formula')->count_all_data();
$paging = helper_page :: multi($num, $perpage, $currpage, 'plugin.php?id=gfarm:cp&mod=formula'.$text);
include template('gfarm:cp/formulagoods/formula');
	

?>