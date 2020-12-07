<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_sample(){
	return 'Sample';
}

function get_seller_review($sellerId){

	/* $select_buyer_reviews = $db->select("buyer_reviews",array("review_seller_id"=>$seller_id)); 
	$count_reviews = $select_buyer_reviews->rowCount();
	if(!$count_reviews == 0){
	$rattings = array();
	while($row_buyer_reviews = $select_buyer_reviews->fetch()){
		$buyer_rating = $row_buyer_reviews->buyer_rating;
		array_push($rattings,$buyer_rating);
	}
	$total = array_sum($rattings);
	@$average = $total/count($rattings);
	$average_rating = substr($average ,0,1);
	}else{
		$average = "0";  
			$average_rating = "0";
	} */

	$ci =& get_instance();
	$ci->db->select('COUNT(*) as total_reviews, COALESCE(AVG(buyer_rating) ,0) as average_ratting');
	$ci->db->from(BUYER_REVIEWS);
	$ci->db->where('review_seller_id', $sellerId);
	$result = $ci->db->get();
	$returnArr = array('average_ratting' => 0, 'total_reviews' => 0);
	foreach($result->result() as $r){
		$returnArr['average_ratting'] = number_format($r->average_ratting, 1);
		$returnArr['total_reviews'] = $r->total_reviews;
	}
	return $returnArr;
}

function get_reviews($proposal_id){
	$ci =& get_instance();
	$ci->db->select('B.seller_user_name as buyer_name, B.seller_image as buyer_image, BR.buyer_review, BR.buyer_rating, BR.review_date, S.seller_user_name as seller_name, S.seller_image as seller_image, SR.seller_review, SR.seller_rating');
	$ci->db->from(BUYER_REVIEWS.' AS BR');
	$ci->db->join(SELLERS.' AS B', 'B.seller_id = BR.review_buyer_id');
	$ci->db->join(SELLER_REVIEWS.' AS SR', 'SR.review_id = BR.review_id', 'LEFT');
	$ci->db->join(SELLERS.' AS S', 'S.seller_id = SR.review_seller_id', 'LEFT');
	$ci->db->where('proposal_id', $proposal_id);
	$result = $ci->db->get();
	$returnArr = $result->result();
	return $returnArr;
}

function get_proposal_faqs($proposal_id){
	$ci =& get_instance();
	$ci->db->select('*');
	$ci->db->from(PROPOSALS_FAQ);
	$ci->db->where('proposal_id', $proposal_id);
	$result = $ci->db->get();
	$returnArr = $result->result();
	return $returnArr;
}

function get_seller_info($seller_id){
	$ci =& get_instance();
	$ci->db->select('*');
	$ci->db->from(SELLERS.' AS S');
	$ci->db->join(SELLER_LEVELS_META.' AS SLM', 'SLM.level_id = S.seller_level');
	$ci->db->where('seller_id', $seller_id);
	$result = $ci->db->get();
	$returnArr = $result->result();
	return $returnArr;
}

function get_seller_langs($seller_id){
	$ci =& get_instance();
	$ci->db->select('SL.language_title as language');
	$ci->db->from(LANGUAGES_RELATION.' AS LR');
	$ci->db->join(SELLER_LANGUAGES.' AS SL', 'SL.language_id = LR.language_id');
	$ci->db->where('LR.seller_id', $seller_id);
	$result = $ci->db->get();
	$returnArr = $result->result();
	return $returnArr;
}

function processing_fee($amount){
	$ci =& get_instance();
	$ci->db->select();
	$ci->db->from(PAYMENT_SETTINGS);
	$result = $ci->db->get();
	$processing_feeType = $result->row()->processing_feeType;
	$processing_fee = $result->row()->processing_fee;
	if($processing_feeType=="fixed") {
		return $processing_fee;
	} elseif($processing_feeType=="percentage") {
		return get_percentage_amount($amount,$processing_fee);
	}
}

function get_percentage_amount($amount, $percentage){
	$calculate_percentage = ($percentage / 100 ) * $amount;
	return $calculate_percentage;
}

function time_ago($timestamp){
	$time_ago = strtotime($timestamp);  
	$current_time = time();  
	$time_difference = $current_time - $time_ago;  
	$seconds = $time_difference;  
	$minutes      = round($seconds / 60 );             // value 60 is seconds  
	$hours           = round($seconds / 3600);        //value 3600 is 60 minutes * 60 sec  
	$days          = round($seconds / 86400);        //86400 = 24 * 60 * 60;  
	$weeks          = round($seconds / 604800);     // 7*24*60*60;  
	$months          = round($seconds / 2629440);  //((365+365+365+365+366)/5/12)*24*60*60  
	$years          = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60  
	if($seconds <= 60){  
	  return "Just Now";  
	}else if($minutes <=60){  
	 if($minutes==1) {  
	   return "one minute ago";  
	 }  else  {  
	   return "$minutes minutes ago";  
	 }  
	}  else if($hours <=24)  {  
	 if($hours==1){  
	   return "an hour ago";  
	 }else{  
	   return "$hours hrs ago";  
	 }  
   }else if($days <= 7)  {  
	 if($days==1)  {  
	   return "yesterday";  
	 }else{
	   return "$days days ago";  
	 }  
   }else if($weeks <= 4.3){ //4.3 == 52/12  
	 if($weeks==1)  {  
	   return "a week ago";  
	 } else{
	   return "$weeks weeks ago";  
	 }  
   }else if($months <=12)  {  
	 if($months==1) {  
	   return "a month ago";  
	 }else{  
	   return "$months months ago";  
	 }
   }else{
	 if($years==1)  {  
	   return "one year ago";  
	 } else {  
	   return "$years years ago";  
	 }  
   }
}

function check_fav($seller_id, $proposal_id){
	$ci =& get_instance();
	$ci->db->select('favourite_id');
	$ci->db->from(FAVORITES);
	$ci->db->where('seller_id', $seller_id);
	$ci->db->where('proposal_id', $proposal_id);
	$result = $ci->db->get();
	if($result->num_rows() > 0)return 1;
	else return 0;
}

?>