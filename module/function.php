<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}

function isImage($filePath) { 
	$fileTypeArray=array("jpg","png","bmp","jpeg","gif","ico"); 
	$filePath=strtolower($filePath); 
	$lastPosition=strrpos($filePath,"."); 
	$isImage=false; 
	if($lastPosition>=0) { 
		$fileType=substr($filePath,$lastPosition+1,strlen($filePath)-$lastPosition); 
		if(in_array($fileType,$fileTypeArray)) { 
			$isImage=true; 
		} 
	} 
	return $isImage; 
}
function isImage1($fileb) { 
	$fileTypeArray=array(".jpg",".png",".bmp",".jpeg",".gif",".ico"); 
	$isImage=false; 
	if(in_array($fileb, $fileTypeArray)) { 	
		$isImage=true; 
	} 
	return $isImage; 
}
function deledepot($depot,$depnum) { //递归仓库
	if($depot['number']-$depnum<=0){
		C::t('#gfarm#gfarm_depot')->delete($depot['id']);
		if($depot['number']-$depnum<0){
			$depnum=$depnum-$depot['number'];
			$depot=C::t('#gfarm#gfarm_depot')->fetch_first_data(' and status=0 and uid='.$depot['uid'].' and goods_id='.$depot['goods_id'].' order by id desc');
			deledepot($depot,$depnum);
		}
	}else{
		C::t('#gfarm#gfarm_depot')->update($depot['id'],array(
			'number'=>$depot['number']-$depnum,
		));
	}
}
function jiesuofunc($landarr,$landlogarr) { //解锁日志
	C::t('#gfarm#gfarm_land_log')->insert($landlogarr);
	C::t('#gfarm#gfarm_member_land')->insert($landarr);	
}
function landfunc($landid,$userarr,$landarr,$landlogarr,$uid) { //农田日志
	C::t('#gfarm#gfarm_land_log')->insert($landlogarr);	
	C::t('#gfarm#gfarm_member')->update($uid,$userarr);
	C::t('#gfarm#gfarm_member_land')->update($landid,$landarr);		
}
function taskway($taskstr,$mytask){
	$str=$taskstr;
	if(in_array($mytask['task_way'], array(1,2,8))){
		if(!empty($mytask['target_object'])){
			$str.=' and good_id='.$mytask['target_object'];
		}
		if($mytask['task_way']==1){//解锁农田个数
			$str.=' and type=1';
		}elseif($mytask['task_way']==2){//铲除农田次数
			$str.=' and type=4';
		}elseif($mytask['task_way']==8){//使用道具次数
			$str.=' and type=5';
		}	
		$csnum=C::t('#gfarm#gfarm_land_log')->count_num($str);
	}elseif(in_array($mytask['task_way'], array(3,4,5,6,7,11,12,13,14,15,16,17))){
		if(!empty($mytask['target_object'])){
			$str.=' and goods_id='.$mytask['target_object'];
		}
		if($mytask['task_way']==3){//种植次数
			$str.=' and type=1';
		}elseif($mytask['task_way']==4){//收获次数
			$str.=' and type=2';
		}elseif($mytask['task_way']==5){//收获个数
			$str.=' and type=3';
		}elseif($mytask['task_way']==6){//偷窃次数
			$str.=' and type=4';
		}elseif($mytask['task_way']==7){//偷窃个数
			$str.=' and type=5';
		}elseif(in_array($mytask['task_way'], array(11,12))){//买入个数
			$str.=' and type=6';
		}elseif($mytask['task_way']==16){//买入金钱
			$str.=' and type=7';
		}elseif(in_array($mytask['task_way'], array(13,14,15))){//卖出个数
			$str.=' and type=8';
		}elseif($mytask['task_way']==17){//卖出金额
			$str.=' and type=9';
		}	
		$csnum=C::t('#gfarm#gfarm_land_all_log')->sum_num($str);
	}elseif($mytask['task_way']==9){//兑换金钱数量
		$csnum=C::t('#gfarm#gfarm_exchange_log')->sum_money($str);
	}elseif($mytask['task_way']==10){//签到天数
		$csnum=C::t('#gfarm#gfarm_member_signin')->count_num($str);
	}
	return abs($csnum);
}
function taskway1($taskstr,$mytask){
	$str=$taskstr;
	if(!in_array($mytask['task_way'], array(9,10,11,12,13,14,15,16,17))){
		if(!empty($mytask['target_object'])){
			$str.=' and good_id='.$mytask['target_object'];
		}
		if($mytask['task_way']==1){//解锁农田个数
			$str.=' and type=1';
		}elseif($mytask['task_way']==2){//铲除农田次数
			$str.=' and type=4';
		}elseif($mytask['task_way']==3){//种植种子次数
			$str.=' and type=2';
		}elseif($mytask['task_way']==4){//收获作物次数
			$str.=' and type=3';
		}elseif($mytask['task_way']==5){//收获作物个数
			$str.=' and type=3';
		}elseif($mytask['task_way']==6){//偷窃作物次数
			$str.=' and type=6';
		}elseif($mytask['task_way']==7){//偷窃作物个数
			$str.=' and type=6';
		}elseif($mytask['task_way']==8){//使用道具次数
			$str.=' and type=5';
		}
		if(in_array($mytask['task_way'], array(1,2,3,4,6,8))){
			$csnum=C::t('#gfarm#gfarm_land_log')->count_num($str);
		}else{
			$csnum=C::t('#gfarm#gfarm_land_log')->sum_num($str);
		}
	}elseif($mytask['task_way']==9){//兑换金钱数量
		$csnum=C::t('#gfarm#gfarm_exchange_log')->sum_money($str);
	}elseif($mytask['task_way']==10){//签到天数
		$csnum=C::t('#gfarm#gfarm_member_signin')->count_num($str);
	}elseif(in_array($mytask['task_way'], array(11,12,13,14,15,16,17))){//商店
		if(!empty($mytask['target_object'])){
			$str.=' and goods_id='.$mytask['target_object'];
		}
		if(in_array($mytask['task_way'], array(11,12,16))){//商店买入个数
			$str.=' and type=1';
		}elseif(in_array($mytask['task_way'], array(13,14,15,17))){//商店卖出个数
			$str.=' and type=2';
		}
		if(!in_array($mytask['task_way'], array(16,17))){
			$csnum=C::t('#gfarm#gfarm_depot_log')->count_num($str);
		}else{
			$csnum=C::t('#gfarm#gfarm_depot_log')->sum_num($str);
		}		
	}
	return abs($csnum);
}
function randnum($basenum){
	$randnum=rand(1, 100+$basenum);
	if($randnum<=45){
		$num=1;
	}elseif($randnum<=70+$basenum/4){
		$num=2;
	}elseif($randnum<=85+$basenum/2){
		$num=3;
	}elseif($randnum<=95+$basenum/4*3){
		$num=4;
	}else{
		$num=5;
	}
	return $num;
}
function adddepotfile($depots,$num,$depotfile,$goodid,$uid) {//仓库堆叠
	if(!empty($depots)){//当该作物在仓库中存在时
		if(empty($depotfile)){//当该作物堆叠无上限时
			C::t('#gfarm#gfarm_depot')->update($depots[0]['id'],array(
				'number'=>$num+$depots[0]['number'],
				'update_time'=>time(),
			));
		}else{//当该作物堆叠有上限时
			foreach ($depots as $value) {
				if($value['number']<$depotfile){
					if($num+$value['number']<$depotfile){
						C::t('#gfarm#gfarm_depot')->update($value['id'],array(
							'number'=>$num+$value['number'],
							'update_time'=>time(),
						));
						$num=0;
					}else{
						C::t('#gfarm#gfarm_depot')->update($value['id'],array(
								'number'=>$depotfile,
								'update_time'=>time(),
						));
						$num=$num-$depotfile+$value['number'];
					}
					if($num<=0){
						break;
					}
				}
			}
			if($num>0){
				$ncount=ceil($num/$depotfile);
				for($i=0;$i<$ncount;$i++){
					if($num-$depotfile*$i>$depotfile){
						$currnumber=$depotfile;
					}else{
						$currnumber=$num-$depotfile*$i;
					}
					C::t('#gfarm#gfarm_depot')->insert(array(
						'uid'=>$uid,
						'goods_id'=>$goodid,
						'number'=>$currnumber,
						'create_time'=>time(),
					));
				}
			}
		}
	}else{//当该作物在仓库中不存在时
		if(empty($depotfile)){//当该作物堆叠无上限时
			C::t('#gfarm#gfarm_depot')->insert(array(
				'uid'=>$uid,
				'goods_id'=>$goodid,
				'number'=>$num,
				'create_time'=>time(),
			));
		}else{//当该作物堆叠有上限时
			$ncount=ceil($num/$depotfile);
			for($i=0;$i<$ncount;$i++){
				if($num-$depotfile*$i>$depotfile){
					$currnumber=$depotfile;
				}else{
					$currnumber=$num-$depotfile*$i;
				}
				C::t('#gfarm#gfarm_depot')->insert(array(
					'uid'=>$uid,
					'goods_id'=>$goodid,
					'number'=>$currnumber,
					'create_time'=>time(),
				));
			}
		}
	}
}
		
	

?>