<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
* This controller contains the common functions
* @author Casperon
*
**/
error_reporting(0);

date_default_timezone_set('Asia/Kolkata');

class MY_Controller extends CI_Controller {
	
    public $privStatus;
    public $data = array();
	
    function __construct() {
			parent::__construct();
			ob_start();
			ob_clean();
			$this->load->model('admin_model');
			$this->load->helper(array('url', 'form', 'cookie','mongodb_helper', 'site_helper'));
			$this->load->library(array('mongo_db','session'));
			$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			$this->output->set_header('Pragma: no-cache');
			$this->output->set_header('Content-Type: text/html; charset=utf-8');

			$adminset = $this->admin_model->get_all_details(ADMIN, array());
			foreach($adminset->row() as $key => $val){
				if($key!='admin_password' && $key!='privileges' && $key!='smtp' && $key!='country' && $key!='countryId' && $key!='currency'){
					if(is_array($val)){
						foreach($val as $ikey => $ival){
							$this->config->set_item($ikey, $ival);
						}
					} else {
						$this->config->set_item($key, $val);
					}
				}
			}
			$this->data['title'] = $this->config->item('meta_title');
			$this->data['heading'] = '';
			$this->data['flash_data'] = $this->session->flashdata('sErrMSG');
			$this->data['flash_data_type'] = $this->session->flashdata('sErrMSGType');
			$this->data['flash_data_key'] = $this->session->flashdata('sErrMSGKey');
			$this->data['adminPrevArr'] = $this->config->item('adminPrev');
			$this->data['adminEmail'] = $this->config->item('email');
			$this->data['privileges'] = (array)json_decode(base64_decode($this->session->userdata(APP_NAME.'_session_admin_privileges')));
			$this->data['subAdminMail'] = $this->session->userdata(APP_NAME.'_session_admin_email');
			$this->data['allPrev'] = '0';
			$this->data['station_map'] = $this->config->item('station_map');
			$this->data['background_image'] = $this->config->item('background_image');
			$this->data['site_logo'] = $this->config->item('site_logo');
			$this->data['site_favicon'] = $this->config->item('favicon');
			$this->data['site_footer'] = $this->config->item('footer_content');
			$this->data['footer'] = $this->config->item('admin_footer_content');
			$this->data['siteContactMail'] = $this->config->item('admin_email');
			$this->data['siteContactNumber'] = $this->config->item('contact_tel');
			$this->data['siteContactAddress'] = $this->config->item('contact_address');
			$this->data['siteTitle'] = $this->config->item('email_title');
			$this->data['meta_title'] = $this->config->item('meta_title');
			$this->data['meta_keyword'] = $this->config->item('meta_keyword');
			$this->data['meta_description'] = $this->config->item('meta_description');
			$this->data['meta_author'] = $this->config->item('site_title');
			if($this->data['title']=="") $this->data['title'] = $this->config->item('email_title');
						
			$this->data['session_user'] = $this->checkLogin('N');
			$this->data['session_user_id'] = $this->checkLogin('A');
         
         $years = [];
         for($year=date('Y', strtotime('+1 year'));$year>=date('Y', strtotime('-15 years'));$year--){
            $years[] = $year;
         }
         $this->data['years'] = $years;
         $this->data['validityInterval'] = ['minutes'=>'Minute(s)', 'hours'=>'Hour(s)', 'days'=>'Day(s)', 'weeks'=>'Week(s)', 'months'=>'Month(s)', 'years'=>'Year(s)'];
         $this->data['languages'] = ['en-us'=>'English (US)', 'es-us'=>'Spanish'];
			$this->data['parking_status'] = ['operational'=>'Operational', 'maintenance'=>'Closed For Maintenance'];
			$this->data['parking_type'] = ['reservation'=>'Reservation Kiosk', 'charging_point'=>'Charging Point'];
			$this->data['cable_status'] = ['plugged'=>'Plugged', 'stored'=>'Stored'];
			$this->data['light_color'] = ['reserved'=>'Reserved', 'unknown_car'=>'Unknown Car', 'maintenance'=>'Charge Point Under Maintenance', 'available'=>'Available Parking Spot', 'ready_for_rental'=>'Car is ready for rental'];
			$this->data['car_status'] = ['abandoned'=>'Abandoned', 'ready_for_rental'=>'Ready for rental', 'in_repair'=>'In Repair', 'plugged_in'=>'Plugged in', 'incoherent'=>'In-Coherent', 'removed_from_fleet'=>'Removed from the fleet', 'dispatch_processing'=>'Dispatch processing', 'potentially_broken'=>'Potentially broken', 'in_preparation'=>'In preparation (construction, preparation)'];
						
			$google_maps_api_key = $this->config->item('google_maps_api_key');
			$this->data['google_maps_api_key'] = ($google_maps_api_key!='')?'&key='.trim($google_maps_api_key):'';
			
			$this->data['admin_page_reference'] = '';
			$this->data['user_type'] = '';
			if($this->checkLogin('A') != ''){
				$this->data['user_type'] = $this->session->userdata(APP_NAME. '_session_admin_type');
			}
			$this->data['maxusers'] = 20;
			$this->data['months'] = ['01'=>'January', '02'=>'February', '03'=>'March', '04'=>'April', '05'=>'May', '06'=>'June', '07'=>'July', '08'=>'August', '09'=>'September', '10'=>'October', '11'=>'November', '12'=>'December'];
		}

