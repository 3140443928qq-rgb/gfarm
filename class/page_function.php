<?php
function page_view($currpage,$maxpage){
	
	$page_number = array();
	$max_page_number = 1;
	$min_page_number = 1;
	if ($maxpage < 11){
		$max_page_number = $maxpage ;
	}else {
		if ($currpage<5){
			$max_page_number = 10;
		}elseif ($currpage>($maxpage-5)) {
			$max_page_number = $maxpage;
			$min_page_number = $maxpage - 9;
		}else {
			$max_page_number = $currpage + 5;
			$min_page_number = $currpage - 4;
		}
	}
	for ($i=$min_page_number;$i<=$max_page_number;$i++){
		$page_number[] = $i;
	}
	return $page_number;
}

function page_div_and_li_width($currpage,$maxpage){
	$page_number_arr = page_view($currpage,$maxpage);
	$page_width_arr = array();
	$div_width = 265;
	$li_width = array();
	foreach ($page_number_arr as $key=>$value){
		if ($value<100){
			$div_width = $div_width + 27;
			$li_width[$key] = 20;
		}elseif ($value<1000){
			$div_width = $div_width + 32;
			$li_width[$key] = 25;
		}elseif ($value<10000){
			$div_width = $div_width + 37;
			$li_width[$key] = 30;
		}else {
			$div_width = $div_width + 42;
			$li_width[$key] = 35;
		}
	}
	$page_width_arr['div_width'] = $div_width;
	$page_width_arr['li_width'] = $li_width;
	return $page_width_arr;
}