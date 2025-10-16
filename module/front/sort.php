<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
//公告
if($_GET['act']=='ad'){
	include template('gfarm:front/announce');
	exit;
}
//消息
if($_GET['act']=='message'){
	$mylogs=C::t('#gfarm#gfarm_land_log')->fetch_all_data(' and uid!='.$_G['uid'].' and (type=6 or type=5) and target_uid='.$_G['uid']." and FROM_UNIXTIME(create_time,'%Y%m%d')>=".date("Ymd",strtotime("-1 day")).' order by create_time desc');
	foreach ($mylogs as $key=> $value) {
		$stealuser[$key]=C::t('#gfarm#gfarm_member')->fetch_by_uid($value['uid']);
	}
	include template('gfarm:front/message');
	exit;
}
//收藏
if($_GET['act']=='collect'){
	if($_GET['formhash']==formhash()){	
		$hascollect=C::t('#gfarm#gfarm_member_collect')->fetch_first_data(' and uid='.$_G['uid'].' and receive_uid='.$uid);
		if(!empty($hascollect)){
			exit;
		}
		C::t('#gfarm#gfarm_member_collect')->insert(array(
		'uid'=>$_G['uid'],
		'receive_uid'=>$uid,
		'create_time'=>time(),
		));
		include template('gfarm:ajax/collectajax');
		exit;
	}
}
//签到
if($_GET['act']=='signin'){
	$tday=date('t');
	$tmonth=date('Y-m');
	$j=0;
	for ($i=1;$i<=$tday;$i++){
		$week=date('w',strtotime($tmonth.'-'.$i));		
		$usersign=C::t('#gfarm#gfarm_member_signin')->fetch_first_data(" and uid=".$_G['uid']." and FROM_UNIXTIME(create_time,'%Y%m%d')=".date("Ymd",strtotime($tmonth.'-'.$i)));
		if(!empty($usersign)){
			$usersigns[$j][$week]=1;
		}
		$weeks[$j][$week]=date('d',strtotime($tmonth.'-'.$i));
		if($week==6){
			$j+=1;
		}
	}
	$signInfos=C::t('#gfarm#gfarm_signin')->fetch_all_data(' order by sign_day');
	$max=0;
	$csign=C::t('#gfarm#gfarm_member_signin')->count_all_data(" and uid=".$_G['uid']." and FROM_UNIXTIME(create_time,'%Y%m')=".date("Ym"));
	foreach ($signInfos as $key=>$value) {
		$rewardstr='';
		if(!empty($value['reward_money'])){
			$rewardstr.=','.$config['moneyname'].'x'.$value['reward_money'];
		}
		if (!empty($value['reward_yuan'])){
			$rewardstr.=','.$config['bullionsname'].'x'.$value['reward_yuan'];
		}
		if (!empty($value['reward_exp'])){
			$rewardstr.=','.lang('plugin/gfarm','079').'x'.$value['reward_exp'];
		}
		if (!empty($value['reward_bene'])){
			$rewardstr.=','.lang('plugin/gfarm','080').'x'.$value['reward_bene'];
		}
		if(!empty($value['reward_items'])){
			$item=C::t('#gfarm#gfarm_goods')->fetch($value['reward_items']);
			$rewardstr.=','.$item['name'].'x'.$value['items_num'];
		}
		$rewardstrs[$key]=substr($rewardstr, 1);
		$signwardss=C::t('#gfarm#gfarm_signin_reward')->fetch_first_data(' and uid='.$_G['uid'].' and sign_id='.$value['id']);
		if(!empty($signwardss)){
			if(date('Ym',$signwardss['update_time'])!=date('Ym')){
				C::t('#gfarm#gfarm_signin_reward')->update($signwardss['id'],array(
					'reward_flag'=>0,
					'update_time'=>time(),
				));
			}
		}	
		if($csign>=$value['sign_day']){
			$max=$key+1;
		}
		$signwards[$key]=C::t('#gfarm#gfarm_signin_reward')->fetch_first_data(' and reward_flag=1 and uid='.$_G['uid'].' and sign_id='.$value['id']);	
	}
	$hassign=C::t('#gfarm#gfarm_member_signin')->fetch_first_data(" and uid=".$_G['uid']." and FROM_UNIXTIME(create_time,'%Y%m%d')=".date("Ymd"));	
	if($_GET['pact']=='in'){
		if($_GET['formhash']==formhash()){
			if(!empty($hassign)){
				include template('gfarm:ajax/signinajax');
				exit;
			}
			C::t('#gfarm#gfarm_member_signin')->insert(array(
			'uid'=>$_G['uid'],
			'create_time'=>time(),
			));
			$tday=date('t');
			$tmonth=date('Y-m');
			$j=0;
			for ($i=1;$i<=$tday;$i++){
				$week=date('w',strtotime($tmonth.'-'.$i));
				if(date("Ymd",strtotime($tmonth.'-'.$i))==date("Ymd")){
					$tflag=$j.$week;
				}
				if($week==6){
					$j+=1;
				}
			}
			$max1=0;
			foreach ($signInfos as $key=>$value) {
				if($csign+1>=$value['sign_day']){
					$max1=$key+1;
				}
			}
			$user_credit = DB::result_first($sql,array('common_member_count',$_G['uid']));
			include template('gfarm:ajax/signinajax');
			exit;
		}
	}
	if($_GET['pact']=='getward'){//领取礼包
		$signid=$_GET['signid'];
		$sign=C::t('#gfarm#gfarm_signin')->fetch($signid);
		if($_GET['formhash']==formhash()){
			if($csign<$sign['sign_day']){
				exit;
			}
			$signward=C::t('#gfarm#gfarm_signin_reward')->fetch_first_data(' and uid='.$_G['uid'].' and sign_id='.$signid);
			$signin=C::t('#gfarm#gfarm_signin')->fetch($signid);
			if(!empty($signward['reward_flag'])){
				exit;
			}
			$userarr=array();
			if (!empty($signin['reward_money'])){
				$userarr['money']=$user['money']+$signin['reward_money'];
			}
			if (!empty($signin['reward_exp'])){
				$userarr['experience']=$user['experience']+$signin['reward_exp'];
				$mylevel1=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$userarr['experience'].' order by level desc');
				if($mylevel1!=$mylevel){
					$levelflag=1;
				}
				$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience>'.$userarr['experience'].' order by level');
				if(empty($nextlevel)){
					$nextlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' order by level desc');
				}
			}
			if (!empty($signin['reward_bene'])){
				$userarr['benevolence']=$user['benevolence']+$signin['reward_bene'];
			}
			if(empty($signward)){
				C::t('#gfarm#gfarm_signin_reward')->insert(array(
					'uid'=>$_G['uid'],
					'sign_id'=>$signid,
					'reward_flag'=>1,
					'create_time'=>time(),
					'update_time'=>time(),
				));
			}else{
				C::t('#gfarm#gfarm_signin_reward')->update($signward['id'],array(
					'reward_flag'=>1,
				));
			}
			C::t('#gfarm#gfarm_member')->update($_G['uid'],$userarr);
			if (!empty($signin['reward_yuan'])){
				C::t("common_member_count")->increase($_G['uid'],array('extcredits'.$config['bullionstype']=>$signin['reward_yuan']));
			}
			if (!empty($signin['reward_items'])){
				$item=C::t('#gfarm#gfarm_goods')->fetch($signin['reward_items']);
				$depots=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and status=0 and uid='.$_G['uid'].' and goods_id='.$signin['reward_items']);
				adddepotfile($depots,$signin['items_num'],$item['depot_pile'],$signin['reward_items'],$_G['uid']);
			}
			
			include template('gfarm:ajax/signwardajax');
			exit;
		}
	}
	include template('gfarm:front/signin');
	exit;
}


?>