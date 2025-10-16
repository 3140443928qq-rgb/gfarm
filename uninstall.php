<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS `pre_gfarm_decorate`;
DROP TABLE IF EXISTS `pre_gfarm_depot`;
DROP TABLE IF EXISTS `pre_gfarm_depot_log`;
DROP TABLE IF EXISTS `pre_gfarm_equipment`;
DROP TABLE IF EXISTS `pre_gfarm_exp_level`;
DROP TABLE IF EXISTS `pre_gfarm_formula`;
DROP TABLE IF EXISTS `pre_gfarm_formula_goods`;
DROP TABLE IF EXISTS `pre_gfarm_formula_log`;
DROP TABLE IF EXISTS `pre_gfarm_germ`;
DROP TABLE IF EXISTS `pre_gfarm_germ_crop`;
DROP TABLE IF EXISTS `pre_gfarm_gifts`;
DROP TABLE IF EXISTS `pre_gfarm_goods`;
DROP TABLE IF EXISTS `pre_gfarm_items`;
DROP TABLE IF EXISTS `pre_gfarm_land_log`;
DROP TABLE IF EXISTS `pre_gfarm_lock_log`;
DROP TABLE IF EXISTS `pre_gfarm_member`;
DROP TABLE IF EXISTS `pre_gfarm_member_decorate`;
DROP TABLE IF EXISTS `pre_gfarm_member_land`;
DROP TABLE IF EXISTS `pre_gfarm_member_equipment`;
DROP TABLE IF EXISTS `pre_gfarm_member_formula`;
DROP TABLE IF EXISTS `pre_gfarm_set`;
DROP TABLE IF EXISTS `pre_gfarm_goods_separate`;
DROP TABLE IF EXISTS `pre_gfarm_goods_update`;
DROP TABLE IF EXISTS `pre_gfarm_operat_land`;
DROP TABLE IF EXISTS `pre_gfarm_combin`;
DROP TABLE IF EXISTS `pre_gfarm_member_picture`;
DROP TABLE IF EXISTS `pre_gfarm_member_signin`;
DROP TABLE IF EXISTS `pre_gfarm_message`;
DROP TABLE IF EXISTS `pre_gfarm_member_collect`;
DROP TABLE IF EXISTS `pre_gfarm_sale`;
DROP TABLE IF EXISTS `pre_gfarm_vip`;
DROP TABLE IF EXISTS `pre_gfarm_task`;
DROP TABLE IF EXISTS `pre_gfarm_task_way`;
DROP TABLE IF EXISTS `pre_gfarm_member_task`;
DROP TABLE IF EXISTS `pre_gfarm_exchange_log`;
DROP TABLE IF EXISTS `pre_gfarm_signin`;
DROP TABLE IF EXISTS `pre_gfarm_signin_reward`;
DROP TABLE IF EXISTS `pre_gfarm_achieve_task`;
DROP TABLE IF EXISTS `pre_gfarm_bounty_task`;
DROP TABLE IF EXISTS `pre_gfarm_daily_task`;
DROP TABLE IF EXISTS `pre_gfarm_main_task`;
DROP TABLE IF EXISTS `pre_gfarm_member_achieve_task`;
DROP TABLE IF EXISTS `pre_gfarm_member_bounty_task`;
DROP TABLE IF EXISTS `pre_gfarm_member_daily_task`;
DROP TABLE IF EXISTS `pre_gfarm_member_main_task`;
DROP TABLE IF EXISTS `pre_gfarm_act_task`;
DROP TABLE IF EXISTS `pre_gfarm_daily_act`;
DROP TABLE IF EXISTS `pre_gfarm_member_act`;
DROP TABLE IF EXISTS `pre_gfarm_plugin`;
DROP TABLE IF EXISTS `pre_gfarm_lock`;
DROP TABLE IF EXISTS `pre_gfarm_food`;
DROP TABLE IF EXISTS `pre_gfarm_land_all_log`;
EOF;
runquery($sql);	
$finish = TRUE;
?>