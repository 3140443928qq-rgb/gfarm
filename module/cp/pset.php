<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$do=$_GET['do'];
if(empty($do)){
	$do='base';
}
$mods=C::t('#gfarm#gfarm_set')->fetch_mod();
$setInfos=C::t('#gfarm#gfarm_set')->fetch_all_data(" and status=0 and mod_en='".$do."'");
if ($_GET['formhash']==formhash()){	
	foreach($setInfos as $key => $val){
		preg_match($val['regex_en'],$_GET[$val['key_en']],$result); 	
		if(empty($result)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".$val['regex_cn']."');";
			echo "</script>";
			exit;
		}
	}
	foreach($setInfos as $key => $val){
		$val['value']=$_GET[$val['key_en']];
		C::t('#gfarm#gfarm_set')->update($val['id'],$val);
	}
	echo "<script>";
	echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
	echo "</script>";
	exit;

} else{
	include template('gfarm:cp/set/plugin');
}

?>