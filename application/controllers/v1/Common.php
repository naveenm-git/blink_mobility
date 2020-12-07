<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Common extends Api_Controller {
	public $_baseUrl = '';
	public function __construct(){
		parent::__construct();
		$this->load->model('common_model');
	}
	
	public function home_page(){
		$langId=1;
		$popular_services = $this->common_model->get_popular_services($langId);
		$pServices = array();
		foreach($popular_services->result() as $res){
			$tmp = array();
			$cLinkArr = explode('/', $res->card_link);
			$tmp['title'] = $res->card_title;
			$tmp['image'] = $this->_baseUrl.'card_images/'.$res->card_image;
			$tmp['link'] = 's/'.$cLinkArr[count($cLinkArr)-2].'/'.$cLinkArr[count($cLinkArr)-1];
			$pServices[] = $tmp;
		}
		$this->_response['pServices'] = $pServices;
		$market_places = $this->common_model->get_market_places($langId);
		$mPlaces = array();
		foreach($market_places->result() as $res){
			$tmp = array();
			$tmp['link'] = 'sub-categories/'.$res->cat_url;
			$tmp['title'] = $res->cat_title;
			$tmp['description'] = $res->cat_desc;
			$tmp['image'] = $this->_baseUrl.'cat_images/'.$res->cat_image;
			$mPlaces[] = $tmp;
		}
		$this->_response['mPlaces'] = $mPlaces;
		$featured_proposals = $this->common_model->get_featured_proposals($langId);
		$fProposals = array();
		foreach($featured_proposals->result() as $res){
			$tmp = array();
			$tmp['proposal_id'] = $res->proposal_id;
			$tmp['title'] = $res->title;
			$tmp['post_image'] = $this->_baseUrl.'proposals/proposal_files/'.$res->post_image;
			$tmp['price'] = '$'.$res->price;
			$tmp['seller_name'] = $res->seller_name;
			if(!empty($res->seller_image))$tmp['seller_image'] = $this->_baseUrl.'user_images/'.$res->seller_image;
			else $tmp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
			$tmp['online_status'] = $res->seller_status;
			$tmp['seller_level'] = $res->seller_level;
			$r = get_seller_review($res->seller_id);
			if($this->isUser()){ 
				$tmp['isFavourite'] = check_fav($this->_user_id, $res->proposal_id);
			} else {
				$tmp['isFavourite'] = 0;
			}
			$tmp['rating'] = $r;
			/* $reviews = get_reviews($res->proposal_id);
			$tmp['reviews'] = [];
			foreach($reviews as $review){
				$temp = (array)$review;
				if(!empty($review->buyer_image))$temp['buyer_image'] = $this->_baseUrl.'user_images/'.$review->buyer_image;
				else $temp['buyer_image'] = $this->_baseUrl.'user_images/empty-image.png';
				if(!empty($review->seller_image))$temp['seller_image'] = $this->_baseUrl.'user_images/'.$review->seller_image;
				else $temp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
				$tmp['reviews'][] = $temp;
			} */
			$tmp['link'] = 'proposal/'.$res->proposal_url;
			$fProposals[] = $tmp;
		}
		$this->_response['fProposals'] = $fProposals;
		if($this->isUser()){
			$recent_proposals = $this->common_model->get_recent_proposals($langId);
			$rViews = array();
			foreach($recent_proposals->result() as $res){
				$tmp = array();
				$tmp['proposal_id'] = $res->proposal_id;
				$tmp['title'] = $res->title;
				$tmp['post_image'] = $this->_baseUrl.'proposals/proposal_files/'.$res->post_image;
				$tmp['price'] = '$'.$res->price;
				$tmp['seller_name'] = $res->seller_name;
				if(!empty($res->seller_image))$tmp['seller_image'] = $this->_baseUrl.'user_images/'.$res->seller_image;
				else $tmp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
				$tmp['online_status'] = $res->seller_status;
				$tmp['seller_level'] = $res->seller_level;
				$r = get_seller_review($res->seller_id);
				if($this->isUser()){ 
					$tmp['isFavourite'] = check_fav($this->_user_id, $res->proposal_id);
				} else {
					$tmp['isFavourite'] = 0;
				}
				$tmp['rating'] = $r;
				/* $reviews = get_reviews($res->proposal_id);
				$tmp['reviews'] = [];
				foreach($reviews as $review){
					$temp = (array)$review;
					if(!empty($review->buyer_image))$temp['buyer_image'] = $this->_baseUrl.'user_images/'.$review->buyer_image;
					else $temp['buyer_image'] = $this->_baseUrl.'user_images/empty-image.png';
					if(!empty($review->seller_image))$temp['seller_image'] = $this->_baseUrl.'user_images/'.$review->seller_image;
					else $temp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
					$tmp['reviews'][] = $temp;
				} */
				$tmp['link'] = 'proposal/'.$res->proposal_url;
				$rViews[] = $tmp;
			}
			$this->_response['rViews'] = $rViews;

			$conditionArr = ['B.seller_id'=>$this->_user_id];
			$offerDetails = $this->common_model->get_my_custom_offers($conditionArr);
			$oDetails = array();
			if($offerDetails->num_rows() > 0){
				foreach($offerDetails->result() as $r){
					$tmp = array();
					$tmp['request_id'] = $r->request_id;
					$tmp['offer_id'] = $r->offer_id;
					$tmp['offer_image'] = $this->_baseUrl.'proposals/proposal_files/'.$r->post_image;
					$tmp['offer_title'] = $r->proposal_title;
					$tmp['offer_description'] = $r->description;
					$tmp['offer_budget'] = '$'.$r->amount;
					$tmp['offer_duration'] = $r->delivery_time;
					$tmp['seller_id'] = $r->seller_id;
					$tmp['seller_name'] = $r->seller_name;
					if(!empty($r->seller_image))$tmp['seller_image'] = $this->_baseUrl.'user_images/'.$r->seller_image;
					else $tmp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
					$tmp['online_status'] = $r->seller_status;

					$chk = $this->common_model->check_inbox($this->_user_id, $r->seller_id);
					
					if($chk->num_rows() > 0){
						$message_group_id = $chk->row()->message_group_id;
					} else {
						$message_group_id = mt_rand();
						$message_status = "empty";

						$this->common_model->simple_insert(INBOX_SELLERS, ["message_group_id" => $message_group_id,"offer_id" => $r->offer_id,"sender_id" => $this->_user_id,"receiver_id" => $r->seller_id,"message_status" => $message_status]);
					}
					$tmp['message_group_id'] = $message_group_id;
					$tmp['checkout_url'] = 'checkout?type=offer&offer_id='.$r->offer_id;
					$oDetails[] = $tmp;
				}
			}
			$this->_response['oDetails'] = $oDetails;

			$conditionArr = ['IM.message_receiver'=>$this->_user_id];
			$offerDetails = $this->common_model->get_my_custom_message_offers($conditionArr);
			$coDetails = array();
			if($offerDetails->num_rows() > 0){
				foreach($offerDetails->result() as $r){
					$tmp = array();
					$tmp['message_group_id'] = $r->message_group_id;
					$tmp['offer_id'] = $r->offer_id;
					$tmp['offer_image'] = $this->_baseUrl.'proposals/proposal_files/'.$r->post_image;
					$tmp['offer_title'] = $r->proposal_title;
					$tmp['offer_description'] = $r->description;
					$tmp['offer_budget'] = '$'.$r->amount;
					$tmp['offer_duration'] = $r->delivery_time;
					$tmp['seller_id'] = $r->seller_id;
					$tmp['seller_name'] = $r->seller_name;
					if(!empty($r->seller_image))$tmp['seller_image'] = $this->_baseUrl.'user_images/'.$r->seller_image;
					else $tmp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
					$tmp['online_status'] = $r->seller_status;
					$tmp['checkout_url'] = 'checkout?type=message&offer_id='.$r->offer_id;
					$coDetails[] = $tmp;
				}
			}
			$this->_response['coDetails'] = $coDetails;
		}
		
		$this->json_response($this->_response);
	}
	
	public function categories(){
		$langId=1;
		$categories = $this->common_model->get_market_places($langId);
		$cArr = array();
		foreach($categories->result() as $res){
			$tmp = array();
			$tmp['cat_id'] = $res->cat_id;
			$tmp['link'] = 'sub-categories/'.$res->cat_url;
			$tmp['title'] = $res->cat_title;
			$tmp['image'] = $this->_baseUrl.'cat_images/'.$res->cat_image;
			$sCArr = $this->common_model->get_sub_categories($res->cat_id, $langId);
			$tmp['sCArr'] = array();
			foreach($sCArr as $r){
				$r->sub_category_link = 's/'.$res->cat_url.'/'.$r->sub_category_link;
				$tmp['sCArr'][] = $r;
			}
			$cArr[] = $tmp;
		}
		$this->_response['cArr'] = $cArr;
		$this->json_response($this->_response);
	}
	
	public function sub_categories($category = ''){
		if($category != ''){ 
			$langId=1;
			$sub_categories = $this->common_model->get_sub_categories_by_link($category, $langId);
			$sCArr = array();
			foreach($sub_categories->result() as $res){
				$tmp = array();
				$tmp['child_id'] = $res->child_id;
				$tmp['link'] = 's/'.$res->cat_url.'/'.$res->sub_category_link;
				$tmp['title'] = $res->sub_category_title;
				$sCArr[] = $tmp;
			}
			$this->_response['sCArr'] = $sCArr;
			$this->_response['bImage'] = $this->_baseUrl.'images/subcat.jpeg';
		}
		$this->json_response($this->_response);
	}
	
	public function search($cat = '', $sub_cat = ''){
		$search_string = '';
		if(isset($_POST['search_string'])){
			$search_string = $this->input->post('search_string');
		}
		$proposals = $this->common_model->get_search_proposals($cat, $sub_cat, $search_string);
		$pList = array();
		foreach($proposals->result() as $res){
			$tmp = array();
			$tmp['proposal_id'] = $res->proposal_id;
			$tmp['title'] = $res->title;
			$tmp['post_image'] = $this->_baseUrl.'proposals/proposal_files/'.$res->post_image;
			$tmp['price'] = '$'.$res->price;
			$tmp['seller_name'] = $res->seller_name;
			if(!empty($res->seller_image))$tmp['seller_image'] = $this->_baseUrl.'user_images/'.$res->seller_image;
			else $tmp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
			$tmp['online_status'] = $res->seller_status;
			$tmp['seller_level'] = $res->seller_level;
			$r = get_seller_review($res->seller_id);
			if($this->isUser()){ 
				$tmp['isFavourite'] = check_fav($this->_user_id, $res->proposal_id);
			} else {
				$tmp['isFavourite'] = 0;
			}
			$tmp['rating'] = $r;
			/* $reviews = get_reviews($res->proposal_id);
			$tmp['reviews'] = [];
			foreach($reviews as $review){
				$temp = (array)$review;
				if(!empty($review->buyer_image))$temp['buyer_image'] = $this->_baseUrl.'user_images/'.$review->buyer_image;
				else $temp['buyer_image'] = $this->_baseUrl.'user_images/empty-image.png';
				if(!empty($review->seller_image))$temp['seller_image'] = $this->_baseUrl.'user_images/'.$review->seller_image;
				else $temp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
				$tmp['reviews'][] = $temp;
			} */
			$tmp['link'] = 'proposal/'.$res->proposal_url;
			$pList[] = $tmp;
		}
		$this->_response['pList'] = $pList;
		
		$this->json_response($this->_response);
	}
	
	public function proposal($proposalLink = ''){
		$proposal = $this->common_model->get_proposal_details($proposalLink);
		// echo '<pre>';print_r($proposal->result());die;
		if($proposal->num_rows() > 0){
			$pDetails = array();
			$pDetails['proposal_id'] = $proposal->row()->proposal_id;
			$pDetails['title'] = $proposal->row()->proposal_title;
			$seller = get_seller_info($proposal->row()->seller_id);
			// echo '<pre>';print_r($seller);die;
			$sellerArr = [];
			if(count($seller) > 0 ){
				foreach($seller as $s){
					$sellerArr['seller_id'] = $s->seller_id;
					$sellerArr['seller_name'] = $s->seller_user_name;
					if(!empty($s->seller_image))$sellerArr['seller_image'] = $this->_baseUrl.'user_images/'.$s->seller_image;
					else $sellerArr['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
					$sellerArr['online_status'] = $s->seller_status;
					$sellerArr['seller_country'] = $s->seller_country;
					$sellerArr['seller_description'] = $s->seller_about;
					$sellerArr['recent_delivery'] = $s->seller_recent_delivery;
					$sellerArr['positive_review'] = $s->seller_rating;
					$sellerArr['seller_since'] = $s->seller_register_date;
					$sellerArr['seller_last_activity'] = date('F, d, Y', strtotime($s->seller_activity));
					$sellerArr['seller_level'] = $s->title;
					$sellerArr['seller_languages'] = get_seller_langs($s->seller_id);
				}
			}
			$pDetails['seller'] = $sellerArr;
			/* $pDetails['seller_name'] = $proposal->row()->seller_name;
			if(!empty($proposal->row()->seller_image))$pDetails['seller_image'] = $this->_baseUrl.'user_images/'.$proposal->row()->seller_image;
			else $pDetails['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
			$pDetails['seller_level'] = $proposal->row()->seller_level; */
			$pDetails['images'] = [];
			if($proposal->row()->proposal_img1 != '')
			$pDetails['images'][] = $this->_baseUrl.'proposals/proposal_files/'.$proposal->row()->proposal_img1;
			if($proposal->row()->proposal_img2 != '')
			$pDetails['images'][] = $this->_baseUrl.'proposals/proposal_files/'.$proposal->row()->proposal_img2;
			if($proposal->row()->proposal_img3 != '')
			$pDetails['images'][] = $this->_baseUrl.'proposals/proposal_files/'.$proposal->row()->proposal_img3;
			if($proposal->row()->proposal_img4 != '')
			$pDetails['images'][] = $this->_baseUrl.'proposals/proposal_files/'.$proposal->row()->proposal_img4;

			$proposalPackages = $this->common_model->get_proposal_packages($proposalLink);
			$pPackages = array();
			foreach($proposalPackages->result() as $res){
				$pPackages[] = array('package_id'=>$res->package_id, 'package_name'=>$res->package_name, 'description'=>$res->description, 'revisions'=>$res->revisions, 'delivery_time'=>$res->delivery_time, 'price'=>'$'.$res->price);
			}
			$pDetails['pPackages'] = $pPackages;
			$pDetails['description'] = $proposal->row()->proposal_desc;
			$r = get_seller_review($proposal->row()->seller_id);
			$pDetails['rating'] = $r;
			$reviews = get_reviews($proposal->row()->proposal_id);
			$pDetails['reviews'] = [];
			foreach($reviews as $review){
				$tmp = (array)$review;
				if(!empty($review->buyer_image))$tmp['buyer_image'] = $this->_baseUrl.'user_images/'.$review->buyer_image;
				else $tmp['buyer_image'] = $this->_baseUrl.'user_images/empty-image.png';
				if(!empty($review->seller_image))$tmp['seller_image'] = $this->_baseUrl.'user_images/'.$review->seller_image;
				else $tmp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
				$tmp['online_status'] = $review->seller_status;
				$pDetails['reviews'][] = $tmp;
			}
			$pDetails['proposal_tags'] = explode(", ", $proposal->row()->proposal_tags);

			$faqs = get_proposal_faqs($proposal->row()->proposal_id);
			$pDetails['faqs'] = [];
			foreach($faqs as $faq){
				$tmp = array();
				$tmp['question'] = $faq->title;
				$tmp['answer'] = $faq->content;
				$pDetails['faqs'][] = $tmp;
			}

			$sellers_proposals = $this->common_model->get_sellers_proposals($proposal->row()->seller_id, $proposal->row()->proposal_id);
			$pDetails['sellers_proposals'] = [];
			foreach($sellers_proposals->result() as $res){
				$tmp = array();
				$tmp['proposal_id'] = $res->proposal_id;
				$tmp['title'] = $res->title;
				$tmp['post_image'] = $this->_baseUrl.'proposals/proposal_files/'.$res->post_image;
				$tmp['price'] = '$'.$res->price;
				$tmp['seller_name'] = $res->seller_name;
				if(!empty($res->seller_image))$tmp['seller_image'] = $this->_baseUrl.'user_images/'.$res->seller_image;
				else $tmp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
				$tmp['online_status'] = $res->seller_status;
				$tmp['seller_level'] = $res->seller_level;
				$r = get_seller_review($res->seller_id);
				if($this->isUser()){ 
					$tmp['isFavourite'] = check_fav($this->_user_id, $res->proposal_id);
				} else {
					$tmp['isFavourite'] = 0;
				}
				$tmp['rating'] = $r;
				/* $reviews = get_reviews($res->proposal_id);
				$tmp['reviews'] = [];
				foreach($reviews as $review){
					$temp = (array)$review;
					if(!empty($review->buyer_image))$temp['buyer_image'] = $this->_baseUrl.'user_images/'.$review->buyer_image;
					else $temp['buyer_image'] = $this->_baseUrl.'user_images/empty-image.png';
					if(!empty($review->seller_image))$temp['seller_image'] = $this->_baseUrl.'user_images/'.$review->seller_image;
					else $temp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
					$tmp['reviews'][] = $temp;
				} */
				$tmp['link'] = 'proposal/'.$res->proposal_url;
				$pDetails['sellers_proposals'][] = $tmp;
			}
			
			if($this->isUser()){
				$recent_proposals = $this->common_model->get_recent_proposals($langId);
				$rViews = array();
				foreach($recent_proposals->result() as $res){
					$tmp = array();
					$tmp['proposal_id'] = $res->proposal_id;
					$tmp['title'] = $res->title;
					$tmp['post_image'] = $this->_baseUrl.'proposals/proposal_files/'.$res->post_image;
					$tmp['price'] = '$'.$res->price;
					$tmp['seller_name'] = $res->seller_name;
					if(!empty($res->seller_image))$tmp['seller_image'] = $this->_baseUrl.'user_images/'.$res->seller_image;
					else $tmp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
					$tmp['online_status'] = $res->seller_status;
					$tmp['seller_level'] = $res->seller_level;
					$r = get_seller_review($res->seller_id);
					if($this->isUser()){ 
						$tmp['isFavourite'] = check_fav($this->_user_id, $res->proposal_id);
					} else {
						$tmp['isFavourite'] = 0;
					}
					$tmp['rating'] = $r;
					/* $reviews = get_reviews($res->proposal_id);
					$tmp['reviews'] = [];
					foreach($reviews as $review){
						$temp = (array)$review;
						if(!empty($review->buyer_image))$temp['buyer_image'] = $this->_baseUrl.'user_images/'.$review->buyer_image;
						else $temp['buyer_image'] = $this->_baseUrl.'user_images/empty-image.png';
						if(!empty($review->seller_image))$temp['seller_image'] = $this->_baseUrl.'user_images/'.$review->seller_image;
						else $temp['seller_image'] = $this->_baseUrl.'user_images/empty-image.png';
						$tmp['reviews'][] = $temp;
					} */
					$tmp['link'] = 'proposal/'.$res->proposal_url;
					$rViews[] = $tmp;
				}
				
				$this->_response['rViews'] = $rViews;

				$chk = $this->user_model->check_inbox($this->_user_id, $proposal->row()->seller_id);
			
				if($chk->num_rows() > 0){
					$pDetails['seller']['message_group_id'] = $chk->row()->message_group_id;
				} else {
					$pDetails['seller']['message_group_id'] = '';
				}
			}

			$this->_response['pDetails'][] = $pDetails;
		} else {
			$this->_response['pDetails'] = array();
		}
		$this->json_response($this->_response);
	}

	public function get_pages(){
		$langId=1;
		$pages = $this->common_model->get_all_details(TERMS, ['language_id' => $langId]);
		$pArr = array();
		foreach($pages->result() as $res){
			$tmp = array();
			$tmp['title'] = $res->term_title;
			$tmp['link'] = 'page/'.$res->term_link;
			$pArr[] = $tmp;
		}
		$this->_response['pArr'] = $pArr;
		$this->json_response($this->_response);
	}

	public function page($pageLink = ''){
		if($pageLink != ''){
			$langId = 1;
			$this->data['pageDetails'] = $this->common_model->get_all_details(TERMS, ['term_link' => $pageLink, 'language_id' => $langId]);
			if($this->data['pageDetails']->num_rows() > 0){
				$this->load->view ( 'page', $this->data );
			}
		}
	}
	/**
	** Delivery time array
	**/
	public function delivery_times(){		
		$delivery_times = $this->common_model->get_all_details('delivery_times',['delivery_title!='=>""]);
		$gen_settings = $this->common_model->get_selected_fields_records('site_currency', GENERAL_SETTINGS, '', '', '');
		$site_currency = $gen_settings->row()->site_currency;
		$currency = $this->common_model->get_all_details('currencies',['id='=>$site_currency]);			
		$this->_response['deliveryArr'] = $delivery_times->result_array();
		$this->_response['currencyArr'] = $currency->result_array();
		$this->json_response($this->_response);
	}
}?>