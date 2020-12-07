<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends Api_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		error_reporting(-1);
	}
	
	public function register(){
		$errArr = array();
		$first_name = strip_tags($this->input->post('first_name'));
		$last_name = strip_tags($this->input->post('last_name'));
		$username = strip_tags($this->input->post('username'));
		$email = strip_tags($this->input->post('email'));
		$password = strip_tags($this->input->post('password'));
		$confirm_password = strip_tags($this->input->post('confirm_password'));
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
			$errArr[] = array('field'=>'email', 'error_msg'=>'Invalid email format.');
		}
		if($password == ''){
			$errArr[] = array('field'=>'password', 'error_msg'=>'Password Is Required.');
		}
		if($confirm_password == ''){
			$errArr[] = array('field'=>'confirm_password', 'error_msg'=>'Confirm Password Is Required.');
		}
		if($password != $confirm_password){
			$errArr[] = array('field'=>'confirm_password', 'error_msg'=>'Password and Confirm Password should be matched');
		}
      
		if(!empty($errArr)){
			$this->_response['response_code'] = '201';
			$this->_response['message'] = 'failed';
			$this->_response['status'] = '0';
			$this->_response['long_message'] = 'Required parameters missing.';
			$this->_response['errArr'] = $errArr;
			$this->json_response($this->_response);exit;
		}
      if($username!=''){
         $username_check = $this->admin_model->get_all_details(USERS, ['username'=>$username]);
         if($username_check->num_rows() > 0){
            $errArr[] = array('field'=>'username', 'error_msg'=>'User name already exist.');
         }
      }
		$email_check = $this->admin_model->get_selected_fields(USERS, ['email'=>$email], ['_id']);
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
		$country = '';
		$encrypted_password = password_hash($password, PASSWORD_DEFAULT);
		$referral_code = mt_rand();
		$ip = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
		
		$regsiter_date = date("F d, Y");
		$seller_activity = date("Y-m-d H:i:s");
		$dataArr = [
         "first_name" => $first_name,
			"last_name" => $last_name,
			"username" => $username,
			"email" => $email,
			"password" => $encrypted_password,
			"created_date" => MongoDATE(time())
      ];
		$this->admin_model->simple_insert(USERS, $dataArr);
		$userArr['id'] = $this->admin_model->get_last_insert_id();
		$userArr['user_type'] = 'user';
		$this->_response['status'] = '1';
		$this->_response['commonArr'] = array('token'=>$this->getjwt($userArr));
		$this->json_response($this->_response);
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
            $this->_response['response_code'] = '100';
            $this->_response['message'] = 'failed';
            $this->_response['status'] = '0';
            $this->_response['long_message'] = 'Your account has been in-active.';
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
			$userArr['user_type'] = 'seller';
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
			$mProfile = $this->admin_model->get_all_details(USERS, ['_id' => objectid($this->_user_id)]);
			$mProfileArr = array();
			if($mProfile->num_rows() > 0){
				$res = $mProfile->row();
				$temp = array();
				$temp['first_name'] = $res->first_name;
				$temp['last_name'] = $res->last_name;
				$temp['registered_date'] = (isset($res->registered_date)?date('d/m/Y h:i A T', MongoDATE($res->registered_date)):'');
				$mProfileArr[] = $temp;
			}
			$this->_response['response'] = $mProfileArr;
			$this->json_response($this->_response);
		} else {
			$this->json_response($this->config->item('_response')['503']);
			exit;
		}
	}
}
