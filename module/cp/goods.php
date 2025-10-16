<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$str="";
$text="";
$currpage=$_GET['page']?$_GET['page']:1;
$perpage=15;
$gid=$_GET['gid']?$_GET['gid']:0;
$do=$_GET['do'];
$start=($currpage-1)*$perpage;
//通用物品
if($_GET['act']=='update'){	
	$goods=C::t('#gfarm#gfarm_goods')->fetch($gid);
	$smalltype=C::t('#gfarm#gfarm_goods_separate')->fetch_all_data(' and pid='.$do);
	if($do=='9'){//种子
		$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$gid);
	}
	if($do=='1'){//装备
		$equipment=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$gid);
	}
	if($do=='4'){//装扮
		$decorate=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$gid);
	}
	if($do=='19'){//道具
		$items=C::t('#gfarm#gfarm_items')->fetch_first_data(' and relation_id='.$gid);
	}
	if($do=='15'){//礼包
		$gifts=C::t('#gfarm#gfarm_gifts')->fetch_first_data(' and relation_id='.$gid);
	}
	if($do=='14'){//食物
		$food=C::t('#gfarm#gfarm_food')->fetch_first_data(' and relation_id='.$gid);
	}
	if($_GET['formhash']==formhash()){
		$tt='/^([1-9]+[0-9]*)$/';
		$tt1='/^(([1-9]+[0-9]*)|[0])$/';
		preg_match($tt1,$_GET['buy_price'],$result1); 
		preg_match($tt1,$_GET['sale_price'],$result2); 
		preg_match($tt1,$_GET['use_level'],$result3); 
		preg_match($tt1,$_GET['use_benevolence'],$result4);
		preg_match($tt,$_GET['sale_count'],$result5); 
		preg_match($tt1,$_GET['add_date'],$result6); 
		preg_match($tt1,$_GET['depot_pile'],$result7); 
		if(empty($_GET['name'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','002')."');";
			echo "</script>";
			exit;
		}else{
			$cname=C::t('#gfarm#gfarm_goods')->count_all_data(" and name='".$_GET['name']."'");
			if(empty($gid)){
				if(!empty($cname)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
					echo "</script>";
					exit;
				}
			}else{
				if($goods['name']!=$_GET['name']){
					if(!empty($cname)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','003')."');";
						echo "</script>";
						exit;
					}
				}
			}
		}		
		if(empty($_GET['_img'])){
			$imagesrc=$_GET['imgsrc0'];
			if(empty($imagesrc)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','028')."');";
				echo "</script>";
				exit;
			}
			if(!isImage($imagesrc)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
				echo "</script>";
				exit;
			}			
		}elseif(!isImage($_GET['_img'])){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
			echo "</script>";
			exit;
		}else{
			$imagesrc=$_GET['_img'];
			$flagsrc=1;
		}
		if(empty($result1)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','030')."');";
			echo "</script>";
			exit;
		}	
		if(empty($result2)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','031')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['use_level'])){
			$_GET['use_level']=0;
		}elseif(empty($result3)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','032')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['use_benevolence'])){
			$_GET['use_benevolence']=0;
		}elseif(empty($result4)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','033')."');";
			echo "</script>";
			exit;
		}		
		if(empty($_GET['sale_count'])){
			$_GET['sale_count']=0;
		}elseif(empty($result5)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','034')."');";
			echo "</script>";
			exit;
		}	
		if(empty($_GET['add_date'])){
			$_GET['add_date']=0;
		}elseif(empty($result6)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','035')."');";
			echo "</script>";
			exit;
		}	
		$begin=0;
		$end=0;
		if(!empty($_GET['sale_time_begin'])||!empty($_GET['sale_time_end'])){
			if(!empty($_GET['sale_time_begin'])){
				$begin=strtotime($_GET['sale_time_begin']);
			}
			if(!empty($_GET['sale_time_end'])){
				$end=strtotime($_GET['sale_time_end']);
			}
			if(!empty($_GET['sale_time_begin'])&&!empty($_GET['sale_time_end'])){
				if($begin>$end){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','018')."');";
					echo "</script>";
					exit;
				}
			}
		}
		if(empty($_GET['depot_pile'])){
			$_GET['depot_pile']=0;
		}elseif(empty($result7)){
			echo "<script language='javascript'>";
			echo "parent.window.showerror('".lang('plugin/gfarm','036')."');";
			echo "</script>";
			exit;
		}
		$updatearr1=array(
			'sort'=>$_GET['sort'],
			'name'=>$_GET['name'],
			'money_types'=>$_GET['money_types'],
			'money_typeb'=>$_GET['money_typeb'],
			'buy_price'=>$_GET['buy_price'],
			'sale_price'=>$_GET['sale_price'],
			'use_level'=>$_GET['use_level'],
			'use_benevolence'=>$_GET['use_benevolence'],
			'use_flag'=>$_GET['use_flag'],
			'depot_pile'=>$_GET['depot_pile'],
			'sale_count'=>$_GET['sale_count'],
			'add_date'=>$_GET['add_date'],
			'sale_time_begin'=>$begin,
			'sale_time_end'=>$end,
			'transaction_flag'=>$_GET['transaction_flag'],
			'description'=>$_GET['description'],
			'update_uid'=>$_G['uid'],
			'update_time'=>time(),
		);
		if(in_array($do, array(12,13))){
			$updatearr1['big_type']=11;
			$updatearr1['small_type']=$do;
		}else{
			$updatearr1['big_type']=$do;
		}
		if($do=='9'){//种子提交
			preg_match($tt,$_GET['experience'],$result1);
			preg_match($tt,$_GET['mature_time'],$result2);
			
			if(empty($_GET['_sprout_img'])){
				$image=$_GET['img0'];
				if(empty($image)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','037')."');";
					echo "</script>";
					exit;
				}
				if(!isImage($image)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
					echo "</script>";
					exit;
				}
			}elseif(!isImage($_GET['_sprout_img'])){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
				echo "</script>";
				exit;
			}else{
				$image=$_GET['_sprout_img'];
				$flag=1;
			}
			if(empty($_GET['_mature_img'])){
				$image1=$_GET['img1'];
				if(empty($image1)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','038')."');";
					echo "</script>";
					exit;
				}
				if(!isImage($image1)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
					echo "</script>";
					exit;
				}
			}elseif(!isImage($_GET['_mature_img'])){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
				echo "</script>";
				exit;
			}else{
				$image1=$_GET['_mature_img'];
				$flag1=1;
			}
			if(empty($result2)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','039')."');";
				echo "</script>";
				exit;
			}
			if(empty($result1)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','040')."');";
				echo "</script>";
				exit;
			}
			if(empty($_GET['_sprout_img'])){
				if($_FILES['sprout_img']['tmp_name']) {//图片处理
					$picname = $_FILES['sprout_img']['name'];
					if ($picname != "") {
						$type = strstr($picname, '.');
						if(!isImage1($type)){
							echo "<script language='javascript'>";
							echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
							echo "</script>";
							exit;
						}
						$rand = rand(100, 999);
						$pics = date("YmdHis") . $rand . $type;
						$image = "source/plugin/gfarm/upload/germ/". $pics;
						move_uploaded_file($_FILES['sprout_img']['tmp_name'], $image);
						@unlink($_FILES['sprout_img']['tmp_name']);
					}
					$flag=1;
				}
			}
				
			if(empty($_GET['_mature_img'])){
				if($_FILES['mature_img']['tmp_name']) {//图片处理
					$picname = $_FILES['mature_img']['name'];
					if ($picname != "") {
						$type = strstr($picname, '.');
						if(!isImage1($type)){
							echo "<script language='javascript'>";
							echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
							echo "</script>";
							exit;
						}
						$rand = rand(100, 999);
						$pics = date("YmdHis") . $rand . $type;
						$image1 = "source/plugin/gfarm/upload/germ/". $pics;
						move_uploaded_file($_FILES['mature_img']['tmp_name'], $image1);
						@unlink($_FILES['mature_img']['tmp_name']);
					}
					$flag1=1;
				}
			}
			$updatearr=array(
			'experience'=>$_GET['experience'],
			'mature_time'=>$_GET['mature_time'],
			'sprout_img'=>$image,
			'mature_img'=>$image1,
			);
		}
		if($do=='1'){//装备提交
			$tt='/^(([1-9]+[0-9]*)|[0])$/';
			$tt1='/^[+-]?(([1-9]+[0-9]*)|[0])$/';
			preg_match($tt1,$_GET['add_steal_odds'],$result1);
			preg_match($tt1,$_GET['add_steal_number'],$result2);
			preg_match($tt,$_GET['valid_time'],$result3);
			if(empty($_GET['add_steal_odds'])){
				$_GET['add_steal_odds']=0;
			}elseif(empty($result1)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','041')."');";
				echo "</script>";
				exit;
			}
			if(empty($_GET['add_steal_number'])){
				$_GET['add_steal_number']=0;
			}elseif(empty($result2)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','042')."');";
				echo "</script>";
				exit;
			}
			if(empty($_GET['valid_time'])){
				$_GET['valid_time']=0;
			}elseif(empty($result3)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','043')."');";
				echo "</script>";
				exit;
			}			
			$updatearr=array(
			'add_steal_odds'=>$_GET['add_steal_odds'],
			'add_steal_number'=>$_GET['add_steal_number'],
			'valid_time'=>$_GET['valid_time'],				
			);
		}
		if($do=='4'){//装扮提交
			$tt='/^(([1-9]+[0-9]*)|[0])$/';
			$tt1='/^[+-]?(([1-9]+[0-9]*)|[0])$/';
			preg_match($tt1,$_GET['add_steal_odds'],$result1);
			preg_match($tt1,$_GET['add_steal_number'],$result2);
			preg_match($tt1,$_GET['add_gain_number'],$result4);
			preg_match($tt1,$_GET['add_gain_time'],$result5);
			preg_match($tt,$_GET['valid_time'],$result3);
			if(empty($_GET['add_gain_number'])){
				$_GET['add_gain_number']=0;
			}elseif(empty($result4)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','044')."');";
				echo "</script>";
				exit;
			}			
			if(empty($_GET['add_gain_time'])){
				$_GET['add_gain_time']=0;
			}elseif(empty($result5)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','045')."');";
				echo "</script>";
				exit;
			}
			if(empty($_GET['add_steal_odds'])){
				$_GET['add_steal_odds']=0;
			}elseif(empty($result1)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','041')."');";
				echo "</script>";
				exit;
			}
			if(empty($_GET['add_steal_number'])){
				$_GET['add_steal_number']=0;
			}elseif(empty($result2)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','042')."');";
				echo "</script>";
				exit;
			}			
			if(empty($_GET['valid_time'])){
				$_GET['valid_time']=0;
			}elseif(empty($result3)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','043')."');";
				echo "</script>";
				exit;
			}
			$updatearr=array(
			'add_steal_odds'=>$_GET['add_steal_odds'],
			'add_steal_number'=>$_GET['add_steal_number'],
			'add_gain_number'=>$_GET['add_gain_number'],
			'add_gain_time'=>$_GET['add_gain_time'],
			'valid_time'=>$_GET['valid_time'],
			);
		}
		if($do=='14'){//食物提交
			$tt='/^(([1-9]+[0-9]*)|[0])$/';
			preg_match($tt,$_GET['addexp'],$result1);
			preg_match($tt,$_GET['addbene'],$result2);
			preg_match($tt,$_GET['addstren'],$result3);
			if(empty($_GET['addexp'])){
				$_GET['addexp']=0;
			}elseif(empty($result1)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','081')."');";
				echo "</script>";
				exit;
			}			
			if(empty($_GET['addbene'])){
				$_GET['addbene']=0;
			}elseif(empty($result2)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','084')."');";
				echo "</script>";
				exit;
			}
			if(empty($_GET['addstren'])){
				$_GET['addstren']=0;
			}elseif(empty($result3)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','083')."');";
				echo "</script>";
				exit;
			}
			$updatearr=array(
			'addexp'=>$_GET['addexp'],
			'addbene'=>$_GET['addbene'],
			'addstren'=>$_GET['addstren'],
			);
		}
		if($do=='19'){//道具提交
			$tt='/^[+-]?(([1-9]+[0-9]*)|[0])$/';
			preg_match($tt,$_GET['add_gain_number'],$result1);
			preg_match($tt,$_GET['add_gain_time'],$result2);
			preg_match($tt,$_GET['add_benevolence'],$result3);
			if(empty($_GET['add_gain_number'])){
				$_GET['add_gain_number']=0;
			}elseif(empty($result1)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','044')."');";
				echo "</script>";
				exit;
			}
			if(empty($_GET['add_gain_time'])){
				$_GET['add_gain_time']=0;
			}elseif(empty($result2)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','045')."');";
				echo "</script>";
				exit;
			}
			if(empty($_GET['add_benevolence'])){
				$_GET['add_benevolence']=0;
			}elseif(empty($result3)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','046')."');";
				echo "</script>";
				exit;
			}
			$updatearr=array(
			'add_gain_number'=>$_GET['add_gain_number'],
			'add_gain_time'=>$_GET['add_gain_time'],
			'add_benevolence'=>$_GET['add_benevolence'],
			'use_target'=>$_GET['use_target'],
			);
		}
		if($do=='15'){//礼包提交
			preg_match($tt,$_GET['open_number'],$result1);
			$goods_id=trim($_GET['goods_id']);
			$strs0=explode('/', $goods_id);
			$cgoodid=count($strs0);
			if(empty($goods_id)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','047')."');";
				echo "</script>";
				exit;
			}else{
				foreach ($strs0 as $key=>$value) {
					preg_match($tt,$value,$bol);
					if(empty($bol)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','048').($key+1).lang('plugin/gfarm','049')."');";
						echo "</script>";
						exit;
					}
				}
			}
			$goods_number=trim($_GET['goods_number']);
			$strs0=explode('/',$goods_number);
			if(empty($goods_number)){
				echo "<script language='javascript'>";
				echo "parent.window.showerror('".lang('plugin/gfarm','050')."');";
				echo "</script>";
				exit;
			}elseif(count($strs0)=='1'){
				preg_match($tt,$goods_number,$bol);
				if(empty($bol)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','023')."');";
					echo "</script>";
					exit;
				}
			}else{
				if(count($strs0)!=$cgoodid){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','051')."');";
					echo "</script>";
					exit;
				}
				foreach ($strs0 as $key=>$value) {
					preg_match($tt,$value,$bol);
					if(empty($bol)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','048').($key+1).lang('plugin/gfarm','052')."');";
						echo "</script>";
						exit;
					}
				}
			}
			$updatearr=array(
			'goods_id'=>$goods_id,
			'goods_number'=>$goods_number,
			'open_type'=>$_GET['open_type'],
			);
			if($_GET['open_type']=='1'){
				if(empty($result1)){
					echo "<script language='javascript'>";
					echo "parent.window.showerror('".lang('plugin/gfarm','053')."');";
					echo "</script>";
					exit;
				}
				$updatearr['open_number']=$_GET['open_number'];
			}
		}
		if(empty($_GET['_img'])){
			if($_FILES['img']['tmp_name']) {//图片处理
				$picname = $_FILES['img']['name'];
				if ($picname != "") {
					$type = strstr($picname, '.');
					if(!isImage1($type)){
						echo "<script language='javascript'>";
						echo "parent.window.showerror('".lang('plugin/gfarm','029')."');";
						echo "</script>";
						exit;
					}
					$rand = rand(100, 999);
					$pics = date("YmdHis") . $rand . $type;
					$imagesrc = "source/plugin/gfarm/upload/goods/". $pics;
					move_uploaded_file($_FILES['img']['tmp_name'], $imagesrc);
					@unlink($_FILES['img']['tmp_name']);
				}
				$flagsrc=1;
			}
		}
		$updatearr1['img']=$imagesrc;
		if(!empty($smalltype)){
			$updatearr1['small_type']=$_GET['small_type'];
		}
		if(empty($gid)){
			$updatearr1['create_time']=time();
			$updatearr1['create_uid']=$_G['uid'];
			$goodid=C::t('#gfarm#gfarm_goods')->insert($updatearr1,true);
			$updatearr['relation_id']=$goodid;
			C::t('#gfarm#gfarm_goods_update')->insert(array(
				'goods_id'=>$goodid,
				'still_num'=>$_GET['sale_count'],
				'update_time'=>time(),
			));

		}else{
			C::t('#gfarm#gfarm_goods')->update($gid,$updatearr1);
			$goodupdate=C::t('#gfarm#gfarm_goods_update')->fetch_first_data(' and goods_id='.$gid);
			C::t('#gfarm#gfarm_goods_update')->update($goodupdate['id'],array(
				'still_num'=>$_GET['sale_count'],
			));
			if(empty($goods['sale_count'])&&!empty($_GET['sale_count'])){				
				C::t('#gfarm#gfarm_goods_update')->update($goodupdate['id'],array(
					'update_time'=>time(),
				));
			}
			if($flagsrc==1&&$goods['img']!=$imagesrc){
				@unlink($goods['img']);
			}
			$updatearr['relation_id']=$gid;
		}
		if($do=='9'){
			if(empty($germ)){
				C::t('#gfarm#gfarm_germ')->insert($updatearr);
			}else{
				C::t('#gfarm#gfarm_germ')->update($germ['id'],$updatearr);
				if($flag==1&&$germ['sprout_img']!=$image){
					@unlink($germ['sprout_img']);
				}
				if($flag1==1&&$germ['mature_img']!=$image1){
					@unlink($germ['mature_img']);
				}
			}
		}
		if($do=='1'){
			if(empty($equipment)){
				C::t('#gfarm#gfarm_equipment')->insert($updatearr);
			}else{
				C::t('#gfarm#gfarm_equipment')->update($equipment['id'],$updatearr);
			}
		}
		if($do=='4'){
			if(empty($decorate)){
				C::t('#gfarm#gfarm_decorate')->insert($updatearr);
			}else{
				C::t('#gfarm#gfarm_decorate')->update($decorate['id'],$updatearr);
			}
		}
		if($do=='14'){
			if(empty($food)){
				C::t('#gfarm#gfarm_food')->insert($updatearr);
			}else{
				C::t('#gfarm#gfarm_food')->update($food['id'],$updatearr);
			}
		}
		if($do=='19'){
			if(empty($items)){
				C::t('#gfarm#gfarm_items')->insert($updatearr);
			}else{
				C::t('#gfarm#gfarm_items')->update($items['id'],$updatearr);
			}
		}
		if($do=='15'){
			if(empty($gifts)){
				C::t('#gfarm#gfarm_gifts')->insert($updatearr);
			}else{
				C::t('#gfarm#gfarm_gifts')->update($gifts['id'],$updatearr);
			}
		}
		
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
	if(!empty($smalltype)){
		$flag=1;
	}
	include template('gfarm:cp/goods/newgoods');
	exit;
}
if($_GET['act']=='delete'){		
	if($_GET['formhash']==formhash()){
		$goods=C::t('#gfarm#gfarm_goods')->fetch($gid);		
		if($goods['big_type']=='9'){//种子
			$germ=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$gid);
			C::t('#gfarm#gfarm_germ')->delete($germ['id']);
		}
		if($goods['big_type']=='1'){//装备
			$equipment=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$gid);
			C::t('#gfarm#gfarm_equipment')->delete($equipment['id']);
		}
		if($goods['big_type']=='4'){//装扮
			$decorate=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$gid);
			C::t('#gfarm#gfarm_decorate')->delete($decorate['id']);
		}
		if($goods['big_type']=='19'){//道具
			$items=C::t('#gfarm#gfarm_items')->fetch_first_data(' and relation_id='.$gid);
			C::t('#gfarm#gfarm_items')->delete($items['id']);
		}
		if($goods['big_type']=='15'){//礼包
			$gifts=C::t('#gfarm#gfarm_gifts')->fetch_first_data(' and relation_id='.$gid);
			C::t('#gfarm#gfarm_gifts')->delete($gifts['id']);
		}
		C::t('#gfarm#gfarm_goods')->delete($gid);
		echo "<script>";
		echo "parent.window.hideWindow('gfarm');";
		echo "parent.window.showmsg('".lang('plugin/gfarm','012')."');";
		echo "</script>";
		exit;
	}
}
$stre='';
if(empty($cequipment)){
	$stre.=' and id!=1 and id!=4';
	$str.=' and big_type!=1 and big_type!=4';
}
if(empty($cgifts)){
	$stre.=' and id!=15';
	$str.=' and big_type!=15';
}
$separate=C::t('#gfarm#gfarm_goods_separate')->fetch_all_data(' and (pid=0 or pid=11) and id!=11'.$stre);
$separates=C::t('#gfarm#gfarm_goods_separate')->fetch_all_data();
foreach ($separates as $value) {
	$separats[$value['id']]=$value;
} 
$goodtype=$_GET['goodtype'];
$goodid=$_GET['goodid'];
$goodname=$_GET['goodname'];
if(!empty($goodtype)){
	if(in_array($goodtype, array(12,13))){
		$str.=' and small_type='.$goodtype;
	}else{
		$str.=' and big_type='.$goodtype;
	}		
	$text.='&goodtype='.$goodtype;
}
$tt='/^([1-9]+[0-9]*)$/';
preg_match($tt,$goodid,$result); 
if(!empty($result)){
	$str.=' and id='.$goodid;
	$text.='&goodid='.$goodid;
}else{
	$goodid='';
}
if(!empty($goodname)){
	$str.=" and name like '%".$goodname."%'";
	$text.='&goodname='.$goodname;
}
if(!empty($do)){	
	if(in_array($do, array(12,13))){
		$str.=' and small_type='.$do;
	}else{
		$str.=' and big_type='.$do;
	}	
	$text.='&do='.$do;
}
$orderfield1=$_GET['orderfield'];
$ordertype1=$_GET['ordertype'];
if(empty($orderfield1)){
	$orderfield1='id';
}
if($ordertype1=='asc'||empty($ordertype1)){
	$ordertype='desc';
	$ordertype1='asc';	
}else{
	$ordertype='asc';
}
$str.=' order by '.$orderfield1.' '.$ordertype1;
$text.='&orderfield='.$orderfield1.'&ordertype='.$ordertype1;
$goodsInfos=C::t('#gfarm#gfarm_goods')->fetch_all_data($str,$start,$perpage);
if($do==9){
	foreach ($goodsInfos as $key=>$value) {
		$datas[$key]=C::t('#gfarm#gfarm_germ')->fetch_first_data(' and relation_id='.$value['id']);
	}
}elseif($do==1){
	foreach ($goodsInfos as $key=>$value) {
		$datas[$key]=C::t('#gfarm#gfarm_equipment')->fetch_first_data(' and relation_id='.$value['id']);
	}
}elseif($do==4){
	foreach ($goodsInfos as $key=>$value) {
		$datas[$key]=C::t('#gfarm#gfarm_decorate')->fetch_first_data(' and relation_id='.$value['id']);
	}
}elseif($do==14){
	foreach ($goodsInfos as $key=>$value) {
		$datas[$key]=C::t('#gfarm#gfarm_food')->fetch_first_data(' and relation_id='.$value['id']);
	}
}elseif($do==19){
	foreach ($goodsInfos as $key=>$value) {
		$datas[$key]=C::t('#gfarm#gfarm_items')->fetch_first_data(' and relation_id='.$value['id']);
	}
}elseif($do==15){
	foreach ($goodsInfos as $key1=>$value) {
		$datas[$key1]=C::t('#gfarm#gfarm_gifts')->fetch_first_data(' and relation_id='.$value['id']);
		$strs=explode('/', $datas[$key1]['goods_id']);
		$strs1=explode('/', $datas[$key1]['goods_number']);
		$str1='';
		foreach ($strs as $key=>$value) {
			$good=C::t('#gfarm#gfarm_goods')->fetch($value);
			if(count($strs1)==1){
				$strs1[$key]=$datas[$key1]['goods_number'];
			}
			$str1.=$good['name'].'x'.$strs1[$key].',';
		}
		$strss[$key1]=substr($str1, 0,-1);
	}
}

$num=C::t('#gfarm#gfarm_goods')->count_all_data($str);
if ($num == 0){
	$maxpage = 1;
}else {
	$maxpage = ceil($num/$perpage);
}
/*分页*/
$page_number_arr = page_view($currpage, $maxpage);
$page_width_arr = page_div_and_li_width($currpage, $maxpage);
include template('gfarm:cp/goods/goods');
	

?>