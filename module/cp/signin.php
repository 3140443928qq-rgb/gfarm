<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$sid=$_GET['sid'];
$start=($currpage-1)*$perpage;
$csignin=C::t('#gfarm#gfarm_signin')->count_all_data();
if($_GET['act']=='update'){		
	if(empty($sid)){		
		if($csignin>=6){
			exit;
		}
	}
	$signin=C::t('#gfarm#gfarm_signin')->fetch($sid);
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['sign_day'],$result0); 
		preg_match($tt,$_GET['reward_money'],$result1); 	
		preg_match($tt,$_GET['reward_yuan'],$result2); 
		preg_match($tt,$_GET['items_num'],$result3); 	
		preg_match($tt,$_GET['reward_bene'],$result4); 
		preg_match($tt,$_GET['reward_exp'],$result5); 		
		if(empty($result0)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','074')."');";
			echo "</script>";
			exit;
		}
		if($_GET['sign_day']>31){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','075')."');";
			echo "</script>";
			exit;
		}else{
			$count=C::t('#gfarm#gfarm_signin')->count_all_data(' and sign_day='.$_GET['sign_day']);
			if(empty($sid)){
				if(!empty($count)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','076')."');";
					echo "</script>";
					exit;
				}
			}else{
				if($signin['sign_day']!=$_GET['sign_day']){
					if(!empty($count)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','076')."');";
						echo "</script>";
						exit;
					}
				}
			}			
		}
		if(empty($_GET['reward_yuan'])&&empty($_GET['reward_money'])&&empty($_GET['reward_items'])&&empty($_GET['reward_bene'])&&empty($_GET['reward_exp'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','006')."');";
			echo "</script>";
			exit;
		}
		if(empty($_GET['reward_money'])){
			$_GET['reward_money']=0;
		}elseif(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','007').$config['moneyname'].lang('plugin/gfarm','082')."');";
			echo "</script>";
			exit;
		}		
		if(empty($_GET['reward_yuan'])){
			$_GET['reward_yuan']=0;
		}elseif(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','007').$config['bullionsname'].lang('plugin/gfarm','082')."');";
			echo "</script>";
			exit;
		}	
		if(!empty($_GET['reward_items'])){
			if(empty($result3)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','008')."');";
				echo "</script>";
				exit;
			}
		}else{
			$_GET['items_num']=0;
		}	
		if(empty($_GET['reward_bene'])){
			$_GET['reward_bene']=0;
		}elseif(empty($result4)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','009')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['reward_exp'])){
			$_GET['reward_exp']=0;
		}elseif(empty($result5)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','010')."');";
			echo "</script>";
			exit;
		}	
		$updatearray=array(
			'sign_day'=>$_GET['sign_day'],
			'reward_money'=>$_GET['reward_money'],
			'reward_yuan'=>$_GET['reward_yuan'],
			'reward_items'=>$_GET['reward_items'],
			'items_num'=>$_GET['items_num'],
			'reward_exp'=>$_GET['reward_exp'],
			'reward_bene'=>$_GET['reward_bene'],
			'update_time'=>time(),
		);
		if(empty($sid)){		
			$updatearray['create_time']=time();
			C::t('#gfarm#gfarm_signin')->insert($updatearray);
		}else{				
			C::t('#gfarm#gfarm_signin')->update($sid,$updatearray);			
		}
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}	
	$items=C::t('#gfarm#gfarm_goods')->fetch_all_data(' and big_type!=11');
	foreach ($items as $key=>$value) {
		$sep[$key]=C::t('#gfarm#gfarm_goods_separate')->fetch($value['big_type']);
		if(!empty($value['small_type'])){
			$sep[$key]=C::t('#gfarm#gfarm_goods_separate')->fetch($value['small_type']);
		}
	}
	include template('gfarm:cp/signin/newsignin');
	exit;
}
$signInfos=C::t('#gfarm#gfarm_signin')->fetch_all_data($str.' order by sign_day',$start,$perpage);
foreach ($signInfos as $key=>$value) {
	$str1='';
	if(!empty($value['reward_money'])){
		$str1.=$config['moneyname'].'x'.$value['reward_money'].',';
	}
	if(!empty($value['reward_yuan'])){
		$str1.=$config['bullionsname'].'x'.$value['reward_yuan'].',';
	}
	if(!empty($value['reward_exp'])){
		$str1.=lang('plugin/gfarm','079').'x'.$value['reward_exp'].',';
	}
	if(!empty($value['reward_bene'])){
		$str1.=lang('plugin/gfarm','080').'x'.$value['reward_bene'].',';
	}
	if(!empty($value['reward_items'])){
		$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
		$str1.=$item['name'].'x'.$value['items_num'].',';
	}
	$str1s[$key]=substr($str1, 0,strlen($str1)-1);
}
$num=C::t('#gfarm#gfarm_signin')->count_all_data();
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/signin/signin');
	

?>