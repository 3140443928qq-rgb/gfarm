<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$cvip=C::t('#gfarm#gfarm_plugin')->count_all_data(" and model='vip' and flag=0");
$cformula=C::t('#gfarm#gfarm_plugin')->count_all_data(" and model='formula' and flag=0");
$cequipment=C::t('#gfarm#gfarm_plugin')->count_all_data(" and model='equipment' and flag=0");
$cgifts=C::t('#gfarm#gfarm_plugin')->count_all_data(" and model='gifts' and flag=0");
$setInfo=C::t('#gfarm#gfarm_set')->fetch_setInfo();
$config=$_G['cache']['plugin']['gfarm'];
$sql="select extcredits".$config['bullionstype']." from %t where uid=%d";
$user_credit = DB::result_first($sql,array('common_member_count',$_G['uid']));
$g_charset=$_G['charset'];
require DISCUZ_ROOT.'source/plugin/gfarm/module/function.php';
$admins=explode('/', $config['adminuids']);
if(!$_G['uid']){
	//没有登录
	showmessage(lang('plugin/gfarm','001'), '', array(), array('login' => true));
	exit;
}else{
	if($_G['groupid']!=1&&!in_array($_G['groupid'],unserialize($config['usergroup']))) {
		//没有权限
		showmessage($setInfo['authority_explain']);
		exit;
	}
}
//新增用户
$user=C::t('#gfarm#gfarm_member')->fetch_by_uid($_G['uid']);
if(!empty($user['astatus'])){
	showmessage($setInfo['authority_explain']);
	exit;
}
if(empty($user)){
	$uid=C::t('#gfarm#gfarm_member')->insert(array(
		'uid'=>$_G['uid'],
		'money'=>$config['firstmoney'],
		'now_strength'=>$setInfo['power'],
		'register_time'=>time(),
	),true);
	C::t('#gfarm#gfarm_combin')->insert(array(
		'uid'=>$_G['uid'],
	));
	$user=C::t('#gfarm#gfarm_member')->fetch_by_uid($_G['uid']);
}
$mylevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$user['experience'].' order by level desc');
if($cvip=='1'){
	$myvip=C::t('#gfarm#gfarm_vip')->fetch_first_data(' and group_id='.$user['groupid']);
}
//回复体力
if(time()-$user['last_restrength']>=$setInfo['restrength_time']*60){
	$tcount=floor((time()-$user['last_restrength'])/($setInfo['restrength_time']*60));
	if(!empty($myvip)){
		if(!empty($myvip['addstrength'])){
			$setInfo['restrength_number']+=$myvip['addstrength'];
		}
	}
	$pow=$user['now_strength']+$tcount*$setInfo['restrength_number'];	
	if($pow>$setInfo['power']+$mylevel['strength']){
		$pow=$setInfo['power']+$mylevel['strength'];
	}
	C::t('#gfarm#gfarm_member')->update($_G['uid'],array(
		'now_strength'=>$pow,
		'last_restrength'=>$user['last_restrength']+$setInfo['restrength_time']*60*$tcount,
	));
}
//主线任务
$maintask=C::t('#gfarm#gfarm_member_main_task')->fetch_first_data(' and uid='.$_G['uid'].' order by a.id desc');
if(empty($maintask)){
	$currtask=C::t('#gfarm#gfarm_main_task')->fetch_first_data(' and visible=0 order by id');
	if(!empty($currtask)){
		C::t('#gfarm#gfarm_member_main_task')->insert(array(
			'uid'=>$_G['uid'],
			'task_id'=>$currtask['id'],
			'create_time'=>time(),
		));
		$maintask=C::t('#gfarm#gfarm_member_main_task')->fetch_first_data(' and uid='.$_G['uid'].' order by a.id desc');
	}		
}else{
	if(!empty($maintask['reward_flag'])){
		$currtask=C::t('#gfarm#gfarm_main_task')->fetch_first_data(' and visible=0 and id>'.$maintask['task_id'].' order by id');
		if(!empty($currtask)){
			C::t('#gfarm#gfarm_member_main_task')->insert(array(
				'uid'=>$_G['uid'],
				'task_id'=>$currtask['id'],
				'create_time'=>time(),
			));
			$maintask=C::t('#gfarm#gfarm_member_main_task')->fetch_first_data(' and uid='.$_G['uid'].' order by a.id desc');
		}	
	}		
}
if (!empty($maintask['reward_items'])){
	$taskitem=C::t('#gfarm#gfarm_goods')->fetch($maintask['reward_items']);
}
//活动任务
$dailyact=C::t('#gfarm#gfarm_daily_act')->fetch_first_data(" and visible=0 and start_time<=".time().' and end_time>='.time());
if(empty($dailyact)){	
	$acttask=C::t('#gfarm#gfarm_act_task')->fetch_first_data(' and visible=0 and start_time<='.time().' and end_time>='.time());
	if(!empty($acttask)){
		C::t('#gfarm#gfarm_daily_act')->insert(array(
			'task_id'=>$acttask['id'],
			'task_name'=>$acttask['task_name'],
			'task_num'=>$acttask['task_num'],
			'member_num'=>$acttask['member_num'],
			'target_object'=>$acttask['target_object'],
			'target_num'=>$acttask['target_num'],
			'reward_money'=>$acttask['reward_money'],
			'reward_yuan'=>$acttask['reward_yuan'],
			'reward_items'=>$acttask['reward_items'],
			'items_num'=>$acttask['items_num'],
			'reward_exp'=>$acttask['reward_exp'],
			'reward_bene'=>$acttask['reward_bene'],
			'start_time'=>$acttask['start_time'],
			'end_time'=>$acttask['end_time'],
			'task_describe'=>$acttask['task_describe'],
			'create_time'=>time(),
		));
		$dailyact=C::t('#gfarm#gfarm_daily_act')->fetch_first_data(" and visible=0 and start_time<=".time().' and end_time>='.time());
	}
}
//日常任务
$dailytasks=C::t('#gfarm#gfarm_member_daily_task')->fetch_all_data(' and uid='.$_G['uid']." and FROM_UNIXTIME(a.create_time,'%Y%m%d')=".date("Ymd"));
if(empty($dailytasks)){
	$alltask=C::t('#gfarm#gfarm_daily_task')->fetch_all_data(' and visible=0 and task_preid=0 and min_level<='.$mylevel['level'].' and (max_level>='.$mylevel['level'].' or max_level=0)');
	foreach ($alltask as $value) {
		C::t('#gfarm#gfarm_member_daily_task')->insert(array(
			'uid'=>$_G['uid'],
			'task_id'=>$value['id'],
			'create_time'=>time(),
		));
	}
}
$dailytasks=C::t('#gfarm#gfarm_member_daily_task')->fetch_all_data(' and reward_flag=0 and uid='.$_G['uid']." and FROM_UNIXTIME(a.create_time,'%Y%m%d')=".date("Ymd"));
$uid=empty($_GET['uid'])?$_G['uid']:$_GET['uid'];
//装备装扮是否到期
$currentuser=C::t('#gfarm#gfarm_member')->fetch_by_uid($uid);
if($cequipment==1){
	$equipments=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$_G['uid'].' and big_type=1');
	$decorates=C::t('#gfarm#gfarm_depot')->fetch_all_data(' and uid='.$currentuser['uid'].' and big_type=4');
	foreach ($equipments as $value) {
		$calequipment=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$value['goods_id']);
		if(!empty($calequipment['valid_time'])){
			if(time()-$value['create_time']>=$calequipment['valid_time']*24*60*60){
				C::t('#gfarm#gfarm_depot')->delete($value['id']);
			}
		}		
	}
	foreach ($decorates as $value) {
		$caldecorate=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$value['goods_id']);
		if(!empty($caldecorate['valid_time'])){
			if(time()-$value['create_time']>=$caldecorate['valid_time']*24*60*60){
				C::t('#gfarm#gfarm_depot')->delete($value['id']);
			}
		}
	}
}
$countland=C::t('#gfarm#gfarm_member_land')->count_all_data(' and uid='.$_G['uid']);
$lock=C::t('#gfarm#gfarm_lock')->fetch_first_data(' and visible=0 and land_num='.($countland+1));
$currentlevel=C::t('#gfarm#gfarm_exp_level')->fetch_first_data(' and experience<='.$currentuser['experience'].' order by level desc');
$current_credit = DB::result_first($sql,array('common_member_count',$uid));
$navtitle = $config['title'];
$metakeywords = $config['keywords'];
$metadescription = $config['description'];
$modarray = array('index','depot','shop','gfarm_ajax','decorate','equipment','certificate','mixture','produce','formula','myself','friend','rank','exchange','sort','picture','market','dailytask','maintask','achieve','acttask','vip');
$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
$mod = !in_array($mod, $modarray) ? 'index' : $mod;
require DISCUZ_ROOT.'source/plugin/gfarm/module/front/'.$mod.'.php';	

?>
