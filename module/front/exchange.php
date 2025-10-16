<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
if($_GET['act']=='in'){
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['dnum'],$result);
		if(empty($result)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','100')."');";
			echo "</script>";
			exit;
		}
		if($_GET['dnum']>$user_credit){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".$config['bullionsname'].lang('plugin/gfarm','101')."');";
			echo "</script>";
			exit;
		}
		$dnum=-$_GET['dnum'];
		$addmoney=$_GET['dnum']*$config['exchangebase'];
		$exchangelogarr=array(
			'uid'=>$_G['uid'],
			'money'=>$_GET['dnum']*$config['exchangebase'],
			'outstyle'=>$config['bullionstype'],
			'exnum'=>$_GET['dnum'],
			'usermoney'=>$user['money']+$addmoney,
			'create_time'=>time(),
		);		
		C::t('#gfarm#gfarm_exchange_log')->insert($exchangelogarr);
		C::t("common_member_count")->increase($_G['uid'],array('extcredits'.$config['bullionstype']=>$dnum));
		C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
		'money'=>$user['money']+$addmoney,
		'last_visit'=>time(),
		));
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','102')."',".($user['money']+$addmoney).",".($user_credit+$dnum).",1);";
		echo "</script>";
		exit;
	}
	include template('gfarm:front/exchange');
	exit;
}
if($_GET['act']=='out'){
	if($_GET['formhash']==formhash()){		
		if(empty($setInfo['isout'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','020')."');";
			echo "</script>";
			exit;
		}
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['dnum'],$result);
		if(empty($result)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','100')."');";
			echo "</script>";
			exit;
		}	
		if($_GET['dnum']<$setInfo['minexnum']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','129').$setInfo[minexnum]."');";
			echo "</script>";
			exit;
		}	
		$hasnum=C::t('#gfarm#gfarm_exchange_log')->sum_exnum(' and type=1 and uid='.$_G['uid']." and FROM_UNIXTIME(create_time,'%Y%m%d')=".date("Ymd"));
		if($_GET['dnum']+$hasnum>$setInfo['maxexnum']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','130').$setInfo[maxexnum]."');";
			echo "</script>";
			exit;
		}
		if($_GET['dnum']*$setInfo['outbase']>$user['money']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".$config['moneyname'].lang('plugin/gfarm','101')."');";
			echo "</script>";
			exit;
		}
		if($user['money']-$_GET['dnum']*$setInfo['outbase']<$setInfo['usermoney']){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','131').$config[moneyname].lang('plugin/gfarm','132').$setInfo['usermoney']."');";
			echo "</script>";
			exit;
		}		
		$dnum=$_GET['dnum'];
		$addmoney=-$_GET['dnum']*$setInfo['outbase'];
		$exchangelogarr=array(
			'uid'=>$_G['uid'],
			'type'=>1,
			'money'=>$_GET['dnum']*$setInfo['outbase'],
			'outstyle'=>$setInfo['outstyle'],
			'exnum'=>$_GET['dnum'],
			'usermoney'=>$user['money']+$addmoney,
			'create_time'=>time(),
		);			
		C::t('#gfarm#gfarm_exchange_log')->insert($exchangelogarr);
		C::t("common_member_count")->increase($_G['uid'],array('extcredits'.$setInfo['outstyle']=>$dnum));
		C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
		'money'=>$user['money']+$addmoney,
		'last_visit'=>time(),
		));
		if($config['bullionstype']==$setInfo['outstyle']){
			$flag=1;
		}else{
			$flag=0;
		}
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','102')."',".($user['money']+$addmoney).",".($user_credit+$dnum).",".$flag.");";
		echo "</script>";
		exit;
	}
	include template('gfarm:front/outexchange');
	exit;
}

?>