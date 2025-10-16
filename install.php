<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_gfarm_decorate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_id` int(11) NOT NULL,
  `add_steal_odds` int(11) NOT NULL,
  `add_steal_number` int(11) NOT NULL,
  `add_gain_number` int(11) NOT NULL,
  `add_gain_time` int(11) NOT NULL,
  `valid_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `pre_gfarm_depot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `islock` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `pre_gfarm_depot_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_number` int(11) NOT NULL,
  `money_type` int(11) NOT NULL,
  `money_price` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `pre_gfarm_equipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_id` int(11) NOT NULL,
  `add_steal_odds` int(11) NOT NULL,
  `add_steal_number` int(11) NOT NULL,
  `valid_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `pre_gfarm_exp_level` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `level` int(11) NOT NULL,
  `experience` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `land_number` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `pre_gfarm_formula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `good_ids` varchar(250) NOT NULL,
  `success_id` int(11) NOT NULL,
  `success_odd` int(11) NOT NULL,
  `stuff_ids` varchar(250) NOT NULL,
  `stuff_odds` varchar(250) NOT NULL,
  `fail_odd` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `create_uid` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `pre_gfarm_formula_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `use_level` int(11) NOT NULL,
  `good_ids` varchar(250) NOT NULL,
  `good_numbers` varchar(250) NOT NULL,
  `produce_id` int(11) NOT NULL,
  `produce_big` int(11) NOT NULL,
  `produce_small` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `create_uid` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `pre_gfarm_formula_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `formula_id` int(11) NOT NULL,
  `formula_name` varchar(50) NOT NULL,
  `goods` varchar(250) NOT NULL,
  `produce` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `pre_gfarm_germ` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_id` int(11) NOT NULL,
  `sprout_img` varchar(200) NOT NULL,
  `mature_img` varchar(200) NOT NULL,
  `experience` int(11) NOT NULL,
  `mature_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `pre_gfarm_germ_crop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort` varchar(50) NOT NULL,
  `germ_id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `produce_big` int(11) NOT NULL,
  `produce_small` int(11) NOT NULL,
  `produce_odds` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `create_uid` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `pre_gfarm_gifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_id` int(11) NOT NULL,
  `goods_id` varchar(250) NOT NULL,
  `goods_number` varchar(250) NOT NULL,
  `open_type` int(11) NOT NULL,
  `open_number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `big_type` int(11) NOT NULL,
  `small_type` int(11) NOT NULL,
  `money_typeb` int(11) NOT NULL,
  `money_types` int(11) NOT NULL,
  `buy_price` int(11) NOT NULL,
  `sale_price` int(11) NOT NULL,
  `use_level` int(11) NOT NULL,
  `use_benevolence` int(11) NOT NULL,
  `use_flag` int(11) NOT NULL,
  `sale_count` int(11) NOT NULL,
  `add_date` int(11) NOT NULL,
  `sale_time_begin` int(11) NOT NULL,
  `sale_time_end` int(11) NOT NULL,
  `transaction_flag` int(11) NOT NULL,
  `img` varchar(200) NOT NULL,
  `description` varchar(250) NOT NULL,
  `depot_pile` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `create_uid` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_uid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_id` int(11) NOT NULL,
  `add_gain_number` int(11) NOT NULL,
  `add_gain_time` int(11) NOT NULL,
  `add_benevolence` int(11) NOT NULL,
  `use_target` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_land_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `target_uid` int(11) NOT NULL,
  `target_land` int(11) NOT NULL,
  `good_id` int(11) NOT NULL,
  `good_name` varchar(50) NOT NULL,
  `germ_id` int(11) NOT NULL,
  `germ_name` varchar(50) NOT NULL,
  `good_number` int(11) NOT NULL,
  `add_gain_number` int(11) NOT NULL,
  `add_gain_time` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_member` (
  `uid` int(11) NOT NULL,
  `experience` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `now_strength` int(11) NOT NULL,
  `benevolence` int(11) NOT NULL,
  `register_time` int(11) NOT NULL,
  `last_visit` int(11) NOT NULL,
  `last_restrength` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_land` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `lock_time` int(11) NOT NULL,
  `land_id` int(11) NOT NULL,
  `land_number` int(11) NOT NULL,
  `germ_id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `germ_time` int(11) NOT NULL,
  `gain_time` int(11) NOT NULL,
  `add_gain_number` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `gain_number` int(11) NOT NULL,
  `steal_number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `pre_gfarm_member_formula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `goods_type` int(11) NOT NULL,
  `use_number` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `use_first_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)  ;

