<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		$this->data['_user_id'] = $this->uri->segment(2);
	}
	
	public function dashboard(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
			$this->data['heading'] = 'Dashboard';
			$this->data['result'] = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->load->view('users/common/dashboard', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function account(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
			$this->data['heading'] = 'Account Details';
			$this->data['result'] = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->load->view('users/account/listing', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function account_edit(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']){
			$this->data['heading'] = 'Account Edit';
			$this->data['result'] = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->load->view('users/account/add_edit', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function account_save(){
		if ($this->checkLogin('A') != ''){
			$objectid = $this->input->post('objectid');
			$edit_paper_invoice = $this->input->post('edit_paper_invoice');
			$dob = $this->input->post('dob');
         if($dob!=''){
            $date = @explode('/', $dob);
            $date = $date[2].'-'.$date[1].'-'.$date[0];
            $dob = MongoDATE(strtotime($date));
         }
			$edit_paper_invoice = ($edit_paper_invoice!='')?'1':'0';
         $conditionArr = ['_id'=>MongoID((string)$objectid)];
         $adminCheck = $this->admin_model->get_all_counts(USERS, $conditionArr);
         if ($adminCheck > 0){
            $dataArr = ['dob'=>$dob, 'edit_paper_invoice'=>$edit_paper_invoice, 'dateModified'=>MongoDATE(time())];
            $this->admin_model->commonInsertUpdate(USERS, 'update', ['_id', 'objectid', 'status', 'password', 'dob', 'subscription_date', 'activation_date', 'expiry_date'], $dataArr, $conditionArr);
            $this->setErrorMessage('success','Users details saved!!!');
            redirect(USERURL.'/'.$objectid.'/account');
         } else {
            $this->setErrorMessage('error','Something went wrong!!!');
            redirect(USERURL.'/'.$objectid.'/account');
         }
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}	
	
	public function subscription(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']!=''){
			$this->data['heading'] = 'Subscription Details';
			$this->data['userdetail'] = $userdetail = $this->admin_model->get_all_details(USERS, ['_id'=>MongoID($this->data['_user_id'])]);
			$this->data['result'] = $this->admin_model->get_all_details(USERS_SUBSCRIPTION, ['user_id'=>$this->data['_user_id']]);
			$this->load->view('users/subscription/listing', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function change_status(){
		if ($this->checkLogin('A') != '' && $this->data['_user_id']){
         $status = $this->input->post('status');
			$id = $this->input->post('id');
         
			$condition = ['_id' => MongoID($id)];
			$check = $this->admin_model->get_all_counts(USERS_SUBSCRIPTION, $condition);
         
			if($check > 0){
				$this->admin_model->update_details(USERS_SUBSCRIPTION, ['status' => $status], $condition);
				echo 'success';
			} else {
				echo 'Something went wrong!!!';
			}
		} else {
			echo 'Something went wrong!!!';
		}
	}
	
}
