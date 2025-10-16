<?php
/*
 * 作者：Crazy创意工作室
 * Q Q：25466413
 * 介绍：GPlay社区江湖的后台入口文件。
 *
 */
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$g_pluginName='gfarm';
$g_style='';
require_once DISCUZ_ROOT.'./source/plugin/gfarm/model/Model.class.php';
require_once DISCUZ_ROOT.'source/plugin/gfarm/model/Page.class.php';
require_once DISCUZ_ROOT.'./source/plugin/gfarm/class/Component.class.php';

require_once DISCUZ_ROOT.'./source/plugin/gfarm/com/gfarm_component.php';
$act=$_GET['act']?$_GET['act']:'index';
$component=new Component();

if($act=='index'){
	$page=new Page();
	$page->page=$_GET['page']?$_GET['page']:1;
	$page->perpage=$_GET['perpage'] ? $_GET['perpage'] : 20;
	$order['id']='asc';
	$where=array();
	$whereString='';
	$page=$component->getPage($page, $where, $order);
	$paging=$page->multi('admin.php?action=plugins&operation=config&do='.$_GET['do'].'&identifier=gfarm&pmod=component');
	include template($g_pluginName.':'.$g_style.'/component');
}

if($act=='install'){//安装
	if($_GET['formhash']==formhash()){
		$componentInfo=$component->getInfo($_GET['cid']);
		if($componentInfo['type']==2){
			require DISCUZ_ROOT.'source/plugin/gfarm/com/gfarm_'.$componentInfo['model'].'.php';
		}
		$component->update(array('flag'=>0),$componentInfo['id']);
		cpmsg(lang('plugin/gfarm', '012'), 'action=plugins&operation=config&do='.$_GET['do'].'&identifier=gfarm&pmod=component&page='.$_GET['page'], 'succeed');
	}
}

if($act=='uninstall'){//卸载
	if($_GET['formhash']==formhash()){
		$componentInfo=$component->getInfo($_GET['cid']);
		if($componentInfo['type']==2){
			require DISCUZ_ROOT.'source/plugin/gfarm/com/gfarm_'.$componentInfo['model'].'.php';
		}
		$component->update(array('flag'=>1),$componentInfo['id']);
		cpmsg(lang('plugin/gfarm', '012'), 'action=plugins&operation=config&do='.$_GET['do'].'&identifier=gfarm&pmod=component&page='.$_GET['page'], 'succeed');
	}
}

?>