<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
if($_GET['act']=='clear'){
	$logn=$_GET['logn'];
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		preg_match($tt,$_GET['number'],$result); 
		if(empty($result)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','074')."');";
			echo "</script>";
			exit;
		}
		if($logn==1){
			$logname=lang('plugin/gfarm','148');
			$data=C::t('#gfarm#gfarm_land_log')->count_all_data(" and type=2 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
			DB::delete(C::t('#gfarm#gfarm_land_log'), " type=2 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
		}elseif($logn==2){
			$logname=lang('plugin/gfarm','149');
			$data=C::t('#gfarm#gfarm_land_log')->count_all_data(" and type=3 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
			DB::delete(C::t('#gfarm#gfarm_land_log'), " type=3 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
		}elseif($logn==3){
			$logname=lang('plugin/gfarm','150');
			$data=C::t('#gfarm#gfarm_land_log')->count_all_data(" and type=6 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
			DB::delete(C::t('#gfarm#gfarm_land_log'), " type=6 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
		}elseif($logn==4){
			$logname=lang('plugin/gfarm','151');
			$data=C::t('#gfarm#gfarm_depot_log')->count_all_data(" and type=1 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
			DB::delete(C::t('#gfarm#gfarm_depot_log'), " type=1 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
		}elseif($logn==5){
			$logname=lang('plugin/gfarm','152');
			$data=C::t('#gfarm#gfarm_depot_log')->count_all_data(" and type=2 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
			DB::delete(C::t('#gfarm#gfarm_depot_log'), " type=2 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd",time()-24*60*60*($_GET['number']-1)));
		}
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','153').$data.lang('plugin/gfarm','154').$logname."');";
		echo "</script>";
		exit;
	}
	include template('gfarm:cp/log/deletelog');
	exit;
}
$nplanting=C::t('#gfarm#gfarm_land_log')->count_all_data(" and type=2 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd"));
$ngot=C::t('#gfarm#gfarm_land_log')->count_all_data(" and type=3 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd"));
$nsteal=C::t('#gfarm#gfarm_land_log')->count_all_data(" and type=6 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd"));
$nbuy=C::t('#gfarm#gfarm_depot_log')->count_all_data(" and type=1 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd"));
$nsale=C::t('#gfarm#gfarm_depot_log')->count_all_data(" and type=2 and FROM_UNIXTIME(create_time,'%Y%m%d')<".date("Ymd"));
include template('gfarm:cp/log/clearlog');
?>