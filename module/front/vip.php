<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}
$vipInfos=C::t('#gfarm#gfarm_vip')->fetch_all_data(' and openindex=1 order by vipname');
include template('gfarm:front/vip');
exit;


?>