	/**
	* 
	* This function return the session value based on param
	* @param $type
	**/
    public function checkLogin($type = '') {
        if ($type == 'A') {
            return $this->session->userdata(APP_NAME.'_session_admin_id');
        } else if ($type == 'N') {
            return $this->session->userdata(APP_NAME.'_session_admin_name');
        } else if ($type == 'M') {
            return $this->session->userdata(APP_NAME.'_session_admin_email');
        } else if ($type == 'T') {
            return $this->session->userdata(APP_NAME.'_session_admin_type');
        } else if ($type == 'P') {
            return $this->session->userdata(APP_NAME.'_session_admin_privileges');
        } else {
            return '';
        }
    }

	/**
	* 
	* This function set the error message and type in session
	* @param string $type
	* @param string $msg
	* @param string $langKey
	**/
    public function setErrorMessage($type = '', $msg = '', $langKey = '') {
			$msg = base64_encode($msg);
			$msgVal = ($type == 'success') ? 'success' : 'error';
	
			if($type == 'success'){
				$keyVal = 'Success';
			}else{
				$keyVal = 'Error';
			}
	
			$this->session->set_flashdata('sErrMSGKey', base64_encode($keyVal));
			$this->session->set_flashdata('sErrMSGType', $msgVal);
			$this->session->set_flashdata('sErrMSG', $msg);
    }

