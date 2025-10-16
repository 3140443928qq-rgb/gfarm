<?php
/**
 * 作者：Crazy创意工作室
 * Q Q：25466413
 * 介绍：活动中心中活动专区的设置表。
 *
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_gfarm_set extends discuz_table
{
	public function __construct() {

		$this->_table = 'gfarm_set';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_all_data($where='',$offset=0,$limit=65535){
		return DB::fetch_all('select * from %t where 1 %i order by displayorder asc limit %d,%d', array($this->_table,$where,$offset,$limit));
	}
	
	public function count_all_data($where=''){
		return DB::result_first('select count(id) from %t where 1 %i', array($this->_table,$where));
	}
	
	public function fetch_setInfo(){
		$res=DB::fetch_all('select key_en,value from %t',array($this->_table));
		foreach($res as $key => $val){
			$result[$val['key_en']]=$val['value'];
		}
		return $result;
	}
	public function fetch_mod(){
		$res=DB::fetch_all('select mod_en,mod_cn from %t group by mod_en',array($this->_table));
		foreach($res as $key => $val){
			$result[$val['mod_en']]=$val['mod_cn'];
		}
		return $result;
	}
}

?>