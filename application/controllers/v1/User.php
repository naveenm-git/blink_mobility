<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends Api_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
	
	public function signup(){
		$errArr = array();
		$first_name = strip_tags($this->input->post('first_name'));
		$last_name = strip_tags($this->input->post('last_name'));
		$email = strip_tags($this->input->post('email'));
		$phone_number = strip_tags($this->input->post('phone_number'));
		$password = strip_tags($this->input->post('password'));
		$birthday = strip_tags($this->input->post('birthday'));
		$verification_code = rand(1000, 9999);
		
		if($birthday != ''){
			$birthday = MongoDATE(strtotime($birthday));
		}
		if($first_name == ''){
			$errArr[] = array('field'=>'first_name', 'error_msg'=>'First Name Is Required.');
		}
		if($last_name == ''){
			$errArr[] = array('field'=>'last_name', 'error_msg'=>'Last Name Is Required.');
		}
		if($email == ''){
			$errArr[] = array('field'=>'email', 'error_msg'=>'Email Is Required.');
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errArr[] = array('field'=>'email', 'error_msg'=>'Invalid Email Format.');
		}
		if($phone_number == ''){
			$errArr[] = array('field'=>'phone_number', 'error_msg'=>'Phone Number Is Required.');
		}
		if($password == ''){
			$errArr[] = array('field'=>'password', 'error_msg'=>'Password Is Required.');
		}
      
		if(!empty($errArr)){
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;
		}
		
		$email_check = $this->admin_model->get_selected_fields(USERS, ['email'=>$email], ['_id', 'profile']);
		if($email_check->num_rows() > 0){
			$errArr[] = array('field'=>'email', 'error_msg'=>'Email already exist.');
		}
		if(!empty($errArr)){
			$this->_response['response_code'] = '202';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Invalid parameters given.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;
		}
		$encrypted_password = password_hash($password, PASSWORD_DEFAULT);				
		$dataArr = [
			"first_name" => $first_name,
			"last_name" => $last_name,
			"username" => $email,
			"email" => $email,
			"phone_number" => $phone_number,
			"birthday" => $birthday,
			"status" => '0',
			"profile" => ['status' => 'Incomplete', 'step' => 1],
			"password" => $encrypted_password,
			"verification_code" => $verification_code
		];
		$this->admin_model->simple_insert(USERS, $dataArr);
		$userArr['id'] = $this->admin_model->get_last_insert_id();
		$userArr['user_type'] = 'user';
		$this->_response['status'] = '1';
		$this->_response['verification_code'] = $verification_code;
		$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
		$this->json_response($this->_response); exit;
	}
	
	public function login(){
		$errArr = array();
		$email = strip_tags($this->input->post('email'));
		$password = strip_tags($this->input->post('password'));
		$device_type = strip_tags($this->input->post('device_type'));
		$device_token = strip_tags($this->input->post('device_token'));
		
		if($email == ''){
			$errArr[] = array('field'=>'email', 'error_msg'=>'Email Is Required.');
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errArr[] = array('field'=>'email', 'error_msg'=>'Invalid email format.');
		}
		if($password == ''){
			$errArr[] = array('field'=>'password', 'error_msg'=>'Password Is Required.');
		}
		$check_login = $this->admin_model->get_selected_fields(USERS, ['email'=>$email], ['password', 'status']);
		if($check_login->num_rows() > 0){ 
         if(!isset($check_login->row()->status) || $check_login->row()->status == '0'){
            $this->_response['response_code'] = '101';
            $this->_response['message'] = 'failed';
            $this->_response['status'] = '0';
            $this->_response['long_message'] = 'Your account is inactive, Contact admin for further details.';
            $this->_response['errArr'] = $errArr;
            $this->json_response($this->_response);exit;
         } else {
            @$hashed_password = $check_login->row()->password;
            @$_id = (string) $check_login->row()->_id;
         }
		} else {
			@$hashed_password = '';
			@$_id = '';
		}
      
		$decrypt_password = password_verify($password, $hashed_password);
		if(!empty($errArr)){
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;
		}
		if($decrypt_password == 0){
			$this->_response['response_code'] = '100';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Invalid Login. Incorrect username/email and password.';
			$this->json_response($this->_response);exit;
		} else {
			$userArr['id'] = $_id;
			$userArr['user_type'] = 'user';
			if($device_type != '' && $device_token != ''){
				$this->admin_model->update_details(USERS, ["device_type" => $device_type, "device_token" => $device_token], ['email'=>$email]);
			}
			$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
		}
		$this->json_response($this->_response);
	}
	
	public function forgot_password(){
		$errArr = array();
		$email = strip_tags($this->input->post('email'));
		if($email == ''){
			$errArr[] = array('field'=>'email', 'error_msg'=>'Email Is Required.');
		}
		if(!empty($errArr)){
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;
		}
		$check_login_email = $this->admin_model->get_all_details(USERS, ['email'=>$email]);
		if($check_login_email->num_rows() > 0){ 
			$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$password = substr(str_shuffle($str_result), 0, 6);
			$decrypt_password = password_hash($password, PASSWORD_DEFAULT);
			$this->admin_model->update_details(USERS, ['password'=>$decrypt_password], ['email'=>$email]);
			$this->_response['password'] = $password;
			$this->_response['long_message'] = 'New password has sent to your registered email.';
		} else {
			$this->_response['response_code'] = '100';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Invalid email.';
			$this->json_response($this->_response);exit;
		}
		$this->json_response($this->_response);
	}
	
	public function profile(){
		if($this->_user_id != ''){
			$mProfile = $this->admin_model->get_all_details(USERS, ['_id' => MongoID($this->_user_id)]);
			$mProfileArr = array();
			if($mProfile->num_rows() > 0){
				$res = $mProfile->row();
				$temp = array();
				$temp['first_name'] = $res->first_name;
				$temp['last_name'] = $res->last_name;
				$temp['registered_date'] = (isset($res->registered_date)?date('d/m/Y h:i A T', MongoEPOCH($res->registered_date)):'');
				$mProfileArr[] = $temp;
				$this->_response['response'] = $mProfileArr;
				$this->json_response($this->_response);
			} else {
				$this->_response['response_code'] = '100';
				$this->_response['message'] = 'failed';
				$this->_response['status'] = '0';
				$this->_response['long_message'] = 'Invalid user.';
				$this->json_response($this->_response);				
			}
		} else {
			$this->json_response($this->config->item('_response')['503']);
			exit;
		}
	}
		
	public function verification_code(){
		$phone_number = $this->input->post('phone_number');
		$verification_code = $this->input->post('verification_code');
		$type = $this->input->post('type');
		if($phone_number != ''){
			$user = $this->admin_model->get_all_details(USERS, ['phone_number'=>$phone_number]);
			if($user->num_rows() > 0){
            if($type=='get'){
               $verification_code = rand(1000, 9999);
               $this->admin_model->update_details(USERS, ['verification_code'=>$verification_code], ['_id'=>MongoID((string) $user->row()->_id)]);
               $userArr['id'] = (string) $user->row()->_id;
               $userArr['user_type'] = 'user';
               $this->_response['status'] = '1';
               $this->_response['verification_code'] = $verification_code;
               $this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
					$this->_response['long_message'] = 'Verification code has been sent to '.$phone_number;
               $this->json_response($this->_response); exit;
            } else {
               if($verification_code == $user->row()->verification_code){
                  $membership = $this->admin_model->get_all_details(SUBSCRIPTION, ['status'=>'1']);
                  $membershipArr = [];
                  if($membership->num_rows() > 0){
                     foreach($membership->result_array() as $res){
                        $membershipArr[] = [
                           'membership_id' => (string) $res['_id'],
                           'name' => $res['name'],
                           'annual_fee' => $res['fees'],
                           'monthly_fee' => '',
                           'total_validity' => $res['validity']['count'].' '.$this->data['validityInterval'][$res['validity']['interval']],
                           'proof_of_qualification' => (isset($res['proof_of_qualification'])?$res['proof_of_qualification']:''),
                           'terms' => (isset($res['terms'])?$res['terms']:[]),
                        ];
                     }
                  }
                  
                  $userArr['id'] = (string) $user->row()->_id;
                  $userArr['user_type'] = 'user';
                  $this->_response['status'] = '1';
						$this->_response['membership'] = $membershipArr;
						$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
						$this->_response['long_message'] = 'Code Verified.';
                  $this->json_response($this->_response); exit;
               } else {
                  if($verification_code == ''){
                     $errArr[] = array('field'=>'verification_code', 'error_msg'=>'Verification Code Is Required.');
                  }
                  $this->_response['response_code'] = ($verification_code!='')?'202':'201';
                  $this->_response['message'] = 'failed';
                  $this->_response['status'] = '0';
                  $this->_response['long_message'] = ($verification_code!='')?'Code Mismatched.':'Required parameters missing.';
                  $this->_response['errArr'] = $errArr;
                  $this->json_response($this->_response);exit;
               }
            }
			} else {
				$this->_response['response_code'] = '202';
				$this->_response['message'] = 'failed';
				$this->_response['status'] = '0';
				$this->_response['long_message'] = 'Invalid parameters given.';
				$this->json_response($this->_response);exit;
			}
		} else {
			if($phone_number == ''){
				$errArr[] = array('field'=>'phone_number', 'error_msg'=>'Phone Number Is Required.');
			}
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;		
		}
	}
	
	public function upload_file(){
      $folder = $this->input->post('folder');
      
		if($folder=='' || empty($_FILES) || $_FILES['file']['name']==''){
         $this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->json_response($this->_response);exit;
      }
      
      $config['upload_path']          = './uploads/'.$folder;
      $config['encrypt_name']     	  = TRUE;
      $config['allowed_types']        = 'gif|jpg|png';
      $config['max_size']             = 5000;
      $this->load->library('upload', $config);
      if ($this->upload->do_upload('file')) {
         $response = $this->upload->data();
         $this->_response['response'] = $response['file_name'];
         $this->_response['long_message'] = 'Your File Uploaded Successfully.';
         $this->json_response($this->_response);exit;
      } else {
         $response = $this->upload->display_errors();
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = $response;
         $this->json_response($this->_response);exit;
      }
	}
	
	public function save_membership_type(){
		$membership_type = $this->input->post('membership_type');
		if($membership_type != ''){
			if($this->_user_id != ''){
				$user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
				if($user->num_rows() > 0){
               $subscriptionArr = (array) getrow(SUBSCRIPTION, $membership_type);
               $subscriptionArr['subscription_id'] = (string) $subscriptionArr['_id'];
               foreach(['_id', 'status', 'terms', 'dateAdded'] as $field){
                  unset($subscriptionArr[$field]);
               }
               $subscriptionArr['user_id'] = $this->_user_id;
               $subscriptionArr['created_at'] = MongoDATE(time());
               $subscriptionArr['platform'] = 'App';
               $subscriptionArr['status'] = '0';
               
					$this->admin_model->simple_insert(USERS_SUBSCRIPTION, $subscriptionArr, ['_id'=>MongoID($this->_user_id)]);
               $subscription_id = $this->admin_model->get_last_insert_id();
					$this->admin_model->update_details(USERS, ['subscription_id'=>$subscription_id, 'profile'=>['status' => 'Incomplete', 'step' => 2]], ['_id'=>MongoID($this->_user_id)]);
					$userArr['id'] = $this->_user_id;
					$userArr['user_type'] = 'user';
					$this->_response['status'] = '1';
					$this->_response['long_message'] = 'Membership Type Saved.';
					$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
					$this->json_response($this->_response); exit;
				} else {
					$this->_response['response_code'] = '202';
					$this->_response['message'] = 'failed';
					$this->_response['status'] = '0';
					$this->_response['long_message'] = 'Invalid parameters given.';
					$this->json_response($this->_response);exit;
				}
			} else {
				$this->_response['response_code'] = '202';
				$this->_response['message'] = 'failed';
				$this->_response['status'] = '0';
				$this->_response['long_message'] = 'Invalid user.';
				$this->json_response($this->_response);exit;
			}
		} else {
			if($membership_type == ''){
				$errArr[] = array('field'=>'membership_type', 'error_msg'=>'Phone Number Is Required.');
			}
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;		
		}
	}
	
	public function save_verification_documents(){
		$license_front = $this->input->post('license_front');
		$license_back = $this->input->post('license_back');
		$profile_photo = $this->input->post('profile_photo');
		$proof_of_qualification = $this->input->post('proof_of_qualification');
		if($license_front != '' && $license_back != '' && $profile_photo != ''){
			if($this->_user_id != ''){
				$user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
				if($user->num_rows() > 0){
					$dataArr = [
                  'documents_to_verify' => [
							'license' => ['front' => $license_front, 'back' => $license_back, 'created_at' => MongoDATE(time())],
							'photo_of_yourself' => ['value' => $profile_photo, 'created_at' => MongoDATE(time())]
						]
					];
					if($proof_of_qualification!=''){
						$dataArr['documents_to_verify']['proof_of_qualification'] = ['value' => $proof_of_qualification, 'created_at' => MongoDATE(time())];
					}
               $dataArr['profile'] = ['status' => 'Incomplete', 'step' => 3];
					$this->admin_model->update_details(USERS, $dataArr, ['_id'=>MongoID($this->_user_id)]);
					$userArr['id'] = $this->_user_id;
					$userArr['user_type'] = 'user';
					$this->_response['status'] = '1';
					$this->_response['long_message'] = 'Verification Documents Saved.';
					$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
					$this->json_response($this->_response); exit;
				} else {
					$this->_response['response_code'] = '202';
					$this->_response['message'] = 'failed';
					$this->_response['status'] = '0';
					$this->_response['long_message'] = 'Invalid parameters given.';
					$this->json_response($this->_response);exit;
				}
			} else {
				$this->_response['response_code'] = '202';
				$this->_response['message'] = 'failed';
				$this->_response['status'] = '0';
				$this->_response['long_message'] = 'Invalid user.';
				$this->json_response($this->_response);exit;
			}
		} else {
			if($license_front == ''){
				$errArr[] = array('field'=>'license_front', 'error_msg'=>'Driver License Front Is Required.');
			}
			if($license_back == ''){
				$errArr[] = array('field'=>'license_back', 'error_msg'=>'Driver License Back Is Required.');
			}
			if($profile_photo == ''){
				$errArr[] = array('field'=>'profile_photo', 'error_msg'=>'Photo Of Yourself Is Required.');
			}
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;		
		}
	}
	
	public function save_payment_method(){
		$name_on_card = $this->input->post('name_on_card');
		$card_number = $this->input->post('card_number');
		$card_expiry_month = $this->input->post('card_expiry_month');
		$card_expiry_year = $this->input->post('card_expiry_year');
		$cvv = $this->input->post('cvv');
		$address_line_1 = $this->input->post('address_line_1');
		$address_line_2 = $this->input->post('address_line_2');
		$city = $this->input->post('city');
		$zipcode = $this->input->post('zipcode');
		$state = $this->input->post('state');
		if($card_number != '' && $card_expiry_month != '' && $card_expiry_year != '' && $cvv != '' && $address_line_1 != '' && $city != '' && $zipcode !='' && $state !=''){
			if($this->_user_id != ''){
				$user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
				if($user->num_rows() > 0){
					$dataArr = [ 
                  'card_detail' =>  [
							'name' => $name_on_card,
							'number' => $card_number,
							'expiry_month' => $card_expiry_month,
							'expiry_year' => $card_expiry_year,
							'address_line_1' => $address_line_1,
							'address_line_2' => $address_line_2,
							'city' => $city,
							'zipcode' => $zipcode,
							'state' => $state
						],
                  'profile' => ['status' => 'Incomplete', 'step' => 4]
					];
					$this->admin_model->update_details(USERS, $dataArr, ['_id'=>MongoID($this->_user_id)]);
					$userArr['id'] = $this->_user_id;
					$userArr['user_type'] = 'user';
					$this->_response['status'] = '1';
					$this->_response['long_message'] = 'Payment Method Saved.';
					$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
					$this->json_response($this->_response); exit;
				} else {
					$this->_response['response_code'] = '202';
					$this->_response['message'] = 'failed';
					$this->_response['status'] = '0';
					$this->_response['long_message'] = 'Invalid parameters given.';
					$this->json_response($this->_response);exit;
				}
			} else {
				$this->_response['response_code'] = '202';
				$this->_response['message'] = 'failed';
				$this->_response['status'] = '0';
				$this->_response['long_message'] = 'Invalid user.';
				$this->json_response($this->_response);exit;
			}
		} else {
			if($name_on_card == ''){
				$errArr[] = array('field'=>'name_on_card', 'error_msg'=>'Name On Card Is Required.');
			}
			if($card_number == ''){
				$errArr[] = array('field'=>'card_number', 'error_msg'=>'Card Number Is Required.');
			}
			if($card_expiry_month == ''){
				$errArr[] = array('field'=>'card_expiry_month', 'error_msg'=>'Card Expiry Month Is Required.');
			}
			if($card_expiry_year == ''){
				$errArr[] = array('field'=>'card_expiry_year', 'error_msg'=>'Card Expiry Year Is Required.');
			}
			if($cvv == ''){
				$errArr[] = array('field'=>'cvv', 'error_msg'=>'CVV Is Required.');
			}
			if($address_line_1 == ''){
				$errArr[] = array('field'=>'address_line_1', 'error_msg'=>'Street Address Is Required.');
			}
			if($city == ''){
				$errArr[] = array('field'=>'city', 'error_msg'=>'City Is Required.');
			}
			if($zipcode == ''){
				$errArr[] = array('field'=>'zipcode', 'error_msg'=>'Zip Code Is Required.');
			}
			if($state == ''){
				$errArr[] = array('field'=>'state', 'error_msg'=>'State Is Required.');
			}
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;		
		}
	}
	
	public function save_preferences(){
		$app_push_notifications = $this->input->post('app_push_notifications');
		$text_messages = $this->input->post('text_messages');
		$emails = $this->input->post('emails');
		if($app_push_notifications != '' && $text_messages != '' && $emails !=''){
			if($this->_user_id != ''){
				$user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
				if($user->num_rows() > 0){
               $ip = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
               $created_at = MongoDATE(time());
					$dataArr = [ 
                  'preferences' => [
							'app_push_notifications' => $app_push_notifications,
							'text_messages' => $text_messages,
							'emails' => $emails
						],
                  'profile' => ['status' => 'Completed', 'step' => 5],
                  'profile_status' => [
                     'personal' => '0',
                     'contact' => '0',
                     'address' => '0',
                     'profile_validation' => '0',
                     'pin_code' => '0'
                  ],
                  'status' => '1',                           
                  'registered_ip' => $ip,
                  'registered_date' => $created_at
					];
					$this->admin_model->update_details(USERS, $dataArr, ['_id'=>MongoID($this->_user_id)]);
               
               $notificationArr = [
                  'user_id' => $this->_user_id,
                  'message' => 'Your account has been created.',
                  'created_at' => $created_at
               ];
               $this->admin_model->simple_insert(NOTIFICATIONS, $notificationArr);
               
					$userArr['id'] = $this->_user_id;
					$userArr['user_type'] = 'user';
					$this->_response['status'] = '1';
					$this->_response['long_message'] = 'Profile Saved.';
					$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
					$this->json_response($this->_response); exit;
				} else {
					$this->_response['response_code'] = '202';
					$this->_response['message'] = 'failed';
					$this->_response['status'] = '0';
					$this->_response['long_message'] = 'Invalid parameters given.';
					$this->json_response($this->_response);exit;
				}
			} else {
				$this->_response['response_code'] = '202';
				$this->_response['message'] = 'failed';
				$this->_response['status'] = '0';
				$this->_response['long_message'] = 'Invalid user.';
				$this->json_response($this->_response);exit;
			}
		} else {
			if($app_push_notifications == ''){
				$errArr[] = array('field'=>'app_push_notifications', 'error_msg'=>'App Push Notifications Is Required.');
			}
			if($text_messages == ''){
				$errArr[] = array('field'=>'text_messages', 'error_msg'=>'Text Messages Is Required.');
			}
			if($emails == ''){
				$emails[] = array('field'=>'emails', 'error_msg'=>'Emails Is Required.');
			}
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;		
		}
	}
	
	public function save_favorite_address(){
		$title = $this->input->post('title');
		$address = $this->input->post('address');
		$lat = $this->input->post('lat');
		$lng = $this->input->post('lng');
		if($title != '' && $address != '' && $lat != '' && $lng !=''){
			if($this->_user_id != ''){
				$user = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->_user_id)]);
				if($user->num_rows() > 0){
					$dataArr = [
                  'user_id' => $this->_user_id,
                  'title' => $title,
                  'address' => $address,
                  'location' => [
							'lat' => floatval($lat),
							'lng' => floatval($lng)
						],
                  'created_at' => MongoDATE(time()),
                  'status' => '1'
					];
					$this->admin_model->simple_insert(FAVORITE_ADDRESS, $dataArr);
					$userArr['id'] = $this->_user_id;
					$userArr['user_type'] = 'user';
					$this->_response['status'] = '1';
					$this->_response['long_message'] = 'Address Saved.';
					$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
					$this->json_response($this->_response); exit;
				} else {
					$this->_response['response_code'] = '202';
					$this->_response['message'] = 'failed';
					$this->_response['status'] = '0';
					$this->_response['long_message'] = 'Invalid parameters given.';
					$this->json_response($this->_response);exit;
				}
			} else {
				$this->_response['response_code'] = '202';
				$this->_response['message'] = 'failed';
				$this->_response['status'] = '0';
				$this->_response['long_message'] = 'Invalid user.';
				$this->json_response($this->_response);exit;
			}
		} else {
			if($app_push_notifications == ''){
				$errArr[] = array('field'=>'app_push_notifications', 'error_msg'=>'App Push Notifications Is Required.');
			}
			if($text_messages == ''){
				$errArr[] = array('field'=>'text_messages', 'error_msg'=>'Text Messages Is Required.');
			}
			if($emails == ''){
				$emails[] = array('field'=>'emails', 'error_msg'=>'Emails Is Required.');
			}
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;
		}
	}
   
	public function get_favorite_address(){
      if($this->_user_id != ''){
         $favorite = $this->admin_model->get_all_details(FAVORITE_ADDRESS, ['user_id'=>$this->_user_id], ['title'=>'ASC']);
         if($favorite->num_rows() > 0){
            $locationArr = [];
            foreach($favorite->result_array() as $address){
               $locationArr[] = [
                  'address_id' => (string) $address['_id'],
                  'title' => $address['title'],
                  'address' => $address['address'],
                  'lat' => $address['location']['lat'],
                  'lng' => $address['location']['lng']
               ];
            }
            $userArr['id'] = $this->_user_id;
            $userArr['user_type'] = 'user';
            $this->_response['status'] = '1';
            $this->_response['response'] = $locationArr;
            $this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
            $this->_response['long_message'] = 'Favorite Address Fetched.';
            $this->json_response($this->_response); exit;
         } else {
            $this->_response['response_code'] = '202';
            $this->_response['message'] = 'failed';
            $this->_response['status'] = '0';
            $this->_response['long_message'] = 'User Does Not Exist.';
            $this->json_response($this->_response);exit;
         }
      } else {
         $this->_response['response_code'] = '202';
         $this->_response['message'] = 'failed';
         $this->_response['status'] = '0';
         $this->_response['long_message'] = 'Invalid user.';
         $this->json_response($this->_response);exit;
      }
	}
   
}