	/**
	* 
	* This function check the admin privileges
	* @param String $name	->	Management Name
	* @param Integer $right	->	0 for view, 1 for add, 2 for edit, 3 delete
	**/
    public function checkPrivileges($name = '', $right = '') {
        $prev = '0';
        $privileges = (array)json_decode($this->session->userdata(APP_NAME.'_session_admin_privileges'));
        @extract($privileges);
        $userName = $this->session->userdata(APP_NAME.'_session_admin_name');
        $adminName = $this->config->item('admin_name');
        if ($userName == $adminName) {
            $prev = '1';
        }
        if (isset(${$name}) && is_array(${$name}) && in_array($right, ${$name})) {
            $prev = '1';
        }
        if ($prev == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

	/**
	* 
	* Generate random string
	* @param Integer $length
	*
	**/
	public function get_rand_str($length = 6) {
		return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	}
	/**
	* 
	* Generate random numbers
	* @param Integer $length
	*
	**/
	public function get_rand_number($length = 6) {
		return substr(str_shuffle("0123456789"), 0, $length);
	}

	/**
	*
	* Resize the image
	* @param int target_width
	* @param int target_height
	* @param string image_name
	* @param string target_path
	*
	**/
	public function imageResizeWithSpace($box_w, $box_h, $userImage, $savepath) {
		$thumb_file = $savepath . $userImage;
		list($w, $h, $type, $attr) = getimagesize($thumb_file);
		$size = getimagesize($thumb_file);
		switch ($size["mime"]) {
			case "image/jpeg":
				$img = imagecreatefromjpeg($thumb_file); //jpeg file
			break;
			case "image/gif":
				$img = imagecreatefromgif($thumb_file); //gif file
			break;
			case "image/png":
				$img = imagecreatefrompng($thumb_file); //png file
			break;
			default:
				$im = false;
			break;
		}
		$new = imagecreatetruecolor($box_w, $box_h);
		if ($new === false) {
			//creation failed -- probably not enough memory
			return null;
		}
		$fill = imagecolorallocate($new, 255, 255, 255);
		imagefill($new, 0, 0, $fill);

		//compute resize ratio
		$hratio = $box_h / imagesy($img);
		$wratio = $box_w / imagesx($img);
		$ratio = min($hratio, $wratio);

		if ($ratio > 1.0) $ratio = 1.0;

		//compute sizes
		$sy = floor(imagesy($img) * $ratio);
		$sx = floor(imagesx($img) * $ratio);

		$m_y = floor(($box_h - $sy) / 2);
		$m_x = floor(($box_w - $sx) / 2);

		if (!imagecopyresampled($new, $img, $m_x, $m_y, 0, 0,$sx, $sy,imagesx($img), imagesy($img))) {
			//copy failed
			imagedestroy($new);
			return null;
		}
		if (isset($i)) imagedestroy($i);
		imagejpeg($new, $thumb_file, 99);
	}

	/**
	* Image resize
	* @param int $width
	* @param int $height
	* @param string $targetImage Name
	* @param string $savepath 
	**/
	public function ImageResizeWithCrop($width, $height, $thumbImage, $savePath) {
		$thumb_file = $savePath . $thumbImage;
		$newimgPath = base_url() . substr($savePath, 2) . $thumbImage;
		/* Get original image x y */
		list($w, $h) = getimagesize($thumb_file);
		$size = getimagesize($thumb_file);
		/* calculate new image size with ratio */
		$ratio = max($width / $w, $height / $h);
		$h = ceil($height / $ratio);
		$x = ($w - $width / $ratio) / 2;
		$w = ceil($width / $ratio);
		/* new file name */
		$path = $savePath . $thumbImage;
		/* read binary data from image file */

		$imgString = file_get_contents($newimgPath);
		/* create image from string */
		$image = imagecreatefromstring($imgString);
		$tmp = imagecreatetruecolor($width, $height);
		imagecopyresampled($tmp, $image, 0, 0, $x, 0, $width, $height, $w, $h);

		/* Save image */
		switch ($size["mime"]) {
			case 'image/jpeg':
				imagejpeg($tmp, $path, 100);
			break;
			case 'image/png':
				imagepng($tmp, $path, 0);
			break;
			case 'image/gif':
				imagegif($tmp, $path);
			break;
			default:
				exit;
			break;
		}
		return $path;
		/* cleanup memory */
		imagedestroy($image);
		imagedestroy($tmp);
	}

	/**
	* Image Compress
	* @param int $quality
	* @param string $source_url 
	* @param string $destination_url 
	**/
	public function ImageCompress($source_url, $destination_url = '', $quality = 70) {
		$info = getimagesize($source_url);
		$savePath = $source_url;
		if ($info['mime'] == 'image/jpeg')
			$image = imagecreatefromjpeg($savePath);
		elseif ($info['mime'] == 'image/gif')
			$image = imagecreatefromgif($savePath);
		elseif ($info['mime'] == 'image/png')
			$image = imagecreatefrompng($savePath);
		### Saving Image
		imagejpeg($image, $savePath, $quality);
	}

	/**
	* Get Image resolution type
	* @param string $destination_url 
	**/
	public function getImageShape($width, $height, $target_file) {
		list($w, $h) = getimagesize($target_file);
		if ($w == $width && $h == $height) {
			$option = "exact";
		} else if ($w == $h) {
			$option = "exact";
		} else if ($w > $h) {
			$option = "landscape";
		} else if ($w < $h) {
			$option = "portrait";
		} else {
			$option = "crop";
		}
		return $option;
	}
		
	public function get_verfication_code(){
		$characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
    for ($i = 0; $i < 6; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 
  
    return strtoupper($randomString);
	}
	
	public function unique_file_name($folder = 'snaps'){
		$filename = str_shuffle(rand(1000000,9999999));
		$ret = 0;
		while($ret==1) {
			if(!file_exists('uploads/'.$folder.'/'.$filename)){
				$ret=1;
			} else {
				$filename = str_shuffle(rand(1000000,9999999));
			}
		}
		return $filename;
	}
	
	public function save_tags($tags=[]){
		if(is_array($tags) && count($tags) > 0){
			$condition = ['type'=>'tags'];
			$common = $this->admin_model->get_all_details(COMMON, $condition);
			if ($common->num_rows() > 0){
				$existing = (count($common->row()->tags)>0)?$common->row()->tags:[];
				$newtags = array_unique(array_merge($existing, $tags));
				sort($newtags);
				$this->admin_model->update_details(COMMON, ['tags' => $newtags], $condition);
			} else {
				$this->admin_model->simple_insert(COMMON, ['type'=>'tags', 'tags'=>$tags]);
			}
		}
	}
	
	public function get_tags(){
		$tags = [];
		$condition = ['type'=>'tags'];
		$common = $this->admin_model->get_all_details(COMMON, $condition);
		if ($common->num_rows() > 0){
			$tags = ($common->row()->tags!='')?$common->row()->tags:[];
		}
		return $tags;
	}
	
	public function change_bulk_status($collection, $status, $ids){
		$dataArr = ['status'=>$status];
		$condition = ['_id'=>['$in'=>$ids]];
		$check = $this->admin_model->get_selected_fields($collection, $condition, ['_id']);
		if($check->num_rows()>0){
			$this->admin_model->update_details($collection, $dataArr, $condition);
			echo 'success';
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
	public function file_upload_multiple_local($files = '', $path = ''){
		$imageArr = [];
		for($i=0;$i<count($files['name']);$i++){
			if(!empty($files['name'][$i])){
				$_FILES['file'] = [
					'name'=>$files['name'][$i], 
					'type'=>$files['type'][$i], 
					'tmp_name'=>$files['tmp_name'][$i], 
					'error'=>$files['error'][$i],
					'size'=>$files['size'][$i]
				];
				$config['overwrite'] = FALSE;
				$config['encrypt_name'] = TRUE;
				$config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
				$config['max_size'] = 5000;
				if(!is_writable($path)) mkdir($path, 0777);
				$config['upload_path'] = $path;
				
				$this->load->library('upload', $config);
				if ($this->upload->do_upload('file')) {
					$bannerDetails = $this->upload->data();
					$imageArr[] = $bannerDetails['file_name'];
				} 
			}
		}
		return $imageArr;
	}

	public function simple_email_send($email, $password){
		$message = '';
		$message .= '<body>';
		$message .= '<p>Your password: '.$password.'</p>';
		$message .= '</body>';
		$sender_email = 'info@fistosports.com';
		$sender_name = 'Fisto Sports';
		$email_values = array (
				'mail_type' => 'html',
				'from_mail_id' => $sender_email,
				'mail_name' => $sender_name,
				'to_mail_id' => $email,
				'subject_message' => 'Organizer Account Credential - Fisto Sports',
				'body_messages' => trim($message)
		);
		// echo '<pre>'; print_r($email_values); die;
		$email_send_to_common = $this->admin_model->common_email_send ($email_values);
	}

	public function email_template_send($emailArr, $template){
		extract($emailArr);
		$base_url = base_url();
		$template_details = $this->admin_model->get_all_details(NEWSLETTER, ['template'=>$template]);
		$message = $template_details->row()->content;
		$message = str_replace(["{","}"],"",addslashes($message));
		eval("\$message = \"$message\";");
		$email_values = array (
			'mail_type' => 'html',
			'mail_name' => $template_details->row()->sender_name,
			'from_mail_id' => $template_details->row()->sender_email,
			'to_mail_id' => $to_email,
			'subject_message' => $template_details->row()->email_subject,
			'body_messages' => $message
		);
		// echo '<pre>'; print_r($email_values); die;
		$email_send_to_common = $this->admin_model->common_email_send($email_values);
	}
	
	public function subscriber_email_template($emailArr, $template){
		extract($emailArr);
		$base_url = base_url();
		$template_details = $this->admin_model->get_all_details(NEWSLETTER, ['_id'=>MongoID($template)]);
		$message = $template_details->row()->content;
		$message = str_replace(["{","}"],"",addslashes($message));
		eval("\$message = \"$message\";");
		$email_values = array (
			'mail_type' => 'html',
			'mail_name' => $template_details->row()->sender_name,
			'from_mail_id' => $template_details->row()->sender_email,
			'to_mail_id' => $to_email,
			'subject_message' => $template_details->row()->email_subject,
			'body_messages' => $message
		);
		// echo '<pre>'; print_r($emailArr); die;
		$email_send_to_common = $this->admin_model->common_email_send($email_values);
	}
	
	public function upload_file($file=''){
      $returnArr['status'] = '0';
      $returnArr['response'] = '';
		if(!empty($_FILES) && $_FILES[$file]['name']!=''){
			$config['upload_path']    = './uploads/';
			$config['encrypt_name']   = TRUE;
			$config['allowed_types']  = 'gif|jpg|png';
			$config['max_size']       = 5000;
			$this->load->library('upload', $config);
			if ($this->upload->do_upload($file)) {
				$response = $this->upload->data();
				$returnArr['status'] = '1';
				$returnArr['response'] = $response['file_name'];
			} else {
				$returnArr['response'] = $this->upload->display_errors();
			}
		} 
      return $returnArr;
	}
	
}

Class Api_Controller extends MY_Controller {
		/*
		 * App controller with jwt
		 * Initial loader of the webservice with custom jwt functions.
		 *
		 * @package         CodeIgniter
		 * @subpackage      Core controller
		 * @category        Core controller
		 * @author          Casperon
		 * @license         Casperon
		 * @version         1.0 (Beta)
		 * @date			2020-11-23
		 */
		public $_user_id;
		public $_user_access;
		public $_jwt_token;
		public $_new_jwt_token;
		public $_response = [];
		public $_open_requests = [];
		public $Token;
		public $_api_version = 'api/v1/';
		function __construct(){
			date_default_timezone_set('Asia/Kolkata');        
			error_reporting(0);
			parent::__construct();
			$this->load->helper(array('form', 'url', 'api'));
			$this->load->library('form_validation');
			header('Access-Control-Allow-Origin: *');
			/* Initialize the open(non-auth) requests array */
			$this->_open_requests = array('sample_notification', 'index', 'app_info', 'home_page', 'search', 'categories', 'sub_categories', 'proposal', 'register', 'signup', 'upload_file', 'get_verification_code', 'login', 'forgot_password', 'get_pages', 'page','send_bulk_notification','send_inbox_notification','delivery_times','send_single_notification','custom_test');
			/* JWT Authentication Begin */
			$headers = $this->input->request_headers();
			$get_requests = $_GET;
			
         if (array_key_exists("Auth", $headers)){ $auth_key = $headers['Auth'];} else if (array_key_exists("auth", $headers)){ $auth_key = $headers['auth'];} else if (array_key_exists("Auth", $get_requests)){ $auth_key = $get_requests['Auth'];} else if (array_key_exists("auth", $get_requests)){ $auth_key = $get_requests['auth'];} else { $auth_key = "";}
			if (array_key_exists("Currency", $headers)){ $currency = $headers['Currency'];} else { $currency = "";}
			$this->data['currency']=$currency;
         
			if(!empty($auth_key)) {
				if($this->isvalidjwt($auth_key)){
					if(!$this->isExpired($auth_key)){
						$this->_jwt_token = $auth_key;
						$this->_new_jwt_token = $this->initUser($auth_key);
						$this->_response['commonArr'] = array('token'=>$this->_new_jwt_token);
					}else{
						$this->json_response($this->config->item('_response')['502']);
						exit;
					}
				}else{
					$this->json_response($this->config->item('_response')['501']);
					exit;
				}
			}else{
				$cf_fun = $this->router->fetch_method();
				if(!in_array($cf_fun,$this->_open_requests)){
					$this->json_response($this->config->item('_response')['503']);
					exit;
				}
			}
			/* End */
			if (array_key_exists("Apptype", $headers))
				$this->Apptype = $headers['Apptype'];
			if (array_key_exists("Apptoken", $headers))
				$this->Token = $headers['Apptoken'];

			$this->_baseUrl = str_ireplace('api/', '', base_url());
		}
		
		public function getjwt($userArr){
			$header = '{"typ":"jwt","alg":"HS256","crt":"'.time().'"}';
			$payload = '{"user_id":"'.$userArr['id'].'","access":"'.$userArr['user_type'].'","lifetime":"'.HASH_LIFE.'"}';
			$signature = hash_hmac('sha256', "$header.$payload", HASH_KEY);
			$jwt = base64_encode($header).'.'.base64_encode($payload).'.'.base64_encode($signature);
			return $jwt;
		}
		
		public function isvalidjwt($jwt){
			if(strpos($jwt,'.')===false){return false;}
			$jwts = explode('.',$jwt);
			if(count($jwts)!=3){return false;}

			$header = base64_decode($jwts[0]);
			$payload = base64_decode($jwts[1]);
			$s1 = hash_hmac('sha256',"$header.$payload",HASH_KEY);
			$s2 = base64_decode($jwts[2]);

			if($s1==$s2){return true;}
			else{return false;}
		}

		public function isExpired($jwt){
			$headerArr = $this->extract_jwt($jwt,'',0);
			$cratedAt = $headerArr['crt'];
			$payloadArr = $this->extract_jwt($jwt);
			$lifetime = $payloadArr['lifetime'];
			$validUpto = $cratedAt + 360 * 360 * $lifetime;
			$now = time();
			if($now > $validUpto){return true;}
			else{return false;}
		}

		public function extract_jwt($jwt,$field='',$i=1){
			$jwts = explode('.',$jwt);
			$payload = base64_decode($jwts[$i]);
			$plArray = json_decode($payload,true);

			if(!empty($field)){
				if(isset($plArray[$field])){
					return $plArray[$field];
				}else{
					return false;
				}
			}else{
				return $plArray;
			}
		}

		public function initUser($jwt){
			if($this->isvalidjwt($jwt)){
				$userArr = $this->extract_jwt($jwt);
				$this->_user_id = $userNArr['id'] = $userArr['user_id'];
				$this->_user_access = $userNArr['user_type'] = $userArr['access'];
				return $this->getjwt($userNArr);
			} else {
				return '';
			}
		}

		/*
		 * Clean string
		 * @param String $orig_text
		*/
		public function cleanString($orig_text) {
			$text = $orig_text;
			// Single letters
			$text = preg_replace("/[∂άαáàâãªä]/u", "a", $text);
			$text = preg_replace("/[∆лДΛдАÁÀÂÃÄ]/u", "A", $text);
			$text = preg_replace("/[ЂЪЬБъь]/u", "b", $text);
			$text = preg_replace("/[βвВ]/u", "B", $text);
			$text = preg_replace("/[çς©с]/u", "c", $text);
			$text = preg_replace("/[ÇС]/u", "C", $text);
			$text = preg_replace("/[δ]/u", "d", $text);
			$text = preg_replace("/[éèêëέëèεе℮ёєэЭ]/u", "e", $text);
			$text = preg_replace("/[ÉÈÊË€ξЄ€Е∑]/u", "E", $text);
			$text = preg_replace("/[₣]/u", "F", $text);
			$text = preg_replace("/[НнЊњ]/u", "H", $text);
			$text = preg_replace("/[ђћЋ]/u", "h", $text);
			$text = preg_replace("/[ÍÌÎÏ]/u", "I", $text);
			$text = preg_replace("/[íìîïιίϊі]/u", "i", $text);
			$text = preg_replace("/[Јј]/u", "j", $text);
			$text = preg_replace("/[ΚЌК]/u", 'K', $text);
			$text = preg_replace("/[ќк]/u", 'k', $text);
			$text = preg_replace("/[ℓ∟]/u", 'l', $text);
			$text = preg_replace("/[Мм]/u", "M", $text);
			$text = preg_replace("/[ñηήηπⁿ]/u", "n", $text);
			$text = preg_replace("/[Ñ∏пПИЙийΝЛ]/u", "N", $text);
			$text = preg_replace("/[óòôõºöοФσόо]/u", "o", $text);
			$text = preg_replace("/[ÓÒÔÕÖθΩθОΩ]/u", "O", $text);
			$text = preg_replace("/[ρφрРф]/u", "p", $text);
			$text = preg_replace("/[®яЯ]/u", "R", $text);
			$text = preg_replace("/[ГЃгѓ]/u", "r", $text);
			$text = preg_replace("/[Ѕ]/u", "S", $text);
			$text = preg_replace("/[ѕ]/u", "s", $text);
			$text = preg_replace("/[Тт]/u", "T", $text);
			$text = preg_replace("/[τ†‡]/u", "t", $text);
			$text = preg_replace("/[úùûüџμΰµυϋύ]/u", "u", $text);
			$text = preg_replace("/[√]/u", "v", $text);
			$text = preg_replace("/[ÚÙÛÜЏЦц]/u", "U", $text);
			$text = preg_replace("/[Ψψωώẅẃẁщш]/u", "w", $text);
			$text = preg_replace("/[ẀẄẂШЩ]/u", "W", $text);
			$text = preg_replace("/[ΧχЖХж]/u", "x", $text);
			$text = preg_replace("/[ỲΫ¥]/u", "Y", $text);
			$text = preg_replace("/[ỳγўЎУуч]/u", "y", $text);
			$text = preg_replace("/[ζ]/u", "Z", $text);

			// Punctuation
			$text = preg_replace("/[‚‚]/u", ",", $text);
			$text = preg_replace("/[`‛′’‘]/u", "'", $text);
			$text = preg_replace("/[″“”«»„]/u", '"', $text);
			$text = preg_replace("/[—–―−–‾⌐─↔→←]/u", '-', $text);
			$text = preg_replace("/[  ]/u", ' ', $text);

			$text = str_replace("…", "...", $text);
			$text = str_replace("≠", "!=", $text);
			$text = str_replace("≤", "<=", $text);
			$text = str_replace("≥", ">=", $text);
			$text = preg_replace("/[‗≈≡]/u", "=", $text);


			// Exciting combinations
			$text = str_replace("ыЫ", "bl", $text);
			$text = str_replace("℅", "c/o", $text);
			$text = str_replace("₧", "Pts", $text);
			$text = str_replace("™", "tm", $text);
			$text = str_replace("№", "No", $text);
			$text = str_replace("Ч", "4", $text);
			$text = str_replace("‰", "%", $text);
			$text = preg_replace("/[∙•]/u", "*", $text);
			$text = str_replace("‹", "<", $text);
			$text = str_replace("›", ">", $text);
			$text = str_replace("‼", "!!", $text);
			$text = str_replace("⁄", "/", $text);
			$text = str_replace("∕", "/", $text);
			$text = str_replace("⅞", "7/8", $text);
			$text = str_replace("⅝", "5/8", $text);
			$text = str_replace("⅜", "3/8", $text);
			$text = str_replace("⅛", "1/8", $text);
			$text = preg_replace("/[‰]/u", "%", $text);
			$text = preg_replace("/[Љљ]/u", "Ab", $text);
			$text = preg_replace("/[Юю]/u", "IO", $text);
			$text = preg_replace("/[ﬁﬂ]/u", "fi", $text);
			$text = preg_replace("/[зЗ]/u", "3", $text);
			$text = str_replace("£", "(pounds)", $text);
			$text = str_replace("₤", "(lira)", $text);
			$text = preg_replace("/[‰]/u", "%", $text);
			$text = preg_replace("/[↨↕↓↑│]/u", "|", $text);
			$text = preg_replace("/[∞∩∫⌂⌠⌡]/u", "", $text);


			//2) Translation CP1252.
			$trans = get_html_translation_table(HTML_ENTITIES);
			$trans['f'] = '&fnof;';    // Latin Small Letter F With Hook
			$trans['-'] = array(
				'&hellip;', // Horizontal Ellipsis
				'&tilde;', // Small Tilde
				'&ndash;'       // Dash
			);
			$trans["+"] = '&dagger;';    // Dagger
			$trans['#'] = '&Dagger;';    // Double Dagger
			$trans['M'] = '&permil;';    // Per Mille Sign
			$trans['S'] = '&Scaron;';    // Latin Capital Letter S With Caron
			$trans['OE'] = '&OElig;';    // Latin Capital Ligature OE
			$trans["'"] = array(
				'&lsquo;', // Left Single Quotation Mark
				'&rsquo;', // Right Single Quotation Mark
				'&rsaquo;', // Single Right-Pointing Angle Quotation Mark
				'&sbquo;', // Single Low-9 Quotation Mark
				'&circ;', // Modifier Letter Circumflex Accent
				'&lsaquo;'  // Single Left-Pointing Angle Quotation Mark
			);

			$trans['"'] = array(
				'&ldquo;', // Left Double Quotation Mark
				'&rdquo;', // Right Double Quotation Mark
				'&bdquo;', // Double Low-9 Quotation Mark
			);

			$trans['*'] = '&bull;';    // Bullet
			$trans['n'] = '&ndash;';    // En Dash
			$trans['m'] = '&mdash;';    // Em Dash
			$trans['tm'] = '&trade;';    // Trade Mark Sign
			$trans['s'] = '&scaron;';    // Latin Small Letter S With Caron
			$trans['oe'] = '&oelig;';    // Latin Small Ligature OE
			$trans['Y'] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
			$trans['euro'] = '&euro;';    // euro currency symbol
			ksort($trans);

			foreach ($trans as $k => $v) {
				$text = str_replace($v, $k, $text);
			}

			// 3) remove <p>, <br/> ...
			$text = strip_tags($text);

			// 4) &amp; => & &quot; => '
			$text = html_entity_decode($text);
			return $text;
		}

		public function json_response($arr, $data=array()){
			if(!array_key_exists('response_code',$arr)){
				$data = $arr;
				$arr = $this->config->item('_response')['200'];
			}
			
			if(!array_key_exists('status',$arr)){
				$arr['status'] = ($arr['response_code']=='200')?'1':'0';
			}
			
			if(count($data)>0){		
				$arr['content'] = $data;
			}
			
			if(isset($arr['content']['message'])){
				$arr['message'] = $arr['content']['message'];
				unset($arr['content']['message']);
			}
			
			if(isset($arr['content']['long_message'])){
				$arr['long_message'] = $arr['content']['long_message'];
				unset($arr['content']['long_message']);
			}
			
			if(isset($arr['content']['commonArr'])){			
				$arr['commonArr'] = $arr['content']['commonArr'];
				unset($arr['content']['commonArr']);
			}
			
			if(isset($arr['content']['status'])){
				unset($arr['content']['status']);
			}
			
			if(isset($arr['content']) && count($arr['content'])==0){
				unset($arr['content']);
			}
			
			$json_encode = json_encode($arr, JSON_PRETTY_PRINT);
			echo $this->cleanString($json_encode);
		}

		public function send_notification($dataArr = array()){
			$fcmUrl = 'https://fcm.googleapis.com/fcm/send';
			$token = $dataArr['token'];
			$notification = [
				'title' => $dataArr['title'],
				'body' => $dataArr['message']
			];
			$extraNotificationData = ["click_action" => 'FLUTTER_NOTIFICATION_CLICK', "page" => $dataArr['page']];
			$fcmNotification = [
				//'registration_ids' => $tokenList, //multple token array
				'to'        => $token, //single token
				'notification' => $notification,
				'data' => $extraNotificationData
			];
			$headers = [
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
			];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$fcmUrl);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;			
   }
}
