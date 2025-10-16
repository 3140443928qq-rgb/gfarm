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

class table_gfarm_germ extends discuz_table
{
	public function __construct() {

		$this->_table = 'gfarm_germ';
		$this->_pk    = 'id';

		parent::__construct();
	}
	public function fetch_all_data($where='',$offset=0,$limit=65535){
		return DB::fetch_all('select a.*,b.img,b.name,b.use_level from %t a,%t b where a.relation_id=b.id %i limit %d,%d', array($this->_table,'gfarm_goods',$where,$offset,$limit));
	}
	public function fetch_first_data($where=''){
		return DB::fetch_first('select a.*,b.img,b.name from %t a,%t b where a.relation_id=b.id %i', array($this->_table,'gfarm_goods',$where));
	}
	public function count_all_data($where=''){
		return DB::result_first('select count(id) from %t where 1 %i', array($this->_table,$where));
	}
}

?>