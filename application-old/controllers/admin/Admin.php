<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
	}
	
	public function index(){
		if ($this->checkLogin('A') == ''){
			$this->load->view('admin/includes/login',$this->data);
		} else {
			redirect(ADMINURL.'/dashboard');
		}
	}
	
	public function dashboard(){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = 'Dashboard';
			$this->data['activeUsers'] = $this->admin_model->get_all_counts(USERS, ['status'=>'1']);
			$this->data['activeStations'] = $this->admin_model->get_all_counts(STATION, ['status'=>'1']);
			$this->data['activeVehicles'] = $this->admin_model->get_all_counts(VEHICLE, ['status'=>'1']);
			$this->data['activePlans'] = $this->admin_model->get_all_counts(SUBSCRIPTION, ['status'=>'1']);
			
			$this->load->view('admin/adminsettings/dashboard', $this->data);
		}
	}
	
	public function dashboard_ajax(){
		$date = $this->input->post('date');
		$exploded = explode(' - ', $date);
		$from = @explode('/', $exploded[0]);
		$to = @explode('/', $exploded[1]);
		$from = strtotime($from[2].'-'.$from[1].'-'.$from[0]);
		$to = strtotime($to[2].'-'.$to[1].'-'.$to[0]);
		
		$categories = $this->admin_model->get_all_details(CATEGORY, ['status'=>'1'], ['name'=>'asc']);
		foreach($categories->result() as $category){
			$condition = ['status'=>'1','category'=>(string)$category->_id];
			if($date!='') $condition['article_date'] = ['$gte'=>$from, '$lte'=>$to];
			$counts = $this->admin_model->get_all_counts(ARTICLE, $condition);
			$donutchart['label'][] = $category->name;
			$donutchart['value'][] = $counts;
		}
		foreach($this->data['eventTypes'] as $evntype){
			$condition = ['status'=>'1','event_type'=>(string)$evntype];
			if($date!='') $condition['date'] = ['$gte'=>$from, '$lte'=>$to];
			$counts = $this->admin_model->get_all_counts(EVENTS, $condition);
			$barchart['label'][] = $evntype;
			$barchart['value'][] = $counts;
		}
		echo json_encode(['status'=>'1', 'data'=>['barchart'=>$barchart, 'donutchart'=>$donutchart]]);
	}
	
	public function forgot_password_form(){
		if ($this->checkLogin('A') == ''){
			$this->data['pagename'] = 'forgot_password';
			$this->data['forgot_email'] = '';
			$this->load->view('forgot_password', $this->data);
		} else {
			redirect(ADMINURL.'/dashboard');
		}
	}
	
	public function forgot_password(){
		if ($this->checkLogin('A') == ''){
			$forgot_email = $this->input->post('admin_email');
			$conditionArr = array('admin_email' => $forgot_email);
			$adminCheck = $this->admin_model->get_all_details(ADMIN, $conditionArr);
			if ($adminCheck->num_rows() == 0){
				$this->setErrorMessage('error','Your account not exist!!!');
				redirect(ADMINURL);
			} else {
				$this->session->set_userdata(['forgot_email' => $forgot_email]);
				$this->data['code'] = $code = trim($this->get_verfication_code());
				$this->admin_model->update_details(ADMIN, ['password'=>md5($code)], ['admin_email' => $forgot_email]);
				$body_messages = $code;
				$email_vaues = [
					'mail_name' => $this->data['title'],
					'from_mail_id' => $this->data['siteContactMail'],
					'to_mail_id' => $forgot_email,
					'subject_message' => 'New Password - '.$this->data['siteTitle'],
					'body_messages' => $body_messages
				];
				$this->admin_model->common_email_send($email_vaues);
				redirect(ADMINURL.'/verify-code');
			}
		} else {
			redirect(ADMINURL.'/dashboard');
		}
	}
	
	public function verification_code(){
		if ($this->checkLogin('A') == ''){
			$this->data['pagename'] = 'verify_code';
			$this->data['forgot_email'] = $this->session->userdata('forgot_email');
			$this->load->view('forgot_password', $this->data);
		} else {
			redirect(ADMINURL.'/dashboard');
		}
	}
		
	public function do_login(){
		$email = $this->input->post('admin_email');
		$pwd = md5($this->input->post('admin_password'));
		$conditionArr = ['admin_email' => $email, 'password'=>$pwd];
		$adminCheck = $this->admin_model->get_all_details(ADMIN, $conditionArr);
		if ($adminCheck->num_rows() > 0){
			$admindata = [
				APP_NAME. '_session_admin_id' => (string)$adminCheck->row()->_id,
				APP_NAME. '_session_admin_name' => $adminCheck->row()->admin_name,
				APP_NAME. '_session_admin_email' => $adminCheck->row()->admin_email,
				APP_NAME. '_session_admin_type' => $adminCheck->row()->type
			];
			$this->session->set_userdata($admindata);
			$this->setErrorMessage('success','Successfully logged in!!!');
			redirect(ADMINURL.'/dashboard');
		} else {
			$this->setErrorMessage('error','Invalid Login Details!!!');
			redirect(ADMINURL);
		}
	}
	
	public function logout(){
		$admindata = array(
			APP_NAME. '_session_admin_id' => '',
			APP_NAME. '_session_admin_name' => '',
			APP_NAME. '_session_admin_email' => '',
			APP_NAME. '_session_admin_type' => ''
		);
		$this->session->set_userdata($admindata);
		$this->setErrorMessage('success','Successfully logged out!!!');
		redirect(ADMINURL);
	}
	
	public function admin_user(){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
		else {
			$this->data['heading'] = "Admin Users";
			$this->data['adminusers'] = $this->admin_model->get_all_details(ADMIN, ['type'=>['$ne'=>'station']]);
			$this->load->view('admin/adminsettings/admin_user',$this->data);
		}
	}
	
	public function admin_settings(){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
		else {
			$this->data['heading'] = "Site Global Configurations";
			$this->data['result'] = $this->admin_model->get_all_details(ADMIN, ['admin_id'=>'1']);
			$this->load->view('admin/adminsettings/admin_settings',$this->data);
		}
	}
	
	public function view_subadmin($id = ''){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			if($id!=''){
				$condition = ['type'=>'subadmin', '_id' => objectid((string)$id)];
				$result = $this->admin_model->get_all_details(ADMIN, $condition);
				if($result->num_rows() > 0){
					$this->data['station'] = $result->result_array();
					$this->data['heading'] = "Subadmin Details";
					$this->load->view('admin/adminsettings/view_subadmin',$this->data);
				} else {
					$this->setErrorMessage('error','Something went wrong!!!');
					redirect(ADMINURL.'/cases');
				}
			} else {
				$this->setErrorMessage('error','Something went wrong!!!');
				redirect(ADMINURL.'/cases');
			}
		}
	}	
	
	public function admin_global_settings(){
	
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
		if (!$this->data['demoserverChk'] || $this->checkLogin('A')==1){
			$form_mode = $this->input->post('form_mode');
			if ($form_mode == 'admin_settings'){
				$admin_name = $this->input->post('admin_name');
				$admin_email = $this->input->post('admin_email');
				$condition = array('admin_name' => $admin_name,'admin_id' => ['$ne' => '1']);
				$duplicate_admin= $this->admin_model->get_all_details(ADMIN,$condition);
				if ($duplicate_admin->num_rows() > 0){
					$this->setErrorMessage('error','Admin name already exists');
					redirect(ADMINURL.'/settings');
				} else {
					$condition = array('admin_name' => $admin_name);
					$duplicate_sub_admin = $this->admin_model->get_all_details(SUBADMIN,$condition);
					if ($duplicate_sub_admin->num_rows() > 0){
						$this->setErrorMessage('error','Sub Admin name exists');
						redirect(ADMINURL.'/settings');
					}else {
						$condition = array('admin_email' => trim($admin_email),'admin_id' => ['$ne' => '1']);
						$duplicate_admin_mail = $this->admin_model->get_all_details(ADMIN,$condition);
						if ($duplicate_admin_mail->num_rows() > 0){
							$this->setErrorMessage('error','Admin email already exists');
							redirect(ADMINURL.'/settings');
						}else {
							$condition = array('admin_email' => $admin_email);
							$duplicate_mail = $this->admin_model->get_all_details(SUBADMIN,$condition);
							if ($duplicate_mail->num_rows() > 0){
								$this->setErrorMessage('error','Sub Admin email exists');
								redirect(ADMINURL.'/settings');
							}
						}
					}
				}
				$condition = array('admin_id'=>'1');
				$excludeArr = array('ios_link','android_link','transaction_fee', 'proffesional_owners_list_count', 's3_bucket_name','s3_access_key','s3_secret_key','google_map_api','form_mode','logo_image','home_logo_image','videoUrl','fevicon_image','site_contact_mail','email_title','footer_content','like_text','liked_text','unlike_text','banner_text','home_title_1','home_title_2','home_title_3','home_title_4','home_title_5','home_title_6','home_text','HomeVideoUrl','contact_address','contact_tel','contact_email','referal_amount', 'google_verification_code', 'vertical_ads', 'horizontal_ads', 'square_ads', 'live_chat','device_expiry_date', 'changepwd', 'password');
				
				$password = $this->input->post('password');
				if(isset($_POST['changepwd']) && $password!=''){
					$dataArr['password'] = md5(trim($this->input->post('password')));
				}

				if(isset($_POST['popup_link'])){
					$popup_status = 0;
					if(isset($_POST['popup_status'])){
						$popup_status = 1;
					}
					$_POST['popup_status'] = $popup_status;
				}
				
				$google_verification_code = htmlentities($this->input->post('google_verification_code', FALSE));
				if(isset($_POST['google_verification_code'])) $dataArr['google_verification_code'] = $google_verification_code;
				
				$live_chat = htmlentities($this->input->post('live_chat', FALSE));
				if(isset($_POST['live_chat'])) $dataArr['live_chat'] = $live_chat;

				$vertical_ads = htmlentities($this->input->post('vertical_ads', FALSE));
				if(isset($_POST['vertical_ads'])) $dataArr['vertical_ads'] = $vertical_ads;

				$horizontal_ads = htmlentities($this->input->post('horizontal_ads', FALSE));
				if(isset($_POST['horizontal_ads'])) $dataArr['horizontal_ads'] = $horizontal_ads;

				$square_ads = htmlentities($this->input->post('square_ads', FALSE));
				if(isset($_POST['square_ads'])) $dataArr['square_ads'] = $square_ads;
				
				$this->admin_model->commonInsertUpdate(ADMIN,'update',$excludeArr,$dataArr,$condition);
				$dataArr = array();
				
				$config['encrypt_name'] = TRUE;
				$config['overwrite'] = FALSE;
				$config['allowed_types'] = 'jpg|jpeg|gif|png|ico';
				$config['max_size'] = 8000;
				$config['upload_path'] = './images/logo';
				$this->load->library('upload', $config);
				
				if($this->upload->do_upload('site_logo')){
					$logoDetails = $this->upload->data();
					$dataArr['site_logo'] = $logoDetails['file_name'];
				}
				if($this->upload->do_upload('background_image')){
					$logoDetails = $this->upload->data();
					$dataArr['background_image'] = $logoDetails['file_name'];
				}
				if($this->upload->do_upload('favicon')){
					$feviconDetails = $this->upload->data();
					$dataArr['favicon'] = $feviconDetails['file_name'];
				}
				
				$excludeArr = ['form_mode','site_logo','home_logo_image','fevicon','watermark','mobile_ad','admin_name','background_image','home_video_poster','list_your_flat_poster','list_your_activity_poster','dropbox_email','dropbox_password', 'google_verification_code', 'vertical_ads', 'horizontal_ads', 'square_ads', 'live_chat', 'changepwd', 'password'];
				$this->admin_model->commonInsertUpdate(ADMIN,'update',$excludeArr,$dataArr,$condition);
				$this->admin_model->saveAdminSettings();
				$this->setErrorMessage('success','Admin details updated successfully');
				
				redirect(ADMINURL.'/settings');
			} else {
				
				$dataArr = array();
				$condition = array('admin_id'=>'1');
				$excludeArr = array('form_mode', 'google_verification_code', 'vertical_ads', 'horizontal_ads', 'square_ads', 'live_chat', 'changepwd', 'password');

				$google_verification_code = htmlentities($this->input->post('google_verification_code', FALSE));
				if(isset($_POST['google_verification_code'])) $dataArr['google_verification_code'] = $google_verification_code;
				
				$live_chat = htmlentities($this->input->post('live_chat', FALSE));
				if(isset($_POST['live_chat'])) $dataArr['live_chat'] = $live_chat;

				$vertical_ads = htmlentities($this->input->post('vertical_ads', FALSE));
				if(isset($_POST['vertical_ads'])) $dataArr['vertical_ads'] = $vertical_ads;

				$horizontal_ads = htmlentities($this->input->post('horizontal_ads', FALSE));
				if(isset($_POST['horizontal_ads'])) $dataArr['horizontal_ads'] = $horizontal_ads;

				$square_ads = htmlentities($this->input->post('square_ads', FALSE));
				if(isset($_POST['square_ads'])) $dataArr['square_ads'] = $square_ads;
				
				$this->admin_model->commonInsertUpdate(ADMIN,'update',$excludeArr,$dataArr,$condition);
				$this->admin_model->saveAdminSettings();
				$this->setErrorMessage('success','Admin details updated successfully');
				redirect(ADMINURL.'/settings');
			}
		} else {
			$this->setErrorMessage('error','You are in demo mode. Settings cannot be changed');
			redirect(ADMINURL.'/settings');
		}
	}
	
	public function admin_change_password(){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$this->data['heading'] = "Change Password";
			$conditionArr = array('admin_id' => '1');
			$this->data['action'] = 'update_password';
			$this->data['admin_settings'] = $this->admin_model->get_all_details(ADMIN, $conditionArr);
			$this->load->view('admin/adminsettings/admin_password',$this->data);
		}
	}
	
	public function update_password(){
		if ($this->checkLogin('A') == ''){
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		} else {
			$current_password = md5($this->input->post('current_password'));
			$conditionArr = array('admin_id'=>'1', 'password'=>$current_password);
			$adminCheck = $this->admin_model->get_all_counts(ADMIN, $conditionArr);
			if ($adminCheck > 0){
				$password = md5($this->input->post('password'));
				$dataArr = array('password'=>$password);
				$this->admin_model->update_details ( ADMIN, $dataArr, $conditionArr );
				$this->setErrorMessage('success','Password Updated Successfully!!!');
				redirect(ADMINURL.'/dashboard');
			} else {
				$this->setErrorMessage('error','Invalid Current Password!!!');
				redirect(ADMINURL.'/change-password');
			}
		}
	}
	
}
