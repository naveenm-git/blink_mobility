<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function short_text($str, $len = 145){
	return (strlen($str) > $len) ? substr($str, 0, $len).'...' : $str; 
}

function getrow($collection, $_id){
	$ci =& get_instance();
	return $ci->admin_model->get_all_details($collection, ['_id'=>new MongoDB\BSON\ObjectId($_id)])->row();
}

function getfixtures($condition){
	$ci =& get_instance();
	return $ci->admin_model->get_all_details(FIXTURES, $condition, ['date_time'=>'asc']);
}

function getresult($collection, $condition, $sorting=[]){
	$ci =& get_instance();
	return $ci->admin_model->get_all_details($collection, $condition, $sorting);
}

function convert_to_seconds($array = '') {
   $ci =& get_instance();
   if($array['interval']=='minutes') return $array['count'] * 60;   
   if($array['interval']=='hours') return $array['count'] * 60 * 60;
   if($array['interval']=='day') return $array['count'] * 60 * 60 * 24;
   if($array['interval']=='month') return $array['count'] * 60 * 60 * 24 * 30;
   if($array['interval']=='month') return $array['count'] * 60 * 60 * 24 * 365;
}

?>
