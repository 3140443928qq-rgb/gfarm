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

class table_gfarm_member extends discuz_table
{
	public function __construct() {

		$this->_table = 'gfarm_member';
		$this->_pk    = 'uid';

		parent::__construct();
	}
	public function fetch_all_data($where='',$offset=0,$limit=65535){
		return DB::fetch_all('select a.*,b.username from %t a,%t b where a.uid=b.uid %i limit %d,%d', array($this->_table,'common_member',$where,$offset,$limit));
	}
	public function fetch_first_data($where=''){
		return DB::fetch_first('select * from %t where 1 %i', array($this->_table,$where));
	}
	public function count_all_data($where=''){
		return DB::result_first('select count(a.uid) from %t a,%t b where a.uid=b.uid %i', array($this->_table,'common_member',$where));
	}
	public function fetch_by_uid($uid){
		return DB::fetch_first('select *,a.status astatus from %t a,%t b where a.uid=b.uid and a.uid=%d', array($this->_table,'common_member',$uid));
	}
	public function update_by_uid($exp,$stren,$uid){
		return DB::result_first('update %t set experience=experience+%d,now_strength=now_strength-%d where uid=%d', array($this->_table,$exp,$stren,$uid));
	}
}

?>