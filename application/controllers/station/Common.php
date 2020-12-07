<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends MY_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('admin_model');
		$this->data['_station_id'] = $this->uri->segment(2);
	}
	
	public function dashboard(){
		if ($this->checkLogin('A') != '' && $this->data['_station_id']!=''){
			$this->data['result'] = $this->admin_model->get_all_details(STATION, ['_id'=>MongoID($this->data['_station_id'])]);
			$this->data['heading'] = 'Dashboard - '.$this->data['result']->row()->name;
			$this->load->view('station/common/dashboard', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function account(){
		if ($this->checkLogin('A') != '' && $this->data['_station_id']!=''){
			$this->data['heading'] = 'Account Details';
			$this->data['result'] = $this->admin_model->get_all_details(STATION, ['_id'=>MongoID($this->data['_station_id'])]);
			$this->load->view('station/account/listing', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
	public function account_edit(){
		if ($this->checkLogin('A') != '' && $this->data['_station_id']){
			$this->data['heading'] = 'Account Edit';
			$this->data['result'] = $this->admin_model->get_all_details(STATION, ['_id'=>MongoID($this->data['_station_id'])]);
			$this->load->view('station/account/add_edit', $this->data);
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
         $adminCheck = $this->admin_model->get_all_counts(STATION, $conditionArr);
         if ($adminCheck > 0){
            $dataArr = ['dob'=>$dob, 'edit_paper_invoice'=>$edit_paper_invoice, 'dateModified'=>MongoDATE(time())];
            $this->admin_model->commonInsertUpdate(STATION, 'update', ['_id', 'objectid', 'status', 'password', 'dob', 'subscription_date', 'activation_date', 'expiry_date'], $dataArr, $conditionArr);
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
		if ($this->checkLogin('A') != '' && $this->data['_station_id']!=''){
			$this->data['heading'] = 'Subscription Details';
			$this->data['userdetail'] = $userdetail = $this->admin_model->get_all_details(STATION, ['_id'=>MongoID($this->data['_station_id'])]);
			$this->data['result'] = $this->admin_model->get_all_details(STATION_SUBSCRIPTION, ['user_id'=>$this->data['_station_id']]);
			$this->load->view('station/subscription/listing', $this->data);
		} else {
			$this->setErrorMessage('error','Login required!!!');
			redirect(ADMINURL);
		}
	}
	
}