CREATE TABLE IF NOT EXISTS `pre_gfarm_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_cn` varchar(20) NOT NULL,
  `mod_en` varchar(20) NOT NULL,
  `key_cn` varchar(20) NOT NULL,
  `key_en` varchar(20) NOT NULL,
  `regex_en` varchar(200) NOT NULL,
  `regex_cn` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `value` varchar(200) NOT NULL,
  `status` int(11) NOT NULL,
  `displayorder` int(11) NOT NULL,
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_goods_separate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `separate_name` varchar(200) NOT NULL,
  `pid` int(11) NOT NULL,
  `table_name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_goods_update` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `still_num` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_operat_land` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `land_id` int(11) NOT NULL,
  `steal_time` int(11) NOT NULL,
  `item_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_combin` (
  `uid` int(11) NOT NULL,
  `goods_id1` int(11) NOT NULL,
  `goods_id2` int(11) NOT NULL,
  `goods_id3` int(11) NOT NULL,
  `goods_id4` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_signin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)  ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send_uid` int(11) NOT NULL,
  `receive_uid` int(11) NOT NULL,
  `send_content` varchar(200) NOT NULL,
  `send_flag` int(11) NOT NULL,
  `receive_flag` int(11) NOT NULL,
  `read_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `receive_uid` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `depot_id` int(11) NOT NULL,
  `good_price` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_vip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `vipname` varchar(50) NOT NULL,
  `reshopmoney` int(11) NOT NULL,
  `relockmoney` int(11) NOT NULL,
  `restealnumber` int(11) NOT NULL,
  `restealodd` int(11) NOT NULL,
  `addstealnumber` int(11) NOT NULL,
  `addstealodd` int(11) NOT NULL,
  `addstrength` int(11) NOT NULL,
  `openget` int(11) NOT NULL,
  `opensteal` int(11) NOT NULL,
  `openindex` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_task_way` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `way_name` varchar(50) NOT NULL,
  `way_type` int(11) NOT NULL,
  `achieve_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_exchange_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `outstyle` int(11) NOT NULL,
  `exnum` int(11) NOT NULL,
  `usermoney` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_signin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sign_day` int(11) NOT NULL,
  `reward_money` int(11) NOT NULL,
  `reward_yuan` int(11) NOT NULL,
  `reward_items` int(11) NOT NULL,
  `items_num` int(11) NOT NULL,
  `reward_exp` int(11) NOT NULL,
  `reward_bene` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_signin_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `sign_id` int(11) NOT NULL,
  `reward_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)  ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_achieve_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(50) NOT NULL,
  `task_way` int(11) NOT NULL,
  `target_object` int(11) NOT NULL,
  `target_num` int(11) NOT NULL,
  `reward_money` int(11) NOT NULL,
  `reward_items` int(11) NOT NULL,
  `items_num` int(11) NOT NULL,
  `reward_exp` int(11) NOT NULL,
  `reward_bene` int(11) NOT NULL,
  `task_describe` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_bounty_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_uid` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `task_way` int(11) NOT NULL,
  `target_object` int(11) NOT NULL,
  `target_num` int(11) NOT NULL,
  `reward_money` int(11) NOT NULL,
  `reward_items` int(11) NOT NULL,
  `items_num` int(11) NOT NULL,
  `reward_exp` int(11) NOT NULL,
  `reward_bene` int(11) NOT NULL,
  `task_time` int(11) NOT NULL,
  `task_describe` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  `target_uid` int(11) NOT NULL,
  `task_in` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_daily_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(50) NOT NULL,
  `min_level` int(11) NOT NULL,
  `max_level` int(11) NOT NULL,
  `task_way` int(11) NOT NULL,
  `target_object` int(11) NOT NULL,
  `target_num` int(11) NOT NULL,
  `reward_money` int(11) NOT NULL,
  `reward_items` int(11) NOT NULL,
  `items_num` int(11) NOT NULL,
  `reward_exp` int(11) NOT NULL,
  `reward_bene` int(11) NOT NULL,
  `task_describe` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  `task_preid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_main_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(50) NOT NULL,
  `task_way` int(11) NOT NULL,
  `target_object` int(11) NOT NULL,
  `target_num` int(11) NOT NULL,
  `reward_money` int(11) NOT NULL,
  `reward_items` int(11) NOT NULL,
  `items_num` int(11) NOT NULL,
  `reward_exp` int(11) NOT NULL,
  `reward_bene` int(11) NOT NULL,
  `task_describe` varchar(250) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_achieve_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `reward_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `reward_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_bounty_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `task_status` int(11) NOT NULL,
  `reward_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `reward_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)  ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_daily_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `reward_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `reward_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)  ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_main_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `reward_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `reward_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_act_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(50) NOT NULL,
  `task_num` int(11) NOT NULL,
  `member_num` int(11) NOT NULL,
  `target_object` int(11) NOT NULL,
  `target_num` int(11) NOT NULL,
  `reward_money` int(11) NOT NULL,
  `reward_yuan` int(11) NOT NULL,
  `reward_items` int(11) NOT NULL,
  `items_num` int(11) NOT NULL,
  `reward_exp` int(11) NOT NULL,
  `reward_bene` int(11) NOT NULL,
  `task_describe` varchar(200) NOT NULL,
  `visible` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_daily_act` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `task_num` int(11) NOT NULL,
  `ready_num` int(11) NOT NULL,
  `member_num` int(11) NOT NULL,
  `target_object` int(11) NOT NULL,
  `target_num` int(11) NOT NULL,
  `reward_money` int(11) NOT NULL,
  `reward_yuan` int(11) NOT NULL,
  `reward_items` int(11) NOT NULL,
  `items_num` int(11) NOT NULL,
  `reward_exp` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  `reward_bene` int(11) NOT NULL,
  `task_describe` varchar(200) NOT NULL,
  `success_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)  ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_member_act` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `act_id` int(11) NOT NULL,
  `first_flag` int(11) NOT NULL,
  `last_flag` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(50) NOT NULL,
  `flag` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `pname` varchar(50) NOT NULL,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_lock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `land_num` int(11) NOT NULL,
  `money_type` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `visible` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS  `pre_gfarm_lock_log` (
 `id` INT( 11 ) NOT NULL AUTO_INCREMENT,
 `uid` INT( 11 ) NOT NULL ,
 `land_num` INT( 11 ) NOT NULL ,
 `money_type` INT( 11 ) NOT NULL ,
 `money` INT( 11 ) NOT NULL ,
 `create_time` INT( 11 ) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE IF NOT EXISTS `pre_gfarm_food` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `relation_id` int(11) NOT NULL,
  `addexp` int(11) NOT NULL,
  `addbene` int(11) NOT NULL,
  `addstren` int(11) NOT NULL,
   PRIMARY KEY (`id`)
) ;
CREATE TABLE IF NOT EXISTS `pre_gfarm_land_all_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `goods_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
EOF;
runquery($sql);
$sql =$installlang['1'];
runquery($sql);
$sql =$installlang['2'];
runquery($sql);
$sql =$installlang['3'];
runquery($sql);
$sql =$installlang['4'];
runquery($sql);
$sql =$installlang['5'];
runquery($sql);
$sql =$installlang['6'];
runquery($sql);
$sql =$installlang['7'];
runquery($sql);
$sql =$installlang['8'];
runquery($sql);
$sql =$installlang['9'];
runquery($sql);
$sql =$installlang['10'];
runquery($sql);
$sql =$installlang['11'];
runquery($sql);
$sql =$installlang['12'];
runquery($sql);
$sql =$installlang['13'];
runquery($sql);
$sql = <<<EOF
ALTER TABLE `pre_gfarm_decorate` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_depot` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_depot_log` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_equipment` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_exp_level` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_formula` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_formula_goods` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_formula_log` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_germ` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_germ_crop` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_gifts` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_goods` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_items` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_land_log` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_land` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_formula` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_set` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_goods_separate` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_goods_update` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_operat_land` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_combin` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_picture` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_signin` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_message` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_collect` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_sale` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_vip` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_task_way` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_exchange_log` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_signin` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_signin_reward` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_achieve_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_bounty_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_daily_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_main_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_achieve_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_bounty_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_daily_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_main_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_act_task` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_daily_act` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_member_act` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_plugin` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_lock` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_lock_log` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_food` AUTO_INCREMENT=100001;
ALTER TABLE `pre_gfarm_land_all_log` AUTO_INCREMENT=100001;
EOF;
runquery($sql);
$finish = TRUE;
?>