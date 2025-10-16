<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$g_charset=$_G['charset'];
$g_pluginName='gfarm';
$g_style='cp';
$setInfo=C::t('#gfarm#gfarm_set')->fetch_setInfo();
$config=$_G['cache']['plugin']['gfarm'];
$admins=explode('/', $config['adminuids']);
if($_G['uid']){
	if($_G['groupid']!=1&&!in_array($_G['uid'],$admins)) {
		//没有权限
		showmessage($setInfo['authority_explain']);
		exit;
	}
}else{
	//没有登录
	showmessage(lang('plugin/gfarm','001'), '', array(), array('login' => true));
	exit;
}
//获取插件名称和版本号
$plugin_info = DB::fetch_first('select * from %t where identifier=%s', array('common_plugin','gfarm'));
$cvip=C::t('#gfarm#gfarm_plugin')->count_all_data(" and model='vip' and flag=0");
$cformula=C::t('#gfarm#gfarm_plugin')->count_all_data(" and model='formula' and flag=0");
$cequipment=C::t('#gfarm#gfarm_plugin')->count_all_data(" and model='equipment' and flag=0");
$cgifts=C::t('#gfarm#gfarm_plugin')->count_all_data(" and model='gifts' and flag=0");
$navtitle = $config['title'];
$metakeywords = $config['keywords'];
$metadescription = $config['description'];
$modarray = array('pset','user','level','goods','germcrop','certificategerm','formulagoods','depot','formula','land','landlog','formulalog','vip','maintask','dailytask','achievetask','mainlog','dailylog','achievelog','signin','signlog','exchangelog','depotlog','acttask','actlog','lock','locklog','index','clear');
require DISCUZ_ROOT.'source/plugin/gfarm/module/function.php';
require_once DISCUZ_ROOT.'source/plugin/gfarm/class/page_function.php';
$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
$mod = !in_array($mod, $modarray) ? 'index' : $mod;
require DISCUZ_ROOT.'source/plugin/gfarm/module/cp/'.$mod.'.php';	

?>
